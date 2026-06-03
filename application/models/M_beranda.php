<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_beranda extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // SYNC TELEMETRI
    // ==========================================
    public function sync_telemetri() {
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

    // ==========================================
    // GET BENDUNGAN DATA (dari database)
    // ==========================================
    public function get_bendungan_data() {
        $this->db->select('
            m.id_pos, m.nama_pos, m.lat, m.lng, m.nwl, m.sungai,
            dm.rain as curah_hujan_manual, 
            dm.wlevel as tma_manual, 
            dm.tanggal_input as tgl_manual,
            db.elevasi, db.volume, db.inflow, db.total_outflow, 
            db.tanggal_input as tgl_bendungan
        ');
        $this->db->from('master_pos m');
        $this->db->join('(SELECT id_pos, rain, wlevel, tanggal_input FROM data_manual WHERE id_manual IN (SELECT MAX(id_manual) FROM data_manual GROUP BY id_pos)) dm', 'm.id_pos = dm.id_pos', 'left');
        $this->db->join('(SELECT id_pos, elevasi, volume, inflow, total_outflow, tanggal_input FROM data_bendungan WHERE id_bendungan IN (SELECT MAX(id_bendungan) FROM data_bendungan GROUP BY id_pos)) db', 'm.id_pos = db.id_pos', 'left');
        $this->db->where('m.is_bendungan', 1);
        $this->db->where('m.is_bendung', 0);
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET BENDUNG DATA (dari database, bukan GeoJSON)
    // ==========================================
    public function get_bendung_data() {
        $this->db->select('
            m.id_pos, m.nama_pos, m.lat, m.lng, m.sungai,
            db.rain, db.elevasi_mercu, db.q_total, db.q_fc1, db.q_fc2, 
            db.q_limpas, db.q_spam_kpbu, db.sluice_gate,
            db.tanggal_input, db.created_at
        ');
        $this->db->from('master_pos m');
        $this->db->join('(SELECT id_pos, rain, elevasi_mercu, q_total, q_fc1, q_fc2, q_limpas, q_spam_kpbu, sluice_gate, tanggal_input, created_at FROM data_bendung WHERE id_bendung IN (SELECT MAX(id_bendung) FROM data_bendung GROUP BY id_pos)) db', 'm.id_pos = db.id_pos', 'left');
        $this->db->where('m.is_bendung', 1);
        $this->db->order_by('m.nama_pos', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET PCH DATA
    // ==========================================
    public function get_pch_data($selected_date) {
        $this->db->select('m.id_pos, m.nama_pos, m.lat, m.lng, t.rain as ch_hari_ini, t.received_at as tgl_terakhir');
        $this->db->from('master_pos m');
        $this->db->join("(
            SELECT t1.id_pos, t1.rain, t1.received_at 
            FROM data_telemetri t1
            INNER JOIN (
                SELECT id_pos, MAX(received_at) as max_time 
                FROM data_telemetri 
                WHERE DATE(received_at) = '$selected_date' 
                GROUP BY id_pos
            ) t2 ON t1.id_pos = t2.id_pos AND t1.received_at = t2.max_time
        ) t", 't.id_pos = m.id_pos', 'left');
        $this->db->where('m.tipe_pos', 'PCH');
        $this->db->where('m.is_bendungan', 0);
        $this->db->where('m.is_bendung', 0);
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET PDA DATA
    // ==========================================
    public function get_pda_data($selected_date) {
        $this->db->select('m.id_pos, m.nama_pos, m.lat, m.lng, t.wlevel as tma_sekarang, t.status as status_siaga, t.received_at as tgl_terakhir');
        $this->db->from('master_pos m');
        $this->db->join("(
            SELECT t1.id_pos, t1.wlevel, t1.status, t1.received_at 
            FROM data_telemetri t1
            INNER JOIN (
                SELECT id_pos, MAX(received_at) as max_time 
                FROM data_telemetri 
                WHERE DATE(received_at) = '$selected_date' 
                GROUP BY id_pos
            ) t2 ON t1.id_pos = t2.id_pos AND t1.received_at = t2.max_time
        ) t", 't.id_pos = m.id_pos', 'left');
        $this->db->where('m.tipe_pos', 'PDA');
        $this->db->where('m.is_bendungan', 0);
        $this->db->where('m.is_bendung', 0);
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET GEOJSON DATA (WS & DAS saja, Bendung diambil dari DB)
    // ==========================================
    public function get_geojson_data() {
        $path_ws  = FCPATH . 'assets/geojson/WS_Lampung.geojson';
        $path_das = FCPATH . 'assets/geojson/DAS_Lampung.geojson';
        
        return [
            'ws_geojson'  => file_exists($path_ws) ? file_get_contents($path_ws) : 'null',
            'das_geojson' => file_exists($path_das) ? file_get_contents($path_das) : 'null',
        ];
    }

    // ==========================================
    // MAIN: GET DASHBOARD SUMMARY
    // ==========================================
    public function get_dashboard_summary($selected_date) {
        $bendungan_db = $this->get_bendungan_data();
        $bendung_db   = $this->get_bendung_data();  // ⬅️ Dari database, bukan GeoJSON
        $pch_db       = $this->get_pch_data($selected_date);
        $pda_db       = $this->get_pda_data($selected_date);
        $geojson      = $this->get_geojson_data();
        
        // Hitung jumlah bendung dari database
        $bendung_count = count($bendung_db);
        
        // Latest TMA
        $latest_tma = $this->db->select('t.wlevel, t.status, m.nama_pos')
                               ->from('data_telemetri t')
                               ->join('master_pos m', 't.id_pos = m.id_pos')
                               ->where('m.tipe_pos', 'PDA')
                               ->order_by('t.received_at', 'DESC')
                               ->limit(1)
                               ->get()->row_array();
        
        // Latest Rain
        $latest_rain = $this->db->select('t.rain, t.received_at')
                                ->from('data_telemetri t')
                                ->join('master_pos m', 't.id_pos = m.id_pos')
                                ->where('m.tipe_pos', 'PCH')
                                ->order_by('t.received_at', 'DESC')
                                ->limit(1)
                                ->get()->row_array();
        
        return [
            'bendungan_db'      => $bendungan_db,
            'bendung_db'        => $bendung_db,       // ⬅️ Data bendung dari DB
            'pch_db'            => $pch_db,
            'pda_db'            => $pda_db,
            'bendung_count'     => $bendung_count,     // ⬅️ Hitung dari DB
            'ws_geojson'        => $geojson['ws_geojson'],
            'bendung_geojson'   => json_encode([       // ⬅️ Konversi data DB ke format GeoJSON
                'type'     => 'FeatureCollection',
                'features' => array_map(function($b) {
                    return [
                        'type'       => 'Feature',
                        'geometry'   => [
                            'type'        => 'Point',
                            'coordinates' => [(float)$b['lng'], (float)$b['lat']]
                        ],
                        'properties' => [
                            'Nama Bendung'              => $b['nama_pos'],
                            'Nama DI'                   => $b['sungai'] ?? '-',
                            'Kab/ Kota'                 => '-',
                            'Kecamatan'                 => '-',
                            'Sumber Air'                => '-',
                            'Kapasitas Debit (m3/s)'    => $b['q_total'] ?? 0,
                            'Luas Layanan (Ha)'         => 0,
                        ]
                    ];
                }, array_filter($bendung_db, function($b) { return !empty($b['lat']) && !empty($b['lng']); }))
            ]),
            'das_geojson'       => $geojson['das_geojson'],
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
    }
}