<main class="bg-slate-50 min-h-screen pb-24 text-slate-800">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-12">
        
        <div class="mb-8 border-l-4 border-brandyellow pl-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-blue uppercase tracking-tighter italic">Monitoring <span class="text-blue-600">Curah Hujan</span></h1>
                <p class="text-slate-500 text-[10px] mt-1 uppercase tracking-[0.2em] font-bold">Seluruh Pos Monitoring Terdaftar</p>
            </div>
            
            <div class="flex items-center gap-4 bg-white p-2 pr-4 rounded-xl shadow-sm border border-slate-200">
                <form action="<?= base_url('index.php/welcome/curah_hujan') ?>" method="GET" class="flex items-center gap-3">
                    <input type="date" name="tanggal" value="<?= $tanggal_pilih ?>" 
                           class="bg-slate-50 border border-slate-200 text-darkblue text-xs font-bold rounded-lg px-3 py-2 cursor-pointer outline-none focus:ring-2 focus:ring-blue-500"
                           onchange="this.form.submit()">
                </form>
                <div class="h-8 w-px bg-slate-200"></div>
                <div class="text-right">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Update Terakhir</p>
                    <p class="text-xs font-black text-darkblue leading-none"><?= $last_update ?></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-5 transition-all hover:shadow-md">
                <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status Koneksi Pos</p>
                    <p class="text-3xl font-black text-slate-800 leading-none">
                        <?= $summary['pos_aktif'] ?> 
                        <span class="text-sm text-slate-400 font-medium">/ <?= $summary['total_pos'] ?> Aktif</span>
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-5 transition-all hover:shadow-md">
                <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hujan Tertinggi</p>
                    <p class="text-3xl font-black text-slate-800 leading-none">
                        <?= number_format($summary['max_hujan'], 1) ?> 
                        <span class="text-sm text-slate-400 font-medium">mm</span>
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-5 transition-all hover:shadow-md">
                <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Rata-rata Wilayah</p>
                    <p class="text-3xl font-black text-slate-800 leading-none">
                        <?= number_format($summary['avg_wilayah'], 1) ?> 
                        <span class="text-sm text-slate-400 font-medium">mm</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col xl:flex-row items-center gap-4">
            <span class="text-[10px] font-black text-darkblue uppercase tracking-widest min-w-max xl:border-r border-slate-200 xl:pr-4">Keterangan Intensitas Hujan</span>
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[10px] font-bold text-slate-600">
                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-sm bg-slate-100 border border-slate-300 block"></span> Nihil / 0</div>
                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-sm bg-emerald-500 block shadow-sm"></span> Hujan Ringan (0 - 20 mm)</div>
                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-sm bg-yellow-400 block shadow-sm"></span> Hujan Sedang (21 - 50 mm)</div>
                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-sm bg-orange-500 block shadow-sm"></span> Hujan Lebat (51 - 100 mm)</div>
                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-sm bg-red-500 block shadow-sm"></span> Hujan S. Lebat (101 - 150 mm)</div>
                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-sm bg-purple-600 block shadow-sm"></span> Hujan Ekstrem (> 150 mm)</div>
            </div>
        </div>

        <?php 
            function getBgIntensity($val) {
                if (!is_numeric($val) || $val <= 0) return 'bg-slate-50 text-slate-400';
                if ($val > 0 && $val <= 20) return 'bg-emerald-500 text-white';
                if ($val > 20 && $val <= 50) return 'bg-yellow-400 text-slate-800';
                if ($val > 50 && $val <= 100) return 'bg-orange-500 text-white';
                if ($val > 100 && $val <= 150) return 'bg-red-500 text-white';
                if ($val > 150) return 'bg-purple-600 text-white';
                return 'bg-slate-50 text-slate-400';
            }
        ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-darkblue px-6 py-4 flex justify-between items-center">
                <h3 class="text-white text-xs font-bold tracking-widest uppercase">Data Pengamatan Curah Hujan</h3>
            </div>

            <div class="overflow-x-auto table-container">
                <table class="w-full text-[11px] text-left border-collapse min-w-[950px]" id="rainTable">
                    <thead class="text-darkblue font-bold uppercase text-center bg-slate-50">
                        <tr>
                            <th rowspan="2" class="p-4 border-b border-r border-slate-200 w-12">No</th>
                            <th rowspan="2" class="p-4 border-b border-r border-slate-200 min-w-[280px] text-left">Nama Pos / Stasiun</th>
                            <th colspan="4" class="p-3 border-b border-r border-slate-200 bg-blue-50/50">Waktu Pengamatan (WIB)</th>
                            <th rowspan="2" class="p-4 border-b border-r border-slate-200 w-24">Total (mm)</th>
                            <th rowspan="2" class="p-4 border-b border-slate-200 w-24">Manual (mm)</th>
                        </tr>
                        <tr class="text-[10px] bg-blue-50/30">
                            <th class="p-2 border-b border-r border-slate-200">07.01-13.00</th>
                            <th class="p-2 border-b border-r border-slate-200">13.01-19.00</th>
                            <th class="p-2 border-b border-r border-slate-200">19.01-01.00</th>
                            <th class="p-2 border-b border-r border-slate-200">01.01-07.00</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-800">
                        <?php foreach($pencatatan as $row): ?>
                        <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition-colors group cursor-crosshair">
                            
                            <td class="p-3 border-r border-slate-100 text-center text-slate-400"><?= $row['no'] ?></td>
                            <td class="p-3 border-r border-slate-100">
                                <span class="font-bold text-darkblue uppercase tracking-tighter"><?= $row['pos'] ?></span>
                                <?php if($row['waktu'] != '--:--'): ?>
                                    <span class="block text-[9px] text-slate-400 font-medium italic mt-0.5">Update: <?= $row['waktu'] ?> WIB</span>
                                <?php else: ?>
                                    <span class="block text-[9px] text-red-400 font-medium italic mt-0.5">Tidak ada data</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="p-3 border-r border-slate-100 text-center font-semibold text-slate-800"><?= number_format($row['w1'], 1) ?></td>
                            <td class="p-3 border-r border-slate-100 text-center font-semibold text-slate-800"><?= number_format($row['w2'], 1) ?></td>
                            <td class="p-3 border-r border-slate-100 text-center font-semibold text-slate-800"><?= number_format($row['w3'], 1) ?></td>
                            <td class="p-3 border-r border-slate-100 text-center font-semibold text-slate-800"><?= number_format($row['w4'], 1) ?></td>
                            
                            <td class="p-3 border-r border-slate-100 text-center font-black <?= getBgIntensity($row['total']) ?>">
                                <?= number_format($row['total'], 1) ?>
                            </td>
                            
                            <td class="p-3 text-center font-black <?= getBgIntensity($row['manual']) ?>">
                                <?= is_numeric($row['manual']) ? number_format($row['manual'], 1) : $row['manual'] ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    /* Styling Scrollbar khusus untuk tabel */
    .table-container::-webkit-scrollbar {
        height: 8px;
    }
    .table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .table-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .col-highlight {
        background-color: rgba(239, 246, 255, 0.7) !important;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const table = document.getElementById("rainTable");
        const cells = table.querySelectorAll("td, th");

        cells.forEach(cell => {
            cell.addEventListener("mouseenter", function() {
                if (this.cellIndex > 1 && this.cellIndex < 6) { // Hanya overlay pada kolom w1-w4
                    const colIndex = this.cellIndex;
                    const rows = table.querySelectorAll("tr");
                    rows.forEach(row => {
                        const cellInSameCol = row.children[colIndex];
                        if (cellInSameCol && cellInSameCol.tagName === 'TD') {
                            cellInSameCol.classList.add("col-highlight");
                        }
                    });
                }
            });

            cell.addEventListener("mouseleave", function() {
                if (this.cellIndex > 1 && this.cellIndex < 6) {
                    const colIndex = this.cellIndex;
                    const rows = table.querySelectorAll("tr");
                    rows.forEach(row => {
                        const cellInSameCol = row.children[colIndex];
                        if (cellInSameCol && cellInSameCol.tagName === 'TD') {
                            cellInSameCol.classList.remove("col-highlight");
                        }
                    });
                }
            });
        });
    });
</script>