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
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Akses Ditolak. Anda bukan Admin Wilayah.', 403);
        }
    }

    private function _get_allowed_pos_ids() {
        $username = $this->session->userdata('username');
        $id_user = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        
        // Admin irigasi - tidak akses ke master_pos
        if ($username == 'irigasi' || $username == 'Irigasi') {
            return [0];
        }
        
        // Admin pantai - tidak akses ke master_pos
        if ($username == 'pantai' || $username == 'Pantai') {
            return [0];
        }
        
        // Admin sedimen - tidak akses ke master_pos
        if ($username == 'sedimen' || $username == 'Sedimen') {
            return [0];
        }
        
        // Admin embung
        if ($username == 'Embung' || $username == 'embung') {
            $user = $this->db->select('id_pos')->where('id_user', $id_user)->get('users')->row();
            
            $ids = [];
            if (!empty($user->id_pos)) {
                $ids = array_map('trim', explode(',', $user->id_pos));
            }
            
            if (empty($ids)) return [0];
            
            $this->db->where_in('id_pos', $ids);
            $this->db->where('jenis_aset', 'embung');
            $filtered = $this->db->get('master_pos')->result();
            $result = array_column($filtered, 'id_pos');
            
            return empty($result) ? [0] : $result;
        }
        
        // Untuk admin hidrologi/bendungan/bendung
        $user = $this->db->select('id_pos')->where('id_user', $id_user)->get('users')->row();
        
        if (!empty($user->id_pos)) {
            return array_map('trim', explode(',', $user->id_pos));
        }
        return [0];
    }

    private function _render($view, $data) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
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
    // KELOLA MANUAL - MAIN
    // ==========================================
    public function kelola_manual() {
        $username = $this->session->userdata('username');
        $allowed_pos = $this->_get_allowed_pos_ids();
        
        $admin_type = 'hidrologi';
        $username_lower = strtolower($username);
        
        if ($username_lower == 'irigasi') {
            $admin_type = 'irigasi';
        } elseif ($username_lower == 'pantai') {
            $admin_type = 'pantai';
        } elseif ($username_lower == 'sedimen') {
            $admin_type = 'sedimen';
        } elseif ($username_lower == 'embung') {
            $admin_type = 'embung';
        } elseif ($username_lower == 'hidrologi') {
            $admin_type = 'hidrologi';
        } else {
            if (!empty($allowed_pos) && $allowed_pos[0] != 0) {
                $this->db->where_in('id_pos', $allowed_pos);
                $pos_check = $this->db->get('master_pos')->result();
                $has_bendungan = false;
                $has_bendung = false;
                foreach ($pos_check as $p) {
                    if ($p->is_bendungan == 1) $has_bendungan = true;
                    if ($p->is_bendung == 1) $has_bendung = true;
                }
                if ($has_bendungan) $admin_type = 'bendungan';
                elseif ($has_bendung) $admin_type = 'bendung';
            }
        }
        
        $data = $this->M_admin->get_manual_data(
            $this->input->get('pos'), 
            $this->input->get('bulan') ?: date('Y-m'),
            $allowed_pos,
            $admin_type
        );
        
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['admin_type'] = $admin_type;
        
        $this->_render('admin/v_kelola_manual', $data);
    }

    // ==========================================
    // TAMBAH DATA - HIDROLOGI (POS)
    // ==========================================
    public function tambah_manual() {
        $pos_id = $this->input->get('pos');
        $pos = $this->db->where('id_pos', $pos_id)->get('master_pos')->row();
        
        if (!$pos) {
            show_error('Pos tidak ditemukan', 404);
        }
        
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Tambah Data Manual - ' . $pos->nama_pos;
        $data['pos'] = $pos;
        $data['tanggal'] = date('Y-m-d');
        $data['admin_type'] = 'hidrologi';
        
        $this->_render('admin/v_tambah_manual', $data);
    }

    // ==========================================
    // EDIT DATA - HIDROLOGI (POS)
    // ==========================================
    public function edit_manual($id) {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Edit Data Manual';
        $data['data'] = $this->M_admin->get_manual_by_id($id);
        $data['admin_type'] = 'hidrologi';
        
        if (!$data['data']) {
            show_error('Data tidak ditemukan', 404);
        }
        
        $this->_render('admin/v_edit_manual', $data);
    }

    // ==========================================
    // TAMBAH DATA - BENDUNGAN
    // ==========================================
    public function tambah_bendungan() {
        $pos_id = $this->input->get('pos');
        $pos = $this->db->where('id_pos', $pos_id)->get('master_pos')->row();
        
        if (!$pos) {
            show_error('Pos tidak ditemukan', 404);
        }
        
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Tambah Data Bendungan - ' . $pos->nama_pos;
        $data['pos'] = $pos;
        $data['tanggal'] = date('Y-m-d');
        $data['admin_type'] = 'bendungan';
        
        $this->_render('admin/v_tambah_bendungan', $data);
    }

    // ==========================================
    // EDIT DATA - BENDUNGAN
    // ==========================================
    public function edit_bendungan($id) {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Edit Data Bendungan';
        $data['data'] = $this->M_admin->get_bendungan_by_id($id);
        $data['admin_type'] = 'bendungan';
        
        if (!$data['data']) {
            show_error('Data tidak ditemukan', 404);
        }
        
        $this->_render('admin/v_edit_bendungan', $data);
    }

    // ==========================================
    // TAMBAH DATA - BENDUNG
    // ==========================================
    public function tambah_bendung() {
        $pos_id = $this->input->get('pos');
        $pos = $this->db->where('id_pos', $pos_id)->get('master_pos')->row();
        
        if (!$pos) {
            show_error('Pos tidak ditemukan', 404);
        }
        
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Tambah Data Bendung - ' . $pos->nama_pos;
        $data['pos'] = $pos;
        $data['tanggal'] = date('Y-m-d');
        $data['admin_type'] = 'bendung';
        
        $this->_render('admin/v_tambah_bendung', $data);
    }

    // ==========================================
    // EDIT DATA - BENDUNG
    // ==========================================
    public function edit_bendung($id) {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Edit Data Bendung';
        $data['data'] = $this->M_admin->get_bendung_by_id($id);
        $data['admin_type'] = 'bendung';
        
        if (!$data['data']) {
            show_error('Data tidak ditemukan', 404);
        }
        
        $this->_render('admin/v_edit_bendung', $data);
    }

    // ==========================================
    // TAMBAH DATA - IRIGASI
    // ==========================================
    public function tambah_irigasi() {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Tambah Data Irigasi';
        $data['admin_type'] = 'irigasi';
        
        $this->_render('admin/v_tambah_irigasi', $data);
    }

    // ==========================================
    // EDIT DATA - IRIGASI
    // ==========================================
    public function edit_irigasi($id) {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Edit Data Irigasi';
        $data['data'] = $this->M_admin->get_irigasi_by_id($id);
        $data['admin_type'] = 'irigasi';
        
        if (!$data['data']) {
            show_error('Data tidak ditemukan', 404);
        }
        
        $this->_render('admin/v_edit_irigasi', $data);
    }

    // ==========================================
    // TAMBAH DATA - PANTAI
    // ==========================================
    public function tambah_pantai() {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Tambah Data Pengaman Pantai';
        $data['admin_type'] = 'pantai';
        
        $this->_render('admin/v_tambah_pantai', $data);
    }

    // ==========================================
    // EDIT DATA - PANTAI
    // ==========================================
    public function edit_pantai($id) {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Edit Data Pengaman Pantai';
        $data['data'] = $this->M_admin->get_pantai_by_id($id);
        $data['admin_type'] = 'pantai';
        
        if (!$data['data']) {
            show_error('Data tidak ditemukan', 404);
        }
        
        $this->_render('admin/v_edit_pantai', $data);
    }

    // ==========================================
    // TAMBAH DATA - SEDIMEN
    // ==========================================
    public function tambah_sedimen() {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Tambah Data Pengendali Sedimen';
        $data['admin_type'] = 'sedimen';
        
        $this->_render('admin/v_tambah_sedimen', $data);
    }

    // ==========================================
    // EDIT DATA - SEDIMEN
    // ==========================================
    public function edit_sedimen($id) {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Edit Data Pengendali Sedimen';
        $data['data'] = $this->M_admin->get_sedimen_by_id($id);
        $data['admin_type'] = 'sedimen';
        
        if (!$data['data']) {
            show_error('Data tidak ditemukan', 404);
        }
        
        $this->_render('admin/v_edit_sedimen', $data);
    }

    // ==========================================
    // TAMBAH DATA - EMBUNG
    // ==========================================
    public function tambah_embung() {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Tambah Data Embung';
        $data['admin_type'] = 'embung';
        
        // Ambil list pos embung untuk dropdown
        $allowed_pos = $this->_get_allowed_pos_ids();
        if (!empty($allowed_pos) && $allowed_pos[0] != 0) {
            $this->db->where_in('id_pos', $allowed_pos);
            $this->db->where('jenis_aset', 'embung');
            $data['pos_list'] = $this->db->order_by('nama_pos', 'ASC')->get('master_pos')->result();
        } else {
            $data['pos_list'] = [];
        }
        
        $this->_render('admin/v_tambah_embung', $data);
    }

    // ==========================================
    // EDIT DATA - EMBUNG
    // ==========================================
    public function edit_embung($id) {
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $data['title'] = 'Edit Data Embung';
        $data['data'] = $this->M_admin->get_embung_by_id($id);
        $data['admin_type'] = 'embung';
        
        if (!$data['data']) {
            show_error('Data tidak ditemukan', 404);
        }
        
        // Ambil list pos embung untuk dropdown
        $allowed_pos = $this->_get_allowed_pos_ids();
        if (!empty($allowed_pos) && $allowed_pos[0] != 0) {
            $this->db->where_in('id_pos', $allowed_pos);
            $this->db->where('jenis_aset', 'embung');
            $data['pos_list'] = $this->db->order_by('nama_pos', 'ASC')->get('master_pos')->result();
        } else {
            $data['pos_list'] = [];
        }
        
        $this->_render('admin/v_edit_embung', $data);
    }

    // ==========================================
    // SIMPAN DATA - POS
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

    // ==========================================
    // SIMPAN DATA - BENDUNGAN
    // ==========================================
    public function simpan_bendungan() {
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $post = $this->input->post();
        
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

    // ==========================================
    // SIMPAN DATA - BENDUNG
    // ==========================================
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
    // SIMPAN DATA - IRIGASI
    // ==========================================
    public function simpan_irigasi() {
        $result = $this->M_admin->insert_manual_irigasi($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    // ==========================================
    // SIMPAN DATA - PANTAI
    // ==========================================
    public function simpan_pantai() {
        $result = $this->M_admin->insert_manual_pantai($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    // ==========================================
    // SIMPAN DATA - SEDIMEN
    // ==========================================
    public function simpan_sedimen() {
        $result = $this->M_admin->insert_manual_sedimen($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    // ==========================================
    // SIMPAN DATA - EMBUNG
    // ==========================================
    public function simpan_embung() {
        $result = $this->M_admin->insert_manual_embung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
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
        $post = $this->input->post();
        
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

    public function update_bendung() {
        $result = $this->M_admin->update_manual_bendung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    // ==========================================
    // UPDATE DATA KHUSUS (IRIGASI, PANTAI, SEDIMEN, EMBUNG)
    // ==========================================
    public function update_irigasi() {
        $result = $this->M_admin->update_manual_irigasi($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    public function update_pantai() {
        $result = $this->M_admin->update_manual_pantai($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    public function update_sedimen() {
        $result = $this->M_admin->update_manual_sedimen($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    public function update_embung() {
        $result = $this->M_admin->update_manual_embung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
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

    public function hapus_bendung($id) {
        $result = $this->M_admin->delete_manual_bendung($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    public function hapus_irigasi($id) {
        $result = $this->M_admin->delete_manual_irigasi($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    public function hapus_pantai($id) {
        $result = $this->M_admin->delete_manual_pantai($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    public function hapus_sedimen($id) {
        $result = $this->M_admin->delete_manual_sedimen($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    public function hapus_embung($id) {
        $result = $this->M_admin->delete_manual_embung($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual');
    }

    // ==========================================
    // AJAX: GET DATA BY ID
    // ==========================================
    public function get_bendung_json($id_bendung) {
        $data = $this->M_admin->get_bendung_by_id($id_bendung);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    public function get_bendungan_json($id_bendungan) {
        $data = $this->M_admin->get_bendungan_by_id($id_bendungan);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    public function get_manual_json($id_manual) {
        $data = $this->M_admin->get_manual_by_id($id_manual);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    public function get_irigasi_json($id_irigasi) {
        $data = $this->M_admin->get_irigasi_by_id($id_irigasi);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    public function get_pantai_json($id_pengaman) {
        $data = $this->M_admin->get_pantai_by_id($id_pengaman);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    public function get_sedimen_json($id_sedimen) {
        $data = $this->M_admin->get_sedimen_by_id($id_sedimen);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    public function get_embung_json($id_embung) {
        $data = $this->M_admin->get_embung_by_id($id_embung);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    // ==========================================
    // EXPORT & IMPORT DATA
    // ==========================================
    public function export_import() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        
        $this->db->select('id_pos, nama_pos, tipe_pos');
        $this->db->where_in('id_pos', $allowed_pos);
        $pos_list = $this->db->order_by('nama_pos', 'ASC')->get('master_pos')->result();
        
        $modules = [
            'telemetri'   => 'Data Telemetri',
            'manual'      => 'Data Manual Pos',
            'bendung'     => 'Data Bendung',
            'bendungan'   => 'Data Bendungan',
            'all'         => 'Semua Data'
        ];
        
        $periods = [
            'all'   => 'Semua',
            'daily' => 'Harian',
            'month' => 'Bulanan'
        ];
        
        $user = $this->db->select('nama_lengkap')->where('id_user', $this->session->userdata('user_id'))->get('users')->row();
        
        $data = [
            'app_name'       => 'HydroSmart',
            'title'          => 'Export & Import Data',
            'admin_name'     => $this->session->userdata('nama_lengkap'),
            'wilayah_name'   => $user->nama_lengkap ?? 'Admin Wilayah',
            'pos_list'       => $pos_list,
            'modules'        => $modules,
            'periods'        => $periods
        ];
        
        $this->_render('admin/v_export_import', $data);
    }

    public function export_csv() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        $module = $this->input->get('module') ?: 'all';
        $id_pos = $this->input->get('id_pos');
        $period = $this->input->get('period') ?: 'all';
        $date = $this->input->get('date') ?: date('Y-m-d');
        
        if (!empty($id_pos) && !in_array($id_pos, $allowed_pos)) {
            show_error('Akses Ditolak!', 403);
        }
        
        if ($module == 'all') {
            $this->_export_all_csv($allowed_pos, $id_pos, $period, $date);
        } else {
            $this->_export_module_csv($module, $allowed_pos, $id_pos, $period, $date);
        }
    }

    private function _export_all_csv($allowed_pos, $id_pos, $period, $date) {
        $data = [];
        
        $tel = $this->_get_export_data('telemetri', $allowed_pos, $id_pos, $period, $date);
        if (!empty($tel)) $data['telemetri'] = $tel;
        
        $man = $this->_get_export_data('manual', $allowed_pos, $id_pos, $period, $date);
        if (!empty($man)) $data['manual'] = $man;
        
        $bdg = $this->_get_export_data('bendung', $allowed_pos, $id_pos, $period, $date);
        if (!empty($bdg)) $data['bendung'] = $bdg;
        
        $bendungan = $this->_get_export_data('bendungan', $allowed_pos, $id_pos, $period, $date);
        if (!empty($bendungan)) $data['bendungan'] = $bendungan;
        
        if (empty($data)) {
            $this->session->set_flashdata('warning', 'Tidak ada data untuk diexport.');
            redirect('admin/export_import');
            return;
        }
        
        $pos_name = $id_pos ? $this->_get_pos_name($id_pos) : 'all_pos';
        $filename = 'export_all_' . $pos_name . '_' . date('Y-m-d_H-i') . '.csv';
        
        $this->load->helper('download');
        force_download($filename, $this->_array_to_csv_multitable($data));
    }

    private function _export_module_csv($module, $allowed_pos, $id_pos, $period, $date) {
        $data = $this->_get_export_data($module, $allowed_pos, $id_pos, $period, $date);
        
        if (empty($data)) {
            $this->session->set_flashdata('warning', 'Tidak ada data untuk diexport.');
            redirect('admin/export_import');
            return;
        }
        
        $pos_name = $id_pos ? $this->_get_pos_name($id_pos) : 'all_pos';
        $filename = 'export_' . $module . '_' . $pos_name . '_' . date('Y-m-d_H-i') . '.csv';
        
        $this->load->helper('download');
        force_download($filename, $this->_array_to_csv($data));
    }

    private function _get_export_data($module, $allowed_pos, $id_pos, $period, $date) {
        if (!empty($id_pos)) {
            $this->db->where('id_pos', $id_pos);
        } else if (!empty($allowed_pos)) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        
        if ($period == 'daily') {
            $this->db->where('DATE(tanggal_input)', $date);
        } else if ($period == 'month') {
            $this->db->where("DATE_FORMAT(tanggal_input, '%Y-%m')", substr($date, 0, 7));
        }
        
        switch ($module) {
            case 'telemetri':
                return $this->db->get('data_telemetri')->result_array();
            case 'manual':
                return $this->db->get('data_manual')->result_array();
            case 'bendung':
                return $this->db->get('data_bendung')->result_array();
            case 'bendungan':
                return $this->db->get('data_bendungan')->result_array();
            default:
                return [];
        }
    }

    private function _array_to_csv($data) {
        if (empty($data)) return '';
        
        $output = '';
        $headers = array_keys((array)$data[0]);
        $output .= implode(';', $headers) . "\n";
        
        foreach ($data as $row) {
            $row = (array)$row;
            foreach ($row as &$val) {
                $val = str_replace(';', ',', $val);
                $val = str_replace("\n", ' ', $val);
                $val = str_replace("\r", ' ', $val);
            }
            $output .= implode(';', $row) . "\n";
        }
        
        return $output;
    }

    private function _array_to_csv_multitable($data) {
        if (empty($data)) return '';
        
        $output = '';
        foreach ($data as $table => $rows) {
            $output .= "--- TABLE: " . strtoupper($table) . " ---\n";
            $output .= $this->_array_to_csv($rows);
            $output .= "\n";
        }
        return $output;
    }

    private function _get_pos_name($id_pos) {
        $row = $this->db->select('nama_pos')->where('id_pos', $id_pos)->get('master_pos')->row();
        return $row ? str_replace(' ', '_', $row->nama_pos) : 'unknown';
    }

    public function export_pdf() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        $module = $this->input->get('module') ?: 'all';
        $id_pos = $this->input->get('id_pos');
        $period = $this->input->get('period') ?: 'all';
        $date = $this->input->get('date') ?: date('Y-m-d');
        
        if (!empty($id_pos) && !in_array($id_pos, $allowed_pos)) {
            show_error('Akses Ditolak!', 403);
        }
        
        if ($module == 'all') {
            $data = [];
            $modules_data = ['telemetri', 'manual', 'bendung', 'bendungan'];
            foreach ($modules_data as $m) {
                $d = $this->_get_export_data($m, $allowed_pos, $id_pos, $period, $date);
                if (!empty($d)) $data[$m] = $d;
            }
        } else {
            $data = $this->_get_export_data($module, $allowed_pos, $id_pos, $period, $date);
        }
        
        if (empty($data)) {
            $this->session->set_flashdata('warning', 'Tidak ada data untuk diexport.');
            redirect('admin/export_import');
            return;
        }
        
        require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
        
        $dompdf = new Dompdf\Dompdf();
        $dompdf->set_option('defaultFont', 'Arial');
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('enable_remote', true);
        
        $pos_name = $id_pos ? $this->_get_pos_name($id_pos) : 'Semua_Pos';
        $filename = 'export_' . $module . '_' . $pos_name . '_' . date('Y-m-d') . '.pdf';
        
        $html = $this->_generate_pdf_html($data, $module, $pos_name, $period, $date);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => 1]);
    }

    private function _generate_pdf_html($data, $module, $pos_name, $period, $date) {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Export Data</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 10px; }
                h1 { font-size: 16px; color: #1a2a4a; border-bottom: 2px solid #f5c518; padding-bottom: 5px; }
                h2 { font-size: 13px; color: #1a2a4a; margin-top: 15px; margin-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                th { background-color: #1a2a4a; color: white; padding: 4px 6px; text-align: left; font-size: 9px; }
                td { border: 1px solid #ddd; padding: 3px 6px; font-size: 9px; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .info { font-size: 9px; color: #666; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <h1>📊 Export Data - ' . ucfirst($module) . '</h1>
            <div class="info">
                <strong>Pos:</strong> ' . $pos_name . ' | 
                <strong>Periode:</strong> ' . ucfirst($period) . ' | 
                <strong>Tanggal:</strong> ' . date('d-m-Y', strtotime($date)) . ' | 
                <strong>Diexport:</strong> ' . date('d-m-Y H:i') . '
            </div>';
        
        if ($module == 'all') {
            foreach ($data as $table => $rows) {
                $html .= '<h2>📋 ' . strtoupper($table) . ' (' . count($rows) . ' data)</h2>';
                $html .= $this->_array_to_html_table($rows);
            }
        } else {
            $html .= $this->_array_to_html_table($data);
        }
        
        $html .= '</body></html>';
        return $html;
    }

    private function _array_to_html_table($data) {
        if (empty($data)) return '<p><em>Tidak ada data</em></p>';
        
        $html = '<table>';
        $headers = array_keys((array)$data[0]);
        $html .= '<thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        
        foreach ($data as $row) {
            $row = (array)$row;
            $html .= '<tr>';
            foreach ($headers as $h) {
                $val = isset($row[$h]) ? $row[$h] : '';
                if (is_null($val)) $val = '';
                $html .= '<td>' . htmlspecialchars($val) . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        return $html;
    }

    public function download_template_csv() {
        $module = $this->input->get('module');
        
        $filename = 'template_' . $module . '.csv';
        $headers = $this->_get_template_headers($module);
        
        $this->load->helper('download');
        force_download($filename, implode(';', $headers) . "\n");
    }

    private function _get_template_headers($module) {
        switch ($module) {
            case 'telemetri':
                return ['id_pos', 'received_at', 'bat_lvl', 'bat_volt', 'rain', 'wlevel', 'created_at'];
            case 'manual':
                return ['id_pos', 'id_user', 'tanggal_input', 'rain', 'wlevel', 'keterangan'];
            case 'bendung':
                return ['id_pos', 'id_user', 'tanggal_input', 'rain', 'elevasi_mercu', 'q_total', 'q_fc1', 'q_fc2', 'q_sal_induk', 'q_limpas', 'q_sungai', 'q_spam_kpbu', 'sluice_gate', 'bukaan_pintu', 'keterangan'];
            case 'bendungan':
                return ['id_pos', 'id_user', 'tanggal_input', 'nwl', 'nwl_volume', 'nwl_luas', 'rain', 'elevasi', 'volume', 'luas', 'inflow', 'pltm', 'spillway', 'total_outflow', 'plta_status', 'irigasi_status', 'tail_water', 'rembesan_vnotch_h', 'rembesan_vnotch_q', 'rembesan_pump_pit_l_h', 'rembesan_pump_pit_l_q', 'rembesan_pump_pit_r_h', 'rembesan_pump_pit_r_q', 'keterangan', 'tahun_mulai_pembangunan', 'tipe_bendungan', 'elevasi_mercu', 'luas_das'];
            default:
                return ['id_pos', 'tanggal_input', 'data1', 'data2', 'keterangan'];
        }
    }

    public function import_csv() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        $module = $this->input->post('module');
        $id_pos = $this->input->post('id_pos');
        
        if (!in_array($id_pos, $allowed_pos)) {
            $this->session->set_flashdata('error', 'Akses Ditolak!');
            redirect('admin/export_import');
            return;
        }
        
        if (!isset($_FILES['file_csv']) || $_FILES['file_csv']['error'] != UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'Gagal upload file.');
            redirect('admin/export_import');
            return;
        }
        
        $file = $_FILES['file_csv'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), ['csv', 'txt'])) {
            $this->session->set_flashdata('error', 'Format file harus CSV atau TXT.');
            redirect('admin/export_import');
            return;
        }
        
        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            $this->session->set_flashdata('error', 'Gagal membaca file.');
            redirect('admin/export_import');
            return;
        }
        
        $headers = fgetcsv($handle, 0, ';');
        if ($headers === false) {
            fclose($handle);
            $this->session->set_flashdata('error', 'File CSV kosong atau format tidak valid.');
            redirect('admin/export_import');
            return;
        }
        
        $imported = 0;
        $failed = 0;
        $errors = [];
        
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) != count($headers)) {
                $failed++;
                continue;
            }
            
            $data = array_combine($headers, $row);
            $data['id_pos'] = $id_pos;
            $data['id_user'] = $user_id;
            
            $result = $this->_import_row($module, $data);
            if ($result) {
                $imported++;
            } else {
                $failed++;
                $errors[] = implode(', ', array_slice($data, 0, 3));
            }
        }
        
        fclose($handle);
        
        $message = "Import selesai! Berhasil: $imported data, Gagal: $failed data.";
        if (!empty($errors)) {
            $message .= " Data gagal: " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) $message .= ' ...';
        }
        
        $this->session->set_flashdata('success', $message);
        redirect('admin/export_import?pos=' . $id_pos);
    }

    private function _import_row($module, $data) {
        foreach ($data as &$val) {
            if ($val === '') $val = null;
        }
        
        $data['created_at'] = date('Y-m-d H:i:s');
        
        switch ($module) {
            case 'telemetri':
                return $this->db->insert('data_telemetri', $data);
            case 'manual':
                return $this->db->insert('data_manual', $data);
            case 'bendung':
                return $this->db->insert('data_bendung', $data);
            case 'bendungan':
                return $this->db->insert('data_bendungan', $data);
            default:
                return false;
        }
    }

    // ==========================================
    // CRUD TERPISAH (REDIRECT KE KELOLA_MANUAL)
    // ==========================================
    
    // Irigasi - redirect ke kelola_manual karena sudah terintegrasi
    public function kelola_irigasi() {
        redirect('admin/kelola_manual');
    }
    
    public function hapus_irigasi_crud($id) {
        redirect('admin/kelola_manual');
    }
    
    // Sedimen - redirect ke kelola_manual karena sudah terintegrasi
    public function kelola_sedimen() {
        redirect('admin/kelola_manual');
    }
    
    public function hapus_sedimen_crud($id) {
        redirect('admin/kelola_manual');
    }
    
    // Pantai - redirect ke kelola_manual karena sudah terintegrasi
    public function kelola_pantai() {
        redirect('admin/kelola_manual');
    }
    
    public function hapus_pantai_crud($id) {
        redirect('admin/kelola_manual');
    }
    
    // Embung - redirect ke kelola_manual karena sudah terintegrasi
    public function kelola_embung() {
        redirect('admin/kelola_manual');
    }
    
    public function hapus_embung_crud($id) {
        redirect('admin/kelola_manual');
    }
}