<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Kelola Master Pos</h1>
        <p class="text-slate-500 text-sm mt-1">Manajemen data pos monitoring hidrologi</p>
    </div>
    <button type="button" onclick="openModalTambah()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pos
    </button>
</div>

<!-- Alert Messages -->
<?php if($this->session->flashdata('success')): ?>
<div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2" id="alert-success">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <?= $this->session->flashdata('success') ?>
</div>
<?php endif; ?>
<?php if($this->session->flashdata('error')): ?>
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2" id="alert-error">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <?= $this->session->flashdata('error') ?>
</div>
<?php endif; ?>

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col sm:flex-row gap-3">
    <div class="relative flex-1">
        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="searchPos" placeholder="Cari nama pos..." class="w-full pl-10 pr-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
    </div>
    <select id="filterTipe" class="px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium text-slate-600">
        <option value="all">Semua Tipe</option>
        <option value="PCH">PCH (Curah Hujan)</option>
        <option value="PDA">PDA (TMA)</option>
        <option value="bendungan">Bendungan</option>
        <option value="bendung">Bendung</option>
    </select>
</div>

<!-- Tabel -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Pos Monitoring</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full" id="totalCounter"><?= count($pos_list) ?> Pos</span>
    </div>
    
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-sm min-w-[700px] md:min-w-[900px]" id="posTable">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-3 text-left font-bold w-8 md:w-10">#</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold">Nama Pos</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-16 md:w-20">Tipe</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold hidden md:table-cell">Sungai</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-24 hidden sm:table-cell">Latitude</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-24 hidden sm:table-cell">Longitude</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold hidden lg:table-cell">Device ID</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-24 hidden lg:table-cell">Jenis</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 md:w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($pos_list)): $no = 1; foreach($pos_list as $pos): ?>
                <?php 
                    $jenis_label = 'Pos Biasa';
                    $jenis_badge_class = 'bg-slate-100 text-slate-600';
                    if($pos->is_bendung == 1) {
                        $jenis_label = 'Bendung';
                        $jenis_badge_class = 'bg-cyan-50 text-cyan-600';
                    } elseif($pos->is_bendungan == 1) {
                        $jenis_label = 'Bendungan';
                        $jenis_badge_class = 'bg-red-50 text-red-600';
                    }
                ?>
                <tr class="hover:bg-slate-50 transition-colors pos-row" data-tipe="<?= $pos->tipe_pos ?>" data-is-bendungan="<?= $pos->is_bendungan ?>" data-is-bendung="<?= $pos->is_bendung ?>">
                    <td class="px-2 md:px-3 py-3 text-slate-400 text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-3">
                        <p class="font-semibold text-darkblue text-xs"><?= htmlspecialchars($pos->nama_pos) ?></p>
                        <p class="text-[10px] text-slate-400"><?= $pos->nomor_pos ?: 'Tanpa Nomor' ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold <?= ($pos->tipe_pos == 'PCH') ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' ?>"><?= $pos->tipe_pos ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-slate-500 text-xs hidden md:table-cell">
                        <?= !empty($pos->sungai) ? htmlspecialchars($pos->sungai) : '<span class="text-slate-300">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden sm:table-cell">
                        <?php if($pos->lat): ?>
                            <span class="font-mono text-[10px] text-slate-600"><?= number_format($pos->lat, 6) ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden sm:table-cell">
                        <?php if($pos->lng): ?>
                            <span class="font-mono text-[10px] text-slate-600"><?= number_format($pos->lng, 6) ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 hidden lg:table-cell">
                        <?php if(!empty($pos->device_id_telemetry)): ?>
                            <span class="text-[10px] font-mono text-purple-600 bg-purple-50 px-2 py-0.5 rounded-lg"><?= htmlspecialchars($pos->device_id_telemetry) ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden lg:table-cell">
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold <?= $jenis_badge_class ?>"><?= $jenis_label ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEdit(<?= $pos->id_pos ?>)" class="p-1.5 md:p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url('superadmin/hapus_pos/'.$pos->id_pos) ?>" onclick="return confirm('Hapus pos ini?')" class="p-1.5 md:p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="9" class="px-5 py-16 text-center text-slate-400">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <p class="text-sm font-medium">Belum ada pos terdaftar</p>
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
<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Tambah Pos</h3>
            <button type="button" onclick="closeModalTambah()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tambah_pos') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            
            <!-- Field Dasar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Pos <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" id="tambah_nama_pos" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Nama pos" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nomor Pos</label>
                    <input type="text" name="nomor_pos" id="tambah_nomor_pos" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Contoh: PDA.001">
                </div>
            </div>
            
            <!-- Tipe Pos dengan Jenis -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tipe Pos <span class="text-red-500">*</span></label>
                    <select name="tipe_pos" id="tambah_tipe_pos" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required onchange="ubahFormTambah()">
                        <option value="PCH">PCH (Curah Hujan)</option>
                        <option value="PDA">PDA (TMA)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Pos <span class="text-red-500">*</span></label>
                    <select name="jenis_pos" id="tambah_jenis_pos" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required onchange="ubahFormTambah()">
                        <option value="biasa">Pos Biasa</option>
                        <option value="bendungan">Bendungan</option>
                        <option value="bendung">Bendung</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude</label>
                    <input type="number" step="any" name="lat" id="tambah_lat" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="-5.438720">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude</label>
                    <input type="number" step="any" name="lng" id="tambah_lng" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="105.245680">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Wilayah Sungai</label>
                    <select name="wilayah_sungai" id="tambah_wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                        <option value="">-- Pilih --</option>
                        <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                        <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                        <option value="SEMANGKA">SEMANGKA</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sungai</label>
                    <input type="text" name="sungai" id="tambah_sungai" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Nama sungai">
                </div>
                <div id="tambah_device_container">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Device ID Telemetri</label>
                    <input type="text" name="device_id_telemetry" id="tambah_device" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="ID telemetri (opsional)">
                    <p class="text-[9px] text-slate-400 mt-1">Khusus untuk pos dengan telemetri</p>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- FIELD DINAMIS -->
            <!-- ============================================ -->
            
            <!-- Field untuk Bendungan -->
            <div id="tambah_field_bendungan" class="border border-red-200 rounded-xl p-4 bg-red-50/30 hidden">
                <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-red-500 rounded-full"></span>Data Bendungan
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tipe Bendungan</label>
                        <input type="text" name="tipe_bendungan" id="tambah_tipe_bendungan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Contoh: Urugan, Beton">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Mulai Pembangunan</label>
                        <input type="number" name="tahun_mulai_pembangunan" id="tambah_tahun_mulai" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="2010">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">NWL (m)</label>
                        <input type="number" step="any" name="nwl" id="tambah_nwl" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Volume NWL (jt.m³)</label>
                        <input type="number" step="any" name="nwl_volume" id="tambah_nwl_volume" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas NWL (km²)</label>
                        <input type="number" step="any" name="nwl_luas" id="tambah_nwl_luas" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Mercu (mdpl)</label>
                        <input type="number" step="any" name="elevasi_mercu" id="tambah_elevasi_mercu" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas DAS (km²)</label>
                        <input type="number" step="any" name="luas_das" id="tambah_luas_das" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                </div>
            </div>
            
            <!-- Field untuk Bendung -->
            <div id="tambah_field_bendung" class="border border-cyan-200 rounded-xl p-4 bg-cyan-50/30 hidden">
                <p class="text-xs font-bold text-cyan-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-cyan-500 rounded-full"></span>Data Bendung
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tipe Bendung</label>
                        <input type="text" name="tipe_bendung" id="tambah_tipe_bendung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Contoh: Tetap, Gerak">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Pembangunan</label>
                        <input type="number" name="tahun_pembangunan_bendung" id="tambah_tahun_bendung" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="2010">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Mercu (m)</label>
                        <input type="number" step="any" name="elevasi_mercu_bendung" id="tambah_elevasi_mercu_bendung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Lebar Bendung (m)</label>
                        <input type="number" step="any" name="lebar_bendung" id="tambah_lebar_bendung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jumlah Pintu</label>
                        <input type="number" name="jumlah_pintu" id="tambah_jumlah_pintu" min="0" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
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
<!-- MODAL EDIT POS -->
<!-- ============================================ -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Edit Pos</h3>
            <button type="button" onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_pos') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_pos" id="edit_id_pos">
            
            <!-- Field Dasar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Pos <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" id="edit_nama_pos" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nomor Pos</label>
                    <input type="text" name="nomor_pos" id="edit_nomor_pos" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
            </div>
            
            <!-- Tipe Pos dengan Jenis -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tipe Pos <span class="text-red-500">*</span></label>
                    <select name="tipe_pos" id="edit_tipe_pos" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required onchange="ubahFormEdit()">
                        <option value="PCH">PCH (Curah Hujan)</option>
                        <option value="PDA">PDA (TMA)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Pos <span class="text-red-500">*</span></label>
                    <select name="jenis_pos" id="edit_jenis_pos" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required onchange="ubahFormEdit()">
                        <option value="biasa">Pos Biasa</option>
                        <option value="bendungan">Bendungan</option>
                        <option value="bendung">Bendung</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude</label>
                    <input type="number" step="any" name="lat" id="edit_lat" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude</label>
                    <input type="number" step="any" name="lng" id="edit_lng" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Wilayah Sungai</label>
                    <select name="wilayah_sungai" id="edit_wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                        <option value="">-- Pilih --</option>
                        <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                        <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                        <option value="SEMANGKA">SEMANGKA</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sungai</label>
                    <input type="text" name="sungai" id="edit_sungai" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Device ID Telemetri</label>
                    <input type="text" name="device_id_telemetry" id="edit_device" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="ID telemetri (opsional)">
                    <p class="text-[9px] text-slate-400 mt-1">Khusus untuk pos dengan telemetri</p>
                </div>
            </div>
            
            <!-- Field untuk Bendungan (Edit) -->
            <div id="edit_field_bendungan" class="border border-red-200 rounded-xl p-4 bg-red-50/30 hidden">
                <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-red-500 rounded-full"></span>Data Bendungan
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tipe Bendungan</label>
                        <input type="text" name="tipe_bendungan" id="edit_tipe_bendungan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Contoh: Urugan, Beton">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Mulai Pembangunan</label>
                        <input type="number" name="tahun_mulai_pembangunan" id="edit_tahun_mulai" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="2010">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">NWL (m)</label>
                        <input type="number" step="any" name="nwl" id="edit_nwl" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Volume NWL (jt.m³)</label>
                        <input type="number" step="any" name="nwl_volume" id="edit_nwl_volume" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas NWL (km²)</label>
                        <input type="number" step="any" name="nwl_luas" id="edit_nwl_luas" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Mercu (mdpl)</label>
                        <input type="number" step="any" name="elevasi_mercu" id="edit_elevasi_mercu" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas DAS (km²)</label>
                        <input type="number" step="any" name="luas_das" id="edit_luas_das" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                </div>
            </div>
            
            <!-- Field untuk Bendung (Edit) -->
            <div id="edit_field_bendung" class="border border-cyan-200 rounded-xl p-4 bg-cyan-50/30 hidden">
                <p class="text-xs font-bold text-cyan-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-cyan-500 rounded-full"></span>Data Bendung
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tipe Bendung</label>
                        <input type="text" name="tipe_bendung" id="edit_tipe_bendung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="Contoh: Tetap, Gerak">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Pembangunan</label>
                        <input type="number" name="tahun_pembangunan_bendung" id="edit_tahun_bendung" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="2010">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Mercu (m)</label>
                        <input type="number" step="any" name="elevasi_mercu_bendung" id="edit_elevasi_mercu_bendung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Lebar Bendung (m)</label>
                        <input type="number" step="any" name="lebar_bendung" id="edit_lebar_bendung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jumlah Pintu</label>
                        <input type="number" name="jumlah_pintu" id="edit_jumlah_pintu" min="0" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalEdit()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    setTimeout(function(){var s=document.getElementById('alert-success');var e=document.getElementById('alert-error');if(s)s.style.display='none';if(e)e.style.display='none';},5000);

    // ==========================================
    // DATA POS UNTUK EDIT (DARI PHP)
    // ==========================================
    var posData = <?= json_encode($pos_list) ?>;

    // ==========================================
    // FUNGSI MODAL
    // ==========================================
    function openModalTambah(){
        document.getElementById('modalTambah').style.display='flex';
        // Reset form
        document.getElementById('tambah_nama_pos').value = '';
        document.getElementById('tambah_nomor_pos').value = '';
        document.getElementById('tambah_lat').value = '';
        document.getElementById('tambah_lng').value = '';
        document.getElementById('tambah_sungai').value = '';
        document.getElementById('tambah_device').value = '';
        document.getElementById('tambah_wilayah_sungai').value = '';
        document.getElementById('tambah_tipe_pos').value = 'PCH';
        document.getElementById('tambah_jenis_pos').value = 'biasa';
        // Reset field dinamis
        document.getElementById('tambah_field_bendungan').classList.add('hidden');
        document.getElementById('tambah_field_bendung').classList.add('hidden');
    }
    function closeModalTambah(){document.getElementById('modalTambah').style.display='none';}
    
    function openModalEdit(id){
        // Cari data pos berdasarkan ID
        var data = posData.find(function(p) { return p.id_pos == id; });
        if (!data) {
            alert('Data pos tidak ditemukan!');
            return;
        }
        
        document.getElementById('edit_id_pos').value = data.id_pos;
        document.getElementById('edit_nomor_pos').value = data.nomor_pos || '';
        document.getElementById('edit_nama_pos').value = data.nama_pos;
        document.getElementById('edit_tipe_pos').value = data.tipe_pos;
        document.getElementById('edit_sungai').value = data.sungai || '';
        document.getElementById('edit_device').value = data.device_id_telemetry || '';
        document.getElementById('edit_lat').value = data.lat || '';
        document.getElementById('edit_lng').value = data.lng || '';
        document.getElementById('edit_wilayah_sungai').value = data.wilayah_sungai || '';
        
        // Tentukan jenis pos
        var jenis = 'biasa';
        if (data.is_bendungan == 1) jenis = 'bendungan';
        else if (data.is_bendung == 1) jenis = 'bendung';
        document.getElementById('edit_jenis_pos').value = jenis;
        
        // Isi field dinamis
        if (jenis == 'bendungan') {
            document.getElementById('edit_tipe_bendungan').value = data.tipe_bendungan || '';
            document.getElementById('edit_tahun_mulai').value = data.tahun_mulai_pembangunan || '';
            document.getElementById('edit_nwl').value = data.nwl || '';
            document.getElementById('edit_nwl_volume').value = data.nwl_volume || '';
            document.getElementById('edit_nwl_luas').value = data.nwl_luas || '';
            document.getElementById('edit_elevasi_mercu').value = data.elevasi_mercu || '';
            document.getElementById('edit_luas_das').value = data.luas_das || '';
        } else if (jenis == 'bendung') {
            document.getElementById('edit_tipe_bendung').value = data.tipe_bendung || '';
            document.getElementById('edit_tahun_bendung').value = data.tahun_pembangunan_bendung || '';
            document.getElementById('edit_elevasi_mercu_bendung').value = data.elevasi_mercu_bendung || '';
            document.getElementById('edit_lebar_bendung').value = data.lebar_bendung || '';
            document.getElementById('edit_jumlah_pintu').value = data.jumlah_pintu || '';
        }
        
        // Tampilkan field sesuai jenis
        ubahFormEditWithData(jenis);
        
        document.getElementById('modalEdit').style.display='flex';
    }
    function closeModalEdit(){document.getElementById('modalEdit').style.display='none';}

    // ==========================================
    // FUNGSI UBAH FORM TAMBAH
    // ==========================================
    function ubahFormTambah(){
        var jenis = document.getElementById('tambah_jenis_pos').value;
        
        // Sembunyikan semua field tambahan
        document.getElementById('tambah_field_bendungan').classList.add('hidden');
        document.getElementById('tambah_field_bendung').classList.add('hidden');
        
        // Tampilkan yang sesuai
        if (jenis == 'bendungan') {
            document.getElementById('tambah_field_bendungan').classList.remove('hidden');
        } else if (jenis == 'bendung') {
            document.getElementById('tambah_field_bendung').classList.remove('hidden');
        }
    }

    // ==========================================
    // FUNGSI UBAH FORM EDIT
    // ==========================================
    function ubahFormEdit(){
        var jenis = document.getElementById('edit_jenis_pos').value;
        ubahFormEditWithData(jenis);
    }

    function ubahFormEditWithData(jenis){
        // Sembunyikan semua field tambahan
        document.getElementById('edit_field_bendungan').classList.add('hidden');
        document.getElementById('edit_field_bendung').classList.add('hidden');
        
        // Tampilkan yang sesuai
        if (jenis == 'bendungan') {
            document.getElementById('edit_field_bendungan').classList.remove('hidden');
        } else if (jenis == 'bendung') {
            document.getElementById('edit_field_bendung').classList.remove('hidden');
        }
    }

    // ==========================================
    // CLOSE MODAL
    // ==========================================
    document.getElementById('modalTambah').addEventListener('click',function(e){if(e.target===this)closeModalTambah();});
    document.getElementById('modalEdit').addEventListener('click',function(e){if(e.target===this)closeModalEdit();});
    document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeModalTambah();closeModalEdit();}});

    // ==========================================
    // FILTER TABLE
    // ==========================================
    document.getElementById('searchPos').addEventListener('input',applyFilters);
    document.getElementById('filterTipe').addEventListener('change',applyFilters);
    function applyFilters(){
        var q=document.getElementById('searchPos').value.toLowerCase();
        var t=document.getElementById('filterTipe').value;
        var rows=document.querySelectorAll('.pos-row');var c=0;
        rows.forEach(function(r){
            var text=r.textContent.toLowerCase();
            var tipe=r.getAttribute('data-tipe');
            var isBendungan = r.getAttribute('data-is-bendungan');
            var isBendung = r.getAttribute('data-is-bendung');
            var show = true;
            
            if(q && text.indexOf(q)===-1) show = false;
            if(t !== 'all'){
                if(t === 'bendungan' && isBendungan !== '1') show = false;
                else if(t === 'bendung' && isBendung !== '1') show = false;
                else if(t === 'PCH' && tipe !== 'PCH') show = false;
                else if(t === 'PDA' && tipe !== 'PDA') show = false;
            }
            if(show){r.style.display='';c++;}else{r.style.display='none';}
        });
        document.getElementById('totalCounter').textContent=c+' Pos';
    }
</script>