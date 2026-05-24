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
     * REVISI: Get semua petugas dengan info MULTI-POS (Tidak pakai JOIN langsung karena id_pos berupa TEXT)
     */
    public function get_all_petugas() {
        // 1. Ambil data users yang rolenya 'petugas' secara spesifik
        $this->db->where('role', 'petugas');
        $this->db->order_by('created_at', 'DESC');
        $users = $this->db->get('users')->result();

        // 2. Loop setiap petugas untuk mencari nama pos-pos miliknya
        foreach ($users as $u) {
            if (!empty($u->id_pos)) {
                // Pecah string "1,2,3" menjadi array [1, 2, 3]
                $id_pos_arr = array_map('trim', explode(',', $u->id_pos));
                
                // Ambil data dari master_pos berdasarkan array ID tersebut
                $this->db->where_in('id_pos', $id_pos_arr);
                $pos_query = $this->db->get('master_pos')->result();

                $nama_pos_arr = [];
                $tipe_pos_arr = [];
                $nomor_pos_arr = [];

                foreach ($pos_query as $pq) {
                    $nama_pos_arr[]  = $pq->nama_pos;
                    $tipe_pos_arr[]  = $pq->tipe_pos;
                    $nomor_pos_arr[] = $pq->nomor_pos;
                }

                // Inject properti dinamis agar nama pos berjejer rapi saat dipanggil di View
                $u->nama_pos  = implode(', ', $nama_pos_arr);  // Hasil: "Pos A, Pos B"
                $u->tipe_pos  = implode(', ', $tipe_pos_arr);
                $u->nomor_pos = implode(', ', $nomor_pos_arr);
            } else {
                $u->nama_pos  = 'Belum ada wilayah tugas';
                $u->tipe_pos  = '-';
                $u->nomor_pos = '-';
            }
        }
        return $users;
    }

    public function get_all_admin() {
        // 1. Ambil data users yang rolenya 'admin' (bukan superadmin / petugas)
        $this->db->where('role', 'admin');
        $this->db->order_by('created_at', 'DESC');
        $admins = $this->db->get('users')->result();

        // 2. Loop setiap admin untuk memetakan multi-pos cakupan wilayah kerjanya
        foreach ($admins as $a) {
            if (!empty($a->id_pos)) {
                $id_pos_arr = array_map('trim', explode(',', $a->id_pos));
                
                $this->db->where_in('id_pos', $id_pos_arr);
                $pos_query = $this->db->get('master_pos')->result();

                $nama_pos_arr = [];
                $tipe_pos_arr = [];

                foreach ($pos_query as $pq) {
                    $nama_pos_arr[] = $pq->nama_pos;
                    $tipe_pos_arr[] = $pq->tipe_pos;
                }

                $a->nama_pos = implode(', ', $nama_pos_arr); 
                $a->tipe_pos = implode(', ', $tipe_pos_arr);
            } else {
                $a->nama_pos = 'Seluruh Wilayah (Akses Penuh)';
                $a->tipe_pos = '-';
            }
        }
        return $admins;
    }

    /**
     * Get semua pos untuk dropdown / list checkbox
     */
    public function get_all_pos_for_select() {
        return $this->select('id_pos, nama_pos, tipe_pos, nomor_pos')
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
     * REVISI: Get detail pos dengan petugas (Kompatibel dengan pencarian string ID di tabel users)
     */
    public function get_detailed_pos_list() {
        // Ambil semua data pos terlebih dahulu
        $this->db->order_by('nama_pos', 'ASC');
        $pos_list = $this->db->get('master_pos')->result();

        // Ambil semua petugas yang memiliki id_pos
        $this->db->where('role', 'petugas');
        $this->db->where('id_pos IS NOT NULL');
        $users = $this->db->get('users')->result();

        // Pasangkan petugas ke pos masing-masing secara dinamis
        foreach ($pos_list as $pos) {
            $petugas_nama_arr = [];
            $petugas_user_arr = [];

            foreach ($users as $u) {
                $user_pos_ids = array_map('trim', explode(',', $u->id_pos));
                if (in_array($pos->id_pos, $user_pos_ids)) {
                    $petugas_nama_arr[] = $u->nama_lengkap;
                    $petugas_user_arr[] = $u->username;
                }
            }

            // Jika ada petugas yang mengelola pos ini, satukan namanya (antisipasi 1 pos dikelola > 1 orang)
            $pos->petugas_nama     = !empty($petugas_nama_arr) ? implode(', ', $petugas_nama_arr) : null;
            $pos->petugas_username = !empty($petugas_user_arr) ? implode(', ', $petugas_user_arr) : null;
        }

        return $pos_list;
    }

    // =========================================================================
    // FITUR TAMBAHAN: UNTUK MENYARING DROP DOWN POS DI LEVEL ADMIN WILAYAH
    // =========================================================================

    /**
     * Alias fungsi untuk Superadmin agar sinkron dengan baris Controller
     */
    public function get_all_pos() {
        return $this->get_all_pos_for_select();
    }

    /**
     * REVISI: Mengambil banyak data POS sekaligus yang ditangani oleh Admin tertentu (Menggunakan WHERE IN)
     */
    public function get_pos_by_admin($id_user) {
        // 1. Ambil nilai id_pos dari tabel users berdasarkan admin yang login
        $user = $this->db->select('id_pos')
                         ->where('id_user', $id_user)
                         ->get('users')
                         ->row();
        
        // 2. Cek apakah user ditemukan dan kolom id_pos ada isinya
        if (!empty($user) && !empty($user->id_pos)) {
            // Pecah string "1,2,3" menjadi array [1, 2, 3]
            $id_pos_arr = array_map('trim', explode(',', $user->id_pos));

            // 3. Cari data pos ke master_pos menggunakan where_in
            $this->db->where_in('id_pos', $id_pos_arr); 
            $this->db->order_by('nama_pos', 'ASC');
            return $this->db->get('master_pos')->result();
        }
        
        return array(); 
    }

    // =========================================================================
    // BARU: AMBIL DATA INPUT MANUAL PETUGAS (DENGAN FILTER LEVEL ADMIN/SUPERADMIN)
    // =========================================================================

    /**
     * Mengambil data manual (Form Biasa) terfilter wilayah kerja Admin
     */
    public function get_data_manual_terfilter($allowed_pos = null) {
        $this->db->select('data_manual.*, master_pos.nama_pos, master_pos.tipe_pos, users.nama_lengkap as nama_petugas');
        $this->db->from('data_manual');
        $this->db->join('master_pos', 'master_pos.id_pos = data_manual.id_pos');
        $this->db->join('users', 'users.id_user = data_manual.id_user', 'left'); // Menggunakan id_user sesuai Controller Petugas
        
        // Saring data jika user login adalah Admin Wilayah (Bukan Superadmin)
        if ($allowed_pos !== null) {
            if (!empty($allowed_pos)) {
                $this->db->where_in('data_manual.id_pos', $allowed_pos);
            } else {
                return array(); // Jika admin tidak punya pos, gagalkan return data
            }
        }
        
        $this->db->order_by('data_manual.tanggal_input', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Mengambil data bendungan terfilter wilayah kerja Admin
     */
    public function get_data_bendungan_terfilter($allowed_pos = null) {
        // Asumsi nama tabel operasional bendungan adalah data_bendungan
        $this->db->select('data_bendungan.*, master_pos.nama_pos, users.nama_lengkap as nama_petugas');
        $this->db->from('data_bendungan');
        $this->db->join('master_pos', 'master_pos.id_pos = data_bendungan.id_pos');
        $this->db->join('users', 'users.id_user = data_bendungan.id_user', 'left');
        
        if ($allowed_pos !== null) {
            if (!empty($allowed_pos)) {
                $this->db->where_in('data_bendungan.id_pos', $allowed_pos);
            } else {
                return array();
            }
        }
        
        $this->db->order_by('data_bendungan.tanggal_input', 'DESC');
        return $this->db->get()->result();
    }
}