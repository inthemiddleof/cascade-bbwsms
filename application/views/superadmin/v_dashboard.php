<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-slate-500 text-sm mt-1">Selamat datang, <span class="font-semibold text-darkblue"><?= htmlspecialchars($admin_name) ?></span>. Berikut ringkasan sistem monitoring hari ini.</p>
    </div>
    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-red-50 text-red-600 border border-red-200 uppercase tracking-wider">
        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
        Super Admin
    </span>
</div>

<!-- Statistik Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Global</span>
        </div>
        <p class="text-2xl font-black text-darkblue mb-1"><?= $total_pos ?></p>
        <p class="text-xs text-slate-500">Total Pos Monitoring</p>
        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-slate-100">
            <span class="text-[10px] text-slate-500"><?= $total_pch ?> PCH</span>
            <span class="text-[10px] text-slate-500"><?= $total_pda ?> PDA</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full"><?= $petugas_aktif ?> Aktif</span>
        </div>
        <p class="text-2xl font-black text-darkblue mb-1"><?= $total_petugas ?></p>
        <p class="text-xs text-slate-500">Total Petugas Terdaftar</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full"><?= $total_pos > 0 ? round(($pos_online/$total_pos)*100) : 0 ?>%</span>
        </div>
        <p class="text-2xl font-black text-darkblue mb-1"><?= $pos_online ?> <span class="text-base text-slate-300">/ <?= $total_pos ?></span></p>
        <p class="text-xs text-slate-500">Pos Online (1 Jam Terakhir)</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Hari Ini</span>
        </div>
        <p class="text-2xl font-black text-darkblue mb-1"><?= number_format($total_data_hari_ini, 0, ',', '.') ?></p>
        <p class="text-xs text-slate-500">Record Data Hari Ini</p>
        <?php if($last_sync): ?>
        <p class="text-[10px] text-slate-400 mt-2">Sync: <?= date('H:i', strtotime($last_sync)) ?> WIB</p>
        <?php endif; ?>
    </div>
</div>

<!-- Toggle View: Table / Chart -->
<div class="flex items-center justify-between mb-4">
    <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Data Pos Monitoring</h3>
    <div class="flex gap-2 bg-slate-100 p-1 rounded-lg">
        <button onclick="setView('table')" id="btnTableView" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all bg-darkblue text-white">
            📋 Tabel
        </button>
        <button onclick="setView('chart')" id="btnChartView" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-500 hover:text-darkblue">
            📊 Grafik
        </button>
    </div>
</div>

<!-- Container Tabel -->
<div id="tableView" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Semua Pos Monitoring</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($pos_list) ?> Pos</span>
    </div>
    <div class="overflow-auto max-h-[400px]">
        <table class="w-full text-sm min-w-[400px]">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-5 py-3 text-left font-bold">Nama Pos</th>
                    <th class="px-5 py-3 text-center font-bold w-20">Tipe</th>
                    <th class="px-5 py-3 text-center font-bold w-20">Data</th>
                    <th class="px-5 py-3 text-center font-bold w-24">Terakhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($pos_list)): foreach($pos_list as $ps): ?>
                <tr class="hover:bg-slate-50 transition-colors cursor-pointer" onclick="selectPosAndViewChart(<?= $ps->id_pos ?>)">
                    <td class="px-5 py-3"><p class="font-semibold text-darkblue text-xs"><?= htmlspecialchars($ps->nama_pos) ?></p></td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold <?= ($ps->tipe_pos == 'PCH') ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' ?>"><?= $ps->tipe_pos ?></span>
                    </td>
                    <td class="px-5 py-3 text-center"><span class="font-bold text-darkblue text-xs"><?= isset($ps->total_data) ? number_format($ps->total_data, 0, ',', '.') : '0' ?></span></td>
                    <td class="px-5 py-3 text-center whitespace-nowrap">
                        <?php $last = $ps->last_data ?? null; ?>
                        <?php if(!empty($last) && $last != '0000-00-00 00:00:00'): ?>
                            <p class="text-xs"><?= date('d/m', strtotime($last)) ?></p>
                            <p class="text-[10px] text-slate-400"><?= date('H:i', strtotime($last)) ?></p>
                        <?php else: ?>
                            <span class="text-xs text-slate-300">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="px-5 py-12 text-center text-slate-400">Belum ada pos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Container Grafik per Pos -->
