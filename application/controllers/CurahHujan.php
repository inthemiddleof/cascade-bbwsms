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

        $this->db->select('t.id_pos, t.rain, t.received_at');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('m.tipe_pos', 'PCH');
        $this->db->where('t.received_at >=', $w1_start);
        $this->db->where('t.received_at <=', $w4_end);
        $this->db->order_by('t.received_at', 'ASC');
        $query_transaksi = $this->db->get()->result_array();

        $transaksi_map = [];
        $latest_full_time = null; 

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

            if ($f_time >= $w1_start && $f_time <= $w1_end) {
                $transaksi_map[$id]['w1'] = max($transaksi_map[$id]['w1'], $rain);
            } elseif ($f_time >= $w2_start && $f_time <= $w2_end) {
                $transaksi_map[$id]['w2'] = max($transaksi_map[$id]['w2'], $rain);
            } elseif ($f_time >= $w3_start && $f_time <= $w3_end) {
                $transaksi_map[$id]['w3'] = max($transaksi_map[$id]['w3'], $rain);
            } elseif ($f_time >= $w4_start && $f_time <= $w4_end) {
                $transaksi_map[$id]['w4'] = max($transaksi_map[$id]['w4'], $rain);
            }
            
            $transaksi_map[$id]['last'] = $f_time;
        }

        $pencatatan = [];
        $total_hujan_wilayah = 0; 
        $max_hujan = 0; 
        $pos_aktif = 0; 

        foreach ($all_stations as $id => $info) {
            $has_data = isset($transaksi_map[$id]);
            $val = $has_data ? $transaksi_map[$id] : ['w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'last'=>null];
            
            $total_harian = max($val['w1'], $val['w2'], $val['w3'], $val['w4']); 
            
            if($has_data) $pos_aktif++;
            if ($total_harian > $max_hujan) $max_hujan = $total_harian;
            $total_hujan_wilayah += $total_harian;

            $pencatatan[] = [
                // Nomor urut kita hapus dulu dari sini, akan diisi setelah di-sorting
                'pos'    => $info['nama'],
                'lokasi' => $info['lokasi'],
                'waktu'  => $val['last'] ? date('H:i', strtotime($val['last'])) : '--:--',
                'w1'     => $val['w1'], 
                'w2'     => $val['w2'], 
                'w3'     => $val['w3'], 
                'w4'     => $val['w4'],
                'total'  => $total_harian,
                'manual' => '-'
            ];
        }

        // --- PROSES PENGURUTAN (SORTING) ---
        usort($pencatatan, function($a, $b) {
            // Cek apakah pos aktif (waktu tidak sama dengan '--:--')
            $a_aktif = ($a['waktu'] !== '--:--') ? 1 : 0;
            $b_aktif = ($b['waktu'] !== '--:--') ? 1 : 0;

            // Jika status aktifnya berbeda, pos yang aktif (1) diletakkan di atas pos tidak aktif (0)
            if ($a_aktif !== $b_aktif) {
                return $b_aktif - $a_aktif; 
            }
            
            // Jika status aktifnya sama, urutkan berdasarkan abjad (nama pos)
            return strcmp($a['pos'], $b['pos']);
        });

        // --- PENOMORAN ULANG ---
        // Memberikan nomor urut yang rapi setelah data diurutkan
        $no = 1;
        foreach ($pencatatan as &$row) {
            $row['no'] = $no++;
        }
        unset($row); // Menghapus referensi pointer untuk mencegah bug

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