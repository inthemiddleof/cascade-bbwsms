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
        if ($this->session->userdata('role') !== 'petugas') show_error('Akses Ditolak', 403);
    }

    // ============================================
    // 1. DASHBOARD
    // ============================================
    public function index() {
        $id_pos = $this->session->userdata('id_pos');
        $pos = $this->M_petugas->get_pos($id_pos);
        
        $data = [
            'app_name'      => 'CASCADE',
            'title'         => 'Dashboard',
            'petugas_name'  => $this->session->userdata('nama_lengkap'),
            'pos'           => $pos,
            'total_hari_ini'    => $this->M_petugas->count_hari_ini($id_pos),
            'total_bulan_ini'   => $this->M_petugas->count_bulan_ini($id_pos),
            'total_semua'       => $this->M_petugas->count_semua($id_pos),
            'riwayat_hari_ini'  => $this->M_petugas->get_riwayat_hari_ini($id_pos),
        ];
        
        $data['content'] = $this->load->view('petugas/v_dashboard', $data, TRUE);
        $this->load->view('layout/v_petugas_layout', $data);
    }

    // ============================================
    // 2. INPUT LAPORAN
    // ============================================
    public function input() {
        $id_pos = $this->session->userdata('id_pos');
        $pos = $this->M_petugas->get_pos($id_pos);
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $data = [
            'app_name'      => 'CASCADE',
            'title'         => 'Input Laporan',
            'petugas_name'  => $this->session->userdata('nama_lengkap'),
            'pos'           => $pos,
            'tanggal'       => $tanggal,
        ];
        
        if ($pos->is_bendungan == 1) {
            $data['content'] = $this->load->view('petugas/v_input_bendungan', $data, TRUE);
        } else {
            $data['data_list'] = $this->M_petugas->get_by_tanggal($id_pos, $tanggal);
            $data['content'] = $this->load->view('petugas/v_input_manual', $data, TRUE);
        }
        
        $this->load->view('layout/v_petugas_layout', $data);
    }

    public function simpan() {
        $id_user = $this->session->userdata('user_id');
        $id_pos = $this->session->userdata('id_pos');
        $tanggal = $this->input->post('tanggal');
        $rain = $this->input->post('rain');
        $wlevel = $this->input->post('wlevel');
        
        if (empty($tanggal) || ($rain === '' && $wlevel === '')) {
            $this->session->set_flashdata('error', 'Tanggal dan minimal satu nilai harus diisi.');
            redirect('petugas/input');
        }
        
        $this->M_petugas->insert([
            'id_pos' => $id_pos, 'id_user' => $id_user, 'tanggal_input' => $tanggal,
            'rain' => ($rain !== '' && $rain !== null) ? (float)$rain : null,
            'wlevel' => ($wlevel !== '' && $wlevel !== null) ? (float)$wlevel : null,
        ]);
        
        $this->session->set_flashdata('success', 'Data berhasil disimpan!');
        redirect('petugas/input?tanggal=' . $tanggal);
    }

    public function simpan_bendungan() {
        $id_user = $this->session->userdata('user_id');
        $id_pos = $this->session->userdata('id_pos');
        $tanggal = $this->input->post('tanggal');
        $rain = $this->input->post('rain');
        $elevasi = $this->input->post('elevasi');
        
        // Update master_pos jika NWL diubah
        $nwl = $this->input->post('nwl');
        $nwl_volume = $this->input->post('nwl_volume');
        $nwl_luas = $this->input->post('nwl_luas');
        
        if ($nwl !== null || $nwl_volume !== null || $nwl_luas !== null) {
            $this->db->where('id_pos', $id_pos)->update('master_pos', [
                'nwl' => $nwl ?: null,
                'nwl_volume' => $nwl_volume ?: null,
                'nwl_luas' => $nwl_luas ?: null,
            ]);
        }
        
        // Simpan rain & elevasi ke data_manual
        if ($rain !== '' || $elevasi !== '') {
            $this->M_petugas->insert([
                'id_pos' => $id_pos, 'id_user' => $id_user, 'tanggal_input' => $tanggal,
                'rain' => ($rain !== '' && $rain !== null) ? (float)$rain : null,
                'wlevel' => ($elevasi !== '' && $elevasi !== null) ? (float)$elevasi : null,
            ]);
        }
        
        // Simpan ke data_bendungan
        $this->M_petugas->insert_bendungan([
            'id_pos' => $id_pos, 'id_user' => $id_user, 'tanggal_input' => $tanggal,
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
        ]);
        
        $this->session->set_flashdata('success', 'Data bendungan berhasil disimpan!');
        redirect('petugas/input?tanggal=' . $tanggal);
    }
    // ============================================
    // 3. KELOLA LAPORAN
    // ============================================
    public function kelola() {
        $id_pos = $this->session->userdata('id_pos');
        $pos = $this->M_petugas->get_pos($id_pos);
        $bulan = $this->input->get('bulan') ?: date('Y-m');
        
        $data = [
            'app_name'      => 'CASCADE',
            'title'         => 'Kelola Laporan',
            'petugas_name'  => $this->session->userdata('nama_lengkap'),
            'pos'           => $pos,
            'bulan'         => $bulan,
        ];
        
        if ($pos->is_bendungan == 1) {
            $data['data_list'] = $this->M_petugas->get_bendungan_by_bulan($id_pos, $bulan);
            $data['content'] = $this->load->view('petugas/v_kelola_bendungan', $data, TRUE);
        } else {
            $data['data_list'] = $this->M_petugas->get_by_bulan($id_pos, $bulan);
            $data['content'] = $this->load->view('petugas/v_kelola_manual', $data, TRUE);
        }
        
        $this->load->view('layout/v_petugas_layout', $data);
    }

    public function update() {
        $id_pos = $this->session->userdata('id_pos');
        $id_manual = $this->input->post('id_manual');
        $data = $this->M_petugas->get_by_id($id_manual, $id_pos);
        if (!$data) { $this->session->set_flashdata('error', 'Data tidak ditemukan.'); redirect('petugas/kelola'); }
        
        $rain = $this->input->post('rain'); $wlevel = $this->input->post('wlevel'); $tanggal = $this->input->post('tanggal');
        
        $this->M_petugas->update_manual($id_manual, [
            'tanggal_input' => $tanggal,
            'rain' => ($rain !== '' && $rain !== null) ? (float)$rain : null,
            'wlevel' => ($wlevel !== '' && $wlevel !== null) ? (float)$wlevel : null,
        ]);
        
        $this->session->set_flashdata('success', 'Data berhasil diperbarui!');
        redirect('petugas/kelola?bulan=' . date('Y-m', strtotime($tanggal)));
    }

    public function hapus($id_manual) {
        $id_pos = $this->session->userdata('id_pos');
        $this->M_petugas->delete_manual($id_manual, $id_pos)
            ? $this->session->set_flashdata('success', 'Data berhasil dihapus.')
            : $this->session->set_flashdata('error', 'Data tidak ditemukan.');
        redirect('petugas/kelola');
    }

    public function update_bendungan() {
        $id_pos = $this->session->userdata('id_pos');
        $id_bendungan = $this->input->post('id_bendungan');
        $data = $this->M_petugas->get_bendungan_by_id($id_bendungan, $id_pos);
        if (!$data) { $this->session->set_flashdata('error', 'Data tidak ditemukan.'); redirect('petugas/kelola'); }
        
        $tanggal = $this->input->post('tanggal');
        
        $this->M_petugas->update_bendungan($id_bendungan, [
            'tanggal_input' => $tanggal,
            'nwl' => $this->input->post('nwl') ?: null, 'elevasi' => $this->input->post('elevasi') ?: null,
            'volume' => $this->input->post('volume') ?: null, 'luas' => $this->input->post('luas') ?: null,
            'inflow' => $this->input->post('inflow') ?: null, 'pltm' => $this->input->post('pltm') ?: null,
            'spillway' => $this->input->post('spillway') ?: null, 'total_outflow' => $this->input->post('total_outflow') ?: null,
            'plta_status' => $this->input->post('plta_status') ?: null, 'irigasi_status' => $this->input->post('irigasi_status') ?: null,
            'tail_water' => $this->input->post('tail_water') ?: null,
            'rembesan_vnotch_h' => $this->input->post('rembesan_vnotch_h') ?: null, 'rembesan_vnotch_q' => $this->input->post('rembesan_vnotch_q') ?: null,
            'rembesan_pump_pit_l_h' => $this->input->post('rembesan_pump_pit_l_h') ?: null, 'rembesan_pump_pit_l_q' => $this->input->post('rembesan_pump_pit_l_q') ?: null,
            'rembesan_pump_pit_r_h' => $this->input->post('rembesan_pump_pit_r_h') ?: null, 'rembesan_pump_pit_r_q' => $this->input->post('rembesan_pump_pit_r_q') ?: null,
            'keterangan' => $this->input->post('keterangan') ?: null,
        ]);
        
        $this->session->set_flashdata('success', 'Data bendungan berhasil diperbarui!');
        redirect('petugas/kelola?bulan=' . date('Y-m', strtotime($tanggal)));
    }

    public function hapus_bendungan($id_bendungan) {
        $id_pos = $this->session->userdata('id_pos');
        $this->M_petugas->delete_bendungan($id_bendungan, $id_pos)
            ? $this->session->set_flashdata('success', 'Data berhasil dihapus.')
            : $this->session->set_flashdata('error', 'Data tidak ditemukan.');
        redirect('petugas/kelola');
    }
}