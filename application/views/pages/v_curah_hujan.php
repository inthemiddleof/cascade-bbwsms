<?php
if (!function_exists('getBgIntensity')) {
    function getBgIntensity($val) {
        if ($val === null || !is_numeric($val) || $val <= 0) {
            return 'bg-slate-100 text-slate-400';
        }
        if ($val > 0 && $val <= 20) return 'bg-emerald-500 text-white';
        if ($val > 20 && $val <= 50) return 'bg-yellow-400 text-slate-800';
        if ($val > 50 && $val <= 100) return 'bg-orange-500 text-white';
        if ($val > 100 && $val <= 150) return 'bg-red-500 text-white';
        if ($val > 150) return 'bg-purple-600 text-white';
        return 'bg-slate-100 text-slate-400';
    }
}

if (!function_exists('safeNumber')) {
    function safeNumber($val, $decimals = 1) {
        if ($val === null || $val === '') return '-';
        return number_format((float)$val, $decimals, ',', '.');
    }
}
?>

<main class="bg-slate-50 min-h-screen pb-16 md:pb-24 text-slate-800 pt-24 md:pt-28 lg:pt-32">
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 md:mb-8 border-l-4 border-brandyellow pl-4 md:pl-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-darkblue uppercase tracking-tighter">
                    Monitoring <span class="text-blue-600">Curah Hujan</span>
                </h1>
                <p class="text-slate-500 text-[9px] md:text-[10px] mt-1 uppercase tracking-[0.2em] font-bold">
                    Seluruh Pos Monitoring Terdaftar
                </p>
            </div>
            
            <div class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-slate-200 w-full sm:w-auto">
                <form action="<?= base_url('index.php/CurahHujan') ?>" method="GET" class="flex-1 sm:flex-initial">
                    <input type="date" name="tanggal" 
                           value="<?= $tanggal_pilih ?>" 
                           class="w-full sm:w-auto bg-slate-50 border border-slate-200 text-darkblue text-xs font-bold rounded-lg px-3 py-2 cursor-pointer outline-none focus:ring-2 focus:ring-brandyellow transition-all"
                           onchange="this.form.submit()">
                </form>
                <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
                <div class="text-right hidden sm:block">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Update Terakhir</p>
                    <p class="text-[10px] font-black text-darkblue leading-none"><?= $last_update ?></p>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 overflow-x-auto">
            <div class="flex items-start sm:items-center gap-3 min-w-max">
                <span class="text-[10px] font-black text-darkblue uppercase tracking-widest sm:border-r border-slate-200 sm:pr-4 shrink-0 pt-0.5">
                    Keterangan Intensitas Hujan
                </span>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-[10px] font-bold text-slate-600">
                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-3.5 h-3.5 rounded-sm bg-slate-100 border border-slate-300 block"></span> Nihil / 0
                    </div>
                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-3.5 h-3.5 rounded-sm bg-emerald-500 block shadow-sm"></span> Hujan Ringan (0 - 20 mm)
                    </div>
                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-3.5 h-3.5 rounded-sm bg-yellow-400 block shadow-sm"></span> Hujan Sedang (21 - 50 mm)
                    </div>
                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-3.5 h-3.5 rounded-sm bg-orange-500 block shadow-sm"></span> Hujan Lebat (51 - 100 mm)
                    </div>
                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-3.5 h-3.5 rounded-sm bg-red-500 block shadow-sm"></span> Hujan S. Lebat (101 - 150 mm)
                    </div>
                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-3.5 h-3.5 rounded-sm bg-purple-600 block shadow-sm"></span> Hujan Ekstrem (> 150 mm)
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            
            <div class="bg-darkblue px-5 md:px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3">
                <h3 class="text-white text-xs font-bold tracking-widest uppercase">Data Pengamatan Curah Hujan</h3>
                <div class="relative w-full sm:w-64 lg:w-72">
                    <input type="text" id="searchPos" 
                           placeholder="Cari nama pos atau stasiun..." 
                           class="w-full bg-white/10 border border-white/20 text-white placeholder-blue-200 text-xs font-medium rounded-lg px-3 py-2.5 pl-9 focus:outline-none focus:ring-2 focus:ring-brandyellow transition-all">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="overflow-x-auto overflow-y-auto max-h-[600px]">
                <table class="w-full text-[11px] text-left border-collapse min-w-[1100px]" id="rainTable">
                    <thead class="text-darkblue font-bold uppercase text-center sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th rowspan="2" class="p-4 border-b border-r border-slate-300 bg-slate-100 w-12">No</th>
                            <th rowspan="2" class="p-4 border-b border-r border-slate-300 bg-slate-100 min-w-[280px] text-left">Nama Pos / Stasiun</th>
                            <th colspan="4" class="p-3 border-b border-r border-slate-300 bg-blue-100">Waktu Pengamatan Telemetri (WIB)</th>
                            <th colspan="3" class="p-3 border-b border-slate-300 bg-emerald-100">Waktu Input Manual (WIB)</th>
                        </tr>
                        <tr class="text-[10px]">
                            <th class="p-2 border-b border-r border-slate-300 bg-blue-50">00.00-06.00</th>
                            <th class="p-2 border-b border-r border-slate-300 bg-blue-50">06.01-12.00</th>
                            <th class="p-2 border-b border-r border-slate-300 bg-blue-50">12.01-18.00</th>
                            <th class="p-2 border-b border-r border-slate-300 bg-blue-50">18.01-23.59</th>
                            <th class="p-2 border-b border-r border-slate-300 bg-emerald-50">07.00-11.59</th>
                            <th class="p-2 border-b border-r border-slate-300 bg-emerald-50">12.00-16.59</th>
                            <th class="p-2 border-b border-slate-300 bg-emerald-50">17.00-06.59</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-800 text-center">
                        <?php foreach($pencatatan as $row): ?>
                        <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition-colors">
                            <td class="p-4 border-r border-slate-100 text-slate-400"><?= $row['no'] ?></td>
                            <td class="p-4 border-r border-slate-100 text-left">
                                <span class="font-bold text-darkblue uppercase tracking-tighter"><?= htmlspecialchars($row['pos']) ?></span>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-1">
                                    <?php if($row['api_waktu']): ?>
                                        <span class="text-[8px] text-blue-500 font-medium">Telemetri: <?= $row['api_waktu'] ?> WIB</span>
                                    <?php endif; ?>
                                    <?php if($row['manual_time']): ?>
                                        <span class="text-[8px] text-emerald-600 font-medium">Manual: <?= $row['manual_time'] ?> WIB</span>
                                    <?php endif; ?>
                                    <?php if(!$row['api_waktu'] && !$row['manual_time']): ?>
                                        <span class="text-[8px] text-slate-400">--:--</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-4 border-r border-slate-100 font-semibold <?= getBgIntensity($row['w1']) ?>"><?= safeNumber($row['w1'], 1) ?></td>
                            <td class="p-4 border-r border-slate-100 font-semibold <?= getBgIntensity($row['w2']) ?>"><?= safeNumber($row['w2'], 1) ?></td>
                            <td class="p-4 border-r border-slate-100 font-semibold <?= getBgIntensity($row['w3']) ?>"><?= safeNumber($row['w3'], 1) ?></td>
                            <td class="p-4 border-r border-slate-100 font-semibold <?= getBgIntensity($row['w4']) ?>"><?= safeNumber($row['w4'], 1) ?></td>
                            <td class="p-4 border-r border-slate-100 font-semibold <?= getBgIntensity($row['manual_07']) ?>"><?= safeNumber($row['manual_07'], 1) ?></td>
                            <td class="p-4 border-r border-slate-100 font-semibold <?= getBgIntensity($row['manual_12']) ?>"><?= safeNumber($row['manual_12'], 1) ?></td>
                            <td class="p-4 font-semibold <?= getBgIntensity($row['manual_17']) ?>"><?= safeNumber($row['manual_17'], 1) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($pencatatan)): ?>
                        <tr>
                            <td colspan="10" class="p-10 text-center text-slate-400">
                                <p class="text-sm font-medium">Tidak ada data untuk tanggal ini</p>
                                <p class="text-xs mt-1">Silakan pilih tanggal lain atau periksa koneksi telemetri</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    .overflow-x-auto::-webkit-scrollbar { width: 8px; height: 8px; }
    .overflow-y-auto::-webkit-scrollbar { width: 8px; }
    .overflow-x-auto::-webkit-scrollbar-track,
    .overflow-y-auto::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .overflow-x-auto::-webkit-scrollbar-thumb,
    .overflow-y-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover,
    .overflow-y-auto::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchPos");
        if (searchInput) {
            const tableRows = document.querySelectorAll("#rainTable tbody tr");
            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase().trim();
                tableRows.forEach(row => {
                    const posNameCell = row.querySelector("td:nth-child(2)"); 
                    if (posNameCell) {
                        row.style.display = posNameCell.textContent.toLowerCase().includes(searchTerm) ? "" : "none";
                    }
                });
            });
        }
    });
</script>