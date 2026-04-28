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
                    'sungai'       => $row['sungai'], // Sekarang sudah sama: kunci API 'sungai' ke kolom DB 'sungai'
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
            
                // Cek duplikasi tetap berdasarkan Nama, Tanggal, dan Jam
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
        // 1. Penanganan Tanggal
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
    
        $data['app_name']      = "CASCADE";
        $data['title']         = "Data Curah Hujan";
        $data['tanggal_pilih'] = $tanggal;
    
        // 2. Ambil Master Pos - Hanya PCH
        $this->db->select('nama_pos as nama_alat, tipe_pos as id_tipe');
        $this->db->where('tipe_pos', 'PCH');
        $master_pos = $this->db->get('master_pos')->result_array();
    
        // 3. Ambil Data Telemetri - Sesuai kolom baru (ReceivedDate & Rain)
        $this->db->where('id_tipe', 'PCH'); 
        $this->db->where('ReceivedDate', $tanggal);
        // Urutkan berdasarkan waktu agar data terakhir yang diproses adalah yang terbaru
        $this->db->order_by('ReceivedTime', 'ASC');
        $query_transaksi = $this->db->get('data_telemetri')->result_array();
    
        $transaksi_map = [];
        $latest_time_found = "00:00:00"; // Variabel penampung waktu terbaru
    
        foreach ($query_transaksi as $tr) {
            $nama = $tr['nama_alat'];
            $time = $tr['ReceivedTime'];
            $rain = (float)($tr['Rain'] ?? 0);
    
            if (!isset($transaksi_map[$nama])) {
                $transaksi_map[$nama] = [
                    'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 
                    'last_time' => '00:00:00'
                ];
            }
    
            // Simpan waktu terbaru secara global untuk Last Update
            if ($time > $latest_time_found) {
                $latest_time_found = $time;
            }
    
            // Logika MAX per periode (Agar data 5 menitan tidak terjumlah/akumulasi salah)
            if ($time >= '07:00:00' && $time < '13:00:00') {
                if ($rain > $transaksi_map[$nama]['w1']) $transaksi_map[$nama]['w1'] = $rain;
            } 
            elseif ($time >= '13:00:00' && $time < '19:00:00') {
                if ($rain > $transaksi_map[$nama]['w2']) $transaksi_map[$nama]['w2'] = $rain;
            } 
            elseif ($time >= '19:00:00' || $time < '01:00:00') {
                if ($rain > $transaksi_map[$nama]['w3']) $transaksi_map[$nama]['w3'] = $rain;
            } 
            elseif ($time >= '01:00:00' && $time < '07:00:00') {
                if ($rain > $transaksi_map[$nama]['w4']) $transaksi_map[$nama]['w4'] = $rain;
            }
            
            // Update waktu terakhir khusus untuk pos ini
            if ($time > $transaksi_map[$nama]['last_time']) {
                $transaksi_map[$nama]['last_time'] = $time;
            }
        }
    
        $pencatatan = [];
        $total_hujan_wilayah = 0; 
        $max_hujan = 0; 
    
        foreach ($master_pos as $i => $pos) {
            $nama = $pos['nama_alat'];
            $ada_data = isset($transaksi_map[$nama]);
            
            // Total harian diambil dari nilai tertinggi yang tercatat
            $total_harian = $ada_data ? max(
                $transaksi_map[$nama]['w1'], 
                $transaksi_map[$nama]['w2'], 
                $transaksi_map[$nama]['w3'], 
                $transaksi_map[$nama]['w4']
            ) : 0;
    
            $item = [
                'no'     => $i + 1,
                'pos'    => $nama,
                'waktu'  => $ada_data ? substr($transaksi_map[$nama]['last_time'], 0, 5) : '--:--',
                'w1'     => $ada_data ? $transaksi_map[$nama]['w1'] : 0,
                'w2'     => $ada_data ? $transaksi_map[$nama]['w2'] : 0,
                'w3'     => $ada_data ? $transaksi_map[$nama]['w3'] : 0,
                'w4'     => $ada_data ? $transaksi_map[$nama]['w4'] : 0,
                'total'  => $total_harian,
                'manual' => '-'
            ];
    
            if ($item['total'] > $max_hujan) $max_hujan = $item['total'];
            $total_hujan_wilayah += $item['total'];
            $pencatatan[] = $item;
        }
    
        // Perbaikan format Last Update agar sinkron dengan data terbaru di DB
        $data['last_update'] = ($latest_time_found !== "00:00:00") 
            ? date('d M Y', strtotime($tanggal)) . ", " . substr($latest_time_found, 0, 5) . " WIB"
            : date('d M Y', strtotime($tanggal)) . ", Data Belum Tersedia";
    
        $data['summary'] = [
            'pos_aktif'   => count($transaksi_map),
            'total_pos'   => count($master_pos),
            'max_hujan'   => $max_hujan,
            'avg_wilayah' => count($master_pos) > 0 ? round($total_hujan_wilayah / count($master_pos), 2) : 0
        ];
        $data['pencatatan'] = $pencatatan;
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_curah_hujan', $data);
        $this->load->view('layout/v_footer', $data);
    }

    public function peta() {
        // Kita gunakan GROUP BY nama_alat karena device_id isinya sama semua (APTECH)
        // Ini akan menghasilkan daftar 40 pos yang berbeda berdasarkan lokasinya.
        $sql = "
            SELECT 
                device_id as id_tampil,
                nama_alat as nama_tampil,
                id_tipe as tipe_tampil,
                CAST(COALESCE(Lat, 0) AS DECIMAL(10,8)) as latitude,
                CAST(COALESCE(Lng, 0) AS DECIMAL(11,8)) as longitude,
                COALESCE(Rain, 0) as rain,
                COALESCE(WLevel, 0) as w_level,
                CONCAT(ReceivedDate, ' ', ReceivedTime) as last_update,
                'TELEMETRY' as asal_data -- Kolom ini wajib ada agar error di v_peta.php hilang
            FROM data_telemetri 
            WHERE id IN (
                SELECT MAX(id) 
                FROM data_telemetri 
                GROUP BY nama_alat -- Mengelompokkan berdasarkan nama lokasi agar unik
            )
            ORDER BY nama_alat ASC
        ";
    
        $semua_pos = $this->db->query($sql)->result_array();
    
        $data = [
            'app_name'     => 'CASCADE',
            'title'        => 'Monitoring 40 Pos Telemetri',
            'semua_pos'    => $semua_pos,
            'summary'      => [
                'total'   => count($semua_pos),
                'online'  => count(array_filter($semua_pos, fn($p) => !empty($p['last_update'])))
            ]
        ];
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_peta', $data);
        //$this->load->view('layout/v_footer', $data);
    }
    public function tma() {
        // 1. Penanganan Tanggal
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
    
        $data['app_name']      = "CASCADE";
        $data['title']         = "Tinggi Muka Air";
        $data['tanggal_pilih'] = $tanggal;
    
        // 2. Ambil Master Pos - HANYA PDA (TMA)
        // Sebaiknya ambil dari master_pos agar semua pos muncul meskipun belum ada data hari ini
        $this->db->select('nama_pos as nama_lokasi, tipe_pos');
        $this->db->where('tipe_pos', 'PDA');
        $master_pos = $this->db->get('master_pos')->result_array();
    
        // 3. Ambil Data Telemetri - Sesuaikan dengan kolom baru (WLevel, ReceivedDate, ReceivedTime)
        $this->db->where('id_tipe', 'PDA'); 
        $this->db->where('ReceivedDate', $tanggal);
        $this->db->order_by('ReceivedTime', 'ASC'); // Urutkan dari waktu terlama ke terbaru
        $query_transaksi = $this->db->get('data_telemetri')->result_array();
    
        $transaksi_map = [];
        $latest_update_time = "00:00";
    
        foreach ($query_transaksi as $tr) {
            $nama = $tr['nama_lokasi'];
            $time_full = $tr['ReceivedTime']; // Format: 10:45:00
            $time_short = substr($time_full, 0, 5); // Format: 10:45
            $wlevel = (float)$tr['WLevel'];
    
            if (!isset($transaksi_map[$nama])) {
                $transaksi_map[$nama] = [
                    'jam_data' => [],
                    'last_val' => $wlevel,
                    'last_time' => $time_short,
                    'siaga' => [
                        's1' => (float)$tr['siaga1'],
                        's2' => (float)$tr['siaga2'],
                        's3' => (float)$tr['siaga3'],
                        's4' => (float)$tr['siaga4']
                    ]
                ];
            }
    
            // Simpan data per jam (untuk kolom 06, 12, 18)
            $hour = (int)substr($time_full, 0, 2);
            $transaksi_map[$nama]['jam_data'][$hour] = $wlevel;
    
            // Update nilai terakhir (karena order by ASC, maka nilai terakhir yang diproses adalah yang paling baru)
            $transaksi_map[$nama]['last_val'] = $wlevel;
            $transaksi_map[$nama]['last_time'] = $time_short;
    
            // Update waktu pembaruan terakhir global
            if ($time_short > $latest_update_time) {
                $latest_update_time = $time_short;
            }
        }
    
        $pencatatan_tma = [];
        $total_w_level = 0; 
        $max_w_level = 0; 
        $count_bahaya = 0;
    
        foreach ($master_pos as $key => $pos) {
            $nama = $pos['nama_lokasi'];
            $ada_data = isset($transaksi_map[$nama]);
    
            // 4. Logika pengambilan data manual (Opsional, pastikan tabel data_manual ada)
            $jam_targets = ['06', '12', '18'];
            $manual_hourly = [];
            foreach ($jam_targets as $jt) {
                $this->db->select('nilai_manual');
                $this->db->from('data_manual');
                $this->db->join('data_telemetri', 'data_telemetri.id = data_manual.id_telemetri');
                $this->db->where('ReceivedDate', $tanggal);
                $this->db->where('HOUR(ReceivedTime)', (int)$jt);
                $this->db->where('data_telemetri.nama_lokasi', $nama);
                $res_m = $this->db->get()->row();
                
                $manual_hourly[$jt] = $res_m ? $res_m->nilai_manual : 0;
            }
    
            $last_val = $ada_data ? $transaksi_map[$nama]['last_val'] : 0;
            $siaga = $ada_data ? $transaksi_map[$nama]['siaga'] : ['s1'=>0, 's2'=>0, 's3'=>0, 's4'=>0];
    
            if ($last_val > $max_w_level) $max_w_level = $last_val;
            $total_w_level += $last_val;
    
            // Cek status siaga (Jika TMA >= Siaga 1)
            if ($ada_data && $siaga['s1'] > 0 && $last_val >= $siaga['s1']) {
                $count_bahaya++;
            }
    
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
                'siaga'     => ['s1'     => $siaga['s1'] ?? 0,
                                's2'     => $siaga['s2'] ?? 0,
                                's3'     => $siaga['s3'] ?? 0,
                                's4'     => $siaga['s4'] ?? 0,
                                // Tambahkan ini jika view memanggil key warna
                                'hijau'  => (float)($siaga['s3'] > 0 ? $siaga['s3'] : ($siaga['s4'] ?? 0)),
                                'kuning' => (float)($siaga['s2'] ?? 0),
                                'merah'  => (float)($siaga['s1'] ?? 0)]
            ];
        }
    
        // 5. Output Summary & View
        $data['last_update'] = ($latest_update_time !== "00:00") 
            ? date('d M Y', strtotime($tanggal)) . ", " . $latest_update_time . " WIB"
            : date('d M Y', strtotime($tanggal)) . ", Data Belum Tersedia";
    
        $data['summary'] = [
            'pos_aktif'     => count($transaksi_map),
            'total_pos'     => count($master_pos),
            'tma_tertinggi' => $max_w_level,
            'tma_rata_rata' => count($master_pos) > 0 ? round($total_w_level / count($master_pos), 2) : 0,
            'status_siaga'  => $count_bahaya,
            'status_aman'   => count($master_pos) - $count_bahaya
        ];
    
        $data['pencatatan_tma'] = $pencatatan_tma;
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_tma', $data);
        $this->load->view('layout/v_footer', $data);
    }
}