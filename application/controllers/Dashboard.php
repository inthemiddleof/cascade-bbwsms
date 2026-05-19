<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
    }

    private function _sync_telemetri() {
        $url = "https://sdatelemetry.com/API_ap_telemetry/datatelemetry2.php?idbbws=12";
        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $json_data = @file_get_contents($url, false, $ctx);
        
        if ($json_data === FALSE) return;
        
        $response = json_decode($json_data, true);
        if (isset($response['telemetryjakarta'])) {
            foreach ($response['telemetryjakarta'] as $row) {
                $this->db->select('id_pos');
                $this->db->where('device_id_telemetry', $row['nama_lokasi']);
                $master = $this->db->get('master_pos')->row_array();

                if ($master) {
                    $id_pos = $master['id_pos'];
                    $received_at = date('Y-m-d H:i:s', strtotime($row['ReceivedDate'] . ' ' . $row['ReceivedTime']));

                    $insert_data = [
                        'id_pos'      => $id_pos,
                        'received_at' => $received_at,
                        'rain'        => (float)$row['Rain'],
                        'wlevel'      => (float)$row['WLevel'],
                        'batt'        => (float)$row['batt'],
                        'status'      => $row['status']
                    ];
                
                    $this->db->where(['id_pos' => $id_pos, 'received_at' => $received_at]);
                    if ($this->db->get('data_telemetri')->num_rows() == 0) {
                        $this->db->insert('data_telemetri', $insert_data);
                    }
                }
            }
        }
    }

    public function index() {
        $this->_sync_telemetri();
    
        // 1. DATA UNTUK STATUS HEADER (PDA & PCH Terbaru)
        $latest_tma = $this->db->select('t.*, m.nama_pos, m.siaga1, m.siaga2, m.siaga3')
                               ->from('data_telemetri t')
                               ->join('master_pos m', 't.id_pos = m.id_pos')
                               ->where('m.tipe_pos', 'PDA')
                               ->order_by('t.received_at', 'DESC')->limit(1)->get()->row_array();
    
        $latest_rain = $this->db->select('t.*, m.nama_pos')
                                ->from('data_telemetri t')
                                ->join('master_pos m', 't.id_pos = m.id_pos')
                                ->where('m.tipe_pos', 'PCH')
                                ->order_by('t.received_at', 'DESC')->limit(1)->get()->row_array();
    
        // 2. QUERY INTEGRASI DATA MANUAL PETUGAS (Untuk Marker Bendungan di Peta)
        // Kita ambil data Master Pos yang is_bendungan = 1, lalu join ke data_manual & data_bendungan terbaru
        $this->db->select('
            m.id_pos, m.nama_pos, m.lat, m.lng, m.nwl, m.siaga1, m.siaga2, m.siaga3,
            dm.rain as curah_hujan_manual, 
            dm.wlevel as tma_manual, 
            dm.tanggal_input as tgl_manual,
            db.elevasi, db.volume, db.inflow, db.total_outflow, db.tanggal_input as tgl_bendungan
        ');
        $this->db->from('master_pos m');
        // Join data harian manual terbaru
        $this->db->join('(SELECT id_pos, rain, wlevel, tanggal_input FROM data_manual WHERE id_manual IN (SELECT MAX(id_manual) FROM data_manual GROUP BY id_pos)) dm', 'm.id_pos = dm.id_pos', 'left');
        // Join parameter teknis bendungan terbaru
        $this->db->join('(SELECT id_pos, elevasi, volume, inflow, total_outflow, tanggal_input FROM data_bendungan WHERE id_bendungan IN (SELECT MAX(id_bendungan) FROM data_bendungan GROUP BY id_pos)) db', 'm.id_pos = db.id_pos', 'left');
        $this->db->where('m.is_bendungan', 1);
        $bendungan_db = $this->db->get()->result_array();

        // 3. MEMBACA SEMUA FILE GEOJSON (Untuk Batas Wilayah & Titik Statis)
        $path_ws = APPPATH . 'views/pages/WS_di_Prov Lampung.geojson';
        $path_inv_bendungan = APPPATH . 'views/pages/inventaris_bendungan.geojson';
        $path_bendung = APPPATH . 'views/pages/bendung_irigasi.geojson';
        $path_das = APPPATH . 'views/pages/DAS_di_Lampung.geojson';

        $ws_geojson_data = file_exists($path_ws) ? file_get_contents($path_ws) : "null";
        $bendungan_geojson_data = file_exists($path_inv_bendungan) ? file_get_contents($path_inv_bendungan) : "null";
        $bendung_geojson_data = file_exists($path_bendung) ? file_get_contents($path_bendung) : "null";
        $das_geojson_data = file_exists($path_das) ? file_get_contents($path_das) : "null";

        $data = [
            'app_name'          => "Hydrosmart",
            'title'             => "BBWS MESUJI SEKAMPUNG",
            'bendungan_db'      => $bendungan_db, // Data dinamis dari database (Manual Petugas)
            'ws_geojson'        => $ws_geojson_data,
            'bendungan_geojson' => $bendungan_geojson_data,
            'bendung_geojson'   => $bendung_geojson_data,
            'das_geojson'       => $das_geojson_data,
            'dam_status'        => [
                'nama'   => !empty($latest_tma['nama_pos']) ? $latest_tma['nama_pos'] : 'Pos Belum Tersedia',
                'level'  => number_format($latest_tma['wlevel'] ?? 0, 2),
                'status' => "NORMAL",
                'trend'  => "Tren Muka Air: " . (!empty($latest_tma['status']) ? $latest_tma['status'] : 'Stabil')
            ],
            'weather_data'      => [
                'kondisi'  => (!empty($latest_rain['rain']) && $latest_rain['rain'] > 0) ? 'Hujan' : 'Cerah Berawan',
                'curah'    => $latest_rain['rain'] ?? '0',
                'prediksi' => 'Update: ' . (!empty($latest_rain['received_at']) ? date('H:i:s', strtotime($latest_rain['received_at'])) : date('H:i:s'))
            ]
        ];
    
        $this->load->view('layout/v_header_beranda', $data);
        $this->load->view('pages/v_beranda', $data);
        $this->load->view('layout/v_footer', $data);
    }
}