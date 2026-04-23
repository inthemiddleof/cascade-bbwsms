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
}