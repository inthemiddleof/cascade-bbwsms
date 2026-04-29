<main class="bg-slate-50 min-h-screen pb-24 text-slate-800 lg:pt-36">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        
        <div class="mb-8 border-l-4 border-brandyellow pl-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-blue uppercase tracking-tighter italic">Monitoring <span class="text-blue-600">Tinggi Muka Air</span></h1>
                <p class="text-slate-500 text-[10px] mt-1 uppercase tracking-[0.2em] font-bold">Seluruh Pos Monitoring Terdaftar</p>
            </div>
            
            <div class="flex items-center gap-4 bg-white p-2 pr-4 rounded-xl shadow-sm border border-slate-200">
                <form action="<?= base_url('index.php/welcome/tma') ?>" method="GET" class="flex items-center gap-3">
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            
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
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">TMA Tertinggi</p>
                    <p class="text-3xl font-black text-slate-800 leading-none">
                        <?= number_format($summary['tma_tertinggi'], 2) ?> 
                        <span class="text-sm text-slate-400 font-medium">m</span>
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-5 transition-all hover:shadow-md">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pos Status Siaga</p>
                    <p class="text-3xl font-black text-red-600 leading-none">
                        <?= $summary['status_siaga'] ?> 
                        <span class="text-sm text-slate-400 font-medium">Pos / <?= $summary['status_aman'] ?> Aman</span>
                    </p>
                </div>
            </div>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-darkblue px-6 py-4 flex justify-between items-center">
                <h3 class="text-white text-xs font-bold tracking-widest uppercase">Data Pengamatan Tinggi Muka Air</h3>
            </div>

            <div class="overflow-x-auto table-container">
                <table class="w-full text-[11px] text-left border-collapse min-w-[1100px]" id="tmaTable">
                    <thead class="text-darkblue font-bold uppercase text-center bg-slate-50">
                        <tr>
                            <th rowspan="2" class="p-4 border-b border-r border-slate-200 w-12">No</th>
                            <th rowspan="2" class="p-4 border-b border-r border-slate-200 min-w-[280px] text-left">Nama Pos / Stasiun</th>
                            <th colspan="4" class="p-3 border-b border-r border-slate-200 bg-blue-50/50">Telemetri (m)</th>
                            <th rowspan="2" class="p-4 border-b border-r border-slate-200 w-24 bg-blue-50">Last (m)</th>
                            <th colspan="4" class="p-3 border-b border-r border-slate-200 bg-slate-100">Manual (m)</th>
                            <th colspan="4" class="p-3 border-b border-slate-200 bg-orange-50/30">Siaga (m)</th>
                        </tr>
                        <tr class="text-[10px]">
                            <th class="p-2 border-b border-r border-slate-200 bg-blue-50/30">00.00-06.00</th>
                            <th class="p-2 border-b border-r border-slate-200 bg-blue-50/30">06.01-12.00</th>
                            <th class="p-2 border-b border-r border-slate-200 bg-blue-50/30">12.01-18.00</th>
                            <th class="p-2 border-b border-r border-slate-200 bg-blue-50/30">18.01-23.59</th>
                            
                            <th class="p-2 border-b border-r border-slate-200 bg-slate-50">00.00-06.00</th>
                            <th class="p-2 border-b border-r border-slate-200 bg-slate-50">06.01-12.00</th>
                            <th class="p-2 border-b border-r border-slate-200 bg-slate-50">12.01-18.00</th>
                            <th class="p-2 border-b border-r border-slate-200 bg-slate-50">18.01-23.59</th>
                            
                            <th class="p-2 border-b border-r border-slate-200 bg-emerald-50">Siaga 4</th>
                            <th class="p-2 border-b border-r border-slate-200 bg-yellow-50">Siaga 3</th>
                            <th class="p-2 border-b border-r border-slate-200 bg-orange-50">Siaga 2</th>
                            <th class="p-2 border-b border-slate-200 bg-red-50">Siaga 1</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-800 text-center">
                        <?php foreach($pencatatan_tma as $row): ?>
                        <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition-colors group cursor-crosshair">
                            
                            <td class="p-3 border-r border-slate-100 text-slate-400"><?= $row['no'] ?></td>
                            <td class="p-3 border-r border-slate-100 text-left">
                                <span class="font-bold text-darkblue uppercase tracking-tighter"><?= $row['pos'] ?></span>
                                <?php if($row['waktu'] != '--:--'): ?>
                                    <span class="block text-[9px] text-slate-400 font-medium italic mt-0.5">Update: <?= $row['waktu'] ?> WIB</span>
                                <?php else: ?>
                                    <span class="block text-[9px] text-red-400 font-medium italic mt-0.5">Tidak ada data</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="p-3 border-r border-slate-100 font-semibold text-slate-800"><?= number_format($row['telemetri']['w1'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-semibold text-slate-800"><?= number_format($row['telemetri']['w2'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-semibold text-slate-800"><?= number_format($row['telemetri']['w3'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-semibold text-slate-800"><?= number_format($row['telemetri']['w4'], 2) ?></td>
                            
                            <td class="p-3 border-r border-slate-100 font-black text-blue-700 bg-blue-50/30"><?= number_format($row['last'], 2) ?></td>
                            
                            <td class="p-3 border-r border-slate-100 font-semibold text-slate-800"><?= number_format($row['manual']['w1'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-semibold text-slate-800"><?= number_format($row['manual']['w2'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-semibold text-slate-800"><?= number_format($row['manual']['w3'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-semibold text-slate-800"><?= number_format($row['manual']['w4'], 2) ?></td>

                            <td class="p-3 border-r border-slate-100 font-bold text-emerald-600 bg-emerald-50/20"><?= number_format($row['siaga']['siaga4'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-bold text-amber-500 bg-yellow-50/20"><?= number_format($row['siaga']['siaga3'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-bold text-orange-600 bg-orange-50/20"><?= number_format($row['siaga']['siaga2'], 2) ?></td>
                            <td class="p-3 font-bold text-red-600 bg-red-50/20"><?= number_format($row['siaga']['siaga1'], 2) ?></td>
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
    // Fitur Highlight X & Y untuk kolom Data Telemetri dan Manual
    document.addEventListener("DOMContentLoaded", function() {
        const table = document.getElementById("tmaTable");
        const cells = table.querySelectorAll("td, th");

        cells.forEach(cell => {
            cell.addEventListener("mouseenter", function() {
                // Highlight diterapkan pada index 2-5 (Telemetri) dan 7-10 (Manual) dikarenakan jumlah kolom bertambah
                if ((this.cellIndex >= 2 && this.cellIndex <= 5) || (this.cellIndex >= 7 && this.cellIndex <= 10)) {
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
                if ((this.cellIndex >= 2 && this.cellIndex <= 5) || (this.cellIndex >= 7 && this.cellIndex <= 10)) {
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