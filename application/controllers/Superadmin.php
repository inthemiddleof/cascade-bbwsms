<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Superadmin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->model('M_superadmin');
        $this->load->helper(['url', 'form']);
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
        
        if (!$this->session->userdata('logged_in')) redirect('auth');
        if ($this->session->userdata('role') !== 'superadmin') show_error('Akses Ditolak.', 403);
    }

    private function _render($view, $data) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/v_superadmin_layout', $data);
    }

    public function index() {
    $data = $this->M_superadmin->get_dashboard_data();
    $data['admin_name'] = $this->session->userdata('nama_lengkap');
    
    // Tambahkan data untuk grafik
    $data['pos_list'] = $this->M_superadmin->get_detailed_pos_list();
    $data['total_pch'] = $this->db->where('tipe_pos', 'PCH')->count_all_results('master_pos');
    $data['total_pda'] = $this->db->where('tipe_pos', 'PDA')->count_all_results('master_pos');
    $data['pos_online'] = $this->_count_online_pos();
    $data['total_data_hari_ini'] = $this->_count_today_data();
    $data['last_sync'] = $this->_get_last_sync();
    
    $this->_render('superadmin/v_dashboard', $data);
}

private function _count_online_pos($allowed_pos = null) {
    $this->db->distinct()->select('id_pos');
    $this->db->where('received_at >=', date('Y-m-d H:i:s', strtotime('-1 hour')));
    if ($allowed_pos !== null) {
        $this->db->where_in('id_pos', $allowed_pos);
    }
    return $this->db->get('data_telemetri')->num_rows();
}

private function _count_today_data($allowed_pos = null) {
    if ($allowed_pos !== null) {
        $this->db->where_in('id_pos', $allowed_pos);
    }
    $t = $this->db->where('DATE(received_at)', date('Y-m-d'))->count_all_results('data_telemetri');
    $m = $this->db->where('tanggal_input', date('Y-m-d'))->count_all_results('data_manual');
    $b = $this->db->where('DATE(tanggal_input)', date('Y-m-d'))->count_all_results('data_bendung');
    $bendungan = $this->db->where('DATE(tanggal_input)', date('Y-m-d'))->count_all_results('data_bendungan');
    return $t + $m + $b + $bendungan;
}