<div id="chartView" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5" style="display: none;">
    <!-- Filter -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-2">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Pos:</label>
            <select id="selectPos" onchange="loadChartData()" class="px-3 py-2 text-xs border-2 border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-brandyellow min-w-[200px] font-medium">
                <option value="">-- Pilih Pos --</option>
                <?php foreach($pos_list as $ps): ?>
                <option value="<?= $ps->id_pos ?>" data-nama="<?= htmlspecialchars($ps->nama_pos) ?>" data-tipe="<?= $ps->tipe_pos ?>" data-total="<?= $ps->total_data ?? 0 ?>" data-last="<?= $ps->last_data ?? '' ?>">
                    <?= htmlspecialchars($ps->nama_pos) ?> (<?= $ps->tipe_pos ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal:</label>
            <input type="date" id="chartDate" value="<?= date('Y-m-d') ?>" onchange="loadChartData()" class="px-3 py-2 text-xs border-2 border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-brandyellow font-medium">
            <button onclick="loadChartData()" class="px-4 py-2 text-xs font-bold bg-brandyellow hover:bg-yellow-400 text-darkblue rounded-lg transition-all">
                🔄 Tampilkan
            </button>
        </div>
    </div>

    <!-- Info Pos Terpilih -->
    <div id="posInfo" class="bg-gradient-to-r from-slate-50 to-white rounded-xl p-4 border border-slate-200 mb-4 hidden">
        <div class="flex flex-wrap items-center gap-6 text-xs">
            <div>
                <span class="text-slate-400">Nama Pos</span>
                <p class="font-bold text-darkblue text-sm" id="posName">-</p>
            </div>
            <div>
                <span class="text-slate-400">Tipe</span>
                <p class="font-bold text-darkblue text-sm" id="posType">-</p>
            </div>
            <div>
                <span class="text-slate-400">Total Data</span>
                <p class="font-bold text-darkblue text-sm" id="posTotalData">0</p>
            </div>
            <div>
                <span class="text-slate-400">Tanggal</span>
                <p class="font-bold text-darkblue text-sm" id="posDate">-</p>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div id="chartLoading" class="text-center py-10 hidden">
        <div class="inline-block w-8 h-8 border-4 border-brandyellow border-t-transparent rounded-full animate-spin"></div>
        <p class="text-sm text-slate-500 mt-2">Memuat data...</p>
    </div>

    <!-- Grafik -->
    <div id="chartContainer">
        <div class="relative" style="height: 400px;">
            <canvas id="chartPos"></canvas>
        </div>
    </div>

    <!-- Statistik Tambahan -->
    <div id="chartStats" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5 hidden">
        <div class="bg-slate-50 rounded-lg p-3 text-center border border-slate-200">
            <p class="text-[10px] text-slate-400 uppercase tracking-wider">Total Data</p>
            <p class="text-lg font-bold text-darkblue" id="statTotal">0</p>
        </div>
        <div class="bg-slate-50 rounded-lg p-3 text-center border border-slate-200">
            <p class="text-[10px] text-slate-400 uppercase tracking-wider">Rata-rata</p>
            <p class="text-lg font-bold text-darkblue" id="statAvg">0.0</p>
        </div>
        <div class="bg-slate-50 rounded-lg p-3 text-center border border-slate-200">
            <p class="text-[10px] text-slate-400 uppercase tracking-wider">Tertinggi</p>
            <p class="text-lg font-bold text-green-600" id="statMax">0</p>
        </div>
        <div class="bg-slate-50 rounded-lg p-3 text-center border border-slate-200">
            <p class="text-[10px] text-slate-400 uppercase tracking-wider">Terendah</p>
            <p class="text-lg font-bold text-red-500" id="statMin">0</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ==========================================
// DATA DARI PHP
// ==========================================
var posList = <?= json_encode($pos_list) ?>;
var totalPCH = <?= $total_pch ?>;
var totalPDA = <?= $total_pda ?>;
var totalPos = <?= $total_pos ?>;
var posOnline = <?= $pos_online ?>;
var totalPetugas = <?= $total_petugas ?>;
var totalDataHariIni = <?= $total_data_hari_ini ?>;

// ==========================================
// CHART INSTANCE
// ==========================================
var chartPos = null;

// ==========================================
// TOGGLE VIEW
// ==========================================
function setView(view) {
    var tableView = document.getElementById('tableView');
    var chartView = document.getElementById('chartView');
    var btnTable = document.getElementById('btnTableView');
    var btnChart = document.getElementById('btnChartView');
    
    if (!tableView || !chartView) {
        console.warn('Elemen tabel atau chart tidak ditemukan di halaman');
        return;
    }
    
    if (view === 'table') {
        tableView.style.display = 'block';
        chartView.style.display = 'none';
        if (btnTable) btnTable.className = 'px-4 py-1.5 text-xs font-bold rounded-lg transition-all bg-darkblue text-white';
        if (btnChart) btnChart.className = 'px-4 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-500 hover:text-darkblue';
    } else {
        tableView.style.display = 'none';
        chartView.style.display = 'block';
        if (btnChart) btnChart.className = 'px-4 py-1.5 text-xs font-bold rounded-lg transition-all bg-darkblue text-white';
        if (btnTable) btnTable.className = 'px-4 py-1.5 text-xs font-bold rounded-lg transition-all text-slate-500 hover:text-darkblue';
        var select = document.getElementById('selectPos');
        if (select && select.value) {
            loadChartData();
        }
    }
}

// ==========================================
// SELECT POS FROM TABLE
// ==========================================
function selectPosAndViewChart(idPos) {
    var select = document.getElementById('selectPos');
    if (select) {
        select.value = idPos;
        setView('chart');
        loadChartData();
    }
}

// ==========================================
// LOAD CHART DATA
// ==========================================
function loadChartData() {
    var select = document.getElementById('selectPos');
    var dateInput = document.getElementById('chartDate');
    
    if (!select) {
        console.warn('Elemen selectPos tidak ditemukan');
        return;
    }
    
    var idPos = select.value;
    var date = dateInput ? dateInput.value : '<?= date('Y-m-d') ?>';
    
    if (!idPos) {
        var posInfo = document.getElementById('posInfo');
        var chartStats = document.getElementById('chartStats');
        if (posInfo) posInfo.classList.add('hidden');
        if (chartStats) chartStats.classList.add('hidden');
        if (chartPos) {
            chartPos.destroy();
            chartPos = null;
        }
        return;
    }
    
    // Update info pos
    var selectedOption = select.options[select.selectedIndex];
    var namaPos = selectedOption ? selectedOption.getAttribute('data-nama') || '-' : '-';
    var tipePos = selectedOption ? selectedOption.getAttribute('data-tipe') || '-' : '-';
    var totalData = selectedOption ? selectedOption.getAttribute('data-total') || '0' : '0';
    
    var posName = document.getElementById('posName');
    var posType = document.getElementById('posType');
    var posTotalData = document.getElementById('posTotalData');
    var posDate = document.getElementById('posDate');
    var posInfo = document.getElementById('posInfo');
    
    if (posName) posName.textContent = namaPos;
    if (posType) posType.textContent = tipePos;
    if (posTotalData) posTotalData.textContent = totalData;
    if (posDate) posDate.textContent = date ? formatDateIndonesia(date) : '-';
    if (posInfo) posInfo.classList.remove('hidden');
    
    // Tampilkan loading
    var chartLoading = document.getElementById('chartLoading');
    var chartContainer = document.getElementById('chartContainer');
    var chartStats = document.getElementById('chartStats');
    
    if (chartLoading) chartLoading.classList.remove('hidden');
    if (chartContainer) chartContainer.classList.add('hidden');
    if (chartStats) chartStats.classList.add('hidden');
    
    // Panggil API
    var url = '<?= base_url('superadmin/get_chart_data') ?>';
    url += '?id_pos=' + encodeURIComponent(idPos);
    url += '&date=' + encodeURIComponent(date);
    
    console.log('Fetching URL:', url); // Debug
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status); // Debug
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data); // Debug
            
            if (chartLoading) chartLoading.classList.add('hidden');
            if (chartContainer) chartContainer.classList.remove('hidden');
            
            if (data.status === 'success') {
                renderChart(data);
                updateStats(data);
                
                // Tampilkan info jumlah data
                var legendUnit = document.getElementById('legendUnit');
                if (legendUnit) {
                    var info = data.unit || '';
                    if (data.has_manual) info += ' | Manual: ' + data.total_manual + ' jam';
                    if (data.has_telemetri) info += ' | Telemetri: ' + data.total_telemetri + ' jam';
                    legendUnit.textContent = info;
                }
            } else {
                alert('Error: ' + (data.message || 'Gagal memuat data'));
            }
        })
        .catch(error => {
            if (chartLoading) chartLoading.classList.add('hidden');
            if (chartContainer) chartContainer.classList.remove('hidden');
            console.error('Fetch Error:', error);
            alert('Terjadi kesalahan saat memuat data: ' + error.message);
        });
}
// ==========================================
// RENDER CHART - SEMUA 288 DATA POINT (5 MENIT)
// ==========================================
function renderChart(data) {
    var canvas = document.getElementById('chartPos');
    if (!canvas) {
        console.warn('Canvas chartPos tidak ditemukan');
        return;
    }
    
    var ctx = canvas.getContext('2d');
    var labels = data.labels || [];
    var manualValues = data.manual_values || [];
    var telemetriValues = data.telemetri_values || [];
    var colors = data.colors || [];
    var unit = data.unit || '';
    var label = data.label || 'Nilai';
    
    if (data.no_data) {
        var noDataMsg = document.getElementById('noDataMessage');
        var chartContainer = document.getElementById('chartContainer');
        var chartStats = document.getElementById('chartStats');
        if (noDataMsg) noDataMsg.classList.remove('hidden');
        if (chartContainer) chartContainer.classList.add('hidden');
        if (chartStats) chartStats.classList.add('hidden');
        return;
    }
    
    var noDataMsg = document.getElementById('noDataMessage');
    if (noDataMsg) noDataMsg.classList.add('hidden');
    
    if (chartPos) {
        chartPos.destroy();
        chartPos = null;
    }
    
    // Siapkan datasets
    var datasets = [];
    
    // 1. Dataset Manual (Bar Chart)
    var manualColors = colors.length > 0 ? colors : manualValues.map(function() {
        return 'rgba(254, 183, 0, 0.8)';
    });
    
    datasets.push({
        label: 'Manual',
        data: manualValues,
        type: 'bar',
        backgroundColor: manualColors,
        borderColor: manualColors.map(function(c) { return c.replace('0.8', '1'); }),
        borderWidth: 1,
        borderRadius: 2,
        barPercentage: 0.8,
        order: 2
    });
    
    // 2. Dataset Telemetri (Line Chart)
    datasets.push({
        label: 'Telemetri',
        data: telemetriValues,
        type: 'line',
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        borderWidth: 2,
        pointBackgroundColor: telemetriValues.map(function(v) {
            return v > 0 ? '#3b82f6' : 'rgba(59, 130, 246, 0.3)';
        }),
        pointBorderColor: '#fff',
        pointBorderWidth: 1,
        pointRadius: telemetriValues.map(function(v) {
            return v > 0 ? 3 : 1;
        }),
        pointHoverRadius: 6,
        fill: true,
        tension: 0.3,
        order: 1,
        spanGaps: false
    });
    
    chartPos = new Chart(ctx, {
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { size: 11, weight: 'bold' },
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255,255,255,0.95)',
                    titleColor: '#0a2a4a',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || '';
                            var value = context.raw;
                            if (value === null || value === undefined) return label + ': -';
                            return label + ': ' + value + ' ' + unit;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        font: { size: 10 },
                        callback: function(value) {
                            return value + ' ' + unit;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { size: 7 },
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 24, // Tampilkan 24 label (setiap jam)
                        callback: function(value, index, ticks) {
                            // Tampilkan label hanya setiap jam (00:00, 01:00, dst)
                            if (index % 12 === 0) {
                                return value;
                            }
                            return '';
                        }
                    }
                }
            }
        }
    });
}

