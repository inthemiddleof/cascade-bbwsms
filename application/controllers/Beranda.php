<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Beranda extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_beranda');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index() {
        $this->M_beranda->sync_telemetri();
        
        $selected_date = $this->input->get('date') ?: date('Y-m-d');
        $selected_date_display = date('d M Y', strtotime($selected_date));
        $summary = $this->M_beranda->get_dashboard_summary($selected_date);

        $data = [
            'app_name'              => "Hydrosmart",
            'title'                 => "BBWS MESUJI SEKAMPUNG",
            'selected_date'         => $selected_date,
            'selected_date_display' => $selected_date_display,
            'bendungan_db'          => $summary['bendungan_db'],
            'bendung_db'            => $summary['bendung_db'],
            'embung_db'             => $summary['embung_db'], 
            'pengaman_db'           => $summary['pengaman_db'],
            'sedimen_db'            => $summary['sedimen_db'],
            'irigasi_db'            => $summary['irigasi_db'],   
            'pch_db'                => $summary['pch_db'],
            'pda_db'                => $summary['pda_db'],
            'bendung_count'         => $summary['bendung_count'],
            'embung_count'          => $summary['embung_count'],
            'pengaman_count'        => $summary['pengaman_count'],
            'sedimen_count'         => $summary['sedimen_count'],
            'irigasi_count'         => $summary['irigasi_count'],
            'ws_geojson'            => $summary['ws_geojson'],
            'bendung_geojson'       => $summary['bendung_geojson'],
            'das_geojson'           => $summary['das_geojson'],
            'dam_status'            => $summary['dam_status'],
            'weather_data'          => $summary['weather_data']
        ];
    
        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_beranda', $data);
    }
}