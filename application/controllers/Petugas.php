<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Petugas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('M_petugas');
        $this->load->helper(['url', 'form']);
        date_default_timezone_set('Asia/Jakarta');
        
        if (!$this->session->userdata('logged_in')) redirect('auth');
        
        // Proteksi: Controller ini khusus untuk role petugas dalam menginput data hidrologi
        $role = $this->session->userdata('role');
        if ($role !== 'petugas') {
            show_error('Akses Ditolak: Halaman ini khusus untuk Petugas Lapangan.', 403);
        }
    }

    // ============================================
    // 1. BYPASS DIRECT KE HALAMAN INPUT
    // ============================================
    public function index() {
        redirect('petugas/input');
    }

    // ============================================
    // 2. FORM INPUT LAPORAN (Manual / Bendungan)
    // ============================================
    public function input() {
        $raw_id_pos = $this->session->userdata('id_pos');
        
        // Pecah string id_pos dari session menjadi array (antisipasi multi-pos/wilayah)
        $assigned_pos_ids = !empty($raw_id_pos) ? array_map('trim', explode(',', $raw_id_pos)) : [];
        
        if (empty($assigned_pos_ids)) {
            show_error('Akun Anda belum dikaitkan dengan pos infrastruktur manapun. Silakan hubungi Admin.', 403);
            return;
        }

        // Cek pos mana yang sedang aktif dipilih via URL ?pos= , jika tidak ada fallback ke pos pertama
        $id_pos_active = $this->input->get('pos', TRUE);
        if (empty($id_pos_active) || !in_array($id_pos_active, $assigned_pos_ids)) {
            $id_pos_active = $assigned_pos_ids[0];
        }

        $pos = $this->M_petugas->get_pos($id_pos_active);
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        // Ambil data detail semua pos yang diampu petugas ini untuk navigasi/dropdown pindah pos di view
        $this->db->where_in('id_pos', $assigned_pos_ids);
        $daftar_pos_petugas = $this->db->get('master_pos')->result();

        $data = [
            'app_name'           => 'CASCADE',
            'title'              => 'Form Input Data Hidrologi',
            'petugas_name'       => $this->session->userdata('nama_lengkap'),
            'pos'                => $pos,
            'tanggal'            => $tanggal,
            'daftar_pos_petugas' => $daftar_pos_petugas,
            'id_pos_active'      => $id_pos_active
        ];
        
        if ($pos && $pos->is_bendungan == 1) {
            $data['content'] = $this->load->view('petugas/v_input_bendungan', $data, TRUE);
        } else {
            $data['data_list'] = $this->M_petugas->get_by_tanggal($id_pos_active, $tanggal);
            $data['content'] = $this->load->view('petugas/v_input_manual', $data, TRUE);
        }
        
        $this->load->view('layout/v_petugas_layout', $data);
    }

    public function simpan() {
        $id_user = $this->session->userdata('id_user') ?: $this->session->userdata('user_id'); 
        $id_pos = $this->input->post('id_pos', TRUE); 
        $tanggal = $this->input->post('tanggal');
        $rain = $this->input->post('rain');
        $wlevel = $this->input->post('wlevel');
        
        // Keamanan Lapis 1: Pastikan pos yang di-submit benar-benar milik petugas yang login via session
        $raw_id_pos = $this->session->userdata('id_pos');
        $assigned_pos_ids = !empty($raw_id_pos) ? array_map('trim', explode(',', $raw_id_pos)) : [];
        if (!in_array((string)$id_pos, array_map('strval', $assigned_pos_ids))) {
            show_error('Akses Terblokir: Anda tidak memiliki otoritas input di pos ini.', 403);
            return;
        }

        if (empty($tanggal) || ($rain === '' && $wlevel === '')) {
            $this->session->set_flashdata('error', 'Tanggal dan minimal satu nilai parameter harus diisi.');
            redirect('petugas/input?pos=' . $id_pos);
        }
        
        $this->M_petugas->insert([
            'id_pos' => $id_pos, 
            'id_user' => $id_user, 
            'tanggal_input' => $tanggal,
            'rain' => ($rain !== '' && $rain !== null) ? (float)$rain : null,
            'wlevel' => ($wlevel !== '' && $wlevel !== null) ? (float)$wlevel : null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->session->set_flashdata('success', 'Data hidrologi berhasil disimpan!');
        redirect('petugas/input?pos=' . $id_pos . '&tanggal=' . $tanggal);
    }

    public function simpan_bendungan() {
        $id_user = $this->session->userdata('user_id') ?: $this->session->userdata('id_user'); 
        $id_pos = $this->input->post('id_pos', TRUE);
        $tanggal = $this->input->post('tanggal');
        $rain = $this->input->post('rain');
        $elevasi = $this->input->post('elevasi');
        
        // Keamanan Lapis 1: Pastikan pos bendungan masuk dalam cakupan tugas
        $raw_id_pos = $this->session->userdata('id_pos');
        $assigned_pos_ids = !empty($raw_id_pos) ? array_map('trim', explode(',', $raw_id_pos)) : [];
        
        if (!in_array((string)$id_pos, array_map('strval', $assigned_pos_ids))) {
            show_error('Akses Terblokir: Anda tidak memiliki otoritas input di bendungan ini.', 403);
            return;
        }

        $nwl = $this->input->post('nwl');
        $nwl_volume = $this->input->post('nwl_volume');
        $nwl_luas = $this->input->post('nwl_luas');
        
        // Update parameter NWL master bendungan jika ada modifikasi baru
        if ($nwl !== null || $nwl_volume !== null || $nwl_luas !== null) {
            $this->db->where('id_pos', $id_pos)->update('master_pos', [
                'nwl' => $nwl ?: null,
                'nwl_volume' => $nwl_volume ?: null,
                'nwl_luas' => $nwl_luas ?: null,
            ]);
        }
        
        // Insert ke tabel utama hidrologi
        if (($rain !== '' && $rain !== null) || ($elevasi !== '' && $elevasi !== null)) {
            $this->M_petugas->insert([
                'id_pos' => $id_pos, 
                'id_user' => $id_user, 
                'tanggal_input' => $tanggal,
                'rain' => ($rain !== '' && $rain !== null) ? (float)$rain : null,
                'wlevel' => ($elevasi !== '' && $elevasi !== null) ? (float)$elevasi : null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Insert ke tabel operasional bendungan harian
        $this->M_petugas->insert_bendungan([
            'id_pos' => $id_pos, 
            'id_user' => $id_user, 
            'tanggal_input' => $tanggal,
            'nwl' => $nwl ?: null, 
            'elevasi' => $elevasi ?: null,
            'volume' => $this->input->post('volume') ?: null,
            'luas' => $this->input->post('luas') ?: null,
            'inflow' => $this->input->post('inflow') ?: null,
            'pltm' => $this->input->post('pltm') ?: null,
            'spillway' => $this->input->post('spillway') ?: null,
            'total_outflow' => $this->input->post('total_outflow') ?: null,
            'plta_status' => $this->input->post('plta_status') ?: null,
            'irigasi_status' => $this->input->post('irigasi_status') ?: null,
            'tail_water' => $this->input->post('tail_water') ?: null,
            'rembesan_vnotch_h' => $this->input->post('rembesan_vnotch_h') ?: null,
            'rembesan_vnotch_q' => $this->input->post('rembesan_vnotch_q') ?: null,
            'rembesan_pump_pit_l_h' => $this->input->post('rembesan_pump_pit_l_h') ?: null,
            'rembesan_pump_pit_l_q' => $this->input->post('rembesan_pump_pit_l_q') ?: null,
            'rembesan_pump_pit_r_h' => $this->input->post('rembesan_pump_pit_r_h') ?: null,
            'rembesan_pump_pit_r_q' => $this->input->post('rembesan_pump_pit_r_q') ?: null,
            'keterangan' => $this->input->post('keterangan') ?: null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->session->set_flashdata('success', 'Data operasional bendungan berhasil disimpan!');
        redirect('petugas/input?pos=' . $id_pos . '&tanggal=' . $tanggal);
    }

    // ============================================
    // 3. KELOLA LAPORAN (Hanya Menampilkan Data / Read-Only)
    // ============================================
    public function kelola() {
        $raw_id_pos = $this->session->userdata('id_pos');
        $assigned_pos_ids = !empty($raw_id_pos) ? array_map('trim', explode(',', $raw_id_pos)) : [];
        
        if (empty($assigned_pos_ids)) {
            show_error('Akun Anda belum dikaitkan dengan pos manapun.', 403);
            return;
        }

        $id_pos_active = $this->input->get('pos', TRUE);
        if (empty($id_pos_active) || !in_array($id_pos_active, $assigned_pos_ids)) {
            $id_pos_active = $assigned_pos_ids[0];
        }

        $pos = $this->M_petugas->get_pos($id_pos_active);
        $bulan = $this->input->get('bulan') ?: date('Y-m');
        
        $this->db->where_in('id_pos', $assigned_pos_ids);
        $daftar_pos_petugas = $this->db->get('master_pos')->result();
        
        $data = [
            'app_name'           => 'CASCADE',
            'title'              => 'Riwayat Laporan Masuk',
            'petugas_name'       => $this->session->userdata('nama_lengkap'),
            'pos'                => $pos,
            'bulan'              => $bulan,
            'daftar_pos_petugas' => $daftar_pos_petugas,
            'id_pos_active'      => $id_pos_active
        ];
        
        if ($pos && $pos->is_bendungan == 1) {
            $data['data_list'] = $this->M_petugas->get_bendungan_by_bulan($id_pos_active, $bulan);
            $data['content'] = $this->load->view('petugas/v_kelola_bendungan', $data, TRUE);
        } else {
            $data['data_list'] = $this->M_petugas->get_by_bulan($id_pos_active, $bulan);
            $data['content'] = $this->load->view('petugas/v_kelola_manual', $data, TRUE);
        }
        
        $this->load->view('layout/v_petugas_layout', $data);
    }

    // =========================================================================
    // LOCK DOWN PROTEKSI: Hak Akses Menghapus & Mengedit Data Dicabut Total
    // =========================================================================
    
    public function update() {
        show_error('Akses Ditolak: Petugas Lapangan tidak memiliki izin mengubah data log hidrologi. Silakan hubungi Admin Wilayah.', 403);
    }

    public function hapus($id_manual) {
        show_error('Akses Ditolak: Petugas Lapangan tidak memiliki izin menghapus data dari sistem. Silakan hubungi Admin Wilayah.', 403);
    }

    public function update_bendungan() {
        show_error('Akses Ditolak: Petugas Lapangan tidak memiliki izin mengubah data log operasional bendungan.', 403);
    }

    public function hapus_bendungan($id_bendungan) {
        show_error('Akses Ditolak: Petugas Lapangan tidak memiliki izin menghapus data operasional bendungan.', 403);
    }
}