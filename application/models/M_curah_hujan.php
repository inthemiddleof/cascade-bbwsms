<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_curah_hujan extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_pch_stations() {
        $this->db->where('tipe_pos', 'PCH');
        $this->db->order_by('nama_pos', 'ASC');
        $master_pos = $this->db->get('master_pos')->result_array();
        
        $all_stations = []; 
        foreach ($master_pos as $pos) {
            $all_stations[$pos['id_pos']] = [
                'nama'   => $pos['nama_pos'], 
                'lokasi' => $pos['sungai'] ?? '-'
            ];
        }
        
        return $all_stations;
    }

    public function get_telemetri_data($tanggal, $slots) {
        $this->db->select('t.id_pos, t.rain, t.received_at');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('m.tipe_pos', 'PCH');
        $this->db->where('t.received_at >=', $slots['w1']['start']);
        $this->db->where('t.received_at <=', $slots['w4']['end']);
        $this->db->order_by('t.received_at', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_manual_data($tanggal) {
        $manual_data = [];
        
        if ($this->db->table_exists('data_manual')) {
            $this->db->select('dm.id_pos, dm.rain, dm.created_at, u.nama_lengkap as petugas');
            $this->db->from('data_manual dm');
            $this->db->join('users u', 'dm.id_user = u.id_user', 'left');
            $this->db->where('dm.tanggal_input', $tanggal);
            $this->db->order_by('dm.created_at', 'DESC');
            $query_manual = $this->db->get()->result_array();
            
            foreach ($query_manual as $mn) {
                $id = $mn['id_pos'];
                if (!isset($manual_data[$id])) {
                    $manual_data[$id] = [
                        'rain'       => (float)$mn['rain'],
                        'waktu'      => date('H:i', strtotime($mn['created_at'])),
                        'petugas'    => $mn['petugas'] ?? '-',
                        'created_at' => $mn['created_at']
                    ];
                }
            }
        }
        
        return $manual_data;
    }

    public function process_telemetri($query_telemetri, $slots) {
        $telemetri_map = [];
        $latest_full_time = null;

        foreach ($query_telemetri as $tr) {
            $id = $tr['id_pos'];
            $f_time = $tr['received_at'];
            $rain = (float)($tr['rain'] ?? 0);
            
            if (!isset($telemetri_map[$id])) {
                $telemetri_map[$id] = [
                    'w1' => null, 'w2' => null, 'w3' => null, 'w4' => null,
                    'last_time' => null, 'last_slot' => null
                ];
            }

            if ($latest_full_time === null || $f_time > $latest_full_time) {
                $latest_full_time = $f_time;
            }

            // Ambil data TERAKHIR per slot
            if ($f_time >= $slots['w1']['start'] && $f_time <= $slots['w1']['end']) {
                $telemetri_map[$id]['w1'] = $rain;
                if ($telemetri_map[$id]['last_time'] === null || $f_time > $telemetri_map[$id]['last_time']) {
                    $telemetri_map[$id]['last_time'] = $f_time;
                    $telemetri_map[$id]['last_slot'] = '00.00-06.00';
                }
            } elseif ($f_time >= $slots['w2']['start'] && $f_time <= $slots['w2']['end']) {
                $telemetri_map[$id]['w2'] = $rain;
                if ($telemetri_map[$id]['last_time'] === null || $f_time > $telemetri_map[$id]['last_time']) {
                    $telemetri_map[$id]['last_time'] = $f_time;
                    $telemetri_map[$id]['last_slot'] = '06.01-12.00';
                }
            } elseif ($f_time >= $slots['w3']['start'] && $f_time <= $slots['w3']['end']) {
                $telemetri_map[$id]['w3'] = $rain;
                if ($telemetri_map[$id]['last_time'] === null || $f_time > $telemetri_map[$id]['last_time']) {
                    $telemetri_map[$id]['last_time'] = $f_time;
                    $telemetri_map[$id]['last_slot'] = '12.01-18.00';
                }
            } elseif ($f_time >= $slots['w4']['start'] && $f_time <= $slots['w4']['end']) {
                $telemetri_map[$id]['w4'] = $rain;
                if ($telemetri_map[$id]['last_time'] === null || $f_time > $telemetri_map[$id]['last_time']) {
                    $telemetri_map[$id]['last_time'] = $f_time;
                    $telemetri_map[$id]['last_slot'] = '18.01-23.59';
                }
            }
        }
        
        // Set null values to 0
        foreach ($telemetri_map as $id => &$data) {
            if ($data['w1'] === null) $data['w1'] = 0;
            if ($data['w2'] === null) $data['w2'] = 0;
            if ($data['w3'] === null) $data['w3'] = 0;
            if ($data['w4'] === null) $data['w4'] = 0;
        }
        unset($data);
        
        return ['telemetri_map' => $telemetri_map, 'latest_full_time' => $latest_full_time];
    }

    public function build_pencatatan($all_stations, $telemetri_map, $manual_data) {
        $pencatatan = [];
        $total_hujan = 0; 
        $max_hujan = 0; 
        $pos_aktif = 0; 

        foreach ($all_stations as $id => $info) {
            $has_tele = isset($telemetri_map[$id]);
            $has_manual = isset($manual_data[$id]);
            
            $val = $has_tele ? $telemetri_map[$id] : [
                'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0,
                'last_time' => null, 'last_slot' => null
            ];
            $man = $has_manual ? $manual_data[$id] : null;
            
            $total_telemetri = $val['w1'] + $val['w2'] + $val['w3'] + $val['w4'];
            
            if ($has_tele || $has_manual) $pos_aktif++;
            
            $stat_val = max($total_telemetri, $man ? (float)$man['rain'] : 0);
            if ($stat_val > $max_hujan) $max_hujan = $stat_val;
            $total_hujan += $stat_val;

            // ==========================================
            // Tentukan nilai manual per rentang jam
            // 07:00-11:59 → manual_07
            // 12:00-16:59 → manual_12
            // 17:00-06:59 → manual_17
            // ==========================================
            $manual_07 = null;
            $manual_12 = null;
            $manual_17 = null;
            $manual_total = null;
            $manual_time = null;
            
            if ($man) {
                $hour = (int)date('H', strtotime($man['created_at'] ?? ''));
                $manual_total = $man['rain'] ?? null;
                $manual_time = $man['waktu'] ?? null;
                
                if ($hour >= 7 && $hour < 12) {
                    $manual_07 = $man['rain'] ?? null;
                } elseif ($hour >= 12 && $hour < 17) {
                    $manual_12 = $man['rain'] ?? null;
                } else {
                    // 17:00 - 06:59
                    $manual_17 = $man['rain'] ?? null;
                }
            }

            $pencatatan[] = [
                'id_pos'      => $id,
                'pos'         => $info['nama'],
                'lokasi'      => $info['lokasi'],
                'api_waktu'   => $val['last_time'] ? date('H:i', strtotime($val['last_time'])) : null,
                'api_slot'    => $val['last_slot'],
                'w1'          => $val['w1'],
                'w2'          => $val['w2'],
                'w3'          => $val['w3'],
                'w4'          => $val['w4'],
                'total'       => $total_telemetri,
                'manual_07'   => $manual_07,
                'manual_12'   => $manual_12,
                'manual_17'   => $manual_17,
                'manual_rain' => $manual_total,
                'manual_time' => $manual_time,
                'petugas'     => $man ? $man['petugas'] : null
            ];
        }

        usort($pencatatan, function($a, $b) {
            $a_aktif = ($a['api_waktu'] || $a['manual_time']) ? 1 : 0;
            $b_aktif = ($b['api_waktu'] || $b['manual_time']) ? 1 : 0;
            if ($a_aktif !== $b_aktif) return $b_aktif - $a_aktif;
            return strcmp($a['pos'], $b['pos']);
        });

        $no = 1;
        foreach ($pencatatan as &$row) { $row['no'] = $no++; }
        unset($row);

        return [
            'pencatatan'    => $pencatatan,
            'total_hujan'   => $total_hujan,
            'max_hujan'     => $max_hujan,
            'pos_aktif'     => $pos_aktif,
            'total_pos'     => count($all_stations)
        ];
    }

    public function get_curah_hujan_data($tanggal) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $tanggal = date('Y-m-d');
        }
        
        $slots = [
            'w1' => ['start' => $tanggal . ' 00:00:00', 'end' => $tanggal . ' 06:00:59'],
            'w2' => ['start' => $tanggal . ' 06:01:00', 'end' => $tanggal . ' 12:00:59'],
            'w3' => ['start' => $tanggal . ' 12:01:00', 'end' => $tanggal . ' 18:00:59'],
            'w4' => ['start' => $tanggal . ' 18:01:00', 'end' => $tanggal . ' 23:59:59']
        ];

        $all_stations = $this->get_all_pch_stations();
        $query_telemetri = $this->get_telemetri_data($tanggal, $slots);
        $manual_data = $this->get_manual_data($tanggal);
        
        $processed = $this->process_telemetri($query_telemetri, $slots);
        
        $result = $this->build_pencatatan($all_stations, $processed['telemetri_map'], $manual_data);
        
        return [
            'tanggal_pilih' => $tanggal,
            'last_update'   => $processed['latest_full_time'] 
                ? date('d M Y H:i', strtotime($processed['latest_full_time'])) . " WIB" 
                : date('d M Y', strtotime($tanggal)),
            'summary'       => [
                'pos_aktif'   => $result['pos_aktif'],
                'total_pos'   => $result['total_pos'],
                'max_hujan'   => $result['max_hujan'],
                'avg_wilayah' => $result['total_pos'] > 0 ? round($result['total_hujan'] / $result['total_pos'], 1) : 0
            ],
            'pencatatan'    => $result['pencatatan']
        ];
    }
}