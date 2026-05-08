<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peta extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index() {
        $tanggal_akhir = date('Y-m-d');
        $tanggal_awal  = date('Y-m-d', strtotime('-6 days')); 
        $dates_template = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates_template[date('Y-m-d', strtotime("-$i days"))] = 0;
        }

        $subquery_max_time = "(SELECT MAX(received_at) FROM data_telemetri WHERE id_pos = m.id_pos)";
        
        $this->db->select('m.id_pos, m.nama_pos as nama_tampil, m.tipe_pos as tipe_tampil, m.lat as latitude, m.lng as longitude, m.siaga1 as siaga_merah, m.siaga2 as siaga_kuning, t.rain, t.wlevel as w_level, t.received_at as last_update');
        $this->db->from('master_pos m');
        $this->db->join('data_telemetri t', "t.id_pos = m.id_pos AND t.received_at = $subquery_max_time", 'left');
        $this->db->order_by('m.nama_pos', 'ASC');
        $current_data = $this->db->get()->result_array();

        $semua_pos = [];
        foreach ($current_data as $row) {
            $id = $row['id_pos'];
            $row['id_tampil'] = $id;
            $row['rain']    = (float)($row['rain'] ?? 0);
            $row['w_level'] = (float)($row['w_level'] ?? 0);
            
            $semua_pos[$id] = $row;
            $semua_pos[$id]['history_map'] = $dates_template; 
        }

        $this->db->select('t.id_pos, m.tipe_pos, t.rain, t.wlevel, DATE(t.received_at) as tgl');
        $this->db->from('data_telemetri t');
        $this->db->join('master_pos m', 't.id_pos = m.id_pos');
        $this->db->where('t.received_at >=', $tanggal_awal . ' 00:00:00');
        $this->db->where('t.received_at <=', $tanggal_akhir . ' 23:59:59');
        $this->db->order_by('t.received_at', 'ASC');
        $history_data = $this->db->get()->result_array();

        foreach ($history_data as $row) {
            $id   = $row['id_pos'];
            $date = $row['tgl'];

            if (isset($semua_pos[$id]) && isset($semua_pos[$id]['history_map'][$date])) {
                if ($row['tipe_pos'] === 'PCH') {
                    $semua_pos[$id]['history_map'][$date] = max($semua_pos[$id]['history_map'][$date], (float)$row['rain']);
                } else {
                    $semua_pos[$id]['history_map'][$date] = (float)$row['wlevel'];
                }
            }
        }

        $final_pos = [];
        foreach ($semua_pos as $pos) {
            if (empty($pos['latitude']) || empty($pos['longitude'])) continue;

            $labels = [];
            $values = [];
            foreach ($pos['history_map'] as $date => $val) {
                $labels[] = date('d M', strtotime($date)); 
                $values[] = round($val, 2);
            }
            $pos['chart_labels'] = $labels;
            $pos['chart_values'] = $values;
            
            $tipe_pos = strtoupper(trim($pos['tipe_tampil']));
            if ($tipe_pos === 'PCH') {
                $pos['chart_title'] = 'Curah Hujan'; 
                $pos['chart_unit']  = 'mm';
            } else {
                $pos['chart_title'] = 'Ketinggian Air'; 
                $pos['chart_unit']  = 'm'; 
            }

            unset($pos['history_map']); 
            $final_pos[] = $pos;
        }

        $data = [
            'app_name'  => 'HydroSmart',
            'title'     => 'Monitoring Pos Peta',
            'semua_pos' => $final_pos,
            'summary'   => [
                'total'  => count($final_pos),
                'online' => count(array_filter($final_pos, fn($p) => !empty($p['last_update'])))
            ]
        ];
    
        $this->load->view('layout/v_header_peta', $data);
        $this->load->view('pages/v_peta', $data);
    }
}