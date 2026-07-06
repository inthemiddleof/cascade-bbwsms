<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_superadmin extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
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
        return $t + $m;
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
        
        return !empty($man->last_sync) ? $man->last_sync : null;
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
                nwl_volume,
                nwl_luas,
                created_at
            ')
            ->where('jenis_aset', 'embung')
            ->order_by('nama_pos', 'ASC')
            ->get('master_pos')
            ->result();
        
        // Ambil data terakhir dari data_embung untuk setiap embung
        foreach ($embung_list as $embung) {
            $last_data = $this->db->select('
                    rain, elevasi, volume, luas_genangan, 
                    inflow, outflow, tanggal_input, created_at
                ')
                ->where('id_pos', $embung->id_pos)
                ->order_by('id_embung', 'DESC')
                ->limit(1)
                ->get('data_embung')
                ->row();
            
            $embung->last_data = $last_data;
            
            // Hitung total data
            $embung->total_data = $this->db->where('id_pos', $embung->id_pos)
                                        ->count_all_results('data_embung');
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
        
        $data = [
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
            'nwl'               => $this->_parse_float($post['nwl'] ?? null),
            'nwl_volume'        => $this->_parse_float($post['nwl_volume'] ?? null),
            'nwl_luas'          => $this->_parse_float($post['nwl_luas'] ?? null),
            'created_at'        => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('master_pos', $data) 
            ? $this->_success('Embung berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan embung.');
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
        
        $data = [
            'nomor_pos'         => $post['nomor_pos'] ?? null,
            'nama_pos'          => $post['nama_pos'],
            'sungai'            => $post['sungai'],
            'wilayah_sungai'    => $post['wilayah_sungai'],
            'lat'               => $this->_parse_float($post['lat']),
            'lng'               => $this->_parse_float($post['lng']),
            'device_id_telemetry' => $post['device_id_telemetry'] ?? null,
            'nwl'               => $this->_parse_float($post['nwl'] ?? null),
            'nwl_volume'        => $this->_parse_float($post['nwl_volume'] ?? null),
            'nwl_luas'          => $this->_parse_float($post['nwl_luas'] ?? null),
        ];
        
        return $this->db->where('id_pos', $post['id_pos'])->update('master_pos', $data) 
            ? $this->_success('Embung berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui embung.');
    }

    public function delete_embung($id) {
        // Cek apakah ada data di data_embung
        $has_data = $this->db->where('id_pos', $id)->count_all_results('data_embung') > 0;
        
        if ($has_data) {
            return $this->_error('Embung memiliki data pengukuran, tidak bisa dihapus. Hapus data terlebih dahulu.');
        }
        
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
                kondisi_bangunan,
                status_operasi,
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
            'kondisi_bangunan'  => $post['kondisi_bangunan'] ?? null,
            'status_operasi'    => $post['status_operasi'] ?? null,
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
            'kondisi_bangunan'  => $post['kondisi_bangunan'] ?? null,
            'status_operasi'    => $post['status_operasi'] ?? null,
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
                kondisi,
                status_operasi,
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
            'kondisi'               => $post['kondisi'] ?? null,
            'status_operasi'        => $post['status_operasi'] ?? null,
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
            'kondisi'               => $post['kondisi'] ?? null,
            'status_operasi'        => $post['status_operasi'] ?? null,
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
                tahun_pembangunan,
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
}