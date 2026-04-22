<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index()
    {
        // Wajib dipanggil untuk menghindari error base_url()
        $this->load->helper('url');
        
        $this->load->view('pages/v_beranda');
    }
}