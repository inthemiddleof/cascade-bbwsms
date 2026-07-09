<!-- VIEW: superadmin/v_kelola_embung.php -->
<!-- Style konsisten dengan halaman kelola pos -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Kelola Embung</h1>
        <p class="text-slate-500 text-sm mt-1">Manajemen data embung / waduk kecil di wilayah BBWS Mesuji Sekampung</p>
    </div>
    <button type="button" onclick="openModalTambah()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Embung
    </button>
</div>

<!-- Alert Messages -->
<?php if($this->session->flashdata('success')): ?>
<div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2" id="alert-success">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <?= $this->session->flashdata('success') ?>
</div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2" id="alert-error">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <?= $this->session->flashdata('error') ?>
</div>
<?php endif; ?>

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col sm:flex-row gap-3">
    <div class="relative flex-1">
        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" id="searchEmbung" placeholder="Cari nama embung..." class="w-full pl-10 pr-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
    </div>
    <select id="filterWilayah" class="px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium text-slate-600">
        <option value="all">Semua Wilayah</option>
        <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
        <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
        <option value="SEMANGKA">SEMANGKA</option>
    </select>
</div>

<!-- Tabel -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Embung</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full" id="totalCounter"><?= count($embung_list) ?> Embung</span>
    </div>
    
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-sm min-w-[900px] md:min-w-[1100px]" id="embungTable">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-3 text-left font-bold w-8 md:w-10">#</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold">Nama Embung</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold hidden md:table-cell">Sungai</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold hidden lg:table-cell">Wilayah</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-24 hidden sm:table-cell">Kapasitas (m³)</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden lg:table-cell">Elevasi (m)</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden xl:table-cell">Tinggi (m)</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden xl:table-cell">Panjang (m)</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden sm:table-cell">Tahun</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 md:w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($embung_list) && count($embung_list) > 0): $no = 1; foreach($embung_list as $e): ?>
                <tr class="hover:bg-slate-50 transition-colors embung-row" data-wilayah="<?= $e->wilayah_sungai ?>">
                    <td class="px-2 md:px-3 py-3 text-slate-400 text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-3">
                        <p class="font-semibold text-darkblue text-xs"><?= htmlspecialchars($e->nama_pos) ?></p>
                        <p class="text-[10px] text-slate-400"><?= $e->nomor_pos ?: 'Tanpa Nomor' ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-slate-500 text-xs hidden md:table-cell">
                        <?= !empty($e->sungai) ? htmlspecialchars($e->sungai) : '<span class="text-slate-300">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-slate-500 text-xs hidden lg:table-cell">
                        <?= !empty($e->wilayah_sungai) ? htmlspecialchars($e->wilayah_sungai) : '<span class="text-slate-300">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden sm:table-cell">
                        <?php if($e->kapasitas_volume): ?>
                            <span class="font-medium text-xs text-slate-600"><?= number_format($e->kapasitas_volume, 0, ',', '.') ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden lg:table-cell">
                        <?php if($e->elevasi_puncak): ?>
                            <span class="font-mono text-xs text-slate-600"><?= number_format($e->elevasi_puncak, 2) ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden xl:table-cell">
                        <?php if($e->tinggi_embung): ?>
                            <span class="font-mono text-xs text-slate-600"><?= number_format($e->tinggi_embung, 2) ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden xl:table-cell">
                        <?php if($e->panjang_tubuh): ?>
                            <span class="font-mono text-xs text-slate-600"><?= number_format($e->panjang_tubuh, 2) ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden sm:table-cell">
                        <?php if($e->tahun_mulai_pembangunan): ?>
                            <span class="text-xs text-slate-600"><?= $e->tahun_mulai_pembangunan ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEdit(<?= htmlspecialchars(json_encode($e)) ?>)" class="p-1.5 md:p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="confirmDeleteEmbung(<?= $e->id_pos ?>, '<?= htmlspecialchars($e->nama_pos, ENT_QUOTES) ?>')" class="p-1.5 md:p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="10" class="px-5 py-16 text-center text-slate-400">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10a4 4 0 014-4h10a4 4 0 014 4v8a4 4 0 01-4 4H7a4 4 0 01-4-4v-8z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 6v2m6-2v2M9 14h6m-6 4h6"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium">Belum ada embung terdaftar</p>
                            <p class="text-xs text-slate-400">Klik "Tambah Embung" untuk menambahkan data</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TAMBAH EMBUNG -->
