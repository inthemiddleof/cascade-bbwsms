<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_auth extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_user_by_username($username) {
        return $this->db->where('username', $username)->get('users')->row();
    }

    public function get_user_by_id($user_id) {
        return $this->db->where('id_user', $user_id)->get('users')->row();
    }

    public function set_remember_token($user_id, $token) {
        return $this->db->where('id_user', $user_id)->update('users', ['remember_token' => $token]);
    }

    public function remove_remember_token($user_id) {
        return $this->db->where('id_user', $user_id)->update('users', ['remember_token' => NULL]);
    }

    public function is_ip_blocked($ip_address) {
        $max_attempts = 5;
        $reset_after = 3600;
        
        $this->db->where('ip_address', $ip_address);
        $this->db->where('status', 'failed');
        $this->db->where('attempt_time >=', date('Y-m-d H:i:s', time() - $reset_after));
        
        return $this->db->count_all_results('login_logs') >= $max_attempts;
    }

    public function log_login($user_id, $username, $status) {
        return $this->db->insert('login_logs', [
            'id_user'    => $user_id,
            'username'   => $username,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status'     => $status
        ]);
    }

    public function increment_login_attempts($username) {
        $user = $this->get_user_by_username($username);
        
        if ($user) {
            $attempts = $user->login_attempts + 1;
            $data = ['login_attempts' => $attempts];
            
            if ($attempts >= 5) {
                $data['locked_until'] = date('Y-m-d H:i:s', time() + 900);
            }
            
            $this->db->where('id_user', $user->id_user)->update('users', $data);
        }
    }

    public function reset_login_attempts($user_id) {
        return $this->db->where('id_user', $user_id)->update('users', [
            'login_attempts' => 0,
            'locked_until'   => NULL
        ]);
    }

    public function update_last_login($user_id) {
        return $this->db->where('id_user', $user_id)->update('users', [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_all_petugas() {
        return $this->db->select('users.*')
                        ->from('users')
                        ->where('users.role', 'petugas')
                        ->order_by('users.created_at', 'DESC')
                        ->get()->result();
    }
}