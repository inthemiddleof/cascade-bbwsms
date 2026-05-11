<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_auth extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ============================================
    // AUTH METHODS
    // ============================================

    /**
     * Get user by username
     */
    public function get_user_by_username($username) {
        $query = $this->db->where('username', $username)
                          ->get('users');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

    /**
     * Get user by id
     */
    public function get_user_by_id($user_id) {
        $query = $this->db->where('id_user', $user_id)
                          ->get('users');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

    /**
     * Get user by remember token
     */
    public function get_user_by_token($token) {
        $query = $this->db->where('remember_token', $token)
                          ->where('status', 'aktif')
                          ->get('users');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

    /**
     * Set remember me token
     */
    public function set_remember_token($user_id, $token) {
        return $this->db->where('id_user', $user_id)
                        ->update('users', ['remember_token' => $token]);
    }

    /**
     * Remove remember me token
     */
    public function remove_remember_token($user_id) {
        return $this->db->where('id_user', $user_id)
                        ->update('users', ['remember_token' => NULL]);
    }

    /**
     * Cek apakah IP terblokir (rate limiting)
     */
    public function is_ip_blocked($ip_address) {
        $max_attempts = 5;
        $reset_after = 3600;
        
        $this->db->where('ip_address', $ip_address);
        $this->db->where('status', 'failed');
        $this->db->where('attempt_time >=', date('Y-m-d H:i:s', time() - $reset_after));
        $failed_count = $this->db->count_all_results('login_logs');
        
        return $failed_count >= $max_attempts;
    }

    /**
     * Log aktivitas login
     */
    public function log_login($user_id, $username, $status) {
        $data = [
            'id_user'    => $user_id,
            'username'   => $username,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status'     => $status
        ];
        
        return $this->db->insert('login_logs', $data);
    }

    /**
     * Increment login attempts
     */
    public function increment_login_attempts($username) {
        $user = $this->get_user_by_username($username);
        
        if ($user) {
            $attempts = $user->login_attempts + 1;
            $data = ['login_attempts' => $attempts];
            
            if ($attempts >= 5) {
                $data['locked_until'] = date('Y-m-d H:i:s', time() + 900);
            }
            
            $this->db->where('id_user', $user->id_user)
                     ->update('users', $data);
        }
    }

    /**
     * Reset login attempts
     */
    public function reset_login_attempts($user_id) {
        return $this->db->where('id_user', $user_id)
                        ->update('users', [
                            'login_attempts' => 0,
                            'locked_until'   => NULL
                        ]);
    }

    /**
     * Update last login time
     */
    public function update_last_login($user_id) {
        return $this->db->where('id_user', $user_id)
                        ->update('users', ['last_login' => date('Y-m-d H:i:s')]);
    }

    // ============================================
    // STATISTIK METHODS (UNTUK DASHBOARD ADMIN)
    // ============================================

    /**
     * Hitung total semua pos
     */
    public function count_all_pos() {
        return $this->db->count_all('master_pos');
    }

    /**
     * Hitung pos berdasarkan tipe (PCH/PDA)
     */
    public function count_pos_by_type($tipe) {
        return $this->db->where('tipe_pos', $tipe)
                        ->count_all_results('master_pos');
    }

    /**
     * Hitung total petugas
     */
    public function count_all_petugas() {
        return $this->db->where('role', 'petugas')
                        ->count_all_results('users');
    }

    /**
     * Hitung petugas berdasarkan status (aktif/nonaktif)
     */
    public function count_petugas_by_status($status) {
        return $this->db->where('role', 'petugas')
                        ->where('status', $status)
                        ->count_all_results('users');
    }

    /**
     * Hitung data telemetri hari ini
     */
    public function count_telemetri_today() {
        $today = date('Y-m-d');
        $this->db->where('DATE(received_at)', $today);
        return $this->db->count_all_results('data_telemetri');
    }

    /**
     * Hitung pos yang online (mengirim data dalam 1 jam terakhir)
     */
    public function count_pos_online() {
        $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        $this->db->distinct();
        $this->db->select('id_pos');
        $this->db->where('received_at >=', $one_hour_ago);
        $query = $this->db->get('data_telemetri');
        
        return $query->num_rows();
    }

    /**
     * Get waktu sinkronisasi terakhir
     */
    public function get_last_sync_time() {
        $query = $this->db->select('MAX(created_at) as last_sync')
                          ->get('data_telemetri');
        
        if ($query->num_rows() > 0) {
            return $query->row()->last_sync;
        }
        
        return null;
    }

    /**
     * Get ringkasan pos dengan data terbaru
     */
    public function get_pos_summary() {
        $this->db->select('m.nama_pos, m.tipe_pos, m.sungai, 
                          COUNT(d.id) as total_data,
                          MAX(d.received_at) as last_data');
        $this->db->from('master_pos m');
        $this->db->join('data_telemetri d', 'm.id_pos = d.id_pos', 'left');
        $this->db->group_by('m.id_pos');
        $this->db->order_by('last_data', 'DESC');
        $this->db->limit(10);
        
        return $this->db->get()->result();
    }

    // ============================================
    // MANAJEMEN PETUGAS
    // ============================================

    /**
     * Get semua petugas dengan info pos
     */
    public function get_all_petugas() {
        $this->db->select('users.*, master_pos.nama_pos, master_pos.tipe_pos, master_pos.nomor_pos');
        $this->db->from('users');
        $this->db->join('master_pos', 'users.id_pos = master_pos.id_pos', 'left');
        $this->db->where('users.role', 'petugas');
        $this->db->order_by('users.created_at', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Get semua pos untuk dropdown
     */
    public function get_all_pos_for_select() {
        $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos');
        $this->db->from('master_pos');
        $this->db->order_by('nama_pos', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Create petugas baru
     */
    public function create_petugas($data) {
        return $this->db->insert('users', $data);
    }

    /**
     * Update status petugas (aktif/nonaktif)
     */
    public function update_status_petugas($id_user, $status) {
        return $this->db->where('id_user', $id_user)
                        ->where('role', 'petugas')
                        ->update('users', ['status' => $status]);
    }

    // ============================================
    // MANAJEMEN POS
    // ============================================

    /**
     * Get detail pos dengan petugas penanggung jawab
     */
    public function get_detailed_pos_list() {
        $this->db->select('master_pos.*, users.nama_lengkap as petugas_nama, users.username as petugas_username');
        $this->db->from('master_pos');
        $this->db->join('users', 'master_pos.id_pos = users.id_pos AND users.role = "petugas" AND users.status = "aktif"', 'left');
        $this->db->order_by('master_pos.nama_pos', 'ASC');
        
        return $this->db->get()->result();
    }

    // ============================================
    // LOGIN LOGS
    // ============================================

    /**
     * Get aktivitas login terbaru
     */
    public function get_recent_activities($limit = 5) {
        $this->db->select('login_logs.*, users.nama_lengkap, users.role');
        $this->db->from('login_logs');
        $this->db->join('users', 'login_logs.id_user = users.id_user', 'left');
        $this->db->order_by('login_logs.attempt_time', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }
}