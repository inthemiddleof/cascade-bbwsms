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

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
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

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
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

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
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

    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
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

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Semua Pos Monitoring</h3>
    </div>
    <div class="overflow-auto max-h-[360px]">
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
                <tr class="hover:bg-slate-50 transition-colors">
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