// ==========================================
// UPDATE STATISTICS
// ==========================================
function updateStats(data) {
    var manualValues = data.manual_values || [];
    var telemetriValues = data.telemetri_values || [];
    var unit = data.unit || '';
    var selectedDate = data.selected_date || data.date || '';
    
    var posDate = document.getElementById('posDate');
    if (posDate && selectedDate) {
        posDate.textContent = formatDateIndonesia(selectedDate);
    }
    
    // Hanya ambil nilai > 0 untuk statistik (data yang benar-benar ada)
    var allValues = [];
    manualValues.forEach(function(v) { 
        if (v !== null && v !== undefined && v > 0) allValues.push(v); 
    });
    telemetriValues.forEach(function(v) { 
        if (v !== null && v !== undefined && v > 0) allValues.push(v); 
    });
    
    var statTotal = document.getElementById('statTotal');
    var statAvg = document.getElementById('statAvg');
    var statMax = document.getElementById('statMax');
    var statMin = document.getElementById('statMin');
    var chartStats = document.getElementById('chartStats');
    
    if (allValues.length === 0) {
        if (statTotal) statTotal.textContent = '0 ' + unit;
        if (statAvg) statAvg.textContent = '0.0 ' + unit;
        if (statMax) statMax.textContent = '0 ' + unit;
        if (statMin) statMin.textContent = '0 ' + unit;
        if (chartStats) chartStats.classList.remove('hidden');
        return;
    }
    
    var total = allValues.reduce(function(sum, v) { return sum + v; }, 0);
    var avg = (total / allValues.length);
    var max = Math.max.apply(null, allValues);
    var min = Math.min.apply(null, allValues);
    
    if (statTotal) statTotal.textContent = total.toFixed(1) + ' ' + unit;
    if (statAvg) statAvg.textContent = avg.toFixed(2) + ' ' + unit;
    if (statMax) statMax.textContent = max.toFixed(2) + ' ' + unit;
    if (statMin) statMin.textContent = min.toFixed(2) + ' ' + unit;
    if (chartStats) chartStats.classList.remove('hidden');
}

// ==========================================
// FORMAT DATE
// ==========================================
function formatDateIndonesia(dateStr) {
    if (!dateStr) return '-';
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    var d = new Date(dateStr + 'T00:00:00');
    return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
}

// ==========================================
// AUTO INIT
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    var tableView = document.getElementById('tableView');
    var chartView = document.getElementById('chartView');
    
    if (tableView && chartView) {
        setView('table');
    } else {
        console.warn('Elemen tabel atau chart tidak ditemukan di halaman');
        if (tableView) {
            tableView.style.display = 'block';
        }
    }
});
</script>