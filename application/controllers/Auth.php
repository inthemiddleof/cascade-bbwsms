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

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            if ($role === 'superadmin' || $role === 'admin') {
                redirect('admin');
            } else {
                redirect('petugas');
            }
        }
        
        $data['title'] = 'Login';
        $data['app_name'] = 'HydroSmart';
        
        $this->load->view('layout/v_header', $data);
        $this->load->view('auth/v_auth', $data);
        $this->load->view('layout/v_footer', $data);
    }

    public function do_login() {
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Username dan password harus diisi.');
            redirect('auth');
        }
        
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);
        
        if ($this->M_auth->is_ip_blocked($this->input->ip_address())) {
            $this->M_auth->log_login(null, $username, 'blocked');
            $this->session->set_flashdata('error', 'Terlalu banyak percobaan. Silakan coba lagi nanti.');
            redirect('auth');
        }
        
        $user = $this->M_auth->get_user_by_username($username);
        
        if (!$user) {
            $this->M_auth->log_login(null, $username, 'failed');
            $this->M_auth->increment_login_attempts($username);
            $this->session->set_flashdata('error', 'Username atau password salah.');
            redirect('auth');
        }
        
        if ($user->locked_until && strtotime($user->locked_until) > time()) {
            $this->M_auth->log_login($user->id_user, $username, 'blocked');
            $this->session->set_flashdata('error', 'Akun terkunci. Silakan coba lagi setelah ' . date('H:i', strtotime($user->locked_until)));
            redirect('auth');
        }
        
        if ($user->status !== 'aktif') {
            $this->session->set_flashdata('error', 'Akun tidak aktif. Hubungi administrator.');
            redirect('auth');
        }
        
        if (!password_verify($password, $user->password)) {
            $this->M_auth->log_login($user->id_user, $username, 'failed');
            $this->M_auth->increment_login_attempts($username);
            $this->session->set_flashdata('error', 'Username atau password salah.');
            redirect('auth');
        }
        
        $this->M_auth->reset_login_attempts($user->id_user);
        $this->M_auth->update_last_login($user->id_user);
        $this->M_auth->log_login($user->id_user, $username, 'success');
        
        $this->session->set_userdata([
            'user_id'       => $user->id_user,
            'username'      => $user->username,
            'nama_lengkap'  => $user->nama_lengkap,
            'role'          => $user->role,
            'id_pos'        => $user->id_pos,
            'logged_in'     => TRUE,
            'last_activity' => time()
        ]);
        
        if ($user->role === 'superadmin') {
            redirect('superadmin');
        } elseif ($user->role === 'admin') {
            redirect('admin');
        } else {
            redirect('petugas');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }
}