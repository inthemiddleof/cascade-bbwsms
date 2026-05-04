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
        
        if ($json_data === FALSE) {
            return;
        }
    
        $response = json_decode($json_data, true);
    
        if (isset($response['telemetryjakarta'])) {
            $data_api = $response['telemetryjakarta'];
            
            foreach ($data_api as $row) {
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
                
                    $this->db->where([
                        'id_pos'      => $id_pos, 
                        'received_at' => $received_at
                    ]);
                    $exists = $this->db->get('data_telemetri')->num_rows();
                
                    if ($exists == 0) {
                        $this->db->insert('data_telemetri', $insert_data);
                    }
                }
            }
        }
    }

    public function index() {
        $this->_sync_telemetri();

        $this->db->select('t.*, m.nama_pos, m.siaga1, m.siaga2, m.siaga3');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('m.tipe_pos', 'PDA');
        $this->db->order_by('t.received_at', 'DESC');
        $this->db->limit(1);
        $latest_tma = $this->db->get()->row_array();
        $this->db->select('t.*, m.nama_pos');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('m.tipe_pos', 'PCH');
        $this->db->order_by('t.received_at', 'DESC');
        $this->db->limit(1);
        $latest_rain = $this->db->get()->row_array();
    
        $status_bendungan = "NORMAL";
        if (!empty($latest_tma)) {
            $lvl = (float)$latest_tma['wlevel'];
            
            if ($lvl >= (float)$latest_tma['siaga1'] && (float)$latest_tma['siaga1'] > 0) {
                $status_bendungan = "BAHAYA";
            } elseif ($lvl >= (float)$latest_tma['siaga2'] && (float)$latest_tma['siaga2'] > 0) {
                $status_bendungan = "SIAGA";
            } elseif ($lvl >= (float)$latest_tma['siaga3'] && (float)$latest_tma['siaga3'] > 0) {
                $status_bendungan = "WASPADA";
            }
        }

        $koordinat_bendungan = [
        ['nama' => 'Bendungan Batutegi', 'lat' => -5.251021126560068, 'lng' => 104.77911027418415],
        ['nama' => 'Bendungan Sekampung', 'lat' => -5.3521978233079235, 'lng' => 104.91809621347446],
        ['nama' => 'Bendungan Way Rarem', 'lat' => -4.927468011391385, 'lng' => 104.78694612395397],
        ['nama' => 'Bendungan Way Jepara', 'lat' => -5.211567462399912, 'lng' => 105.67273735964285],
        ['nama' => 'Bendungan Marga Tiga', 'lat' => -5.207558751020262, 'lng' => 105.48742603587237],
    ];

    // Ambil data telemetri terbaru untuk tiap bendungan (opsional untuk akurasi popup)
    foreach ($koordinat_bendungan as &$bend) {
        $this->db->select('t.wlevel, t.rain, t.received_at');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('m.nama_pos LIKE', '%' . str_replace('Bendungan ', '', $bend['nama']) . '%');
        $this->db->order_by('t.received_at', 'DESC');
        $this->db->limit(1);
        $res = $this->db->get()->row_array();
        
        $bend['wlevel'] = $res['wlevel'] ?? '0.00';
        $bend['rain'] = $res['rain'] ?? '0.00';
        $bend['last_update'] = isset($res['received_at']) ? date('H:i', strtotime($res['received_at'])) : '-';
    }
    
        $data = [
            'app_name' => "CASCADE",
            'title'    => "Sistem Informasi Hidrologi",
            'hero_bg'  => "https://images.unsplash.com/photo-1545459723-861eb1bb3809",
            'map_data' => $koordinat_bendungan,
            'dam_status' => [
                'nama'   => !empty($latest_tma['nama_pos']) ? $latest_tma['nama_pos'] : 'Pos Belum Tersedia',
                'level'  => number_format($latest_tma['wlevel'] ?? 0, 2),
                'status' => $status_bendungan,
                'trend'  => "Tren Muka Air: " . (!empty($latest_tma['status']) ? $latest_tma['status'] : 'Stabil')
            ],
            'weather_data' => [
                'kondisi'  => (!empty($latest_rain['rain']) && $latest_rain['rain'] > 0) ? 'Hujan' : 'Cerah Berawan',
                'curah'    => $latest_rain['rain'] ?? '0',
                'prediksi' => 'Terakhir Update: ' . (!empty($latest_rain['received_at']) ? date('H:i:s', strtotime($latest_rain['received_at'])) : date('H:i:s'))
            ]
        ];
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_beranda', $data);
        $this->load->view('layout/v_footer', $data);
    }
}