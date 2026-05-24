<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->library('session');
        $this->load->model('M_auth');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
    }

    /**
     * Halaman Login
     */
    public function index() {
        // Jika sudah login, redirect sesuai hak akses lapis 3
        if ($this->session->userdata('logged_in')) {
            $role = strtolower($this->session->userdata('role'));
            if ($role == 'superadmin' || $role == 'admin') {
                redirect('admin');
            } else {
                redirect('petugas');
            }
        }
        
        $data['title'] = 'Login - CASCADE';
        $data['app_name'] = 'CASCADE';
        
        $this->load->view('layout/v_header', $data);
        $this->load->view('auth/v_login', $data);
        $this->load->view('layout/v_footer');
    }

    /**
     * Proses Login
     */
    public function do_login() {
        // Validasi form
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Username dan password harus diisi');
            redirect('auth');
            return;
        }
        
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);
        
        // DEBUGGING - Hapus setelah production
        log_message('debug', 'Login attempt: ' . $username);
        
        // Cek user di database
        $user = $this->M_auth->get_user_by_username($username);
        
        // DEBUGGING
        if (!$user) {
            log_message('debug', 'User tidak ditemukan: ' . $username);
            $this->session->set_flashdata('error', 'Username atau password salah');
            redirect('auth');
            return;
        }
        
        // DEBUGGING
        log_message('debug', 'User ditemukan: ' . $user->username . ' | Status: ' . $user->status);
        
        // Cek status akun
        if ($user->status !== 'aktif') {
            $this->session->set_flashdata('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            redirect('auth');
            return;
        }
        
        // Verifikasi password
        $password_valid = password_verify($password, $user->password);
        
        // DEBUGGING
        log_message('debug', 'Password valid: ' . ($password_valid ? 'YA' : 'TIDAK'));
        
        if (!$password_valid) {
            // Jika password tidak valid, coba cek apakah password masih plain text (untuk migrasi)
            if ($password === $user->password) {
                // Password masih plain text, update ke hash
                $new_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $this->db->where('id_user', $user->id_user)
                         ->update('users', ['password' => $new_hash]);
                
                log_message('debug', 'Password di-upgrade ke bcrypt');
                $password_valid = true;
            } else {
                $this->session->set_flashdata('error', 'Username atau password salah');
                redirect('auth');
                return;
            }
        }
        
        // Memastikan string role dibaca sebagai huruf kecil (lowercase) dan bersih dari spasi
        $fixed_role = strtolower(trim($user->role));
        
        // Login sukses, bangun data session
        $session_data = [
            'user_id'      => $user->id_user,
            'username'     => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'role'         => $fixed_role, // Menyimpan 'super_admin', 'admin', atau 'petugas'
            'id_pos'       => $user->id_pos,
            'logged_in'    => TRUE,
            'last_activity'=> time()
        ];
        
        $this->session->set_userdata($session_data);
        
        // Update last login
        $this->db->where('id_user', $user->id_user)
                 ->update('users', ['last_login' => date('Y-m-d H:i:s')]);
        
        // Redirect dinamis lapis 3 berdasarkan role baru
        if ($fixed_role == 'super_admin' || $fixed_role == 'admin') {
            redirect('admin');
        } else {
            redirect('petugas');
        }
    }

    /**
     * Logout
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }
}