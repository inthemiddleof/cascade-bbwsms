<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_admin extends CI_Model {

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

    private function _empty_manual_data($admin_type = 'hidrologi') {
        return [
            'app_name' => 'HydroSmart',
            'title' => 'Kelola Laporan Manual',
            'pos_list' => [],
            'pos' => null,
            'bulan' => null,
            'data_list' => [],
            'admin_type' => $admin_type
        ];
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function get_dashboard_data($allowed_pos) {
        $pos_tanggung_jawab = $this->_get_pos_with_data($allowed_pos);

        $total_pos = $this->db->where_in('id_pos', $allowed_pos)->count_all_results('master_pos');
        $total_pch = $this->db->where_in('id_pos', $allowed_pos)->where('tipe_pos', 'PCH')->count_all_results('master_pos');
        $total_pda = $this->db->where_in('id_pos', $allowed_pos)->where('tipe_pos', 'PDA')->count_all_results('master_pos');
        
        $all_petugas = $this->db->where('role', 'petugas')->get('users')->result();
        $total_petugas = 0; 
        $petugas_aktif = 0;
        
        foreach ($all_petugas as $p) {
            $p_ids = array_map('trim', explode(',', $p->id_pos));
            if (array_intersect($p_ids, $allowed_pos)) { 
                $total_petugas++; 
                if ($p->status === 'aktif') $petugas_aktif++; 
            }
        }

        $total_data_hari_ini = $this->_count_today_data($allowed_pos);
        $pos_online = $this->_count_online_pos($allowed_pos);
        $last_sync = $this->_get_last_sync($allowed_pos);

        return [
            'app_name'            => 'HydroSmart', 
            'title'               => 'Dashboard',
            'total_pos'           => $total_pos, 
            'total_pch'           => $total_pch, 
            'total_pda'           => $total_pda,
            'total_petugas'       => $total_petugas, 
            'petugas_aktif'       => $petugas_aktif,
            'total_data_hari_ini' => $total_data_hari_ini, 
            'pos_online'          => $pos_online, 
            'last_sync'           => $last_sync,
            'pos_list'            => $this->get_detailed_pos_list($allowed_pos),
            'pos_tanggung_jawab'  => $pos_tanggung_jawab,
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
            $tel_count = $this->db->where('id_pos', $row->id_pos)->count_all_results('data_telemetri');
            $man_count = $this->db->where('id_pos', $row->id_pos)->count_all_results('data_manual');
            $bdg_count = $this->db->where('id_pos', $row->id_pos)->count_all_results('data_bendung');
            $bendungan_count = $this->db->where('id_pos', $row->id_pos)->count_all_results('data_bendungan');
            
            $row->total_data = $tel_count + $man_count + $bdg_count + $bendungan_count;
            $row->total_data_bendung = $bdg_count;
            
            $tel_last = $this->db->select('MAX(received_at) as last_data')
                                ->where('id_pos', $row->id_pos)
                                ->get('data_telemetri')->row();
            $man_last = $this->db->select('MAX(created_at) as last_data')
                                ->where('id_pos', $row->id_pos)
                                ->get('data_manual')->row();
            $bdg_last = $this->db->select('MAX(created_at) as last_data')
                                ->where('id_pos', $row->id_pos)
                                ->get('data_bendung')->row();
            $bendungan_last = $this->db->select('MAX(created_at) as last_data')
                                    ->where('id_pos', $row->id_pos)
                                    ->get('data_bendungan')->row();
            
            $tel_time = !empty($tel_last->last_data) ? strtotime($tel_last->last_data) : 0;
            $man_time = !empty($man_last->last_data) ? strtotime($man_last->last_data) : 0;
            $bdg_time = !empty($bdg_last->last_data) ? strtotime($bdg_last->last_data) : 0;
            $bendungan_time = !empty($bendungan_last->last_data) ? strtotime($bendungan_last->last_data) : 0;
            
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
            
            $row->last_data_bendung = (!empty($bdg_last->last_data)) ? $bdg_last->last_data : null;
        }
        return $pos_list;
    }

    private function _count_today_data($allowed_pos = null) {
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $t = $this->db->where('DATE(received_at)', date('Y-m-d'))->count_all_results('data_telemetri');
        $m = $this->db->where('DATE(tanggal_input)', date('Y-m-d'))->count_all_results('data_manual');
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
    // KELOLA PETUGAS
    // ==========================================
    public function get_petugas_data($allowed_pos) {
        $all = $this->db->where('role', 'petugas')->get('users')->result();
        $petugas_list = [];
        
        if ($allowed_pos !== null) {
            foreach ($all as $p) {
                $u = $this->db->select('id_pos')->where('id_user', $p->id_user)->get('users')->row();
                if (!empty($u->id_pos) && array_intersect(array_map('trim', explode(',', $u->id_pos)), $allowed_pos)) {
                    $petugas_list[] = $p;
                }
            }
            $pos_list = $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos, is_bendungan, is_bendung')
                                 ->where_in('id_pos', $allowed_pos)
                                 ->order_by('nama_pos', 'ASC')
                                 ->get('master_pos')->result();
        } else {
            $petugas_list = $all;
            $pos_list = $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos, is_bendungan, is_bendung')
                                 ->order_by('nama_pos', 'ASC')
                                 ->get('master_pos')->result();
        }
        
        $map = []; 
        $all_pos = $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos, is_bendungan, is_bendung')->get('master_pos')->result();
        foreach ($all_pos as $mp) {
            $map[$mp->id_pos] = $mp;
        }
        
        foreach ($petugas_list as $p) {
            $raw = $p->id_pos ?: '';
            if (empty($raw)) { 
                $u = $this->db->select('id_pos')->where('id_user', $p->id_user)->get('users')->row(); 
                $raw = $u->id_pos ?? ''; 
            }
            $names = []; 
            $types = []; 
            $nums = [];
            if (!empty($raw)) {
                foreach (array_map('trim', explode(',', $raw)) as $id) {
                    if (isset($map[$id])) { 
                        $names[] = $map[$id]->nama_pos; 
                        $types[] = $map[$id]->tipe_pos; 
                        $nums[] = $map[$id]->nomor_pos ?: $id; 
                    }
                }
            }
            $p->nama_pos = $names ? implode(', ', $names) : 'Belum Ditugaskan';
            $p->tipe_pos = $types ? implode(', ', array_unique($types)) : '-';
            $p->nomor_pos = $nums ? implode(', ', array_unique($nums)) : '-';
        }
        
        return [
            'app_name'     => 'HydroSmart', 
            'title'        => 'Kelola Petugas', 
            'petugas_list' => $petugas_list, 
            'pos_list'     => $pos_list
        ];
    }

    public function insert_petugas($post, $allowed) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('id_pos[]', 'Pos', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $ids = $post['id_pos'] ?? [];
        if ($allowed !== null) { 
            foreach ($ids as $id) { 
                if (!in_array($id, $allowed)) {
                    show_error('Akses Terblokir!', 403); 
                }
            } 
        }
        
        $data = [
            'username'     => $post['username'], 
            'password'     => password_hash($post['password'], PASSWORD_BCRYPT, ['cost' => 12]), 
            'nama_lengkap' => $post['nama_lengkap'], 
            'email'        => $post['email'] ?? null, 
            'role'         => 'petugas', 
            'id_pos'       => implode(',', $ids), 
            'status'       => 'aktif', 
            'created_at'   => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('users', $data) 
            ? $this->_success('Petugas berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan.');
    }

    public function update_petugas($post, $allowed) {
        $this->load->library('form_validation');
        $user = $this->db->get_where('users', ['id_user' => $post['id_user']])->row();
        
        if (!$user) {
            return $this->_error('User tidak ditemukan.');
        }
        
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('id_pos[]', 'Pos', 'required');
        
        if ($post['username'] != $user->username) {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]');
        }
        
        if ($this->form_validation->run() == FALSE) {
            return $this->_error(validation_errors());
        }
        
        $ids = $post['id_pos'] ?? [];
        if ($allowed !== null) { 
            foreach ($ids as $id) { 
                if (!in_array($id, $allowed)) {
                    show_error('Akses Ditolak!', 403); 
                }
            } 
        }
        
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
            ? $this->_success('Data diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    public function delete_petugas($id) {
        return $this->db->where('id_user', $id)->delete('users') 
            ? $this->_success('Akun dihapus!') 
            : $this->_error('Gagal menghapus.');
    }

    public function set_status($id, $status) {
        $this->db->where('id_user', $id)->update('users', ['status' => $status]);
        return $this->_success('Status diubah.');
    }

        // ==========================================
    // KELOLA MANUAL - MAIN FUNCTION
    // ==========================================
    public function get_manual_data($pos_filter, $bulan, $allowed_pos, $admin_type = 'hidrologi') {
        
        // ==========================================
        // 1. ADMIN IRIGASI - Ambil dari tabel data_irigasi
        // ==========================================
        if ($admin_type == 'irigasi') {
            $data_list = $this->get_irigasi_manual_data($bulan);
            
            return [
                'app_name' => 'HydroSmart',
                'title' => 'Kelola Laporan Manual - Irigasi',
                'pos_list' => [],
                'pos' => null,
                'bulan' => $bulan,
                'data_list' => $data_list,
                'admin_type' => $admin_type
            ];
        }
        
        // ==========================================
        // 2. ADMIN PANTAI - Ambil dari tabel data_pengaman_pantai
        // ==========================================
        if ($admin_type == 'pantai') {
            $data_list = $this->get_pantai_manual_data($bulan);
            
            return [
                'app_name' => 'HydroSmart',
                'title' => 'Kelola Laporan Manual - Pengaman Pantai',
                'pos_list' => [],
                'pos' => null,
                'bulan' => $bulan,
                'data_list' => $data_list,
                'admin_type' => $admin_type
            ];
        }
        
        // ==========================================
        // 3. ADMIN SEDIMEN - Ambil dari tabel data_pengendali_sedimen
        // ==========================================
        if ($admin_type == 'sedimen') {
            $data_list = $this->get_sedimen_manual_data($bulan);
            
            return [
                'app_name' => 'HydroSmart',
                'title' => 'Kelola Laporan Manual - Pengendali Sedimen',
                'pos_list' => [],
                'pos' => null,
                'bulan' => $bulan,
                'data_list' => $data_list,
                'admin_type' => $admin_type
            ];
        }
        
        // ==========================================
        // 4. ADMIN EMBUNG
        // ==========================================
        if ($admin_type == 'embung') {
            if (empty($allowed_pos) || $allowed_pos[0] == 0) {
                return $this->_empty_manual_data($admin_type);
            }
            
            $this->db->select('id_pos, nama_pos, tipe_pos, jenis_aset');
            $this->db->where_in('id_pos', $allowed_pos);
            $this->db->where('jenis_aset', 'embung');
            $pos_list = $this->db->order_by('nama_pos', 'ASC')->get('master_pos')->result();
            
            if (empty($pos_list)) {
                return $this->_empty_manual_data($admin_type);
            }
            
            $selected_id = (!empty($pos_filter) && in_array($pos_filter, array_column($pos_list, 'id_pos'))) 
                ? $pos_filter 
                : $pos_list[0]->id_pos;
            
            $pos = $this->db->where('id_pos', $selected_id)->get('master_pos')->row();
            
            $data_list = $this->get_embung_manual_data_by_pos($selected_id, $bulan);
            
            return [
                'app_name' => 'HydroSmart',
                'title' => 'Kelola Laporan Manual - Embung',
                'pos_list' => $pos_list,
                'pos' => $pos,
                'bulan' => $bulan,
                'data_list' => $data_list,
                'admin_type' => $admin_type
            ];
        }
        
        // ==========================================
        // 5. ADMIN HIDROLOGI (Bendungan & Bendung)
        // ==========================================
        if (empty($allowed_pos) || $allowed_pos[0] == 0) {
            return $this->_empty_manual_data($admin_type);
        }
        
        $this->db->select('id_pos, nama_pos, tipe_pos, is_bendungan, is_bendung');
        $this->db->where_in('id_pos', $allowed_pos);
        $pos_list = $this->db->order_by('nama_pos', 'ASC')->get('master_pos')->result();
        
        if (empty($pos_list)) {
            return $this->_empty_manual_data($admin_type);
        }
        
        $selected_id = (!empty($pos_filter) && in_array($pos_filter, array_column($pos_list, 'id_pos'))) 
            ? $pos_filter 
            : $pos_list[0]->id_pos;
        
        $pos = $this->db->where('id_pos', $selected_id)->get('master_pos')->row();
        
        if ($pos->is_bendung == 1) {
            $data_list = $this->get_bendung_data_by_pos($selected_id, $bulan);
            $title = 'Kelola Laporan Manual - Bendung';
        } elseif ($pos->is_bendungan == 1) {
            $data_list = $this->get_bendungan_data_by_pos($selected_id, $bulan);
            $title = 'Kelola Laporan Manual - Bendungan';
        } else {
            $data_list = $this->get_manual_data_by_pos($selected_id, $bulan);
            $title = 'Kelola Laporan Manual - Pos';
        }
        
        return [
            'app_name' => 'HydroSmart',
            'title' => $title,
            'pos_list' => $pos_list,
            'pos' => $pos,
            'bulan' => $bulan,
            'data_list' => $data_list,
            'admin_type' => $admin_type
        ];
    }

    // ==========================================
    // DATA POS MANUAL (ADMIN HIDROLOGI)
    // ==========================================
    public function get_manual_data_by_pos($id_pos, $bulan) {
        $this->db->select('
            m.id_manual,
            m.id_pos,
            m.id_user,
            m.tanggal_input,
            m.rain,
            m.wlevel,
            m.keterangan,
            m.created_at,
            u.nama_lengkap as nama_user,
            p.nama_pos,
            p.tipe_pos
        ');
        $this->db->from('data_manual m');
        $this->db->join('users u', 'm.id_user = u.id_user', 'left');
        $this->db->join('master_pos p', 'm.id_pos = p.id_pos', 'left');
        $this->db->where('m.id_pos', $id_pos);
        if ($bulan) {
            $this->db->where("DATE_FORMAT(m.tanggal_input, '%Y-%m') =", $bulan);
        }
        $this->db->order_by('m.tanggal_input', 'DESC');
        $this->db->order_by('m.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA IRIGASI UNTUK MANUAL (ADMIN IRIGASI)
    // ==========================================
    public function get_irigasi_manual_data($bulan) {
        $this->db->select("
            i.id_irigasi as id_manual,
            i.nama_aset as nama_pos,
            'IRIGASI' as tipe_pos,
            i.created_at as tanggal_input,
            i.created_at,
            NULL as rain,
            NULL as wlevel,
            i.keterangan_tambahan as keterangan,
            i.luas_fungsional as nilai_1,
            i.luas_potensial as nilai_2,
            i.status_pemeliharaan as status,
            i.kabupaten_kota,
            i.kecamatan,
            'irigasi' as sumber_data
        ", false);
        $this->db->from('data_irigasi i');
        if ($bulan) {
            $this->db->where("DATE_FORMAT(i.created_at, '%Y-%m') =", $bulan);
        }
        $this->db->order_by('i.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA PANTAI UNTUK MANUAL (ADMIN PANTAI)
    // ==========================================
    public function get_pantai_manual_data($bulan) {
        $this->db->select("
            p.id_pengaman as id_manual,
            p.nama_aset as nama_pos,
            'PANTAI' as tipe_pos,
            p.created_at as tanggal_input,
            p.created_at,
            NULL as rain,
            NULL as wlevel,
            p.jenis_bangunan as nilai_1,
            p.panjang as nilai_2,
            p.kabupaten_kota as nilai_3,
            p.kecamatan as nilai_4,
            p.keterangan,
            'pantai' as sumber_data
        ", false);
        $this->db->from('data_pengaman_pantai p');
        if ($bulan) {
            $this->db->where("DATE_FORMAT(p.created_at, '%Y-%m') =", $bulan);
        }
        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA SEDIMEN UNTUK MANUAL (ADMIN SEDIMEN)
    // ==========================================
    public function get_sedimen_manual_data($bulan) {
        $this->db->select("
            s.id_sedimen as id_manual,
            s.nama_aset as nama_pos,
            'SEDIMEN' as tipe_pos,
            s.created_at as tanggal_input,
            s.created_at,
            NULL as rain,
            NULL as wlevel,
            s.jenis_bangunan as nilai_1,
            s.daya_tampung as nilai_2,
            s.panjang as nilai_3,
            s.tinggi as nilai_4,
            s.keterangan,
            'sedimen' as sumber_data
        ", false);
        $this->db->from('data_pengendali_sedimen s');
        if ($bulan) {
            $this->db->where("DATE_FORMAT(s.created_at, '%Y-%m') =", $bulan);
        }
        $this->db->order_by('s.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA EMBUNG PER POS (ADMIN EMBUNG)
    // ==========================================
    public function get_embung_manual_data_by_pos($id_pos, $bulan) {
        $this->db->select("
            e.id_embung as id_manual,
            p.nama_pos,
            'EMBUNG' as tipe_pos,
            e.created_at as tanggal_input,
            e.created_at,
            NULL as rain,
            NULL as wlevel,
            e.kapasitas_volume as nilai_1,
            e.elevasi_puncak as nilai_2,
            e.tinggi_embung as nilai_3,
            e.panjang_tubuh as nilai_4,
            'embung' as sumber_data
        ", false);
        $this->db->from('data_embung e');
        $this->db->join('master_pos p', 'e.id_pos = p.id_pos', 'left');
        $this->db->where('e.id_pos', $id_pos);
        if ($bulan) {
            $this->db->where("DATE_FORMAT(e.created_at, '%Y-%m') =", $bulan);
        }
        $this->db->order_by('e.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA BENDUNGAN
    // ==========================================
    public function get_bendungan_data_by_pos($id_pos, $bulan) {
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
        $this->db->where("DATE_FORMAT(d.tanggal_input, '%Y-%m') =", $bulan);
        $this->db->order_by('d.tanggal_input', 'DESC');
        $this->db->order_by('d.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA BENDUNG
    // ==========================================
    public function get_bendung_data_by_pos($id_pos, $bulan) {
        $this->db->select('
            b.id_bendung,
            b.id_pos,
            b.id_user,
            b.tanggal_input,
            b.rain,
            b.elevasi_mercu,
            b.q_total,
            b.q_fc1,
            b.q_fc2,
            b.q_sal_induk,
            b.q_limpas,
            b.q_sungai,
            b.q_spam_kpbu,
            b.sluice_gate,
            b.bukaan_pintu,
            b.keterangan,
            b.created_at,
            u.nama_lengkap as nama_user
        ');
        $this->db->from('data_bendung b');
        $this->db->join('users u', 'b.id_user = u.id_user', 'left');
        $this->db->where('b.id_pos', $id_pos);
        $this->db->where("DATE_FORMAT(b.tanggal_input, '%Y-%m')", $bulan);
        $this->db->order_by('b.tanggal_input', 'DESC');
        $this->db->order_by('b.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ==========================================
    // INSERT DATA
    // ==========================================
    public function insert_manual_pos($post, $user_id, $allowed) {
        if ($allowed !== null && !in_array($post['id_pos'], $allowed)) {
            show_error('Akses Terblokir!', 403);
        }
        
        $data = [
            'id_pos'        => $post['id_pos'], 
            'id_user'       => $user_id, 
            'tanggal_input' => $post['tanggal_input'], 
            'rain'          => $this->_parse_float($post['rain'] ?? null), 
            'wlevel'        => $this->_parse_float($post['wlevel'] ?? null), 
            'keterangan'    => $post['keterangan'] ?: null, 
            'created_at'    => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_manual', $data) 
            ? $this->_success('Data berhasil disimpan!') 
            : $this->_error('Gagal menyimpan.');
    }

    public function insert_manual_bendungan($post, $user_id, $allowed) {
        if ($allowed !== null && !in_array($post['id_pos'], $allowed)) {
            show_error('Akses Terblokir!', 403);
        }
        
        $data = [
            'id_pos'                  => $post['id_pos'], 
            'id_user'                 => $user_id, 
            'tanggal_input'           => $post['tanggal_input'],
            'nwl'                     => $this->_parse_float($post['nwl'] ?? null), 
            'nwl_volume'              => $this->_parse_float($post['nwl_volume'] ?? null), 
            'nwl_luas'                => $this->_parse_float($post['nwl_luas'] ?? null),
            'rain'                    => $this->_parse_float($post['rain'] ?? null), 
            'elevasi'                 => $this->_parse_float($post['elevasi'] ?? null), 
            'volume'                  => $this->_parse_float($post['volume'] ?? null), 
            'luas'                    => $this->_parse_float($post['luas'] ?? null),
            'inflow'                  => $this->_parse_float($post['inflow'] ?? null), 
            'pltm'                    => $this->_parse_float($post['pltm'] ?? null), 
            'spillway'                => $this->_parse_float($post['spillway'] ?? null), 
            'total_outflow'           => $this->_parse_float($post['total_outflow'] ?? null),
            'plta_status'             => $post['plta_status'] ?: null, 
            'irigasi_status'          => $post['irigasi_status'] ?: null, 
            'tail_water'              => $post['tail_water'] ?: null,
            'rembesan_vnotch_h'       => $this->_parse_float($post['rembesan_vnotch_h'] ?? null), 
            'rembesan_vnotch_q'       => $this->_parse_float($post['rembesan_vnotch_q'] ?? null),
            'rembesan_pump_pit_l_h'   => $this->_parse_float($post['rembesan_pump_pit_l_h'] ?? null), 
            'rembesan_pump_pit_l_q'   => $this->_parse_float($post['rembesan_pump_pit_l_q'] ?? null),
            'rembesan_pump_pit_r_h'   => $this->_parse_float($post['rembesan_pump_pit_r_h'] ?? null), 
            'rembesan_pump_pit_r_q'   => $this->_parse_float($post['rembesan_pump_pit_r_q'] ?? null),
            'keterangan'              => $post['keterangan'] ?: null,
            'tahun_mulai_pembangunan' => !empty($post['tahun_mulai_pembangunan']) ? $post['tahun_mulai_pembangunan'] : null,
            'tipe_bendungan'          => $post['tipe_bendungan'] ?? null,
            'elevasi_mercu'           => $this->_parse_float($post['elevasi_mercu'] ?? null),
            'luas_das'                => $this->_parse_float($post['luas_das'] ?? null),
            'created_at'              => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_bendungan', $data) 
            ? $this->_success('Data bendungan berhasil disimpan!') 
            : $this->_error('Gagal menyimpan.');
    }

    public function insert_manual_bendung($post, $user_id, $allowed) {
        if ($allowed !== null && !in_array($post['id_pos'], $allowed)) {
            show_error('Akses Terblokir!', 403);
        }
        
        $data = [
            'id_pos'         => $post['id_pos'],
            'id_user'        => $user_id,
            'tanggal_input'  => $post['tanggal_input'],
            'rain'           => $this->_parse_float($post['rain'] ?? null),
            'elevasi_mercu'  => $this->_parse_float($post['elevasi_mercu'] ?? null),
            'q_total'        => $this->_parse_float($post['q_total'] ?? null),
            'q_fc1'          => $this->_parse_float($post['q_fc1'] ?? null),
            'q_fc2'          => $this->_parse_float($post['q_fc2'] ?? null),
            'q_sal_induk'    => $this->_parse_float($post['q_sal_induk'] ?? null),
            'q_limpas'       => $this->_parse_float($post['q_limpas'] ?? null),
            'q_sungai'       => $this->_parse_float($post['q_sungai'] ?? null),
            'q_spam_kpbu'    => $this->_parse_float($post['q_spam_kpbu'] ?? null),
            'sluice_gate'    => $this->_parse_float($post['sluice_gate'] ?? null),
            'bukaan_pintu'   => $this->_parse_float($post['bukaan_pintu'] ?? null),
            'keterangan'     => $post['keterangan'] ?: null,
            'created_at'     => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_bendung', $data) 
            ? $this->_success('Data bendung berhasil disimpan!') 
            : $this->_error('Gagal menyimpan.');
    }

    // ==========================================
    // INSERT DATA IRIGASI (ADMIN IRIGASI)
    // ==========================================
    public function insert_manual_irigasi($post) {
        $data = [
            'nama_aset' => $post['nama_aset'],
            'jenis_daerah_irigasi' => $post['jenis_daerah_irigasi'] ?? null,
            'kode_identifikasi' => $post['kode_identifikasi'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'daerah_aliran_sungai' => $post['daerah_aliran_sungai'] ?? null,
            'kewenangan' => $post['kewenangan'] ?? null,
            'status_pemeliharaan' => $post['status_pemeliharaan'] ?? null,
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'luas_potensial' => $this->_parse_float($post['luas_potensial'] ?? null),
            'luas_fungsional' => $this->_parse_float($post['luas_fungsional'] ?? null),
            'keterangan_tambahan' => $post['keterangan_tambahan'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_irigasi', $data) 
            ? $this->_success('Data irigasi berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    // ==========================================
    // INSERT DATA PANTAI (ADMIN PANTAI)
    // ==========================================
    public function insert_manual_pantai($post) {
        $data = [
            'nama_aset' => $post['nama_aset'],
            'jenis_bangunan' => $post['jenis_bangunan'] ?? null,
            'sungai' => $post['sungai'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'panjang' => $this->_parse_float($post['panjang'] ?? null),
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'keterangan' => $post['keterangan'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_pengaman_pantai', $data) 
            ? $this->_success('Data pengaman pantai berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    // ==========================================
    // INSERT DATA SEDIMEN (ADMIN SEDIMEN)
    // ==========================================
    public function insert_manual_sedimen($post) {
        $data = [
            'nama_aset' => $post['nama_aset'],
            'jenis_bangunan' => $post['jenis_bangunan'] ?? null,
            'sungai' => $post['sungai'] ?? null,
            'daerah_aliran_sungai' => $post['daerah_aliran_sungai'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'daya_tampung' => $this->_parse_float($post['daya_tampung'] ?? null),
            'panjang' => $this->_parse_float($post['panjang'] ?? null),
            'tinggi' => $this->_parse_float($post['tinggi'] ?? null),
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'keterangan' => $post['keterangan'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_pengendali_sedimen', $data) 
            ? $this->_success('Data pengendali sedimen berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    // ==========================================
    // INSERT DATA EMBUNG (ADMIN EMBUNG)
    // ==========================================
    public function insert_manual_embung($post) {
        $data = [
            'id_pos' => $post['id_pos'],
            'kapasitas_volume' => $this->_parse_float($post['kapasitas_volume'] ?? null),
            'elevasi_puncak' => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'tinggi_embung' => $this->_parse_float($post['tinggi_embung'] ?? null),
            'panjang_tubuh' => $this->_parse_float($post['panjang_tubuh'] ?? null),
            'tahun_mulai_pembangunan' => $post['tahun_mulai_pembangunan'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_embung', $data) 
            ? $this->_success('Data embung berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    // ==========================================
    // UPDATE DATA
    // ==========================================
    public function update_manual_pos($post) {
        $data = [
            'tanggal_input' => $post['tanggal'], 
            'rain'          => $this->_parse_float($post['rain'] ?? null), 
            'wlevel'        => $this->_parse_float($post['wlevel'] ?? null), 
            'keterangan'    => $post['keterangan'] ?? null
        ];
        
        return $this->db->where('id_manual', $post['id_manual'])->update('data_manual', $data) 
            ? $this->_success('Data diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    public function update_manual_bendungan($post) {
        $data = [
            'tanggal_input'           => $post['tanggal'],
            'nwl'                     => $this->_parse_float($post['nwl'] ?? null), 
            'nwl_volume'              => $this->_parse_float($post['nwl_volume'] ?? null), 
            'nwl_luas'                => $this->_parse_float($post['nwl_luas'] ?? null),
            'rain'                    => $this->_parse_float($post['rain'] ?? null), 
            'elevasi'                 => $this->_parse_float($post['elevasi'] ?? null),
            'volume'                  => $this->_parse_float($post['volume'] ?? null), 
            'luas'                    => $this->_parse_float($post['luas'] ?? null),
            'inflow'                  => $this->_parse_float($post['inflow'] ?? null), 
            'pltm'                    => $this->_parse_float($post['pltm'] ?? null), 
            'spillway'                => $this->_parse_float($post['spillway'] ?? null), 
            'total_outflow'           => $this->_parse_float($post['total_outflow'] ?? null),
            'plta_status'             => $post['plta_status'] ?? null, 
            'irigasi_status'          => $post['irigasi_status'] ?? null, 
            'tail_water'              => $post['tail_water'] ?? null,
            'rembesan_vnotch_h'       => $this->_parse_float($post['rembesan_vnotch_h'] ?? null), 
            'rembesan_vnotch_q'       => $this->_parse_float($post['rembesan_vnotch_q'] ?? null),
            'rembesan_pump_pit_l_h'   => $this->_parse_float($post['rembesan_pump_pit_l_h'] ?? null), 
            'rembesan_pump_pit_l_q'   => $this->_parse_float($post['rembesan_pump_pit_l_q'] ?? null),
            'rembesan_pump_pit_r_h'   => $this->_parse_float($post['rembesan_pump_pit_r_h'] ?? null), 
            'rembesan_pump_pit_r_q'   => $this->_parse_float($post['rembesan_pump_pit_r_q'] ?? null),
            'keterangan'              => $post['keterangan'] ?? null,
            'tahun_mulai_pembangunan' => !empty($post['tahun_mulai_pembangunan']) ? $post['tahun_mulai_pembangunan'] : null,
            'tipe_bendungan'          => $post['tipe_bendungan'] ?? null,
            'elevasi_mercu'           => $this->_parse_float($post['elevasi_mercu'] ?? null),
            'luas_das'                => $this->_parse_float($post['luas_das'] ?? null),
        ];
        
        return $this->db->where('id_bendungan', $post['id_bendungan'])->update('data_bendungan', $data) 
            ? $this->_success('Data bendungan diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    public function update_manual_bendung($post) {
        $data = [
            'tanggal_input'  => $post['tanggal'],
            'rain'           => $this->_parse_float($post['rain'] ?? null),
            'elevasi_mercu'  => $this->_parse_float($post['elevasi_mercu'] ?? null),
            'q_total'        => $this->_parse_float($post['q_total'] ?? null),
            'q_fc1'          => $this->_parse_float($post['q_fc1'] ?? null),
            'q_fc2'          => $this->_parse_float($post['q_fc2'] ?? null),
            'q_sal_induk'    => $this->_parse_float($post['q_sal_induk'] ?? null),
            'q_limpas'       => $this->_parse_float($post['q_limpas'] ?? null),
            'q_sungai'       => $this->_parse_float($post['q_sungai'] ?? null),
            'q_spam_kpbu'    => $this->_parse_float($post['q_spam_kpbu'] ?? null),
            'sluice_gate'    => $this->_parse_float($post['sluice_gate'] ?? null),
            'bukaan_pintu'   => $this->_parse_float($post['bukaan_pintu'] ?? null),
            'keterangan'     => $post['keterangan'] ?? null,
        ];
        
        return $this->db->where('id_bendung', $post['id_bendung'])->update('data_bendung', $data) 
            ? $this->_success('Data bendung diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    // ==========================================
    // UPDATE DATA IRIGASI (ADMIN IRIGASI)
    // ==========================================
    public function update_manual_irigasi($post) {
        $data = [
            'nama_aset' => $post['nama_aset'],
            'jenis_daerah_irigasi' => $post['jenis_daerah_irigasi'] ?? null,
            'kode_identifikasi' => $post['kode_identifikasi'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'daerah_aliran_sungai' => $post['daerah_aliran_sungai'] ?? null,
            'kewenangan' => $post['kewenangan'] ?? null,
            'status_pemeliharaan' => $post['status_pemeliharaan'] ?? null,
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'luas_potensial' => $this->_parse_float($post['luas_potensial'] ?? null),
            'luas_fungsional' => $this->_parse_float($post['luas_fungsional'] ?? null),
            'keterangan_tambahan' => $post['keterangan_tambahan'] ?? null
        ];
        
        return $this->db->where('id_irigasi', $post['id_manual'])->update('data_irigasi', $data) 
            ? $this->_success('Data irigasi diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    // ==========================================
    // UPDATE DATA PANTAI (ADMIN PANTAI)
    // ==========================================
    public function update_manual_pantai($post) {
        $data = [
            'nama_aset' => $post['nama_aset'],
            'jenis_bangunan' => $post['jenis_bangunan'] ?? null,
            'sungai' => $post['sungai'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'panjang' => $this->_parse_float($post['panjang'] ?? null),
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'keterangan' => $post['keterangan'] ?? null
        ];
        
        return $this->db->where('id_pengaman', $post['id_manual'])->update('data_pengaman_pantai', $data) 
            ? $this->_success('Data pengaman pantai diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    // ==========================================
    // UPDATE DATA SEDIMEN (ADMIN SEDIMEN)
    // ==========================================
    public function update_manual_sedimen($post) {
        $data = [
            'nama_aset' => $post['nama_aset'],
            'jenis_bangunan' => $post['jenis_bangunan'] ?? null,
            'sungai' => $post['sungai'] ?? null,
            'daerah_aliran_sungai' => $post['daerah_aliran_sungai'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'daya_tampung' => $this->_parse_float($post['daya_tampung'] ?? null),
            'panjang' => $this->_parse_float($post['panjang'] ?? null),
            'tinggi' => $this->_parse_float($post['tinggi'] ?? null),
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'keterangan' => $post['keterangan'] ?? null
        ];
        
        return $this->db->where('id_sedimen', $post['id_manual'])->update('data_pengendali_sedimen', $data) 
            ? $this->_success('Data pengendali sedimen diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    // ==========================================
    // UPDATE DATA EMBUNG (ADMIN EMBUNG)
    // ==========================================
    public function update_manual_embung($post) {
        $data = [
            'kapasitas_volume' => $this->_parse_float($post['kapasitas_volume'] ?? null),
            'elevasi_puncak' => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'tinggi_embung' => $this->_parse_float($post['tinggi_embung'] ?? null),
            'panjang_tubuh' => $this->_parse_float($post['panjang_tubuh'] ?? null),
            'tahun_mulai_pembangunan' => $post['tahun_mulai_pembangunan'] ?? null
        ];
        
        return $this->db->where('id_embung', $post['id_manual'])->update('data_embung', $data) 
            ? $this->_success('Data embung diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    // ==========================================
    // DELETE DATA
    // ==========================================
    public function delete_manual_pos($id, $allowed) {
        $row = $this->db->get_where('data_manual', ['id_manual' => $id])->row();
        if (!$row) {
            return $this->_error('Data tidak ditemukan.');
        }
        if ($allowed !== null && !in_array($row->id_pos, $allowed)) {
            show_error('Akses Ditolak!', 403);
        }
        
        return $this->db->where('id_manual', $id)->delete('data_manual') 
            ? $this->_success('Data dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    public function delete_manual_bendungan($id) {
        return $this->db->where('id_bendungan', $id)->delete('data_bendungan') 
            ? $this->_success('Data bendungan dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    public function delete_manual_bendung($id) {
        return $this->db->where('id_bendung', $id)->delete('data_bendung') 
            ? $this->_success('Data bendung dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    public function delete_manual_irigasi($id) {
        return $this->db->where('id_irigasi', $id)->delete('data_irigasi') 
            ? $this->_success('Data irigasi dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    public function delete_manual_pantai($id) {
        return $this->db->where('id_pengaman', $id)->delete('data_pengaman_pantai') 
            ? $this->_success('Data pengaman pantai dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    public function delete_manual_sedimen($id) {
        return $this->db->where('id_sedimen', $id)->delete('data_pengendali_sedimen') 
            ? $this->_success('Data pengendali sedimen dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    public function delete_manual_embung($id) {
        return $this->db->where('id_embung', $id)->delete('data_embung') 
            ? $this->_success('Data embung dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    // ==========================================
    // GET SINGLE DATA BY ID
    // ==========================================
    public function get_manual_by_id($id_manual) {
        $this->db->select('
            m.*,
            u.nama_lengkap as nama_user,
            p.nama_pos,
            p.tipe_pos
        ');
        $this->db->from('data_manual m');
        $this->db->join('users u', 'm.id_user = u.id_user', 'left');
        $this->db->join('master_pos p', 'm.id_pos = p.id_pos', 'left');
        $this->db->where('m.id_manual', $id_manual);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_bendungan_by_id($id_bendungan) {
        $this->db->select('
            d.*,
            u.nama_lengkap as nama_user,
            p.nama_pos,
            p.tipe_pos
        ');
        $this->db->from('data_bendungan d');
        $this->db->join('users u', 'd.id_user = u.id_user', 'left');
        $this->db->join('master_pos p', 'd.id_pos = p.id_pos', 'left');
        $this->db->where('d.id_bendungan', $id_bendungan);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_bendung_by_id($id_bendung) {
        $this->db->select('
            b.*,
            u.nama_lengkap as nama_user,
            p.nama_pos,
            p.tipe_pos
        ');
        $this->db->from('data_bendung b');
        $this->db->join('users u', 'b.id_user = u.id_user', 'left');
        $this->db->join('master_pos p', 'b.id_pos = p.id_pos', 'left');
        $this->db->where('b.id_bendung', $id_bendung);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_irigasi_by_id($id_irigasi) {
        return $this->db->get_where('data_irigasi', ['id_irigasi' => $id_irigasi])->row();
    }

    public function get_pantai_by_id($id_pengaman) {
        return $this->db->get_where('data_pengaman_pantai', ['id_pengaman' => $id_pengaman])->row();
    }

    public function get_sedimen_by_id($id_sedimen) {
        return $this->db->get_where('data_pengendali_sedimen', ['id_sedimen' => $id_sedimen])->row();
    }

    public function get_embung_by_id($id_embung) {
        $this->db->select('e.*, p.nama_pos');
        $this->db->from('data_embung e');
        $this->db->join('master_pos p', 'e.id_pos = p.id_pos', 'left');
        $this->db->where('e.id_embung', $id_embung);
        return $this->db->get()->row();
    }

    // ==========================================
    // GET BENDUNGAN DATA BY DATE (UNTUK PETUGAS)
    // ==========================================
    public function get_bendungan_by_date($id_pos, $tanggal) {
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
        $this->db->where('DATE(d.tanggal_input)', $tanggal);
        $this->db->order_by('d.created_at', 'DESC');
        return $this->db->get()->result();
    }

    // ==========================================
    // EXPORT & IMPORT DATA
    // ==========================================
    public function get_export_data($module, $allowed_pos, $id_pos = null, $period = 'all', $date = null) {
        if (!empty($id_pos)) {
            $this->db->where('id_pos', $id_pos);
        } else if (!empty($allowed_pos)) {
            $this->db->where_in('id_pos', $allowed_pos);
        } else {
            return [];
        }
        
        if ($period == 'daily' && !empty($date)) {
            $this->db->where('DATE(tanggal_input)', $date);
        } else if ($period == 'month' && !empty($date)) {
            $this->db->where("DATE_FORMAT(tanggal_input, '%Y-%m')", substr($date, 0, 7));
        }
        
        switch ($module) {
            case 'telemetri':
                return $this->db->get('data_telemetri')->result_array();
            case 'manual':
                return $this->db->get('data_manual')->result_array();
            case 'bendung':
                return $this->db->get('data_bendung')->result_array();
            case 'bendungan':
                return $this->db->get('data_bendungan')->result_array();
            default:
                return [];
        }
    }

    public function get_all_export_data($allowed_pos, $id_pos = null, $period = 'all', $date = null) {
        $result = [];
        $modules = ['telemetri', 'manual', 'bendung', 'bendungan'];
        
        foreach ($modules as $module) {
            $data = $this->get_export_data($module, $allowed_pos, $id_pos, $period, $date);
            if (!empty($data)) {
                $result[$module] = $data;
            }
        }
        
        return $result;
    }

    public function get_template_headers($module) {
        switch ($module) {
            case 'telemetri':
                return ['id_pos', 'received_at', 'bat_lvl', 'bat_volt', 'rain', 'wlevel', 'created_at'];
            case 'manual':
                return ['id_pos', 'id_user', 'tanggal_input', 'rain', 'wlevel', 'keterangan'];
            case 'bendung':
                return ['id_pos', 'id_user', 'tanggal_input', 'rain', 'elevasi_mercu', 'q_total', 'q_fc1', 'q_fc2', 'q_sal_induk', 'q_limpas', 'q_sungai', 'q_spam_kpbu', 'sluice_gate', 'bukaan_pintu', 'keterangan'];
            case 'bendungan':
                return ['id_pos', 'id_user', 'tanggal_input', 'nwl', 'nwl_volume', 'nwl_luas', 'rain', 'elevasi', 'volume', 'luas', 'inflow', 'pltm', 'spillway', 'total_outflow', 'plta_status', 'irigasi_status', 'tail_water', 'rembesan_vnotch_h', 'rembesan_vnotch_q', 'rembesan_pump_pit_l_h', 'rembesan_pump_pit_l_q', 'rembesan_pump_pit_r_h', 'rembesan_pump_pit_r_q', 'keterangan', 'tahun_mulai_pembangunan', 'tipe_bendungan', 'elevasi_mercu', 'luas_das'];
            default:
                return ['id_pos', 'tanggal_input', 'data1', 'data2', 'keterangan'];
        }
    }

    public function import_csv_data($module, $data, $user_id) {
        foreach ($data as &$val) {
            if ($val === '' || $val === 'NULL' || $val === 'null') {
                $val = null;
            }
            if (is_numeric($val)) {
                $val = (float)$val;
            }
        }
        
        $data['id_user'] = $user_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        switch ($module) {
            case 'telemetri':
                return $this->db->insert('data_telemetri', $data);
            case 'manual':
                return $this->db->insert('data_manual', $data);
            case 'bendung':
                return $this->db->insert('data_bendung', $data);
            case 'bendungan':
                return $this->db->insert('data_bendungan', $data);
            default:
                return false;
        }
    }

    public function bulk_import_csv($module, $rows, $user_id, $id_pos) {
        $imported = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($rows as $row) {
            $row['id_pos'] = $id_pos;
            
            if (empty($row['tanggal_input'])) {
                $failed++;
                $errors[] = 'Missing tanggal_input';
                continue;
            }
            
            $result = $this->import_csv_data($module, $row, $user_id);
            if ($result) {
                $imported++;
            } else {
                $failed++;
                $errors[] = 'Gagal import: ' . json_encode($row);
            }
        }
        
        return [
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors
        ];
    }

    public function get_admin_pos_list($allowed_pos) {
        if (empty($allowed_pos)) {
            return [];
        }
        
        $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos, is_bendungan, is_bendung');
        $this->db->where_in('id_pos', $allowed_pos);
        $this->db->order_by('nama_pos', 'ASC');
        return $this->db->get('master_pos')->result();
    }

    public function get_export_modules() {
        return [
            'telemetri'   => 'Data Telemetri',
            'manual'      => 'Data Manual Pos',
            'bendung'     => 'Data Bendung',
            'bendungan'   => 'Data Bendungan',
            'all'         => 'Semua Data'
        ];
    }

    public function get_export_periods() {
        return [
            'all'   => 'Semua',
            'daily' => 'Harian',
            'month' => 'Bulanan'
        ];
    }

    // ==========================================
    // DATA IRIGASI (CRUD LENGKAP UNTUK MENU TERPISAH)
    // ==========================================
    public function get_irigasi_data() {
        $data_list = $this->db->order_by('nama_aset', 'ASC')->get('data_irigasi')->result();
        
        return [
            'app_name' => 'HydroSmart',
            'title' => 'Kelola Data Irigasi',
            'data_list' => $data_list
        ];
    }

    public function insert_irigasi($post) {
        $data = [
            'kode_integrasi' => $post['kode_integrasi'] ?? null,
            'nama_aset' => $post['nama_aset'],
            'jenis_daerah_irigasi' => $post['jenis_daerah_irigasi'] ?? null,
            'kode_identifikasi' => $post['kode_identifikasi'] ?? null,
            'status_sumber_data' => $post['status_sumber_data'] ?? null,
            'unit_kerja' => $post['unit_kerja'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'daerah_aliran_sungai' => $post['daerah_aliran_sungai'] ?? null,
            'kewenangan' => $post['kewenangan'] ?? null,
            'lintas_kewenangan' => $post['lintas_kewenangan'] ?? null,
            'tahun_data' => $post['tahun_data'] ?? null,
            'bangunan_pengambilan' => $post['bangunan_pengambilan'] ?? null,
            'status_pemeliharaan' => $post['status_pemeliharaan'] ?? null,
            'di_op_kan_oleh' => $post['di_op_kan_oleh'] ?? null,
            'deskripsi_aset' => $post['deskripsi_aset'] ?? null,
            'keterangan_tambahan' => $post['keterangan_tambahan'] ?? null,
            'status_data' => $post['status_data'] ?? null,
            'status_verifikasi' => $post['status_verifikasi'] ?? null,
            'provinsi' => $post['provinsi'] ?? null,
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'kelurahan' => $post['kelurahan'] ?? null,
            'latitude' => $this->_parse_float($post['latitude'] ?? null),
            'longitude' => $this->_parse_float($post['longitude'] ?? null),
            'keterangan_lokasi' => $post['keterangan_lokasi'] ?? null,
            'luas_permen' => $this->_parse_float($post['luas_permen'] ?? null),
            'luas_baku' => $this->_parse_float($post['luas_baku'] ?? null),
            'luas_potensial' => $this->_parse_float($post['luas_potensial'] ?? null),
            'luas_fungsional' => $this->_parse_float($post['luas_fungsional'] ?? null),
            'jenis_bangunan_utama' => $post['jenis_bangunan_utama'] ?? null,
            'nama_bangunan_utama_bendungan' => $post['nama_bangunan_utama_bendungan'] ?? null,
            'nama_bangunan_utama_bendung' => $post['nama_bangunan_utama_bendung'] ?? null,
            'nama_bangunan_utama_free_intake' => $post['nama_bangunan_utama_free_intake'] ?? null,
            'sumber_air' => $post['sumber_air'] ?? null,
            'luas_tangkapan_hujan' => $this->_parse_float($post['luas_tangkapan_hujan'] ?? null),
            'jenis_rawa' => $post['jenis_rawa'] ?? null,
            'fungsi_jaringan_irigasi' => $post['fungsi_jaringan_irigasi'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_irigasi', $data) 
            ? $this->_success('Data irigasi berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    public function update_irigasi($post) {
        $data = [
            'kode_integrasi' => $post['kode_integrasi'] ?? null,
            'nama_aset' => $post['nama_aset'],
            'jenis_daerah_irigasi' => $post['jenis_daerah_irigasi'] ?? null,
            'kode_identifikasi' => $post['kode_identifikasi'] ?? null,
            'status_sumber_data' => $post['status_sumber_data'] ?? null,
            'unit_kerja' => $post['unit_kerja'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'daerah_aliran_sungai' => $post['daerah_aliran_sungai'] ?? null,
            'kewenangan' => $post['kewenangan'] ?? null,
            'lintas_kewenangan' => $post['lintas_kewenangan'] ?? null,
            'tahun_data' => $post['tahun_data'] ?? null,
            'bangunan_pengambilan' => $post['bangunan_pengambilan'] ?? null,
            'status_pemeliharaan' => $post['status_pemeliharaan'] ?? null,
            'di_op_kan_oleh' => $post['di_op_kan_oleh'] ?? null,
            'deskripsi_aset' => $post['deskripsi_aset'] ?? null,
            'keterangan_tambahan' => $post['keterangan_tambahan'] ?? null,
            'status_data' => $post['status_data'] ?? null,
            'status_verifikasi' => $post['status_verifikasi'] ?? null,
            'provinsi' => $post['provinsi'] ?? null,
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'kelurahan' => $post['kelurahan'] ?? null,
            'latitude' => $this->_parse_float($post['latitude'] ?? null),
            'longitude' => $this->_parse_float($post['longitude'] ?? null),
            'keterangan_lokasi' => $post['keterangan_lokasi'] ?? null,
            'luas_permen' => $this->_parse_float($post['luas_permen'] ?? null),
            'luas_baku' => $this->_parse_float($post['luas_baku'] ?? null),
            'luas_potensial' => $this->_parse_float($post['luas_potensial'] ?? null),
            'luas_fungsional' => $this->_parse_float($post['luas_fungsional'] ?? null),
            'jenis_bangunan_utama' => $post['jenis_bangunan_utama'] ?? null,
            'nama_bangunan_utama_bendungan' => $post['nama_bangunan_utama_bendungan'] ?? null,
            'nama_bangunan_utama_bendung' => $post['nama_bangunan_utama_bendung'] ?? null,
            'nama_bangunan_utama_free_intake' => $post['nama_bangunan_utama_free_intake'] ?? null,
            'sumber_air' => $post['sumber_air'] ?? null,
            'luas_tangkapan_hujan' => $this->_parse_float($post['luas_tangkapan_hujan'] ?? null),
            'jenis_rawa' => $post['jenis_rawa'] ?? null,
            'fungsi_jaringan_irigasi' => $post['fungsi_jaringan_irigasi'] ?? null
        ];
        
        return $this->db->where('id_irigasi', $post['id_irigasi'])->update('data_irigasi', $data) 
            ? $this->_success('Data irigasi berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui data.');
    }

    public function delete_irigasi($id) {
        return $this->db->where('id_irigasi', $id)->delete('data_irigasi') 
            ? $this->_success('Data irigasi berhasil dihapus!') 
            : $this->_error('Gagal menghapus data.');
    }

    // ==========================================
    // DATA PENGENDALI SEDIMEN
    // ==========================================
    public function get_sedimen_data() {
        $data_list = $this->db->order_by('nama_aset', 'ASC')->get('data_pengendali_sedimen')->result();
        
        return [
            'app_name' => 'HydroSmart',
            'title' => 'Kelola Data Pengendali Sedimen',
            'data_list' => $data_list
        ];
    }

    public function insert_sedimen($post) {
        $data = [
            'kode_integrasi' => $post['kode_integrasi'] ?? null,
            'nama_aset' => $post['nama_aset'],
            'jenis_bangunan' => $post['jenis_bangunan'] ?? null,
            'sungai' => $post['sungai'] ?? null,
            'daerah_aliran_sungai' => $post['daerah_aliran_sungai'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'lat' => $this->_parse_float($post['lat'] ?? null),
            'lng' => $this->_parse_float($post['lng'] ?? null),
            'daya_tampung' => $this->_parse_float($post['daya_tampung'] ?? null),
            'panjang' => $this->_parse_float($post['panjang'] ?? null),
            'lebar' => $this->_parse_float($post['lebar'] ?? null),
            'tinggi' => $this->_parse_float($post['tinggi'] ?? null),
            'tahun_dibangun' => $post['tahun_dibangun'] ?? null,
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'kelurahan' => $post['kelurahan'] ?? null,
            'jenis_material' => $post['jenis_material'] ?? null,
            'keterangan' => $post['keterangan'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_pengendali_sedimen', $data) 
            ? $this->_success('Data pengendali sedimen berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    public function update_sedimen($post) {
        $data = [
            'kode_integrasi' => $post['kode_integrasi'] ?? null,
            'nama_aset' => $post['nama_aset'],
            'jenis_bangunan' => $post['jenis_bangunan'] ?? null,
            'sungai' => $post['sungai'] ?? null,
            'daerah_aliran_sungai' => $post['daerah_aliran_sungai'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'lat' => $this->_parse_float($post['lat'] ?? null),
            'lng' => $this->_parse_float($post['lng'] ?? null),
            'daya_tampung' => $this->_parse_float($post['daya_tampung'] ?? null),
            'panjang' => $this->_parse_float($post['panjang'] ?? null),
            'lebar' => $this->_parse_float($post['lebar'] ?? null),
            'tinggi' => $this->_parse_float($post['tinggi'] ?? null),
            'tahun_dibangun' => $post['tahun_dibangun'] ?? null,
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'kelurahan' => $post['kelurahan'] ?? null,
            'jenis_material' => $post['jenis_material'] ?? null,
            'keterangan' => $post['keterangan'] ?? null
        ];
        
        return $this->db->where('id_sedimen', $post['id_sedimen'])->update('data_pengendali_sedimen', $data) 
            ? $this->_success('Data pengendali sedimen berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui data.');
    }

    public function delete_sedimen($id) {
        return $this->db->where('id_sedimen', $id)->delete('data_pengendali_sedimen') 
            ? $this->_success('Data pengendali sedimen berhasil dihapus!') 
            : $this->_error('Gagal menghapus data.');
    }

    // ==========================================
    // DATA PENGAMAN PANTAI
    // ==========================================
    public function get_pantai_data() {
        $data_list = $this->db->order_by('nama_aset', 'ASC')->get('data_pengaman_pantai')->result();
        
        return [
            'app_name' => 'HydroSmart',
            'title' => 'Kelola Data Pengaman Pantai',
            'data_list' => $data_list
        ];
    }

    public function insert_pantai($post) {
        $data = [
            'kode_integrasi' => $post['kode_integrasi'] ?? null,
            'nama_aset' => $post['nama_aset'],
            'jenis_bangunan' => $post['jenis_bangunan'] ?? null,
            'sungai' => $post['sungai'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'lat_awal' => $this->_parse_float($post['lat_awal'] ?? null),
            'lng_awal' => $this->_parse_float($post['lng_awal'] ?? null),
            'lat_akhir' => $this->_parse_float($post['lat_akhir'] ?? null),
            'lng_akhir' => $this->_parse_float($post['lng_akhir'] ?? null),
            'panjang' => $this->_parse_float($post['panjang'] ?? null),
            'elevasi_puncak' => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'lebar_puncak' => $this->_parse_float($post['lebar_puncak'] ?? null),
            'tahun_dibangun' => $post['tahun_dibangun'] ?? null,
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'kelurahan' => $post['kelurahan'] ?? null,
            'manfaat' => $post['manfaat'] ?? null,
            'keterangan' => $post['keterangan'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_pengaman_pantai', $data) 
            ? $this->_success('Data pengaman pantai berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    public function update_pantai($post) {
        $data = [
            'kode_integrasi' => $post['kode_integrasi'] ?? null,
            'nama_aset' => $post['nama_aset'],
            'jenis_bangunan' => $post['jenis_bangunan'] ?? null,
            'sungai' => $post['sungai'] ?? null,
            'wilayah_sungai' => $post['wilayah_sungai'] ?? null,
            'lat_awal' => $this->_parse_float($post['lat_awal'] ?? null),
            'lng_awal' => $this->_parse_float($post['lng_awal'] ?? null),
            'lat_akhir' => $this->_parse_float($post['lat_akhir'] ?? null),
            'lng_akhir' => $this->_parse_float($post['lng_akhir'] ?? null),
            'panjang' => $this->_parse_float($post['panjang'] ?? null),
            'elevasi_puncak' => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'lebar_puncak' => $this->_parse_float($post['lebar_puncak'] ?? null),
            'tahun_dibangun' => $post['tahun_dibangun'] ?? null,
            'kabupaten_kota' => $post['kabupaten_kota'] ?? null,
            'kecamatan' => $post['kecamatan'] ?? null,
            'kelurahan' => $post['kelurahan'] ?? null,
            'manfaat' => $post['manfaat'] ?? null,
            'keterangan' => $post['keterangan'] ?? null
        ];
        
        return $this->db->where('id_pengaman', $post['id_pengaman'])->update('data_pengaman_pantai', $data) 
            ? $this->_success('Data pengaman pantai berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui data.');
    }

    public function delete_pantai($id) {
        return $this->db->where('id_pengaman', $id)->delete('data_pengaman_pantai') 
            ? $this->_success('Data pengaman pantai berhasil dihapus!') 
            : $this->_error('Gagal menghapus data.');
    }

    // ==========================================
    // DATA EMBUNG
    // ==========================================
    public function get_embung_data() {
        $this->db->select('e.*, p.nama_pos, p.tipe_pos, p.wilayah_sungai');
        $this->db->from('data_embung e');
        $this->db->join('master_pos p', 'e.id_pos = p.id_pos', 'left');
        $this->db->order_by('e.created_at', 'DESC');
        $data_list = $this->db->get()->result();
        
        return [
            'app_name' => 'HydroSmart',
            'title' => 'Kelola Data Embung',
            'data_list' => $data_list
        ];
    }

    public function insert_embung($post) {
        $data = [
            'id_pos' => $post['id_pos'],
            'kapasitas_volume' => $this->_parse_float($post['kapasitas_volume'] ?? null),
            'elevasi_puncak' => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'tinggi_embung' => $this->_parse_float($post['tinggi_embung'] ?? null),
            'panjang_tubuh' => $this->_parse_float($post['panjang_tubuh'] ?? null),
            'tahun_mulai_pembangunan' => $post['tahun_mulai_pembangunan'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('data_embung', $data) 
            ? $this->_success('Data embung berhasil ditambahkan!') 
            : $this->_error('Gagal menambahkan data.');
    }

    public function update_embung($post) {
        $data = [
            'id_pos' => $post['id_pos'],
            'kapasitas_volume' => $this->_parse_float($post['kapasitas_volume'] ?? null),
            'elevasi_puncak' => $this->_parse_float($post['elevasi_puncak'] ?? null),
            'tinggi_embung' => $this->_parse_float($post['tinggi_embung'] ?? null),
            'panjang_tubuh' => $this->_parse_float($post['panjang_tubuh'] ?? null),
            'tahun_mulai_pembangunan' => $post['tahun_mulai_pembangunan'] ?? null
        ];
        
        return $this->db->where('id_embung', $post['id_embung'])->update('data_embung', $data) 
            ? $this->_success('Data embung berhasil diperbarui!') 
            : $this->_error('Gagal memperbarui data.');
    }

    public function delete_embung($id) {
        return $this->db->where('id_embung', $id)->delete('data_embung') 
            ? $this->_success('Data embung berhasil dihapus!') 
            : $this->_error('Gagal menghapus data.');
    }
}