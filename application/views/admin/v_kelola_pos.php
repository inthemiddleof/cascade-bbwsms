<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Pos</h1>
        <p class="text-slate-500 text-sm mt-1">Manajemen data pos monitoring hidrologi</p>
    </div>
    <button onclick="openModalTambah()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pos
    </button>
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

<!-- Filter & Search -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col sm:flex-row gap-3">
    <div class="relative flex-1">
        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="searchPos" placeholder="Cari nama pos, nomor pos, sungai, atau device ID..." class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50">
    </div>
    <div class="flex gap-2">
        <select id="filterTipe" class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-slate-50 font-medium text-slate-600">
            <option value="all">Semua Tipe</option>
            <option value="PCH">PCH (Curah Hujan)</option>
            <option value="PDA">PDA (Tinggi Muka Air)</option>
        </select>
        <select id="filterPetugas" class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-slate-50 font-medium text-slate-600">
            <option value="all">Semua Status</option>
            <option value="ada">Ada Petugas</option>
            <option value="tidak">Belum Ada Petugas</option>
        </select>
    </div>
</div>

<!-- Tabel Data -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Pos Monitoring</h3>
        <span class="text-[10px] text-slate-400 font-bold" id="totalCounter"><?= count($pos_list) ?> POS</span>
    </div>
    
    <div class="overflow-x-auto max-h-[550px] overflow-y-auto">
        <table class="w-full text-xs" id="posTable">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider sticky top-0 z-10">
                <tr>
                    <th class="px-5 py-3 text-left font-bold w-10">#</th>
                    <th class="px-5 py-3 text-left font-bold">Nomor / Nama Pos</th>
                    <th class="px-5 py-3 text-center font-bold w-20">Tipe</th>
                    <th class="px-5 py-3 text-left font-bold">Sungai</th>
                    <th class="px-5 py-3 text-left font-bold">Koordinat</th>
                    <th class="px-5 py-3 text-left font-bold">Device ID</th>
                    <th class="px-5 py-3 text-left font-bold">Petugas</th>
                    <th class="px-5 py-3 text-center font-bold w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(!empty($pos_list)): $no = 1; foreach($pos_list as $pos): ?>
                <tr class="hover:bg-slate-50/50 transition-colors pos-row" 
                    data-tipe="<?= $pos->tipe_pos ?>" 
                    data-petugas="<?= !empty($pos->petugas_nama) ? 'ada' : 'tidak' ?>">
                    <td class="px-5 py-3.5 text-slate-400"><?= $no++ ?></td>
                    
                    <td class="px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="font-semibold text-darkblue"><?= $pos->nama_pos ?></p>
                            <p class="text-[10px] text-slate-400 font-mono"><?= $pos->nomor_pos ?: 'Tanpa Nomor' ?></p>
                        </div>
                    </td>
                    
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold <?= ($pos->tipe_pos == 'PCH') ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= ($pos->tipe_pos == 'PCH') ? 'bg-blue-500' : 'bg-green-500' ?>"></span>
                            <?= $pos->tipe_pos ?>
                        </span>
                    </td>
                    
                    <td class="px-5 py-3.5 text-slate-500">
                        <?= $pos->sungai ?: '<span class="text-slate-300 italic">-</span>' ?>
                    </td>
                    
                    <td class="px-5 py-3.5">
                        <div class="font-mono text-[10px] text-slate-500">
                            <p><?= number_format($pos->lat, 6) ?></p>
                            <p><?= number_format($pos->lng, 6) ?></p>
                        </div>
                    </td>
                    
                    <td class="px-5 py-3.5">
                        <?php if($pos->device_id_telemetry): ?>
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-600 font-mono"><?= $pos->device_id_telemetry ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 italic text-[10px]">-</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="px-5 py-3.5">
                        <?php if(!empty($pos->petugas_nama)): ?>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-brandyellow/20 flex items-center justify-center flex-shrink-0">
                                    <span class="text-darkblue font-bold text-[9px]"><?= strtoupper(substr($pos->petugas_nama, 0, 2)) ?></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-darkblue text-[11px] truncate"><?= $pos->petugas_nama ?></p>
                                    <p class="text-[9px] text-slate-400">@<?= $pos->petugas_username ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-orange-500 text-[10px] font-bold bg-orange-50 px-2 py-1 rounded-full">Butuh Petugas</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="openModalEdit(
                                '<?= $pos->id_pos ?>',
                                '<?= addslashes($pos->nomor_pos ?? '') ?>',
                                '<?= addslashes($pos->nama_pos) ?>',
                                '<?= $pos->tipe_pos ?>',
                                '<?= addslashes($pos->sungai ?? '') ?>',
                                '<?= $pos->lat ?>',
                                '<?= $pos->lng ?>',
                                '<?= addslashes($pos->device_id_telemetry ?? '') ?>'
                            )" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Edit Pos">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url('admin/hapus_pos/'.$pos->id_pos) ?>" onclick="return confirm('HAPUS pos ini?\n\nPastikan pos tidak memiliki data telemetri. Tindakan ini tidak dapat dibatalkan.')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus Pos">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="8" class="px-5 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-slate-400 font-semibold">Belum ada data pos</p>
                                <p class="text-slate-300 text-[11px] mt-1">Klik tombol "Tambah Pos" untuk memulai</p>
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
<!-- MODAL TAMBAH POS -->
<!-- ============================================ -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Tambah Pos Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Lengkapi data pos monitoring</p>
            </div>
            <button onclick="closeModalTambah()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('admin/tambah_pos', ['class' => 'p-5 space-y-4']) ?>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Pos <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_pos" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="Contoh: PDA.001" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Pos <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="Contoh: Sumur Putri" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tipe Pos <span class="text-red-500">*</span></label>
                    <select name="tipe_pos" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
                        <option value="">-- Pilih --</option>
                        <option value="PCH">PCH (Curah Hujan)</option>
                        <option value="PDA">PDA (Tinggi Muka Air)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Sungai</label>
                    <input type="text" name="sungai" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="Nama sungai">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Latitude <span class="text-red-500">*</span></label>
                    <input type="text" name="lat" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="Contoh: -5.438720" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Longitude <span class="text-red-500">*</span></label>
                    <input type="text" name="lng" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="Contoh: 105.245680" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Device ID Telemetri</label>
                <input type="text" name="device_id_telemetry" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="ID perangkat dari API telemetri">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalTambah()" class="flex-1 px-4 py-3 border border-slate-300 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">Simpan Pos</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT POS -->
