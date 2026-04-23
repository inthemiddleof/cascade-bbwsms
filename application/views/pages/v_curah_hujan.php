<main class="bg-slate-50 min-h-screen pb-24 text-slate-800">
<div class="max-w-7xl mx-auto px-6 lg:px-12 pt-12">
        
        <div class="mb-10 border-l-4 border-brandyellow pl-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-darkblue uppercase tracking-tighter italic">Monitoring <span class="text-blue-600">Curah Hujan</span></h1>
                <p class="text-slate-500 text-[10px] mt-1 uppercase tracking-[0.2em] font-bold">Data Real-time Sistem Cascade</p>
            </div>
            
            <div class="flex items-center gap-4 bg-white p-2 pr-4 rounded-xl shadow-sm border border-slate-200">
                <form action="<?= base_url('index.php/welcome/curah_hujan') ?>" method="GET" class="flex items-center gap-3">
                    <div class="relative">
                        <input type="date" 
                               name="tanggal" 
                               value="<?= $tanggal_pilih ?>" 
                               class="bg-slate-50 border border-slate-200 text-darkblue text-xs font-bold rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-3 pr-2 py-2 cursor-pointer transition-all"
                               onchange="this.form.submit()">
                    </div>
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
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Status Pos Stasiun</p>
                <p class="text-2xl font-black text-emerald-600"><?= $summary['pos_aktif'] ?>/<?= $summary['total_pos'] ?> <span class="text-xs text-slate-400 font-medium">Aktif</span></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Hujan Maks (Hulu/Hilir)</p>
                <p class="text-2xl font-black text-darkblue">
                    <?= number_format($summary['max_hulu'], 1) ?> / <?= number_format($summary['max_hilir'], 1) ?> 
                    <span class="text-xs text-slate-400 font-medium">mm</span>
                </p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Rata-rata Wilayah</p>
                <p class="text-2xl font-black text-blue-500"><?= number_format($summary['avg_wilayah'], 1) ?> <span class="text-xs text-slate-400 font-medium">mm</span></p>
            </div>
        </div>

        <div class="space-y-12">
            
            <?php 
            $tables = [
                ['title' => 'Wilayah Aliran Hulu', 'data' => $pencatatan_hulu, 'theme' => 'bg-darkblue'],
                ['title' => 'Wilayah Aliran Hilir', 'data' => $pencatatan_hilir, 'theme' => 'bg-blue-800']
            ];

            foreach($tables as $table): 
            ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="<?= $table['theme'] ?> px-6 py-3 flex justify-between items-center">
                    <h3 class="text-white text-xs font-bold tracking-widest uppercase flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-brandyellow rounded-full animate-pulse"></span>
                        <?= $table['title'] ?>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-[11px] text-left border-collapse min-w-[1000px]">
                        <thead class="bg-slate-50 text-darkblue font-bold uppercase border-b border-slate-200 text-center">
                            <tr>
                                <th rowspan="2" class="sticky left-0 z-30 bg-slate-50 p-4 border-r border-slate-200 w-12">No</th>
                                <th rowspan="2" class="sticky left-12 z-30 bg-slate-50 p-4 border-r border-slate-200 min-w-[220px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] text-left uppercase">Pos Stasiun</th>
                                <th colspan="4" class="p-3 border-r border-slate-200 italic tracking-wider bg-slate-100/50">Intensitas Per Periode (WIB)</th>
                                <th rowspan="2" class="p-4 border-r border-slate-200 bg-slate-200/30">Total Telemetri</th>
                                <th rowspan="2" class="p-4 bg-slate-200/30">Manual Harian</th>
                            </tr>
                            <tr class="text-[9px] bg-slate-50">
                                <th class="p-2 border-r border-slate-200 font-semibold">07.01-13.00</th>
                                <th class="p-2 border-r border-slate-200 font-semibold">13.01-19.00</th>
                                <th class="p-2 border-r border-slate-200 font-semibold">19.01-01.00</th>
                                <th class="p-2 border-r border-slate-200 font-semibold">01.01-07.00</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($table['data'])): ?>
                                <tr>
                                    <td colspan="8" class="p-10 text-center text-slate-400 italic">Data tidak ditemukan untuk tanggal ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($table['data'] as $row): ?>
                                <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition-colors group text-center">
                                    <td class="sticky left-0 z-20 bg-white group-hover:bg-blue-50 p-3 border-r border-slate-100 text-slate-400 font-medium">
                                        <?= $row['no'] ?>
                                    </td>
                                    <td class="sticky left-12 z-20 bg-white group-hover:bg-blue-50 p-3 border-r border-slate-100 font-bold text-darkblue text-left shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] uppercase tracking-tight">
                                        <?= $row['pos'] ?>
                                    </td>
                                    <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['w1'], 2) ?></td>
                                    <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['w2'], 2) ?></td>
                                    <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['w3'], 2) ?></td>
                                    <td class="p-3 border-r border-slate-100 text-slate-600"><?= number_format($row['w4'], 2) ?></td>
                                    <td class="p-3 border-r border-slate-100 font-black <?= ($row['total'] > 0) ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' ?>">
                                        <?= number_format($row['total'], 2) ?>
                                    </td>
                                    <td class="p-3 font-black <?= ($row['manual'] > 0 && is_numeric($row['manual'])) ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400' ?>">
                                        <?= $row['manual'] ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
</main>

<style>
    /* Scrollbar Styling */
    .overflow-x-auto::-webkit-scrollbar { height: 8px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f1f5f9; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f1f5f9; }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>