<!-- ============================================ -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Tambah Embung</h3>
            <button type="button" onclick="closeModalTambah()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tambah_embung') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            
            <!-- Field Dasar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Embung <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" id="tambah_nama_pos" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Nama embung" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nomor Pos</label>
                    <input type="text" name="nomor_pos" id="tambah_nomor_pos" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Contoh: EMB.001">
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sungai <span class="text-red-500">*</span></label>
                    <input type="text" name="sungai" id="tambah_sungai" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Nama sungai" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Wilayah Sungai <span class="text-red-500">*</span></label>
                    <select name="wilayah_sungai" id="tambah_wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                        <option value="">-- Pilih Wilayah --</option>
                        <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                        <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                        <option value="SEMANGKA">SEMANGKA</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="lat" id="tambah_lat" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="-5.3971" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="lng" id="tambah_lng" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="105.2668" required>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Device ID Telemetry</label>
                <input type="text" name="device_id_telemetry" id="tambah_device_id" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="ID telemetri (opsional)">
                <p class="text-[9px] text-slate-400 mt-1">Khusus untuk embung dengan telemetri</p>
            </div>
            
            <!-- Data Teknis Embung -->
            <div class="border border-amber-200 rounded-xl p-4 bg-amber-50/30">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>Data Teknis Embung
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kapasitas Volume (m³)</label>
                        <input type="number" step="any" name="kapasitas_volume" id="tambah_kapasitas_volume" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Puncak (m)</label>
                        <input type="number" step="any" name="elevasi_puncak" id="tambah_elevasi_puncak" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tinggi Embung (m)</label>
                        <input type="number" step="any" name="tinggi_embung" id="tambah_tinggi_embung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Panjang Tubuh (m)</label>
                        <input type="number" step="any" name="panjang_tubuh" id="tambah_panjang_tubuh" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Mulai Pembangunan</label>
                        <input type="number" name="tahun_mulai_pembangunan" id="tambah_tahun_mulai" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="2020">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalTambah()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT EMBUNG -->
<!-- ============================================ -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Edit Embung</h3>
            <button type="button" onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_embung') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_pos" id="edit_id_pos">
            
            <!-- Field Dasar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Embung <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" id="edit_nama_pos" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nomor Pos</label>
                    <input type="text" name="nomor_pos" id="edit_nomor_pos" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sungai <span class="text-red-500">*</span></label>
                    <input type="text" name="sungai" id="edit_sungai" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Wilayah Sungai <span class="text-red-500">*</span></label>
                    <select name="wilayah_sungai" id="edit_wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                        <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                        <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                        <option value="SEMANGKA">SEMANGKA</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="lat" id="edit_lat" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="lng" id="edit_lng" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Device ID Telemetry</label>
                <input type="text" name="device_id_telemetry" id="edit_device_id" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="ID telemetri (opsional)">
                <p class="text-[9px] text-slate-400 mt-1">Khusus untuk embung dengan telemetri</p>
            </div>
            
            <!-- Data Teknis Embung -->
            <div class="border border-amber-200 rounded-xl p-4 bg-amber-50/30">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>Data Teknis Embung
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kapasitas Volume (m³)</label>
                        <input type="number" step="any" name="kapasitas_volume" id="edit_kapasitas_volume" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Puncak (m)</label>
                        <input type="number" step="any" name="elevasi_puncak" id="edit_elevasi_puncak" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tinggi Embung (m)</label>
                        <input type="number" step="any" name="tinggi_embung" id="edit_tinggi_embung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Panjang Tubuh (m)</label>
                        <input type="number" step="any" name="panjang_tubuh" id="edit_panjang_tubuh" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Mulai Pembangunan</label>
                        <input type="number" name="tahun_mulai_pembangunan" id="edit_tahun_mulai" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="2020">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalEdit()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL KONFIRMASI HAPUS -->
