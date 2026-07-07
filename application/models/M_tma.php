<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_tma extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================
    // GET ALL PDA STATIONS
    // ==========================================
    public function get_all_pda_stations() {
        $this->db->where('tipe_pos', 'PDA');
        $this->db->order_by('nama_pos', 'ASC');
        $master_pos = $this->db->get('master_pos')->result_array();
        
        $all_stations = []; 
        foreach ($master_pos as $pos) {
            $all_stations[$pos['id_pos']] = [
                'nama'   => $pos['nama_pos'], 
                'lokasi' => $pos['sungai'] ?? '-',
                'siaga1' => (float)($pos['siaga1'] ?? 0),
                'siaga2' => (float)($pos['siaga2'] ?? 0),
                'siaga3' => (float)($pos['siaga3'] ?? 0),
                'siaga4' => (float)($pos['siaga4'] ?? 0)
            ];
        }
        
        return $all_stations;
    }

    // ==========================================
    // GET TELEMETRI DATA
    // ==========================================
    public function get_telemetri_data($w1_start, $w4_end) {
        $this->db->select('t.id_pos, t.wlevel, t.received_at');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('m.tipe_pos', 'PDA');
        $this->db->where('t.received_at >=', $w1_start);
        $this->db->where('t.received_at <=', $w4_end);
        $this->db->order_by('t.received_at', 'ASC');
        return $this->db->get()->result_array();
    }

    // ==========================================
    // GET MANUAL DATA
    // ==========================================
    public function get_manual_data($tanggal) {
        $manual_map = [];
        
        if ($this->db->table_exists('data_manual')) {
            $this->db->select('dm.id_pos, dm.wlevel, dm.created_at, u.nama_lengkap as petugas');
            $this->db->from('data_manual dm');
            $this->db->join('users u', 'dm.id_user = u.id_user', 'left');
            $this->db->where('dm.tanggal_input', $tanggal);
            $this->db->order_by('dm.created_at', 'DESC');
            $query_manual = $this->db->get()->result_array();
            
            foreach ($query_manual as $mn) {
                $id = $mn['id_pos'];
                if (!isset($manual_map[$id])) {
                    $manual_map[$id] = [
                        'wlevel'     => (float)$mn['wlevel'],
                        'waktu'      => date('H:i', strtotime($mn['created_at'])),
                        'petugas'    => $mn['petugas'] ?? '-',
                        'created_at' => $mn['created_at']  // ✅ Tambah created_at
                    ];
                }
            }
        }
        
        return $manual_map;
    }

    // ==========================================
    // PROCESS TELEMETRI DATA
    // ==========================================
    public function process_telemetri($query_telemetri, $slots) {
        $telemetri_map = [];
        $latest_full_time = null;

        foreach ($query_telemetri as $tr) {
            $id = $tr['id_pos'];
            $f_time = $tr['received_at'];
            $wlevel = (float)($tr['wlevel'] ?? 0);
            
            if (!isset($telemetri_map[$id])) {
                $telemetri_map[$id] = [
                    'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0,
                    'last_time' => null
                ];
            }

            if ($latest_full_time === null || $f_time > $latest_full_time) {
                $latest_full_time = $f_time;
            }

            if ($f_time >= $slots['w1']['start'] && $f_time <= $slots['w1']['end']) {
                $telemetri_map[$id]['w1'] = max($telemetri_map[$id]['w1'], $wlevel);
            } elseif ($f_time >= $slots['w2']['start'] && $f_time <= $slots['w2']['end']) {
                $telemetri_map[$id]['w2'] = max($telemetri_map[$id]['w2'], $wlevel);
            } elseif ($f_time >= $slots['w3']['start'] && $f_time <= $slots['w3']['end']) {
                $telemetri_map[$id]['w3'] = max($telemetri_map[$id]['w3'], $wlevel);
            } elseif ($f_time >= $slots['w4']['start'] && $f_time <= $slots['w4']['end']) {
                $telemetri_map[$id]['w4'] = max($telemetri_map[$id]['w4'], $wlevel);
            }
            
            if ($telemetri_map[$id]['last_time'] === null || $f_time > $telemetri_map[$id]['last_time']) {
                $telemetri_map[$id]['last_time'] = $f_time;
            }
        }
        
        return [
            'telemetri_map'   => $telemetri_map, 
            'latest_full_time' => $latest_full_time
        ];
    }

    // ==========================================
    // BUILD PENCATATAN TABLE
    // ==========================================
    public function build_pencatatan($all_stations, $telemetri_map, $manual_map) {
        $pencatatan_tma = [];
        $pos_aktif = 0;

        foreach ($all_stations as $id => $info) {
            $has_tele = isset($telemetri_map[$id]);
            $has_manual = isset($manual_map[$id]);
            
            $val = $has_tele ? $telemetri_map[$id] : [
                'w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0,
                'last_time' => null
            ];
            $man = $has_manual ? $manual_map[$id] : null;
            
            if ($has_tele || $has_manual) $pos_aktif++;

            // LAST (M): Ambil dari slot terakhir yang ada data
            $last_from_slot = 0;
            if ($val['w4'] > 0) {
                $last_from_slot = $val['w4'];
            } elseif ($val['w3'] > 0) {
                $last_from_slot = $val['w3'];
            } elseif ($val['w2'] > 0) {
                $last_from_slot = $val['w2'];
            } elseif ($val['w1'] > 0) {
                $last_from_slot = $val['w1'];
            }

            // ==========================================
            // Tentukan nilai manual per jam (07, 12, 17)
            // ==========================================
            $manual_07 = null;
            $manual_12 = null;
            $manual_17 = null;
            $manual_val = null;
            $manual_time = null;
            
            if ($man) {
                $hour = (int)date('H', strtotime($man['created_at'] ?? ''));
                $manual_val = $man['wlevel'] ?? null;
                $manual_time = $man['waktu'] ?? null;
                
                if ($hour <= 7) {
                    $manual_07 = $man['wlevel'] ?? null;
                } elseif ($hour <= 12) {
                    $manual_12 = $man['wlevel'] ?? null;
                } elseif ($hour <= 17) {
                    $manual_17 = $man['wlevel'] ?? null;
                }
            }

            $pencatatan_tma[] = [
                'id_pos'      => $id,
                'pos'         => $info['nama'],
                'lokasi'      => $info['lokasi'],
                'api_waktu'   => $val['last_time'] ? date('H:i', strtotime($val['last_time'])) : null,
                'w1'          => $val['w1'],
                'w2'          => $val['w2'],
                'w3'          => $val['w3'],
                'w4'          => $val['w4'],
                'last'        => $last_from_slot,
                'manual_07'   => $manual_07,      // ✅ Baru
                'manual_12'   => $manual_12,      // ✅ Baru
                'manual_17'   => $manual_17,      // ✅ Baru
                'manual_val'  => $manual_val,
                'manual_time' => $manual_time,
                'petugas'     => $man ? $man['petugas'] : '-',
                'siaga'       => [
                    'siaga1' => $info['siaga1'],
                    'siaga2' => $info['siaga2'],
                    'siaga3' => $info['siaga3'],
                    'siaga4' => $info['siaga4']
                ]
            ];
        }

        usort($pencatatan_tma, function($a, $b) {
            $a_aktif = ($a['api_waktu'] || $a['manual_time']) ? 1 : 0;
            $b_aktif = ($b['api_waktu'] || $b['manual_time']) ? 1 : 0;
            if ($a_aktif !== $b_aktif) return $b_aktif - $a_aktif;
            return strcmp($a['pos'], $b['pos']);
        });

        $no = 1;
        foreach ($pencatatan_tma as &$row) { 
            $row['no'] = $no++; 
        }
        unset($row);

        return $pencatatan_tma;
    }

    // ==========================================
    // MAIN: GET TMA DATA
    // ==========================================
    public function get_tma_data($tanggal) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $tanggal = date('Y-m-d');
        }

        $slots = [
            'w1' => ['start' => $tanggal . ' 00:00:00', 'end' => $tanggal . ' 06:00:59'],
            'w2' => ['start' => $tanggal . ' 06:01:00', 'end' => $tanggal . ' 12:00:59'],
            'w3' => ['start' => $tanggal . ' 12:01:00', 'end' => $tanggal . ' 18:00:59'],
            'w4' => ['start' => $tanggal . ' 18:01:00', 'end' => $tanggal . ' 23:59:59']
        ];

        $all_stations    = $this->get_all_pda_stations();
        $query_telemetri = $this->get_telemetri_data($slots['w1']['start'], $slots['w4']['end']);
        $manual_map      = $this->get_manual_data($tanggal);
        $processed       = $this->process_telemetri($query_telemetri, $slots);
        $pencatatan_tma  = $this->build_pencatatan($all_stations, $processed['telemetri_map'], $manual_map);
        
        return [
            'tanggal_pilih'  => $tanggal,
            'last_update'    => $processed['latest_full_time'] 
                ? date('d M Y H:i', strtotime($processed['latest_full_time'])) . " WIB"
                : date('d M Y', strtotime($tanggal)),
            'pencatatan_tma' => $pencatatan_tma
        ];
    }
}