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
                    'nama_alat'    => $row['nama_alat'],
                    'device_id'    => $row['id_merk'],
                    'nama_lokasi'  => $row['nama_lokasi'],
                    'sungai'       => $row['sungai'],
                    'ReceivedDate' => $row['ReceivedDate'],
                    'ReceivedTime' => $row['ReceivedTime'],
                    'Rain'         => (float)$row['Rain'],
                    'WLevel'       => (float)$row['WLevel'],
                    'batt'         => (float)$row['batt'],
                    'Lat'          => (float)$row['Lat'],
                    'Lng'          => (float)$row['Lng'],
                    'id_tipe'      => $row['id_tipe'],
                    'id_merk'      => $row['id_merk'],
                    'status'       => $row['status'],
                    'siaga1'       => (float)$row['siaga1'],
                    'siaga2'       => (float)$row['siaga2'],
                    'siaga3'       => (float)$row['siaga3'],
                    'siaga4'       => (float)$row['siaga4']
                ];
            
                $this->db->where([
                    'nama_alat'    => $insert_data['nama_alat'],
                    'ReceivedDate' => $insert_data['ReceivedDate'],
                    'ReceivedTime' => $insert_data['ReceivedTime']
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
        $this->db->order_by('ReceivedDate', 'DESC');
        $this->db->order_by('ReceivedTime', 'DESC');
        $latest_tma = $this->db->get('data_telemetri', 1)->row_array();
    
        $this->db->where('id_tipe', 'PCH');
        $this->db->order_by('ReceivedDate', 'DESC');
        $this->db->order_by('ReceivedTime', 'DESC');
        $latest_rain = $this->db->get('data_telemetri', 1)->row_array();
    
        $status_bendungan = "NORMAL";
        if ($latest_tma) {
            $lvl = (float)$latest_tma['WLevel'];
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
                'level'  => number_format($latest_tma['WLevel'] ?? 0, 2),
                'status' => $status_bendungan,
                'trend'  => "Tren Muka Air " . ($latest_tma['status'] ?? 'Stabil')
            ],
            'weather_data' => [
                'kondisi'  => (isset($latest_rain['rain']) && $latest_rain['rain'] > 0) ? 'Hujan Sedang' : 'Cerah Berawan',
                'curah'    => $latest_rain['rain'] ?? '0',
                'prediksi' => 'Terakhir Update: ' . ($latest_rain['ReceivedTime'] ?? date('H:i:s'))
            ]
        ];
    
        $data['galeri'] = $this->db->order_by('created_at', 'DESC')->limit(8)->get('galeri_kegiatan')->result_array();
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_beranda', $data);
        $this->load->view('layout/v_footer', $data);
    }

