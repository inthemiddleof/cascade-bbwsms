<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CurahHujan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_curah_hujan');
        $this->load->helper(['url', 'form', 'number']);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index() {
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');        
        $result = $this->M_curah_hujan->get_curah_hujan_data($tanggal);
        $data = [
            'app_name'      => "HydroSmart",
            'title'         => "Data Curah Hujan",
            'tanggal_pilih' => $result['tanggal_pilih'],
            'last_update'   => $result['last_update'],
            'summary'       => $result['summary'],
            'pencatatan'    => $result['pencatatan']
        ];

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_curah_hujan', $data);
        $this->load->view('layout/v_footer', $data);
    }
}