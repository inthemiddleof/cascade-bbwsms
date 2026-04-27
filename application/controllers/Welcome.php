<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Fungsi Sinkronisasi: Menarik data dari API ke tabel data_telemetri
     */
    public function sync_data() {
        // 1. Ambil data dari API
        $url = "https://sdatelemetry.com/API_ap_telemetry/datatelemetry2.php?idbbws=12";
        $response = file_get_contents($url);
    
        // 2. Dekode JSON ke Array
        $json = json_decode($response, true);
    
        // 3. Validasi apakah data berhasil diambil
        if (isset($json['telemetryjakarta']) && is_array($json['telemetryjakarta'])) {
            $data_api = $json['telemetryjakarta'];
    
            // Kosongkan tabel sebelum diisi data terbaru
            $this->db->empty_table('data_telemetri');
    
            foreach ($data_api as $row) {
                $insert_data = [
                    'nama_alat'      => $row['nama_alat'],
                    'device_id'      => $row['id_merk'] . "-" . ($row['nama_lokasi'] ?? 'UNIT'),
                    'nama_lokasi'    => $row['nama_lokasi'],
                    'wilayah_sungai' => $row['sungai'],
                    'lat'            => !empty($row['Lat']) ? (float)$row['Lat'] : null,
                    'lon'            => !empty($row['Lng']) ? (float)$row['Lng'] : null,
                    'received_date'  => $row['ReceivedDate'],
                    'received_time'  => $row['ReceivedTime'],
                    'batt'           => $row['batt'],
                    'rain'           => (float)$row['Rain'],
                    'w_level'        => (float)$row['WLevel'],
                    'id_tipe'        => $row['id_tipe'],
                    'status'         => $row['status'],
                    'siaga_hijau'    => (float)$row['siaga4'], 
                    'siaga_kuning'   => (float)$row['siaga3'],
                    'siaga_merah'    => (float)$row['siaga2'],
                    'created_at'     => date('Y-m-d H:i:s')
                ];
    
                $this->db->insert('data_telemetri', $insert_data);
            }
            echo "Sync Berhasil!";
        } else {
            echo "Gagal mengambil data: Variabel json tidak ditemukan atau format salah.";
        }
    }
  
    public function index() {
        // Panggil sync secara otomatis (silent mode) setiap kali Beranda dibuka
        $this->sync_data(true); 
    
        // --- Ambil Data TMA (PDA) ---
        $this->db->where('id_tipe', 'PDA');
        $this->db->order_by('received_date', 'DESC');
        $this->db->order_by('received_time', 'DESC');
        $latest_tma = $this->db->get('data_telemetri', 1)->row_array();
    
        // --- Ambil Data Hujan (PCH) ---
        $this->db->where('id_tipe', 'PCH');
        $this->db->order_by('received_date', 'DESC');
        $this->db->order_by('received_time', 'DESC');
        $latest_rain = $this->db->get('data_telemetri', 1)->row_array();
    
        // Logika Status Bendungan
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
    
        // Ambil galeri (auto-sync dari folder tetap jalan jika kodenya masih ada)
        $data['galeri'] = $this->db->order_by('created_at', 'DESC')->limit(8)->get('galeri_kegiatan')->result_array();
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_beranda', $data);
        $this->load->view('layout/v_footer', $data);
    }

    /**
     * Halaman Monitoring Curah Hujan
     */
    public function curah_hujan() {
        // Ambil tanggal dari input GET (kalender), jika kosong default null agar bisa tampil semua
        $tanggal = $this->input->get('tanggal');
        
        $data['app_name']      = "CASCADE";
        $data['title']         = "Data Curah Hujan";
        $data['tanggal_pilih'] = $tanggal ?: date('Y-m-d'); // Untuk tampilan di kalender
        
        // Jika user memilih tanggal, filter berdasarkan tanggal tersebut
        if ($tanggal) {
            $this->db->where('received_date', $tanggal);
        }
        
        $res = $this->db->get('data_telemetri');
        $pencatatan = $res->result_array();
    
        $formatted_data = [];
        foreach ($pencatatan as $key => $row) {
            $formatted_data[] = [
                'no'    => $key + 1,
                'pos'   => $row['nama_alat'],
                'w1'    => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0,
                'total' => $row['rain'],
                'manual' => 0.0
            ];
        }
    
        $data['pencatatan_hulu'] = $formatted_data;
        $data['pencatatan_hilir'] = [];
    
        // --- PERBAIKAN WAKTU UPDATE DI SINI ---
        // Cari data dengan waktu paling terbaru di tabel
        $last_entry = $this->db->order_by('received_date', 'DESC')
                               ->order_by('received_time', 'DESC')
                               ->get('data_telemetri', 1)->row_array();
    
        if ($last_entry) {
            // Format: 24 Apr 2026, 04:34 WIB
            $tgl = date('d M Y', strtotime($last_entry['received_date']));
            $jam = date('H:i', strtotime($last_entry['received_time']));
            $data['last_update'] = $tgl . ", " . $jam . " WIB";
        } else {
            $data['last_update'] = date('d M Y, H:i') . " WIB";
        }
        // --------------------------------------
    
        $data['summary'] = [
            'pos_aktif'   => count($pencatatan),
            'total_pos'   => 40,
            'max_hulu'    => (count($pencatatan) > 0) ? max(array_column($pencatatan, 'rain')) : 0,
            'max_hilir'   => 0,
            'avg_wilayah' => (count($pencatatan) > 0) ? array_sum(array_column($pencatatan, 'rain')) / count($pencatatan) : 0
        ];
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_curah_hujan', $data);
        $this->load->view('layout/v_footer', $data);
    }
    /**
     * Halaman Monitoring Tinggi Muka Air
     */
    public function tma() {
        $tanggal = $this->input->get('tanggal');
    
        $data['app_name']      = "CASCADE";
        $data['title']         = "Tinggi Muka Air";
        $data['tanggal_pilih'] = $tanggal ?: date('Y-m-d');
        
        // Filter tanggal jika ada input dari kalender
        if ($tanggal) {
            $this->db->where('received_date', $tanggal);
        }
    
        $res = $this->db->get('data_telemetri');
        $pencatatan = $res->result_array();
    
        $formatted_data = [];
        foreach ($pencatatan as $key => $row) {
            $formatted_data[] = [
                'no' => $key + 1,
                'pos' => $row['nama_alat'],
                'telemetri' => [
                    '06' => 0.00, '12' => 0.00, '18' => 0.00, 
                    'last' => (float)$row['w_level'] 
                ],
                'manual' => ['06' => 0.00, '12' => 0.00, '18' => 0.00],
                'siaga' => [
                    'hijau' => (float)$row['siaga_hijau'], 
                    'kuning' => (float)$row['siaga_kuning'], 
                    'merah' => (float)$row['siaga_merah']
                ]
            ];
        }
    
        $data['pencatatan_tma'] = $formatted_data;
    
        $max_level = 0;
        foreach($pencatatan as $r) { 
            if((float)$r['w_level'] > $max_level) $max_level = (float)$r['w_level']; 
        }
    
        // --- PERBAIKAN WAKTU UPDATE DI SINI ---
        // Ambil data terbaru secara global untuk menentukan waktu update terakhir
        $last_entry = $this->db->order_by('received_date', 'DESC')
                               ->order_by('received_time', 'DESC')
                               ->get('data_telemetri', 1)->row_array();
    
        if ($last_entry) {
            // Format: 24 Apr 2026, 04:34 WIB
            $tgl = date('d M Y', strtotime($last_entry['received_date']));
            $jam = date('H:i', strtotime($last_entry['received_time']));
            $data['last_update'] = $tgl . ", " . $jam . " WIB";
        } else {
            $data['last_update'] = date('d M Y, H:i') . " WIB";
        }
        // --------------------------------------
    
        $data['summary'] = [
            'pos_aktif'     => count($pencatatan),
            'total_pos'     => 40,
            'tma_tertinggi' => $max_level,
            'status_siaga'  => 0
        ];
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_tma', $data);
        $this->load->view('layout/v_footer', $data);
    }
    /**
     * Halaman Monitoring Kualitas Air
     */
    
     public function kualitas_air() {
        $tanggal = $this->input->get('tanggal');
        if (!$tanggal) { $tanggal = date('Y-m-d'); }

        $data['app_name']      = "CASCADE";
        $data['title']         = "Kualitas Air";
        $data['tanggal_pilih'] = $tanggal;
        $data['last_update']   = date('d M Y, H:i') . " WIB";

        $data['summary'] = [
            'pos_aktif'    => 8,
            'total_pos'    => 10,
            'status_aman'  => 6,
            'status_waspada' => 2
        ];

        // Simulasi data parameter kualitas air
        $data['pencatatan_kualitas'] = [
            [
                'no' => 1,
                'pos' => 'STASIUN WAY SEKAMPUNG - HULU',
                'waktu' => '14:20',
                'ph' => 7.2,
                'temp' => 28.5,
                'do' => 6.4,
                'turbidity' => 12.5,
                'tds' => 150,
                'status' => 'BAIK'
            ],
            [
                'no' => 2,
                'pos' => 'STASIUN BATU TEGI',
                'waktu' => '14:15',
                'ph' => 6.1, // pH agak rendah
                'temp' => 27.2,
                'do' => 5.2,
                'turbidity' => 45.0, // Agak keruh
                'tds' => 320,
                'status' => 'CEMAR RINGAN'
            ],
        ];

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_kualitas_air', $data);
        $this->load->view('layout/v_footer', $data);
    }

    public function peta()
{
    // 1. Ambil semua data dari tabel telemetri
    $this->db->order_by('nama_alat', 'ASC');
    $semua_pos = $this->db->get('data_telemetri')->result_array();

    // 2. Hitung Ringkasan (Summary) untuk Sidebar Kanan
    $total_pos = count($semua_pos);
    $online    = 0;
    $offline   = 0;

    foreach ($semua_pos as $pos) {
        if (strtolower($pos['status']) !== 'offline') $online++;
    }

    // 3. Siapkan data untuk dikirim ke View
    $data = [
        'app_name'     => 'CASCADE',
        'title'        => 'Peta Sebaran Stasiun',
        'semua_pos'    => $semua_pos,
        'summary'      => [
            'total'   => $total_pos,
            'online'  => $online,
            'offline' => $total_pos - $online
        ], // Pastikan ada tanda koma di sini
        'current_page' => 'peta' // Baris terakhir dalam array tidak wajib pakai koma, tapi disarankan
    ];

    // 4. Load View
    $this->load->view('layout/v_header', $data);
    $this->load->view('pages/v_peta', $data);
    //$this->load->view('layout/v_footer');
}

}