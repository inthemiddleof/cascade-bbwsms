<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Laporan Manual</h1>
        <p class="text-slate-500 text-sm mt-1">Pos Aktif: <span class="font-bold text-darkblue"><?= $pos->nama_pos ?> (<?= $pos->tipe_pos ?>)</span></p>
    </div>
    
    <div class="flex-shrink-0">
        <button type="button" onclick="openModalPilihInput()" class="w-full sm:w-auto px-4 py-2.5 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-xs uppercase tracking-wider transition-all shadow-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Data Manual
        </button>
    </div>
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

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col md:flex-row gap-4 items-end md:items-center justify-between">
    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <div class="flex flex-col gap-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Stasiun / Pos:</span>
            <select onchange="window.location='<?= base_url('admin/kelola_manual') ?>?pos='+this.value+'&bulan=<?= $bulan ?>'" class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50 font-medium">
                <?php if(!empty($pos_list)): foreach($pos_list as $pl): ?>
                    <option value="<?= $pl->id_pos ?>" <?= $pl->id_pos == $pos->id_pos ? 'selected' : '' ?>><?= $pl->nama_pos ?> (<?= $pl->tipe_pos ?>)</option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Periode Bulan:</span>
            <input type="month" value="<?= $bulan ?>" onchange="window.location='<?= base_url('admin/kelola_manual') ?>?pos=<?= $pos->id_pos ?>&bulan='+this.value" class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50 font-medium">
        </div>
    </div>
    <span class="text-xs text-slate-400 font-medium bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">Menampilkan <b class="text-slate-700 mx-0.5"><?= count($data_list) ?></b> log records</span>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Laporan Petugas</h3>
        <span class="text-[10px] text-slate-400 font-bold"><?= count($data_list) ?> DATA FOUND</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left font-bold w-10">#</th>
                    <th class="px-5 py-3 text-left font-bold">Tanggal</th>
                    <th class="px-5 py-3 text-left font-bold">Petugas Input</th>
                    <th class="px-5 py-3 text-left font-bold">Jam Kirim</th>
                    <th class="px-5 py-3 text-left font-bold">Nilai Parameter</th>
                    <th class="px-5 py-3 text-center font-bold w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(!empty($data_list)): $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3.5 text-slate-400"><?= $no++ ?></td>
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-darkblue"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                    </td>
                    <td class="px-5 py-3.5 font-medium text-slate-700">
                        <?= !empty($d->nama_petugas) ? $d->nama_petugas : '<span class="text-slate-400 italic">Sistem/Superadmin</span>' ?>
                    </td>
                    <td class="px-5 py-3.5 text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?= date('H:i', strtotime($d->created_at)) ?> WIB
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <?php if($pos->tipe_pos == 'PCH'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-600">
                                CH: <?= $d->rain !== null ? $d->rain.' mm' : '-' ?>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-600">
                                TMA: <?= $d->wlevel !== null ? $d->wlevel.' m' : '-' ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="openModalEdit('<?= $d->id_manual ?>','<?= $d->tanggal_input ?>','<?= $d->rain ?? '' ?>','<?= $d->wlevel ?? '' ?>')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Edit Data Laporan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url('admin/hapus_manual/'.$d->id_manual.'?pos='.$pos->id_pos) ?>" onclick="return confirm('Hapus permanen record data manual ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus Data Laporan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" class="px-5 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <div>
                                <p class="text-slate-400 font-semibold">Belum ada kiriman data dari petugas</p>
                                <p class="text-slate-300 text-[11px] mt-1">Laporan dari petugas lapangan pada stasiun ini akan terakumulasi di sini</p>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Modifikasi Data Manual</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui entri parameter pengukuran stasiun</p>
            </div>
            <button onclick="closeModalEdit()" type="button" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('admin/update_manual', ['class' => 'p-5 space-y-4']) ?>
            <input type="hidden" name="id_manual" id="edit_id">
            <input type="hidden" name="id_pos" value="<?= $pos->id_pos ?>">
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal Pengukuran <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="edit_tanggal" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
            </div>
            
            <?php if($pos->tipe_pos == 'PCH'): ?>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Intensitas Curah Hujan (mm)</label>
                <input type="number" step="any" name="rain" id="edit_rain" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="0.0">
            </div>
            <?php endif; ?>
            
            <?php if($pos->tipe_pos !== 'PCH'): ?>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tinggi Muka Air / TMA (m)</label>
                <input type="number" step="any" name="wlevel" id="edit_wlevel" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="0.00">
            </div>
            <?php endif; ?>
            
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalEdit()" class="flex-1 px-4 py-3 border border-slate-300 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">Simpan Koreksi</button>
            </div>
        <?= form_close() ?>
    </div>
</div>


<div id="modalPilihInput" class="fixed inset-0 bg-black/50 z-[60] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Input Data Darurat</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pilih tipe infrastruktur hidrologi</p>
            </div>
            <button type="button" onclick="closeModalPilihInput()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5 space-y-3">
            <p class="text-xs text-slate-500 leading-relaxed">Gunakan fitur ini jika petugas lapangan berhalangan mengisi laporan berkala. Tentukan kategori form di bawah:</p>
            
            <div class="grid grid-cols-1 gap-2.5">
                <a href="<?= base_url('admin/tambah_data_bendungan') ?>" class="flex items-center gap-4 p-4 border border-slate-200 rounded-xl hover:border-brandyellow hover:bg-yellow-50/20 transition-all group">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-brandyellow group-hover:text-darkblue transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-700">Data Berkala Bendungan</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">TMA, Volume Waduk, Inflow, Outflow, dll.</p>
                    </div>
                </a>

                <a href="<?= base_url('admin/tambah_data_pos') ?>" class="flex items-center gap-4 p-4 border border-slate-200 rounded-xl hover:border-brandyellow hover:bg-yellow-50/20 transition-all group">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center group-hover:bg-brandyellow group-hover:text-darkblue transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-700">Data Pos Manual Biasa</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Curah Hujan (PCH) / Tinggi Muka Air Sungai (PDA)</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>


<script>
// --- JS HANDLING MODAL EDIT (EXISTING) ---
function openModalEdit(id, tanggal, rain, wlevel) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_tanggal').value = tanggal;
    
    let rainInput = document.getElementById('edit_rain');
    let wlevelInput = document.getElementById('edit_wlevel');
    
    if(rainInput) {
        rainInput.value = (rain !== null && rain !== '' && rain !== 'null') ? parseFloat(rain) : '';
    }
    if(wlevelInput) {
        wlevelInput.value = (wlevel !== null && wlevel !== '' && wlevel !== 'null') ? parseFloat(wlevel) : '';
    }
    
    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('modalEdit').classList.add('flex');
}

function closeModalEdit() { 
    document.getElementById('modalEdit').classList.add('hidden'); 
    document.getElementById('modalEdit').classList.remove('flex'); 
}

// --- JS HANDLING MODAL TAMBAH DATA BARU (FITUR BARU) ---
function openModalPilihInput() {
    const modal = document.getElementById('modalPilihInput');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModalPilihInput() {
    const modal = document.getElementById('modalPilihInput');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Global Event Click Outside & Escape handling
window.addEventListener('click', function(e) {
    const modalEdit = document.getElementById('modalEdit');
    const modalPilih = document.getElementById('modalPilihInput');
    
    if (e.target === modalEdit) closeModalEdit();
    if (e.target === modalPilih) closeModalPilihInput();
});

document.addEventListener('keydown', function(e) { 
    if (e.key === 'Escape') {
        closeModalEdit();
        closeModalPilihInput();
    }
});
</script>