public function curah_hujan() {
    $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
    
    $data['app_name']      = "CASCADE";
    $data['title']         = "Data Curah Hujan";
    $data['tanggal_pilih'] = $tanggal;

    $w1_start = $tanggal . ' 00:00:00';
    $w1_end   = $tanggal . ' 06:00:59';
    $w2_start = $tanggal . ' 06:01:00';
    $w2_end   = $tanggal . ' 12:00:59';
    $w3_start = $tanggal . ' 12:01:00';
    $w3_end   = $tanggal . ' 18:00:59';
    $w4_start = $tanggal . ' 18:01:00';
    $w4_end   = $tanggal . ' 23:59:59';

    $all_stations = []; 

    $this->db->where('tipe_pos', 'PCH');
    $master_pos = $this->db->get('master_pos')->result_array();
    foreach ($master_pos as $pos) {
        $nama = $pos['nama_pos'];
        $all_stations[$nama] = ['nama' => $nama, 'lokasi' => $pos['sub_wilayah'] ?? '-'];
    }

    $this->db->select('nama_alat');
    $this->db->distinct();
    $this->db->where('id_tipe', 'PCH');
    $pos_telemetri_all = $this->db->get('data_telemetri')->result_array();
    foreach ($pos_telemetri_all as $pt) {
        $nama = $pt['nama_alat'];
        if (!isset($all_stations[$nama])) {
            $all_stations[$nama] = ['nama' => $nama, 'lokasi' => '-'];
        }
    }

    $sql = "
        SELECT nama_alat, Rain, CONCAT(ReceivedDate, ' ', ReceivedTime) as full_time
        FROM data_telemetri
        WHERE id_tipe = 'PCH' 
        AND CONCAT(ReceivedDate, ' ', ReceivedTime) BETWEEN ? AND ?
        ORDER BY ReceivedDate ASC, ReceivedTime ASC
    ";
    $query_transaksi = $this->db->query($sql, [$w1_start, $w4_end])->result_array();

    $transaksi_map = [];
    $latest_full_time = null; 

    foreach ($query_transaksi as $tr) {
        $nama = $tr['nama_alat'];
        $f_time = $tr['full_time'];
        $rain = (float)($tr['Rain'] ?? 0);
        
        if (!isset($transaksi_map[$nama])) {
            $transaksi_map[$nama] = ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last'=>null];
        }

        if ($latest_full_time == null || $f_time > $latest_full_time) {
            $latest_full_time = $f_time;
        }

        if ($f_time >= $w1_start && $f_time <= $w1_end) {
            $transaksi_map[$nama]['w1'] = max($transaksi_map[$nama]['w1'], $rain);
        } elseif ($f_time >= $w2_start && $f_time <= $w2_end) {
            $transaksi_map[$nama]['w2'] = max($transaksi_map[$nama]['w2'], $rain);
        } elseif ($f_time >= $w3_start && $f_time <= $w3_end) {
            $transaksi_map[$nama]['w3'] = max($transaksi_map[$nama]['w3'], $rain);
        } elseif ($f_time >= $w4_start && $f_time <= $w4_end) {
            $transaksi_map[$nama]['w4'] = max($transaksi_map[$nama]['w4'], $rain);
        }
        
        $transaksi_map[$nama]['last'] = $f_time;
    }

    ksort($all_stations);

    $pencatatan = [];
    $total_hujan_wilayah = 0; $max_hujan = 0; $pos_aktif = 0; $no = 1;

    foreach ($all_stations as $nama => $info) {
        $has_data = isset($transaksi_map[$nama]);
        $val = $has_data ? $transaksi_map[$nama] : ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last'=>null];
        $total_harian = max($val['w1'], $val['w2'], $val['w3'], $val['w4']);
        
        if($has_data) $pos_aktif++;

        $pencatatan[] = [
            'no'     => $no++,
            'pos'    => $nama,
            'lokasi' => $info['lokasi'],
            'waktu'  => $val['last'] ? date('H:i', strtotime($val['last'])) : '--:--',
            'w1'     => $val['w1'], 'w2' => $val['w2'], 'w3' => $val['w3'], 'w4' => $val['w4'],
            'total'  => $total_harian,
            'manual' => '-'
        ];
        
        if ($total_harian > $max_hujan) $max_hujan = $total_harian;
        $total_hujan_wilayah += $total_harian;
    }

    $data['last_update'] = ($latest_full_time) 
        ? "Data Terakhir: " . date('H:i', strtotime($latest_full_time)) . " WIB"
        : date('d M Y', strtotime($tanggal)) . ": Data Belum Tersedia";

    $data['summary'] = [
        'pos_aktif'   => $pos_aktif,
        'total_pos'   => count($all_stations),
        'max_hujan'   => $max_hujan,
        'avg_wilayah' => count($all_stations) > 0 ? round($total_hujan_wilayah / count($all_stations), 2) : 0
    ];
    
    $data['pencatatan'] = $pencatatan;

    $this->load->view('layout/v_header', $data);
    $this->load->view('pages/v_curah_hujan', $data);
    $this->load->view('layout/v_footer', $data);
}

 public function tma() {
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $data['app_name']      = "CASCADE";
        $data['title']         = "Tinggi Muka Air";
        $data['tanggal_pilih'] = $tanggal;

        $w1_start = $tanggal . ' 00:00:00';
        $w1_end   = $tanggal . ' 06:00:59';
        $w2_start = $tanggal . ' 06:01:00';
        $w2_end   = $tanggal . ' 12:00:59';
        $w3_start = $tanggal . ' 12:01:00';
        $w3_end   = $tanggal . ' 18:00:59';
        $w4_start = $tanggal . ' 18:01:00';
        $w4_end   = $tanggal . ' 23:59:59';

        $all_stations = []; 

        $this->db->where('tipe_pos', 'PDA'); 
        $master_pos = $this->db->get('master_pos')->result_array();
        
        foreach ($master_pos as $pos) {
            $nama = $pos['nama_pos'];
            $all_stations[$nama] = [
                'nama'   => $nama, 
                'lokasi' => $pos['sub_wilayah'] ?? '-',
                'siaga1' => (float)($pos['siaga1'] ?? 0),
                'siaga2' => (float)($pos['siaga2'] ?? 0),
                'siaga3' => (float)($pos['siaga3'] ?? 0),
                'siaga4' => (float)($pos['siaga4'] ?? 0)
            ];
        }

        $sql_all_tele = "SELECT nama_alat, MAX(siaga1) as s1, MAX(siaga2) as s2, MAX(siaga3) as s3, MAX(siaga4) as s4 
                         FROM data_telemetri WHERE id_tipe = 'PDA' GROUP BY nama_alat";
        $pos_telemetri_all = $this->db->query($sql_all_tele)->result_array();
        
        foreach ($pos_telemetri_all as $pt) {
            $nama = $pt['nama_alat'];
            if (!isset($all_stations[$nama])) {
                $all_stations[$nama] = [
                    'nama'   => $nama, 
                    'lokasi' => '-',
                    'siaga1' => (float)($pt['s1'] ?? 0),
                    'siaga2' => (float)($pt['s2'] ?? 0),
                    'siaga3' => (float)($pt['s3'] ?? 0),
                    'siaga4' => (float)($pt['s4'] ?? 0)
                ];
            }
        }

        $sql = "
            SELECT nama_alat, WLevel, CONCAT(ReceivedDate, ' ', ReceivedTime) as full_time
            FROM data_telemetri
            WHERE id_tipe = 'PDA' 
            AND CONCAT(ReceivedDate, ' ', ReceivedTime) BETWEEN ? AND ?
            ORDER BY ReceivedDate ASC, ReceivedTime ASC
        ";
        $query_transaksi = $this->db->query($sql, [$w1_start, $w4_end])->result_array();

        $transaksi_map = [];
        $latest_full_time = null; 

        foreach ($query_transaksi as $tr) {
            $nama = $tr['nama_alat'];
            $f_time = $tr['full_time'];
            $wlevel = (float)($tr['WLevel'] ?? 0);
            
            if (!isset($transaksi_map[$nama])) {
                $transaksi_map[$nama] = ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last_val'=>0, 'last_time'=>null];
            }

            if ($latest_full_time == null || $f_time > $latest_full_time) {
                $latest_full_time = $f_time;
            }

            if ($f_time >= $w1_start && $f_time <= $w1_end) {
                $transaksi_map[$nama]['w1'] = max($transaksi_map[$nama]['w1'], $wlevel);
            } elseif ($f_time >= $w2_start && $f_time <= $w2_end) {
                $transaksi_map[$nama]['w2'] = max($transaksi_map[$nama]['w2'], $wlevel);
            } elseif ($f_time >= $w3_start && $f_time <= $w3_end) {
                $transaksi_map[$nama]['w3'] = max($transaksi_map[$nama]['w3'], $wlevel);
            } elseif ($f_time >= $w4_start && $f_time <= $w4_end) {
                $transaksi_map[$nama]['w4'] = max($transaksi_map[$nama]['w4'], $wlevel);
            }
            
            $transaksi_map[$nama]['last_val'] = $wlevel;
            $transaksi_map[$nama]['last_time'] = $f_time;
        }

        ksort($all_stations);

        $pencatatan_tma = [];
        $total_w_level = 0; 
        $max_w_level = 0; 
        $count_bahaya = 0; 
        $pos_aktif = 0; 
        $no = 1;

        foreach ($all_stations as $nama => $info) {
            $has_data = isset($transaksi_map[$nama]);
            $val = $has_data ? $transaksi_map[$nama] : ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last_val'=>0, 'last_time'=>null];
            
            if($has_data) $pos_aktif++;

            $last_val = $val['last_val'];
            $siaga1 = $info['siaga1']; 

            if ($last_val > $max_w_level) $max_w_level = $last_val;
            $total_w_level += $last_val;

            if ($siaga1 > 0 && $last_val >= $siaga1) {
                $count_bahaya++;
            }

            $pencatatan_tma[] = [
                'no'     => $no++,
                'pos'    => $nama,
                'lokasi' => $info['lokasi'],
                'waktu'  => $val['last_time'] ? date('H:i', strtotime($val['last_time'])) : '--:--',
                'telemetri' => [
                    'w1' => $val['w1'],
                    'w2' => $val['w2'],
                    'w3' => $val['w3'],
                    'w4' => $val['w4']
                ],
                'last'   => $last_val,
                'manual' => [ 
                    'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0
                ],
                'siaga'  => [
                    'siaga1' => $info['siaga1'],
                    'siaga2' => $info['siaga2'],
                    'siaga3' => $info['siaga3'],
                    'siaga4' => $info['siaga4'],
                ]
            ];
        }

        $data['last_update'] = ($latest_full_time) 
            ? "Data Terakhir: " . date('H:i', strtotime($latest_full_time)) . " WIB"
            : date('d M Y', strtotime($tanggal)) . ": Data Belum Tersedia";

        $data['summary'] = [
            'pos_aktif'     => $pos_aktif,
            'total_pos'     => count($all_stations),
            'tma_tertinggi' => $max_w_level,
            'tma_rata_rata' => count($all_stations) > 0 ? round($total_w_level / count($all_stations), 2) : 0,
            'status_siaga'  => $count_bahaya,
            'status_aman'   => count($all_stations) - $count_bahaya
        ];
        
        $data['pencatatan_tma'] = $pencatatan_tma;

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_tma', $data);
        $this->load->view('layout/v_footer', $data);
    }

    public function peta() {
            $tanggal_akhir = date('Y-m-d');
            $tanggal_awal  = date('Y-m-d', strtotime('-6 days')); 

            $dates_template = [];
            for ($i = 6; $i >= 0; $i--) {
                $dates_template[date('Y-m-d', strtotime("-$i days"))] = 0;
            }

            $sql_current = "
                SELECT 
                    nama_alat as nama_tampil,
                    device_id as id_tampil,
                    id_tipe as tipe_tampil,
                    CAST(COALESCE(Lat, 0) AS DECIMAL(10,8)) as latitude,
                    CAST(COALESCE(Lng, 0) AS DECIMAL(11,8)) as longitude,
                    COALESCE(Rain, 0) as rain,
                    COALESCE(WLevel, 0) as w_level,
                    siaga1 as siaga_merah,
                    siaga2 as siaga_kuning,
                    CONCAT(ReceivedDate, ' ', ReceivedTime) as last_update
                FROM data_telemetri 
                WHERE id IN (
                    SELECT MAX(id) 
                    FROM data_telemetri 
                    GROUP BY nama_alat
                )
                ORDER BY nama_alat ASC
            ";
            $current_data = $this->db->query($sql_current)->result_array();

            $semua_pos = [];
            foreach ($current_data as $row) {
                $nama_kunci = strtoupper(trim($row['nama_tampil'])); 
                $semua_pos[$nama_kunci] = $row;
                $semua_pos[$nama_kunci]['history_map'] = $dates_template; 
            }

            $sql_history = "
                SELECT nama_alat, id_tipe, Rain, WLevel, ReceivedDate
                FROM data_telemetri
                WHERE ReceivedDate >= ? AND ReceivedDate <= ?
                ORDER BY ReceivedDate ASC, ReceivedTime ASC
            ";
            $history_data = $this->db->query($sql_history, [$tanggal_awal, $tanggal_akhir])->result_array();

// 4. Kalkulasi Data Historis secara Ketat berdasarkan Tipe Master
        foreach ($history_data as $row) {
            $nama_kunci = strtoupper(trim($row['nama_alat']));
            $date = $row['ReceivedDate'];

            if (isset($semua_pos[$nama_kunci]) && isset($semua_pos[$nama_kunci]['history_map'][$date])) {
                
                // Gunakan tipe_tampil dari array induk agar konsisten
                $tipe_pos = strtoupper(trim($semua_pos[$nama_kunci]['tipe_tampil']));
                
                if ($tipe_pos === 'PCH') {
                    // [PERBAIKAN LOGIKA]: 
                    // Ambil nilai tertinggi di hari itu karena alat telemetri 
                    // umumnya mengirimkan nilai hujan kumulatif harian.
                    $semua_pos[$nama_kunci]['history_map'][$date] = max(
                        $semua_pos[$nama_kunci]['history_map'][$date], 
                        (float)$row['Rain']
                    );
                } else {
                    // PDA (atau lainnya): Timpa dengan nilai WLevel terbaru per hari tersebut
                    $semua_pos[$nama_kunci]['history_map'][$date] = (float)$row['WLevel'];
                }
            }
        }

            $final_pos = [];
            foreach ($semua_pos as $pos) {
                if (empty($pos['latitude']) || empty($pos['longitude'])) continue;

                $labels = [];
                $values = [];
                foreach ($pos['history_map'] as $date => $val) {
                    $labels[] = date('d M', strtotime($date)); 
                    $values[] = round($val, 2);
                }
                $pos['chart_labels'] = $labels;
                $pos['chart_values'] = $values;
                
                $tipe_pos = strtoupper(trim($pos['tipe_tampil']));
                if ($tipe_pos === 'PCH') {
                    $pos['chart_title'] = 'Curah Hujan';
                    $pos['chart_unit']  = 'mm';
                } else {
                    $pos['chart_title'] = 'Ketinggian Air';
                    $pos['chart_unit']  = 'm'; 
                }

                unset($pos['history_map']); 
                
                $final_pos[] = $pos;
            }

            $data = [
                'app_name'  => 'CASCADE',
                'title'     => 'Monitoring Pos Peta',
                'semua_pos' => $final_pos,
                'summary'   => [
                    'total'  => count($final_pos),
                    'online' => count(array_filter($final_pos, fn($p) => !empty($p['last_update'])))
                ]
            ];
        
            $this->load->view('layout/v_header_peta', $data);
            $this->load->view('pages/v_peta', $data);
        }
}