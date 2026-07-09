<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Petugas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->model('M_petugas');
        $this->load->helper(['url', 'form', 'hydro']);
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        if ($this->session->userdata('role') !== 'petugas') {
            show_error('Akses Ditolak. Anda bukan Petugas Lapangan.', 403);
        }
    }

    private function _get_assigned_pos_ids() {
        $raw = $this->session->userdata('id_pos');
        if (empty($raw)) return [];
        return strpos($raw, ',') !== false 
            ? array_map('trim', explode(',', $raw)) 
            : [trim($raw)];
    }

    private function _validate_pos($id_pos) {
        return in_array((string)$id_pos, $this->_get_assigned_pos_ids());
    }

    private function _parse_float($value) {
        return ($value === '' || $value === null) ? null : (float)$value;
    }

    private function _render($view, $data) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/v_petugas_layout', $data);
    }

    public function index() {
        redirect('petugas/input');
    }

    // ==========================================
    // FORM INPUT
    // ==========================================
    public function input() {
        $assigned = $this->_get_assigned_pos_ids();
        if (empty($assigned)) {
            show_error('Anda belum dikaitkan dengan pos manapun.', 403);
        }

        $id_pos_active = $this->input->get('pos');
        if (empty($id_pos_active) || !$this->_validate_pos($id_pos_active)) {
            $id_pos_active = $assigned[0];
        }

        $pos = $this->M_petugas->get_pos($id_pos_active);
        if (!$pos) {
            show_error('Data pos tidak ditemukan.', 404);
        }

        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        $daftar_pos = $this->M_petugas->get_pos_by_ids($assigned);

        $data = [
            'app_name'           => 'HydroSmart',
            'title'              => 'Form Input',
            'petugas_name'       => $this->session->userdata('nama_lengkap'),
            'pos'                => $pos,
            'tanggal'            => $tanggal,
            'daftar_pos_petugas' => $daftar_pos,
            'id_pos_active'      => $id_pos_active
        ];

        // Tentukan view berdasarkan tipe pos
        if ($pos->is_bendung == 1) {
            // View input bendung
            $data['data_list'] = $this->M_petugas->get_bendung_by_tanggal($id_pos_active, $tanggal);
            $data['content'] = $this->load->view('petugas/v_input_bendung', $data, TRUE);
        } elseif ($pos->is_bendungan == 1) {
            // View input bendungan
            $data['content'] = $this->load->view('petugas/v_input_bendungan', $data, TRUE);
        } else {
            // View input pos biasa (PCH/PDA)
            $data['data_list'] = $this->M_petugas->get_by_tanggal($id_pos_active, $tanggal);
            $data['content'] = $this->load->view('petugas/v_input_manual', $data, TRUE);
        }

        $this->load->view('layout/v_petugas_layout', $data);
    }

    // ==========================================
    // SIMPAN DATA POS BIASA
    // ==========================================
    public function simpan() {
        $id_pos  = $this->input->post('id_pos');
        $tanggal = $this->input->post('tanggal');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');

        if (!$this->_validate_pos($id_pos)) {
            show_error('Akses Terblokir!', 403);
        }

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        $pos = $this->M_petugas->get_pos($id_pos);
        if ($pos->tipe_pos == 'PCH') {
            $this->form_validation->set_rules('rain', 'Curah Hujan', 'required|numeric|greater_than_equal_to[0]');
        } else {
            $this->form_validation->set_rules('wlevel', 'TMA', 'required|numeric|greater_than_equal_to[0]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('petugas/input?pos=' . $id_pos . '&tanggal=' . $tanggal);
        }

        $this->M_petugas->insert([
            'id_pos'        => $id_pos,
            'id_user'       => $user_id,
            'tanggal_input' => $tanggal,
            'rain'          => $this->_parse_float($this->input->post('rain')),
            'wlevel'        => cm_to_m($this->input->post('wlevel')),
            'keterangan'    => $this->input->post('keterangan') ?: null,
        ]);

        $this->session->set_flashdata('success', 'Data berhasil disimpan!');
        redirect('petugas/input?pos=' . $id_pos . '&tanggal=' . $tanggal);
    }

    // ==========================================
    // SIMPAN DATA BENDUNGAN (DENGAN KOLOM BARU)
    // ==========================================
    public function simpan_bendungan() {
        $id_pos  = $this->input->post('id_pos');
        $tanggal = $this->input->post('tanggal');
        
        if (!$this->_validate_pos($id_pos)) {
            show_error('Akses Terblokir!', 403);
        }

        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');

        // Update data tetap bendungan di master_pos (jika diisi)
        $nwl = $this->input->post('nwl');
        if ($nwl !== null && $nwl !== '') {
            $this->db->where('id_pos', $id_pos)->update('master_pos', [
                'nwl'        => $this->_parse_float($nwl),
                'nwl_volume' => $this->_parse_float($this->input->post('nwl_volume')),
                'nwl_luas'   => $this->_parse_float($this->input->post('nwl_luas')),
            ]);
        }

        $this->M_petugas->insert_bendungan([
            'id_pos'                => $id_pos,
            'id_user'               => $user_id,
            'tanggal_input'         => $tanggal,
            'nwl'                   => $this->_parse_float($this->input->post('nwl')),
            'nwl_volume'            => $this->_parse_float($this->input->post('nwl_volume')),
            'nwl_luas'              => $this->_parse_float($this->input->post('nwl_luas')),
            'rain'                  => $this->_parse_float($this->input->post('rain')),
            'elevasi'               => cm_to_m($this->input->post('elevasi')),
            'volume'                => $this->_parse_float($this->input->post('volume')),
            'luas'                  => $this->_parse_float($this->input->post('luas')),
            'inflow'                => $this->_parse_float($this->input->post('inflow')),
            'pltm'                  => $this->_parse_float($this->input->post('pltm')),
            'spillway'              => $this->_parse_float($this->input->post('spillway')),
            'total_outflow'         => $this->_parse_float($this->input->post('total_outflow')),
            'plta_status'           => $this->input->post('plta_status') ?: null,
            'irigasi_status'        => $this->input->post('irigasi_status') ?: null,
            'tail_water'            => $this->input->post('tail_water') ?: null,
            'rembesan_vnotch_h'     => $this->_parse_float($this->input->post('rembesan_vnotch_h')),
            'rembesan_vnotch_q'     => $this->_parse_float($this->input->post('rembesan_vnotch_q')),
            'rembesan_pump_pit_l_h' => $this->_parse_float($this->input->post('rembesan_pump_pit_l_h')),
            'rembesan_pump_pit_l_q' => $this->_parse_float($this->input->post('rembesan_pump_pit_l_q')),
            'rembesan_pump_pit_r_h' => $this->_parse_float($this->input->post('rembesan_pump_pit_r_h')),
            'rembesan_pump_pit_r_q' => $this->_parse_float($this->input->post('rembesan_pump_pit_r_q')),
            'keterangan'            => $this->input->post('keterangan') ?: null,
            // KOLOM BARU
            'tahun_mulai_pembangunan' => !empty($this->input->post('tahun_mulai_pembangunan')) ? $this->input->post('tahun_mulai_pembangunan') : null,
            'tipe_bendungan'          => $this->input->post('tipe_bendungan') ?: null,
            'elevasi_mercu'           => $this->_parse_float($this->input->post('elevasi_mercu')),
            'luas_das'                => $this->_parse_float($this->input->post('luas_das')),
        ]);

        // Juga insert ke data_manual jika rain/elevasi diisi
        $rain = $this->input->post('rain');
        $elevasi = $this->input->post('elevasi');
        if (($rain !== '' && $rain !== null) || ($elevasi !== '' && $elevasi !== null)) {
            $this->M_petugas->insert([
                'id_pos'        => $id_pos,
                'id_user'       => $user_id,
                'tanggal_input' => $tanggal,
                'rain'          => $this->_parse_float($rain),
                'wlevel'        => $this->_parse_float($elevasi),
            ]);
        }

        $this->session->set_flashdata('success', 'Data bendungan berhasil disimpan!');
        redirect('petugas/input?pos=' . $id_pos . '&tanggal=' . $tanggal);
    }

    // ==========================================
    // SIMPAN DATA BENDUNG (SESUAI STRUKTUR TERBARU)
    // ==========================================
    public function simpan_bendung() {
        $id_pos  = $this->input->post('id_pos');
        $tanggal = $this->input->post('tanggal');
        
        if (!$this->_validate_pos($id_pos)) {
            show_error('Akses Terblokir!', 403);
        }

        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');

        $this->M_petugas->insert_bendung([
            'id_pos'        => $id_pos,
            'id_user'       => $user_id,
            'tanggal_input' => $tanggal,
            'rain'          => $this->_parse_float($this->input->post('rain')),
            'elevasi_mercu' => cm_to_m($this->input->post('elevasi_mercu')),
            'q_total'       => $this->_parse_float($this->input->post('q_total')),
            'q_fc1'         => $this->_parse_float($this->input->post('q_fc1')),
            'q_fc2'         => $this->_parse_float($this->input->post('q_fc2')),
            'q_sal_induk'   => $this->_parse_float($this->input->post('q_sal_induk')),
            'q_limpas'      => $this->_parse_float($this->input->post('q_limpas')),
            'q_sungai'      => $this->_parse_float($this->input->post('q_sungai')),
            'q_spam_kpbu'   => $this->_parse_float($this->input->post('q_spam_kpbu')),
            'sluice_gate'   => $this->_parse_float($this->input->post('sluice_gate')),
            'bukaan_pintu'  => $this->_parse_float($this->input->post('bukaan_pintu')),
            'keterangan'    => $this->input->post('keterangan') ?: null,
        ]);

        $this->session->set_flashdata('success', 'Data bendung berhasil disimpan!');
        redirect('petugas/input?pos=' . $id_pos . '&tanggal=' . $tanggal);
    }

    // ==========================================
    // RIWAYAT DATA (KELOLA) - FILTER PER HARI
    // ==========================================
    public function kelola() {
        $assigned = $this->_get_assigned_pos_ids();
        if (empty($assigned)) {
            show_error('Anda belum dikaitkan dengan pos manapun.', 403);
        }

        $id_pos_active = $this->input->get('pos');
        if (empty($id_pos_active) || !$this->_validate_pos($id_pos_active)) {
            $id_pos_active = $assigned[0];
        }

        $pos = $this->M_petugas->get_pos($id_pos_active);
        if (!$pos) {
            show_error('Data pos tidak ditemukan.', 404);
        }

        // Filter per hari (tanggal lengkap: Y-m-d)
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $daftar_pos = $this->M_petugas->get_pos_by_ids($assigned);

        $data = [
            'app_name'           => 'HydroSmart',
            'title'              => 'Riwayat Laporan',
            'petugas_name'       => $this->session->userdata('nama_lengkap'),
            'pos'                => $pos,
            'tanggal'            => $tanggal,
            'daftar_pos_petugas' => $daftar_pos,
            'id_pos_active'      => $id_pos_active,
            'data_list'          => []
        ];

        // Tentukan view berdasarkan tipe pos
        if ($pos->is_bendung == 1) {
            // Riwayat bendung
            $data['data_list'] = $this->M_petugas->get_bendung_by_tanggal($id_pos_active, $tanggal);
            $this->_render('petugas/v_kelola_bendung', $data);
        } elseif ($pos->is_bendungan == 1) {
            // Riwayat bendungan
            $data['data_list'] = $this->M_petugas->get_bendungan_by_tanggal($id_pos_active, $tanggal);
            $this->_render('petugas/v_kelola_bendungan', $data);
        } else {
            // Riwayat pos biasa
            $data['data_list'] = $this->M_petugas->get_by_tanggal_with_user($id_pos_active, $tanggal);
            $this->_render('petugas/v_kelola_manual', $data);
        }
    }

    // ==========================================
    // RIWAYAT DATA BENDUNG (KHUSUS BENDUNG)
    // ==========================================
    public function kelola_bendung() {
        $assigned = $this->_get_assigned_pos_ids();
        if (empty($assigned)) {
            show_error('Anda belum dikaitkan dengan pos manapun.', 403);
        }

        $id_pos_active = $this->input->get('pos');
        
        // Jika tidak ada pos yang dipilih atau tidak valid, cari pos bendung pertama
        if (empty($id_pos_active) || !$this->_validate_pos($id_pos_active)) {
            $id_pos_active = null;
            foreach ($assigned as $id) {
                $pos_check = $this->M_petugas->get_pos($id);
                if ($pos_check && $pos_check->is_bendung == 1) {
                    $id_pos_active = $id;
                    break;
                }
            }
            if (empty($id_pos_active)) {
                show_error('Tidak ada pos bendung yang tersedia.', 404);
            }
        }

        $pos = $this->M_petugas->get_pos($id_pos_active);
        if (!$pos) {
            show_error('Data pos tidak ditemukan.', 404);
        }

        // Filter per hari (tanggal lengkap: Y-m-d)
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $daftar_pos = $this->M_petugas->get_pos_by_ids($assigned);

        // Ambil data bendung
        $data_list = $this->M_petugas->get_bendung_by_tanggal($id_pos_active, $tanggal);

        $data = [
            'app_name'           => 'HydroSmart',
            'title'              => 'Riwayat Laporan Bendung',
            'petugas_name'       => $this->session->userdata('nama_lengkap'),
            'pos'                => $pos,
            'tanggal'            => $tanggal,
            'daftar_pos_petugas' => $daftar_pos,
            'id_pos_active'      => $id_pos_active,
            'data_list'          => $data_list
        ];

        $this->_render('petugas/v_kelola_bendung', $data);
    }

    // ==========================================
    // RIWAYAT DATA BENDUNGAN (KHUSUS BENDUNGAN)
    // ==========================================
    public function kelola_bendungan() {
        $assigned = $this->_get_assigned_pos_ids();
        if (empty($assigned)) {
            show_error('Anda belum dikaitkan dengan pos manapun.', 403);
        }

        $id_pos_active = $this->input->get('pos');
        
        // Jika tidak ada pos yang dipilih atau tidak valid, cari pos bendungan pertama
        if (empty($id_pos_active) || !$this->_validate_pos($id_pos_active)) {
            $id_pos_active = null;
            foreach ($assigned as $id) {
                $pos_check = $this->M_petugas->get_pos($id);
                if ($pos_check && $pos_check->is_bendungan == 1) {
                    $id_pos_active = $id;
                    break;
                }
            }
            if (empty($id_pos_active)) {
                show_error('Tidak ada pos bendungan yang tersedia.', 404);
            }
        }

        $pos = $this->M_petugas->get_pos($id_pos_active);
        if (!$pos) {
            show_error('Data pos tidak ditemukan.', 404);
        }

        // Filter per hari (tanggal lengkap: Y-m-d)
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $daftar_pos = $this->M_petugas->get_pos_by_ids($assigned);

        // Ambil data bendungan
        $data_list = $this->M_petugas->get_bendungan_by_tanggal($id_pos_active, $tanggal);

        $data = [
            'app_name'           => 'HydroSmart',
            'title'              => 'Riwayat Laporan Bendungan',
            'petugas_name'       => $this->session->userdata('nama_lengkap'),
            'pos'                => $pos,
            'tanggal'            => $tanggal,
            'daftar_pos_petugas' => $daftar_pos,
            'id_pos_active'      => $id_pos_active,
            'data_list'          => $data_list
        ];

        $this->_render('petugas/v_kelola_bendungan', $data);
    }
}