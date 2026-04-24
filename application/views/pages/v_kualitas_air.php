<main class="bg-slate-50 min-h-screen pb-24 text-slate-800">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-12">
        
        <div class="mb-10 border-l-4 border-brandyellow pl-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-darkblue uppercase tracking-tighter italic">Monitoring <span class="text-blue-600">Kualitas Air</span></h1>
                <p class="text-slate-500 text-[10px] mt-1 uppercase tracking-[0.2em] font-bold">Real-time Water Quality Analysis</p>
            </div>
            
            <div class="flex items-center gap-4 bg-white p-2 pr-4 rounded-xl shadow-sm border border-slate-200">
                <form action="<?= base_url('index.php/welcome/kualitas_air') ?>" method="GET" class="flex items-center gap-3">
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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Total Stasiun</p>
                <p class="text-2xl font-black text-darkblue"><?= $summary['pos_aktif'] ?>/<?= $summary['total_pos'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Kualitas Baik</p>
                <p class="text-2xl font-black text-emerald-600"><?= $summary['status_aman'] ?> <span class="text-xs text-slate-400 font-medium">Titik</span></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Tercemar Ringan</p>
                <p class="text-2xl font-black text-amber-500"><?= $summary['status_waspada'] ?> <span class="text-xs text-slate-400 font-medium">Titik</span></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Indeks Rata-rata</p>
                <p class="text-2xl font-black text-blue-600">84.2 <span class="text-xs text-slate-400 font-medium">IKA</span></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-darkblue px-6 py-3">
                <h3 class="text-white text-xs font-bold tracking-widest uppercase flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-brandyellow rounded-full animate-pulse"></span>
                    Parameter Fisika & Kimia Per Stasiun
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[11px] text-left border-collapse min-w-[1100px]">
                    <thead class="bg-slate-50 text-darkblue font-bold uppercase border-b border-slate-200 text-center">
                        <tr>
                            <th rowspan="2" class="sticky left-0 z-30 bg-slate-50 p-4 border-r border-slate-200 w-12">NO</th>
                            <th rowspan="2" class="sticky left-12 z-30 bg-slate-50 p-4 border-r border-slate-200 min-w-[220px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] text-left">NAMA STASIUN MONITORING</th>
                            <th rowspan="2" class="p-4 border-r border-slate-200">WAKTU</th>
                            <th colspan="5" class="p-2 border-r border-slate-200 bg-blue-50/50">PARAMETER SENSOR</th>
                            <th rowspan="2" class="p-4">STATUS KUALITAS</th>
                        </tr>
                        <tr class="text-[9px] bg-slate-50">
                            <th class="p-2 border-r border-slate-200">pH</th>
                            <th class="p-2 border-r border-slate-200">Suhu (°C)</th>
                            <th class="p-2 border-r border-slate-200">DO (mg/L)</th>
                            <th class="p-2 border-r border-slate-200">Turbidity (NTU)</th>
                            <th class="p-2 border-r border-slate-200">TDS (ppm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pencatatan_kualitas as $row): ?>
                        <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition-colors group text-center">
                            <td class="sticky left-0 z-20 bg-white group-hover:bg-blue-50 p-3 border-r border-slate-100 text-slate-400"><?= $row['no'] ?></td>
                            <td class="sticky left-12 z-20 bg-white group-hover:bg-blue-50 p-3 border-r border-slate-100 font-bold text-darkblue text-left shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] uppercase"><?= $row['pos'] ?></td>
                            <td class="p-3 border-r border-slate-100 font-medium text-slate-500 italic"><?= $row['waktu'] ?> WIB</td>
                            
                            <td class="p-3 border-r border-slate-100 font-bold <?= ($row['ph'] < 6.5 || $row['ph'] > 8.5) ? 'text-red-600' : 'text-slate-700' ?>"><?= number_format($row['ph'], 1) ?></td>
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['temp'], 1) ?></td>
                            <td class="p-3 border-r border-slate-100 text-slate-600 font-semibold"><?= number_format($row['do'], 1) ?></td>
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['turbidity'], 1) ?></td>
                            <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['tds'], 0) ?></td>

                            <td class="p-3">
                                <?php 
                                    $statusColor = 'bg-emerald-500';
                                    if($row['status'] != 'BAIK') $statusColor = 'bg-amber-500';
                                ?>
                                <span class="<?= $statusColor ?> text-white text-[9px] font-bold px-3 py-1 rounded-full uppercase italic tracking-tighter">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-4">
            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Kondisi Baik</span>
            </div>
            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Tercemar Ringan</span>
            </div>
            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Tercemar Berat / pH Abnormal</span>
            </div>
        </div>

    </div>
</main>