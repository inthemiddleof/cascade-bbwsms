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

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function index() {
        $data = $this->M_superadmin->get_dashboard_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_dashboard', $data);
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
        $this->load->model('M_admin');
        
        $data = $this->M_admin->get_manual_data(
            $this->input->get('pos'), 
            $this->input->get('bulan') ?: date('Y-m'),
            null // Superadmin bisa akses semua pos
        );
        
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        
        // Siapkan data untuk JavaScript (fix bug edit)
        $data['pos_data_js'] = [];
        $data['bendungan_data_js'] = [];
        $data['bendung_data_js'] = [];
        
        if (!empty($data['pos']) && !empty($data['data_list'])) {
            if ($data['pos']->is_bendung == 1) {
                // Data bendung
                foreach ($data['data_list'] as $d) {
                    $data['bendung_data_js'][$d->id_bendung] = [
                        'tanggal'      => $d->tanggal_input,
                        'rain'         => $d->rain,
                        'elevasi_mercu'=> $d->elevasi_mercu,
                        'q_total'      => $d->q_total,
                        'q_fc1'        => $d->q_fc1,
                        'q_fc2'        => $d->q_fc2,
                        'q_limpas'     => $d->q_limpas,
                        'q_spam_kpbu'  => $d->q_spam_kpbu,
                        'sluice_gate'  => $d->sluice_gate,
                        'keterangan'   => $d->keterangan ?? '',
                    ];
                }
            } elseif ($data['pos']->is_bendungan == 1) {
                // Data bendungan
                foreach ($data['data_list'] as $d) {
                    $data['bendungan_data_js'][$d->id_bendungan] = [
                        'tanggal'        => $d->tanggal_input,
                        'nwl'            => $d->nwl,
                        'nwl_volume'     => $d->nwl_volume,
                        'nwl_luas'       => $d->nwl_luas,
                        'rain'           => $d->rain,
                        'elevasi'        => $d->elevasi,
                        'volume'         => $d->volume,
                        'luas'           => $d->luas,
                        'inflow'         => $d->inflow,
                        'pltm'           => $d->pltm,
                        'spillway'       => $d->spillway,
                        'total_outflow'  => $d->total_outflow,
                        'plta_status'    => $d->plta_status ?? '',
                        'irigasi_status' => $d->irigasi_status ?? '',
                        'tail_water'     => $d->tail_water ?? '',
                        'rvh'            => $d->rembesan_vnotch_h,
                        'rvq'            => $d->rembesan_vnotch_q,
                        'rplh'           => $d->rembesan_pump_pit_l_h,
                        'rplq'           => $d->rembesan_pump_pit_l_q,
                        'rprh'           => $d->rembesan_pump_pit_r_h,
                        'rprq'           => $d->rembesan_pump_pit_r_q,
                        'keterangan'     => $d->keterangan ?? '',
                    ];
                }
            } else {
                // Data pos biasa
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
        
        $this->_render('superadmin/v_kelola_manual', $data);
    }

    // ==========================================
    // SIMPAN DATA
    // ==========================================
    public function simpan_data_pos() {
        $this->load->model('M_admin');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
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
        $result = $this->M_admin->insert_manual_bendungan(
            $this->input->post(),
            $user_id,
            null
        );
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal_input'))));
    }

    // ==========================================
    // SIMPAN BENDUNG (BARU)
    // ==========================================
    public function simpan_bendung() {
        $this->load->model('M_admin');
        $user_id = $this->session->userdata('user_id') ?: $this->session->userdata('id_user');
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
        $result = $this->M_admin->update_manual_pos($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal'))));
    }

    public function update_bendungan() {
        $this->load->model('M_admin');
        $result = $this->M_admin->update_manual_bendungan($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_manual?pos=' . $this->input->post('id_pos') . '&bulan=' . date('Y-m', strtotime($this->input->post('tanggal'))));
    }

    // ==========================================
    // UPDATE BENDUNG (BARU)
    // ==========================================
    public function update_bendung() {
        $this->load->model('M_admin');
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

    // ==========================================
    // HAPUS BENDUNG (BARU)
    // ==========================================
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
    // EXPORT & IMPORT DATA (OPSI 3 - SEDERHANA)
    // ==========================================

    /**
     * Halaman Export/Import Data
     */
    public function export_import() {
        $data = [
            'app_name'   => 'HydroSmart',
            'title'      => 'Export & Import Data',
            'admin_name' => $this->session->userdata('nama_lengkap'),
            'modules'    => [
                'embung' => 'Kelola Embung',
                'pengaman_pantai' => 'Pengaman Pantai',
                'pengendali_sedimen' => 'Pengendali Sedimen',
                'irigasi' => 'Daerah Irigasi'
            ],
            'periods' => [
                'all' => 'Semua Data',
                'daily' => 'Harian',
                'weekly' => 'Mingguan',
                'monthly' => 'Bulanan',
                'yearly' => 'Tahunan'
            ]
        ];
        $this->_render('superadmin/v_export_import', $data);
    }

    /**
 * Export Data ke CSV (Format Excel Friendly)
 */
public function export_csv() {
    $this->load->helper('download');
    
    $module = $this->input->get('module');
    $period = $this->input->get('period') ?? 'all';
    $date = $this->input->get('date') ?? date('Y-m-d');
    
    $data = $this->_get_export_data($module, $period, $date);
    
    if (empty($data)) {
        $this->session->set_flashdata('error', 'Tidak ada data untuk diexport.');
        redirect('superadmin/export_import');
    }
    
    $headers = array_keys((array)$data[0]);
    $module_label = str_replace('_', ' ', ucwords($module));
    $filename = str_replace('_', '-', $module) . '_' . date('Y-m-d_H-i') . '.csv';
    
    // Header untuk download CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // BOM untuk UTF-8
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // Gunakan delimiter titik koma (;) untuk Excel Indonesia
    $delimiter = ';';
    
    // Header info
    fputcsv($output, ['=== ' . strtoupper($module_label) . ' ==='], $delimiter);
    fputcsv($output, ['Periode', $this->_get_period_label($period, $date)], $delimiter);
    fputcsv($output, ['Total Data', count($data) . ' record'], $delimiter);
    fputcsv($output, ['Dicetak', date('d-m-Y H:i:s')], $delimiter);
    fputcsv($output, [], $delimiter);
    
    // Header kolom
    $header_labels = array_map(function($h) {
        return str_replace('_', ' ', ucwords($h));
    }, $headers);
    fputcsv($output, $header_labels, $delimiter);
    
    // Tulis data
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

    /**
     * Export Data ke PDF (menggunakan Dompdf)
     */
    public function export_pdf() {
        $module = $this->input->get('module');
        $period = $this->input->get('period') ?? 'all';
        $date = $this->input->get('date') ?? date('Y-m-d');
        
        $data = $this->_get_export_data($module, $period, $date);
        
        if (empty($data)) {
            $this->session->set_flashdata('error', 'Tidak ada data untuk diexport.');
            redirect('superadmin/export_import');
        }
        
        // Load Dompdf
        $this->_load_dompdf();
        
        $dompdf = new Dompdf\Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('defaultFont', 'sans-serif');
        
        $module_label = str_replace('_', ' ', ucwords($module));
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

    /**
 * Import Data dari CSV (Support Delimiter ; dan ,)
 */
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
    
    // Baca file CSV
    $file = fopen($_FILES['file_csv']['tmp_name'], 'r');
    
    // Baca baris pertama untuk deteksi delimiter
    $first_line = fgets($file);
    rewind($file);
    
    // Deteksi delimiter: cari koma atau titik koma
    $delimiter = ',';
    if (strpos($first_line, ';') !== false) {
        $delimiter = ';';
    } elseif (strpos($first_line, "\t") !== false) {
        $delimiter = "\t";
    }
    
    // Ambil header
    $headers = fgetcsv($file, 0, $delimiter);
    if ($headers === FALSE) {
        fclose($file);
        $this->session->set_flashdata('error', 'File CSV tidak valid atau kosong.');
        redirect('superadmin/export_import');
    }
    
    $headers = array_map('trim', $headers);
    
    // Cari baris data dimulai (skip baris info jika ada)
    $data_start_row = 0;
    $temp_headers = [];
    foreach ($headers as $h) {
        if (strpos($h, '===') !== false || strpos($h, 'Nama') !== false || strpos($h, 'Periode') !== false) {
            $data_start_row++;
            // Baca header berikutnya
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
    
    // Mapping header ke field database
    $field_map = $this->_get_field_mapping($module);
    
    $success = 0;
    $failed = 0;
    $errors = [];
    $row_index = $data_start_row + 1;
    
    while (($row = fgetcsv($file, 0, $delimiter)) !== FALSE) {
        $row_index++;
        
        // Skip baris kosong
        if (empty(array_filter($row))) continue;
        
        // Pastikan jumlah kolom sesuai
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
                // Konversi format angka (Indonesia ke float)
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

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Load Dompdf Library
     */
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
        
        // Jika tidak ada, tampilkan error
        show_error('Library Dompdf tidak ditemukan. Silakan download Dompdf dan letakkan di application/third_party/dompdf/');
    }

    /**
     * Generate PDF HTML
     */
    private function _generate_pdf_html($module_label, $period_label, $headers, $data) {
        // Batasi jumlah kolom untuk PDF agar tidak overflow
        $max_cols = 12;
        if (count($headers) > $max_cols) {
            $headers = array_slice($headers, 0, $max_cols);
            // Potong data juga
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
            // Singkat label jika terlalu panjang
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
                // Format untuk kolom kondisi/status
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
            
            // Batasi 500 baris untuk PDF (agar tidak overload)
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

    /**
     * Ambil data untuk export
     */
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
        } else {
            return [];
        }
        
        // Filter periode
        if ($period != 'all') {
            $field_date = 'created_at';
            $this->_apply_period_filter($period, $date, $field_date);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Apply filter periode
     */
    private function _apply_period_filter($period, $date, $field) {
        $date_obj = new DateTime($date);
        
        switch ($period) {
            case 'daily':
                $this->db->where('DATE(' . $field . ')', $date);
                break;
            case 'weekly':
                $start = clone $date_obj;
                $start->modify('monday this week');
                $end = clone $start;
                $end->modify('sunday this week');
                $this->db->where($field . ' >=', $start->format('Y-m-d 00:00:00'));
                $this->db->where($field . ' <=', $end->format('Y-m-d 23:59:59'));
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

    /**
     * Get period label
     */
    private function _get_period_label($period, $date) {
        $date_obj = new DateTime($date);
        switch ($period) {
            case 'all': return 'Semua Data';
            case 'daily': return 'Harian - ' . $date_obj->format('d-m-Y');
            case 'weekly': 
                $start = clone $date_obj;
                $start->modify('monday this week');
                $end = clone $start;
                $end->modify('sunday this week');
                return 'Mingguan - ' . $start->format('d-m-Y') . ' s/d ' . $end->format('d-m-Y');
            case 'monthly': return 'Bulanan - ' . $date_obj->format('F Y');
            case 'yearly': return 'Tahunan - ' . $date_obj->format('Y');
            default: return $date;
        }
    }

    /**
     * Get column letter untuk Excel
     */
    private function _get_column_letter($index) {
        $letters = range('A', 'Z');
        if ($index <= 26) return $letters[$index - 1];
        return 'A' . $letters[$index - 27];
    }

    /**
     * Get field mapping untuk import
     */
    private function _get_field_mapping($module) {
        $maps = [
            'embung' => [
                'Nomor Pos' => 'nomor_pos',
                'Nama Pos' => 'nama_pos',
                'Sungai' => 'sungai',
                'Wilayah Sungai' => 'wilayah_sungai',
                'Latitude' => 'lat',
                'Longitude' => 'lng',
                'NWL' => 'nwl',
                'Volume NWL' => 'nwl_volume',
                'Luas NWL' => 'nwl_luas',
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
                'Panjang' => 'panjang',
                'Elevasi Puncak' => 'elevasi_puncak',
                'Lebar Puncak' => 'lebar_puncak',
                'Kondisi' => 'kondisi_bangunan',
                'Status Operasi' => 'status_operasi',
                'Tahun' => 'tahun_dibangun',
                'Kab/Kota' => 'kabupaten_kota',
                'Kecamatan' => 'kecamatan',
                'Kelurahan' => 'kelurahan',
                'Manfaat' => 'manfaat',
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
                'Daya Tampung' => 'daya_tampung',
                'Panjang' => 'panjang',
                'Lebar' => 'lebar',
                'Tinggi' => 'tinggi',
                'Kondisi' => 'kondisi',
                'Status Operasi' => 'status_operasi',
                'Tahun' => 'tahun_dibangun',
                'Kab/Kota' => 'kabupaten_kota',
                'Kecamatan' => 'kecamatan',
                'Kelurahan' => 'kelurahan',
                'Jenis Material' => 'jenis_material',
            ],
            'irigasi' => [
                'Kode Integrasi' => 'kode_integrasi',
                'Nama DI' => 'nama_aset',
                'Jenis DI' => 'jenis_daerah_irigasi',
                'Wilayah Sungai' => 'wilayah_sungai',
                'DAS' => 'daerah_aliran_sungai',
                'Kab/Kota' => 'kabupaten_kota',
                'Kecamatan' => 'kecamatan',
                'Kelurahan' => 'kelurahan',
                'Latitude' => 'latitude',
                'Longitude' => 'longitude',
                'Luas Permen' => 'luas_permen',
                'Luas Baku' => 'luas_baku',
                'Luas Potensial' => 'luas_potensial',
                'Luas Fungsional' => 'luas_fungsional',
                'Sumber Air' => 'sumber_air',
                'Jenis Bangunan Utama' => 'jenis_bangunan_utama',
                'Tahun' => 'tahun_pembangunan',
                'Status Pemeliharaan' => 'status_pemeliharaan',
                'Di OP Kan Oleh' => 'di_op_kan_oleh',
            ],
        ];
        return $maps[$module] ?? [];
    }

    /**
     * Import data ke database
     */
    private function _import_data($module, $data) {
        try {
            // Bersihkan data
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

    // ==========================================
    // KELOLA TELEMETRI
    // ==========================================
    public function kelola_telemetri() {
        $data = $this->M_superadmin->get_telemetri_data();
        $data['admin_name'] = $this->session->userdata('nama_lengkap');
        $this->_render('superadmin/v_kelola_telemetri', $data);
    }

    public function edit_telemetri() {
        $result = $this->M_superadmin->update_telemetri($this->input->post());
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_telemetri');
    }

    public function hapus_telemetri($id) {
        $result = $this->M_superadmin->delete_telemetri($id);
        $this->session->set_flashdata($result['status'], $result['message']);
        redirect('superadmin/kelola_telemetri');
    }

    // ==========================================
    // EXPORT TELEMETRI KHUSUS
    // ==========================================

    /**
     * Halaman Export Telemetri
     */
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

    /**
 * Export Data Telemetri ke CSV (Format Excel Friendly)
 */
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
    
    // Header untuk download CSV - dengan charset UTF-8 BOM
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // BOM untuk UTF-8 (biar Excel baca dengan benar)
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // Gunakan delimiter titik koma (;) agar Excel Indonesia bisa membaca dengan benar
    $delimiter = ';';
    
    // Header info
    fputcsv($output, ['=== DATA TELEMETRI ==='], $delimiter);
    fputcsv($output, ['Nama Pos', $pos->nama_pos], $delimiter);
    fputcsv($output, ['Tipe Pos', $pos->tipe_pos], $delimiter);
    fputcsv($output, ['Device ID', $pos->device_id_telemetry], $delimiter);
    fputcsv($output, ['Periode', $this->_get_period_label_telemetri($period, $date, $start_time, $end_time)], $delimiter);
    fputcsv($output, ['Total Data', count($data) . ' record'], $delimiter);
    fputcsv($output, ['Dicetak', date('d-m-Y H:i:s')], $delimiter);
    fputcsv($output, [], $delimiter);
    
    // Header kolom
    $headers = ['No', 'Tanggal', 'Jam', 'Baterai (V)', 'Curah Hujan (mm)', 'TMA (m)', 'Status'];
    fputcsv($output, $headers, $delimiter);
    
    // Data
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

    /**
     * Export Data Telemetri ke PDF
     */
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
    // PRIVATE METHODS UNTUK TELEMETRI
    // ==========================================

    /**
     * Get data telemetri untuk export
     */
    private function _get_telemetri_export_data($id_pos, $period, $date, $start_time, $end_time) {
        $this->db->select('received_at, batt, rain, wlevel, status');
        $this->db->from('data_telemetri');
        $this->db->where('id_pos', $id_pos);
        
        // Filter periode
        $date_obj = new DateTime($date);
        
        switch ($period) {
            case 'hourly':
                // Data per jam pada tanggal tertentu
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
                // Kustom: jam dan menit
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

    /**
     * Get period label untuk telemetri
     */
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

    /**
     * Generate PDF HTML untuk telemetri
     */
    private function _generate_telemetri_pdf_html($pos, $period_label, $data) {
        $total = count($data);
        $tipe_icon = ($pos->tipe_pos == 'PCH') ? '🌧️' : '📊';
        
        // Hitung statistik
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
            
            // Batasi 500 baris
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
    $headers = array_keys($field_map);
    
    $filename = 'template_' . $module . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo "\xEF\xBB\xBF";
    $output = fopen('php://output', 'w');
    $delimiter = ';';
    
    // Header info
    fputcsv($output, ['=== TEMPLATE IMPORT ' . strtoupper(str_replace('_', ' ', $module)) . ' ==='], $delimiter);
    fputcsv($output, ['Kolom yang wajib diisi: ' . implode(', ', $headers)], $delimiter);
    fputcsv($output, ['Format angka: gunakan titik (.) untuk desimal'], $delimiter);
    fputcsv($output, ['Tanggal: gunakan format YYYY-MM-DD'], $delimiter);
    fputcsv($output, [], $delimiter);
    
    // Header kolom
    fputcsv($output, $headers, $delimiter);
    
    // Contoh data (1 baris)
    $example = [];
    foreach ($headers as $h) {
        $example[] = 'Contoh_' . str_replace(' ', '_', $h);
    }
    fputcsv($output, $example, $delimiter);
    
    fclose($output);
    exit;
}
}