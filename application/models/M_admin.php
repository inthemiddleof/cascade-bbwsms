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
    // KELOLA MANUAL DATA (DENGAN BENDUNG)
    // ==========================================
    public function get_manual_data($pos_filter, $bulan, $allowed_pos) {
        // Ambil daftar pos
        $this->db->select('id_pos, nama_pos, tipe_pos, is_bendungan, is_bendung');
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $pos_list = $this->db->order_by('nama_pos', 'ASC')->get('master_pos')->result();

        // Tentukan pos yang dipilih
        $selected_id = (!empty($pos_filter) && ($allowed_pos === null || in_array($pos_filter, $allowed_pos))) 
            ? $pos_filter 
            : ($pos_list[0]->id_pos ?? null);
            
        $pos = null;
        if ($selected_id) {
            $pos = $this->db->where('id_pos', $selected_id)->get('master_pos')->row();
        }
        
        $data_list = [];

        if ($pos) {
            // CEK TIPE POS: Bendung > Bendungan > Pos Biasa
            if ($pos->is_bendung == 1) {
                // QUERY BENDUNG
                $data_list = $this->get_bendung_data_by_pos($selected_id, $bulan);
            } elseif ($pos->is_bendungan == 1) {
                // QUERY BENDUNGAN
                $data_list = $this->get_bendungan_data_by_pos($selected_id, $bulan);
            } else {
                // QUERY POS BIASA
                $data_list = $this->get_manual_data_by_pos($selected_id, $bulan);
            }
        }

        return [
            'app_name'  => 'HydroSmart', 
            'title'     => 'Kelola Laporan Manual', 
            'pos_list'  => $pos_list, 
            'pos'       => $pos, 
            'bulan'     => $bulan, 
            'data_list' => $data_list
        ];
    }

    /**
     * Get data manual pos biasa (PCH/PDA) berdasarkan pos dan bulan
     */
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
            u.nama_lengkap as nama_petugas
        ');
        $this->db->from('data_manual m');
        $this->db->join('users u', 'm.id_user = u.id_user', 'left');
        $this->db->where('m.id_pos', $id_pos);
        $this->db->where("DATE_FORMAT(m.tanggal_input, '%Y-%m') =", $bulan);
        $this->db->order_by('m.tanggal_input', 'DESC');
        $this->db->order_by('m.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get data bendungan berdasarkan pos dan bulan (DENGAN KOLOM BARU)
     */
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

    /**
     * Get data bendung berdasarkan pos dan bulan (SESUAI STRUKTUR TERBARU)
     */
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
        $this->db->where("DATE_FORMAT(b.tanggal_input, '%Y-%m') =", $bulan);
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
            // KOLOM BARU
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

    /**
     * Insert data bendung (SESUAI STRUKTUR TERBARU)
     */
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
            // KOLOM BARU
            'tahun_mulai_pembangunan' => !empty($post['tahun_mulai_pembangunan']) ? $post['tahun_mulai_pembangunan'] : null,
            'tipe_bendungan'          => $post['tipe_bendungan'] ?? null,
            'elevasi_mercu'           => $this->_parse_float($post['elevasi_mercu'] ?? null),
            'luas_das'                => $this->_parse_float($post['luas_das'] ?? null),
        ];
        
        return $this->db->where('id_bendungan', $post['id_bendungan'])->update('data_bendungan', $data) 
            ? $this->_success('Data bendungan diperbarui!') 
            : $this->_error('Gagal memperbarui.');
    }

    /**
     * Update data bendung (SESUAI STRUKTUR TERBARU)
     */
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

    /**
     * Delete data bendung (SESUAI STRUKTUR TERBARU)
     */
    public function delete_manual_bendung($id) {
        return $this->db->where('id_bendung', $id)->delete('data_bendung') 
            ? $this->_success('Data bendung dihapus.') 
            : $this->_error('Gagal menghapus.');
    }

    // ==========================================
    // GET SINGLE DATA BY ID (UNTUK EDIT)
    // ==========================================
    
    /**
     * Get single bendung data by ID (SESUAI STRUKTUR TERBARU)
     */
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

    /**
     * Get single bendungan data by ID (DENGAN KOLOM BARU)
     */
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

    /**
     * Get single pos manual data by ID
     */
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

    // ==========================================
    // GET BENDUNGAN DATA BY POS (UNTUK PETUGAS)
    // ==========================================
    
    /**
     * Get data bendungan by date (untuk petugas)
     */
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
}