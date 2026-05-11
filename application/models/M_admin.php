<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_admin extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Hitung total semua pos
     */
    public function count_all_pos() {
        return $this->db->count_all('master_pos');
    }

    /**
     * Hitung pos berdasarkan tipe
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
     * Hitung petugas berdasarkan status
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
        return $this->db->where('DATE(received_at)', $today)
                        ->count_all_results('data_telemetri');
    }

    /**
     * Hitung pos yang online (ada data dalam 1 jam terakhir)
     */
    public function count_pos_online() {
        $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        $this->db->distinct();
        $this->db->select('id_pos');
        $this->db->where('received_at >=', $one_hour_ago);
        return $this->db->count_all_results('data_telemetri');
    }

    /**
     * Get waktu sinkronisasi terakhir
     */
    public function get_last_sync_time() {
        return $this->db->select('MAX(created_at) as last_sync')
                        ->get('data_telemetri')
                        ->row('last_sync');
    }

    /**
     * Get aktivitas terbaru (login log)
     */
    public function get_recent_activities($limit = 5) {
        return $this->db->select('login_logs.*, users.nama_lengkap, users.role')
                        ->from('login_logs')
                        ->join('users', 'login_logs.id_user = users.id_user', 'left')
                        ->order_by('attempt_time', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->result();
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

    /**
     * Get semua petugas dengan info pos
     */
    public function get_all_petugas() {
        return $this->db->select('users.*, master_pos.nama_pos, master_pos.tipe_pos, master_pos.nomor_pos')
                        ->from('users')
                        ->join('master_pos', 'users.id_pos = master_pos.id_pos', 'left')
                        ->where('users.role', 'petugas')
                        ->order_by('users.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    /**
     * Get semua pos untuk dropdown
     */
    public function get_all_pos_for_select() {
        return $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos')
                        ->from('master_pos')
                        ->order_by('nama_pos', 'ASC')
                        ->get()
                        ->result();
    }

    /**
     * Create petugas baru
     */
    public function create_petugas($data) {
        return $this->db->insert('users', $data);
    }

    /**
     * Update status petugas
     */
    public function update_status_petugas($id_user, $status) {
        return $this->db->where('id_user', $id_user)
                        ->where('role', 'petugas')
                        ->update('users', ['status' => $status]);
    }

    /**
     * Get detail pos dengan petugas
     */
    public function get_detailed_pos_list() {
        return $this->db->select('master_pos.*, users.nama_lengkap as petugas_nama, users.username as petugas_username')
                        ->from('master_pos')
                        ->join('users', 'master_pos.id_pos = users.id_pos AND users.role = "petugas"', 'left')
                        ->order_by('master_pos.nama_pos', 'ASC')
                        ->get()
                        ->result();
    }
}