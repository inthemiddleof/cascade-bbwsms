<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_petugas extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ============================================
    // STATISTIK
    // ============================================
    public function count_hari_ini($id_pos) {
        return $this->db->where('id_pos', $id_pos)->where('tanggal_input', date('Y-m-d'))->count_all_results('data_manual');
    }

    public function count_bulan_ini($id_pos) {
        return $this->db->where('id_pos', $id_pos)->where('MONTH(tanggal_input)', date('m'))->where('YEAR(tanggal_input)', date('Y'))->count_all_results('data_manual');
    }

    public function count_semua($id_pos) {
        return $this->db->where('id_pos', $id_pos)->count_all_results('data_manual');
    }

    public function get_riwayat_hari_ini($id_pos, $limit = 5) {
        return $this->db->where('id_pos', $id_pos)->where('tanggal_input', date('Y-m-d'))->order_by('created_at', 'DESC')->limit($limit)->get('data_manual')->result();
    }

    // ============================================
    // DATA MANUAL (CRUD) - PCH/PDA
    // ============================================
    public function get_by_tanggal($id_pos, $tanggal) {
        return $this->db->where('id_pos', $id_pos)->where('tanggal_input', $tanggal)->order_by('created_at', 'ASC')->get('data_manual')->result();
    }

    public function get_by_bulan($id_pos, $bulan) {
        return $this->db->where('id_pos', $id_pos)->where("DATE_FORMAT(tanggal_input, '%Y-%m') = '$bulan'")->order_by('tanggal_input', 'DESC')->order_by('created_at', 'DESC')->get('data_manual')->result();
    }

    public function get_by_id($id_manual, $id_pos) {
        return $this->db->where('id_manual', $id_manual)->where('id_pos', $id_pos)->get('data_manual')->row();
    }

    public function insert($data) {
        return $this->db->insert('data_manual', $data);
    }

    public function update_manual($id_manual, $data) {
        return $this->db->where('id_manual', $id_manual)->update('data_manual', $data);
    }

    public function delete_manual($id_manual, $id_pos) {
        return $this->db->where('id_manual', $id_manual)->where('id_pos', $id_pos)->delete('data_manual');
    }

    // ============================================
    // DATA BENDUNGAN (CRUD)
    // ============================================
    public function insert_bendungan($data) {
        return $this->db->insert('data_bendungan', $data);
    }

    public function get_bendungan_by_bulan($id_pos, $bulan) {
        return $this->db->where('id_pos', $id_pos)->where("DATE_FORMAT(tanggal_input, '%Y-%m') = '$bulan'")->order_by('tanggal_input', 'DESC')->order_by('created_at', 'DESC')->get('data_bendungan')->result();
    }

    public function get_bendungan_by_id($id_bendungan, $id_pos) {
        return $this->db->where('id_bendungan', $id_bendungan)->where('id_pos', $id_pos)->get('data_bendungan')->row();
    }

    public function update_bendungan($id_bendungan, $data) {
        return $this->db->where('id_bendungan', $id_bendungan)->update('data_bendungan', $data);
    }

    public function delete_bendungan($id_bendungan, $id_pos) {
        return $this->db->where('id_bendungan', $id_bendungan)->where('id_pos', $id_pos)->delete('data_bendungan');
    }

    // ============================================
    // MASTER POS
    // ============================================
    public function get_pos($id_pos) {
        return $this->db->where('id_pos', $id_pos)->get('master_pos')->row();
    }
}