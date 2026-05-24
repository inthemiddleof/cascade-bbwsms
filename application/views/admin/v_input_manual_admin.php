<div class="mb-6">
    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Pilih Pos untuk Input:</label>
    <select class="w-full px-4 py-3 border-2 border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium" 
            onchange="window.location.href='<?= base_url('admin/tambah_data_pos?id_pos=') ?>' + this.value">
        <?php foreach($list_pos as $lp): ?>
            <option value="<?= $lp->id_pos ?>" <?= (isset($pos) && $pos->id_pos == $lp->id_pos) ? 'selected' : '' ?>>
                <?= $lp->nama_pos ?> (Tipe: <?= $lp->tipe_pos ?>)
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-6">
    <?php if(isset($pos)): ?>
        <h3 class="text-xl font-black text-slate-800">Input Laporan - <?= $pos->nama_pos ?></h3>
        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Tipe: <?= $pos->tipe_pos ?></p>
        <input type="hidden" name="id_pos" value="<?= $pos->id_pos ?>">
    <?php else: ?>
        <div class="p-4 bg-amber-50 text-amber-700 rounded-xl text-sm italic">
            Silakan pilih pos terlebih dahulu untuk memulai input data.
        </div>
    <?php endif; ?>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <?= $this->session->flashdata('success') ?>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <?= $this->session->flashdata('error') ?>
    </div>
<?php endif; ?>

<div class="mb-4">
    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal Pengukuran</label>
    <input type="date" 
           name="tanggal" 
           value="<?= isset($tanggal) ? $tanggal : date('Y-m-d') ?>" 
           onchange="window.location.href='<?= base_url('admin/tambah_data_pos?id_pos=' . ($pos->id_pos ?? '') . '&tanggal=') ?>' + this.value" 
           class="px-4 py-2.5 border-2 border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium">
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-5">
    <div class="px-5 py-4 bg-darkblue">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                <?php if($pos->tipe_pos == 'PCH'): ?>
                <svg class="w-5 h-5 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                <?php else: ?>
                <svg class="w-5 h-5 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <?php endif; ?>
            </div>
            <div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Input Data <?= $pos->tipe_pos ?></h3>
                <p class="text-blue-200 text-[10px] mt-0.5">Data pengukuran <?= $pos->tipe_pos == 'PCH' ? 'curah hujan' : 'tinggi muka air' ?> untuk tanggal <?= date('d M Y', strtotime($tanggal)) ?></p>
            </div>
        </div>
    </div>
    
    <form action="<?= base_url('admin/simpan_data_pos') ?>" method="POST" class="p-5">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
        
        <input type="hidden" name="id_pos" value="<?= $pos->id_pos ?>">
        <input type="hidden" name="tanggal_input" value="<?= $tanggal ?>">
        
        <div class="space-y-5">
            <div class="border border-slate-200 rounded-xl p-4">
                <?php if($pos->tipe_pos == 'PCH'): ?>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>
                    Curah Hujan
                </p>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nilai Curah Hujan (mm)</label>
                    <input type="number" step="any" name="curah_hujan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="Masukkan nilai curah hujan dalam mm" required>
                </div>
                <?php else: ?>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-green-500 rounded-full"></span>
                    Tinggi Muka Air
                </p>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nilai TMA (m)</label>
                    <input type="number" step="any" name="tma" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="Masukkan nilai TMA dalam meter" required>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-blue-700">Jam input akan tercatat otomatis: <b class="text-blue-900"><?= date('H:i') ?> WIB</b></p>
            </div>
            
            <button type="submit" class="w-full bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold py-4 rounded-2xl text-sm transition-all shadow-lg shadow-brandyellow/20 active:scale-[0.98] flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Simpan Data
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Data Tersimpan - <?= date('d M Y', strtotime($tanggal)) ?>
        </h3>
        <span class="text-[10px] text-slate-400 font-bold"><?= count($data_list) ?> DATA</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left font-bold w-10">#</th>
                    <th class="px-5 py-3 text-left font-bold">Jam Input</th>
                    <th class="px-5 py-3 text-left font-bold">Nilai Pengukuran (<?= $pos->tipe_pos == 'PCH' ? 'mm' : 'M' ?>)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(!empty($data_list)): $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-semibold text-darkblue"><?= date('H:i', strtotime($d->created_at)) ?> WIB</span>
                        </div>
                    </td>
                    <<td class="px-5 py-3.5">
                        <?php if($pos->tipe_pos == 'PCH'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                                <?= $d->rain !== null ? $d->rain.' mm' : '0 mm' ?>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <?= $d->wlevel !== null ? $d->wlevel.' m' : '-' ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="3" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-slate-400 font-semibold">Belum ada data untuk tanggal ini</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>