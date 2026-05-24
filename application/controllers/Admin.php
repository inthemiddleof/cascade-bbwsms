<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
        // REVISI: Menggunakan M_admin untuk pengolahan data dashboard & petugas lapangan
        $this->load->model('M_admin'); 
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Jakarta');
        
        // Proteksi Lapis Keamanan: Hanya izinkan superadmin dan admin kedua
        if (!$this->session->userdata('logged_in')) redirect('auth');
        
        $role = $this->session->userdata('role');
        if ($role !== 'superadmin' && $role !== 'admin' && $this->session->userdata('username') !== 'superadmin') {
            show_error('Akses Ditolak: Anda tidak memiliki otoritas di modul ini.', 403);
        }
    }

    /**
     * Helper internal untuk memilah hak cakupan wilayah kerja (Scope Pos)
     */
    private function _get_allowed_pos_ids() {
        $role = $this->session->userdata('role');
        // REVISI fallback session: mengantisipasi perbedaan penamaan user_id / id_user saat login
        $id_user = $this->session->userdata('user_id') ? $this->session->userdata('user_id') : $this->session->userdata('id_user');
        
        // Jika Super Admin, kembalikan null (artinya: bypass / open all)
        if ($role === 'superadmin' || $this->session->userdata('username') === 'superadmin') {
            return null; 
        }
        
        // Jika Admin Kedua, ambil daftar id_pos yang didelegasikan (menggunakan model M_admin)
        $user_data = $this->db->select('id_pos')->where('id_user', $id_user)->get('users')->row();
        
        if (!empty($user_data) && !empty($user_data->id_pos)) {
            return array_map('trim', explode(',', $user_data->id_pos));
        }
        
        // Jika belum didelegasikan sama sekali, berikan array berisi 0 agar query SQL aman & kosong
        return [0];
    }

    // ============================================
    // DASHBOARD (AUTOMATIC ISOLATION FILTER)
    // ============================================
    public function index() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        $role = $this->session->userdata('role');
        
        // Modifikasi Query Statistik Berdasarkan Hak Akses Wilayah Kerja Admin
        if ($allowed_pos !== null) {
            
            // FIX PERBAIKAN DI SINI:
            // Saring murni berdasarkan ID Pos yang diizinkan (allowed_pos) menggunakan where_in, 
            // bukan mengandalkan murni admin_id yang rentan tidak sinkron di database.
            $this->db->from('master_pos');
            $this->db->where_in('id_pos', $allowed_pos);
            $pos_tanggung_jawab = $this->db->get()->result();

            // Statistik Khusus Terfilter untuk Admin Kedua
            $this->db->where_in('id_pos', $allowed_pos);
            $total_pos = $this->db->count_all_results('master_pos');

            $this->db->where_in('id_pos', $allowed_pos)->where('tipe_pos', 'PCH');
            $total_pch = $this->db->count_all_results('master_pos');

            $this->db->where_in('id_pos', $allowed_pos)->where('tipe_pos', 'PDA');
            $total_pda = $this->db->count_all_results('master_pos');

            // Hitung petugas lapangan yang memegang pos di wilayah admin ini
            $total_petugas = 0;
            $petugas_aktif = 0;
            $pos_list = [];

            // Ambil semua petugas lapangan untuk difilter secara programmatik (karena id_pos di tabel users berupa TEXT)
            $all_petugas = $this->db->where('role', 'petugas')->get('users')->result();
            $filtered_petugas_ids = [0]; // default fallback

            foreach ($all_petugas as $p) {
                $p_pos_ids = array_map('trim', explode(',', $p->id_pos));
                // Cek apakah ada pos petugas yang beririsan dengan pos admin kedua
                if (array_intersect($p_pos_ids, $allowed_pos)) {
                    $total_petugas++;
                    if ($p->status === 'aktif') {
                        $petugas_aktif++;
                    }
                    $filtered_petugas_ids[] = $p->id_user;
                }
            }

            $total_data_hari_ini = 0;
            $pos_online = 0;
            if (!empty($allowed_pos) && !in_array(0, $allowed_pos)) {
                $this->db->where_in('id_pos', $allowed_pos)->where('DATE(received_at)', date('Y-m-d'));
                $total_data_hari_ini = $this->db->count_all_results('data_telemetri');

                $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
                $this->db->distinct()->select('id_pos')->where_in('id_pos', $allowed_pos)->where('received_at >=', $one_hour_ago);
                $pos_online = $this->db->get('data_telemetri')->num_rows();
            }

            // List ringkasan pos terfilter untuk admin kedua
            $pos_list = $this->M_admin->get_detailed_pos_list();
            foreach ($pos_list as $key => $pos_item) {
                if (!in_array($pos_item->id_pos, $allowed_pos)) {
                    unset($pos_list[$key]); // Buang pos yang bukan hak aksesnya
                }
            }
            $pos_list = array_values($pos_list); // reset index array

        } else {
            // Terbuka Penuh Seluruh Unit Se-Lampung untuk Super Admin
            $pos_tanggung_jawab   = []; // Superadmin dikontrol via $pos_list penuh di view
            $total_pos           = $this->M_admin->count_all_pos();
            $total_pch           = $this->M_admin->count_pos_by_type('PCH');
            $total_pda           = $this->M_admin->count_pos_by_type('PDA');
            $total_petugas       = $this->M_admin->count_all_petugas();
            $petugas_aktif       = $this->M_admin->count_petugas_by_status('aktif');
            $total_data_hari_ini = $this->M_admin->count_telemetri_today();
            $pos_online          = $this->M_admin->count_pos_online();
            $pos_list            = $this->M_admin->get_detailed_pos_list();
        }

        $data = [
            'app_name'            => 'CASCADE', 
            'title'               => 'Dashboard', 
            'admin_name'          => $this->session->userdata('nama_lengkap'),
            'total_pos'           => $total_pos,
            'total_pch'           => $total_pch,
            'total_pda'           => $total_pda,
            'total_petugas'       => $total_petugas,
            'petugas_aktif'       => $petugas_aktif,
            'total_data_hari_ini' => $total_data_hari_ini,
            'pos_online'          => $pos_online,
            'last_sync'           => $this->M_admin->get_last_sync_time(),
            'pos_list'            => $pos_list,
            'pos_tanggung_jawab'  => $pos_tanggung_jawab, // Dikirim ke view dashboard terfilter aman
        ];

        $this->load->model('M_auth');
        $data['content'] = $this->load->view('admin/v_dashboard', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }

    // ============================================
    // KELOLA PETUGAS (CRUD DENGAN VALIDASI AREA)
    // ============================================
    public function kelola_petugas() {
        $allowed_pos = $this->_get_allowed_pos_ids();
    
        // 1. Ambil semua petugas dari model
        $all_petugas = $this->M_admin->get_all_petugas();
        $petugas_list = [];
    
        if ($allowed_pos !== null) {
            // FILTER UNTUK ADMIN REGIONAL
            foreach ($all_petugas as $p) {
                $raw_user = $this->db->select('id_pos')->where('id_user', $p->id_user)->get('users')->row();
                if(!empty($raw_user) && !empty($raw_user->id_pos)) {
                    $p_pos_ids = array_map('trim', explode(',', $raw_user->id_pos));
                    if (array_intersect($p_pos_ids, $allowed_pos)) {
                        $petugas_list[] = $p;
                    }
                }
            }
    
            $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos')->from('master_pos')->where_in('id_pos', $allowed_pos)->order_by('nama_pos', 'ASC');
            $pos_list = $this->db->get()->result();
        } else {
            // UNTUK SUPERADMIN (Ambil semua tanpa filter regional)
            $petugas_list = $all_petugas;
            
            // FIX: Tambahkan tipe_pos dan nomor_pos agar tidak memicu error di view
            $pos_list = $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos')->from('master_pos')->order_by('nama_pos', 'ASC')->get()->result();
        }
    
        // 2. MAPPING KEMBALI DATA PETUGAS (Antisipasi format id_pos gabungan string "1,2,3")
        // Langkah ini krusial agar properties $p->nomor_pos dan $p->tipe_pos terisi dengan aman di view
        $pos_map = [];
        $all_master_pos = $this->db->select('id_pos, nama_pos, tipe_pos, nomor_pos')->get('master_pos')->result();
        foreach ($all_master_pos as $mp) {
            $pos_map[$mp->id_pos] = $mp;
        }
    
        foreach ($petugas_list as $p) {
            // Ambil data string id_pos dari row user jika ada, jika tidak ada fallback ke property default model
            $id_pos_raw = isset($p->id_pos) ? $p->id_pos : '';
            
            // Jika di model datanya belum berbentuk string id_pos, kita query manual dari tabel users
            if (empty($id_pos_raw)) {
                $raw_u = $this->db->select('id_pos')->where('id_user', $p->id_user)->get('users')->row();
                $id_pos_raw = !empty($raw_u) ? $raw_u->id_pos : '';
            }
    
            $assigned_names   = [];
            $assigned_types   = [];
            $assigned_numbers = [];
    
            if (!empty($id_pos_raw)) {
                $ids = array_map('trim', explode(',', $id_pos_raw));
                foreach ($ids as $id) {
                    if (isset($pos_map[$id])) {
                        $assigned_names[]   = $pos_map[$id]->nama_pos;
                        $assigned_types[]   = $pos_map[$id]->tipe_pos;
                        $assigned_numbers[] = $pos_map[$id]->nomor_pos ?: $pos_map[$id]->id_pos;
                    }
                }
            }
    
            // Menyuntikkan properti virtual ke dalam baris objek data petugas agar dibaca aman oleh view
            $p->nama_pos  = !empty($assigned_names) ? implode(', ', $assigned_names) : 'Belum Ditugaskan';
            $p->tipe_pos  = !empty($assigned_types) ? implode(', ', array_unique($assigned_types)) : '-';
            $p->nomor_pos = !empty($assigned_numbers) ? implode(', ', array_unique($assigned_numbers)) : '-';
        }
    
        $data = [
            'app_name'    => 'CASCADE', 
            'title'       => 'Kelola Petugas', 
            'admin_name'  => $this->session->userdata('nama_lengkap'),
            'petugas_list'=> $petugas_list, 
            'pos_list'    => $pos_list,
        ];
        
        $data['content'] = $this->load->view('admin/v_kelola_petugas', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }
    
    public function tambah_petugas() {
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('id_pos[]', 'Pos', 'required', [
            'required' => 'Minimal harus memilih satu %s cakupan wilayah!'
        ]);
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $id_pos_array = $this->input->post('id_pos', TRUE);
            $allowed_pos = $this->_get_allowed_pos_ids();
            $current_role = $this->session->userdata('role');
    
            if ($allowed_pos !== null && !empty($id_pos_array)) {
                foreach ($id_pos_array as $pos_input_id) {
                    if (!in_array($pos_input_id, $allowed_pos)) {
                        show_error('Akses Terblokir: Anda mencoba menugaskan petugas di luar wilayah pos kontrol Anda!', 403);
                        return;
                    }
                }
            }
    
            $id_pos_string = !empty($id_pos_array) ? implode(',', $id_pos_array) : '';
            $fix_role = ($current_role === 'superadmin' || $this->session->userdata('username') === 'superadmin') ? 'admin' : 'petugas';
    
            $data_user = [
                'username'     => $this->input->post('username', TRUE),
                'password'     => password_hash($this->input->post('password'), PASSWORD_BCRYPT, ['cost' => 12]),
                'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
                'email'        => $this->input->post('email', TRUE),
                'role'         => $fix_role,
                'id_pos'       => $id_pos_string, 
                'status'       => 'aktif',
                'created_at'   => date('Y-m-d H:i:s')
            ];
            
            $insert_status = $this->db->insert('users', $data_user);
    
            if ($insert_status) {
                if ($fix_role === 'admin' && !empty($id_pos_array)) {
                    $user_baru = $this->db->get_where('users', ['username' => $data_user['username']])->row();
                    $new_id_user = (!empty($user_baru)) ? $user_baru->id_user : $this->db->insert_id();
                    $clean_id_pos = array_filter(array_map('intval', $id_pos_array));
    
                    if (!empty($clean_id_pos) && $new_id_user > 0) {
                        $this->db->where_in('id_pos', $clean_id_pos);
                        $this->db->update('master_pos', ['admin_id' => $new_id_user]);
                    }
                }
                $this->session->set_flashdata('success', 'User Baru ('.ucfirst($fix_role).') berhasil ditambahkan!');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan user.');
            }
        }
        redirect('admin/kelola_petugas');
    }

    public function edit_petugas() {
        $id_user = $this->input->post('id_user', TRUE);
        $user = $this->db->get_where('users', ['id_user' => $id_user])->row();
        
        if (!$user) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect('admin/kelola_petugas');
            return;
        }

        $allowed_pos = $this->_get_allowed_pos_ids();
        
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('id_pos[]', 'Pos', 'required', [
            'required' => 'Minimal harus memilih satu %s cakupan wilayah!'
        ]);
        
        if ($this->input->post('username') != $user->username) {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $id_pos_array = $this->input->post('id_pos', TRUE);

            if ($allowed_pos !== null && !empty($id_pos_array)) {
                foreach ($id_pos_array as $pos_input_id) {
                    if (!in_array($pos_input_id, $allowed_pos)) {
                        show_error('Akses Ditolak: Lokasi pemindahan pos di luar jangkauan kendali Anda.', 403);
                        return;
                    }
                }
            }

            $id_pos_string = !empty($id_pos_array) ? implode(',', $id_pos_array) : '';

            $data_update = [
                'username'     => $this->input->post('username', TRUE),
                'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
                'email'        => $this->input->post('email', TRUE),
                'id_pos'       => $id_pos_string, 
            ];
            
            $password = $this->input->post('password');
            if (!empty($password)) {
                $data_update['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            }
            
            $update_status = $this->db->where('id_user', $id_user)->update('users', $data_update);

            if ($update_status) {
                if ($user->role === 'admin') {
                    $this->db->where('admin_id', $id_user);
                    $this->db->update('master_pos', ['admin_id' => NULL]);

                    if (!empty($id_pos_array)) {
                        $clean_id_pos = array_filter(array_map('intval', $id_pos_array));
                        if (!empty($clean_id_pos)) {
                            $this->db->where_in('id_pos', $clean_id_pos);
                            $this->db->update('master_pos', ['admin_id' => $id_user]);
                        }
                    }
                }
                $this->session->set_flashdata('success', 'Data user berhasil diperbarui!');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data user.');
            }
        }
        redirect('admin/kelola_petugas');
    }

    public function hapus_petugas($id_user) {
        $user = $this->db->get_where('users', ['id_user' => $id_user])->row();
        if (!$user) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect('admin/kelola_petugas');
            return;
        }

        $this->db->where('id_user', $id_user)->delete('users')
            ? $this->session->set_flashdata('success', 'Akun berhasil dihapus dari sistem!')
            : $this->session->set_flashdata('error', 'Gagal menghapus akun.');
            
        redirect('admin/kelola_petugas');
    }

    public function nonaktifkan_petugas($id_user) {
        $this->db->where('id_user', $id_user)->update('users', ['status' => 'nonaktif'])
            ? $this->session->set_flashdata('success', 'User berhasil dinonaktifkan.')
            : $this->session->set_flashdata('error', 'Gagal menonaktifkan user.');
        redirect('admin/kelola_petugas');
    }

    public function aktifkan_petugas($id_user) {
        $this->db->where('id_user', $id_user)->update('users', ['status' => 'aktif'])
            ? $this->session->set_flashdata('success', 'User berhasil diaktifkan.')
            : $this->session->set_flashdata('error', 'Gagal mengaktifkan user.');
        redirect('admin/kelola_petugas');
    }

    private function _restrict_to_superadmin() {
        $role = $this->session->userdata('role');
        if ($role !== 'superadmin' && $this->session->userdata('username') !== 'superadmin') {
            show_error('Akses Terbatas: Hanya Super Admin pusat yang diizinkan memanipulasi master infrastruktur Pos/Bendungan.', 403);
        }
    }

    public function kelola_pos() {
        $this->_restrict_to_superadmin();
        
        $this->db->select('*')->from('master_pos');
        $this->db->order_by('nama_pos', 'ASC');
        $pos_list = $this->db->get()->result();

        $data = [
            'app_name'   => 'CASCADE', 
            'title'      => 'Kelola Master Pos Hidrologi', 
            'admin_name' => $this->session->userdata('nama_lengkap'),
            'pos_list'   => $pos_list
        ];
        
        $data['content'] = $this->load->view('admin/v_kelola_pos', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }

    public function tambah_pos() {
        $this->_restrict_to_superadmin();
        
        $this->form_validation->set_rules('nomor_pos', 'Nomor Pos', 'required|trim|is_unique[master_pos.nomor_pos]');
        $this->form_validation->set_rules('nama_pos', 'Nama Pos', 'required|trim');
        $this->form_validation->set_rules('tipe_pos', 'Tipe Pos', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data_pos = [
                'nomor_pos'  => $this->input->post('nomor_pos', TRUE),
                'nama_pos'   => $this->input->post('nama_pos', TRUE),
                'tipe_pos'   => $this->input->post('tipe_pos', TRUE),
                'latitude'   => $this->input->post('latitude') ?: null,
                'longitude'  => $this->input->post('longitude') ?: null,
                'kabupaten'  => $this->input->post('kabupaten', TRUE) ?: null,
                'kecamatan'  => $this->input->post('kecamatan', TRUE) ?: null,
                'desa'       => $this->input->post('desa', TRUE) ?: null,
                'status_pos' => $this->input->post('status_pos') ?: 'aktif',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('master_pos', $data_pos)
                ? $this->session->set_flashdata('success', 'Master Pos Hidrologi baru berhasil didaftarkan!')
                : $this->session->set_flashdata('error', 'Gagal mendaftarkan pos baru.');
        }
        redirect('admin/kelola_pos');
    }

     // ============================================
    // ROUTING INPUT MANUAL OLEH ADMIN (POS UMUM)
    // ============================================
    public function tambah_data_pos() {
        $id_user = $this->session->userdata('user_id') ? $this->session->userdata('user_id') : $this->session->userdata('id_user');
        $user    = $this->db->get_where('users', ['id_user' => $id_user])->row();
    
        if (!$user) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke modul ini.');
            redirect('admin/dashboard');
            return;
        }
    
        // Mengambil id_pos yang menjadi hak akses admin ini (jika admin kedua)
        $allowed_pos = $this->_get_allowed_pos_ids();
    
        // Ambil daftar pos hidrologi (bukan bendungan) sesuai hak regional admin
        $this->db->where('is_bendungan', 0);
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $data['list_pos'] = $this->db->get('master_pos')->result();
    
        // Tentukan pos mana yang sedang aktif dipilih / diinput
        $id_selected = $this->input->get('id_pos');
        if (empty($id_selected)) {
            $id_selected = !empty($data['list_pos']) ? $data['list_pos'][0]->id_pos : 0;
        }
    
        // Proteksi tambahan: Jika admin kedua mencoba tembak URL id_pos di luar haknya
        if ($allowed_pos !== null && !in_array($id_selected, $allowed_pos)) {
            show_error('Akses Terblokir: Anda tidak diizinkan mengakses pos di luar wilayah kendali Anda!', 403);
            return;
        }
        
        $data['pos'] = $this->db->get_where('master_pos', ['id_pos' => $id_selected])->row();
        $data['tanggal']   = $this->input->get('tanggal') ? $this->input->get('tanggal') : date('Y-m-d');
        
        // Ambil riwayat log data manual bulanan stasiun terpilih
        $data['data_list'] = $this->db->where('id_pos', $id_selected)
                                      ->like('tanggal_input', substr($data['tanggal'], 0, 7), 'after')
                                      ->order_by('tanggal_input', 'DESC')
                                      ->get('data_manual')
                                      ->result();
    
        $data['app_name'] = 'CASCADE';
        $data['title']    = 'Input Manual Pos';
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        
        $data['content'] = $this->load->view('admin/v_input_manual_admin', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }

    public function edit_pos() {
        $this->_restrict_to_superadmin();
        
        $id_pos = $this->input->post('id_pos', TRUE);
        
        $this->form_validation->set_rules('nomor_pos', 'Nomor Pos', 'required|trim');
        $this->form_validation->set_rules('nama_pos', 'Nama Pos', 'required|trim');
        $this->form_validation->set_rules('tipe_pos', 'Tipe Pos', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $cek = $this->db->get_where('master_pos', ['nomor_pos' => $this->input->post('nomor_pos'), 'id_pos !=' => $id_pos])->num_rows();
            if ($cek > 0) {
                $this->session->set_flashdata('error', 'Nomor Pos tersebut sudah digunakan oleh stasiun lain!');
                redirect('admin/kelola_pos');
                return;
            }

            $data_update = [
                'nomor_pos'  => $this->input->post('nomor_pos', TRUE),
                'nama_pos'   => $this->input->post('nama_pos', TRUE),
                'tipe_pos'   => $this->input->post('tipe_pos', TRUE),
                'latitude'   => $this->input->post('latitude') ?: null,
                'longitude'  => $this->input->post('longitude') ?: null,
                'kabupaten'  => $this->input->post('kabupaten', TRUE) ?: null,
                'kecamatan'  => $this->input->post('kecamatan', TRUE) ?: null,
                'desa'       => $this->input->post('desa', TRUE) ?: null,
                'status_pos' => $this->input->post('status_pos') ?: 'aktif',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->where('id_pos', $id_pos)->update('master_pos', $data_update)
                ? $this->session->set_flashdata('success', 'Informasi profile master pos berhasil diperbarui!')
                : $this->session->set_flashdata('error', 'Gagal memperbarui data pos.');
        }
        redirect('admin/kelola_pos');
    }

    public function hapus_pos($id_pos) {
        $this->_restrict_to_superadmin();

        $has_manual    = $this->db->where('id_pos', $id_pos)->count_all_results('data_manual') > 0;
        $has_telemetri = $this->db->where('id_pos', $id_pos)->count_all_results('data_telemetri') > 0;
        $has_bendungan = $this->db->where('id_pos', $id_pos)->count_all_results('data_bendungan') > 0;

        if ($has_manual || $has_telemetri || $has_bendungan) {
            $this->session->set_flashdata('error', 'Pos gagal dibersihkan! Stasiun ini masih memiliki keterikatan data log transaksional aktif di database.');
        } else {
            $this->db->where('id_pos', $id_pos)->delete('master_pos')
                ? $this->session->set_flashdata('success', 'Master stasiun pos berhasil dihapus permanen.')
                : $this->session->set_flashdata('error', 'Gagal mengeksekusi perintah hapus database.');
        }
        redirect('admin/kelola_pos');
    }

    

    // ============================================
    // REVISI FINAL: KELOLA DATA BENDUNGAN + INTEGRASI MASTER_POS
    // ============================================
    // ============================================
    // REVISI INTERAKTIF: KELOLA DATA BENDUNGAN (FILTER DROPDOWN)
    // ============================================
    public function kelola_bendungan() {
        $allowed_pos = $this->_get_allowed_pos_ids(); 
        
        if ($allowed_pos === null) {
            $allowed_pos = [];
        }
        
        // 1. Ambil parameter id_pos dari filter Dropdown URL (?pos=...)
        $pos_filter = $this->input->get('pos', TRUE);
        $bulan = $this->input->get('bulan') ?? date('Y-m');
    
        // 2. Ambil daftar pos yang VALID sebagai bendungan (is_bendungan = 1) sesuai hak akses regional
        if (!empty($allowed_pos)) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $this->db->where('is_bendungan', 1); 
        $this->db->order_by('nama_pos', 'ASC');
        $list_bendungan = $this->db->get('master_pos')->result();
    
        // 3. Logika Penentuan ID Bendungan yang Sedang Aktif Dilihat
        $selected_pos_id = null;
        if (!empty($pos_filter)) {
            // Jika ada input filter dari user, pastikan bendungan tersebut ada di dalam hak aksesnya
            if (empty($allowed_pos) || in_array($pos_filter, $allowed_pos)) {
                $selected_pos_id = $pos_filter;
            }
        }
        
        // Fallback: Jika admin pertama kali buka menu (belum pilih apapun), otomatis ke bendungan pertama di list
        if (empty($selected_pos_id) && !empty($list_bendungan)) {
            $selected_pos_id = $list_bendungan[0]->id_pos;
        }
    
        // 4. Ambil detail data profil bendungan terpilih dari master_pos
        $pos = null;
        if (!empty($selected_pos_id)) {
            $pos = $this->db->where('id_pos', $selected_pos_id)->get('master_pos')->row();
        }
    
        // 5. PROTEKSI BACKEND SAFETINESS CHECK
        if (!$pos || $pos->is_bendungan != 1) {
            $this->session->set_flashdata('error', 'Infrastruktur Bendungan tidak ditemukan atau Akun Anda tidak memiliki otoritas regional.');
            redirect('admin');
            exit;
        }
    
        // 6. Ambil log records bulanan terfilter murni hanya untuk bendungan yang dipilih
        $data_list = $this->db->where('id_pos', $selected_pos_id)
                              ->like('tanggal_input', $bulan, 'after')
                              ->order_by('tanggal_input', 'DESC')
                              ->get('data_bendungan')
                              ->result();
    
        // 7. Satukan paket data ke view utama layout
        $data = [
            'app_name'     => 'CASCADE',
            'title'        => 'Kelola Data Bendungan',
            'admin_name'   => $this->session->userdata('nama_lengkap'),
            'pos'          => $pos,               // Data profil bendungan aktif saat ini
            'list_pos'     => $list_bendungan,   // List untuk dirender di dropdown select option
            'data_list'    => $data_list,         // Log data transaksional terfilter
            'bulan'        => $bulan,
            'admin_role'   => $this->session->userdata('role'),
            'current_url'  => 'admin/kelola_bendungan'
        ];
    
        $data['content'] = $this->load->view('admin/v_kelola_bendungan', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }

    public function tambah_bendungan() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        $id_pos_input = $this->input->post('id_pos', TRUE);
    
        // Validasi Lapis Regional: Pastikan admin memasukkan data pos yang benar-benar miliknya
        if ($allowed_pos !== null && !in_array($id_pos_input, $allowed_pos)) {
            show_error('Akses Terblokir: Anda tidak diizinkan menambahkan data di luar pos kendali Anda!', 403);
            return;
        }
        
        $this->form_validation->set_rules('id_pos', 'Nama Stasiun / Pos', 'required');
        $this->form_validation->set_rules('tanggal_input', 'Tanggal Pengukuran', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data_insert = [
                'id_pos'                => $id_pos_input,
                'id_user'               => $this->session->userdata('user_id') ? $this->session->userdata('user_id') : $this->session->userdata('id_user'),
                'id_manual'             => $this->input->post('id_manual') ?: null,
                'tanggal_input'         => $this->input->post('tanggal_input', TRUE),
                'nwl'                   => $this->input->post('nwl') !== '' ? $this->input->post('nwl') : null,
                'nwl_volume'            => $this->input->post('nwl_volume') !== '' ? $this->input->post('nwl_volume') : null,
                'nwl_luas'              => $this->input->post('nwl_luas') !== '' ? $this->input->post('nwl_luas') : null,
                'elevasi'               => $this->input->post('elevasi') !== '' ? $this->input->post('elevasi') : null,
                'volume'                => $this->input->post('volume') !== '' ? $this->input->post('volume') : null,
                'luas'                  => $this->input->post('luas') !== '' ? $this->input->post('luas') : null,
                'inflow'                => $this->input->post('inflow') !== '' ? $this->input->post('inflow') : null,
                'pltm'                  => $this->input->post('pltm') !== '' ? $this->input->post('pltm') : null,
                'spillway'              => $this->input->post('spillway') !== '' ? $this->input->post('spillway') : null,
                'total_outflow'         => $this->input->post('total_outflow') !== '' ? $this->input->post('total_outflow') : null,
                'plta_status'           => $this->input->post('plta_status') ?: null,
                'irigasi_status'        => $this->input->post('irigasi_status') ?: null,
                'tail_water'            => $this->input->post('tail_water') ?: null,
                'rembesan_vnotch_h'     => $this->input->post('rembesan_vnotch_h') !== '' ? $this->input->post('rembesan_vnotch_h') : null,
                'rembesan_vnotch_q'     => $this->input->post('rembesan_vnotch_q') !== '' ? $this->input->post('rembesan_vnotch_q') : null,
                'rembesan_pump_pit_l_h' => $this->input->post('rembesan_pump_pit_l_h') !== '' ? $this->input->post('rembesan_pump_pit_l_h') : null,
                'rembesan_pump_pit_l_q' => $this->input->post('rembesan_pump_pit_l_q') !== '' ? $this->input->post('rembesan_pump_pit_l_q') : null,
                'rembesan_pump_pit_r_h' => $this->input->post('rembesan_pump_pit_r_h') !== '' ? $this->input->post('rembesan_pump_pit_r_h') : null,
                'rembesan_pump_pit_r_q' => $this->input->post('rembesan_pump_pit_r_q') !== '' ? $this->input->post('rembesan_pump_pit_r_q') : null,
                'keterangan'            => $this->input->post('keterangan', TRUE) ?: null,
            ];
            
            $this->db->insert('data_bendungan', $data_insert)
                ? $this->session->set_flashdata('success', 'Data laporan bendungan berhasil diinput!')
                : $this->session->set_flashdata('error', 'Gagal menginput data bendungan.');
        }
        
        // Redirect kembali secara dinamis sesuai parameter pos yang diisi
        redirect('admin/kelola_bendungan?pos='.$id_pos_input.'&bulan='.date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    // ============================================
    // ROUTING INPUT MANUAL OLEH ADMIN (BENDUNGAN)
    // ============================================
    public function tambah_data_bendungan() {
    $id_user = $this->session->userdata('user_id') ? $this->session->userdata('user_id') : $this->session->userdata('id_user'); 
    $user    = $this->db->get_where('users', ['id_user' => $id_user])->row();
    
    if (!$user) {
        $this->session->set_flashdata('error', 'Data user tidak ditemukan.');
        redirect('admin/dashboard');
        return;
    }

    // Mengambil id_pos wilayah kerja admin ini
    $allowed_pos = $this->_get_allowed_pos_ids();

    // Ambil daftar master khusus bendungan (is_bendungan = 1) sesuai hak regional admin
    $this->db->where('is_bendungan', 1);
    if ($allowed_pos !== null) {
        $this->db->where_in('id_pos', $allowed_pos);
    }
    $data['list_pos'] = $this->db->get('master_pos')->result();

    // Tentukan bendungan mana yang sedang aktif dipilih / diinput
    $id_selected = $this->input->get('id_pos');
    if (empty($id_selected)) {
        $id_selected = !empty($data['list_pos']) ? $data['list_pos'][0]->id_pos : 0;
    }

    // Proteksi tambahan: Mencegah manipulasi URL id_pos bendungan dari luar regionalnya
    if ($allowed_pos !== null && !in_array($id_selected, $allowed_pos)) {
        show_error('Akses Terblokir: Anda tidak diizinkan mengakses bendungan di luar wilayah kendali Anda!', 403);
        return;
    }
    
    $data['pos'] = $this->db->get_where('master_pos', ['id_pos' => $id_selected])->row();
    $data['tanggal']   = $this->input->get('tanggal') ? $this->input->get('tanggal') : date('Y-m-d');
    $data['data_list'] = []; 

    if ($id_selected) {
        $data['data_list'] = $this->db->where('id_pos', $id_selected)
                                      ->like('tanggal_input', substr($data['tanggal'], 0, 7), 'after')
                                      ->order_by('tanggal_input', 'DESC')
                                      ->get('data_bendungan')
                                      ->result();
    }

    $data['app_name'] = 'CASCADE';
    $data['title']    = 'Input Manual Bendungan';
    $data['admin_name'] = $this->session->userdata('nama_lengkap');
    
    $data['content'] = $this->load->view('admin/v_input_bendungan_admin', $data, TRUE);
    $this->load->view('layout/v_admin_layout', $data);
}

    
    // ============================================
    // BARU: MASTER KELOLA MANUAL (TRANSAKSIONAL POS HUJAN / DUGA AIR)
    // ============================================
    public function kelola_manual() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        
        $pos_filter   = $this->input->get('pos', TRUE);
        $bulan_filter = $this->input->get('bulan', TRUE);

        // Ambil daftar pos hujan (PCH) & duga air (PDA) yang berhak diakses
        $this->db->select('id_pos, nama_pos, nomor_pos, tipe_pos');
        if ($allowed_pos !== null) {
            $this->db->where_in('id_pos', $allowed_pos);
        }
        $this->db->order_by('nama_pos', 'ASC');
        $pos_list = $this->db->get('master_pos')->result();

        $selected_pos_id = null;
        $selected_pos_obj = null;

        if (!empty($pos_filter)) {
            if ($allowed_pos === null || in_array($pos_filter, $allowed_pos)) {
                $selected_pos_id = $pos_filter;
            }
        }
        
        if (empty($selected_pos_id) && !empty($pos_list)) {
            $selected_pos_id = $pos_list[0]->id_pos;
        }

        if (!empty($selected_pos_id)) {
            foreach ($pos_list as $p) {
                if ($p->id_pos == $selected_pos_id) {
                    $selected_pos_obj = $p;
                    break;
                }
            }
        }

        if ($selected_pos_obj === null) {
            $selected_pos_obj = (object) [
                'id_pos'    => '',
                'nama_pos'  => 'Belum Ada Pos Terpilih',
                'nomor_pos' => '-',
                'tipe_pos'  => 'PCH'
            ];
        }

        if (empty($bulan_filter)) {
            $bulan_filter = date('Y-m');
        }

        // Ambil riwayat laporan manual terfilter scope wilayah kerja
        $this->db->select('m.*, p.nama_pos, p.nomor_pos, p.tipe_pos, u.nama_lengkap as nama_petugas')
                 ->from('data_manual m')
                 ->join('master_pos p', 'm.id_pos = p.id_pos', 'left')
                 ->join('users u', 'm.id_user = u.id_user', 'left');

        if ($allowed_pos !== null) {
            if (!empty($pos_filter) && in_array($pos_filter, $allowed_pos)) {
                $this->db->where('m.id_pos', $pos_filter);
            } else {
                $this->db->where_in('m.id_pos', $allowed_pos);
            }
        } else {
            if (!empty($pos_filter)) {
                $this->db->where('m.id_pos', $pos_filter);
            }
        }

        $this->db->where('DATE_FORMAT(m.tanggal_input, "%Y-%m") =', $bulan_filter);
        $this->db->order_by('m.tanggal_input', 'DESC');
        $data_list = $this->db->get()->result();

        $data = [
            'app_name'   => 'CASCADE', 
            'title'      => 'Kelola Laporan Manual', 
            'admin_name' => $this->session->userdata('nama_lengkap'),
            'pos_list'   => $pos_list,        
            'pos'        => $selected_pos_obj, 
            'bulan'      => $bulan_filter,    
            'data_list'  => $data_list        
        ];
        
        $data['content'] = $this->load->view('admin/v_kelola_manual', $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }


    public function tambah_manual() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        $id_pos_input = $this->input->post('id_pos', TRUE);

        if ($allowed_pos !== null && !in_array($id_pos_input, $allowed_pos)) {
            show_error('Akses Terblokir: Anda tidak memiliki kendali di pos ini!', 403);
            return;
        }

        $this->form_validation->set_rules('id_pos', 'Stasiun Pos', 'required');
        $this->form_validation->set_rules('tanggal_input', 'Tanggal Pengukuran', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data_insert = [
                'id_pos'        => $id_pos_input,
                'id_user'       => $this->session->userdata('user_id') ? $this->session->userdata('user_id') : $this->session->userdata('id_user'),
                'tanggal_input' => $this->input->post('tanggal_input', TRUE),
                'curah_hujan'   => $this->input->post('curah_hujan') !== '' ? $this->input->post('curah_hujan') : null,
                'tinggi_muka_air'=> $this->input->post('tinggi_muka_air') !== '' ? $this->input->post('tinggi_muka_air') : null,
                'debit'         => $this->input->post('debit') !== '' ? $this->input->post('debit') : null,
                'keterangan'    => $this->input->post('keterangan', TRUE) ?: null,
                'created_at'    => date('Y-m-d H:i:s')
            ];

            $this->db->insert('data_manual', $data_insert)
                ? $this->session->set_flashdata('success', 'Data laporan manual berhasil disimpan!')
                : $this->session->set_flashdata('error', 'Gagal menyimpan laporan manual.');
        }
        redirect('admin/kelola_manual?pos='.$id_pos_input.'&bulan='.date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    
    // Di dalam Admin.php
public function simpan_bendungan() {
    $data = [
        'id_pos'    => $this->input->post('id_pos'),
        'tanggal'   => $this->input->post('tanggal'),
        // ... field lainnya ...
    ];
    $this->db->insert('tabel_laporan_bendungan', $data);
    $this->session->set_flashdata('success', 'Data berhasil disimpan!');
    redirect('admin/kelola_manual');
}

    public function edit_manual() {
        $id_manual = $this->input->post('id_manual', TRUE);
        $id_pos_input = $this->input->post('id_pos', TRUE);
        $allowed_pos = $this->_get_allowed_pos_ids();

        if ($allowed_pos !== null && !in_array($id_pos_input, $allowed_pos)) {
            show_error('Akses Terblokir: Anda tidak memiliki hak memodifikasi data pada stasiun pos ini.', 403);
            return;
        }

        $this->form_validation->set_rules('id_manual', 'ID Data', 'required');
        $this->form_validation->set_rules('tanggal_input', 'Tanggal', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data_update = [
                'tanggal_input' => $this->input->post('tanggal_input', TRUE),
                'curah_hujan'   => $this->input->post('curah_hujan') !== '' ? $this->input->post('curah_hujan') : null,
                'tinggi_muka_air'=> $this->input->post('tinggi_muka_air') !== '' ? $this->input->post('tinggi_muka_air') : null,
                'debit'         => $this->input->post('debit') !== '' ? $this->input->post('debit') : null,
                'keterangan'    => $this->input->post('keterangan', TRUE) ?: null,
            ];

            $this->db->where('id_manual', $id_manual)->update('data_manual', $data_update)
                ? $this->session->set_flashdata('success', 'Data laporan manual berhasil diperbarui!')
                : $this->session->set_flashdata('error', 'Gagal memperbarui data laporan manual.');
        }
        redirect('admin/kelola_manual?pos='.$id_pos_input.'&bulan='.date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    // ============================================
// PROSES SIMPAN INPUT MANUAL POS (UNTUK ADMIN & SUPERADMIN)
// ============================================
public function simpan_data_pos() {
    $allowed_pos = $this->_get_allowed_pos_ids();
    $id_pos_input = $this->input->post('id_pos', TRUE);

    if ($allowed_pos !== null && !in_array($id_pos_input, $allowed_pos)) {
        show_error('Akses Terblokir: Anda tidak diizinkan menambahkan data di luar pos kendali Anda!', 403);
        return;
    }

    $this->form_validation->set_rules('id_pos', 'Nama Stasiun / Pos', 'required');
    $this->form_validation->set_rules('tanggal_input', 'Tanggal Pengukuran', 'required');
    
    if ($this->form_validation->run() == FALSE) {
        $this->session->set_flashdata('error', validation_errors());
        redirect('admin/tambah_data_pos?id_pos=' . $id_pos_input);
    } else {
        $data_insert = [
            'id_pos'        => $id_pos_input,
            'id_user'       => $this->session->userdata('user_id') ? $this->session->userdata('user_id') : $this->session->userdata('id_user'),
            'tanggal_input' => $this->input->post('tanggal_input', TRUE),
            // FIX: Nama key array disesuaikan dengan kolom asli DB (rain & wlevel)
            'rain'          => $this->input->post('curah_hujan') !== '' ? $this->input->post('curah_hujan') : 0,
            'wlevel'        => $this->input->post('tma') !== '' ? $this->input->post('tma') : null,
            'keterangan'    => $this->input->post('keterangan', TRUE) ?: null,
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $insert = $this->db->insert('data_manual', $data_insert);

        if ($insert) {
            $this->session->set_flashdata('success', 'Data log manual pos berhasil disimpan!');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data ke database.');
        }
        
        redirect('admin/tambah_data_pos?id_pos=' . $id_pos_input . '&tanggal=' . $data_insert['tanggal_input']);
    }
}

    public function update_manual()
{
    // 1. Ambil data dari post form modal
    $id_manual = $this->input->post('id_manual');
    $id_pos    = $this->input->post('id_pos');
    $tanggal   = $this->input->post('tanggal');
    
    // Ambil parameter bulan untuk redirect kembali ke posisi filter yang sama
    $bulan     = date('Y-m', strtotime($tanggal));

    // 2. Siapkan array untuk update data ke database
    $data_update = [
        'tanggal_input' => $tanggal,
        'updated_at'    => date('Y-m-d H:i:s')
    ];

    // 3. Cek inputan berdasarkan tipe data yang masuk (Rain atau Wlevel)
    if ($this->input->post('rain') !== null) {
        $data_update['rain'] = $this->input->post('rain') === '' ? null : $this->input->post('rain');
    }
    
    if ($this->input->post('wlevel') !== null) {
        $data_update['wlevel'] = $this->input->post('wlevel') === '' ? null : $this->input->post('wlevel');
    }

    // 4. Eksekusi update ke tabel database
    // Ganti 'tb_log_manual' dengan nama tabel laporan manual Anda yang sebenarnya
    $this->db->where('id_manual', $id_manual);
    $update = $this->db->update('data_manual', $data_update);

    // 5. Berikan feedback ke user dan redirect kembali
    if ($update) {
        $this->session->set_flashdata('success', 'Data laporan manual berhasil diperbarui!');
    } else {
        $this->session->set_slate('error', 'Gagal memperbarui data, silahkan coba lagi.');
    }

    // Redirect kembali ke halaman kelola_manual dengan membawa parameter pos dan bulan yang aktif
    redirect('admin/kelola_manual?pos=' . $id_pos . '&bulan=' . $bulan);
}

    public function hapus_manual($id_manual) {
        $data_manual = $this->db->get_where('data_manual', ['id_manual' => $id_manual])->row();
        if (!$data_manual) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan.');
            redirect('admin/kelola_manual');
            return;
        }

        $allowed_pos = $this->_get_allowed_pos_ids();
        if ($allowed_pos !== null && !in_array($data_manual->id_pos, $allowed_pos)) {
            show_error('Akses Ditolak: Anda tidak diizinkan menghapus data log ini.', 403);
            return;
        }

        // Cek dependensi: Jika id_manual ini dipakai sebagai FK di data_bendungan, set NULL dulu di data_bendungan
        $this->db->where('id_manual', $id_manual)->update('data_bendungan', ['id_manual' => NULL]);

        $this->db->where('id_manual', $id_manual)->delete('data_manual')
            ? $this->session->set_flashdata('success', 'Data laporan manual berhasil dihapus permanen.')
            : $this->session->set_flashdata('error', 'Gagal menghapus log data.');

        redirect('admin/kelola_manual?pos='.$data_manual->id_pos.'&bulan='.date('Y-m', strtotime($data_manual->tanggal_input)));
    }

    
}