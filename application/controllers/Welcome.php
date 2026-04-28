<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
    }

    public function sync_data($silent = false) {
        $url = "https://sdatelemetry.com/API_ap_telemetry/datatelemetry2.php?idbbws=12";
        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $json_data = @file_get_contents($url, false, $ctx);
        
        if ($json_data === FALSE) {
            if (!$silent) echo "Gagal: Tidak dapat terhubung ke server API.";
            return;
        }
    
        $response = json_decode($json_data, true);
    
        if (isset($response['telemetryjakarta'])) {
            $data_api = $response['telemetryjakarta'];
            
            foreach ($data_api as $row) {
                $insert_data = [
                    'nama_alat'      => $row['nama_alat'],
                    'device_id'      => $row['id_merk'],
                    'nama_lokasi'    => $row['nama_lokasi'],
                    'lat'            => (float)($row['Lat'] ?? 0),
                    'lon'            => (float)($row['Lng'] ?? 0),
                    'received_date'  => $row['Tgl'] ?? date('Y-m-d'),
                    'received_time'  => $row['Jam'] ?? date('H:i:s'),
                    'rain'           => (float)($row['Rainfall'] ?? 0),
                    'w_level'        => (float)($row['W_Level'] ?? 0),
                    'batt'           => (float)($row['Batt'] ?? 0),
                    'id_tipe'        => $row['id_tipe'],
                    'siaga1'         => (float)($row['siaga1'] ?? 0),
                    'siaga2'         => (float)($row['siaga2'] ?? 0),
                    'siaga3'         => (float)($row['siaga3'] ?? 0),
                    'siaga4'         => (float)($row['siaga4'] ?? 0),
                    'status'         => $row['status'] ?? 'Normal',
                ];

                // Cek duplikasi agar data tidak double
                $this->db->where([
                    'nama_alat'     => $insert_data['nama_alat'],
                    'received_date' => $insert_data['received_date'],
                    'received_time' => $insert_data['received_time']
                ]);
                $exists = $this->db->get('data_telemetri')->num_rows();

                if ($exists == 0) {
                    $this->db->insert('data_telemetri', $insert_data);
                }
            }
            if (!$silent) echo "Sync Berhasil!";
        } else {
            if (!$silent) echo "Gagal mengambil data: Format JSON salah.";
        }
    }
  
    public function index() {
        $this->sync_data(true); 
    
        $this->db->where('id_tipe', 'PDA');
        $this->db->order_by('received_date', 'DESC');
        $this->db->order_by('received_time', 'DESC');
        $latest_tma = $this->db->get('data_telemetri', 1)->row_array();
    
        $this->db->where('id_tipe', 'PCH');
        $this->db->order_by('received_date', 'DESC');
        $this->db->order_by('received_time', 'DESC');
        $latest_rain = $this->db->get('data_telemetri', 1)->row_array();
    
        $status_bendungan = "NORMAL";
        if ($latest_tma) {
            $lvl = (float)$latest_tma['w_level'];
            // Menggunakan siaga1 sebagai ambang batas tertinggi (Bahaya/Awas)
            if ($lvl >= (float)$latest_tma['siaga1'] && (float)$latest_tma['siaga1'] > 0) $status_bendungan = "BAHAYA";
            elseif ($lvl >= (float)$latest_tma['siaga2'] && (float)$latest_tma['siaga2'] > 0) $status_bendungan = "SIAGA";
            elseif ($lvl >= (float)$latest_tma['siaga3'] && (float)$latest_tma['siaga3'] > 0) $status_bendungan = "WASPADA";
        }
    
        $data = [
            'app_name' => "CASCADE",
            'title'    => "Sistem Informasi Hidrologi",
            'hero_bg'  => "https://images.unsplash.com/photo-1545459723-861eb1bb3809?q=80&w=1920&auto=format&fit=crop",
            'dam_status' => [
                'nama'   => $latest_tma['nama_alat'] ?? 'Bendungan Margatiga',
                'level'  => number_format($latest_tma['w_level'] ?? 0, 2),
                'status' => $status_bendungan,
                'trend'  => "Tren Muka Air " . ($latest_tma['status'] ?? 'Stabil')
            ],
            'weather_data' => [
                'kondisi'  => (isset($latest_rain['rain']) && $latest_rain['rain'] > 0) ? 'Hujan Sedang' : 'Cerah Berawan',
                'curah'    => $latest_rain['rain'] ?? '0',
                'prediksi' => 'Terakhir Update: ' . ($latest_rain['received_time'] ?? date('H:i:s'))
            ]
        ];
    
        $data['galeri'] = $this->db->order_by('created_at', 'DESC')->limit(8)->get('galeri_kegiatan')->result_array();
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_beranda', $data);
        $this->load->view('layout/v_footer', $data);
    }

    public function curah_hujan() {
        $tanggal = $this->input->get('tanggal');
        if (!$tanggal) { $tanggal = date('Y-m-d'); }

        $data['app_name']      = "CASCADE";
        $data['title']         = "Data Curah Hujan";
        $data['tanggal_pilih'] = $tanggal;

        $this->db->select('nama_pos as nama_alat, tipe_pos as id_tipe');
        $this->db->where_in('tipe_pos', ['PCH', 'PDA']);
        $master_pos = $this->db->get('master_pos')->result_array();

        $this->db->where_in('id_tipe', ['PCH', 'PDA']); 
        $this->db->where('received_date', $tanggal);
        $query_transaksi = $this->db->get('data_telemetri')->result_array();

        $transaksi_map = [];
        foreach ($query_transaksi as $tr) {
            $nama = $tr['nama_alat'];
            $time = $tr['received_time'];
            $rain = (float)($tr['rain'] ?? 0);

            if (!isset($transaksi_map[$nama])) {
                $transaksi_map[$nama] = ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'total'=>0, 'last_time'=>'00:00:00'];
            }

            if ($time > '07:00:00' && $time <= '13:00:00') $transaksi_map[$nama]['w1'] += $rain;
            elseif ($time > '13:00:00' && $time <= '19:00:00') $transaksi_map[$nama]['w2'] += $rain;
            elseif ($time > '19:00:00' || $time <= '01:00:00') $transaksi_map[$nama]['w3'] += $rain;
            elseif ($time > '01:00:00' && $time <= '07:00:00') $transaksi_map[$nama]['w4'] += $rain;
            
            $transaksi_map[$nama]['total'] += $rain;
            if ($time > $transaksi_map[$nama]['last_time']) $transaksi_map[$nama]['last_time'] = $time;
        }

        $pencatatan = [];
        $total_hujan = 0; $max_hujan = 0; $latest_time = "--:--";

        foreach ($master_pos as $i => $pos) {
            $nama = $pos['nama_alat'];
            $ada_data = isset($transaksi_map[$nama]);
            $item = [
                'no'     => $i + 1,
                'pos'    => $nama,
                'waktu'  => $ada_data ? substr($transaksi_map[$nama]['last_time'], 0, 5) : '--:--',
                'w1'     => $ada_data ? round($transaksi_map[$nama]['w1'], 2) : 0,
                'w2'     => $ada_data ? round($transaksi_map[$nama]['w2'], 2) : 0,
                'w3'     => $ada_data ? round($transaksi_map[$nama]['w3'], 2) : 0,
                'w4'     => $ada_data ? round($transaksi_map[$nama]['w4'], 2) : 0,
                'total'  => $ada_data ? round($transaksi_map[$nama]['total'], 2) : 0,
                'manual' => '-'
            ];
            if ($item['total'] > $max_hujan) $max_hujan = $item['total'];
            $total_hujan += $item['total'];
            if ($ada_data && $item['waktu'] > $latest_time) $latest_time = $item['waktu'];
            $pencatatan[] = $item;
        }

        $data['last_update'] = date('d M Y', strtotime($tanggal)) . ", " . $latest_time . " WIB";
        $data['summary'] = [
            'pos_aktif'   => count($transaksi_map),
            'total_pos'   => count($master_pos),
            'max_hujan'   => $max_hujan,
            'avg_wilayah' => count($master_pos) > 0 ? round($total_hujan / count($master_pos), 2) : 0
        ];
        $data['pencatatan'] = $pencatatan;

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_curah_hujan', $data);
        $this->load->view('layout/v_footer', $data);
    }

    public function peta() {
        // 1. Ambil data terbaru dari setiap alat di tabel telemetri
        // Kita gunakan subquery untuk mendapatkan record terakhir (tgl & jam terbaru) per device_id
        $sql_latest = "
            SELECT t1.* FROM data_telemetri t1
            INNER JOIN (
                SELECT device_id, MAX(CONCAT(received_date, ' ', received_time)) as max_dt
                FROM data_telemetri 
                GROUP BY device_id
            ) t2 ON t1.device_id = t2.device_id 
            AND CONCAT(t1.received_date, ' ', t1.received_time) = t2.max_dt
        ";
        $latest_telemetry = $this->db->query($sql_latest)->result_array();
    
        // Map data telemetri berdasarkan device_id agar mudah dicocokkan
        $tele_map = [];
        foreach ($latest_telemetry as $lt) {
            $tele_map[$lt['device_id']] = $lt;
        }
    
        // 2. Ambil data Master Pos
        $master_pos = $this->db->get('master_pos')->result_array();
    
        $final_pos = [];
        $registered_device_ids = [];
    
        // 3. Proses Master Pos (Prioritas Utama)
        foreach ($master_pos as $m) {
            $dev_id = $m['device_id_telemetry'];
            $has_data = isset($tele_map[$dev_id]);
            $t = $has_data ? $tele_map[$dev_id] : null;
    
            $final_pos[] = [
                'id_tampil'   => $m['nomor_pos'],
                'nama_tampil' => $m['nama_pos'],
                'tipe_tampil' => $m['tipe_pos'],
                'latitude'    => $m['lat'],
                'longitude'   => $m['lon'],
                'siaga1'      => $m['siaga1'],
                'siaga2'      => $m['siaga2'],
                'siaga3'      => $m['siaga3'],
                'siaga4'      => $m['siaga4'],
                'rain'        => $has_data ? $t['rain'] : 0,
                'w_level'     => $has_data ? $t['w_level'] : 0,
                'last_update' => $has_data ? $t['received_date'] . ' ' . $t['received_time'] : null,
                'asal_data'   => 'MASTER'
            ];
            
            if ($dev_id) {
                $registered_device_ids[] = $dev_id;
            }
        }
    
        // 4. Proses Pos Tambahan (Hanya jika ada di API tapi tidak ada di Master)
        // Dan hanya ambil yang koordinatnya valid (tidak 0 atau null)
        foreach ($latest_telemetry as $lt) {
            if (!in_array($lt['device_id'], $registered_device_ids)) {
                if ($lt['lat'] != 0 && $lt['lon'] != 0 && !empty($lt['lat'])) {
                    $final_pos[] = [
                        'id_tampil'   => $lt['device_id'],
                        'nama_tampil' => $lt['nama_alat'] . " (Unregistered)",
                        'tipe_tampil' => $lt['id_tipe'],
                        'latitude'    => $lt['lat'],
                        'longitude'   => $lt['lon'],
                        'siaga1'      => $lt['siaga1'],
                        'siaga2'      => $lt['siaga2'],
                        'siaga3'      => $lt['siaga3'],
                        'siaga4'      => $lt['siaga4'],
                        'rain'        => $lt['rain'],
                        'w_level'     => $lt['w_level'],
                        'last_update' => $lt['received_date'] . ' ' . $lt['received_time'],
                        'asal_data'   => 'TELEMETRY'
                    ];
                }
            }
        }
    
        $data = [
            'app_name'     => 'CASCADE',
            'title'        => 'Peta Sebaran Stasiun',
            'semua_pos'    => $final_pos,
            'summary'      => [
                'total'   => count($final_pos),
                'online'  => count(array_filter($final_pos, fn($p) => $p['last_update'] !== null)),
                'offline' => count(array_filter($final_pos, fn($p) => $p['last_update'] === null))
            ]
        ];
        
        // Data rawan banjir tetap sama
        $data['rawan_banjir'] = [
            ['nama' => 'Bandar Lampung (Pesisir)', 'lat' => -5.449, 'lon' => 105.275, 'level' => 'tinggi'],
            ['nama' => 'Hilir Way Sekampung', 'lat' => -5.350, 'lon' => 105.500, 'level' => 'sedang'],
            ['nama' => 'Hilir Way Semaka', 'lat' => -5.550, 'lon' => 104.600, 'level' => 'tinggi'],
            ['nama' => 'Punggur/Seputih', 'lat' => -4.950, 'lon' => 105.150, 'level' => 'cukup'],
            ['nama' => 'Mesuji (Rawa)', 'lat' => -4.050, 'lon' => 105.400, 'level' => 'sedang'],
        ];
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_peta', $data);
        $this->load->view('layout/v_footer', $data);
    }

    public function tma() {
        $tanggal = $this->input->get('tanggal');
        if (!$tanggal) { $tanggal = date('Y-m-d'); }

        $data['app_name']      = "CASCADE";
        $data['title']         = "Tinggi Muka Air";
        $data['tanggal_pilih'] = $tanggal;

        $this->db->select('nama_lokasi');
        $this->db->group_by('nama_lokasi');
        $master_pos = $this->db->get('data_telemetri')->result_array();

        $this->db->where('received_date', $tanggal);
        $query_transaksi = $this->db->get('data_telemetri')->result_array();

        $transaksi_map = [];
        $latest_update_time = "00:00";

        foreach ($query_transaksi as $tr) {
            $nama = $tr['nama_lokasi'];
            $time = substr($tr['received_time'], 0, 5);

            if (!isset($transaksi_map[$nama])) {
                $transaksi_map[$nama] = [
                    'jam_data' => [],
                    'last_val' => $tr['w_level'],
                    'last_time' => $time,
                    'siaga' => [
                        's1' => $tr['siaga1'],
                        's2' => $tr['siaga2'],
                        's3' => $tr['siaga3'],
                        's4' => $tr['siaga4']
                    ]
                ];
            }

            $hour = (int)substr($time, 0, 2);
            $transaksi_map[$nama]['jam_data'][$hour] = $tr['w_level'];
            if ($time > $latest_update_time) $latest_update_time = $time;
        }

        $pencatatan_tma = [];
        $total_w_level = 0; $max_w_level = 0; $count_bahaya = 0;

        foreach ($master_pos as $key => $pos) {
            $nama = $pos['nama_lokasi'];
            $ada_data = isset($transaksi_map[$nama]);

            $jam_targets = ['06', '12', '18'];
            $manual_hourly = [];
            foreach ($jam_targets as $jt) {
                $res_m = $this->db->select('nilai_manual')
                                  ->where('tanggal', $tanggal)
                                  ->where('HOUR(jam_ukur)', (int)$jt)
                                  ->join('data_telemetri', 'data_telemetri.id = data_manual.id_telemetri')
                                  ->where('data_telemetri.nama_lokasi', $nama)
                                  ->get('data_manual', 1)->row();
                $manual_hourly[$jt] = $res_m ? $res_m->nilai_manual : 0;
            }

            $last_val = $ada_data ? $transaksi_map[$nama]['last_val'] : 0;
            $siaga = $ada_data ? $transaksi_map[$nama]['siaga'] : ['s1'=>0, 's2'=>0, 's3'=>0, 's4'=>0];

            if ($last_val > $max_w_level) $max_w_level = $last_val;
            $total_w_level += $last_val;
            if ($ada_data && $last_val >= $siaga['s1'] && $siaga['s1'] > 0) $count_bahaya++;

            $pencatatan_tma[] = [
                'no'        => $key + 1,
                'pos'       => $nama,
                'waktu'     => $ada_data ? $transaksi_map[$nama]['last_time'] : '--:--',
                'telemetri' => [
                    '06' => $ada_data && isset($transaksi_map[$nama]['jam_data'][6]) ? $transaksi_map[$nama]['jam_data'][6] : 0,
                    '12' => $ada_data && isset($transaksi_map[$nama]['jam_data'][12]) ? $transaksi_map[$nama]['jam_data'][12] : 0,
                    '18' => $ada_data && isset($transaksi_map[$nama]['jam_data'][18]) ? $transaksi_map[$nama]['jam_data'][18] : 0,
                ],
                'last'      => $last_val,
                'manual'    => $manual_hourly,
                'siaga'     => $siaga
            ];
        }

        $data['last_update'] = date('d M Y', strtotime($tanggal)) . ", " . $latest_update_time . " WIB";
        $data['summary'] = [
            'pos_aktif'     => count($transaksi_map),
            'total_pos'     => count($master_pos),
            'tma_tertinggi' => $max_w_level,
            'tma_rata_rata' => count($master_pos) > 0 ? ($total_w_level / count($master_pos)) : 0,
            'status_siaga'  => $count_bahaya,
            'siaga_merah'   => $count_bahaya,
            'status_aman'   => count($master_pos) - $count_bahaya
        ];

        $data['pencatatan_tma'] = $pencatatan_tma;

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_tma', $data);
        $this->load->view('layout/v_footer', $data);
    }
}