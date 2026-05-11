<!-- Welcome Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
    <p class="text-slate-500 text-sm mt-1">Selamat datang, <span class="font-semibold text-darkblue"><?= $admin_name ?></span>. Berikut ringkasan sistem monitoring hari ini.</p>
</div>

<!-- ============================================ -->
<!-- ROW 1: 4 Statistik Utama -->
<!-- ============================================ -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <!-- Total Pos Monitoring -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Total</span>
        </div>
        <p class="text-3xl font-black text-darkblue mb-1"><?= $total_pos ?></p>
        <p class="text-xs text-slate-500">Total Pos Monitoring</p>
        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <span class="text-[10px] text-slate-500 font-medium"><?= $total_pch ?> PCH</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                <span class="text-[10px] text-slate-500 font-medium"><?= $total_pda ?> PDA</span>
            </div>
        </div>
    </div>

    <!-- Total Petugas Terdaftar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><?= $petugas_aktif ?> Aktif</span>
        </div>
        <p class="text-3xl font-black text-darkblue mb-1"><?= $total_petugas ?></p>
        <p class="text-xs text-slate-500">Total Petugas Terdaftar</p>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="w-full bg-slate-100 rounded-full h-1.5">
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-700" style="width: <?= $total_petugas > 0 ? round(($petugas_aktif/$total_petugas)*100) : 0 ?>%"></div>
            </div>
            <p class="text-[10px] text-slate-400 mt-1"><?= $total_petugas > 0 ? round(($petugas_aktif/$total_petugas)*100) : 0 ?>% dalam status aktif</p>
        </div>
    </div>

    <!-- Pos Online -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><?= $total_pos > 0 ? round(($pos_online/$total_pos)*100) : 0 ?>%</span>
        </div>
        <p class="text-3xl font-black text-darkblue mb-1"><?= $pos_online ?> <span class="text-lg text-slate-300 font-medium">/ <?= $total_pos ?></span></p>
        <p class="text-xs text-slate-500">Pos Online (1 Jam Terakhir)</p>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="w-full bg-slate-100 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full transition-all duration-700" style="width: <?= $total_pos > 0 ? round(($pos_online/$total_pos)*100) : 0 ?>%"></div>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Koneksi real-time ke server</p>
        </div>
    </div>

    <!-- Record Data Telemetri -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Hari Ini</span>
        </div>
        <p class="text-3xl font-black text-darkblue mb-1"><?= number_format($total_data_hari_ini, 0, ',', '.') ?></p>
        <p class="text-xs text-slate-500">Record Data Telemetri</p>
        <div class="mt-3 pt-3 border-t border-slate-100 space-y-1.5">
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[10px] text-slate-500">Tanggal: <b><?= date('d M Y') ?></b></span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span class="text-[10px] text-slate-500">Sinkronisasi: <b><?= $last_sync ? date('H:i', strtotime($last_sync)) : '--:--' ?> WIB</b></span>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ROW 2: Tabel Ringkasan Pos Terbaru -->
<!-- ============================================ -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            Pos dengan Data Terbaru
        </h3>
        <a href="<?= base_url('admin/kelola_pos') ?>" class="text-[10px] text-brandyellow font-bold hover:underline">Lihat Semua Pos</a>
    </div>
    
    <div class="overflow-x-auto">
        <?php $pos_summary = $this->M_auth->get_pos_summary(); ?>
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left font-bold">Nama Pos</th>
                    <th class="px-5 py-3 text-center font-bold w-16">Tipe</th>
                    <th class="px-5 py-3 text-left font-bold">Sungai</th>
                    <th class="px-5 py-3 text-left font-bold">Petugas</th>
                    <th class="px-5 py-3 text-center font-bold w-24">Total Data</th>
                    <th class="px-5 py-3 text-center font-bold w-28">Data Terakhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(!empty($pos_summary)): 
                    foreach($pos_summary as $ps): 
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-darkblue"><?= $ps->nama_pos ?></p>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold <?= ($ps->tipe_pos == 'PCH') ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' ?>">
                            <?= $ps->tipe_pos ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-500"><?= $ps->sungai ?: '<span class="text-slate-300">-</span>' ?></td>
                    <td class="px-5 py-3">
                        <?php if(!empty($ps->petugas_nama)): ?>
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-brandyellow/20 flex items-center justify-center">
                                    <span class="text-darkblue font-bold text-[7px]"><?= strtoupper(substr($ps->petugas_nama, 0, 2)) ?></span>
                                </div>
                                <span class="text-darkblue font-semibold text-[11px]"><?= $ps->petugas_nama ?></span>
                            </div>
                        <?php else: ?>
                            <span class="text-orange-400 text-[10px] bg-orange-50 px-2 py-0.5 rounded-full">Belum ada</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-sm font-bold text-darkblue"><?= number_format($ps->total_data, 0, ',', '.') ?></span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <?php if(!empty($ps->last_data)): ?>
                            <div>
                                <p class="text-[10px] text-slate-600 font-medium"><?= date('d M Y', strtotime($ps->last_data)) ?></p>
                                <p class="text-[9px] text-slate-400"><?= date('H:i', strtotime($ps->last_data)) ?> WIB</p>
                            </div>
                        <?php else: ?>
                            <span class="text-[10px] text-slate-300 italic">Belum ada</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p class="text-slate-400">Belum ada data telemetri</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Live Server Clock
function updateServerClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const clockEl = document.getElementById('serverClock');
    if (clockEl) {
        clockEl.textContent = hours + ':' + minutes + ':' + seconds + ' WIB';
    }
}
setInterval(updateServerClock, 1000);
updateServerClock();
</script>