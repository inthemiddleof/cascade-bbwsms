<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-darkblue">Export Data Telemetri</h1>
            <p class="text-sm text-slate-500 mt-1">Export data telemetri PCH / PDA dengan filter waktu spesifik</p>
        </div>
    </div>

    <!-- Flash Message -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="" method="GET" id="formExportTelemetri" class="space-y-5">
            <!-- Pilih Pos -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Pilih Pos Telemetri <span class="text-red-500">*</span></label>
                <select name="id_pos" id="pos_telemetri" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    <option value="">-- Pilih Pos --</option>
                    <?php foreach ($pos_list as $p): ?>
                        <option value="<?= $p->id_pos ?>" <?= ($this->input->get('id_pos') == $p->id_pos) ? 'selected' : '' ?>>
                            [<?= $p->tipe_pos ?>] <?= $p->nama_pos ?> (<?= $p->device_id_telemetry ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Periode -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Periode Data</label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                    <?php foreach ($periods as $key => $label): ?>
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-all <?= ($this->input->get('period') == $key || ($key == 'daily' && !$this->input->get('period'))) ? 'bg-slate-50 border-brandyellow' : '' ?>">
                            <input type="radio" name="period" value="<?= $key ?>" class="w-4 h-4 accent-brandyellow" <?= ($this->input->get('period') == $key || ($key == 'daily' && !$this->input->get('period'))) ? 'checked' : '' ?> onchange="toggleTimeFilter(this.value)">
                            <span class="text-sm text-slate-600"><?= $label ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal</label>
                <input type="date" name="date" value="<?= $this->input->get('date') ?? date('Y-m-d') ?>" class="w-full max-w-xs px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
            </div>

            <!-- Filter Jam & Menit (Kustom) -->
            <div id="time_filter_container" class="grid grid-cols-1 md:grid-cols-2 gap-4 <?= ($this->input->get('period') == 'hourly' || $this->input->get('period') == 'custom') ? '' : 'hidden' ?>">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jam Mulai</label>
                    <input type="time" name="start_time" id="start_time" value="<?= $this->input->get('start_time') ?? '00:00' ?>" step="60" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jam Selesai</label>
                    <input type="time" name="end_time" id="end_time" value="<?= $this->input->get('end_time') ?? '23:59' ?>" step="60" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>

            <!-- Info -->
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-sm text-blue-700 font-medium mb-1">📌 Keterangan Filter:</p>
                <ul class="text-xs text-blue-600 space-y-0.5 list-disc list-inside">
                    <li><strong>Per Jam:</strong> Data pada tanggal tertentu dengan rentang jam yang dipilih</li>
                    <li><strong>Harian:</strong> Semua data pada tanggal tertentu</li>
                    <li><strong>Mingguan:</strong> Data dalam satu minggu (Senin-Minggu)</li>
                    <li><strong>Bulanan:</strong> Data dalam satu bulan penuh</li>
                    <li><strong>Kustom:</strong> Data pada tanggal dengan rentang jam & menit spesifik</li>
                </ul>
            </div>

            <!-- Tombol Export -->
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="exportTelemetri('csv')" class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/><path d="M8 12l3 3-3 3m3-3H5m6 0h3"/></svg>
                    Export CSV
                </button>
                <button type="button" onclick="exportTelemetri('pdf')" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/><path d="M9 9h6v2H9zm0 4h6v2H9zm0 4h4v2H9z"/></svg>
                    Export PDF
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Export Cards -->
    <div class="mt-6">
        <h3 class="text-sm font-bold text-slate-600 mb-3">Quick Export Hari Ini</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <?php foreach ($pos_list as $p): ?>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-3 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-slate-600 truncate"><?= htmlspecialchars($p->nama_pos) ?></span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded <?= $p->tipe_pos == 'PCH' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' ?>"><?= $p->tipe_pos ?></span>
                    </div>
                    <div class="flex gap-1.5">
                        <a href="<?= base_url('superadmin/export_telemetri_csv?id_pos=' . $p->id_pos . '&period=daily&date=' . date('Y-m-d')) ?>" class="flex-1 px-2 py-1.5 bg-green-500 hover:bg-green-600 text-white text-[10px] font-bold rounded-lg text-center">CSV</a>
                        <a href="<?= base_url('superadmin/export_telemetri_pdf?id_pos=' . $p->id_pos . '&period=daily&date=' . date('Y-m-d')) ?>" class="flex-1 px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[10px] font-bold rounded-lg text-center">PDF</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// ==========================================
// TOGGLE TIME FILTER
// ==========================================
function toggleTimeFilter(period) {
    var container = document.getElementById('time_filter_container');
    if (period === 'hourly' || period === 'custom') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

// ==========================================
// EXPORT TELEMETRI
// ==========================================
function exportTelemetri(format) {
    var form = document.getElementById('formExportTelemetri');
    var id_pos = document.getElementById('pos_telemetri').value;
    var period = document.querySelector('input[name="period"]:checked');
    var date = document.querySelector('input[name="date"]').value;
    var start_time = document.getElementById('start_time').value;
    var end_time = document.getElementById('end_time').value;
    
    if (!id_pos) {
        alert('Silakan pilih pos telemetri terlebih dahulu!');
        return;
    }
    
    var url = '<?= base_url('superadmin/export_telemetri_') ?>' + format;
    url += '?id_pos=' + encodeURIComponent(id_pos);
    url += '&period=' + encodeURIComponent(period ? period.value : 'daily');
    url += '&date=' + encodeURIComponent(date);
    url += '&start_time=' + encodeURIComponent(start_time);
    url += '&end_time=' + encodeURIComponent(end_time);
    
    window.open(url, '_blank');
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    var checked = document.querySelector('input[name="period"]:checked');
    if (checked) {
        toggleTimeFilter(checked.value);
    }
});
</script>