<!-- ============================================ -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Edit Data Pos</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi pos monitoring</p>
            </div>
            <button onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('admin/edit_pos', ['class' => 'p-5 space-y-4']) ?>
            <input type="hidden" name="id_pos" id="edit_id_pos">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Pos <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_pos" id="edit_nomor_pos" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Pos <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" id="edit_nama_pos" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tipe Pos <span class="text-red-500">*</span></label>
                    <select name="tipe_pos" id="edit_tipe_pos" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
                        <option value="PCH">PCH (Curah Hujan)</option>
                        <option value="PDA">PDA (Tinggi Muka Air)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Sungai</label>
                    <input type="text" name="sungai" id="edit_sungai" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Latitude <span class="text-red-500">*</span></label>
                    <input type="text" name="lat" id="edit_lat" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Longitude <span class="text-red-500">*</span></label>
                    <input type="text" name="lng" id="edit_lng" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Device ID Telemetri</label>
                <input type="text" name="device_id_telemetry" id="edit_device" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalEdit()" class="flex-1 px-4 py-3 border border-slate-300 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">Simpan Perubahan</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script>
// Modal Functions
function openModalTambah() { document.getElementById('modalTambah').classList.remove('hidden'); document.getElementById('modalTambah').classList.add('flex'); }
function closeModalTambah() { document.getElementById('modalTambah').classList.add('hidden'); document.getElementById('modalTambah').classList.remove('flex'); }

function openModalEdit(id, nomor, nama, tipe, sungai, lat, lng, device) {
    document.getElementById('edit_id_pos').value = id;
    document.getElementById('edit_nomor_pos').value = nomor;
    document.getElementById('edit_nama_pos').value = nama;
    document.getElementById('edit_tipe_pos').value = tipe;
    document.getElementById('edit_sungai').value = sungai;
    document.getElementById('edit_lat').value = lat;
    document.getElementById('edit_lng').value = lng;
    document.getElementById('edit_device').value = device;
    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('modalEdit').classList.add('flex');
}
function closeModalEdit() { document.getElementById('modalEdit').classList.add('hidden'); document.getElementById('modalEdit').classList.remove('flex'); }

// Close modal on overlay click
document.getElementById('modalTambah').addEventListener('click', function(e) { if (e.target === this) closeModalTambah(); });
document.getElementById('modalEdit').addEventListener('click', function(e) { if (e.target === this) closeModalEdit(); });

// Close on ESC
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeModalTambah(); closeModalEdit(); } });

// Search & Filter
document.getElementById('searchPos').addEventListener('input', applyFilters);
document.getElementById('filterTipe').addEventListener('change', applyFilters);
document.getElementById('filterPetugas').addEventListener('change', applyFilters);

function applyFilters() {
    const query = document.getElementById('searchPos').value.toLowerCase();
    const tipeFilter = document.getElementById('filterTipe').value;
    const petugasFilter = document.getElementById('filterPetugas').value;
    
    const rows = document.querySelectorAll('.pos-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const tipe = row.dataset.tipe;
        const petugas = row.dataset.petugas;
        
        const matchSearch = text.includes(query);
        const matchTipe = tipeFilter === 'all' || tipe === tipeFilter;
        const matchPetugas = petugasFilter === 'all' || petugas === petugasFilter;
        
        if (matchSearch && matchTipe && matchPetugas) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('totalCounter').textContent = visibleCount + ' POS';
}
</script>