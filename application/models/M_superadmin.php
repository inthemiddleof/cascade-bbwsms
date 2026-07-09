<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_superadmin extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('M_admin'); // Load M_admin untuk akses data bendung
    }

    private function _parse_float($val) {
        return ($val === '' || $val === null) ? null : (float)$val;
    }

    private function _success($msg) { 
        return ['status' => 'success', 'message' => $msg]; 
    }
    
    private function _error($msg) { 
        return ['status' => 'error', 'message' => $msg]; 
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function get_dashboard_data() {
        return [
            'app_name'            => 'HydroSmart', 
            'title'               => 'Dashboard',
            'total_pos'           => $this->db->count_all_results('master_pos'),
            'total_pch'           => $this->db->where('tipe_pos', 'PCH')->count_all_results('master_pos'),
            'total_pda'           => $this->db->where('tipe_pos', 'PDA')->count_all_results('master_pos'),
            'total_petugas'       => $this->db->where('role', 'petugas')->count_all_results('users'),
            'petugas_aktif'       => $this->db->where('role', 'petugas')->where('status', 'aktif')->count_all_results('users'),
            'total_data_hari_ini' => $this->_count_today_data(),
            'pos_online'          => $this->_count_online_pos(),
            'last_sync'           => $this->_get_last_sync(),
            'pos_list'            => $this->get_detailed_pos_list(),
            'pos_tanggung_jawab'  => [],
        ];
    }

    private function _get_pos_with_data($allowed_pos = null) {
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $this->db->select('id_pos, nama_pos, tipe_pos, is_bendungan, is_bendung, sungai');
        $this->db->order_by('nama_pos', 'ASC');
        $pos_list = $this->db->get('master_pos')->result();
        
        foreach ($pos_list as $row) {
            // Hitung total data dari semua sumber
            $tel_count = $this->db->where('id_pos', $row->id_pos)->count_all_results('data_telemetri');
            $man_count = $this->db->where('id_pos', $row->id_pos)->count_all_results('data_manual');
            $bdg_count = $this->db->where('id_pos', $row->id_pos)->count_all_results('data_bendung');
            $bendungan_count = $this->db->where('id_pos', $row->id_pos)->count_all_results('data_bendungan');
            
            $row->total_data = $tel_count + $man_count + $bdg_count + $bendungan_count;
            $row->total_data_bendung = $bdg_count;
            
            // Last data dari telemetri
            $tel_last = $this->db->select('MAX(received_at) as last_data')
                                ->where('id_pos', $row->id_pos)
                                ->get('data_telemetri')->row();
            // Last data dari manual
            $man_last = $this->db->select('MAX(created_at) as last_data')
                                ->where('id_pos', $row->id_pos)
                                ->get('data_manual')->row();
            // Last data dari bendung
            $bdg_last = $this->db->select('MAX(created_at) as last_data')
                                ->where('id_pos', $row->id_pos)
                                ->get('data_bendung')->row();
            // Last data dari bendungan
            $bendungan_last = $this->db->select('MAX(created_at) as last_data')
                                    ->where('id_pos', $row->id_pos)
                                    ->get('data_bendungan')->row();
            
            $tel_time = !empty($tel_last->last_data) ? strtotime($tel_last->last_data) : 0;
            $man_time = !empty($man_last->last_data) ? strtotime($man_last->last_data) : 0;
            $bdg_time = !empty($bdg_last->last_data) ? strtotime($bdg_last->last_data) : 0;
            $bendungan_time = !empty($bendungan_last->last_data) ? strtotime($bendungan_last->last_data) : 0;
            
            // Last data overall (paling baru)
            $max_time = max($tel_time, $man_time, $bdg_time, $bendungan_time);
            if ($max_time == $tel_time && $tel_time > 0) {
                $row->last_data = $tel_last->last_data;
            } elseif ($max_time == $man_time && $man_time > 0) {
                $row->last_data = $man_last->last_data;
            } elseif ($max_time == $bdg_time && $bdg_time > 0) {
                $row->last_data = $bdg_last->last_data;
            } elseif ($max_time == $bendungan_time && $bendungan_time > 0) {
                $row->last_data = $bendungan_last->last_data;
            } else {
                $row->last_data = null;
            }
            
            // Last data bendung (khusus)
            $row->last_data_bendung = (!empty($bdg_last->last_data)) ? $bdg_last->last_data : null;
        }
        return $pos_list;
    }

    private function _count_today_data($allowed_pos = null) {
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $t = $this->db->where('DATE(received_at)', date('Y-m-d'))->count_all_results('data_telemetri');
        $m = $this->db->where('tanggal_input', date('Y-m-d'))->count_all_results('data_manual');
        $b = $this->db->where('DATE(tanggal_input)', date('Y-m-d'))->count_all_results('data_bendung');
        $bendungan = $this->db->where('DATE(tanggal_input)', date('Y-m-d'))->count_all_results('data_bendungan');
        return $t + $m + $b + $bendungan;
    }

    private function _count_online_pos($allowed_pos = null) {
        $this->db->distinct()->select('id_pos');
        $this->db->where('received_at >=', date('Y-m-d H:i:s', strtotime('-1 hour')));
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        return $this->db->get('data_telemetri')->num_rows();
    }

    private function _get_last_sync($allowed_pos = null) {
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $this->db->select('MAX(received_at) as last_sync');
        $tel = $this->db->get('data_telemetri')->row();
        
        if (!empty($tel->last_sync)) {
            return $tel->last_sync;
        }
        
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $this->db->select('MAX(created_at) as last_sync');
        $man = $this->db->get('data_manual')->row();
        
        if (!empty($man->last_sync)) {
            return $man->last_sync;
        }
        
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $this->db->select('MAX(created_at) as last_sync');
        $bendung = $this->db->get('data_bendung')->row();
        
        if (!empty($bendung->last_sync)) {
            return $bendung->last_sync;
        }
        
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $this->db->select('MAX(created_at) as last_sync');
        $bendungan = $this->db->get('data_bendungan')->row();
        
        return !empty($bendungan->last_sync) ? $bendungan->last_sync : null;
    }

    public function get_detailed_pos_list($allowed_pos = null) {
        return $this->_get_pos_with_data($allowed_pos);
    }

    // ==========================================
    // KELOLA POS
    // ==========================================
    public function get_pos_data() {
        return [
            'app_name' => 'HydroSmart', 
            'title'    => 'Kelola Master Pos', 
            'pos_list' => $this->db->select('*, is_bendungan, is_bendung')
                                  ->order_by('nama_pos', 'ASC')
                                  ->get('master_pos')->result()
        ];
    }

    public function insert_pos($post) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama_pos', 'Nama Pos', 'required|trim');
        $this->form_validation->set_rules('tipe_pos', 'Tipe Pos', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        // Tentukan is_bendungan dan is_bendung
        $is_bendungan = isset($post['is_bendungan']) ? 1 : 0;
        $is_bendung = isset($post['is_bendung']) ? 1 : 0;
        
        $data = [
            'nama_pos'     => $post['nama_pos'], 
            'tipe_pos'     => $post['tipe_pos'], 
            'nomor_pos'    => $post['nomor_pos'] ?: null, 
            'sungai'       => $post['sungai'] ?: null, 
            'lat'          => $this->_parse_float($post['lat'] ?? null), 
            'lng'          => $this->_parse_float($post['lng'] ?? null),
            'is_bendungan' => $is_bendungan,
            'is_bendung'   => $is_bendung,
            'created_at'   => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('master_pos', $data) 
            ? $this->_success('Pos berhasil didaftarkan!') 
            : $this->_error('Gagal mendaftarkan.');
    }

    public function update_pos($post) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama_pos', 'Nama Pos', 'required|trim');
        $this->form_validation->set_rules('tipe_pos', 'Tipe Pos', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        // Tentukan is_bendungan dan is_bendung
        $is_bendungan = isset($post['is_bendungan']) ? 1 : 0;
        $is_bendung = isset($post['is_bendung']) ? 1 : 0;
        
        $data = [
            'nama_pos'     => $post['nama_pos'], 
            'tipe_pos'     => $post['tipe_pos'], 
            'nomor_pos'    => $post['nomor_pos'] ?: null, 
            'sungai'       => $post['sungai'] ?: null, 
            'lat'          => $this->_parse_float($post['lat'] ?? null), 
            'lng'          => $this->_parse_float($post['lng'] ?? null),
            'is_bendungan' => $is_bendungan,
            'is_bendung'   => $is_bendung
        ];
        
        return $this->db->where('id_pos', $post['id_pos'])->update('master_pos', $data) 
            ? $this->_success('Pos diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    public function delete_pos($id) {
        $has = $this->db->where('id_pos', $id)->count_all_results('data_manual') > 0 
            || $this->db->where('id_pos', $id)->count_all_results('data_telemetri') > 0 
            || $this->db->where('id_pos', $id)->count_all_results('data_bendungan') > 0
            || $this->db->where('id_pos', $id)->count_all_results('data_bendung') > 0;
            
        if ($has) {
            return $this->_error('Pos memiliki data, tidak bisa dihapus.');
        }
        
        return $this->db->where('id_pos', $id)->delete('master_pos') 
            ? $this->_success('Pos dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    // ==========================================
    // GET POS BY ID (UNTUK EDIT)
    // ==========================================
    public function get_pos_by_id($id_pos) {
        return $this->db->where('id_pos', $id_pos)->get('master_pos')->row();
    }

    // ==========================================
    // GET POS JSON UNTUK EDIT (AJAX)
    // ==========================================
    public function get_pos_json($id_pos) {
        $pos = $this->get_pos_by_id($id_pos);
        if (!$pos) {
            return ['error' => 'Data tidak ditemukan'];
        }
        
        return [
            'id_pos'                    => $pos->id_pos,
            'nomor_pos'                 => $pos->nomor_pos,
            'nama_pos'                  => $pos->nama_pos,
            'tipe_pos'                  => $pos->tipe_pos,
            'sungai'                    => $pos->sungai,
            'wilayah_sungai'            => $pos->wilayah_sungai,
            'lat'                       => $pos->lat,
            'lng'                       => $pos->lng,
            'device_id_telemetry'       => $pos->device_id_telemetry,
            'is_bendungan'              => $pos->is_bendungan,
            'is_bendung'                => $pos->is_bendung,
            // Field tambahan untuk bendungan
            'tipe_bendungan'            => $pos->tipe_bendungan ?? null,
            'tahun_mulai_pembangunan'   => $pos->tahun_mulai_pembangunan ?? null,
            'nwl'                       => $pos->nwl ?? null,
            'nwl_volume'                => $pos->nwl_volume ?? null,
            'nwl_luas'                  => $pos->nwl_luas ?? null,
            'elevasi_mercu'             => $pos->elevasi_mercu ?? null,
            'luas_das'                  => $pos->luas_das ?? null,
            // Field tambahan untuk bendung
            'tipe_bendung'              => $pos->tipe_bendung ?? null,
            'tahun_pembangunan_bendung' => $pos->tahun_pembangunan_bendung ?? null,
            'elevasi_mercu_bendung'     => $pos->elevasi_mercu_bendung ?? null,
            'lebar_bendung'             => $pos->lebar_bendung ?? null,
            'jumlah_pintu'              => $pos->jumlah_pintu ?? null,
            'intensitas_default'        => $pos->intensitas_default ?? null,
        ];
    }

    // ==========================================
    // KELOLA ADMIN
    // ==========================================
    public function get_admin_data() {
        $admin_list = $this->db->where('role', 'admin')
                               ->order_by('nama_lengkap', 'ASC')
                               ->get('users')->result();
                               
        $pos_list = $this->db->select('id_pos, nama_pos, tipe_pos, is_bendungan, is_bendung')
                             ->order_by('nama_pos', 'ASC')
                             ->get('master_pos')->result();
                             
        $pos_map = []; 
        $all_pos = $this->db->select('id_pos, nama_pos, tipe_pos, is_bendungan, is_bendung')->get('master_pos')->result();
        foreach ($all_pos as $mp) {
            $pos_map[$mp->id_pos] = $mp;
        }
        
        foreach ($admin_list as $a) {
            $raw = $a->id_pos ?? ''; 
            $names = [];
            if (!empty($raw)) { 
                foreach (array_map('trim', explode(',', $raw)) as $id) { 
                    if (isset($pos_map[$id])) {
                        $names[] = $pos_map[$id]->nama_pos; 
                    }
                } 
            }
            $a->nama_pos = !empty($names) ? implode(', ', $names) : 'Belum Ditugaskan';
        }
        
        return [
            'app_name'   => 'HydroSmart', 
            'title'      => 'Kelola Admin', 
            'admin_list' => $admin_list, 
            'pos_list'   => $pos_list
        ];
    }

    public function insert_admin($post) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('id_pos[]', 'Cakupan Wilayah', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $ids = $post['id_pos'] ?? [];
        $data = [
            'username'     => $post['username'], 
            'password'     => password_hash($post['password'], PASSWORD_BCRYPT, ['cost' => 12]), 
            'nama_lengkap' => $post['nama_lengkap'], 
            'email'        => $post['email'] ?? null, 
            'role'         => 'admin', 
            'id_pos'       => implode(',', $ids), 
            'status'       => 'aktif', 
            'created_at'   => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('users', $data) 
            ? $this->_success('Admin berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan.');
    }

    public function update_admin($post) {
        $this->load->library('form_validation');
        $user = $this->db->get_where('users', ['id_user' => $post['id_user']])->row();
        
        if (!$user) {
            return $this->_error('User tidak ditemukan.');
        }
        
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('id_pos[]', 'Cakupan Wilayah', 'required');
        
        if ($post['username'] != $user->username) {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]');
        }
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $ids = $post['id_pos'] ?? [];
        $data = [
            'username'     => $post['username'], 
            'nama_lengkap' => $post['nama_lengkap'], 
            'email'        => $post['email'] ?? null, 
            'id_pos'       => implode(',', $ids)
        ];
        
        if (!empty($post['password'])) {
            $data['password'] = password_hash($post['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        
        return $this->db->where('id_user', $post['id_user'])->update('users', $data) 
            ? $this->_success('Data admin diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    public function delete_admin($id) {
        return $this->db->where('id_user', $id)->where('role', 'admin')->delete('users') 
            ? $this->_success('Admin dihapus!') 
            : $this->_error('Gagal menghapus.');
    }

    public function set_admin_status($id, $status) {
        $this->db->where('id_user', $id)->where('role', 'admin')->update('users', ['status' => $status]);
        return $this->_success('Status admin diubah.');
    }

    // ==========================================
    // KELOLA EMBUNG
    // ==========================================
    public function get_embung_data() {
        // Ambil semua data embung dari master_pos (jenis_aset = 'embung')
        $embung_list = $this->db->select('
                m.id_pos,
                m.nomor_pos,
                m.nama_pos,
                m.tipe_pos,
                m.sungai,
                m.wilayah_sungai,
                m.lat,
                m.lng,
                m.device_id_telemetry,
                m.is_bendungan,
                m.is_bendung,
                m.jenis_aset,
                m.created_at as pos_created_at,
                e.id_embung,
                e.kapasitas_volume,
                e.elevasi_puncak,
                e.tinggi_embung,
                e.panjang_tubuh,
                e.tahun_mulai_pembangunan,
                e.created_at,
                e.updated_at
            ')
            ->from('master_pos m')
            ->join('data_embung e', 'm.id_pos = e.id_pos', 'left')
            ->where('m.jenis_aset', 'embung')
            ->order_by('m.nama_pos', 'ASC')
            ->get()
            ->result();
        
        // Ambil data terakhir dari data_embung untuk setiap embung
        foreach ($embung_list as $embung) {
            // Hitung total data
            $embung->total_data = $this->db->where('id_pos', $embung->id_pos)
                                        ->count_all_results('data_embung');
            
            // Data terakhir
            $last_data = $this->db->select('
                    kapasitas_volume,
                    elevasi_puncak,
                    tinggi_embung,
                    panjang_tubuh,
                    tahun_mulai_pembangunan,
                    created_at
                ')
                ->where('id_pos', $embung->id_pos)
                ->order_by('id_embung', 'DESC')
                ->limit(1)
                ->get('data_embung')
                ->row();
            
            $embung->last_data = $last_data;
        }
        
        return [
            'app_name'    => 'HydroSmart',
            'title'       => 'Kelola Embung',
            'embung_list' => $embung_list
        ];
    }

    public function insert_embung($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nama_pos', 'Nama Embung', 'required|trim');
        $this->form_validation->set_rules('sungai', 'Sungai', 'required|trim');
        $this->form_validation->set_rules('wilayah_sungai', 'Wilayah Sungai', 'required|trim');
        $this->form_validation->set_rules('lat', 'Latitude', 'required|numeric');
        $this->form_validation->set_rules('lng', 'Longitude', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        // Insert ke master_pos
        $data_pos = [
            'nomor_pos'         => $post['nomor_pos'] ?? null,
            'nama_pos'          => $post['nama_pos'],
            'tipe_pos'          => 'PCH',
            'sungai'            => $post['sungai'],
            'wilayah_sungai'    => $post['wilayah_sungai'],
            'lat'               => $this->_parse_float($post['lat']),
            'lng'               => $this->_parse_float($post['lng']),
            'device_id_telemetry' => $post['device_id_telemetry'] ?? null,
            'is_bendungan'      => 0,
            'is_bendung'        => 0,
            'jenis_aset'        => 'embung',
            'created_at'        => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('master_pos', $data_pos);
        $id_pos = $this->db->insert_id();
        
        // Insert ke data_embung
        $data_embung = [
            'id_pos'                    => $id_pos,
            'kapasitas_volume'          => $this->_parse_float($post['kapasitas_volume'] ?? null),
            'elevasi_puncak'            => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'tinggi_embung'             => $this->_parse_float($post['tinggi_embung'] ?? null),
            'panjang_tubuh'             => $this->_parse_float($post['panjang_tubuh'] ?? null),
            'tahun_mulai_pembangunan'   => !empty($post['tahun_mulai_pembangunan']) ? $post['tahun_mulai_pembangunan'] : null,
            'created_at'                => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('data_embung', $data_embung);
        
        return $this->_success('Embung berhasil ditambahkan!');
    }

    public function update_embung($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nama_pos', 'Nama Embung', 'required|trim');
        $this->form_validation->set_rules('sungai', 'Sungai', 'required|trim');
        $this->form_validation->set_rules('wilayah_sungai', 'Wilayah Sungai', 'required|trim');
        $this->form_validation->set_rules('lat', 'Latitude', 'required|numeric');
        $this->form_validation->set_rules('lng', 'Longitude', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        // Update master_pos
        $data_pos = [
            'nomor_pos'         => $post['nomor_pos'] ?? null,
            'nama_pos'          => $post['nama_pos'],
            'sungai'            => $post['sungai'],
            'wilayah_sungai'    => $post['wilayah_sungai'],
            'lat'               => $this->_parse_float($post['lat']),
            'lng'               => $this->_parse_float($post['lng']),
            'device_id_telemetry' => $post['device_id_telemetry'] ?? null,
        ];
        
        $this->db->where('id_pos', $post['id_pos'])->update('master_pos', $data_pos);
        
        // Update data_embung
        $data_embung = [
            'kapasitas_volume'          => $this->_parse_float($post['kapasitas_volume'] ?? null),
            'elevasi_puncak'            => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'tinggi_embung'             => $this->_parse_float($post['tinggi_embung'] ?? null),
            'panjang_tubuh'             => $this->_parse_float($post['panjang_tubuh'] ?? null),
            'tahun_mulai_pembangunan'   => !empty($post['tahun_mulai_pembangunan']) ? $post['tahun_mulai_pembangunan'] : null,
            'updated_at'                => date('Y-m-d H:i:s')
        ];
        
        // Cek apakah sudah ada data di data_embung
        $existing = $this->db->where('id_pos', $post['id_pos'])->get('data_embung')->row();
        
        if ($existing) {
            $this->db->where('id_pos', $post['id_pos'])->update('data_embung', $data_embung);
        } else {
            $data_embung['id_pos'] = $post['id_pos'];
            $data_embung['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('data_embung', $data_embung);
        }
        
        return $this->_success('Embung berhasil diperbarui!');
    }

    public function delete_embung($id) {
        // Hapus data_embung dulu
        $this->db->where('id_pos', $id)->delete('data_embung');
        
        // Hapus master_pos
        return $this->db->where('id_pos', $id)->where('jenis_aset', 'embung')->delete('master_pos') 
            ? $this->_success('Embung berhasil dihapus!') 
            : $this->_error('Gagal menghapus embung.');
    }

    // ==========================================
    // KELOLA PENGAMAN PANTAI
    // ==========================================
    public function get_pengaman_pantai_data() {
        $pengaman_list = $this->db->select('
                id_pengaman,
                kode_integrasi,
                nama_aset,
                jenis_bangunan,
                sungai,
                wilayah_sungai,
                lat_awal,
                lng_awal,
                lat_akhir,
                lng_akhir,
                panjang,
                elevasi_puncak,
                lebar_puncak,
                tahun_dibangun,
                kabupaten_kota,
                kecamatan,
                kelurahan,
                manfaat,
                keterangan,
                created_at,
                updated_at
            ')
            ->order_by('nama_aset', 'ASC')
            ->get('data_pengaman_pantai')
            ->result();
        
        return [
            'app_name'      => 'HydroSmart',
            'title'         => 'Kelola Pengaman Pantai',
            'pengaman_list' => $pengaman_list
        ];
    }

    public function insert_pengaman_pantai($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nama_aset', 'Nama Aset', 'required|trim');
        $this->form_validation->set_rules('jenis_bangunan', 'Jenis Bangunan', 'required|trim');
        $this->form_validation->set_rules('wilayah_sungai', 'Wilayah Sungai', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $data = [
            'kode_integrasi'    => $post['kode_integrasi'] ?? null,
            'nama_aset'         => $post['nama_aset'],
            'jenis_bangunan'    => $post['jenis_bangunan'],
            'sungai'            => $post['sungai'] ?? null,
            'wilayah_sungai'    => $post['wilayah_sungai'],
            'lat_awal'          => $this->_parse_float($post['lat_awal'] ?? null),
            'lng_awal'          => $this->_parse_float($post['lng_awal'] ?? null),
            'lat_akhir'         => $this->_parse_float($post['lat_akhir'] ?? null),
            'lng_akhir'         => $this->_parse_float($post['lng_akhir'] ?? null),
            'panjang'           => $this->_parse_float($post['panjang'] ?? null),
            'elevasi_puncak'    => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'lebar_puncak'      => $this->_parse_float($post['lebar_puncak'] ?? null),
            'tahun_dibangun'    => !empty($post['tahun_dibangun']) ? $post['tahun_dibangun'] : null,
            'kabupaten_kota'    => $post['kabupaten_kota'] ?? null,
            'kecamatan'         => $post['kecamatan'] ?? null,
            'kelurahan'         => $post['kelurahan'] ?? null,
            'manfaat'           => $post['manfaat'] ?? null,
            'keterangan'        => $post['keterangan'] ?? null,
            'created_at'        => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_pengaman_pantai', $data) 
            ? $this->_success('Pengaman Pantai berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    public function update_pengaman_pantai($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nama_aset', 'Nama Aset', 'required|trim');
        $this->form_validation->set_rules('jenis_bangunan', 'Jenis Bangunan', 'required|trim');
        $this->form_validation->set_rules('wilayah_sungai', 'Wilayah Sungai', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $data = [
            'kode_integrasi'    => $post['kode_integrasi'] ?? null,
            'nama_aset'         => $post['nama_aset'],
            'jenis_bangunan'    => $post['jenis_bangunan'],
            'sungai'            => $post['sungai'] ?? null,
            'wilayah_sungai'    => $post['wilayah_sungai'],
            'lat_awal'          => $this->_parse_float($post['lat_awal'] ?? null),
            'lng_awal'          => $this->_parse_float($post['lng_awal'] ?? null),
            'lat_akhir'         => $this->_parse_float($post['lat_akhir'] ?? null),
            'lng_akhir'         => $this->_parse_float($post['lng_akhir'] ?? null),
            'panjang'           => $this->_parse_float($post['panjang'] ?? null),
            'elevasi_puncak'    => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'lebar_puncak'      => $this->_parse_float($post['lebar_puncak'] ?? null),
            'tahun_dibangun'    => !empty($post['tahun_dibangun']) ? $post['tahun_dibangun'] : null,
            'kabupaten_kota'    => $post['kabupaten_kota'] ?? null,
            'kecamatan'         => $post['kecamatan'] ?? null,
            'kelurahan'         => $post['kelurahan'] ?? null,
            'manfaat'           => $post['manfaat'] ?? null,
            'keterangan'        => $post['keterangan'] ?? null
        ];
        
        return $this->db->where('id_pengaman', $post['id_pengaman'])->update('data_pengaman_pantai', $data) 
            ? $this->_success('Pengaman Pantai berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui data.');
    }

    public function delete_pengaman_pantai($id) {
        return $this->db->where('id_pengaman', $id)->delete('data_pengaman_pantai') 
            ? $this->_success('Pengaman Pantai berhasil dihapus!') 
            : $this->_error('Gagal menghapus data.');
    }

    // ==========================================
    // KELOLA PENGENDALI SEDIMEN
    // ==========================================
    public function get_pengendali_sedimen_data() {
        $sedimen_list = $this->db->select('
                id_sedimen,
                kode_integrasi,
                nama_aset,
                jenis_bangunan,
                sungai,
                daerah_aliran_sungai,
                wilayah_sungai,
                lat,
                lng,
                daya_tampung,
                panjang,
                lebar,
                tinggi,
                tahun_dibangun,
                kabupaten_kota,
                kecamatan,
                kelurahan,
                jenis_material,
                keterangan,
                created_at,
                updated_at
            ')
            ->order_by('nama_aset', 'ASC')
            ->get('data_pengendali_sedimen')
            ->result();
        
        return [
            'app_name'      => 'HydroSmart',
            'title'         => 'Kelola Pengendali Sedimen',
            'sedimen_list'  => $sedimen_list
        ];
    }

    public function insert_pengendali_sedimen($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nama_aset', 'Nama Aset', 'required|trim');
        $this->form_validation->set_rules('jenis_bangunan', 'Jenis Bangunan', 'required|trim');
        $this->form_validation->set_rules('sungai', 'Sungai', 'required|trim');
        $this->form_validation->set_rules('wilayah_sungai', 'Wilayah Sungai', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $data = [
            'kode_integrasi'        => $post['kode_integrasi'] ?? null,
            'nama_aset'             => $post['nama_aset'],
            'jenis_bangunan'        => $post['jenis_bangunan'],
            'sungai'                => $post['sungai'],
            'daerah_aliran_sungai'  => $post['daerah_aliran_sungai'] ?? null,
            'wilayah_sungai'        => $post['wilayah_sungai'],
            'lat'                   => $this->_parse_float($post['lat'] ?? null),
            'lng'                   => $this->_parse_float($post['lng'] ?? null),
            'daya_tampung'          => $this->_parse_float($post['daya_tampung'] ?? null),
            'panjang'               => $this->_parse_float($post['panjang'] ?? null),
            'lebar'                 => $this->_parse_float($post['lebar'] ?? null),
            'tinggi'                => $this->_parse_float($post['tinggi'] ?? null),
            'tahun_dibangun'        => !empty($post['tahun_dibangun']) ? $post['tahun_dibangun'] : null,
            'kabupaten_kota'        => $post['kabupaten_kota'] ?? null,
            'kecamatan'             => $post['kecamatan'] ?? null,
            'kelurahan'             => $post['kelurahan'] ?? null,
            'jenis_material'        => $post['jenis_material'] ?? null,
            'keterangan'            => $post['keterangan'] ?? null,
            'created_at'            => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_pengendali_sedimen', $data) 
            ? $this->_success('Pengendali Sedimen berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    public function update_pengendali_sedimen($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nama_aset', 'Nama Aset', 'required|trim');
        $this->form_validation->set_rules('jenis_bangunan', 'Jenis Bangunan', 'required|trim');
        $this->form_validation->set_rules('sungai', 'Sungai', 'required|trim');
        $this->form_validation->set_rules('wilayah_sungai', 'Wilayah Sungai', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $data = [
            'kode_integrasi'        => $post['kode_integrasi'] ?? null,
            'nama_aset'             => $post['nama_aset'],
            'jenis_bangunan'        => $post['jenis_bangunan'],
            'sungai'                => $post['sungai'],
            'daerah_aliran_sungai'  => $post['daerah_aliran_sungai'] ?? null,
            'wilayah_sungai'        => $post['wilayah_sungai'],
            'lat'                   => $this->_parse_float($post['lat'] ?? null),
            'lng'                   => $this->_parse_float($post['lng'] ?? null),
            'daya_tampung'          => $this->_parse_float($post['daya_tampung'] ?? null),
            'panjang'               => $this->_parse_float($post['panjang'] ?? null),
            'lebar'                 => $this->_parse_float($post['lebar'] ?? null),
            'tinggi'                => $this->_parse_float($post['tinggi'] ?? null),
            'tahun_dibangun'        => !empty($post['tahun_dibangun']) ? $post['tahun_dibangun'] : null,
            'kabupaten_kota'        => $post['kabupaten_kota'] ?? null,
            'kecamatan'             => $post['kecamatan'] ?? null,
            'kelurahan'             => $post['kelurahan'] ?? null,
            'jenis_material'        => $post['jenis_material'] ?? null,
            'keterangan'            => $post['keterangan'] ?? null
        ];
        
        return $this->db->where('id_sedimen', $post['id_sedimen'])->update('data_pengendali_sedimen', $data) 
            ? $this->_success('Pengendali Sedimen berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui data.');
    }

    public function delete_pengendali_sedimen($id) {
        return $this->db->where('id_sedimen', $id)->delete('data_pengendali_sedimen') 
            ? $this->_success('Pengendali Sedimen berhasil dihapus!') 
            : $this->_error('Gagal menghapus data.');
    }

    // ==========================================
    // KELOLA DAERAH IRIGASI
    // ==========================================
    public function get_irigasi_data() {
        $irigasi_list = $this->db->select('
                id_irigasi,
                kode_integrasi,
                nama_aset,
                jenis_daerah_irigasi,
                kode_identifikasi,
                status_sumber_data,
                unit_kerja,
                wilayah_sungai,
                daerah_aliran_sungai,
                kewenangan,
                lintas_kewenangan,
                tahun_data,
                bangunan_pengambilan,
                status_pemeliharaan,
                di_op_kan_oleh,
                deskripsi_aset,
                keterangan_tambahan,
                status_data,
                status_verifikasi,
                provinsi,
                kabupaten_kota,
                kecamatan,
                kelurahan,
                latitude,
                longitude,
                keterangan_lokasi,
                luas_permen,
                luas_baku,
                luas_potensial,
                luas_fungsional,
                jenis_bangunan_utama,
                nama_bangunan_utama_bendungan,
                nama_bangunan_utama_bendung,
                nama_bangunan_utama_free_intake,
                sumber_air,
                luas_tangkapan_hujan,
                jenis_rawa,
                fungsi_jaringan_irigasi,
                created_at,
                updated_at
            ')
            ->order_by('nama_aset', 'ASC')
            ->get('data_irigasi')
            ->result();
        
        return [
            'app_name'      => 'HydroSmart',
            'title'         => 'Kelola Daerah Irigasi',
            'irigasi_list'  => $irigasi_list
        ];
    }

    public function insert_irigasi($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nama_aset', 'Nama Aset', 'required|trim');
        $this->form_validation->set_rules('jenis_daerah_irigasi', 'Jenis Daerah Irigasi', 'required|trim');
        $this->form_validation->set_rules('wilayah_sungai', 'Wilayah Sungai', 'required|trim');
        $this->form_validation->set_rules('daerah_aliran_sungai', 'Daerah Aliran Sungai', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $data = [
            'kode_integrasi'                => $post['kode_integrasi'] ?? null,
            'nama_aset'                     => $post['nama_aset'],
            'jenis_daerah_irigasi'          => $post['jenis_daerah_irigasi'],
            'kode_identifikasi'             => $post['kode_identifikasi'] ?? null,
            'status_sumber_data'            => $post['status_sumber_data'] ?? null,
            'unit_kerja'                    => $post['unit_kerja'] ?? null,
            'wilayah_sungai'                => $post['wilayah_sungai'],
            'daerah_aliran_sungai'          => $post['daerah_aliran_sungai'],
            'kewenangan'                    => $post['kewenangan'] ?? null,
            'lintas_kewenangan'             => $post['lintas_kewenangan'] ?? null,
            'tahun_data'                    => $post['tahun_data'] ?? null,
            'bangunan_pengambilan'          => $post['bangunan_pengambilan'] ?? null,
            'status_pemeliharaan'           => $post['status_pemeliharaan'] ?? null,
            'di_op_kan_oleh'                => $post['di_op_kan_oleh'] ?? null,
            'deskripsi_aset'                => $post['deskripsi_aset'] ?? null,
            'keterangan_tambahan'           => $post['keterangan_tambahan'] ?? null,
            'status_data'                   => $post['status_data'] ?? null,
            'status_verifikasi'             => $post['status_verifikasi'] ?? null,
            'provinsi'                      => $post['provinsi'] ?? null,
            'kabupaten_kota'                => $post['kabupaten_kota'] ?? null,
            'kecamatan'                     => $post['kecamatan'] ?? null,
            'kelurahan'                     => $post['kelurahan'] ?? null,
            'latitude'                      => $this->_parse_float($post['latitude'] ?? null),
            'longitude'                     => $this->_parse_float($post['longitude'] ?? null),
            'keterangan_lokasi'             => $post['keterangan_lokasi'] ?? null,
            'luas_permen'                   => $this->_parse_float($post['luas_permen'] ?? null),
            'luas_baku'                     => $this->_parse_float($post['luas_baku'] ?? null),
            'luas_potensial'                => $this->_parse_float($post['luas_potensial'] ?? null),
            'luas_fungsional'               => $this->_parse_float($post['luas_fungsional'] ?? null),
            'jenis_bangunan_utama'          => $post['jenis_bangunan_utama'] ?? null,
            'nama_bangunan_utama_bendungan' => $post['nama_bangunan_utama_bendungan'] ?? null,
            'nama_bangunan_utama_bendung'   => $post['nama_bangunan_utama_bendung'] ?? null,
            'nama_bangunan_utama_free_intake' => $post['nama_bangunan_utama_free_intake'] ?? null,
            'sumber_air'                    => $post['sumber_air'] ?? null,
            'luas_tangkapan_hujan'          => $this->_parse_float($post['luas_tangkapan_hujan'] ?? null),
            'jenis_rawa'                    => $post['jenis_rawa'] ?? null,
            'fungsi_jaringan_irigasi'       => $post['fungsi_jaringan_irigasi'] ?? null,
            'created_at'                    => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_irigasi', $data) 
            ? $this->_success('Daerah Irigasi berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    public function update_irigasi($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nama_aset', 'Nama Aset', 'required|trim');
        $this->form_validation->set_rules('jenis_daerah_irigasi', 'Jenis Daerah Irigasi', 'required|trim');
        $this->form_validation->set_rules('wilayah_sungai', 'Wilayah Sungai', 'required|trim');
        $this->form_validation->set_rules('daerah_aliran_sungai', 'Daerah Aliran Sungai', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $data = [
            'kode_integrasi'                => $post['kode_integrasi'] ?? null,
            'nama_aset'                     => $post['nama_aset'],
            'jenis_daerah_irigasi'          => $post['jenis_daerah_irigasi'],
            'kode_identifikasi'             => $post['kode_identifikasi'] ?? null,
            'status_sumber_data'            => $post['status_sumber_data'] ?? null,
            'unit_kerja'                    => $post['unit_kerja'] ?? null,
            'wilayah_sungai'                => $post['wilayah_sungai'],
            'daerah_aliran_sungai'          => $post['daerah_aliran_sungai'],
            'kewenangan'                    => $post['kewenangan'] ?? null,
            'lintas_kewenangan'             => $post['lintas_kewenangan'] ?? null,
            'tahun_data'                    => $post['tahun_data'] ?? null,
            'tahun_pembangunan'             => $post['tahun_pembangunan'] ?? null,
            'bangunan_pengambilan'          => $post['bangunan_pengambilan'] ?? null,
            'status_pemeliharaan'           => $post['status_pemeliharaan'] ?? null,
            'di_op_kan_oleh'                => $post['di_op_kan_oleh'] ?? null,
            'deskripsi_aset'                => $post['deskripsi_aset'] ?? null,
            'keterangan_tambahan'           => $post['keterangan_tambahan'] ?? null,
            'status_data'                   => $post['status_data'] ?? null,
            'status_verifikasi'             => $post['status_verifikasi'] ?? null,
            'provinsi'                      => $post['provinsi'] ?? null,
            'kabupaten_kota'                => $post['kabupaten_kota'] ?? null,
            'kecamatan'                     => $post['kecamatan'] ?? null,
            'kelurahan'                     => $post['kelurahan'] ?? null,
            'latitude'                      => $this->_parse_float($post['latitude'] ?? null),
            'longitude'                     => $this->_parse_float($post['longitude'] ?? null),
            'keterangan_lokasi'             => $post['keterangan_lokasi'] ?? null,
            'luas_permen'                   => $this->_parse_float($post['luas_permen'] ?? null),
            'luas_baku'                     => $this->_parse_float($post['luas_baku'] ?? null),
            'luas_potensial'                => $this->_parse_float($post['luas_potensial'] ?? null),
            'luas_fungsional'               => $this->_parse_float($post['luas_fungsional'] ?? null),
            'jenis_bangunan_utama'          => $post['jenis_bangunan_utama'] ?? null,
            'nama_bangunan_utama_bendungan' => $post['nama_bangunan_utama_bendungan'] ?? null,
            'nama_bangunan_utama_bendung'   => $post['nama_bangunan_utama_bendung'] ?? null,
            'nama_bangunan_utama_free_intake' => $post['nama_bangunan_utama_free_intake'] ?? null,
            'sumber_air'                    => $post['sumber_air'] ?? null,
            'luas_tangkapan_hujan'          => $this->_parse_float($post['luas_tangkapan_hujan'] ?? null),
            'jenis_rawa'                    => $post['jenis_rawa'] ?? null,
            'fungsi_jaringan_irigasi'       => $post['fungsi_jaringan_irigasi'] ?? null
        ];
        
        return $this->db->where('id_irigasi', $post['id_irigasi'])->update('data_irigasi', $data) 
            ? $this->_success('Daerah Irigasi berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui data.');
    }

    public function delete_irigasi($id) {
        return $this->db->where('id_irigasi', $id)->delete('data_irigasi') 
            ? $this->_success('Daerah Irigasi berhasil dihapus!') 
            : $this->_error('Gagal menghapus data.');
    }

    // ==========================================
    // KELOLA TELEMETRI
    // ==========================================
    public function get_telemetri_data() {
        // Ambil pos yang memiliki device_id_telemetry (PCH dan PDA)
        $telemetri_list = $this->db->select('
                id_pos,
                nomor_pos,
                nama_pos,
                tipe_pos,
                sungai,
                wilayah_sungai,
                lat,
                lng,
                device_id_telemetry,
                is_bendungan,
                is_bendung,
                jenis_aset,
                nwl,
                created_at
            ')
            ->where('device_id_telemetry IS NOT NULL')
            ->where('device_id_telemetry !=', '')
            ->order_by('tipe_pos', 'ASC')
            ->order_by('nama_pos', 'ASC')
            ->get('master_pos')
            ->result();
        
        // Ambil data telemetri terakhir untuk setiap pos
        foreach ($telemetri_list as $pos) {
            $last_data = $this->db->select('
                    received_at,
                    batt,
                    rain,
                    wlevel,
                    status
                ')
                ->where('id_pos', $pos->id_pos)
                ->order_by('received_at', 'DESC')
                ->limit(1)
                ->get('data_telemetri')
                ->row();
            
            $pos->last_data = $last_data;
            
            // Hitung total data telemetri
            $pos->total_data = $this->db->where('id_pos', $pos->id_pos)
                                    ->count_all_results('data_telemetri');
            
            // Status online (1 jam terakhir)
            $is_online = false;
            if ($last_data && !empty($last_data->received_at)) {
                $last_time = strtotime($last_data->received_at);
                $is_online = (time() - $last_time) < 3600; // 1 jam
            }
            $pos->is_online = $is_online;
        }
        
        return [
            'app_name'        => 'HydroSmart',
            'title'           => 'Kelola Telemetri',
            'telemetri_list'  => $telemetri_list
        ];
    }

    public function update_telemetri($post) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('device_id_telemetry', 'Device ID Telemetry', 'required|trim');
        $this->form_validation->set_rules('nama_pos', 'Nama Pos', 'required|trim');
        $this->form_validation->set_rules('tipe_pos', 'Tipe Pos', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $data = [
            'device_id_telemetry' => $post['device_id_telemetry'],
            'nama_pos'            => $post['nama_pos'],
            'tipe_pos'            => $post['tipe_pos'],
            'nomor_pos'           => $post['nomor_pos'] ?? null,
            'sungai'              => $post['sungai'] ?? null,
            'wilayah_sungai'      => $post['wilayah_sungai'] ?? null,
            'lat'                 => $this->_parse_float($post['lat'] ?? null),
            'lng'                 => $this->_parse_float($post['lng'] ?? null),
            'nwl'                 => $this->_parse_float($post['nwl'] ?? null),
        ];
        
        return $this->db->where('id_pos', $post['id_pos'])->update('master_pos', $data) 
            ? $this->_success('Data telemetri berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui data.');
    }

    public function delete_telemetri($id) {
        // Hapus device_id_telemetry saja, bukan hapus pos
        $data = [
            'device_id_telemetry' => null
        ];
        
        return $this->db->where('id_pos', $id)->update('master_pos', $data) 
            ? $this->_success('Device ID Telemetry berhasil dihapus!') 
            : $this->_error('Gagal menghapus device ID.');
    }
    
    // ==========================================
    // GET POS LIST UNIQUE UNTUK DROPDOWN
    // ==========================================
    /**
     * Get list pos UNIK untuk dropdown (menghilangkan duplikat)
     * Khusus untuk PCH dan PDA saja (bukan embung)
     */
    public function get_pos_list_unique($allowed_pos = null) {
        $this->db->select('id_pos, nama_pos, tipe_pos, is_bendungan, is_bendung');
        $this->db->from('master_pos');
        
        // Filter berdasarkan pos yang diizinkan
        if ($allowed_pos !== null && is_array($allowed_pos) && !empty($allowed_pos)) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        
        // KRUSIAL: HANYA ambil pos dengan tipe PCH atau PDA
        // DAN BUKAN embung
        $this->db->where('is_bendungan', 0);
        $this->db->where('is_bendung', 0);
        $this->db->where_in('tipe_pos', ['PCH', 'PDA']);
        $this->db->where('jenis_aset !=', 'embung');
        // Atau: $this->db->where('jenis_aset IS NULL OR jenis_aset != "embung"');
        
        // PENTING: GROUP BY untuk menghilangkan duplikat
        $this->db->group_by('id_pos');
        $this->db->order_by('nama_pos', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get list pos UNIK untuk dropdown (termasuk semua tipe kecuali embung)
     * Digunakan untuk filter di kelola manual
     */
    public function get_all_pos_unique($allowed_pos = null) {
        $this->db->select('id_pos, nama_pos, tipe_pos, is_bendungan, is_bendung');
        $this->db->from('master_pos');
        
        if ($allowed_pos !== null && is_array($allowed_pos) && !empty($allowed_pos)) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        
        // 🔥 KRUSIAL: EXCLUDE EMBUNG
        // Embung tidak boleh muncul di dropdown kelola manual
        $this->db->where('jenis_aset !=', 'embung');
        // Atau bisa juga: $this->db->where('jenis_aset IS NULL OR jenis_aset != "embung"');
        
        $this->db->group_by('id_pos');
        $this->db->order_by('nama_pos', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    // ==========================================
    // KELOLA BENDUNGAN (DENGAN KOLOM BARU)
    // ==========================================
    
    /**
     * Get all bendungan data with new columns
     */
    public function get_bendungan_data() {
        $this->db->select('
            b.id_bendungan,
            b.id_pos,
            b.id_user,
            b.tanggal_input,
            b.nwl,
            b.nwl_volume,
            b.nwl_luas,
            b.rain,
            b.elevasi,
            b.volume,
            b.luas,
            b.inflow,
            b.pltm,
            b.spillway,
            b.total_outflow,
            b.plta_status,
            b.irigasi_status,
            b.tail_water,
            b.rembesan_vnotch_h,
            b.rembesan_vnotch_q,
            b.rembesan_pump_pit_l_h,
            b.rembesan_pump_pit_l_q,
            b.rembesan_pump_pit_r_h,
            b.rembesan_pump_pit_r_q,
            b.keterangan,
            b.created_at,
            b.updated_at,
            b.tahun_mulai_pembangunan,
            b.tipe_bendungan,
            b.elevasi_mercu,
            b.luas_das,
            p.nama_pos,
            p.tipe_pos,
            p.sungai,
            u.nama_lengkap as nama_user
        ');
        $this->db->from('data_bendungan b');
        $this->db->join('master_pos p', 'b.id_pos = p.id_pos', 'left');
        $this->db->join('users u', 'b.id_user = u.id_user', 'left');
        $this->db->order_by('b.tanggal_input', 'DESC');
        $this->db->order_by('b.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get single bendungan data by ID (with new columns)
     */
    public function get_bendungan_by_id($id_bendungan) {
        $this->db->select('
            b.*,
            p.nama_pos,
            p.tipe_pos,
            p.sungai,
            u.nama_lengkap as nama_user
        ');
        $this->db->from('data_bendungan b');
        $this->db->join('master_pos p', 'b.id_pos = p.id_pos', 'left');
        $this->db->join('users u', 'b.id_user = u.id_user', 'left');
        $this->db->where('b.id_bendungan', $id_bendungan);
        return $this->db->get()->row();
    }

    /**
     * Get bendungan data by pos and date range
     */
    public function get_bendungan_by_pos($id_pos, $bulan = null) {
        $this->db->select('
            b.*,
            u.nama_lengkap as nama_user
        ');
        $this->db->from('data_bendungan b');
        $this->db->join('users u', 'b.id_user = u.id_user', 'left');
        $this->db->where('b.id_pos', $id_pos);
        
        if ($bulan) {
            $this->db->where("DATE_FORMAT(b.tanggal_input, '%Y-%m') =", $bulan);
        }
        
        $this->db->order_by('b.tanggal_input', 'DESC');
        $this->db->order_by('b.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Insert bendungan data (with new columns)
     */
    public function insert_bendungan($post, $user_id, $allowed_pos = null) {
        if ($allowed_pos !== null && !in_array($post['id_pos'], $allowed_pos)) {
            show_error('Akses Terblokir!', 403);
        }
        
        $data = [
            'id_pos'                    => $post['id_pos'],
            'id_user'                   => $user_id,
            'tanggal_input'             => $post['tanggal_input'],
            'nwl'                       => $this->_parse_float($post['nwl'] ?? null),
            'nwl_volume'                => $this->_parse_float($post['nwl_volume'] ?? null),
            'nwl_luas'                  => $this->_parse_float($post['nwl_luas'] ?? null),
            'rain'                      => $this->_parse_float($post['rain'] ?? null),
            'elevasi'                   => $this->_parse_float($post['elevasi'] ?? null),
            'volume'                    => $this->_parse_float($post['volume'] ?? null),
            'luas'                      => $this->_parse_float($post['luas'] ?? null),
            'inflow'                    => $this->_parse_float($post['inflow'] ?? null),
            'pltm'                      => $this->_parse_float($post['pltm'] ?? null),
            'spillway'                  => $this->_parse_float($post['spillway'] ?? null),
            'total_outflow'             => $this->_parse_float($post['total_outflow'] ?? null),
            'plta_status'               => $post['plta_status'] ?? null,
            'irigasi_status'            => $post['irigasi_status'] ?? null,
            'tail_water'                => $post['tail_water'] ?? null,
            'rembesan_vnotch_h'         => $this->_parse_float($post['rembesan_vnotch_h'] ?? null),
            'rembesan_vnotch_q'         => $this->_parse_float($post['rembesan_vnotch_q'] ?? null),
            'rembesan_pump_pit_l_h'     => $this->_parse_float($post['rembesan_pump_pit_l_h'] ?? null),
            'rembesan_pump_pit_l_q'     => $this->_parse_float($post['rembesan_pump_pit_l_q'] ?? null),
            'rembesan_pump_pit_r_h'     => $this->_parse_float($post['rembesan_pump_pit_r_h'] ?? null),
            'rembesan_pump_pit_r_q'     => $this->_parse_float($post['rembesan_pump_pit_r_q'] ?? null),
            'keterangan'                => $post['keterangan'] ?? null,
            // KOLOM BARU
            'tahun_mulai_pembangunan'   => !empty($post['tahun_mulai_pembangunan']) ? $post['tahun_mulai_pembangunan'] : null,
            'tipe_bendungan'            => $post['tipe_bendungan'] ?? null,
            'elevasi_mercu'             => $this->_parse_float($post['elevasi_mercu'] ?? null),
            'luas_das'                  => $this->_parse_float($post['luas_das'] ?? null),
            'created_at'                => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_bendungan', $data) 
            ? $this->_success('Data bendungan berhasil disimpan!') 
            : $this->_error('Gagal menyimpan data bendungan.');
    }

    /**
     * Update bendungan data (with new columns)
     */
    public function update_bendungan($post) {
        $data = [
            'tanggal_input'             => $post['tanggal'],
            'nwl'                       => $this->_parse_float($post['nwl'] ?? null),
            'nwl_volume'                => $this->_parse_float($post['nwl_volume'] ?? null),
            'nwl_luas'                  => $this->_parse_float($post['nwl_luas'] ?? null),
            'rain'                      => $this->_parse_float($post['rain'] ?? null),
            'elevasi'                   => $this->_parse_float($post['elevasi'] ?? null),
            'volume'                    => $this->_parse_float($post['volume'] ?? null),
            'luas'                      => $this->_parse_float($post['luas'] ?? null),
            'inflow'                    => $this->_parse_float($post['inflow'] ?? null),
            'pltm'                      => $this->_parse_float($post['pltm'] ?? null),
            'spillway'                  => $this->_parse_float($post['spillway'] ?? null),
            'total_outflow'             => $this->_parse_float($post['total_outflow'] ?? null),
            'plta_status'               => $post['plta_status'] ?? null,
            'irigasi_status'            => $post['irigasi_status'] ?? null,
            'tail_water'                => $post['tail_water'] ?? null,
            'rembesan_vnotch_h'         => $this->_parse_float($post['rembesan_vnotch_h'] ?? null),
            'rembesan_vnotch_q'         => $this->_parse_float($post['rembesan_vnotch_q'] ?? null),
            'rembesan_pump_pit_l_h'     => $this->_parse_float($post['rembesan_pump_pit_l_h'] ?? null),
            'rembesan_pump_pit_l_q'     => $this->_parse_float($post['rembesan_pump_pit_l_q'] ?? null),
            'rembesan_pump_pit_r_h'     => $this->_parse_float($post['rembesan_pump_pit_r_h'] ?? null),
            'rembesan_pump_pit_r_q'     => $this->_parse_float($post['rembesan_pump_pit_r_q'] ?? null),
            'keterangan'                => $post['keterangan'] ?? null,
            // KOLOM BARU
            'tahun_mulai_pembangunan'   => !empty($post['tahun_mulai_pembangunan']) ? $post['tahun_mulai_pembangunan'] : null,
            'tipe_bendungan'            => $post['tipe_bendungan'] ?? null,
            'elevasi_mercu'             => $this->_parse_float($post['elevasi_mercu'] ?? null),
            'luas_das'                  => $this->_parse_float($post['luas_das'] ?? null),
        ];
        
        return $this->db->where('id_bendungan', $post['id_bendungan'])->update('data_bendungan', $data) 
            ? $this->_success('Data bendungan diperbarui!') 
            : $this->_error('Gagal memperbarui data bendungan.');
    }

    /**
     * Delete bendungan data
     */
    public function delete_bendungan($id) {
        return $this->db->where('id_bendungan', $id)->delete('data_bendungan') 
            ? $this->_success('Data bendungan dihapus.') 
            : $this->_error('Gagal menghapus data bendungan.');
    }

    // ==========================================
    // KELOLA MANUAL - GET DATA (BARU)
    // ==========================================
    
    /**
     * Get data untuk kelola manual berdasarkan pos dan bulan
     */
    public function get_kelola_manual_data($id_pos, $bulan) {
        // Load M_admin jika belum
        if (!isset($this->M_admin)) {
            $this->load->model('M_admin');
        }
        
        $pos = $this->get_pos_by_id($id_pos);
        if (!$pos) {
            return ['pos' => null, 'data_list' => []];
        }
        
        $data_list = [];
        if ($pos->is_bendung == 1) {
            $data_list = $this->M_admin->get_bendung_data_by_pos($id_pos, $bulan);
        } elseif ($pos->is_bendungan == 1) {
            $data_list = $this->M_admin->get_bendungan_data_by_pos($id_pos, $bulan);
        } else {
            $data_list = $this->M_admin->get_manual_data_by_pos($id_pos, $bulan);
        }
        
        return [
            'pos' => $pos,
            'data_list' => $data_list
        ];
    }

    // ==========================================
    // FORMAT DATA UNTUK JAVASCRIPT
    // ==========================================
    
    /**
     * Format data bendung untuk JavaScript
     */
    public function format_bendung_for_js($data) {
        if (empty($data)) return [];
        $result = [];
        foreach ($data as $d) {
            $result[$d->id_bendung] = [
                'tanggal'        => $d->tanggal_input,
                'rain'           => $d->rain,
                'elevasi_mercu'  => $d->elevasi_mercu,
                'q_total'        => $d->q_total,
                'q_fc1'          => $d->q_fc1,
                'q_fc2'          => $d->q_fc2,
                'q_sal_induk'    => $d->q_sal_induk ?? null,
                'q_limpas'       => $d->q_limpas,
                'q_sungai'       => $d->q_sungai ?? null,
                'q_spam_kpbu'    => $d->q_spam_kpbu,
                'sluice_gate'    => $d->sluice_gate,
                'bukaan_pintu'   => $d->bukaan_pintu ?? null,
                'keterangan'     => $d->keterangan ?? '',
            ];
        }
        return $result;
    }

    /**
     * Format data bendungan untuk JavaScript (dengan kolom baru)
     */
    public function format_bendungan_for_js($data) {
        if (empty($data)) return [];
        $result = [];
        foreach ($data as $d) {
            $result[$d->id_bendungan] = [
                'tanggal'                  => $d->tanggal_input,
                'nwl'                      => $d->nwl,
                'nwl_volume'               => $d->nwl_volume,
                'nwl_luas'                 => $d->nwl_luas,
                'rain'                     => $d->rain,
                'elevasi'                  => $d->elevasi,
                'volume'                   => $d->volume,
                'luas'                     => $d->luas,
                'inflow'                   => $d->inflow,
                'pltm'                     => $d->pltm,
                'spillway'                 => $d->spillway,
                'total_outflow'            => $d->total_outflow,
                'plta_status'              => $d->plta_status ?? '',
                'irigasi_status'           => $d->irigasi_status ?? '',
                'tail_water'               => $d->tail_water ?? '',
                'rvh'                      => $d->rembesan_vnotch_h,
                'rvq'                      => $d->rembesan_vnotch_q,
                'rplh'                     => $d->rembesan_pump_pit_l_h,
                'rplq'                     => $d->rembesan_pump_pit_l_q,
                'rprh'                     => $d->rembesan_pump_pit_r_h,
                'rprq'                     => $d->rembesan_pump_pit_r_q,
                'keterangan'               => $d->keterangan ?? '',
                'tahun_mulai_pembangunan'  => $d->tahun_mulai_pembangunan ?? '',
                'tipe_bendungan'           => $d->tipe_bendungan ?? '',
                'elevasi_mercu'            => $d->elevasi_mercu,
                'luas_das'                 => $d->luas_das,
            ];
        }
        return $result;
    }

    /**
     * Format data pos biasa untuk JavaScript
     */
    public function format_pos_for_js($data) {
        if (empty($data)) return [];
        $result = [];
        foreach ($data as $d) {
            $result[$d->id_manual] = [
                'tanggal'    => $d->tanggal_input,
                'rain'       => $d->rain,
                'wlevel'     => $d->wlevel,
                'keterangan' => $d->keterangan ?? '',
            ];
        }
        return $result;
    }
}