<!-- ============================================ -->
<div id="modalHapus" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-darkblue mb-2">Hapus Embung?</h3>
            <p class="text-slate-500 text-sm mb-6">Apakah Anda yakin ingin menghapus embung <strong id="hapus_nama_embung" class="text-darkblue"></strong>? Data yang telah dihapus tidak dapat dikembalikan.</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeModalHapus()" class="px-6 py-2.5 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <a href="#" id="hapus_link_embung" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition-all shadow-md">Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function(){
        var s = document.getElementById('alert-success');
        var e = document.getElementById('alert-error');
        if(s) s.style.display = 'none';
        if(e) e.style.display = 'none';
    }, 5000);

    // ==========================================
    // FUNGSI MODAL TAMBAH
    // ==========================================
    function openModalTambah(){
        document.getElementById('modalTambah').style.display = 'flex';
        // Reset form
        document.getElementById('tambah_nama_pos').value = '';
        document.getElementById('tambah_nomor_pos').value = '';
        document.getElementById('tambah_sungai').value = '';
        document.getElementById('tambah_wilayah_sungai').value = '';
        document.getElementById('tambah_lat').value = '';
        document.getElementById('tambah_lng').value = '';
        document.getElementById('tambah_device_id').value = '';
        document.getElementById('tambah_kapasitas_volume').value = '';
        document.getElementById('tambah_elevasi_puncak').value = '';
        document.getElementById('tambah_tinggi_embung').value = '';
        document.getElementById('tambah_panjang_tubuh').value = '';
        document.getElementById('tambah_tahun_mulai').value = '';
    }
    
    function closeModalTambah(){
        document.getElementById('modalTambah').style.display = 'none';
    }

    // ==========================================
    // FUNGSI MODAL EDIT
    // ==========================================
    function openModalEdit(data){
        document.getElementById('edit_id_pos').value = data.id_pos;
        document.getElementById('edit_nama_pos').value = data.nama_pos || '';
        document.getElementById('edit_nomor_pos').value = data.nomor_pos || '';
        document.getElementById('edit_sungai').value = data.sungai || '';
        document.getElementById('edit_wilayah_sungai').value = data.wilayah_sungai || '';
        document.getElementById('edit_lat').value = data.lat || '';
        document.getElementById('edit_lng').value = data.lng || '';
        document.getElementById('edit_device_id').value = data.device_id_telemetry || '';
        
        // Data teknis embung
        document.getElementById('edit_kapasitas_volume').value = data.kapasitas_volume || '';
        document.getElementById('edit_elevasi_puncak').value = data.elevasi_puncak || '';
        document.getElementById('edit_tinggi_embung').value = data.tinggi_embung || '';
        document.getElementById('edit_panjang_tubuh').value = data.panjang_tubuh || '';
        document.getElementById('edit_tahun_mulai').value = data.tahun_mulai_pembangunan || '';
        
        document.getElementById('modalEdit').style.display = 'flex';
    }
    
    function closeModalEdit(){
        document.getElementById('modalEdit').style.display = 'none';
    }

    // ==========================================
    // FUNGSI MODAL HAPUS
    // ==========================================
    function confirmDeleteEmbung(id, nama) {
        document.getElementById('hapus_nama_embung').textContent = nama;
        document.getElementById('hapus_link_embung').href = '<?= base_url('superadmin/hapus_embung/') ?>' + id;
        document.getElementById('modalHapus').style.display = 'flex';
    }
    
    function closeModalHapus(){
        document.getElementById('modalHapus').style.display = 'none';
    }

    // ==========================================
    // CLOSE MODAL (klik di luar modal)
    // ==========================================
    document.getElementById('modalTambah').addEventListener('click', function(e){
        if(e.target === this) closeModalTambah();
    });
    
    document.getElementById('modalEdit').addEventListener('click', function(e){
        if(e.target === this) closeModalEdit();
    });
    
    document.getElementById('modalHapus').addEventListener('click', function(e){
        if(e.target === this) closeModalHapus();
    });
    
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            closeModalTambah();
            closeModalEdit();
            closeModalHapus();
        }
    });

    // ==========================================
    // FILTER TABLE
    // ==========================================
    document.getElementById('searchEmbung').addEventListener('input', applyFilters);
    document.getElementById('filterWilayah').addEventListener('change', applyFilters);
    
    function applyFilters(){
        var q = document.getElementById('searchEmbung').value.toLowerCase();
        var w = document.getElementById('filterWilayah').value;
        var rows = document.querySelectorAll('.embung-row');
        var c = 0;
        
        rows.forEach(function(r){
            var text = r.textContent.toLowerCase();
            var wilayah = r.getAttribute('data-wilayah');
            var show = true;
            
            if(q && text.indexOf(q) === -1) show = false;
            if(w !== 'all' && wilayah !== w) show = false;
            
            if(show){
                r.style.display = '';
                c++;
            } else {
                r.style.display = 'none';
            }
        });
        
        document.getElementById('totalCounter').textContent = c + ' Embung';
    }
</script>

<!-- CSS Tambahan untuk modal -->
<style>
    /* Style untuk modal agar konsisten */
    #modalTambah, #modalEdit, #modalHapus {
        transition: opacity 0.2s ease;
    }
    
    #modalTambah .bg-white, 
    #modalEdit .bg-white, 
    #modalHapus .bg-white {
        animation: modalSlideIn 0.2s ease-out;
    }
    
    @keyframes modalSlideIn {
        from {
            transform: scale(0.95);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .bg-darkblue {
        background-color: #1a2a6c;
    }
    .hover\:bg-blue-900:hover {
        background-color: #0f1a4a;
    }
    .shadow-darkblue\/10 {
        box-shadow: 0 4px 6px -1px rgba(26, 42, 108, 0.1);
    }
</style>