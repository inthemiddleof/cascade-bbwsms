<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_petugas extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // MASTER POS
    // ==========================================
    public function get_pos($id_pos) {
        return $this->db->where('id_pos', $id_pos)->get('master_pos')->row();
    }

    public function get_pos_by_ids($ids) {
        if (empty($ids)) return [];
        $this->db->where_in('id_pos', $ids);
        $this->db->order_by('nama_pos', 'ASC');
        return $this->db->get('master_pos')->result();
    }

    // ==========================================
    // DATA MANUAL (POS BIASA - PCH/PDA)
    // ==========================================

    /**
     * Ambil data berdasarkan tanggal (untuk form input)
     */
    public function get_by_tanggal($id_pos, $tanggal) {
        return $this->db->where('id_pos', $id_pos)
                        ->where('tanggal_input', $tanggal)
                        ->order_by('created_at', 'ASC')
                        ->get('data_manual')->result();
    }

    /**
     * Ambil data berdasarkan tanggal + JOIN users (untuk riwayat)
     */
    public function get_by_tanggal_with_user($id_pos, $tanggal) {
        $this->db->select('
            m.id_manual,
            m.id_pos,
            m.id_user,
            m.tanggal_input,
            m.rain,
            m.wlevel,
            m.keterangan,
            m.created_at,
            u.nama_lengkap as nama_petugas
        ');
        $this->db->from('data_manual m');
        $this->db->join('users u', 'm.id_user = u.id_user', 'left');
        $this->db->where('m.id_pos', $id_pos);
        
        if (!empty($tanggal)) {
            $this->db->where('m.tanggal_input', $tanggal);
        }
        
        $this->db->order_by('m.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Insert data manual pos biasa
     */
    public function insert($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('data_manual', $data);
    }

    // ==========================================
    // DATA BENDUNGAN (DENGAN KOLOM BARU)
    // ==========================================

    /**
     * Ambil data bendungan berdasarkan tanggal + JOIN users (untuk riwayat)
     */
    public function get_bendungan_by_tanggal($id_pos, $tanggal) {
        $this->db->select('
            d.id_bendungan,
            d.id_pos,
            d.id_user,
            d.tanggal_input,
            d.nwl,
            d.nwl_volume,
            d.nwl_luas,
            d.rain,
            d.elevasi,
            d.volume,
            d.luas,
            d.inflow,
            d.pltm,
            d.spillway,
            d.total_outflow,
            d.plta_status,
            d.irigasi_status,
            d.tail_water,
            d.rembesan_vnotch_h,
            d.rembesan_vnotch_q,
            d.rembesan_pump_pit_l_h,
            d.rembesan_pump_pit_l_q,
            d.rembesan_pump_pit_r_h,
            d.rembesan_pump_pit_r_q,
            d.keterangan,
            d.created_at,
            d.tahun_mulai_pembangunan,
            d.tipe_bendungan,
            d.elevasi_mercu,
            d.luas_das,
            u.nama_lengkap as nama_user
        ');
        $this->db->from('data_bendungan d');
        $this->db->join('users u', 'd.id_user = u.id_user', 'left');
        $this->db->where('d.id_pos', $id_pos);
        
        if (!empty($tanggal)) {
            $this->db->where('d.tanggal_input', $tanggal);
        }
        
        $this->db->order_by('d.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Insert data bendungan (DENGAN KOLOM BARU)
     */
    public function insert_bendungan($data) {
        // Pastikan kolom baru ada di $data
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('data_bendungan', $data);
    }

    // ==========================================
    // DATA BENDUNG (SESUAI STRUKTUR TERBARU)
    // ==========================================

    /**
     * Ambil data bendung berdasarkan tanggal + JOIN users (untuk riwayat)
     */
    public function get_bendung_by_tanggal($id_pos, $tanggal) {
        $this->db->select('
            d.id_bendung,
            d.id_pos,
            d.id_user,
            d.tanggal_input,
            d.rain,
            d.elevasi_mercu,
            d.q_total,
            d.q_fc1,
            d.q_fc2,
            d.q_sal_induk,
            d.q_limpas,
            d.q_sungai,
            d.q_spam_kpbu,
            d.sluice_gate,
            d.bukaan_pintu,
            d.keterangan,
            d.created_at,
            u.nama_lengkap as nama_user
        ');
        $this->db->from('data_bendung d');
        $this->db->join('users u', 'd.id_user = u.id_user', 'left');
        $this->db->where('d.id_pos', $id_pos);
        
        if (!empty($tanggal)) {
            $this->db->where('d.tanggal_input', $tanggal);
        }
        
        $this->db->order_by('d.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Insert data bendung (SESUAI STRUKTUR TERBARU)
     */
    public function insert_bendung($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('data_bendung', $data);
    }
}