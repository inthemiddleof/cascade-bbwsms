<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tma extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_tma');
        $this->load->helper(['url', 'form', 'number']);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index() {
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $result = $this->M_tma->get_tma_data($tanggal);

        $data = [
            'app_name'       => "HydroSmart",
            'title'          => "Tinggi Muka Air",
            'tanggal_pilih'  => $result['tanggal_pilih'],
            'last_update'    => $result['last_update'],
            'pencatatan_tma' => $result['pencatatan_tma']
        ];

        $this->load->view('layout/v_header', $data);
        $this->load->view('pages/v_tma', $data);
        $this->load->view('layout/v_footer', $data);
    }
}