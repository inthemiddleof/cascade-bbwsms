<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Superadmin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->model('M_superadmin');
        $this->load->helper(['url', 'form']);
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
        
        if (!$this->session->userdata('logged_in')) redirect('auth');
        if ($this->session->userdata('role') !== 'superadmin') show_error('Akses Ditolak.', 403);
    }

    private function _render($view, $data) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/v_superadmin_layout', $data);
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function index() {
        $data = $this->M_superadmin->get_dashboard_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_dashboard', $data);
    }

    // ==========================================
    // KELOLA POS
    // ==========================================
    public function kelola_pos() {
        $data = $this->M_superadmin->get_pos_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_pos', $data);
    }

    public function tambah_pos() {
        $result = $this->M_superadmin->insert_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pos');
    }

    public function edit_pos() {
        $result = $this->M_superadmin->update_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pos');
    }

    public function hapus_pos($id) {
        $result = $this->M_superadmin->delete_pos($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pos');
    }

    // ==========================================
    // KELOLA ADMIN
    // ==========================================
    public function kelola_admin() {
        $data = $this->M_superadmin->get_admin_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_admin', $data);
    }

    public function tambah_admin() {
        $result = $this->M_superadmin->insert_admin($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_admin');
    }

    public function edit_admin() {
        $result = $this->M_superadmin->update_admin($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_admin');
    }

    public function hapus_admin($id) {
        $result = $this->M_superadmin->delete_admin($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_admin');
    }

    public function nonaktifkan_admin($id) {
        $this->M_superadmin->set_admin_status($id, 'nonaktif');
        $this->session->set_flashdata('success', 'Admin dinonaktifkan.');
        redirect('superadmin/kelola_admin');
    }

    public function aktifkan_admin($id) {
        $this->M_superadmin->set_admin_status($id, 'aktif');
        $this->session->set_flashdata('success', 'Admin diaktifkan.');
        redirect('superadmin/kelola_admin');
    }

    // ==========================================
    // KELOLA MANUAL
    // ==========================================
    public function kelola_manual() {
        $this->load->model('M_admin');
        
        $data = $this->M_admin->get_manual_data(
            $this->input->get('pos'), 
            $this->input->get('bulan') ?: date('Y-m'),
            null // Superadmin bisa akses semua pos
        );
        
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        
        // Siapkan data untuk JavaScript (fix bug edit)
        $data['pos_data_js'] = [];
        $data['bendungan_data_js'] = [];
        $data['bendung_data_js'] = [];
        
        if (!empty($data['pos']) && !empty($data['data_list'])) {
            if ($data['pos']->is_bendung == 1) {
                // Data bendung
                foreach ($data['data_list'] as $d) {
                    $data['bendung_data_js'][$d->id_bendung] = [
                        'tanggal'      => $d->tanggal_input,
                        'rain'         => $d->rain,
                        'elevasi_mercu'=> $d->elevasi_mercu,
                        'q_total'      => $d->q_total,
                        'q_fc1'        => $d->q_fc1,
                        'q_fc2'        => $d->q_fc2,
                        'q_limpas'     => $d->q_limpas,
                        'q_spam_kpbu'  => $d->q_spam_kpbu,
                        'sluice_gate'  => $d->sluice_gate,
                        'keterangan'   => $d->keterangan ?? '',
                    ];
                }
            } elseif ($data['pos']->is_bendungan == 1) {
                // Data bendungan
                foreach ($data['data_list'] as $d) {
                    $data['bendungan_data_js'][$d->id_bendungan] = [
                        'tanggal'        => $d->tanggal_input,
                        'nwl'            => $d->nwl,
                        'nwl_volume'     => $d->nwl_volume,
                        'nwl_luas'       => $d->nwl_luas,
                        'rain'           => $d->rain,
                        'elevasi'        => $d->elevasi,
                        'volume'         => $d->volume,
                        'luas'           => $d->luas,
                        'inflow'         => $d->inflow,
                        'pltm'           => $d->pltm,
                        'spillway'       => $d->spillway,
                        'total_outflow'  => $d->total_outflow,
                        'plta_status'    => $d->plta_status ?? '',
                        'irigasi_status' => $d->irigasi_status ?? '',
                        'tail_water'     => $d->tail_water ?? '',
                        'rvh'            => $d->rembesan_vnotch_h,
                        'rvq'            => $d->rembesan_vnotch_q,
                        'rplh'           => $d->rembesan_pump_pit_l_h,
                        'rplq'           => $d->rembesan_pump_pit_l_q,
                        'rprh'           => $d->rembesan_pump_pit_r_h,
                        'rprq'           => $d->rembesan_pump_pit_r_q,
                        'keterangan'     => $d->keterangan ?? '',
                    ];
                }
            } else {
                // Data pos biasa
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
        
        $this->_render('superadmin/v_kelola_manual', $data);
    }

    // ==========================================
    // SIMPAN DATA
    // ==========================================
    public function simpan_data_pos() {
        $this->load->model('M_admin');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $result = $this->M_admin->insert_manual_pos(
            $this->input->post(),
            $user_id,
            null
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    public function simpan_bendungan() {
        $this->load->model('M_admin');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $result = $this->M_admin->insert_manual_bendungan(
            $this->input->post(),
            $user_id,
            null
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    // ==========================================
    // SIMPAN BENDUNG (BARU)
    // ==========================================
    public function simpan_bendung() {
        $this->load->model('M_admin');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $result = $this->M_admin->insert_manual_bendung(
            $this->input->post(),
            $user_id,
            null
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    // ==========================================
    // UPDATE DATA
    // ==========================================
    public function update_manual() {
        $this->load->model('M_admin');
        $result = $this->M_admin->update_manual_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal'))));
    }

    public function update_bendungan() {
        $this->load->model('M_admin');
        $result = $this->M_admin->update_manual_bendungan($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal'))));
    }

    // ==========================================
    // UPDATE BENDUNG (BARU)
    // ==========================================
    public function update_bendung() {
        $this->load->model('M_admin');
        $result = $this->M_admin->update_manual_bendung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal'))));
    }

    // ==========================================
    // HAPUS DATA
    // ==========================================
    public function hapus_manual($id) {
        $this->load->model('M_admin');
        $result = $this->M_admin->delete_manual_pos($id, null);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    public function hapus_bendungan($id) {
        $this->load->model('M_admin');
        $result = $this->M_admin->delete_manual_bendungan($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    // ==========================================
    // HAPUS BENDUNG (BARU)
    // ==========================================
    public function hapus_bendung($id) {
        $this->load->model('M_admin');
        $result = $this->M_admin->delete_manual_bendung($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->get('pos'));
    }
}