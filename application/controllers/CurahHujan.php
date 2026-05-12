<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CurahHujan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index() {
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $w1_start = $tanggal . ' 00:00:00'; $w1_end = $tanggal . ' 06:00:59';
        $w2_start = $tanggal . ' 06:01:00'; $w2_end = $tanggal . ' 12:00:59';
        $w3_start = $tanggal . ' 12:01:00'; $w3_end = $tanggal . ' 18:00:59';
        $w4_start = $tanggal . ' 18:01:00'; $w4_end = $tanggal . ' 23:59:59';

        $all_stations = []; 
        
        $this->db->where('tipe_pos', 'PCH');
        $this->db->order_by('nama_pos', 'ASC');
        $master_pos = $this->db->get('master_pos')->result_array();
        
        foreach ($master_pos as $pos) {
            $all_stations[$pos['id_pos']] = [
                'nama'   => $pos['nama_pos'], 
                'lokasi' => $pos['sungai'] ?? '-'
            ];
        }

        // --- 1. AMBIL DATA TELEMETRI ---
        // Kita ambil data rain saja
        $this->db->select('t.id_pos, t.rain, t.received_at');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('m.tipe_pos', 'PCH');
        $this->db->where('t.received_at >=', $w1_start);
        $this->db->where('t.received_at <=', $w4_end);
        $this->db->order_by('t.received_at', 'ASC');
        $query_transaksi = $this->db->get()->result_array();

        // --- 2. AMBIL DATA MANUAL ---
        $this->db->select('dm.id_pos, dm.rain, dm.created_at, u.nama_lengkap as petugas');
        $this->db->from('data_manual dm');
        $this->db->join('users u', 'dm.id_user = u.id_user', 'left');
        $this->db->where('dm.tanggal_input', $tanggal);
        $query_manual = $this->db->get()->result_array();

        $transaksi_map = [];
        $manual_map = [];
        $latest_full_time = null; 

        // Mapping & Penjumlahan (Accumulation) Data Telemetri
        foreach ($query_transaksi as $tr) {
            $id = $tr['id_pos'];
            $f_time = $tr['received_at'];
            $rain = (float)($tr['rain']);
            
            if (!isset($transaksi_map[$id])) {
                $transaksi_map[$id] = ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last'=>$f_time];
            }

            if ($latest_full_time == null || $f_time > $latest_full_time) {
                $latest_full_time = $f_time;
            }

            // Logika Akumulasi: Menjumlahkan data yang masuk di periode tersebut
            if ($f_time >= $w1_start && $f_time <= $w1_end) {
                $transaksi_map[$id]['w1'] += $rain;
            } elseif ($f_time >= $w2_start && $f_time <= $w2_end) {
                $transaksi_map[$id]['w2'] += $rain;
            } elseif ($f_time >= $w3_start && $f_time <= $w3_end) {
                $transaksi_map[$id]['w3'] += $rain;
            } elseif ($f_time >= $w4_start && $f_time <= $w4_end) {
                $transaksi_map[$id]['w4'] += $rain;
            }
            
            $transaksi_map[$id]['last'] = $f_time;
        }

        // Mapping Data Manual
        foreach ($query_manual as $mn) {
            if (!isset($manual_map[$mn['id_pos']]) || $mn['created_at'] > $manual_map[$mn['id_pos']]['created_at']) {
                $manual_map[$mn['id_pos']] = [
                    'rain' => $mn['rain'],
                    'waktu' => date('H:i', strtotime($mn['created_at'])),
                    'petugas' => $mn['petugas'],
                    'created_at' => $mn['created_at']
                ];
            }
        }

        $pencatatan = [];
        $total_hujan_wilayah = 0; 
        $max_hujan = 0; 
        $pos_aktif = 0; 

        foreach ($all_stations as $id => $info) {
            $has_tele = isset($transaksi_map[$id]);
            $has_manual = isset($manual_map[$id]);
            
            $val = $has_tele ? $transaksi_map[$id] : ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last'=>null];
            $man = $has_manual ? $manual_map[$id] : ['rain' => null, 'waktu' => '--:--', 'petugas' => '-'];
            
            // TOTAL HARIAN = Penjumlahan dari W1 sampai W4
            $total_akumulasi = $val['w1'] + $val['w2'] + $val['w3'] + $val['w4']; 
            
            if($has_tele || $has_manual) $pos_aktif++;
            
            // Gunakan total akumulasi untuk statistik
            $stat_val = max($total_akumulasi, (float)$man['rain']);
            if ($stat_val > $max_hujan) $max_hujan = $stat_val;
            $total_hujan_wilayah += $stat_val;

            $pencatatan[] = [
                'id_pos' => $id,
                'pos'    => $info['nama'],
                'lokasi' => $info['lokasi'],
                'waktu'  => $val['last'] ? date('H:i', strtotime($val['last'])) : '--:--',
                'w1'     => $val['w1'], 
                'w2'     => $val['w2'], 
                'w3'     => $val['w3'], 
                'w4'     => $val['w4'],
                'total'  => $total_akumulasi, // Sekarang berisi hasil penjumlahan
                'manual_rain'  => $man['rain'],
                'manual_time'  => $man['waktu'],
                'petugas'      => $man['petugas']
            ];
        }

        // Sorting: Pos Aktif ke atas
        usort($pencatatan, function($a, $b) {
            $a_aktif = ($a['waktu'] !== '--:--' || $a['manual_time'] !== '--:--') ? 1 : 0;
            $b_aktif = ($b['waktu'] !== '--:--' || $b['manual_time'] !== '--:--') ? 1 : 0;
            if ($a_aktif !== $b_aktif) return $b_aktif - $a_aktif; 
            return strcmp($a['pos'], $b['pos']);
        });

        $no = 1;
        foreach ($pencatatan as &$row) { $row['no'] = $no++; }
        unset($row);

        $data = [
            'app_name'      => "HydroSmart",
            'title'         => "Data Curah Hujan",
            'tanggal_pilih' => $tanggal,
            'last_update'   => ($latest_full_time) ? "Data Terakhir: " . date('H:i', strtotime($latest_full_time)) . " WIB" : date('d M Y', strtotime($tanggal)) . ": Data Belum Tersedia",
            'summary'       => [
                'pos_aktif'   => $pos_aktif,
                'total_pos'   => count($all_stations),
                'max_hujan'   => $max_hujan,
                'avg_wilayah' => count($all_stations) > 0 ? round($total_hujan_wilayah / count($all_stations), 2) : 0
            ],
            'pencatatan'    => $pencatatan
        ];

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_curah_hujan', $data);
        $this->load->view('layout/v_footer', $data);
    }
}