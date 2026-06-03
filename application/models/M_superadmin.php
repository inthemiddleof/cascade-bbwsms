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
}