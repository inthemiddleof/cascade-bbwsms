<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Laporan</h1>
        <p class="text-slate-500 text-sm mt-1">Pos: <span class="font-bold text-darkblue"><?= $pos->nama_pos ?> (<?= $pos->tipe_pos ?>)</span></p>
    </div>
</div>

<!-- Alert Messages -->
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

<!-- Filter Bulan -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col sm:flex-row gap-3">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
        <span class="text-sm font-bold text-slate-600 uppercase tracking-wider">Bulan:</span>
    </div>
    <input type="month" value="<?= $bulan ?>" onchange="window.location='<?= base_url('petugas/kelola') ?>?bulan='+this.value" class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50 font-medium">
    <span class="text-[10px] text-slate-400 flex items-center">Menampilkan <b class="mx-1"><?= count($data_list) ?></b> data</span>
</div>

<!-- Tabel Data -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Laporan</h3>
        <span class="text-[10px] text-slate-400 font-bold"><?= count($data_list) ?> DATA</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left font-bold w-10">#</th>
                    <th class="px-5 py-3 text-left font-bold">Tanggal</th>
                    <th class="px-5 py-3 text-left font-bold">Jam Input</th>
                    <th class="px-5 py-3 text-left font-bold">NilaI TMA (M)</th>
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
                    <td class="px-5 py-3.5 text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?= date('H:i', strtotime($d->created_at)) ?> WIB
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <?php if($pos->tipe_pos == 'PCH'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-600">
                                <?= $d->rain !== null ? $d->rain.' mm' : '-' ?>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-600">
                                <?= $d->wlevel !== null ? $d->wlevel.' m' : '-' ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="openModalEdit('<?= $d->id_manual ?>','<?= $d->tanggal_input ?>','<?= $d->rain ?>','<?= $d->wlevel ?>')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url('petugas/hapus/'.$d->id_manual) ?>" onclick="return confirm('Hapus data ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" class="px-5 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <div>
                                <p class="text-slate-400 font-semibold">Belum ada data untuk bulan ini</p>
                                <p class="text-slate-300 text-[11px] mt-1">Gunakan menu Input Laporan untuk menambahkan data</p>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT -->
<!-- ============================================ -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Edit Laporan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui data laporan</p>
            </div>
            <button onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('petugas/update', ['class' => 'p-5 space-y-4']) ?>
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_manual" id="edit_id">
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="edit_tanggal" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
            </div>
            
            <?php if($pos->tipe_pos == 'PCH'): ?>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Curah Hujan (mm)</label>
                <input type="number" step="any" name="rain" id="edit_rain" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="0.0">
            </div>
            <?php endif; ?>
            
            <?php if($pos->tipe_pos == 'PDA'): ?>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tinggi Muka Air (m)</label>
                <input type="number" step="any" name="wlevel" id="edit_wlevel" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="0.00">
            </div>
            <?php endif; ?>
            
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalEdit()" class="flex-1 px-4 py-3 border border-slate-300 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">Simpan Perubahan</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script>
function openModalEdit(id, tanggal, rain, wlevel) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_tanggal').value = tanggal;
    document.getElementById('edit_rain').value = (rain !== null && rain !== '' && rain !== 'null') ? parseFloat(rain) : '';
    document.getElementById('edit_wlevel').value = (wlevel !== null && wlevel !== '' && wlevel !== 'null') ? parseFloat(wlevel) : '';
    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('modalEdit').classList.add('flex');
}
function closeModalEdit() { document.getElementById('modalEdit').classList.add('hidden'); document.getElementById('modalEdit').classList.remove('flex'); }
document.getElementById('modalEdit').addEventListener('click', function(e) { if (e.target === this) closeModalEdit(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModalEdit(); });
</script>