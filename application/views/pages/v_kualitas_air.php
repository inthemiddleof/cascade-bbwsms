<main class="bg-slate-50 min-h-screen pb-24 text-slate-800">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-12">
        
        <!-- HEADER -->
        <div class="mb-10 border-l-4 border-brandyellow pl-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-darkblue uppercase tracking-tighter italic">
                    Monitoring <span class="text-blue-600">Kualitas Air</span>
                </h1>
                <p class="text-slate-500 text-[10px] mt-1 uppercase tracking-[0.2em] font-bold">
                    Real-time Water Quality Analysis
                </p>
            </div>
            
            <div class="flex items-center gap-4 bg-white p-2 pr-4 rounded-xl shadow-sm border border-slate-200">
                <form action="<?= base_url('index.php/welcome/kualitas_air') ?>" method="GET" class="flex items-center gap-3">
                    <input type="date" 
                           name="tanggal" 
                           value="<?= $tanggal_pilih ?? date('Y-m-d') ?>" 
                           class="bg-slate-50 border border-slate-200 text-darkblue text-xs font-bold rounded-lg px-3 py-2 cursor-pointer"
                           onchange="this.form.submit()">
                </form>

                <div class="h-8 w-px bg-slate-200"></div>

                <div class="text-right">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                        Update Terakhir
                    </p>
                    <p class="text-xs font-black text-darkblue">
                        <?= $last_update ?? '-' ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Total Stasiun</p>
                <p class="text-2xl font-black text-darkblue">
                    <?= $summary['pos_aktif'] ?? 0 ?>/<?= $summary['total_pos'] ?? 0 ?>
                </p>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Kualitas Baik</p>
                <p class="text-2xl font-black text-emerald-600">
                    <?= $summary['status_aman'] ?? 0 ?> 
                    <span class="text-xs text-slate-400">Titik</span>
                </p>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Tercemar Ringan</p>
                <p class="text-2xl font-black text-amber-500">
                    <?= $summary['status_waspada'] ?? 0 ?> 
                    <span class="text-xs text-slate-400">Titik</span>
                </p>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Indeks Rata-rata</p>
                <p class="text-2xl font-black text-blue-600">
                    <?= $summary['ika'] ?? '0.0' ?> 
                    <span class="text-xs text-slate-400">IKA</span>
                </p>
            </div>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-darkblue px-6 py-3">
                <h3 class="text-white text-xs font-bold tracking-widest uppercase flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-brandyellow rounded-full animate-pulse"></span>
                    Parameter Fisika & Kimia Per Stasiun
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[11px] border-collapse min-w-[1100px]">
                    
                    <thead class="bg-slate-50 text-darkblue font-bold uppercase border-b text-center">
                        <tr>
                            <th rowspan="2" class="sticky left-0 bg-slate-50 p-3 border-r">No</th>
                            <th rowspan="2" class="sticky left-12 bg-slate-50 p-3 border-r text-left min-w-[220px]">
                                Nama Stasiun
                            </th>
                            <th rowspan="2">Waktu</th>
                            <th colspan="5" class="bg-blue-50">Parameter Sensor</th>
                            <th rowspan="2">Status</th>
                        </tr>
                        <tr>
                            <th>pH</th>
                            <th>Suhu</th>
                            <th>DO</th>
                            <th>Turbidity</th>
                            <th>TDS</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($pencatatan_kualitas)): ?>
                            <?php foreach($pencatatan_kualitas as $row): ?>

                            <?php
                                $ph = $row['ph'] ?? 0;
                                $status = $row['status'] ?? 'UNKNOWN';

                                // warna status
                                $statusColor = 'bg-emerald-500';
                                if ($status == 'CEMAR RINGAN') $statusColor = 'bg-amber-500';
                                if ($status == 'CEMAR BERAT') $statusColor = 'bg-red-500';
                            ?>

                            <tr class="border-b text-center hover:bg-blue-50">

                                <td><?= $row['no'] ?? '-' ?></td>

                                <td class="text-left font-bold">
                                    <?= $row['pos'] ?? '-' ?>
                                </td>

                                <td class="italic text-slate-500">
                                    <?= ($row['waktu'] ?? '-') ?> WIB
                                </td>

                                <!-- PH -->
                                <td class="font-bold <?= ($ph < 6.5 || $ph > 8.5) ? 'text-red-600' : 'text-slate-700' ?>">
                                    <?= number_format($ph, 1) ?>
                                </td>

                                <td><?= number_format($row['temp'] ?? 0, 1) ?></td>
                                <td><?= number_format($row['do'] ?? 0, 1) ?></td>
                                <td><?= number_format($row['turbidity'] ?? 0, 1) ?></td>
                                <td><?= number_format($row['tds'] ?? 0, 0) ?></td>

                                <!-- STATUS -->
                                <td>
                                    <span class="<?= $statusColor ?> text-white text-[9px] px-3 py-1 rounded-full uppercase">
                                        <?= $status ?>
                                    </span>
                                </td>

                            </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center p-6 text-slate-400 italic">
                                    Data kualitas air tidak tersedia
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

        <!-- LEGEND -->
        <div class="mt-6 flex flex-wrap gap-4">
            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border">
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Baik</span>
            </div>

            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border">
                <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Cemar Ringan</span>
            </div>

            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border">
                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Cemar Berat</span>
            </div>
        </div>

    </div>
</main>