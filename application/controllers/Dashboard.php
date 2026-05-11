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
    
        // 1. Data TMA & Curah Hujan Terbaru untuk Dashboard
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
    
        // 2. Status Siaga
        $status_bendungan = "NORMAL";
        if (!empty($latest_tma)) {
            $lvl = (float)$latest_tma['wlevel'];
            if ($lvl >= (float)$latest_tma['siaga1'] && (float)$latest_tma['siaga1'] > 0) $status_bendungan = "BAHAYA";
            elseif ($lvl >= (float)$latest_tma['siaga2'] && (float)$latest_tma['siaga2'] > 0) $status_bendungan = "SIAGA";
            elseif ($lvl >= (float)$latest_tma['siaga3'] && (float)$latest_tma['siaga3'] > 0) $status_bendungan = "WASPADA";
        }
    
        // 3. Koordinat Bendungan (Data Statis untuk Overlay)
        $koordinat_bendungan = [
            ['nama' => 'Bendungan Batutegi', 'lat' => -5.255509, 'lng' => 104.778750],
            ['nama' => 'Bendungan Sekampung', 'lat' => -5.333278, 'lng' => 104.918360],
            ['nama' => 'Bendungan Way Rarem', 'lat' => -4.927468, 'lng' => 104.786946],
            ['nama' => 'Bendungan Way Jepara', 'lat' => -5.211567, 'lng' => 105.672737],
            ['nama' => 'Bendungan Marga Tiga', 'lat' => -5.207558, 'lng' => 105.487426],
        ];
    
        foreach ($koordinat_bendungan as &$bend) {
            $res = $this->db->select('t.wlevel, t.rain, t.received_at')
                            ->from('data_telemetri t')
                            ->join('master_pos m', 't.id_pos = m.id_pos')
                            ->where('m.nama_pos LIKE', '%' . str_replace('Bendungan ', '', $bend['nama']) . '%')
                            ->order_by('t.received_at', 'DESC')->limit(1)->get()->row_array();
            
            $bend['wlevel'] = $res['wlevel'] ?? '0.00';
            $bend['rain'] = $res['rain'] ?? '0.00';
            $bend['last_update'] = isset($res['received_at']) ? date('H:i', strtotime($res['received_at'])) : '-';
        }
    
        // 4. MEMBACA FILE GEOJSON (Wilayah Sungai, Inventaris Bendungan, & Bendung Irigasi)
        $path_ws = APPPATH . 'views/pages/WS_di_Prov Lampung.geojson';
        $path_inv_bendungan = APPPATH . 'views/pages/inventaris_bendungan.geojson';
        $path_bendung = APPPATH . 'views/pages/bendung_irigasi.geojson'; // Lokasi file Bendung Irigasi
        $path_das = APPPATH . 'views/pages/DAS_di_Lampung.geojson';

        $ws_geojson_data = file_exists($path_ws) ? file_get_contents($path_ws) : "null";
        $bendungan_geojson_data = file_exists($path_inv_bendungan) ? file_get_contents($path_inv_bendungan) : "null";
        $bendung_geojson_data = file_exists($path_bendung) ? file_get_contents($path_bendung) : "null";
        $das_geojson_data = file_exists($path_das) ? file_get_contents($path_das) : "null";

        // 5. Menyusun Data ke View
        $data = [
            'app_name'          => "Hydrosmart",
            'title'             => "BBWS MESUJI SEKAMPUNG",
            'map_data'          => $koordinat_bendungan,
            'ws_geojson'        => $ws_geojson_data,
            'bendungan_geojson' => $bendungan_geojson_data,
            'bendung_geojson'   => $bendung_geojson_data, // Data baru untuk Bendung Irigasi
            'das_geojson'       => $das_geojson_data,
            'dam_status'        => [
                'nama'   => !empty($latest_tma['nama_pos']) ? $latest_tma['nama_pos'] : 'Pos Belum Tersedia',
                'level'  => number_format($latest_tma['wlevel'] ?? 0, 2),
                'status' => $status_bendungan,
                'trend'  => "Tren Muka Air: " . (!empty($latest_tma['status']) ? $latest_tma['status'] : 'Stabil')
            ],
            'weather_data'      => [
                'kondisi'  => (!empty($latest_rain['rain']) && $latest_rain['rain'] > 0) ? 'Hujan' : 'Cerah Berawan',
                'curah'    => $latest_rain['rain'] ?? '0',
                'prediksi' => 'Terakhir Update: ' . (!empty($latest_rain['received_at']) ? date('H:i:s', strtotime($latest_rain['received_at'])) : date('H:i:s'))
            ]
        ];
    
        $this->load->view('layout/v_header_beranda', $data);
        $this->load->view('pages/v_beranda', $data);
        $this->load->view('layout/v_footer', $data);
    }
}