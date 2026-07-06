<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-darkblue">Export & Import Data</h1>
            <p class="text-sm text-slate-500 mt-1">Export atau import data infrastruktur dalam format CSV atau PDF</p>
        </div>
    </div>

    <!-- Flash Message -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('warning')): ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <?= $this->session->flashdata('warning') ?>
        </div>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- EXPORT SECTION -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-darkblue">Export Data</h3>
                    <p class="text-sm text-slate-500">Export data ke CSV atau PDF</p>
                </div>
            </div>
            
            <form action="" method="GET" id="formExport" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Pilih Modul</label>
                    <select name="module" id="export_module" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="">Pilih Modul</option>
                        <?php foreach ($modules as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Periode Data</label>
                    <div class="grid grid-cols-3 gap-2">
                        <?php foreach ($periods as $key => $label): ?>
                            <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-all">
                                <input type="radio" name="period" value="<?= $key ?>" class="w-4 h-4 accent-brandyellow" <?= $key == 'all' ? 'checked' : '' ?>>
                                <span class="text-sm text-slate-600"><?= $label ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div id="export_date_container">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="exportData('csv')" class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/><path d="M8 12l3 3-3 3m3-3H5m6 0h3"/></svg>
                        CSV / Excel
                    </button>
                    <button type="button" onclick="exportData('pdf')" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/><path d="M9 9h6v2H9zm0 4h6v2H9zm0 4h4v2H9z"/></svg>
                        PDF
                    </button>
                </div>
            </form>
            
            <div class="mt-4 bg-blue-50 rounded-lg p-3 border border-blue-200">
                <p class="text-xs text-blue-600">💡 <strong>CSV</strong> dapat dibuka dengan Microsoft Excel atau Google Sheets. <strong>PDF</strong> untuk cetak dokumen.</p>
            </div>
        </div>

        <!-- IMPORT SECTION -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-darkblue">Import Data</h3>
                    <p class="text-sm text-slate-500">Import data dari file CSV</p>
                </div>
            </div>
            
            <form action="<?= base_url('superadmin/import_csv') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Pilih Modul</label>
                    <select name="module" id="import_module" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="">Pilih Modul</option>
                        <?php foreach ($modules as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Pilih File CSV</label>
                    <div class="relative">
                        <input type="file" name="file_csv" id="file_csv" accept=".csv,.txt" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brandyellow file:text-darkblue hover:file:bg-yellow-400">
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Format: .csv atau .txt, maksimal 5MB</p>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                    <p class="text-sm text-yellow-700 font-medium mb-2">📌 Cara Import:</p>
                    <ul class="text-xs text-yellow-600 space-y-1 list-disc list-inside">
                        <li>Export data terlebih dahulu untuk mendapatkan template</li>
                        <li>Baris pertama adalah header (nama kolom)</li>
                        <li>Pastikan format data sesuai dengan kolom di database</li>
                        <li>Data dengan format salah akan dilewati</li>
                    </ul>
                </div>
                
                <button type="submit" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Data
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Export Cards -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($modules as $key => $label): ?>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-darkblue text-sm"><?= $label ?></h4>
                    <span class="text-xs text-slate-400">Quick Export</span>
                </div>
                <div class="flex gap-2">
                    <a href="<?= base_url('superadmin/export_csv?module=' . $key . '&period=all') ?>" class="flex-1 px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition-all text-center flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/></svg>
                        CSV
                    </a>
                    <a href="<?= base_url('superadmin/export_pdf?module=' . $key . '&period=all') ?>" class="flex-1 px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition-all text-center flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/></svg>
                        PDF
                    </a>
                </div>
                <p class="text-[10px] text-slate-400 mt-2 text-center">Semua data <?= strtolower($label) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// ==========================================
// EXPORT FUNCTION
// ==========================================
function exportData(format) {
    var form = document.getElementById('formExport');
    var module = document.getElementById('export_module').value;
    var period = document.querySelector('input[name="period"]:checked');
    var date = document.querySelector('input[name="date"]').value;
    
    if (!module) {
        alert('Silakan pilih modul terlebih dahulu!');
        return;
    }
    
    var url = '<?= base_url('superadmin/export_') ?>' + format;
    url += '?module=' + encodeURIComponent(module);
    url += '&period=' + encodeURIComponent(period ? period.value : 'all');
    url += '&date=' + encodeURIComponent(date);
    
    window.open(url, '_blank');
}

// ==========================================
// PERIODE TOGGLE
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    var periodRadios = document.querySelectorAll('input[name="period"]');
    var dateContainer = document.getElementById('export_date_container');
    var dateInput = document.querySelector('input[name="date"]');
    
    periodRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'all') {
                dateContainer.style.opacity = '0.5';
                dateInput.disabled = true;
            } else {
                dateContainer.style.opacity = '1';
                dateInput.disabled = false;
            }
        });
    });
    
    // Trigger initial state
    var checked = document.querySelector('input[name="period"]:checked');
    if (checked && checked.value === 'all') {
        dateContainer.style.opacity = '0.5';
        dateInput.disabled = true;
    }
});
</script>