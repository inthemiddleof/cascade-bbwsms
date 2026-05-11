<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
        $this->load->model('M_auth');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Jakarta');
        
        if (!$this->session->userdata('logged_in')) redirect('auth');
        if ($this->session->userdata('role') !== 'admin') show_error('Akses Ditolak', 403);
    }

    // ============================================
    // DASHBOARD
    // ============================================
    public function index() {
        $data = [
            'app_name'  => 'CASCADE', 
            'title'     => 'Dashboard', 
            'admin_name'=> $this->session->userdata('nama_lengkap'),
            
            // Statistik
            'total_pos'       => $this->M_auth->count_all_pos(),
            'total_pch'       => $this->M_auth->count_pos_by_type('PCH'),
            'total_pda'       => $this->M_auth->count_pos_by_type('PDA'),
            'total_petugas'   => $this->M_auth->count_all_petugas(),
            'petugas_aktif'   => $this->M_auth->count_petugas_by_status('aktif'),
            'total_data_hari_ini' => $this->M_auth->count_telemetri_today(),
            'pos_online'      => $this->M_auth->count_pos_online(),
            'last_sync'       => $this->M_auth->get_last_sync_time(),
            
            // Data untuk ringkasan pos & distribusi
            'pos_list'        => $this->M_auth->get_detailed_pos_list(),
        ];
        $data['content'] = $this->load->view('admin/v_dashboard', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }

    // ============================================
    // KELOLA PETUGAS (CRUD)
    // ============================================
    
    public function kelola_petugas() {
        $data = [
            'app_name'  => 'CASCADE', 'title' => 'Kelola Petugas', 'admin_name'=> $this->session->userdata('nama_lengkap'),
            'petugas_list' => $this->M_auth->get_all_petugas(), 'pos_list' => $this->M_auth->get_all_pos_for_select(),
        ];
        $data['content'] = $this->load->view('admin/v_kelola_petugas', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }

    public function tambah_petugas() {
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('id_pos', 'Pos', 'required|integer');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data_user = [
                'username'     => $this->input->post('username', TRUE),
                'password'     => password_hash($this->input->post('password'), PASSWORD_BCRYPT, ['cost' => 12]),
                'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
                'email'        => $this->input->post('email', TRUE),
                'role'         => 'petugas',
                'id_pos'       => $this->input->post('id_pos', TRUE),
                'status'       => 'aktif'
            ];
            $this->M_auth->create_petugas($data_user) 
                ? $this->session->set_flashdata('success', 'Petugas berhasil ditambahkan!')
                : $this->session->set_flashdata('error', 'Gagal menambahkan petugas.');
        }
        redirect('admin/kelola_petugas');
    }

    public function edit_petugas() {
        $id_user = $this->input->post('id_user', TRUE);
        $user = $this->M_auth->get_user_by_id($id_user);
        
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('id_pos', 'Pos', 'required|integer');
        
        if ($user && $this->input->post('username') != $user->username) {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data_update = [
                'username'     => $this->input->post('username', TRUE),
                'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
                'email'        => $this->input->post('email', TRUE),
                'id_pos'       => $this->input->post('id_pos', TRUE),
            ];
            
            $password = $this->input->post('password');
            if (!empty($password)) {
                $data_update['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            }
            
            $this->M_auth->update_petugas($id_user, $data_update)
                ? $this->session->set_flashdata('success', 'Data petugas berhasil diperbarui!')
                : $this->session->set_flashdata('error', 'Gagal memperbarui data petugas.');
        }
        redirect('admin/kelola_petugas');
    }

    public function hapus_petugas($id_user) {
        $user = $this->M_auth->get_user_by_id($id_user);
        
        if (!$user) {
            $this->session->set_flashdata('error', 'Petugas tidak ditemukan.');
        } elseif ($user->role == 'admin') {
            $this->session->set_flashdata('error', 'Tidak dapat menghapus akun admin.');
        } else {
            $this->M_auth->delete_petugas($id_user)
                ? $this->session->set_flashdata('success', 'Petugas berhasil dihapus!')
                : $this->session->set_flashdata('error', 'Gagal menghapus petugas.');
        }
        redirect('admin/kelola_petugas');
    }

    public function nonaktifkan_petugas($id_user) {
        $this->M_auth->update_status_petugas($id_user, 'nonaktif')
            ? $this->session->set_flashdata('success', 'Petugas berhasil dinonaktifkan.')
            : $this->session->set_flashdata('error', 'Gagal menonaktifkan petugas.');
        redirect('admin/kelola_petugas');
    }

    public function aktifkan_petugas($id_user) {
        $this->M_auth->update_status_petugas($id_user, 'aktif')
            ? $this->session->set_flashdata('success', 'Petugas berhasil diaktifkan.')
            : $this->session->set_flashdata('error', 'Gagal mengaktifkan petugas.');
        redirect('admin/kelola_petugas');
    }

    // ============================================
    // KELOLA POS (CRUD LENGKAP)
    // ============================================
    
    /**
     * READ - Tampilkan semua pos
     */
    public function kelola_pos() {
        $data = [
            'app_name'  => 'CASCADE', 'title' => 'Kelola Pos', 'admin_name'=> $this->session->userdata('nama_lengkap'),
            'pos_list'  => $this->M_auth->get_detailed_pos_list(),
        ];
        $data['content'] = $this->load->view('admin/v_kelola_pos', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }

    /**
     * CREATE - Tambah pos baru
     */
    public function tambah_pos() {
        $this->form_validation->set_rules('nomor_pos', 'Nomor Pos', 'required');
        $this->form_validation->set_rules('nama_pos', 'Nama Pos', 'required');
        $this->form_validation->set_rules('tipe_pos', 'Tipe Pos', 'required|in_list[PCH,PDA]');
        $this->form_validation->set_rules('lat', 'Latitude', 'required|numeric');
        $this->form_validation->set_rules('lng', 'Longitude', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data_pos = [
                'nomor_pos'            => $this->input->post('nomor_pos', TRUE),
                'nama_pos'             => $this->input->post('nama_pos', TRUE),
                'tipe_pos'             => $this->input->post('tipe_pos', TRUE),
                'sungai'               => $this->input->post('sungai', TRUE),
                'lat'                  => $this->input->post('lat', TRUE),
                'lng'                  => $this->input->post('lng', TRUE),
                'device_id_telemetry'  => $this->input->post('device_id_telemetry', TRUE),
            ];
            
            $this->db->insert('master_pos', $data_pos)
                ? $this->session->set_flashdata('success', 'Pos berhasil ditambahkan!')
                : $this->session->set_flashdata('error', 'Gagal menambahkan pos.');
        }
        redirect('admin/kelola_pos');
    }

    /**
     * UPDATE - Edit pos
     */
    public function edit_pos() {
        $id_pos = $this->input->post('id_pos', TRUE);
        
        $this->form_validation->set_rules('nomor_pos', 'Nomor Pos', 'required');
        $this->form_validation->set_rules('nama_pos', 'Nama Pos', 'required');
        $this->form_validation->set_rules('tipe_pos', 'Tipe Pos', 'required|in_list[PCH,PDA]');
        $this->form_validation->set_rules('lat', 'Latitude', 'required|numeric');
        $this->form_validation->set_rules('lng', 'Longitude', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data_update = [
                'nomor_pos'            => $this->input->post('nomor_pos', TRUE),
                'nama_pos'             => $this->input->post('nama_pos', TRUE),
                'tipe_pos'             => $this->input->post('tipe_pos', TRUE),
                'sungai'               => $this->input->post('sungai', TRUE),
                'lat'                  => $this->input->post('lat', TRUE),
                'lng'                  => $this->input->post('lng', TRUE),
                'device_id_telemetry'  => $this->input->post('device_id_telemetry', TRUE),
            ];
            
            $this->db->where('id_pos', $id_pos)->update('master_pos', $data_update)
                ? $this->session->set_flashdata('success', 'Data pos berhasil diperbarui!')
                : $this->session->set_flashdata('error', 'Gagal memperbarui data pos.');
        }
        redirect('admin/kelola_pos');
    }

    /**
     * DELETE - Hapus pos
     */
    public function hapus_pos($id_pos) {
        // Cek apakah pos memiliki data telemetri
        $has_data = $this->db->where('id_pos', $id_pos)->count_all_results('data_telemetri') > 0;
        
        if ($has_data) {
            $this->session->set_flashdata('error', 'Pos tidak dapat dihapus karena masih memiliki data telemetri. Hapus data telemetri terlebih dahulu atau nonaktifkan pos.');
        } else {
            // Lepas petugas dari pos ini
            $this->db->where('id_pos', $id_pos)->update('users', ['id_pos' => NULL]);
            
            // Hapus pos
            $this->db->where('id_pos', $id_pos)->delete('master_pos')
                ? $this->session->set_flashdata('success', 'Pos berhasil dihapus!')
                : $this->session->set_flashdata('error', 'Gagal menghapus pos.');
        }
        redirect('admin/kelola_pos');
    }
}