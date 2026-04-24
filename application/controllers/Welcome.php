<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Memastikan helper url terload untuk base_url()
        $this->load->helper('url');
    }

    /**
     * Halaman Beranda
     */
    public function index() {
        $data = [
            'app_name' => "CASCADE",
            'title'    => "Sistem Informasi Hidrologi",
            'hero_bg'  => "https://images.unsplash.com/photo-1545459723-861eb1bb3809?q=80&w=1920&auto=format&fit=crop",
            'dam_status' => [
                'nama' => 'Bendungan Margatiga',
                'level' => '12.45',
                'status' => 'Waspada',
                'trend' => 'naik'
            ],
            'weather_data' => [
                'kondisi' => 'Hujan Sedang',
                'curah' => '45',
                'prediksi' => 'Berawan dalam 3 jam'
            ]
        ];

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_beranda', $data);
        $this->load->view('layout/v_footer', $data);
    }

    /**
     * Halaman Monitoring Curah Hujan
     */
    public function curah_hujan() {
        // 1. Ambil input tanggal dari GET (Fitur Kalender)
        // Jika tidak ada, default ke tanggal hari ini
        $tanggal = $this->input->get('tanggal');
            if (!$tanggal) {
                $tanggal = date('Y-m-d');
            }
        // 2. Persiapan Data Meta & UI
        $data['app_name']      = "CASCADE";
        $data['title']         = "Data Curah Hujan";
        $data['tanggal_pilih'] = $tanggal;
        
        // Simulasi waktu update terakhir (bisa diambil dari created_at terbaru di DB)
        $data['last_update']   = date('d M Y, H:i') . " WIB";

        // 3. Simulasi Data Summary / Analisis Cepat
        // Nantinya nilai ini bisa didapat dari query SQL (SUM, AVG, MAX)
        $data['summary'] = [
            'pos_aktif'   => 32,
            'total_pos'   => 35,
            'max_hulu'    => ($tanggal == date('Y-m-d')) ? 16.0 : 5.4, // Contoh fluktuasi data
            'max_hilir'   => ($tanggal == date('Y-m-d')) ? 2.5 : 0.0,
            'avg_wilayah' => 4.2
        ];

        // 4. Simulasi Data Pencatatan Wilayah HULU
        $data['pencatatan_hulu'] = [
            [
                'no' => 1, 
                'pos' => 'Parangjoho - KAB. WONOGIRI', 
                'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 
                'total' => 0, 'manual' => 0.0
            ],
            [
                'no' => 2, 
                'pos' => 'SongPutri - KAB. WONOGIRI', 
                'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 
                'total' => 0, 'manual' => 0.0
            ],
            [
                'no' => 3, 
                'pos' => 'Colo Weir CH - KAB. SUKOHARJO', 
                'w1' => 0, 'w2' => 0, 'w3' => 15.0, 'w4' => 1.0, 
                'total' => 16.0, 'manual' => 14.2
            ],
            [
                'no' => 4, 
                'pos' => 'Sidorejo - KAB. WONOGIRI', 
                'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 
                'total' => 0, 'manual' => 0.0
            ]
        ];

        // 5. Simulasi Data Pencatatan Wilayah HILIR
        $data['pencatatan_hilir'] = [
            [
                'no' => 1, 
                'pos' => 'Pekalongan - KAB. LAMPUNG TIMUR', 
                'w1' => 0, 'w2' => 2.5, 'w3' => 0, 'w4' => 0, 
                'total' => 2.5, 'manual' => 2.0
            ],
            [
                'no' => 2, 
                'pos' => 'Batanghari - KAB. LAMPUNG TIMUR', 
                'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 
                'total' => 0, 'manual' => 0.0
            ]
        ];

        // 6. Load View
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_curah_hujan', $data);
        $this->load->view('layout/v_footer', $data);
    }

    public function tma() {
        $tanggal = $this->input->get('tanggal');
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        $data['app_name']      = "CASCADE";
        $data['title']         = "Tinggi Muka Air";
        $data['tanggal_pilih'] = $tanggal;
        $data['last_update']   = date('d M Y, H:i') . " WIB";

        $data['summary'] = [
            'pos_aktif'     => 12,
            'total_pos'     => 12,
            'tma_tertinggi' => 10.40,
            'status_siaga'  => 1
        ];

        // Data simulasi dengan struktur jam lengkap sesuai gambar
        $data['pencatatan_tma'] = [
                [
                    'no' => 1,
                    'pos' => 'BENDUNGAN MARGATIGA',
                    'telemetri' => ['06' => 10.20, '12' => 10.45, '18' => 10.40, 'last' => 10.42],
                    'manual'    => ['06' => 10.15, '12' => 10.40, '18' => 10.40],
                    'siaga'     => ['hijau' => 11.50, 'kuning' => 12.50, 'merah' => 13.50]
                ],
            ];

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_tma', $data);
        $this->load->view('layout/v_footer', $data);
    }

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

}