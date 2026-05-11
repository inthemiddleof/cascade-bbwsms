<!-- Welcome Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
    <p class="text-slate-500 text-sm mt-1">Selamat datang, <span class="font-semibold text-darkblue"><?= $petugas_name ?></span>. Berikut ringkasan data hari ini.</p>
</div>

<!-- ============================================ -->
<!-- ROW 1: Statistik Cards -->
<!-- ============================================ -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    
    <!-- Input Hari Ini -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Hari Ini</span>
        </div>
        <p class="text-3xl font-black text-darkblue mb-1"><?= $total_hari_ini ?></p>
        <p class="text-xs text-slate-500">Input Hari Ini</p>
        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-slate-100">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-[10px] text-slate-500">Tanggal: <b><?= date('d M Y') ?></b></span>
        </div>
    </div>

    <!-- Input Bulan Ini -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Bulan Ini</span>
        </div>
        <p class="text-3xl font-black text-darkblue mb-1"><?= $total_bulan_ini ?></p>
        <p class="text-xs text-slate-500">Input Bulan <?= date('F') ?></p>
        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-slate-100">
            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <span class="text-[10px] text-slate-500">Periode: <b><?= date('F Y') ?></b></span>
        </div>
    </div>

    <!-- Total Semua Input -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2.5 py-1 rounded-full">Total</span>
        </div>
        <p class="text-3xl font-black text-darkblue mb-1"><?= number_format($total_semua, 0, ',', '.') ?></p>
        <p class="text-xs text-slate-500">Total Semua Input</p>
        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-slate-100">
            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span class="text-[10px] text-slate-500">Akumulasi seluruh data</span>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- ROW 2: Info Pos + Riwayat (1 Baris) -->
<!-- ============================================ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    <!-- Info Pos -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
            <span class="w-1.5 h-5 bg-blue-500 rounded-full"></span>
            Informasi Pos
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2.5 border-b border-slate-100">
                <span class="text-sm text-slate-500">Nama Pos</span>
                <span class="text-sm font-bold text-darkblue"><?= $pos->nama_pos ?></span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-slate-100">
                <span class="text-sm text-slate-500">Tipe Pos</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold <?= ($pos->tipe_pos == 'PCH') ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= ($pos->tipe_pos == 'PCH') ? 'bg-blue-500' : 'bg-green-500' ?>"></span>
                        <?= $pos->tipe_pos ?>
                    </span>
                    <?php if($pos->is_bendungan == 1): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Bendungan
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Pos Biasa
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-slate-100">
                <span class="text-sm text-slate-500">Nomor Pos</span>
                <span class="text-sm font-semibold text-darkblue"><?= $pos->nomor_pos ?: '-' ?></span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-slate-100">
                <span class="text-sm text-slate-500">Sungai</span>
                <span class="text-sm font-semibold text-slate-700"><?= $pos->sungai ?: '-' ?></span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-slate-100">
                <span class="text-sm text-slate-500">Koordinat</span>
                <span class="text-sm font-semibold text-darkblue font-mono text-[11px]"><?= number_format($pos->lat, 6) ?>, <?= number_format($pos->lng, 6) ?></span>
            </div>
            <div class="flex justify-between items-center py-2.5">
                <span class="text-sm text-slate-500">Device ID</span>
                <span class="text-sm font-semibold text-darkblue font-mono text-[11px]"><?= $pos->device_id_telemetry ?: '-' ?></span>
            </div>
        </div>
    </div>

    <!-- Riwayat Input Hari Ini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Input Hari Ini
            </h3>
            <span class="text-[10px] text-slate-400 font-bold"><?= count($riwayat_hari_ini) ?> DATA</span>
        </div>
        
        <?php if(!empty($riwayat_hari_ini)): ?>
        <div class="flex-1 overflow-y-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider sticky top-0">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold w-10">#</th>
                        <th class="px-5 py-3 text-left font-bold">Jam</th>
                        <?php if($pos->tipe_pos == 'PCH' || $pos->is_bendungan == 1): ?>
                        <th class="px-5 py-3 text-left font-bold">Curah Hujan</th>
                        <?php endif; ?>
                        <?php if($pos->tipe_pos == 'PDA' || $pos->is_bendungan == 1): ?>
                        <th class="px-5 py-3 text-left font-bold">TMA</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php $no = 1; foreach($riwayat_hari_ini as $r): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3 text-slate-400"><?= $no++ ?></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-semibold text-darkblue"><?= date('H:i', strtotime($r->created_at)) ?> WIB</span>
                            </div>
                        </td>
                        <?php if($pos->tipe_pos == 'PCH' || $pos->is_bendungan == 1): ?>
                        <td class="px-5 py-3"><span class="font-bold text-blue-600"><?= $r->rain !== null ? $r->rain.' mm' : '-' ?></span></td>
                        <?php endif; ?>
                        <?php if($pos->tipe_pos == 'PDA' || $pos->is_bendungan == 1): ?>
                        <td class="px-5 py-3"><span class="font-bold text-green-600"><?= $r->wlevel !== null ? $r->wlevel.' m' : '-' ?></span></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="flex-1 flex items-center justify-center py-12">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="text-slate-400 text-sm">Belum ada input hari ini</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================ -->
<!-- ROW 3: Parameter Bendungan (jika bendungan) -->
<!-- ============================================ -->
<!-- Parameter Bendungan (jika bendungan) -->
<?php if($pos->is_bendungan == 1): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider mb-5 flex items-center gap-2">
        <span class="w-1.5 h-5 bg-amber-500 rounded-full"></span>
        Parameter Bendungan
    </h3>
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-amber-50 rounded-xl p-5 text-center border border-amber-100">
            <p class="text-[10px] text-amber-500 uppercase font-bold tracking-wider mb-2">NWL</p>
            <p class="text-2xl font-black text-amber-700">
                <?= $pos->nwl ? rtrim(rtrim(number_format($pos->nwl, 2), '0'), '.') : '-' ?>
            </p>
            <p class="text-[10px] text-amber-400 mt-1">meter</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-5 text-center border border-amber-100">
            <p class="text-[10px] text-amber-500 uppercase font-bold tracking-wider mb-2">Volume NWL</p>
            <p class="text-2xl font-black text-amber-700">
                <?= $pos->nwl_volume ? rtrim(rtrim(number_format($pos->nwl_volume, 0), '0'), '.') : '-' ?>
            </p>
            <p class="text-[10px] text-amber-400 mt-1">jt.m³</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-5 text-center border border-amber-100">
            <p class="text-[10px] text-amber-500 uppercase font-bold tracking-wider mb-2">Luas NWL</p>
            <p class="text-2xl font-black text-amber-700">
                <?= $pos->nwl_luas ? rtrim(rtrim(number_format($pos->nwl_luas, 3), '0'), '.') : '-' ?>
            </p>
            <p class="text-[10px] text-amber-400 mt-1">km²</p>
        </div>
    </div>
</div>
<?php endif; ?>