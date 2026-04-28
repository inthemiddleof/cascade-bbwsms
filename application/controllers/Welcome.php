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
        $ctx = stream_context_create(['http' => ['timeout' => 5]]);
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
                    'nama_alat'   => $row['nama_alat'],
                    'device_id'   => $row['id_merk'],
                    'nama_lokasi' => $row['nama_lokasi'],
                    'lat'         => (float)($row['Lat'] ?? 0),
                    'lon'         => (float)($row['Lng'] ?? 0),
                    'rain'        => (float)($row['Rain'] ?? 0),
                    'w_level'     => (float)($row['WLevel'] ?? 0),
                    'id_tipe'     => $row['id_tipe'], // PCH atau PDA
                    'status'      => $row['status'] ?? 'Offline',
                    'tgl'         => date('Y-m-d H:i:s', strtotime($row['ReceivedDate'] . ' ' . $row['ReceivedTime']))
                ];
                $this->db->insert('data_telemetri', $insert_data);
            }
            if (!$silent) echo "Sync Berhasil!";
        } else {
            if (!$silent) echo "Gagal mengambil data: Variabel json tidak ditemukan atau format salah.";
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
            if ($lvl >= (float)$latest_tma['siaga_merah'] && (float)$latest_tma['siaga_merah'] > 0) $status_bendungan = "BAHAYA";
            elseif ($lvl >= (float)$latest_tma['siaga_kuning'] && (float)$latest_tma['siaga_kuning'] > 0) $status_bendungan = "SIAGA";
            elseif ($lvl >= (float)$latest_tma['siaga_hijau'] && (float)$latest_tma['siaga_hijau'] > 0) $status_bendungan = "WASPADA";
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
        // 1. Ambil input tanggal, default hari ini
        $tanggal = $this->input->get('tanggal');
        if (!$tanggal) { 
            $tanggal = date('Y-m-d'); 
        }
    
        $data['app_name']      = "CASCADE";
        $data['title']         = "Data Curah Hujan";
        $data['tanggal_pilih'] = $tanggal;
    
        // 2. Ambil Master Pos dari tabel master_pos (bukan data_telemetri) 
        // agar semua daftar pos muncul meski di tanggal tersebut tidak ada kiriman data
        $this->db->select('nama_pos as nama_alat, tipe_pos as id_tipe');
        $this->db->where_in('tipe_pos', ['PCH', 'PDA']);
        $master_pos = $this->db->get('master_pos')->result_array();
    
        // 3. Ambil data transaksi berdasarkan tanggal yang dipilih
        $this->db->where_in('id_tipe', ['PCH', 'PDA']); 
        $this->db->where('received_date', $tanggal);
        $query_transaksi = $this->db->get('data_telemetri')->result_array();
    
        $transaksi_map = [];
        foreach ($query_transaksi as $tr) {
            $nama = $tr['nama_alat'];
            $time = $tr['received_time'];
            $rain = (float) $tr['rain'];
    
            if (!isset($transaksi_map[$nama])) {
                $transaksi_map[$nama] = ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'total'=>0, 'last_time'=>'00:00:00'];
            }
    
            // Pembagian Waktu (W1-W4)
            if ($time > '07:00:00' && $time <= '13:00:00') {
                $transaksi_map[$nama]['w1'] += $rain;
            } elseif ($time > '13:00:00' && $time <= '19:00:00') {
                $transaksi_map[$nama]['w2'] += $rain;
            } elseif ($time > '19:00:00' || $time <= '01:00:00') {
                $transaksi_map[$nama]['w3'] += $rain;
            } elseif ($time > '01:00:00' && $time <= '07:00:00') {
                $transaksi_map[$nama]['w4'] += $rain;
            }
            
            $transaksi_map[$nama]['total'] += $rain;
            if ($time > $transaksi_map[$nama]['last_time']) {
                $transaksi_map[$nama]['last_time'] = $time;
            }
        }
    
        $pencatatan = [];
        $total_hujan_wilayah = 0;
        $max_hujan = 0;
        $latest_update_time = "--:--";
    
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
            $total_hujan_wilayah += $item['total'];
            if ($ada_data && ($latest_update_time == "--:--" || $item['waktu'] > $latest_update_time)) {
                $latest_update_time = $item['waktu'];
            }
    
            $pencatatan[] = $item;
        }
    
        $data['last_update'] = date('d M Y', strtotime($tanggal)) . ", " . $latest_update_time . " WIB";
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

    public function peta()
{
    $sql = "
        -- 1. Ambil semua dari Master Pos (Prioritas Master)
        SELECT 
            m.nomor_pos as id_tampil,
            m.nama_pos as nama_tampil,
            m.tipe_pos as tipe_tampil,
            m.lat as latitude,
            m.lon as longitude,
            COALESCE(t.rain, 0) as rain,
            COALESCE(t.w_level, 0) as w_level,
            t.tgl as last_update,
            'MASTER' as asal_data
        FROM master_pos m
        LEFT JOIN (
            SELECT t1.* FROM data_telemetri t1
            INNER JOIN (
                SELECT device_id, MAX(tgl) as max_tgl 
                FROM data_telemetri GROUP BY device_id
            ) t2 ON t1.device_id = t2.device_id AND t1.tgl = t2.max_tgl
        ) t ON m.device_id_telemetry = t.device_id

        UNION

        -- 2. Ambil dari Telemetri yang belum terdaftar di Master (Gunakan kordinat alat)
        SELECT 
            t.device_id as id_tampil,
            t.nama_alat as nama_tampil,
            t.id_tipe as tipe_tampil,
            t.lat as latitude,
            t.lon as longitude,
            t.rain,
            t.w_level,
            t.tgl as last_update,
            'TELEMETRY' as asal_data
        FROM data_telemetri t
        WHERE t.device_id NOT IN (SELECT COALESCE(device_id_telemetry, '') FROM master_pos)
        AND t.lat IS NOT NULL AND t.lat != 0
    ";

    $semua_pos = $this->db->query($sql)->result_array();

    $data = [
        'app_name'     => 'CASCADE',
        'title'        => 'Peta Sebaran Stasiun',
        'semua_pos'    => $semua_pos,
        'summary'      => [
            'total'   => count($semua_pos),
            'online'  => count(array_filter($semua_pos, fn($p) => $p['last_update'] !== null)),
            'offline' => count(array_filter($semua_pos, fn($p) => $p['last_update'] === null))
        ]
    ];
    
    $data['rawan_banjir'] = [
        ['nama' => 'Bandar Lampung (Pesisir)', 'lat' => -5.449, 'lon' => 105.275, 'level' => 'tinggi'],
        ['nama' => 'Hilir Way Sekampung', 'lat' => -5.350, 'lon' => 105.500, 'level' => 'sedang'],
        ['nama' => 'Hilir Way Semaka', 'lat' => -5.550, 'lon' => 104.600, 'level' => 'tinggi'],
        ['nama' => 'Punggur/Seputih', 'lat' => -4.950, 'lon' => 105.150, 'level' => 'cukup'],
        ['nama' => 'Mesuji (Rawa)', 'lat' => -4.050, 'lon' => 105.400, 'level' => 'sedang'],
    ];

    $this->load->view('layout/v_header', $data);
    $this->load->view('pages/v_peta', $data);
}

