<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-darkblue">Kelola Embung</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data embung / waduk kecil</p>
        </div>
        <button onclick="openModal('modalTambahEmbung')" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Embung
        </button>
    </div>

    <!-- Flash Message -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Tabel Embung -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama Embung</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Sungai</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Wilayah Sungai</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">NWL</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Volume NWL</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Luas NWL</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($embung_list) && count($embung_list) > 0): ?>
                        <?php $no = 1; foreach ($embung_list as $e): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-all">
                                <td class="px-4 py-3 text-slate-500"><?= $no++ ?></td>
                                <td class="px-4 py-3 font-medium text-darkblue"><?= htmlspecialchars($e->nama_pos) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($e->sungai ?? '-') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($e->wilayah_sungai ?? '-') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= number_format($e->nwl ?? 0, 2) ?> m</td>
                                <td class="px-4 py-3 text-slate-600"><?= number_format($e->nwl_volume ?? 0, 0) ?> m³</td>
                                <td class="px-4 py-3 text-slate-600"><?= number_format($e->nwl_luas ?? 0, 2) ?> Ha</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditEmbung(<?= htmlspecialchars(json_encode($e)) ?>)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button onclick="confirmDeleteEmbung(<?= $e->id_pos ?>, '<?= htmlspecialchars($e->nama_pos, ENT_QUOTES) ?>')" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                <p class="font-medium">Belum ada data embung</p>
                                <p class="text-xs mt-1">Klik "Tambah Embung" untuk menambahkan</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH EMBUNG -->
<!-- ========================================== -->
<div id="modalTambahEmbung" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalTambahEmbung')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Embung</h3>
            <button onclick="closeModal('modalTambahEmbung')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tambah_embung') ?>" method="POST" class="px-6 py-4 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Embung <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nomor Pos</label>
                    <input type="text" name="nomor_pos" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sungai <span class="text-red-500">*</span></label>
                    <input type="text" name="sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Wilayah Sungai <span class="text-red-500">*</span></label>
                    <select name="wilayah_sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="">Pilih Wilayah Sungai</option>
                        <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                        <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                        <option value="SEMANGKA">SEMANGKA</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Latitude <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="lat" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="-5.3971">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Longitude <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="lng" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="105.2668">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Device ID Telemetry</label>
                <input type="text" name="device_id_telemetry" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
            </div>
            <div class="border-t border-slate-200 pt-4">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Data Teknis</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">NWL (m)</label>
                        <input type="number" step="any" name="nwl" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Volume NWL (m³)</label>
                        <input type="number" step="any" name="nwl_volume" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas NWL (Ha)</label>
                        <input type="number" step="any" name="nwl_luas" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalTambahEmbung')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT EMBUNG -->
<!-- ========================================== -->
<div id="modalEditEmbung" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalEditEmbung')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Edit Embung</h3>
            <button onclick="closeModal('modalEditEmbung')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_embung') ?>" method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="id_pos" id="edit_id_pos">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Embung <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" id="edit_nama_pos" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nomor Pos</label>
                    <input type="text" name="nomor_pos" id="edit_nomor_pos" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sungai <span class="text-red-500">*</span></label>
                    <input type="text" name="sungai" id="edit_sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Wilayah Sungai <span class="text-red-500">*</span></label>
                    <select name="wilayah_sungai" id="edit_wilayah_sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                        <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                        <option value="SEMANGKA">SEMANGKA</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Latitude <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="lat" id="edit_lat" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Longitude <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="lng" id="edit_lng" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Device ID Telemetry</label>
                <input type="text" name="device_id_telemetry" id="edit_device_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
            </div>
            <div class="border-t border-slate-200 pt-4">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Data Teknis</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">NWL (m)</label>
                        <input type="number" step="any" name="nwl" id="edit_nwl" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Volume NWL (m³)</label>
                        <input type="number" step="any" name="nwl_volume" id="edit_nwl_volume" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas NWL (Ha)</label>
                        <input type="number" step="any" name="nwl_luas" id="edit_nwl_luas" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalEditEmbung')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL KONFIRMASI HAPUS -->
<!-- ========================================== -->
<div id="modalHapusEmbung" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalHapusEmbung')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-center text-darkblue mb-2">Hapus Embung?</h3>
            <p class="text-center text-slate-500 text-sm mb-6">Apakah Anda yakin ingin menghapus embung <strong id="hapus_nama_embung" class="text-darkblue"></strong>? Data yang telah dihapus tidak dapat dikembalikan.</p>
            <div class="flex justify-center gap-3">
                <button onclick="closeModal('modalHapusEmbung')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <a href="#" id="hapus_link_embung" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-sm transition-all">Hapus</a>
            </div>
        </div>
    </div>
</div>

<style>
    #modalTambahEmbung, #modalEditEmbung, #modalHapusEmbung {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    #modalTambahEmbung.show, #modalEditEmbung.show, #modalHapusEmbung.show {
        opacity: 1 !important;
        visibility: visible !important;
    }
    #modalTambahEmbung .bg-white, #modalEditEmbung .bg-white, #modalHapusEmbung .bg-white {
        transition: transform 0.3s ease;
    }
    #modalTambahEmbung.show .bg-white, #modalEditEmbung.show .bg-white, #modalHapusEmbung.show .bg-white {
        transform: scale(1) !important;
    }
</style>

<script>
// ==========================================
// MODAL FUNCTIONS
// ==========================================
function openModal(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}

// ==========================================
// EDIT EMBUNG
// ==========================================
function openEditEmbung(data) {
    document.getElementById('edit_id_pos').value = data.id_pos;
    document.getElementById('edit_nama_pos').value = data.nama_pos || '';
    document.getElementById('edit_nomor_pos').value = data.nomor_pos || '';
    document.getElementById('edit_sungai').value = data.sungai || '';
    document.getElementById('edit_wilayah_sungai').value = data.wilayah_sungai || '';
    document.getElementById('edit_lat').value = data.lat || '';
    document.getElementById('edit_lng').value = data.lng || '';
    document.getElementById('edit_device_id').value = data.device_id_telemetry || '';
    document.getElementById('edit_nwl').value = data.nwl || '';
    document.getElementById('edit_nwl_volume').value = data.nwl_volume || '';
    document.getElementById('edit_nwl_luas').value = data.nwl_luas || '';
    openModal('modalEditEmbung');
}

// ==========================================
// HAPUS EMBUNG
// ==========================================
function confirmDeleteEmbung(id, nama) {
    document.getElementById('hapus_nama_embung').textContent = nama;
    document.getElementById('hapus_link_embung').href = '<?= base_url('superadmin/hapus_embung/') ?>' + id;
    openModal('modalHapusEmbung');
}

// Tutup modal dengan ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.show').forEach(function(el) {
            el.classList.remove('show');
        });
        document.body.style.overflow = '';
    }
});
</script>