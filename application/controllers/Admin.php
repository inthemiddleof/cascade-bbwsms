<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->model('M_admin');
        $this->load->helper(['url', 'form']);
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
        
        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        // Cek role
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Akses Ditolak. Anda bukan Admin Wilayah.', 403);
        }
    }

    // ==========================================
    // HELPER: Ambil ID pos yang diizinkan
    // ==========================================
    private function _get_allowed_pos_ids() {
        $id_user = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $user = $this->db->select('id_pos')->where('id_user', $id_user)->get('users')->row();
        
        if (!empty($user->id_pos)) {
            return array_map('trim', explode(',', $user->id_pos));
        }
        return [0];
    }

    // ==========================================
    // HELPER: Render view dengan layout
    // ==========================================
    private function _render($view, $data) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/v_superadmin_layout', $data);
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function index() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        $data = $this->M_admin->get_dashboard_data($allowed_pos);
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('admin/v_dashboard', $data);
    }

    // ==========================================
    // KELOLA PETUGAS
    // ==========================================
    public function kelola_petugas() {
        $data = $this->M_admin->get_petugas_data($this->_get_allowed_pos_ids());
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('admin/v_kelola_petugas', $data);
    }

    public function tambah_petugas() {
        $result = $this->M_admin->insert_petugas(
            $this->input->post(), 
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_petugas');
    }

    public function edit_petugas() {
        $result = $this->M_admin->update_petugas(
            $this->input->post(), 
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_petugas');
    }

    public function hapus_petugas($id) {
        $result = $this->M_admin->delete_petugas($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_petugas');
    }

    public function nonaktifkan_petugas($id) {
        $this->M_admin->set_status($id, 'nonaktif');
        $this->session->set_flashdata('success', 'Petugas dinonaktifkan.');
        redirect('admin/kelola_petugas');
    }

    public function aktifkan_petugas($id) {
        $this->M_admin->set_status($id, 'aktif');
        $this->session->set_flashdata('success', 'Petugas diaktifkan.');
        redirect('admin/kelola_petugas');
    }

    // ==========================================
    // KELOLA MANUAL (DENGAN DUKUNGAN BENDUNG)
    // ==========================================
    public function kelola_manual() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        
        $data = $this->M_admin->get_manual_data(
            $this->input->get('pos'), 
            $this->input->get('bulan') ?: date('Y-m'),
            $allowed_pos
        );
        
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        
        // Siapkan data untuk JavaScript
        $data['pos_data_js'] = [];
        $data['bendungan_data_js'] = [];
        $data['bendung_data_js'] = [];
        
        if (!empty($data['pos']) && !empty($data['data_list'])) {
            if ($data['pos']->is_bendung == 1) {
                // Data bendung (SESUAI STRUKTUR TERBARU)
                foreach ($data['data_list'] as $d) {
                    $data['bendung_data_js'][$d->id_bendung] = [
                        'tanggal'        => $d->tanggal_input,
                        'rain'           => $d->rain,
                        'elevasi_mercu'  => $d->elevasi_mercu,
                        'q_total'        => $d->q_total,
                        'q_fc1'          => $d->q_fc1,
                        'q_fc2'          => $d->q_fc2,
                        'q_sal_induk'    => $d->q_sal_induk,
                        'q_limpas'       => $d->q_limpas,
                        'q_sungai'       => $d->q_sungai,
                        'q_spam_kpbu'    => $d->q_spam_kpbu,
                        'sluice_gate'    => $d->sluice_gate,
                        'bukaan_pintu'   => $d->bukaan_pintu,
                        'keterangan'     => $d->keterangan ?? '',
                    ];
                }
            } elseif ($data['pos']->is_bendungan == 1) {
                // Data bendungan (DENGAN KOLOM BARU)
                foreach ($data['data_list'] as $d) {
                    $data['bendungan_data_js'][$d->id_bendungan] = [
                        'tanggal'                  => $d->tanggal_input,
                        'nwl'                      => $d->nwl,
                        'nwl_volume'               => $d->nwl_volume,
                        'nwl_luas'                 => $d->nwl_luas,
                        'rain'                     => $d->rain,
                        'elevasi'                  => $d->elevasi,
                        'volume'                   => $d->volume,
                        'luas'                     => $d->luas,
                        'inflow'                   => $d->inflow,
                        'pltm'                     => $d->pltm,
                        'spillway'                 => $d->spillway,
                        'total_outflow'            => $d->total_outflow,
                        'plta_status'              => $d->plta_status ?? '',
                        'irigasi_status'           => $d->irigasi_status ?? '',
                        'tail_water'               => $d->tail_water ?? '',
                        'rvh'                      => $d->rembesan_vnotch_h,
                        'rvq'                      => $d->rembesan_vnotch_q,
                        'rplh'                     => $d->rembesan_pump_pit_l_h,
                        'rplq'                     => $d->rembesan_pump_pit_l_q,
                        'rprh'                     => $d->rembesan_pump_pit_r_h,
                        'rprq'                     => $d->rembesan_pump_pit_r_q,
                        'keterangan'               => $d->keterangan ?? '',
                        // KOLOM BARU
                        'tahun_mulai_pembangunan'  => $d->tahun_mulai_pembangunan ?? '',
                        'tipe_bendungan'           => $d->tipe_bendungan ?? '',
                        'elevasi_mercu'            => $d->elevasi_mercu,
                        'luas_das'                 => $d->luas_das,
                    ];
                }
            } else {
                // Data pos biasa (PCH/PDA)
                foreach ($data['data_list'] as $d) {
                    $data['pos_data_js'][$d->id_manual] = [
                        'tanggal'    => $d->tanggal_input,
                        'rain'       => $d->rain,
                        'wlevel'     => $d->wlevel,
                        'keterangan' => $d->keterangan ?? '',
                    ];
                }
            }
        }
        
        $this->_render('admin/v_kelola_manual', $data);
    }

    // ==========================================
    // SIMPAN DATA
    // ==========================================
    public function simpan_data_pos() {
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $result = $this->M_admin->insert_manual_pos(
            $this->input->post(),
            $user_id,
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal_input')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    public function simpan_bendungan() {
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        
        // Ambil data POST
        $post = $this->input->post();
        
        // Tambahkan kolom baru ke data
        $post['tahun_mulai_pembangunan'] = $this->input->post('tahun_mulai_pembangunan');
        $post['tipe_bendungan'] = $this->input->post('tipe_bendungan');
        $post['elevasi_mercu'] = $this->input->post('elevasi_mercu');
        $post['luas_das'] = $this->input->post('luas_das');
        
        $result = $this->M_admin->insert_manual_bendungan(
            $post,
            $user_id,
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal_input')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    /**
     * Simpan data bendung (SESUAI STRUKTUR TERBARU)
     */
    public function simpan_bendung() {
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $result = $this->M_admin->insert_manual_bendung(
            $this->input->post(),
            $user_id,
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal_input')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    // ==========================================
    // UPDATE DATA
    // ==========================================
    public function update_manual() {
        $result = $this->M_admin->update_manual_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    public function update_bendungan() {
        // Ambil data POST
        $post = $this->input->post();
        
        // Tambahkan kolom baru ke data
        $post['tahun_mulai_pembangunan'] = $this->input->post('tahun_mulai_pembangunan');
        $post['tipe_bendungan'] = $this->input->post('tipe_bendungan');
        $post['elevasi_mercu'] = $this->input->post('elevasi_mercu');
        $post['luas_das'] = $this->input->post('luas_das');
        
        $result = $this->M_admin->update_manual_bendungan($post);
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    /**
     * Update data bendung (SESUAI STRUKTUR TERBARU)
     */
    public function update_bendung() {
        $result = $this->M_admin->update_manual_bendung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    // ==========================================
    // HAPUS DATA
    // ==========================================
    public function hapus_manual($id) {
        $result = $this->M_admin->delete_manual_pos($id, $this->_get_allowed_pos_ids());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    public function hapus_bendungan($id) {
        $result = $this->M_admin->delete_manual_bendungan($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    /**
     * Hapus data bendung (SESUAI STRUKTUR TERBARU)
     */
    public function hapus_bendung($id) {
        $result = $this->M_admin->delete_manual_bendung($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    // ==========================================
    // AJAX: GET DATA BY ID (UNTUK EDIT MODAL)
    // ==========================================
    
    /**
     * Get data bendung by ID (AJAX) - SESUAI STRUKTUR TERBARU
     */
    public function get_bendung_json($id_bendung) {
        $data = $this->M_admin->get_bendung_by_id($id_bendung);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    /**
     * Get data bendungan by ID (AJAX) - DENGAN KOLOM BARU
     */
    public function get_bendungan_json($id_bendungan) {
        $data = $this->M_admin->get_bendungan_by_id($id_bendungan);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    /**
     * Get data pos manual by ID (AJAX)
     */
    public function get_manual_json($id_manual) {
        $data = $this->M_admin->get_manual_by_id($id_manual);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }
}