public function tma() {
    $tanggal = $this->input->get('tanggal');
    if (!$tanggal) { $tanggal = date('Y-m-d'); }

    $data['app_name']      = "CASCADE";
    $data['title']         = "Tinggi Muka Air";
    $data['tanggal_pilih'] = $tanggal;

    // 1. Ambil daftar lokasi unik (Seluruh Pos)
    $this->db->select('nama_lokasi');
    $this->db->group_by('nama_lokasi');
    $master_pos = $this->db->get('data_telemetri')->result_array();

    // 2. Ambil seluruh data transaksi pada tanggal terpilih (Logika seperti Curah Hujan)
    $this->db->where('received_date', $tanggal);
    $query_transaksi = $this->db->get('data_telemetri')->result_array();

    // Mapping data transaksi ke dalam array agar mudah dipanggil
    $transaksi_map = [];
    $latest_update_time = "00:00"; // Inisialisasi waktu terkecil

    foreach ($query_transaksi as $tr) {
        $nama = $tr['nama_lokasi'];
        $time = substr($tr['received_time'], 0, 5); // Ambil Jam:Menit

        if (!isset($transaksi_map[$nama])) {
            $transaksi_map[$nama] = [
                'jam_data' => [],
                'last_val' => $tr['w_level'],
                'last_time' => $time,
                'siaga' => [
                    'hijau' => $tr['siaga_hijau'],
                    'kuning' => $tr['siaga_kuning'],
                    'merah' => $tr['siaga_merah']
                ]
            ];
        }

        // Simpan data berdasarkan jam untuk kolom 06, 12, 18
        $hour = (int)substr($time, 0, 2);
        $transaksi_map[$nama]['jam_data'][$hour] = $tr['w_level'];

        // Cari waktu terbaru secara keseluruhan (Logika Sinkronisasi)
        if ($time > $latest_update_time) {
            $latest_update_time = $time;
        }
    }

    $pencatatan_tma = [];
    $total_w_level = 0;
    $max_w_level = 0;
    $count_merah = 0;

    foreach ($master_pos as $key => $pos) {
        $nama = $pos['nama_lokasi'];
        $ada_data = isset($transaksi_map[$nama]);

        // Ambil data manual (Jika ada)
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
        $siaga = $ada_data ? $transaksi_map[$nama]['siaga'] : ['hijau'=>0, 'kuning'=>0, 'merah'=>0];

        // Statistik
        if ($last_val > $max_w_level) $max_w_level = $last_val;
        $total_w_level += $last_val;
        if ($ada_data && $last_val >= $siaga['merah'] && $siaga['merah'] > 0) $count_merah++;

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

    // Logika Sinkronisasi: Gabungkan Tanggal + Waktu Terbaru yang ditemukan
    $data['last_update'] = date('d M Y', strtotime($tanggal)) . ", " . $latest_update_time . " WIB";

    $data['summary'] = [
        'pos_aktif'     => count($transaksi_map),
        'total_pos'     => count($master_pos),
        'tma_tertinggi' => $max_w_level,
        'tma_rata_rata' => count($master_pos) > 0 ? ($total_w_level / count($master_pos)) : 0,
        'status_siaga'  => $count_merah, // Memperbaiki error Line 35 di v_tma.php
        'siaga_merah'   => $count_merah,
        'status_aman'   => count($master_pos) - $count_merah
    ];

    $data['pencatatan_tma'] = $pencatatan_tma;

    $this->load->view('layout/v_header', $data);
    $this->load->view('pages/v_tma', $data);
    $this->load->view('layout/v_footer', $data);
}

}