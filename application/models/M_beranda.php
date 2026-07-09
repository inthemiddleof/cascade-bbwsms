<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_beranda extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

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

                    $rain = (float)$row['Rain'];
                    $wlevel_cm = (float)$row['WLevel'];
                    
                    if ($row['id_tipe'] == 'PCH' && $wlevel_cm == 650.000) {
                        $wlevel_cm = 0;
                    }

                    $insert_data = [
                        'id_pos'      => $id_pos,
                        'received_at' => $received_at,
                        'rain'        => $rain,
                        'wlevel'      => cm_to_m($wlevel_cm),
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
    // GET BENDUNGAN DATA - pakai jenis_aset (DENGAN KOLOM BARU)
    // ==========================================
    public function get_bendungan_data() {
        $this->db->select('
            m.id_pos, 
            m.nama_pos, 
            m.lat, 
            m.lng, 
            m.nwl, 
            m.sungai, 
            m.wilayah_sungai,
            db.rain, 
            db.elevasi, 
            db.volume, 
            db.inflow, 
            db.total_outflow, 
            db.tanggal_input as tgl_bendungan, 
            db.created_at,
            db.tahun_mulai_pembangunan,
            db.tipe_bendungan,
            db.elevasi_mercu,
            db.luas_das
        ');
        $this->db->from('master_pos m');
        $this->db->join('(
            SELECT 
                id_pos, 
                rain, 
                elevasi, 
                volume, 
                inflow, 
                total_outflow, 
                tanggal_input, 
                created_at,
                tahun_mulai_pembangunan,
                tipe_bendungan,
                elevasi_mercu,
                luas_das
            FROM data_bendungan 
            WHERE id_bendungan IN (SELECT MAX(id_bendungan) FROM data_bendungan GROUP BY id_pos)
        ) db', 'm.id_pos = db.id_pos', 'left');
        $this->db->where('m.jenis_aset', 'bendungan');
        $this->db->order_by('m.nama_pos', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET BENDUNG DATA - pakai jenis_aset (SESUAI STRUKTUR TERBARU)
    // ==========================================
    public function get_bendung_data() {
        $this->db->select('
            m.id_pos, 
            m.nama_pos, 
            m.lat, 
            m.lng, 
            m.sungai, 
            m.wilayah_sungai,
            db.rain, 
            db.elevasi_mercu, 
            db.q_total, 
            db.q_fc1, 
            db.q_fc2, 
            db.q_sal_induk,
            db.q_limpas, 
            db.q_sungai,
            db.q_spam_kpbu, 
            db.sluice_gate,
            db.bukaan_pintu,
            db.tanggal_input, 
            db.created_at
        ');
        $this->db->from('master_pos m');
        $this->db->join('(
            SELECT 
                id_pos, 
                rain, 
                elevasi_mercu, 
                q_total, 
                q_fc1, 
                q_fc2, 
                q_sal_induk,
                q_limpas, 
                q_sungai,
                q_spam_kpbu, 
                sluice_gate,
                bukaan_pintu,
                tanggal_input, 
                created_at 
            FROM data_bendung 
            WHERE id_bendung IN (SELECT MAX(id_bendung) FROM data_bendung GROUP BY id_pos)
        ) db', 'm.id_pos = db.id_pos', 'left');
        $this->db->where('m.jenis_aset', 'bendung');
        $this->db->order_by('m.nama_pos', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET EMBUNG DATA - pakai jenis_aset
    // ==========================================
    public function get_embung_data() {
        $this->db->select('
            m.id_pos,
            m.nama_pos,
            m.lat,
            m.lng,
            m.sungai,
            m.wilayah_sungai,
            e.id_embung,
            e.kapasitas_volume,
            e.elevasi_puncak,
            e.tinggi_embung,
            e.panjang_tubuh,
            e.tahun_mulai_pembangunan,
            e.created_at
        ');
        $this->db->from('master_pos m');
        $this->db->join('(
            SELECT id_pos, id_embung, kapasitas_volume, elevasi_puncak, tinggi_embung, panjang_tubuh, tahun_mulai_pembangunan, created_at 
            FROM data_embung 
            WHERE id_embung IN (SELECT MAX(id_embung) FROM data_embung GROUP BY id_pos)
        ) e', 'm.id_pos = e.id_pos', 'left');
        $this->db->where('m.jenis_aset', 'embung');
        $this->db->order_by('m.nama_pos', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET PCH DATA - pakai jenis_aset (SEMUA PCH)
    // ==========================================
    public function get_pch_data($selected_date) {
        $this->db->select('
            m.id_pos, 
            m.nama_pos, 
            m.lat, 
            m.lng, 
            m.wilayah_sungai,
            m.sungai,
            t.rain as ch_hari_ini, 
            t.received_at as tgl_terakhir
        ');
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
        $this->db->where('m.jenis_aset', 'pch');
        $this->db->order_by('m.nama_pos', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET PDA DATA - pakai jenis_aset (SEMUA PDA)
    // ==========================================
    public function get_pda_data($selected_date) {
        $this->db->select('
            m.id_pos, 
            m.nama_pos, 
            m.lat, 
            m.lng, 
            m.wilayah_sungai,
            m.sungai,
            t.wlevel as tma_sekarang, 
            t.status as status_siaga, 
            t.received_at as tgl_terakhir
        ');
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
        $this->db->where('m.jenis_aset', 'pda');
        $this->db->order_by('m.nama_pos', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET GEOJSON DATA (WS & DAS)
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
    // GET PENGAMAN PANTAI DATA
    // ==========================================
    public function get_pengaman_pantai_data() {
        $this->db->select('
            id_pengaman,
            kode_integrasi,
            nama_aset,
            jenis_bangunan,
            sungai,
            wilayah_sungai,
            lat_awal,
            lng_awal,
            lat_akhir,
            lng_akhir,
            panjang,
            elevasi_puncak,
            lebar_puncak,
            tahun_dibangun,
            kabupaten_kota,
            kecamatan,
            kelurahan,
            manfaat,
            created_at
        ');
        $this->db->from('data_pengaman_pantai');
        $this->db->order_by('nama_aset', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET PENGENDALI SEDIMEN DATA
    // ==========================================
    public function get_pengendali_sedimen_data() {
        $this->db->select('
            id_sedimen,
            kode_integrasi,
            nama_aset,
            jenis_bangunan,
            sungai,
            daerah_aliran_sungai,
            wilayah_sungai,
            lat,
            lng,
            daya_tampung,
            panjang,
            lebar,
            tinggi,
            tahun_dibangun,
            kabupaten_kota,
            kecamatan,
            kelurahan,
            jenis_material,
            keterangan,
            created_at
        ');
        $this->db->from('data_pengendali_sedimen');
        $this->db->order_by('nama_aset', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET DAERAH IRIGASI DATA
    // ==========================================
    public function get_irigasi_data() {
        $this->db->select('
            id_irigasi,
            kode_integrasi,
            nama_aset,
            jenis_daerah_irigasi,
            wilayah_sungai,
            daerah_aliran_sungai,
            provinsi,
            kabupaten_kota,
            kecamatan,
            kelurahan,
            latitude,
            longitude,
            luas_permen,
            luas_baku,
            luas_potensial,
            luas_fungsional,
            jenis_bangunan_utama,
            sumber_air,
            status_pemeliharaan,
            di_op_kan_oleh,
            deskripsi_aset,
            created_at
        ');
        $this->db->from('data_irigasi');
        $this->db->order_by('nama_aset', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // MAIN: GET DASHBOARD SUMMARY
    // ==========================================
    public function get_dashboard_summary($selected_date) {
        $bendungan_db = $this->get_bendungan_data();
        $bendung_db   = $this->get_bendung_data();
        $embung_db    = $this->get_embung_data();
        $pengaman_db  = $this->get_pengaman_pantai_data();
        $sedimen_db   = $this->get_pengendali_sedimen_data();
        $irigasi_db   = $this->get_irigasi_data();
        $pch_db       = $this->get_pch_data($selected_date);
        $pda_db       = $this->get_pda_data($selected_date);
        $geojson      = $this->get_geojson_data();
        
        $bendung_count = count($bendung_db);
        $embung_count  = count($embung_db);
        $pengaman_count = count($pengaman_db);
        $sedimen_count = count($sedimen_db); 
        $irigasi_count = count($irigasi_db);
        
        // Latest TMA
        $latest_tma = $this->db->select('t.wlevel, t.status, m.nama_pos')
                               ->from('data_telemetri t')
                               ->join('master_pos m', 't.id_pos = m.id_pos')
                               ->where('m.jenis_aset', 'pda')
                               ->order_by('t.received_at', 'DESC')
                               ->limit(1)
                               ->get()->row_array();
        
        // Latest Rain
        $latest_rain = $this->db->select('t.rain, t.received_at')
                                ->from('data_telemetri t')
                                ->join('master_pos m', 't.id_pos = m.id_pos')
                                ->where('m.jenis_aset', 'pch')
                                ->order_by('t.received_at', 'DESC')
                                ->limit(1)
                                ->get()->row_array();
        
        return [
            'bendungan_db'      => $bendungan_db,
            'bendung_db'        => $bendung_db,
            'embung_db'         => $embung_db,
            'pengaman_db'       => $pengaman_db,
            'sedimen_db'        => $sedimen_db,
            'irigasi_db'        => $irigasi_db,
            'pch_db'            => $pch_db,
            'pda_db'            => $pda_db,
            'bendung_count'     => $bendung_count,
            'embung_count'      => $embung_count,
            'pengaman_count'    => $pengaman_count,
            'sedimen_count'     => $sedimen_count,
            'irigasi_count'     => $irigasi_count,
            'ws_geojson'        => $geojson['ws_geojson'],
            'bendung_geojson'   => json_encode([
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