<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->model('M_admin');
        $this->load->helper(['url', 'form']);
        $this->load->database();
        date_default_timezone_set('Asia/Jakarta');
        
        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        // Cek role
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Akses Ditolak. Anda bukan Admin Wilayah.', 403);
        }
    }

    // ==========================================
    // HELPER: Ambil ID pos yang diizinkan
    // ==========================================
    private function _get_allowed_pos_ids() {
        $id_user = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $user = $this->db->select('id_pos')->where('id_user', $id_user)->get('users')->row();
        
        if (!empty($user->id_pos)) {
            return array_map('trim', explode(',', $user->id_pos));
        }
        return [0];
    }

    // ==========================================
    // HELPER: Render view dengan layout
    // ==========================================
    private function _render($view, $data) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/v_admin_layout', $data);
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function index() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        $data = $this->M_admin->get_dashboard_data($allowed_pos);
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('admin/v_dashboard', $data);
    }

    // ==========================================
    // KELOLA PETUGAS
    // ==========================================
    public function kelola_petugas() {
        $data = $this->M_admin->get_petugas_data($this->_get_allowed_pos_ids());
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('admin/v_kelola_petugas', $data);
    }

    public function tambah_petugas() {
        $result = $this->M_admin->insert_petugas(
            $this->input->post(), 
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_petugas');
    }

    public function edit_petugas() {
        $result = $this->M_admin->update_petugas(
            $this->input->post(), 
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_petugas');
    }

    public function hapus_petugas($id) {
        $result = $this->M_admin->delete_petugas($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_petugas');
    }

    public function nonaktifkan_petugas($id) {
        $this->M_admin->set_status($id, 'nonaktif');
        $this->session->set_flashdata('success', 'Petugas dinonaktifkan.');
        redirect('admin/kelola_petugas');
    }

    public function aktifkan_petugas($id) {
        $this->M_admin->set_status($id, 'aktif');
        $this->session->set_flashdata('success', 'Petugas diaktifkan.');
        redirect('admin/kelola_petugas');
    }

    // ==========================================
    // KELOLA MANUAL (DENGAN DUKUNGAN BENDUNG)
    // ==========================================
    public function kelola_manual() {
        $allowed_pos = $this->_get_allowed_pos_ids();
        
        $data = $this->M_admin->get_manual_data(
            $this->input->get('pos'), 
            $this->input->get('bulan') ?: date('Y-m'),
            $allowed_pos
        );
        
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        
        // Siapkan data untuk JavaScript
        $data['pos_data_js'] = [];
        $data['bendungan_data_js'] = [];
        $data['bendung_data_js'] = [];
        
        if (!empty($data['pos']) && !empty($data['data_list'])) {
            if ($data['pos']->is_bendung == 1) {
                // Data bendung (SESUAI STRUKTUR TERBARU)
                foreach ($data['data_list'] as $d) {
                    $data['bendung_data_js'][$d->id_bendung] = [
                        'tanggal'        => $d->tanggal_input,
                        'rain'           => $d->rain,
                        'elevasi_mercu'  => $d->elevasi_mercu,
                        'q_total'        => $d->q_total,
                        'q_fc1'          => $d->q_fc1,
                        'q_fc2'          => $d->q_fc2,
                        'q_sal_induk'    => $d->q_sal_induk,
                        'q_limpas'       => $d->q_limpas,
                        'q_sungai'       => $d->q_sungai,
                        'q_spam_kpbu'    => $d->q_spam_kpbu,
                        'sluice_gate'    => $d->sluice_gate,
                        'bukaan_pintu'   => $d->bukaan_pintu,
                        'keterangan'     => $d->keterangan ?? '',
                    ];
                }
            } elseif ($data['pos']->is_bendungan == 1) {
                // Data bendungan (DENGAN KOLOM BARU)
                foreach ($data['data_list'] as $d) {
                    $data['bendungan_data_js'][$d->id_bendungan] = [
                        'tanggal'                  => $d->tanggal_input,
                        'nwl'                      => $d->nwl,
                        'nwl_volume'               => $d->nwl_volume,
                        'nwl_luas'                 => $d->nwl_luas,
                        'rain'                     => $d->rain,
                        'elevasi'                  => $d->elevasi,
                        'volume'                   => $d->volume,
                        'luas'                     => $d->luas,
                        'inflow'                   => $d->inflow,
                        'pltm'                     => $d->pltm,
                        'spillway'                 => $d->spillway,
                        'total_outflow'            => $d->total_outflow,
                        'plta_status'              => $d->plta_status ?? '',
                        'irigasi_status'           => $d->irigasi_status ?? '',
                        'tail_water'               => $d->tail_water ?? '',
                        'rvh'                      => $d->rembesan_vnotch_h,
                        'rvq'                      => $d->rembesan_vnotch_q,
                        'rplh'                     => $d->rembesan_pump_pit_l_h,
                        'rplq'                     => $d->rembesan_pump_pit_l_q,
                        'rprh'                     => $d->rembesan_pump_pit_r_h,
                        'rprq'                     => $d->rembesan_pump_pit_r_q,
                        'keterangan'               => $d->keterangan ?? '',
                        // KOLOM BARU
                        'tahun_mulai_pembangunan'  => $d->tahun_mulai_pembangunan ?? '',
                        'tipe_bendungan'           => $d->tipe_bendungan ?? '',
                        'elevasi_mercu'            => $d->elevasi_mercu,
                        'luas_das'                 => $d->luas_das,
                    ];
                }
            } else {
                // Data pos biasa (PCH/PDA)
                foreach ($data['data_list'] as $d) {
                    $data['pos_data_js'][$d->id_manual] = [
                        'tanggal'    => $d->tanggal_input,
                        'rain'       => $d->rain,
                        'wlevel'     => $d->wlevel,
                        'keterangan' => $d->keterangan ?? '',
                    ];
                }
            }
        }
        
        $this->_render('admin/v_kelola_manual', $data);
    }

    // ==========================================
    // SIMPAN DATA
    // ==========================================
    public function simpan_data_pos() {
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $result = $this->M_admin->insert_manual_pos(
            $this->input->post(),
            $user_id,
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal_input')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    public function simpan_bendungan() {
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        
        // Ambil data POST
        $post = $this->input->post();
        
        // Tambahkan kolom baru ke data
        $post['tahun_mulai_pembangunan'] = $this->input->post('tahun_mulai_pembangunan');
        $post['tipe_bendungan'] = $this->input->post('tipe_bendungan');
        $post['elevasi_mercu'] = $this->input->post('elevasi_mercu');
        $post['luas_das'] = $this->input->post('luas_das');
        
        $result = $this->M_admin->insert_manual_bendungan(
            $post,
            $user_id,
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal_input')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    /**
     * Simpan data bendung (SESUAI STRUKTUR TERBARU)
     */
    public function simpan_bendung() {
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
        $result = $this->M_admin->insert_manual_bendung(
            $this->input->post(),
            $user_id,
            $this->_get_allowed_pos_ids()
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal_input')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    // ==========================================
    // UPDATE DATA
    // ==========================================
    public function update_manual() {
        $result = $this->M_admin->update_manual_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    public function update_bendungan() {
        // Ambil data POST
        $post = $this->input->post();
        
        // Tambahkan kolom baru ke data
        $post['tahun_mulai_pembangunan'] = $this->input->post('tahun_mulai_pembangunan');
        $post['tipe_bendungan'] = $this->input->post('tipe_bendungan');
        $post['elevasi_mercu'] = $this->input->post('elevasi_mercu');
        $post['luas_das'] = $this->input->post('luas_das');
        
        $result = $this->M_admin->update_manual_bendungan($post);
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    /**
     * Update data bendung (SESUAI STRUKTUR TERBARU)
     */
    public function update_bendung() {
        $result = $this->M_admin->update_manual_bendung($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        
        $pos_id = $this->input->post('id_pos');
        $bulan = date('Y-m', strtotime($this->input->post('tanggal')));
        redirect('admin/kelola_manual?pos=' . $pos_id . '&bulan=' . $bulan);
    }

    // ==========================================
    // HAPUS DATA
    // ==========================================
    public function hapus_manual($id) {
        $result = $this->M_admin->delete_manual_pos($id, $this->_get_allowed_pos_ids());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    public function hapus_bendungan($id) {
        $result = $this->M_admin->delete_manual_bendungan($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    /**
     * Hapus data bendung (SESUAI STRUKTUR TERBARU)
     */
    public function hapus_bendung($id) {
        $result = $this->M_admin->delete_manual_bendung($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('admin/kelola_manual?pos=' . $this->input->get('pos'));
    }

    // ==========================================
    // AJAX: GET DATA BY ID (UNTUK EDIT MODAL)
    // ==========================================
    
    /**
     * Get data bendung by ID (AJAX) - SESUAI STRUKTUR TERBARU
     */
    public function get_bendung_json($id_bendung) {
        $data = $this->M_admin->get_bendung_by_id($id_bendung);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    /**
     * Get data bendungan by ID (AJAX) - DENGAN KOLOM BARU
     */
    public function get_bendungan_json($id_bendungan) {
        $data = $this->M_admin->get_bendungan_by_id($id_bendungan);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    /**
     * Get data pos manual by ID (AJAX)
     */
    public function get_manual_json($id_manual) {
        $data = $this->M_admin->get_manual_by_id($id_manual);
        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    }

    // ==========================================
// EXPORT & IMPORT DATA
// ==========================================

public function export_import() {
    $allowed_pos = $this->_get_allowed_pos_ids();
    
    // Data pos yang diizinkan
    $this->db->select('id_pos, nama_pos, tipe_pos');
    $this->db->where_in('id_pos', $allowed_pos);
    $pos_list = $this->db->order_by('nama_pos', 'ASC')->get('master_pos')->result();
    
    // Modules untuk export/import
    $modules = [
        'telemetri'   => 'Data Telemetri',
        'manual'      => 'Data Manual Pos',
        'bendung'     => 'Data Bendung',
        'bendungan'   => 'Data Bendungan',
        'all'         => 'Semua Data'
    ];
    
    $periods = [
        'all'   => 'Semua',
        'daily' => 'Harian',
        'month' => 'Bulanan'
    ];
    
    // Nama wilayah admin
    $user = $this->db->select('nama_lengkap')->where('id_user', $this->session->userdata('user_id'))->get('users')->row();
    
    $data = [
        'app_name'       => 'HydroSmart',
        'title'          => 'Export & Import Data',
        'admin_name'     => $this->session->userdata('nama_lengkap'),
        'wilayah_name'   => $user->nama_lengkap ?? 'Admin Wilayah',
        'pos_list'       => $pos_list,
        'modules'        => $modules,
        'periods'        => $periods
    ];
    
    $this->_render('admin/v_export_import', $data);
}

public function export_csv() {
    $allowed_pos = $this->_get_allowed_pos_ids();
    $module = $this->input->get('module') ?: 'all';
    $id_pos = $this->input->get('id_pos');
    $period = $this->input->get('period') ?: 'all';
    $date = $this->input->get('date') ?: date('Y-m-d');
    
    // Validasi akses pos
    if (!empty($id_pos) && !in_array($id_pos, $allowed_pos)) {
        show_error('Akses Ditolak! Anda tidak memiliki akses ke pos ini.', 403);
    }
    
    if ($module == 'all') {
        $this->_export_all_csv($allowed_pos, $id_pos, $period, $date);
    } else {
        $this->_export_module_csv($module, $allowed_pos, $id_pos, $period, $date);
    }
}

private function _export_all_csv($allowed_pos, $id_pos, $period, $date) {
    $data = [];
    
    $tel = $this->_get_export_data('telemetri', $allowed_pos, $id_pos, $period, $date);
    if (!empty($tel)) $data['telemetri'] = $tel;
    
    $man = $this->_get_export_data('manual', $allowed_pos, $id_pos, $period, $date);
    if (!empty($man)) $data['manual'] = $man;
    
    $bdg = $this->_get_export_data('bendung', $allowed_pos, $id_pos, $period, $date);
    if (!empty($bdg)) $data['bendung'] = $bdg;
    
    $bendungan = $this->_get_export_data('bendungan', $allowed_pos, $id_pos, $period, $date);
    if (!empty($bendungan)) $data['bendungan'] = $bendungan;
    
    if (empty($data)) {
        $this->session->set_flashdata('warning', 'Tidak ada data untuk diexport.');
        redirect('admin/export_import');
        return;
    }
    
    $pos_name = $id_pos ? $this->_get_pos_name($id_pos) : 'all_pos';
    $filename = 'export_all_' . $pos_name . '_' . date('Y-m-d_H-i') . '.csv';
    
    $this->load->helper('download');
    force_download($filename, $this->_array_to_csv_multitable($data));
}

private function _export_module_csv($module, $allowed_pos, $id_pos, $period, $date) {
    $data = $this->_get_export_data($module, $allowed_pos, $id_pos, $period, $date);
    
    if (empty($data)) {
        $this->session->set_flashdata('warning', 'Tidak ada data untuk diexport.');
        redirect('admin/export_import');
        return;
    }
    
    $pos_name = $id_pos ? $this->_get_pos_name($id_pos) : 'all_pos';
    $filename = 'export_' . $module . '_' . $pos_name . '_' . date('Y-m-d_H-i') . '.csv';
    
    $this->load->helper('download');
    force_download($filename, $this->_array_to_csv($data));
}

private function _get_export_data($module, $allowed_pos, $id_pos, $period, $date) {
    if (!empty($id_pos)) {
        $this->db->where('id_pos', $id_pos);
    } else if (!empty($allowed_pos)) {
        $this->db->where_in('id_pos', $allowed_pos);
    }
    
    if ($period == 'daily') {
        $this->db->where('DATE(tanggal_input)', $date);
    } else if ($period == 'month') {
        $this->db->where("DATE_FORMAT(tanggal_input, '%Y-%m')", substr($date, 0, 7));
    }
    
    switch ($module) {
        case 'telemetri':
            return $this->db->get('data_telemetri')->result_array();
        case 'manual':
            return $this->db->get('data_manual')->result_array();
        case 'bendung':
            return $this->db->get('data_bendung')->result_array();
        case 'bendungan':
            return $this->db->get('data_bendungan')->result_array();
        default:
            return [];
    }
}

private function _array_to_csv($data) {
    if (empty($data)) return '';
    
    $output = '';
    $headers = array_keys((array)$data[0]);
    $output .= implode(';', $headers) . "\n";
    
    foreach ($data as $row) {
        $row = (array)$row;
        foreach ($row as &$val) {
            $val = str_replace(';', ',', $val);
            $val = str_replace("\n", ' ', $val);
            $val = str_replace("\r", ' ', $val);
        }
        $output .= implode(';', $row) . "\n";
    }
    
    return $output;
}

private function _array_to_csv_multitable($data) {
    if (empty($data)) return '';
    
    $output = '';
    foreach ($data as $table => $rows) {
        $output .= "--- TABLE: " . strtoupper($table) . " ---\n";
        $output .= $this->_array_to_csv($rows);
        $output .= "\n";
    }
    return $output;
}

private function _get_pos_name($id_pos) {
    $row = $this->db->select('nama_pos')->where('id_pos', $id_pos)->get('master_pos')->row();
    return $row ? str_replace(' ', '_', $row->nama_pos) : 'unknown';
}

// ==========================================
// EXPORT PDF - LANGSUNG LOAD DOMPDF DARI THIRD_PARTY
// ==========================================
public function export_pdf() {
    $allowed_pos = $this->_get_allowed_pos_ids();
    $module = $this->input->get('module') ?: 'all';
    $id_pos = $this->input->get('id_pos');
    $period = $this->input->get('period') ?: 'all';
    $date = $this->input->get('date') ?: date('Y-m-d');
    
    // Validasi akses pos
    if (!empty($id_pos) && !in_array($id_pos, $allowed_pos)) {
        show_error('Akses Ditolak! Anda tidak memiliki akses ke pos ini.', 403);
    }
    
    // Ambil data
    if ($module == 'all') {
        $data = [];
        $modules_data = ['telemetri', 'manual', 'bendung', 'bendungan'];
        foreach ($modules_data as $m) {
            $d = $this->_get_export_data($m, $allowed_pos, $id_pos, $period, $date);
            if (!empty($d)) $data[$m] = $d;
        }
    } else {
        $data = $this->_get_export_data($module, $allowed_pos, $id_pos, $period, $date);
    }
    
    if (empty($data)) {
        $this->session->set_flashdata('warning', 'Tidak ada data untuk diexport.');
        redirect('admin/export_import');
        return;
    }
    
    // ============================================================
    // LOAD DOMPDF LANGSUNG DARI THIRD_PARTY (TANPA LIBRARY WRAPPER)
    // ============================================================
    require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
    
    $dompdf = new Dompdf\Dompdf();
    $dompdf->set_option('defaultFont', 'Arial');
    $dompdf->set_option('isRemoteEnabled', true);
    $dompdf->set_option('isHtml5ParserEnabled', true);
    $dompdf->set_option('enable_remote', true);
    
    $pos_name = $id_pos ? $this->_get_pos_name($id_pos) : 'Semua_Pos';
    $filename = 'export_' . $module . '_' . $pos_name . '_' . date('Y-m-d') . '.pdf';
    
    // Generate HTML content
    $html = $this->_generate_pdf_html($data, $module, $pos_name, $period, $date);
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream($filename, ['Attachment' => 1]);
}

private function _generate_pdf_html($data, $module, $pos_name, $period, $date) {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Export Data</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            h1 { font-size: 16px; color: #1a2a4a; border-bottom: 2px solid #f5c518; padding-bottom: 5px; }
            h2 { font-size: 13px; color: #1a2a4a; margin-top: 15px; margin-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
            th { background-color: #1a2a4a; color: white; padding: 4px 6px; text-align: left; font-size: 9px; }
            td { border: 1px solid #ddd; padding: 3px 6px; font-size: 9px; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .info { font-size: 9px; color: #666; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <h1>📊 Export Data - ' . ucfirst($module) . '</h1>
        <div class="info">
            <strong>Pos:</strong> ' . $pos_name . ' | 
            <strong>Periode:</strong> ' . ucfirst($period) . ' | 
            <strong>Tanggal:</strong> ' . date('d-m-Y', strtotime($date)) . ' | 
            <strong>Diexport:</strong> ' . date('d-m-Y H:i') . '
        </div>';
    
    if ($module == 'all') {
        foreach ($data as $table => $rows) {
            $html .= '<h2>📋 ' . strtoupper($table) . ' (' . count($rows) . ' data)</h2>';
            $html .= $this->_array_to_html_table($rows);
        }
    } else {
        $html .= $this->_array_to_html_table($data);
    }
    
    $html .= '</body></html>';
    return $html;
}

private function _array_to_html_table($data) {
    if (empty($data)) return '<p><em>Tidak ada data</em></p>';
    
    $html = '<table>';
    $headers = array_keys((array)$data[0]);
    $html .= '<thead><tr>';
    foreach ($headers as $h) {
        $html .= '<th>' . htmlspecialchars($h) . '</th>';
    }
    $html .= '</tr></thead><tbody>';
    
    foreach ($data as $row) {
        $row = (array)$row;
        $html .= '<tr>';
        foreach ($headers as $h) {
            $val = isset($row[$h]) ? $row[$h] : '';
            if (is_null($val)) $val = '';
            $html .= '<td>' . htmlspecialchars($val) . '</td>';
        }
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

public function download_template_csv() {
    $module = $this->input->get('module');
    
    $filename = 'template_' . $module . '.csv';
    $headers = $this->_get_template_headers($module);
    
    $this->load->helper('download');
    force_download($filename, implode(';', $headers) . "\n");
}

private function _get_template_headers($module) {
    switch ($module) {
        case 'telemetri':
            return ['id_pos', 'received_at', 'bat_lvl', 'bat_volt', 'rain', 'wlevel', 'created_at'];
        case 'manual':
            return ['id_pos', 'id_user', 'tanggal_input', 'rain', 'wlevel', 'keterangan'];
        case 'bendung':
            return ['id_pos', 'id_user', 'tanggal_input', 'rain', 'elevasi_mercu', 'q_total', 'q_fc1', 'q_fc2', 'q_sal_induk', 'q_limpas', 'q_sungai', 'q_spam_kpbu', 'sluice_gate', 'bukaan_pintu', 'keterangan'];
        case 'bendungan':
            return ['id_pos', 'id_user', 'tanggal_input', 'nwl', 'nwl_volume', 'nwl_luas', 'rain', 'elevasi', 'volume', 'luas', 'inflow', 'pltm', 'spillway', 'total_outflow', 'plta_status', 'irigasi_status', 'tail_water', 'rembesan_vnotch_h', 'rembesan_vnotch_q', 'rembesan_pump_pit_l_h', 'rembesan_pump_pit_l_q', 'rembesan_pump_pit_r_h', 'rembesan_pump_pit_r_q', 'keterangan', 'tahun_mulai_pembangunan', 'tipe_bendungan', 'elevasi_mercu', 'luas_das'];
        default:
            return ['id_pos', 'tanggal_input', 'data1', 'data2', 'keterangan'];
    }
}

public function import_csv() {
    $allowed_pos = $this->_get_allowed_pos_ids();
    $module = $this->input->post('module');
    $id_pos = $this->input->post('id_pos');
    
    if (!in_array($id_pos, $allowed_pos)) {
        $this->session->set_flashdata('error', 'Akses Ditolak! Anda tidak memiliki akses ke pos ini.');
        redirect('admin/export_import');
        return;
    }
    
    if (!isset($_FILES['file_csv']) || $_FILES['file_csv']['error'] != UPLOAD_ERR_OK) {
        $this->session->set_flashdata('error', 'Gagal upload file.');
        redirect('admin/export_import');
        return;
    }
    
    $file = $_FILES['file_csv'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ['csv', 'txt'])) {
        $this->session->set_flashdata('error', 'Format file harus CSV atau TXT.');
        redirect('admin/export_import');
        return;
    }
    
    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        $this->session->set_flashdata('error', 'Gagal membaca file.');
        redirect('admin/export_import');
        return;
    }
    
    $headers = fgetcsv($handle, 0, ';');
    if ($headers === false) {
        fclose($handle);
        $this->session->set_flashdata('error', 'File CSV kosong atau format tidak valid.');
        redirect('admin/export_import');
        return;
    }
    
    $imported = 0;
    $failed = 0;
    $errors = [];
    
    $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
    
    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        if (count($row) != count($headers)) {
            $failed++;
            continue;
        }
        
        $data = array_combine($headers, $row);
        $data['id_pos'] = $id_pos;
        $data['id_user'] = $user_id;
        
        $result = $this->_import_row($module, $data);
        if ($result) {
            $imported++;
        } else {
            $failed++;
            $errors[] = implode(', ', array_slice($data, 0, 3));
        }
    }
    
    fclose($handle);
    
    $message = "Import selesai! Berhasil: $imported data, Gagal: $failed data.";
    if (!empty($errors)) {
        $message .= " Data gagal: " . implode('; ', array_slice($errors, 0, 5));
        if (count($errors) > 5) $message .= ' ...';
    }
    
    $this->session->set_flashdata('success', $message);
    redirect('admin/export_import?pos=' . $id_pos);
}

private function _import_row($module, $data) {
    foreach ($data as &$val) {
        if ($val === '') $val = null;
    }
    
    $data['created_at'] = date('Y-m-d H:i:s');
    
    switch ($module) {
        case 'telemetri':
            return $this->db->insert('data_telemetri', $data);
        case 'manual':
            return $this->db->insert('data_manual', $data);
        case 'bendung':
            return $this->db->insert('data_bendung', $data);
        case 'bendungan':
            return $this->db->insert('data_bendungan', $data);
        default:
            return false;
    }
}
}