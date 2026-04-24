<main class="bg-slate-50 min-h-screen pb-24 text-slate-800">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-12">
        
        <div class="mb-10 border-l-4 border-brandyellow pl-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-darkblue uppercase tracking-tighter italic">Monitoring <span class="text-blue-600">Tinggi Muka Air</span></h1>
                <p class="text-slate-500 text-[10px] mt-1 uppercase tracking-[0.2em] font-bold">Data Real-time Sistem Cascade</p>
            </div>
            
            <div class="flex items-center gap-4 bg-white p-2 pr-4 rounded-xl shadow-sm border border-slate-200">
                <form action="<?= base_url('index.php/welcome/tma') ?>" method="GET" class="flex items-center gap-3">
                    <input type="date" name="tanggal" value="<?= $tanggal_pilih ?>" 
                           class="bg-slate-50 border border-slate-200 text-darkblue text-xs font-bold rounded-lg px-3 py-2 cursor-pointer"
                           onchange="this.form.submit()">
                </form>
                <div class="h-8 w-px bg-slate-200"></div>
                <div class="text-right">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Update Terakhir</p>
                    <p class="text-xs font-black text-darkblue leading-none"><?= $last_update ?></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Status Koneksi Pos</p>
                <p class="text-2xl font-black text-emerald-600"><?= $summary['pos_aktif'] ?>/<?= $summary['total_pos'] ?> <span class="text-xs text-slate-400 font-medium">Online</span></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">TMA Tertinggi</p>
                <p class="text-2xl font-black text-darkblue"><?= number_format($summary['tma_tertinggi'], 2) ?> <span class="text-xs text-slate-400 font-medium">m</span></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Kondisi Kritis</p>
                <p class="text-2xl font-black text-blue-500"><?= $summary['status_siaga'] ?> <span class="text-xs text-slate-400 font-medium">Pos</span></p>
            </div>
        </div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-darkblue px-6 py-3">
                <h3 class="text-white text-[10px] font-bold tracking-widest uppercase flex items-center gap-2 text-center justify-center">
                    Wilayah Hulu & Hilir - Pemantauan Tinggi Muka Air
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[10px] text-left border-collapse min-w-[1200px]">
                    <thead class="bg-slate-50 text-darkblue font-bold uppercase border-b border-slate-200 text-center">
                        <tr>
                            <th rowspan="3" class="sticky left-0 z-30 bg-slate-50 p-4 border-r border-slate-200 w-12 text-[11px]">No</th>
                            <th rowspan="3" class="sticky left-12 z-30 bg-slate-50 p-4 border-r border-slate-200 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] text-left text-[11px]">Pos Tinggi Muka Air</th>
                            <th colspan="7" class="p-2 border-r border-slate-200">Waktu (WIB)</th>
                            <th colspan="3" class="p-2 border-r border-slate-200 bg-slate-100">Level Siaga (TTG)</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="p-2 border-r border-slate-200 bg-blue-50/50">Telemetri</th>
                            <th colspan="3" class="p-2 border-r border-slate-200 bg-emerald-50/50">Manual</th>
                            <th rowspan="2" class="p-2 border-r border-slate-200 text-emerald-600 bg-emerald-50">Siaga Hijau</th>
                            <th rowspan="2" class="p-2 border-r border-slate-200 text-amber-600 bg-amber-50">Siaga Kuning</th>
                            <th rowspan="2" class="p-2 text-red-600 bg-red-50">Siaga Merah</th>
                        </tr>
                        <tr class="text-[9px]">
                            <th class="p-2 border-r border-slate-200">06.00</th>
                            <th class="p-2 border-r border-slate-200">12.00</th>
                            <th class="p-2 border-r border-slate-200">18.00</th>
                            <th class="p-2 border-r border-slate-200 bg-blue-100 italic">Last Data</th>
                            
                            <th class="p-2 border-r border-slate-200">06.00</th>
                            <th class="p-2 border-r border-slate-200">12.00</th>
                            <th class="p-2 border-r border-slate-200 text-center">18.00</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pencatatan_tma as $row): ?>
                        <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition-colors group text-center">
                            <td class="sticky left-0 z-20 bg-white group-hover:bg-blue-50 p-3 border-r border-slate-100 font-medium text-slate-400"><?= $row['no'] ?></td>
                            <td class="sticky left-12 z-20 bg-white group-hover:bg-blue-50 p-3 border-r border-slate-100 font-bold text-darkblue text-left shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] uppercase tracking-tighter"><?= $row['pos'] ?></td>
                            
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['telemetri']['06'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['telemetri']['12'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['telemetri']['18'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-black text-blue-700 bg-blue-50/50"><?= number_format($row['telemetri']['last'], 2) ?></td>
                            
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['manual']['06'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['manual']['12'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['manual']['18'], 2) ?></td>

                            <td class="p-3 border-r border-slate-100 font-bold text-emerald-600 bg-emerald-50/20"><?= number_format($row['siaga']['hijau'], 2) ?></td>
                            <td class="p-3 border-r border-slate-100 font-bold text-amber-600 bg-amber-50/20"><?= number_format($row['siaga']['kuning'], 2) ?></td>
                            <td class="p-3 font-bold text-red-600 bg-red-50/20"><?= number_format($row['siaga']['merah'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    /* Custom Scrollbar agar tidak menutupi data */
    .overflow-x-auto::-webkit-scrollbar { height: 8px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f1f5f9; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f1f5f9; }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>