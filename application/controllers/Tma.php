<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tma extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index() {
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $data['app_name']      = "HydroSmart";
        $data['title']         = "Tinggi Muka Air";
        $data['tanggal_pilih'] = $tanggal;

        $w1_start = $tanggal . ' 00:00:00'; $w1_end = $tanggal . ' 06:00:59';
        $w2_start = $tanggal . ' 06:01:00'; $w2_end = $tanggal . ' 12:00:59';
        $w3_start = $tanggal . ' 12:01:00'; $w3_end = $tanggal . ' 18:00:59';
        $w4_start = $tanggal . ' 18:01:00'; $w4_end = $tanggal . ' 23:59:59';

        $all_stations = []; 

        $this->db->where('tipe_pos', 'PDA');
        $this->db->order_by('nama_pos', 'ASC'); 
        $master_pos = $this->db->get('master_pos')->result_array();
        
        foreach ($master_pos as $pos) {
            $all_stations[$pos['id_pos']] = [
                'id_pos' => $pos['id_pos'],
                'nama'   => $pos['nama_pos'], 
                'lokasi' => $pos['sungai'] ?? '-',
                'siaga1' => (float)($pos['siaga1'] ?? 0),
                'siaga2' => (float)($pos['siaga2'] ?? 0),
                'siaga3' => (float)($pos['siaga3'] ?? 0),
                'siaga4' => (float)($pos['siaga4'] ?? 0)
            ];
        }

        // --- 1. AMBIL DATA TELEMETRI ---
        $this->db->select('t.id_pos, t.wlevel, t.received_at');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('m.tipe_pos', 'PDA');
        $this->db->where('t.received_at >=', $w1_start);
        $this->db->where('t.received_at <=', $w4_end);
        $this->db->order_by('t.received_at', 'ASC');
        $query_transaksi = $this->db->get()->result_array();

        // --- 2. AMBIL DATA MANUAL (Integrasi Baru) ---
        $this->db->select('dm.id_pos, dm.wlevel, dm.created_at, u.nama_lengkap as petugas');
        $this->db->from('data_manual dm');
        $this->db->join('users u', 'dm.id_user = u.id_user', 'left');
        $this->db->where('dm.tanggal_input', $tanggal);
        $query_manual = $this->db->get()->result_array();

        $transaksi_map = [];
        $manual_map = [];
        $latest_full_time = null; 

        // Mapping Data Telemetri
        foreach ($query_transaksi as $tr) {
            $id = $tr['id_pos'];
            $f_time = $tr['received_at'];
            $wlevel = (float)($tr['wlevel'] ?? 0);
            
            if (!isset($transaksi_map[$id])) {
                $transaksi_map[$id] = ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last_val'=>0, 'last_time'=>null];
            }

            if ($latest_full_time == null || $f_time > $latest_full_time) {
                $latest_full_time = $f_time;
            }

            if ($f_time >= $w1_start && $f_time <= $w1_end) $transaksi_map[$id]['w1'] = max($transaksi_map[$id]['w1'], $wlevel);
            elseif ($f_time >= $w2_start && $f_time <= $w2_end) $transaksi_map[$id]['w2'] = max($transaksi_map[$id]['w2'], $wlevel);
            elseif ($f_time >= $w3_start && $f_time <= $w3_end) $transaksi_map[$id]['w3'] = max($transaksi_map[$id]['w3'], $wlevel);
            elseif ($f_time >= $w4_start && $f_time <= $w4_end) $transaksi_map[$id]['w4'] = max($transaksi_map[$id]['w4'], $wlevel);
            
            $transaksi_map[$id]['last_val'] = $wlevel;
            $transaksi_map[$id]['last_time'] = $f_time;
        }

        // Mapping Data Manual
        foreach ($query_manual as $mn) {
            $manual_map[$mn['id_pos']] = [
                'wlevel' => $mn['wlevel'],
                'waktu'  => date('H:i', strtotime($mn['created_at'])),
                'petugas'=> $mn['petugas']
            ];
        }

        $pencatatan_tma = [];
        $total_w_level = 0; 
        $max_w_level = 0; 
        $count_bahaya = 0; 
        $pos_aktif = 0; 

        foreach ($all_stations as $id => $info) {
            $val = isset($transaksi_map[$id]) ? $transaksi_map[$id] : ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last_val'=>0, 'last_time'=>null];
            $man = isset($manual_map[$id]) ? $manual_map[$id] : ['wlevel'=>null, 'waktu'=>'--:--', 'petugas'=>'-'];
            
            if(isset($transaksi_map[$id]) || isset($manual_map[$id])) $pos_aktif++;

            // Prioritas TMA Tertinggi untuk Summary (pilih mana yang ada data)
            $last_val = ($val['last_val'] > 0) ? $val['last_val'] : (float)$man['wlevel'];
            if ($last_val > $max_w_level) $max_w_level = $last_val;
            $total_w_level += $last_val;

            if ($info['siaga1'] > 0 && $last_val >= $info['siaga1']) $count_bahaya++;

            $pencatatan_tma[] = [
                'id_pos'    => $id,
                'pos'       => $info['nama'],
                'lokasi'    => $info['lokasi'],
                'waktu'     => $val['last_time'] ? date('H:i', strtotime($val['last_time'])) : '--:--',
                'telemetri' => [
                    'w1' => $val['w1'], 'w2' => $val['w2'], 'w3' => $val['w3'], 'w4' => $val['w4']
                ],
                'last'      => $val['last_val'],
                // Data Manual
                'manual_val' => $man['wlevel'],
                'manual_time'=> $man['waktu'],
                'petugas'    => $man['petugas'],
                'siaga'     => [
                    'siaga1' => $info['siaga1'], 'siaga2' => $info['siaga2'], 'siaga3' => $info['siaga3'], 'siaga4' => $info['siaga4'],
                ]
            ];
        }

        // Sorting: Pos Aktif ke atas
        usort($pencatatan_tma, function($a, $b) {
            $a_aktif = ($a['waktu'] !== '--:--' || $a['manual_time'] !== '--:--') ? 1 : 0;
            $b_aktif = ($b['waktu'] !== '--:--' || $b['manual_time'] !== '--:--') ? 1 : 0;
            if ($a_aktif !== $b_aktif) return $b_aktif - $a_aktif; 
            return strcmp($a['pos'], $b['pos']);
        });

        $no = 1;
        foreach ($pencatatan_tma as &$row) { $row['no'] = $no++; }
        unset($row); 

        $data['last_update'] = ($latest_full_time) 
            ? "Data Terakhir: " . date('H:i', strtotime($latest_full_time)) . " WIB"
            : date('d M Y', strtotime($tanggal)) . ": Data Belum Tersedia";

        $data['summary'] = [
            'pos_aktif'     => $pos_aktif,
            'total_pos'     => count($all_stations),
            'tma_tertinggi' => $max_w_level,
            'tma_rata_rata' => count($all_stations) > 0 ? round($total_w_level / count($all_stations), 2) : 0,
            'status_siaga'  => $count_bahaya,
            'status_aman'   => count($all_stations) - $count_bahaya
        ];
        
        $data['pencatatan_tma'] = $pencatatan_tma;

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_tma', $data);
        $this->load->view('layout/v_footer', $data);
    }
}