private function _get_last_sync($allowed_pos = null) {
    if ($allowed_pos !== null) {
        $this->db->where_in('id_pos', $allowed_pos);
    }
    $this->db->select('MAX(received_at) as last_sync');
    $tel = $this->db->get('data_telemetri')->row();
    
    if (!empty($tel->last_sync)) {
        return $tel->last_sync;
    }
    
    if ($allowed_pos !== null) {
        $this->db->where_in('id_pos', $allowed_pos);
    }
    $this->db->select('MAX(created_at) as last_sync');
    $man = $this->db->get('data_manual')->row();
    
    if (!empty($man->last_sync)) {
        return $man->last_sync;
    }
    
    if ($allowed_pos !== null) {
        $this->db->where_in('id_pos', $allowed_pos);
    }
    $this->db->select('MAX(created_at) as last_sync');
    $bendung = $this->db->get('data_bendung')->row();
    
    if (!empty($bendung->last_sync)) {
        return $bendung->last_sync;
    }
    
    if ($allowed_pos !== null) {
        $this->db->where_in('id_pos', $allowed_pos);
    }
    $this->db->select('MAX(created_at) as last_sync');
    $bendungan = $this->db->get('data_bendungan')->row();
    
    return !empty($bendungan->last_sync) ? $bendungan->last_sync : null;
}
    // ==========================================
    // KELOLA POS
    // ==========================================
    public function kelola_pos() {
        $data = $this->M_superadmin->get_pos_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_pos', $data);
    }

    public function tambah_pos() {
        $result = $this->M_superadmin->insert_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pos');
    }

    public function edit_pos() {
        $result = $this->M_superadmin->update_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pos');
    }

    public function hapus_pos($id) {
        $result = $this->M_superadmin->delete_pos($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pos');
    }

    // ==========================================
    // KELOLA ADMIN
    // ==========================================
    public function kelola_admin() {
        $data = $this->M_superadmin->get_admin_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_admin', $data);
    }

    public function tambah_admin() {
        $result = $this->M_superadmin->insert_admin($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_admin');
    }

    public function edit_admin() {
        $result = $this->M_superadmin->update_admin($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_admin');
    }

    public function hapus_admin($id) {
        $result = $this->M_superadmin->delete_admin($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_admin');
    }

    public function nonaktifkan_admin($id) {
        $this->M_superadmin->set_admin_status($id, 'nonaktif');
        $this->session->set_flashdata('success', 'Admin dinonaktifkan.');
        redirect('superadmin/kelola_admin');
    }

    public function aktifkan_admin($id) {
        $this->M_superadmin->set_admin_status($id, 'aktif');
        $this->session->set_flashdata('success', 'Admin diaktifkan.');
        redirect('superadmin/kelola_admin');
    }

    // ==========================================
    // KELOLA MANUAL
    // ==========================================
    public function kelola_manual() {
        $id_pos = $this->input->get('pos');
        $bulan = $this->input->get('bulan') ?: date('Y-m');
        
        $pos_list = $this->M_superadmin->get_all_pos_unique(null);
        
        if (empty($id_pos) && !empty($pos_list)) {
            $id_pos = $pos_list[0]->id_pos;
        }
        
        $pos = null;
        foreach ($pos_list as $p) {
            if ($p->id_pos == $id_pos) {
                $pos = $p;
                break;
            }
        }
        
        if (empty($pos) && !empty($pos_list)) {
            $pos = $pos_list[0];
            $id_pos = $pos->id_pos;
        }
        
        $kelola_data = $this->M_superadmin->get_kelola_manual_data($id_pos, $bulan);
        $data_list = $kelola_data['data_list'];
        $pos = $kelola_data['pos'];
        
        $pos_data_js = [];
        $bendungan_data_js = [];
        $bendung_data_js = [];
        
        if (!empty($pos) && !empty($data_list)) {
            if ($pos->is_bendung == 1) {
                $bendung_data_js = $this->M_superadmin->format_bendung_for_js($data_list);
            } elseif ($pos->is_bendungan == 1) {
                $bendungan_data_js = $this->M_superadmin->format_bendungan_for_js($data_list);
            } else {
                $pos_data_js = $this->M_superadmin->format_pos_for_js($data_list);
            }
        }
        
        $data = [
            'app_name' => 'HydroSmart',
            'title' => 'Kelola Laporan Manual',
            'admin_name' => $this->session->userdata('nama_lengkap'),
            'pos' => $pos,
            'pos_list' => $pos_list,
            'bulan' => $bulan,
            'data_list' => $data_list,
            'pos_data_js' => $pos_data_js,
            'bendungan_data_js' => $bendungan_data_js,
            'bendung_data_js' => $bendung_data_js,
        ];
        
        $this->_render('superadmin/v_kelola_manual', $data);
    }

    // ==========================================
    // SIMPAN DATA
    // ==========================================
    public function simpan_data_pos() {
        $this->load->model('M_admin');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        
        $this->form_validation->set_rules('id_pos', 'Pos', 'required');
        $this->form_validation->set_rules('tanggal_input', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('superadmin/kelola_manual');
        }
        
        $result = $this->M_admin->insert_manual_pos(
            $this->input->post(),
            $user_id,
            null
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    public function simpan_bendungan() {
        $this->load->model('M_admin');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        
        $this->form_validation->set_rules('id_pos', 'Pos', 'required');
        $this->form_validation->set_rules('tanggal_input', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('superadmin/kelola_manual');
        }
        
        $post = $this->input->post();
        $post['tahun_mulai_pembangunan'] = $this->input->post('tahun_mulai_pembangunan');
        $post['tipe_bendungan'] = $this->input->post('tipe_bendungan');
        $post['elevasi_mercu'] = $this->input->post('elevasi_mercu');
        $post['luas_das'] = $this->input->post('luas_das');
        
        $result = $this->M_admin->insert_manual_bendungan(
            $post,
            $user_id,
            null
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    public function simpan_bendung() {
        $this->load->model('M_admin');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        
        $this->form_validation->set_rules('id_pos', 'Pos', 'required');
        $this->form_validation->set_rules('tanggal_input', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('superadmin/kelola_manual');
        }
        
        $result = $this->M_admin->insert_manual_bendung(
            $this->input->post(),
            $user_id,
            null
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    // ==========================================
    // UPDATE DATA
    // ==========================================
    public function update_manual() {
        $this->load->model('M_admin');
        
        $this->form_validation->set_rules('id_manual', 'ID Manual', 'required');
        $this->form_validation->set_rules('id_pos', 'Pos', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('superadmin/kelola_manual');
        }
        
        $result = $this->M_admin->update_manual_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal'))));
    }

    public function update_bendungan() {
        $this->load->model('M_admin');
        
        $this->form_validation->set_rules('id_bendungan', 'ID Bendungan', 'required');
        $this->form_validation->set_rules('id_pos', 'Pos', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('superadmin/kelola_manual');
        }
        
        $post = $this->input->post();
        $post['tahun_mulai_pembangunan'] = $this->input->post('tahun_mulai_pembangunan');
        $post['tipe_bendungan'] = $this->input->post('tipe_bendungan');
        $post['elevasi_mercu'] = $this->input->post('elevasi_mercu');
        $post['luas_das'] = $this->input->post('luas_das');
        
        $result = $this->M_admin->update_manual_bendungan($post);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal'))));
    }

    public function update_bendung() {
        $this->load->model('M_admin');
        
        $this->form_validation->set_rules('id_bendung', 'ID Bendung', 'required');
        $this->form_validation->set_rules('id_pos', 'Pos', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('superadmin/kelola_manual');
        }
        
        $result = $this->M_admin->update_manual_bendung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal'))));
    }

    // ==========================================
    // HAPUS DATA
    // ==========================================
    public function hapus_manual($id) {
        $this->load->model('M_admin');
        $result = $this->M_admin->delete_manual_pos($id, null);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    public function hapus_bendungan($id) {
        $this->load->model('M_admin');
        $result = $this->M_admin->delete_manual_bendungan($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    public function hapus_bendung($id) {
        $this->load->model('M_admin');
        $result = $this->M_admin->delete_manual_bendung($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    // ==========================================
    // KELOLA EMBUNG
    // ==========================================
    public function kelola_embung() {
        $data = $this->M_superadmin->get_embung_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_embung', $data);
    }

    public function tambah_embung() {
        $result = $this->M_superadmin->insert_embung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_embung');
    }

    public function edit_embung() {
        $result = $this->M_superadmin->update_embung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_embung');
    }

    public function hapus_embung($id) {
        $result = $this->M_superadmin->delete_embung($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_embung');
    }

    // ==========================================
    // KELOLA PENGAMAN PANTAI
    // ==========================================
    public function kelola_pengaman_pantai() {
        $data = $this->M_superadmin->get_pengaman_pantai_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_pengaman_pantai', $data);
    }

    public function tambah_pengaman_pantai() {
        $result = $this->M_superadmin->insert_pengaman_pantai($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pengaman_pantai');
    }

    public function edit_pengaman_pantai() {
        $result = $this->M_superadmin->update_pengaman_pantai($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pengaman_pantai');
    }

    public function hapus_pengaman_pantai($id) {
        $result = $this->M_superadmin->delete_pengaman_pantai($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pengaman_pantai');
    }

    // ==========================================
    // KELOLA PENGENDALI SEDIMEN
    // ==========================================
    public function kelola_pengendali_sedimen() {
        $data = $this->M_superadmin->get_pengendali_sedimen_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_pengendali_sedimen', $data);
    }

    public function tambah_pengendali_sedimen() {
        $result = $this->M_superadmin->insert_pengendali_sedimen($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pengendali_sedimen');
    }

    public function edit_pengendali_sedimen() {
        $result = $this->M_superadmin->update_pengendali_sedimen($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pengendali_sedimen');
    }

    public function hapus_pengendali_sedimen($id) {
        $result = $this->M_superadmin->delete_pengendali_sedimen($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_pengendali_sedimen');
    }

    // ==========================================
    // KELOLA DAERAH IRIGASI
    // ==========================================
    public function kelola_irigasi() {
        $data = $this->M_superadmin->get_irigasi_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_irigasi', $data);
    }

    public function tambah_irigasi() {
        $result = $this->M_superadmin->insert_irigasi($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_irigasi');
    }

    public function edit_irigasi() {
        $result = $this->M_superadmin->update_irigasi($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_irigasi');
    }

    public function hapus_irigasi($id) {
        $result = $this->M_superadmin->delete_irigasi($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_irigasi');
    }

    // ==========================================
    // EXPORT & IMPORT DATA
    // ==========================================

    public function export_import() {
        $data = [
            'app_name'   => 'HydroSmart',
            'title'      => 'Export & Import Data',
            'admin_name' => $this->session->userdata('nama_lengkap'),
            'modules'    => [
                'embung' => 'Embung',
                'pengaman_pantai' => 'Pengaman Pantai',
                'pengendali_sedimen' => 'Pengendali Sedimen',
                'irigasi' => 'Daerah Irigasi',
                'bendung' => 'Data Bendung',
                'bendungan' => 'Data Bendungan',
                'pos_manual_pch' => 'Data Manual PCH (Curah Hujan)',
                'pos_manual_pda' => 'Data Manual PDA (TMA)',
            ],
            'periods' => [
                'all' => 'Semua Data',
                'daily' => 'Harian',
                'monthly' => 'Bulanan',
                'yearly' => 'Tahunan'
            ]
        ];
        $this->_render('superadmin/v_export_import', $data);
    }

    public function export_csv() {
        $module = $this->input->get('module');
        $period = $this->input->get('period') ?? 'all';
        $date = $this->input->get('date') ?? date('Y-m-d');
        
        $data = $this->_get_export_data($module, $period, $date);
        
        if (empty($data)) {
            $this->session->set_flashdata('error', 'Tidak ada data untuk diexport.');
            redirect('superadmin/export_import');
        }
        
        $headers = array_keys((array)$data[0]);
        $module_label = str_replace('_', ' ', ucwords(str_replace('_manual', '', $module)));
        $filename = str_replace('_', '-', $module) . '_' . date('Y-m-d_H-i') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        $delimiter = ';';
        
        fputcsv($output, ['=== ' . strtoupper($module_label) . ' ==='], $delimiter);
        fputcsv($output, ['Periode', $this->_get_period_label($period, $date)], $delimiter);
        fputcsv($output, ['Total Data', count($data) . ' record'], $delimiter);
        fputcsv($output, ['Dicetak', date('d-m-Y H:i:s')], $delimiter);
        fputcsv($output, [], $delimiter);
        
        $header_labels = array_map(function($h) {
            return str_replace('_', ' ', ucwords($h));
        }, $headers);
        fputcsv($output, $header_labels, $delimiter);
        
        foreach ($data as $row) {
            $row_data = [];
            foreach ((array)$row as $value) {
                $row_data[] = $value ?? '';
            }
            fputcsv($output, $row_data, $delimiter);
        }
        
        fclose($output);
        exit;
    }

    public function export_pdf() {
        $module = $this->input->get('module');
        $period = $this->input->get('period') ?? 'all';
        $date = $this->input->get('date') ?? date('Y-m-d');
        
        $data = $this->_get_export_data($module, $period, $date);
        
        if (empty($data)) {
            $this->session->set_flashdata('error', 'Tidak ada data untuk diexport.');
            redirect('superadmin/export_import');
        }
        
        $this->_load_dompdf();
        
        $dompdf = new Dompdf\Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('defaultFont', 'sans-serif');
        
        $module_label = str_replace('_', ' ', ucwords(str_replace('_manual', '', $module)));
        $period_label = $this->_get_period_label($period, $date);
        $headers = array_keys((array)$data[0]);
        
        $html = $this->_generate_pdf_html($module_label, $period_label, $headers, $data);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = str_replace('_', '-', $module) . '_' . date('Y-m-d_H-i') . '.pdf';
        $dompdf->stream($filename, array("Attachment" => 1));
        exit;
    }

    public function import_csv() {
        $module = $this->input->post('module');
        
        if (empty($_FILES['file_csv']['name'])) {
            $this->session->set_flashdata('error', 'Silakan pilih file CSV terlebih dahulu.');
            redirect('superadmin/export_import');
        }
        
        $ext = pathinfo($_FILES['file_csv']['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), ['csv', 'txt'])) {
            $this->session->set_flashdata('error', 'Hanya file CSV yang diperbolehkan.');
            redirect('superadmin/export_import');
        }
        
        $file = fopen($_FILES['file_csv']['tmp_name'], 'r');
        $first_line = fgets($file);
        rewind($file);
        
        $delimiter = ',';
        if (strpos($first_line, ';') !== false) {
            $delimiter = ';';
        } elseif (strpos($first_line, "\t") !== false) {
            $delimiter = "\t";
        }
        
        $headers = fgetcsv($file, 0, $delimiter);
        if ($headers === FALSE) {
            fclose($file);
            $this->session->set_flashdata('error', 'File CSV tidak valid atau kosong.');
            redirect('superadmin/export_import');
        }
        
        $headers = array_map('trim', $headers);
        
        // Skip baris info jika ada
        foreach ($headers as $h) {
            if (strpos($h, '===') !== false || strpos($h, 'Periode') !== false) {
                $headers = fgetcsv($file, 0, $delimiter);
                if ($headers === FALSE) {
                    fclose($file);
                    $this->session->set_flashdata('error', 'Format file CSV tidak sesuai.');
                    redirect('superadmin/export_import');
                }
                $headers = array_map('trim', $headers);
                break;
            }
        }
        
        $field_map = $this->_get_field_mapping($module);
        
        $success = 0;
        $failed = 0;
        $errors = [];
        $row_index = 1;
        
        while (($row = fgetcsv($file, 0, $delimiter)) !== FALSE) {
            $row_index++;
            
            if (empty(array_filter($row))) continue;
            
            if (count($row) < count($headers)) {
                $failed++;
                $errors[] = 'Baris ' . $row_index . ': Jumlah kolom tidak sesuai';
                continue;
            }
            
            $data = [];
            foreach ($headers as $col_index => $header) {
                $field = $field_map[$header] ?? null;
                if ($field && isset($row[$col_index])) {
                    $value = trim($row[$col_index]);
                    if (is_numeric(str_replace(',', '.', str_replace('.', '', $value)))) {
                        $value = floatval(str_replace(',', '.', str_replace('.', '', $value)));
                    }
                    $data[$field] = $value;
                }
            }
            
            if (empty($data)) continue;
            
            $result = $this->_import_data($module, $data);
            if ($result['status'] == 'success') {
                $success++;
            } else {
                $failed++;
                $errors[] = 'Baris ' . $row_index . ': ' . $result['message'];
            }
        }
        
        fclose($file);
        
        $message = "Import selesai! Berhasil: $success, Gagal: $failed";
        if (!empty($errors)) {
            $message .= ' | Detail: ' . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= ' ... dan ' . (count($errors) - 5) . ' error lainnya.';
            }
        }
        
        $this->session->set_flashdata($failed > 0 ? 'warning' : 'success', $message);
        redirect('superadmin/export_import');
    }

   /**
 * Download Template CSV untuk Import
 */
public function download_template_csv() {
    $module = $this->input->get('module');
    
    if (empty($module)) {
        $this->session->set_flashdata('error', 'Silakan pilih modul terlebih dahulu.');
        redirect('superadmin/export_import');
    }
    
    $field_map = $this->_get_field_mapping($module);
    
    // Untuk PCH dan PDA, gunakan template khusus
    if ($module == 'pos_manual_pch' || $module == 'pos_manual_pda') {
        $this->_download_template_hidrologi($module);
        return;
    }
    
    // Untuk modul lainnya
    $headers = array_keys($field_map);
    $filename = 'template_' . $module . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo "\xEF\xBB\xBF";
    $output = fopen('php://output', 'w');
    $delimiter = ';';
    
    fputcsv($output, ['=== TEMPLATE IMPORT ' . strtoupper(str_replace('_', ' ', $module)) . ' ==='], $delimiter);
    fputcsv($output, ['Kolom yang wajib diisi: ' . implode(', ', $headers)], $delimiter);
    fputcsv($output, ['Format angka: gunakan titik (.) untuk desimal'], $delimiter);
    fputcsv($output, ['Tanggal: gunakan format YYYY-MM-DD'], $delimiter);
    fputcsv($output, [], $delimiter);
    
    fputcsv($output, $headers, $delimiter);
    
    $example = [];
    foreach ($headers as $h) {
        $example[] = 'Contoh_' . str_replace(' ', '_', $h);
    }
    fputcsv($output, $example, $delimiter);
    
    fclose($output);
    exit;
}

/**
 * Download Template Khusus untuk PCH dan PDA
 */
private function _download_template_hidrologi($module) {
    $is_pch = ($module == 'pos_manual_pch');
    $tipe = $is_pch ? 'PCH' : 'PDA';
    $satuan = $is_pch ? 'mm' : 'cm';
    $nama_modul = $is_pch ? 'Curah Hujan (PCH)' : 'Tinggi Muka Air (PDA)';
    $filename = 'template_' . $module . '.csv';
    
    // Buat data template untuk 31 hari
    $times = ['07:00:00', '12:00:00', '17:00:00'];
    $dates = [];
    for ($i = 1; $i <= 31; $i++) {
        $dates[] = '2026-01-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    }
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo "\xEF\xBB\xBF";
    $output = fopen('php://output', 'w');
    $delimiter = ';';
    
    // ==========================================
    // HEADER INFO
    // ==========================================
    fputcsv($output, ['=== TEMPLATE IMPORT DATA ' . $nama_modul . ' ==='], $delimiter);
    fputcsv($output, ['Tipe Pos', $tipe], $delimiter);
    fputcsv($output, ['Satuan', $satuan], $delimiter);
    fputcsv($output, ['Format Tanggal', 'YYYY-MM-DD'], $delimiter);
    fputcsv($output, ['Format Waktu', 'HH:MM:SS (24 jam)'], $delimiter);
    fputcsv($output, ['Kolom Wajib Diisi', 'time, date, value'], $delimiter);
    fputcsv($output, [], $delimiter);
    
    // ==========================================
    // HEADER KOLOM (RAPIH DENGAN SPASI)
    // ==========================================
    fputcsv($output, ['Waktu (Jam)', 'Tanggal', 'Nilai (' . $satuan . ')', 'Unit'], $delimiter);
    
    // ==========================================
    // DATA TEMPLATE (31 Hari x 3 Waktu)
    // ==========================================
    foreach ($dates as $date) {
        foreach ($times as $time) {
            fputcsv($output, [$time, $date, '', $satuan], $delimiter);
        }
    }
    
    // ==========================================
    // CATATAN KAKI
    // ==========================================
    fputcsv($output, [], $delimiter);
    fputcsv($output, ['=== CATATAN ==='], $delimiter);
    fputcsv($output, ['1. Isi kolom "Nilai" dengan angka sesuai pengukuran'], $delimiter);
    fputcsv($output, ['2. Waktu pengukuran bisa disesuaikan (07:00, 12:00, 17:00)'], $delimiter);
    fputcsv($output, ['3. Tanggal bisa diubah sesuai kebutuhan'], $delimiter);
    fputcsv($output, ['4. Unit otomatis: ' . $satuan], $delimiter);
    fputcsv($output, ['5. Kolom "Unit" tidak wajib diisi'], $delimiter);
    
    fclose($output);
    exit;
}

    // ==========================================
    // EXPORT TELEMETRI
    // ==========================================

    public function export_telemetri() {
        $data = [
            'app_name'    => 'HydroSmart',
            'title'       => 'Export Data Telemetri',
            'admin_name'  => $this->session->userdata('nama_lengkap'),
            'pos_list'    => $this->db->select('id_pos, nama_pos, device_id_telemetry, tipe_pos')
                                    ->where('device_id_telemetry IS NOT NULL')
                                    ->where('device_id_telemetry !=', '')
                                    ->order_by('nama_pos', 'ASC')
                                    ->get('master_pos')
                                    ->result(),
            'periods' => [
                'hourly' => 'Per Jam',
                'daily' => 'Harian',
                'weekly' => 'Mingguan',
                'monthly' => 'Bulanan',
                'custom' => 'Kustom (Jam & Menit)'
            ]
        ];
        $this->_render('superadmin/v_export_telemetri', $data);
    }

    public function export_telemetri_csv() {
        $this->load->helper('download');
        
        $id_pos = $this->input->get('id_pos');
        $period = $this->input->get('period') ?? 'daily';
        $date = $this->input->get('date') ?? date('Y-m-d');
        $start_time = $this->input->get('start_time') ?? '00:00';
        $end_time = $this->input->get('end_time') ?? '23:59';
        
        if (empty($id_pos)) {
            $this->session->set_flashdata('error', 'Silakan pilih pos telemetri.');
            redirect('superadmin/export_telemetri');
        }
        
        $data = $this->_get_telemetri_export_data($id_pos, $period, $date, $start_time, $end_time);
        
        if (empty($data)) {
            $this->session->set_flashdata('error', 'Tidak ada data telemetri untuk diexport.');
            redirect('superadmin/export_telemetri');
        }
        
        $pos = $this->db->select('nama_pos, tipe_pos, device_id_telemetry')
                        ->where('id_pos', $id_pos)
                        ->get('master_pos')
                        ->row();
        
        $filename = 'telemetri_' . str_replace(' ', '_', $pos->nama_pos) . '_' . date('Y-m-d_H-i') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        $delimiter = ';';
        
        fputcsv($output, ['=== DATA TELEMETRI ==='], $delimiter);
        fputcsv($output, ['Nama Pos', $pos->nama_pos], $delimiter);
        fputcsv($output, ['Tipe Pos', $pos->tipe_pos], $delimiter);
        fputcsv($output, ['Device ID', $pos->device_id_telemetry], $delimiter);
        fputcsv($output, ['Periode', $this->_get_period_label_telemetri($period, $date, $start_time, $end_time)], $delimiter);
        fputcsv($output, ['Total Data', count($data) . ' record'], $delimiter);
        fputcsv($output, ['Dicetak', date('d-m-Y H:i:s')], $delimiter);
        fputcsv($output, [], $delimiter);
        
        $headers = ['No', 'Tanggal', 'Jam', 'Baterai (V)', 'Curah Hujan (mm)', 'TMA (m)', 'Status'];
        fputcsv($output, $headers, $delimiter);
        
        $no = 1;
        foreach ($data as $row) {
            $row_data = [
                $no++,
                date('d-m-Y', strtotime($row->received_at)),
                date('H:i:s', strtotime($row->received_at)),
                number_format($row->batt ?? 0, 1),
                number_format($row->rain ?? 0, 1),
                number_format($row->wlevel ?? 0, 2),
                $row->status ?? '-'
            ];
            fputcsv($output, $row_data, $delimiter);
        }
        
        fclose($output);
        exit;
    }

    public function export_telemetri_pdf() {
        $id_pos = $this->input->get('id_pos');
        $period = $this->input->get('period') ?? 'daily';
        $date = $this->input->get('date') ?? date('Y-m-d');
        $start_time = $this->input->get('start_time') ?? '00:00';
        $end_time = $this->input->get('end_time') ?? '23:59';
        
        if (empty($id_pos)) {
            $this->session->set_flashdata('error', 'Silakan pilih pos telemetri.');
            redirect('superadmin/export_telemetri');
        }
        
        $data = $this->_get_telemetri_export_data($id_pos, $period, $date, $start_time, $end_time);
        
        if (empty($data)) {
            $this->session->set_flashdata('error', 'Tidak ada data telemetri untuk diexport.');
            redirect('superadmin/export_telemetri');
        }
        
        $pos = $this->db->select('nama_pos, tipe_pos, device_id_telemetry')
                        ->where('id_pos', $id_pos)
                        ->get('master_pos')
                        ->row();
        
        $this->_load_dompdf();
        
        $dompdf = new Dompdf\Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('defaultFont', 'sans-serif');
        
        $period_label = $this->_get_period_label_telemetri($period, $date, $start_time, $end_time);
        $html = $this->_generate_telemetri_pdf_html($pos, $period_label, $data);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'telemetri_' . str_replace(' ', '_', $pos->nama_pos) . '_' . date('Y-m-d_H-i') . '.pdf';
        $dompdf->stream($filename, array("Attachment" => 1));
        exit;
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    private function _load_dompdf() {
        $paths = [
            APPPATH . 'third_party/dompdf/autoload.inc.php',
            APPPATH . 'vendor/autoload.php',
            FCPATH . 'vendor/autoload.php',
            APPPATH . 'third_party/dompdf/vendor/autoload.php',
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
        
        show_error('Library Dompdf tidak ditemukan. Silakan download Dompdf dan letakkan di application/third_party/dompdf/');
    }

    private function _generate_pdf_html($module_label, $period_label, $headers, $data) {
        $max_cols = 12;
        if (count($headers) > $max_cols) {
            $headers = array_slice($headers, 0, $max_cols);
            $data = array_map(function($row) use ($max_cols) {
                return array_slice((array)$row, 0, $max_cols);
            }, $data);
        }
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Export ' . $module_label . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 11px; padding: 15px; }
                .header { text-align: center; margin-bottom: 15px; border-bottom: 3px solid #feb700; padding-bottom: 10px; }
                .header h1 { color: #0a2a4a; margin: 0; font-size: 18px; }
                .header .subtitle { color: #666; margin: 3px 0; font-size: 10px; }
                .header .total { font-size: 10px; font-weight: bold; color: #0a2a4a; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }
                th { background-color: #feb700; color: #0a2a4a; padding: 6px 8px; text-align: left; font-weight: bold; border: 1px solid #e5a500; }
                td { padding: 5px 8px; border: 1px solid #ddd; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .footer { margin-top: 15px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; }
                .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 8px; font-weight: bold; }
                .badge-success { background: #d1fae5; color: #065f46; }
                .badge-warning { background: #fef3c7; color: #92400e; }
                .badge-danger { background: #fee2e2; color: #991b1b; }
                .badge-info { background: #dbeafe; color: #1e40af; }
                .badge-primary { background: #e0e7ff; color: #3730a3; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📊 ' . $module_label . '</h1>
                <div class="subtitle">Periode: ' . $period_label . ' | Dicetak: ' . date('d-m-Y H:i:s') . '</div>
                <div class="total">Total Data: ' . number_format(count($data)) . ' record</div>
            </div>
            <table>
                <thead>
                    <tr>';
        
        foreach ($headers as $header) {
            $label = str_replace('_', ' ', ucwords($header));
            if (strlen($label) > 25) {
                $label = substr($label, 0, 22) . '...';
            }
            $html .= '<th>' . $label . '</th>';
        }
        
        $html .= '</tr></thead><tbody>';
        
        $row_count = 0;
        foreach ($data as $item) {
            $row_count++;
            $html .= '<tr>';
            foreach ((array)$item as $key => $value) {
                $display = htmlspecialchars($value ?? '-');
                if (strpos($key, 'kondisi') !== false || strpos($key, 'status') !== false) {
                    $value_str = (string)$value;
                    if (strpos($value_str, 'Baik') !== false || strpos($value_str, 'Beroperasi') !== false) {
                        $display = '<span class="badge badge-success">' . $value_str . '</span>';
                    } elseif (strpos($value_str, 'Rusak Ringan') !== false) {
                        $display = '<span class="badge badge-warning">' . $value_str . '</span>';
                    } elseif (strpos($value_str, 'Rusak Berat') !== false || strpos($value_str, 'Tidak Beroperasi') !== false) {
                        $display = '<span class="badge badge-danger">' . $value_str . '</span>';
                    } elseif (!empty($value_str)) {
                        $display = '<span class="badge badge-primary">' . $value_str . '</span>';
                    }
                }
                $html .= '<td>' . $display . '</td>';
            }
            $html .= '</tr>';
            
            if ($row_count >= 500) {
                $html .= '<tr><td colspan="' . count($headers) . '" style="text-align:center;font-style:italic;color:#999;padding:10px;">... dan ' . (count($data) - 500) . ' data lainnya. Export CSV untuk semua data.</td></tr>';
                break;
            }
        }
        
        $html .= '</tbody></table>
            <div class="footer">
                <p>Dicetak dari Sistem HydroSmart - BBWS Mesuji Sekampung</p>
                <p>' . date('d-m-Y H:i:s') . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    private function _get_period_label($period, $date) {
        $date_obj = new DateTime($date);
        switch ($period) {
            case 'all': return 'Semua Data';
            case 'daily': return 'Harian - ' . $date_obj->format('d-m-Y');
            case 'monthly': return 'Bulanan - ' . $date_obj->format('F Y');
            case 'yearly': return 'Tahunan - ' . $date_obj->format('Y');
            default: return $date;
        }
    }

    private function _get_period_label_telemetri($period, $date, $start_time, $end_time) {
        $date_obj = new DateTime($date);
        
        switch ($period) {
            case 'hourly':
                return 'Per Jam - ' . $date_obj->format('d-m-Y') . ' (' . $start_time . ' - ' . $end_time . ')';
            case 'daily':
                return 'Harian - ' . $date_obj->format('d-m-Y');
            case 'weekly':
                $start = clone $date_obj;
                $start->modify('monday this week');
                $end = clone $start;
                $end->modify('sunday this week');
                return 'Mingguan - ' . $start->format('d-m-Y') . ' s/d ' . $end->format('d-m-Y');
            case 'monthly':
                return 'Bulanan - ' . $date_obj->format('F Y');
            case 'custom':
                return 'Kustom - ' . $date_obj->format('d-m-Y') . ' (' . $start_time . ' - ' . $end_time . ')';
            default:
                return $date_obj->format('d-m-Y');
        }
    }

    private function _generate_telemetri_pdf_html($pos, $period_label, $data) {
        $total = count($data);
        $tipe_icon = ($pos->tipe_pos == 'PCH') ? '🌧️' : '📊';
        
        $max_rain = 0;
        $max_wlevel = 0;
        $avg_batt = 0;
        $total_rain = 0;
        
        foreach ($data as $d) {
            if ($d->rain > $max_rain) $max_rain = $d->rain;
            if ($d->wlevel > $max_wlevel) $max_wlevel = $d->wlevel;
            $avg_batt += $d->batt ?? 0;
            $total_rain += $d->rain ?? 0;
        }
        $avg_batt = ($total > 0) ? $avg_batt / $total : 0;
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Export Telemetri - ' . $pos->nama_pos . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 11px; padding: 15px; }
                .header { text-align: center; margin-bottom: 15px; border-bottom: 3px solid #feb700; padding-bottom: 10px; }
                .header h1 { color: #0a2a4a; margin: 0; font-size: 20px; }
                .header .subtitle { color: #666; margin: 3px 0; font-size: 10px; }
                .info-grid { display: flex; gap: 20px; flex-wrap: wrap; margin: 10px 0 15px 0; padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
                .info-item { font-size: 10px; }
                .info-item label { font-weight: bold; color: #475569; }
                .info-item span { color: #0a2a4a; }
                .stats { display: flex; gap: 15px; flex-wrap: wrap; margin: 10px 0 15px 0; }
                .stat-box { padding: 8px 14px; background: #f1f5f9; border-radius: 6px; border-left: 3px solid #feb700; }
                .stat-box .label { font-size: 8px; color: #94a3b8; text-transform: uppercase; }
                .stat-box .value { font-size: 14px; font-weight: bold; color: #0a2a4a; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }
                th { background-color: #feb700; color: #0a2a4a; padding: 6px 8px; text-align: left; font-weight: bold; border: 1px solid #e5a500; }
                td { padding: 5px 8px; border: 1px solid #ddd; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 8px; font-weight: bold; }
                .badge-success { background: #d1fae5; color: #065f46; }
                .badge-warning { background: #fef3c7; color: #92400e; }
                .badge-danger { background: #fee2e2; color: #991b1b; }
                .badge-info { background: #dbeafe; color: #1e40af; }
                .footer { margin-top: 15px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>' . $tipe_icon . ' Data Telemetri - ' . htmlspecialchars($pos->nama_pos) . '</h1>
                <div class="subtitle">Periode: ' . $period_label . ' | Dicetak: ' . date('d-m-Y H:i:s') . '</div>
            </div>
            <div class="info-grid">
                <div class="info-item"><label>Nama Pos:</label> <span>' . htmlspecialchars($pos->nama_pos) . '</span></div>
                <div class="info-item"><label>Tipe:</label> <span>' . htmlspecialchars($pos->tipe_pos) . '</span></div>
                <div class="info-item"><label>Device ID:</label> <span>' . htmlspecialchars($pos->device_id_telemetry) . '</span></div>
                <div class="info-item"><label>Total Data:</label> <span>' . number_format($total) . ' record</span></div>
            </div>
            <div class="stats">
                <div class="stat-box"><div class="label">Total Curah Hujan</div><div class="value">' . number_format($total_rain, 1) . ' mm</div></div>
                <div class="stat-box"><div class="label">Maks Curah Hujan</div><div class="value">' . number_format($max_rain, 1) . ' mm</div></div>
                <div class="stat-box"><div class="label">Maks TMA</div><div class="value">' . number_format($max_wlevel, 2) . ' m</div></div>
                <div class="stat-box"><div class="label">Rata-rata Baterai</div><div class="value">' . number_format($avg_batt, 1) . ' V</div></div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Baterai (V)</th>
                        <th>Curah Hujan (mm)</th>
                        <th>TMA (m)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        $no = 1;
        $row_count = 0;
        foreach ($data as $d) {
            $row_count++;
            $status_badge = '';
            $status = $d->status ?? '-';
            if (strtolower($status) == 'cerah' || strtolower($status) == 'normal') {
                $status_badge = '<span class="badge badge-success">' . $status . '</span>';
            } elseif (strtolower($status) == 'ringan' || strtolower($status) == 'sedang') {
                $status_badge = '<span class="badge badge-warning">' . $status . '</span>';
            } elseif (strtolower($status) == 'lebat' || strtolower($status) == 'sangatlebat') {
                $status_badge = '<span class="badge badge-danger">' . $status . '</span>';
            } else {
                $status_badge = '<span class="badge badge-info">' . $status . '</span>';
            }
            
            $html .= '<tr>
                <td>' . $no++ . '</td>
                <td>' . date('d-m-Y', strtotime($d->received_at)) . '</td>
                <td>' . date('H:i:s', strtotime($d->received_at)) . '</td>
                <td>' . number_format($d->batt ?? 0, 1) . '</td>
                <td>' . number_format($d->rain ?? 0, 1) . '</td>
                <td>' . number_format($d->wlevel ?? 0, 2) . '</td>
                <td>' . $status_badge . '</td>
            </tr>';
            
            if ($row_count >= 500) {
                $html .= '<tr><td colspan="7" style="text-align:center;font-style:italic;color:#999;padding:10px;">... dan ' . ($total - 500) . ' data lainnya. Export CSV untuk semua data.</td></tr>';
                break;
            }
        }
        
        $html .= '</tbody></table>
            <div class="footer">
                <p>Dicetak dari Sistem HydroSmart - BBWS Mesuji Sekampung</p>
                <p>' . date('d-m-Y H:i:s') . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    private function _get_telemetri_export_data($id_pos, $period, $date, $start_time, $end_time) {
        $this->db->select('received_at, batt, rain, wlevel, status');
        $this->db->from('data_telemetri');
        $this->db->where('id_pos', $id_pos);
        
        $date_obj = new DateTime($date);
        
        switch ($period) {
            case 'hourly':
                $this->db->where('DATE(received_at)', $date);
                $this->db->where('TIME(received_at) >=', $start_time);
                $this->db->where('TIME(received_at) <=', $end_time);
                break;
            case 'daily':
                $this->db->where('DATE(received_at)', $date);
                break;
            case 'weekly':
                $start = clone $date_obj;
                $start->modify('monday this week');
                $end = clone $start;
                $end->modify('sunday this week');
                $this->db->where('received_at >=', $start->format('Y-m-d 00:00:00'));
                $this->db->where('received_at <=', $end->format('Y-m-d 23:59:59'));
                break;
            case 'monthly':
                $this->db->where('MONTH(received_at)', $date_obj->format('m'));
                $this->db->where('YEAR(received_at)', $date_obj->format('Y'));
                break;
            case 'custom':
                $this->db->where('DATE(received_at)', $date);
                if (!empty($start_time)) {
                    $this->db->where('TIME(received_at) >=', $start_time);
                }
                if (!empty($end_time)) {
                    $this->db->where('TIME(received_at) <=', $end_time);
                }
                break;
            default:
                $this->db->where('DATE(received_at)', $date);
        }
        
        $this->db->order_by('received_at', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    private function _get_field_mapping($module) {
        $maps = [
            'embung' => [
                'Nama Pos' => 'nama_pos',
                'Sungai' => 'sungai',
                'Wilayah Sungai' => 'wilayah_sungai',
                'Latitude' => 'lat',
                'Longitude' => 'lng',
                'NWL (m)' => 'nwl',
                'Volume NWL (jt.m³)' => 'nwl_volume',
                'Luas NWL (km²)' => 'nwl_luas',
                'Kapasitas Volume (m³)' => 'kapasitas_volume',
                'Elevasi Puncak (m)' => 'elevasi_puncak',
                'Tinggi Embung (m)' => 'tinggi_embung',
                'Panjang Tubuh (m)' => 'panjang_tubuh',
                'Tahun Mulai Pembangunan' => 'tahun_mulai_pembangunan',
            ],
            'pengaman_pantai' => [
                'Kode Integrasi' => 'kode_integrasi',
                'Nama Aset' => 'nama_aset',
                'Jenis Bangunan' => 'jenis_bangunan',
                'Sungai' => 'sungai',
                'Wilayah Sungai' => 'wilayah_sungai',
                'Lat Awal' => 'lat_awal',
                'Lng Awal' => 'lng_awal',
                'Lat Akhir' => 'lat_akhir',
                'Lng Akhir' => 'lng_akhir',
                'Panjang (m)' => 'panjang',
                'Elevasi Puncak (m)' => 'elevasi_puncak',
                'Lebar Puncak (m)' => 'lebar_puncak',
                'Tahun Dibangun' => 'tahun_dibangun',
                'Kabupaten/Kota' => 'kabupaten_kota',
                'Kecamatan' => 'kecamatan',
                'Kelurahan/Desa' => 'kelurahan',
                'Manfaat' => 'manfaat',
                'Keterangan' => 'keterangan',
            ],
            'pengendali_sedimen' => [
                'Kode Integrasi' => 'kode_integrasi',
                'Nama Aset' => 'nama_aset',
                'Jenis Bangunan' => 'jenis_bangunan',
                'Sungai' => 'sungai',
                'DAS' => 'daerah_aliran_sungai',
                'Wilayah Sungai' => 'wilayah_sungai',
                'Latitude' => 'lat',
                'Longitude' => 'lng',
                'Daya Tampung (m³)' => 'daya_tampung',
                'Panjang (m)' => 'panjang',
                'Lebar (m)' => 'lebar',
                'Tinggi (m)' => 'tinggi',
                'Tahun Dibangun' => 'tahun_dibangun',
                'Kabupaten/Kota' => 'kabupaten_kota',
                'Kecamatan' => 'kecamatan',
                'Kelurahan/Desa' => 'kelurahan',
                'Jenis Material' => 'jenis_material',
                'Keterangan' => 'keterangan',
            ],
            'irigasi' => [
                'Kode Integrasi' => 'kode_integrasi',
                'Nama Daerah Irigasi' => 'nama_aset',
                'Jenis DI' => 'jenis_daerah_irigasi',
                'Wilayah Sungai' => 'wilayah_sungai',
                'DAS' => 'daerah_aliran_sungai',
                'Kabupaten/Kota' => 'kabupaten_kota',
                'Kecamatan' => 'kecamatan',
                'Desa/Kelurahan' => 'kelurahan',
                'Latitude' => 'latitude',
                'Longitude' => 'longitude',
                'Luas Permen (ha)' => 'luas_permen',
                'Luas Baku (ha)' => 'luas_baku',
                'Luas Potensial (ha)' => 'luas_potensial',
                'Luas Fungsional (ha)' => 'luas_fungsional',
                'Sumber Air' => 'sumber_air',
                'Jenis Bangunan Utama' => 'jenis_bangunan_utama',
                'Tahun Pembangunan' => 'tahun_pembangunan',
                'Status Pemeliharaan' => 'status_pemeliharaan',
                'Di OP Kan Oleh' => 'di_op_kan_oleh',
            ],
            'bendung' => [
                'Tanggal Pengukuran' => 'tanggal_input',
                'Nama Bendung' => 'nama_pos',
                'Sungai' => 'sungai',
                'Wilayah Sungai' => 'wilayah_sungai',
                'Curah Hujan (mm)' => 'rain',
                'Elevasi Air thd Mercu (m)' => 'elevasi_mercu',
                'Q Total (m³/dt)' => 'q_total',
                'Q FC1 (m³/dt)' => 'q_fc1',
                'Q FC2 (m³/dt)' => 'q_fc2',
                'Q Saluran Induk (m³/dt)' => 'q_sal_induk',
                'Q Limpas (m³/dt)' => 'q_limpas',
                'Q Sungai (m³/dt)' => 'q_sungai',
                'Q SPAM KPBU (m³/dt)' => 'q_spam_kpbu',
                'Sluice Gate (m³/dt)' => 'sluice_gate',
                'Bukaan Pintu (cm)' => 'bukaan_pintu',
                'Keterangan' => 'keterangan',
            ],
            'bendungan' => [
                'Tanggal Pengukuran' => 'tanggal_input',
                'Nama Bendungan' => 'nama_pos',
                'Sungai' => 'sungai',
                'Wilayah Sungai' => 'wilayah_sungai',
                'NWL (m)' => 'nwl',
                'Volume NWL (jt.m³)' => 'nwl_volume',
                'Luas NWL (km²)' => 'nwl_luas',
                'Curah Hujan (mm)' => 'rain',
                'Elevasi TMA (m)' => 'elevasi',
                'Volume Tampungan (jt.m³)' => 'volume',
                'Luas Genangan (km²)' => 'luas',
                'Inflow (m³/s)' => 'inflow',
                'PLTM (m³/s)' => 'pltm',
                'Spillway (m³/s)' => 'spillway',
                'Total Outflow (m³/s)' => 'total_outflow',
                'Status PLTA' => 'plta_status',
                'Status Irigasi' => 'irigasi_status',
                'Tail Water' => 'tail_water',
                'Rembesan V-Notch h (cm)' => 'rembesan_vnotch_h',
                'Rembesan V-Notch Q (lt/s)' => 'rembesan_vnotch_q',
                'Rembesan Pump Pit Kiri h (cm)' => 'rembesan_pump_pit_l_h',
                'Rembesan Pump Pit Kiri Q (lt/s)' => 'rembesan_pump_pit_l_q',
                'Rembesan Pump Pit Kanan h (cm)' => 'rembesan_pump_pit_r_h',
                'Rembesan Pump Pit Kanan Q (lt/s)' => 'rembesan_pump_pit_r_q',
                'Tahun Mulai Pembangunan' => 'tahun_mulai_pembangunan',
                'Tipe Bendungan' => 'tipe_bendungan',
                'Elevasi Mercu (m)' => 'elevasi_mercu',
                'Luas DAS (km²)' => 'luas_das',
                'Keterangan' => 'keterangan',
            ],
            'pos_manual_pch' => [
            'Waktu (Jam)' => 'time',
            'Tanggal' => 'date',
            'Nilai (mm)' => 'value',
            'Unit' => 'unit',
        ],
            'pos_manual_pda' => [
            'Waktu (Jam)' => 'time',
            'Tanggal' => 'date',
            'Nilai (cm)' => 'value',
            'Unit' => 'unit',
        ],
        ];
        return $maps[$module] ?? [];
    }

    private function _get_export_data($module, $period, $date) {
        $this->db->select('*');
        
        if ($module == 'embung') {
            $this->db->from('master_pos');
            $this->db->where('jenis_aset', 'embung');
            $this->db->order_by('nama_pos', 'ASC');
        } elseif ($module == 'pengaman_pantai') {
            $this->db->from('data_pengaman_pantai');
            $this->db->order_by('nama_aset', 'ASC');
        } elseif ($module == 'pengendali_sedimen') {
            $this->db->from('data_pengendali_sedimen');
            $this->db->order_by('nama_aset', 'ASC');
        } elseif ($module == 'irigasi') {
            $this->db->from('data_irigasi');
            $this->db->order_by('nama_aset', 'ASC');
        } elseif ($module == 'bendung') {
            $this->db->select('b.tanggal_input, p.nama_pos, p.sungai, p.wilayah_sungai, b.rain, b.elevasi_mercu, b.q_total, b.q_fc1, b.q_fc2, b.q_sal_induk, b.q_limpas, b.q_sungai, b.q_spam_kpbu, b.sluice_gate, b.bukaan_pintu, b.keterangan, b.created_at');
            $this->db->from('data_bendung b');
            $this->db->join('master_pos p', 'b.id_pos = p.id_pos', 'left');
            $this->db->order_by('b.tanggal_input', 'DESC');
            $this->db->order_by('b.created_at', 'DESC');
        } elseif ($module == 'bendungan') {
            $this->db->select('b.tanggal_input, p.nama_pos, p.sungai, p.wilayah_sungai, b.nwl, b.nwl_volume, b.nwl_luas, b.rain, b.elevasi, b.volume, b.luas, b.inflow, b.pltm, b.spillway, b.total_outflow, b.plta_status, b.irigasi_status, b.tail_water, b.rembesan_vnotch_h, b.rembesan_vnotch_q, b.rembesan_pump_pit_l_h, b.rembesan_pump_pit_l_q, b.rembesan_pump_pit_r_h, b.rembesan_pump_pit_r_q, b.tahun_mulai_pembangunan, b.tipe_bendungan, b.elevasi_mercu, b.luas_das, b.keterangan, b.created_at');
            $this->db->from('data_bendungan b');
            $this->db->join('master_pos p', 'b.id_pos = p.id_pos', 'left');
            $this->db->order_by('b.tanggal_input', 'DESC');
            $this->db->order_by('b.created_at', 'DESC');
        } elseif ($module == 'pos_manual_pch') {
            $this->db->select('m.tanggal_input, p.nama_pos, p.sungai, p.wilayah_sungai, m.rain, m.keterangan, m.created_at');
            $this->db->from('data_manual m');
            $this->db->join('master_pos p', 'm.id_pos = p.id_pos', 'left');
            $this->db->where('p.tipe_pos', 'PCH');
            $this->db->where('p.is_bendungan', 0);
            $this->db->where('p.is_bendung', 0);
            $this->db->order_by('m.tanggal_input', 'DESC');
            $this->db->order_by('m.created_at', 'DESC');
        } elseif ($module == 'pos_manual_pda') {
            $this->db->select('m.tanggal_input, p.nama_pos, p.sungai, p.wilayah_sungai, m.wlevel * 100 as wlevel, m.keterangan, m.created_at');
            $this->db->from('data_manual m');
            $this->db->join('master_pos p', 'm.id_pos = p.id_pos', 'left');
            $this->db->where('p.tipe_pos', 'PDA');
            $this->db->where('p.is_bendungan', 0);
            $this->db->where('p.is_bendung', 0);
            $this->db->order_by('m.tanggal_input', 'DESC');
            $this->db->order_by('m.created_at', 'DESC');
        } else {
            return [];
        }
        
        if ($period != 'all') {
            $field_date = 'created_at';
            $this->_apply_period_filter($period, $date, $field_date);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    private function _apply_period_filter($period, $date, $field) {
        $date_obj = new DateTime($date);
        
        switch ($period) {
            case 'daily':
                $this->db->where('DATE(' . $field . ')', $date);
                break;
            case 'monthly':
                $this->db->where('MONTH(' . $field . ')', $date_obj->format('m'));
                $this->db->where('YEAR(' . $field . ')', $date_obj->format('Y'));
                break;
            case 'yearly':
                $this->db->where('YEAR(' . $field . ')', $date_obj->format('Y'));
                break;
        }
    }

    private function _import_data($module, $data) {
        try {
            foreach ($data as $key => $value) {
                if ($value === '' || $value === null) {
                    $data[$key] = null;
                }
            }
            
            if ($module == 'embung') {
                $data['tipe_pos'] = 'PCH';
                $data['jenis_aset'] = 'embung';
                $data['is_bendungan'] = 0;
                $data['is_bendung'] = 0;
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('master_pos', $data);
            } elseif ($module == 'pengaman_pantai') {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('data_pengaman_pantai', $data);
            } elseif ($module == 'pengendali_sedimen') {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('data_pengendali_sedimen', $data);
            } elseif ($module == 'irigasi') {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('data_irigasi', $data);
            } elseif ($module == 'bendung') {
                $pos = $this->db->select('id_pos')->where('nama_pos', $data['nama_pos'])->where('is_bendung', 1)->get('master_pos')->row();
                if (!$pos) {
                    $pos_data = [
                        'nama_pos' => $data['nama_pos'],
                        'tipe_pos' => 'PCH',
                        'sungai' => $data['sungai'] ?? null,
                        'wilayah_sungai' => $data['wilayah_sungai'] ?? null,
                        'is_bendung' => 1,
                        'is_bendungan' => 0,
                        'jenis_aset' => 'bendung',
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('master_pos', $pos_data);
                    $id_pos = $this->db->insert_id();
                } else {
                    $id_pos = $pos->id_pos;
                }
                
                $bendung_data = [
                    'id_pos' => $id_pos,
                    'tanggal_input' => $data['tanggal_input'] ?? date('Y-m-d'),
                    'rain' => $data['rain'] ?? null,
                    'elevasi_mercu' => $data['elevasi_mercu'] ?? null,
                    'q_total' => $data['q_total'] ?? null,
                    'q_fc1' => $data['q_fc1'] ?? null,
                    'q_fc2' => $data['q_fc2'] ?? null,
                    'q_sal_induk' => $data['q_sal_induk'] ?? null,
                    'q_limpas' => $data['q_limpas'] ?? null,
                    'q_sungai' => $data['q_sungai'] ?? null,
                    'q_spam_kpbu' => $data['q_spam_kpbu'] ?? null,
                    'sluice_gate' => $data['sluice_gate'] ?? null,
                    'bukaan_pintu' => $data['bukaan_pintu'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('data_bendung', $bendung_data);
            } elseif ($module == 'bendungan') {
                $pos = $this->db->select('id_pos')->where('nama_pos', $data['nama_pos'])->where('is_bendungan', 1)->get('master_pos')->row();
                if (!$pos) {
                    $pos_data = [
                        'nama_pos' => $data['nama_pos'],
                        'tipe_pos' => 'PCH',
                        'sungai' => $data['sungai'] ?? null,
                        'wilayah_sungai' => $data['wilayah_sungai'] ?? null,
                        'is_bendungan' => 1,
                        'is_bendung' => 0,
                        'jenis_aset' => 'bendungan',
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('master_pos', $pos_data);
                    $id_pos = $this->db->insert_id();
                } else {
                    $id_pos = $pos->id_pos;
                }
                
                $bendungan_data = [
                    'id_pos' => $id_pos,
                    'tanggal_input' => $data['tanggal_input'] ?? date('Y-m-d'),
                    'nwl' => $data['nwl'] ?? null,
                    'nwl_volume' => $data['nwl_volume'] ?? null,
                    'nwl_luas' => $data['nwl_luas'] ?? null,
                    'rain' => $data['rain'] ?? null,
                    'elevasi' => $data['elevasi'] ?? null,
                    'volume' => $data['volume'] ?? null,
                    'luas' => $data['luas'] ?? null,
                    'inflow' => $data['inflow'] ?? null,
                    'pltm' => $data['pltm'] ?? null,
                    'spillway' => $data['spillway'] ?? null,
                    'total_outflow' => $data['total_outflow'] ?? null,
                    'plta_status' => $data['plta_status'] ?? null,
                    'irigasi_status' => $data['irigasi_status'] ?? null,
                    'tail_water' => $data['tail_water'] ?? null,
                    'rembesan_vnotch_h' => $data['rembesan_vnotch_h'] ?? null,
                    'rembesan_vnotch_q' => $data['rembesan_vnotch_q'] ?? null,
                    'rembesan_pump_pit_l_h' => $data['rembesan_pump_pit_l_h'] ?? null,
                    'rembesan_pump_pit_l_q' => $data['rembesan_pump_pit_l_q'] ?? null,
                    'rembesan_pump_pit_r_h' => $data['rembesan_pump_pit_r_h'] ?? null,
                    'rembesan_pump_pit_r_q' => $data['rembesan_pump_pit_r_q'] ?? null,
                    'tahun_mulai_pembangunan' => $data['tahun_mulai_pembangunan'] ?? null,
                    'tipe_bendungan' => $data['tipe_bendungan'] ?? null,
                    'elevasi_mercu' => $data['elevasi_mercu'] ?? null,
                    'luas_das' => $data['luas_das'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('data_bendungan', $bendungan_data);
           } elseif ($module == 'pos_manual_pch') {
            // Cari id_pos dari nama_pos
            $pos = $this->db->select('id_pos')
                           ->where('nama_pos', $data['nama_pos'])
                           ->where('tipe_pos', 'PCH')
                           ->where('is_bendungan', 0)
                           ->where('is_bendung', 0)
                           ->get('master_pos')
                           ->row();
            
            if (!$pos) {
                // Buat pos baru jika belum ada
                $pos_data = [
                    'nama_pos' => $data['nama_pos'],
                    'tipe_pos' => 'PCH',
                    'sungai' => $data['sungai'] ?? null,
                    'wilayah_sungai' => $data['wilayah_sungai'] ?? null,
                    'is_bendungan' => 0,
                    'is_bendung' => 0,
                    'jenis_aset' => 'pch',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('master_pos', $pos_data);
                $id_pos = $this->db->insert_id();
            } else {
                $id_pos = $pos->id_pos;
            }
               // Format tanggal dan waktu
            $tanggal = $data['date'] ?? date('Y-m-d');
            $waktu = $data['time'] ?? '00:00:00';
            $datetime = $tanggal . ' ' . $waktu;
            
            // Insert ke data_manual
            $manual_data = [
                'id_pos' => $id_pos,
                'tanggal_input' => $tanggal,
                'rain' => $data['value'] ?? null,
                'wlevel' => null,
                'keterangan' => 'Import dari template - Jam: ' . $waktu,
                'created_at' => $datetime
            ];
            $this->db->insert('data_manual', $manual_data);
            
        // ==========================================
        // MODUL BARU: POS MANUAL PDA
        // ==========================================
        } elseif ($module == 'pos_manual_pda') {
            // Cari id_pos dari nama_pos
            $pos = $this->db->select('id_pos')
                           ->where('nama_pos', $data['nama_pos'])
                           ->where('tipe_pos', 'PDA')
                           ->where('is_bendungan', 0)
                           ->where('is_bendung', 0)
                           ->get('master_pos')
                           ->row();
            
            if (!$pos) {
                // Buat pos baru jika belum ada
                $pos_data = [
                    'nama_pos' => $data['nama_pos'],
                    'tipe_pos' => 'PDA',
                    'sungai' => $data['sungai'] ?? null,
                    'wilayah_sungai' => $data['wilayah_sungai'] ?? null,
                    'is_bendungan' => 0,
                    'is_bendung' => 0,
                    'jenis_aset' => 'pda',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('master_pos', $pos_data);
                $id_pos = $this->db->insert_id();
            } else {
                $id_pos = $pos->id_pos;
            }
            
            // Format tanggal dan waktu
            $tanggal = $data['date'] ?? date('Y-m-d');
            $waktu = $data['time'] ?? '00:00:00';
            $datetime = $tanggal . ' ' . $waktu;
            
            // Konversi cm ke meter
            $wlevel_meter = null;
            if (!empty($data['value'])) {
                $wlevel_meter = floatval($data['value']) / 100;
            }
            
            // Insert ke data_manual
            $manual_data = [
                'id_pos' => $id_pos,
                'tanggal_input' => $tanggal,
                'rain' => null,
                'wlevel' => $wlevel_meter,
                'keterangan' => 'Import dari template - Jam: ' . $waktu,
                'created_at' => $datetime
            ];
            $this->db->insert('data_manual', $manual_data);
            
        } else {
            return ['status' => 'error', 'message' => 'Module tidak dikenal'];
        }
        
        return $this->db->affected_rows() > 0 
            ? ['status' => 'success', 'message' => 'Data berhasil diimport'] 
            : ['status' => 'error', 'message' => 'Gagal import data'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function get_chart_data() {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $id_pos = $this->input->get('id_pos');
    $date = $this->input->get('date') ?? date('Y-m-d');
    
    if (empty($id_pos)) {
        echo json_encode(['status' => 'error', 'message' => 'ID Pos tidak ditemukan']);
        return;
    }
    
    $pos = $this->db->where('id_pos', $id_pos)->get('master_pos')->row();
    if (!$pos) {
        echo json_encode(['status' => 'error', 'message' => 'Pos tidak ditemukan']);
        return;
    }
    
    $value_field = ($pos->tipe_pos == 'PCH') ? 'rain' : 'wlevel';
    $unit = ($pos->tipe_pos == 'PCH') ? 'mm' : 'cm';
    $label = ($pos->tipe_pos == 'PCH') ? 'Curah Hujan' : 'Tinggi Muka Air';
    
    // ==========================================
    // BUAT SEMUA WAKTU 00:00 - 23:55 (interval 5 menit)
    // ==========================================
    $allTimes = [];
    for ($i = 0; $i < 24; $i++) {
        for ($j = 0; $j < 60; $j += 5) {
            $allTimes[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':' . str_pad($j, 2, '0', STR_PAD_LEFT);
        }
    }
    
    // Inisialisasi array dengan nilai 0 untuk semua waktu
    $manual_data_by_time = array_fill_keys($allTimes, 0);
    $telemetri_data_by_time = array_fill_keys($allTimes, 0);
    $manual_count_by_time = array_fill_keys($allTimes, 0);
    $telemetri_count_by_time = array_fill_keys($allTimes, 0);
    
    // ==========================================
    // AMBIL DATA MANUAL
    // ==========================================
    try {
        $this->db->select("
            DATE_FORMAT(created_at, '%H:%i') as waktu,
            " . $value_field . " as value
        ");
        $this->db->from('data_manual');
        $this->db->where('id_pos', $id_pos);
        $this->db->where('DATE(tanggal_input)', $date);
        $this->db->where($value_field . ' IS NOT NULL');
        $manual_raw = $this->db->get()->result();
        
        foreach ($manual_raw as $m) {
            $waktu = $this->_round_to_5_minutes($m->waktu);
            if (isset($manual_data_by_time[$waktu])) {
                $val = floatval($m->value);
                if ($pos->tipe_pos == 'PDA') {
                    $val = $val * 100;
                }
                $manual_data_by_time[$waktu] = $val; // Langsung assign, bukan +=
                $manual_count_by_time[$waktu] = 1;
            }
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error manual: ' . $e->getMessage()]);
        return;
    }
    
    // ==========================================
    // AMBIL DATA TELEMETRI
    // ==========================================
    try {
        $this->db->select("
            DATE_FORMAT(received_at, '%H:%i') as waktu,
            wlevel as value
        ");
        $this->db->from('data_telemetri');
        $this->db->where('id_pos', $id_pos);
        $this->db->where('DATE(received_at)', $date);
        $this->db->where('wlevel IS NOT NULL');
        $this->db->order_by('received_at', 'ASC');
        $telemetri_raw = $this->db->get()->result();
        
        foreach ($telemetri_raw as $t) {
            $waktu = $this->_round_to_5_minutes($t->waktu);
            if (isset($telemetri_data_by_time[$waktu])) {
                $val = floatval($t->value);
                if ($pos->tipe_pos == 'PDA') {
                    $val = $val * 100;
                }
                $telemetri_data_by_time[$waktu] = $val;
                $telemetri_count_by_time[$waktu] = 1;
            }
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error telemetri: ' . $e->getMessage()]);
        return;
    }
    
    // ==========================================
    // BUAT LABEL DAN VALUES - SEMUA WAKTU MUNCUL
    // ==========================================
    $labels = [];
    $manual_values = [];
    $telemetri_values = [];
    $colors = [];
    
    $all_valid = [];
    foreach ($allTimes as $waktu) {
        $labels[] = $waktu;
        
        $manual_val = $manual_data_by_time[$waktu] ?? 0;
        $telemetri_val = $telemetri_data_by_time[$waktu] ?? 0;
        
        $manual_values[] = round($manual_val, 2);
        $telemetri_values[] = round($telemetri_val, 2);
        
        if ($manual_val > 0) $all_valid[] = $manual_val;
        if ($telemetri_val > 0) $all_valid[] = $telemetri_val;
    }
    
    // ==========================================
    // GENERATE WARNA
    // ==========================================
    if (empty($all_valid)) {
        foreach ($manual_values as $val) {
            $colors[] = 'rgba(203, 213, 225, 0.5)';
        }
    } else {
        $max_val = max($all_valid);
        $min_val = min($all_valid);
        $range = $max_val - $min_val;
        if ($range == 0) $range = 1;
        
        foreach ($manual_values as $val) {
            if ($val == 0) {
                $colors[] = 'rgba(203, 213, 225, 0.5)';
            } else {
                $ratio = ($val - $min_val) / $range;
                $r = 254 - round($ratio * 200);
                $g = 183 + round($ratio * 50);
                $b = 0 + round($ratio * 150);
                $colors[] = "rgba($r, $g, $b, 0.8)";
            }
        }
    }
    
    $has_manual = count(array_filter($manual_values, function($v) { return $v > 0; })) > 0;
    $has_telemetri = count(array_filter($telemetri_values, function($v) { return $v > 0; })) > 0;
    
    echo json_encode([
        'status' => 'success',
        'labels' => $labels,
        'manual_values' => $manual_values,
        'telemetri_values' => $telemetri_values,
        'colors' => $colors,
        'unit' => $unit,
        'label' => $label,
        'pos_name' => $pos->nama_pos,
        'date' => $date,
        'has_manual' => $has_manual,
        'has_telemetri' => $has_telemetri,
        'total_manual' => count(array_filter($manual_values, function($v) { return $v > 0; })),
        'total_telemetri' => count(array_filter($telemetri_values, function($v) { return $v > 0; })),
        'no_data' => false
    ]);
}

/**
 * Bulatkan waktu ke 5 menit terdekat
 */
private function _round_to_5_minutes($time) {
    $parts = explode(':', $time);
    $hour = intval($parts[0]);
    $minute = intval($parts[1]);
    $minute = round($minute / 5) * 5;
    if ($minute == 60) {
        $minute = 0;
        $hour++;
        if ($hour == 24) $hour = 0;
    }
    return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT);
}

// ==========================================
// KELOLA MATA AIR
// ==========================================

/**
 * Halaman Kelola Mata Air
 */
public function kelola_mata_air() {
    $data = $this->M_superadmin->get_mata_air_data();
    $data['admin_name'] = $this->session->userdata('nama_lengkap');
    $this->_render('superadmin/v_kelola_mata_air', $data);
}

/**
 * Tambah Mata Air
 */
public function tambah_mata_air() {
    $result = $this->M_superadmin->insert_mata_air($this->input->post());
    $this->session->set_flashdata($result['status'], $result['message']);
    redirect('superadmin/kelola_mata_air');
}

/**
 * Edit Mata Air
 */
public function edit_mata_air() {
    $result = $this->M_superadmin->update_mata_air($this->input->post());
    $this->session->set_flashdata($result['status'], $result['message']);
    redirect('superadmin/kelola_mata_air');
}

/**
 * Hapus Mata Air
 */
public function hapus_mata_air($id) {
    $result = $this->M_superadmin->delete_mata_air($id);
    $this->session->set_flashdata($result['status'], $result['message']);
    redirect('superadmin/kelola_mata_air');
}

/**
 * Get Mata Air by ID (AJAX)
 */
public function get_mata_air_json($id_mata_air) {
    $data = $this->M_superadmin->get_mata_air_by_id($id_mata_air);
    if ($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Data tidak ditemukan']);
    }
}
}