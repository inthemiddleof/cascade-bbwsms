<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-darkblue">Kelola Pengendali Sedimen</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data bangunan pengendali sedimen (Cekdam / Sabodam)</p>
        </div>
        <button onclick="openModal('modalTambahSedimen')" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengendali Sedimen
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

    <!-- Tabel Pengendali Sedimen -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama Aset</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Sungai</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Wilayah Sungai</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Daya Tampung (m³)</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Kondisi</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sedimen_list) && count($sedimen_list) > 0): ?>
                        <?php $no = 1; foreach ($sedimen_list as $s): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-all">
                                <td class="px-4 py-3 text-slate-500"><?= $no++ ?></td>
                                <td class="px-4 py-3 font-medium text-darkblue"><?= htmlspecialchars($s->nama_aset) ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-amber-50 text-amber-600 rounded-full text-xs font-medium">
                                        <?= htmlspecialchars($s->jenis_bangunan ?? 'Cekdam') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($s->sungai ?? '-') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($s->wilayah_sungai ?? '-') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= number_format($s->daya_tampung ?? 0, 0) ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= 
                                        strpos($s->kondisi ?? '', 'Baik') !== false ? 'bg-emerald-50 text-emerald-600' : 
                                        (strpos($s->kondisi ?? '', 'Rusak Ringan') !== false ? 'bg-yellow-50 text-yellow-600' :
                                        (strpos($s->kondisi ?? '', 'Rusak Berat') !== false ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-500'))
                                    ?>">
                                        <?= htmlspecialchars($s->kondisi ?? '-') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditSedimen(<?= htmlspecialchars(json_encode($s)) ?>)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button onclick="confirmDeleteSedimen(<?= $s->id_sedimen ?>, '<?= htmlspecialchars($s->nama_aset, ENT_QUOTES) ?>')" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
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
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                <p class="font-medium">Belum ada data pengendali sedimen</p>
                                <p class="text-xs mt-1">Klik "Tambah Pengendali Sedimen" untuk menambahkan</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH PENGENDALI SEDIMEN -->
<!-- ========================================== -->
<div id="modalTambahSedimen" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalTambahSedimen')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Pengendali Sedimen</h3>
            <button onclick="closeModal('modalTambahSedimen')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tambah_pengendali_sedimen') ?>" method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_aset" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kode Integrasi</label>
                    <input type="text" name="kode_integrasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="06.08.xxx">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Bangunan <span class="text-red-500">*</span></label>
                    <select name="jenis_bangunan" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="">Pilih Jenis</option>
                        <option value="Cekdam">Cekdam</option>
                        <option value="Sabodam">Sabodam</option>
                        <option value="Check Dam">Check Dam</option>
                    </select>
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
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sungai <span class="text-red-500">*</span></label>
                    <input type="text" name="sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Daerah Aliran Sungai (DAS)</label>
                    <input type="text" name="daerah_aliran_sungai" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Latitude</label>
                    <input type="number" step="any" name="lat" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Longitude</label>
                    <input type="number" step="any" name="lng" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Daya Tampung (m³)</label>
                    <input type="number" step="any" name="daya_tampung" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Panjang (m)</label>
                    <input type="number" step="any" name="panjang" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Lebar (m)</label>
                    <input type="number" step="any" name="lebar" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tinggi (m)</label>
                    <input type="number" step="any" name="tinggi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kondisi</label>
                    <select name="kondisi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="">Pilih Kondisi</option>
                        <option value="Baik / Beroperasi">Baik / Beroperasi</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Operasi</label>
                    <select name="status_operasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="">Pilih Status</option>
                        <option value="Beroperasi">Beroperasi</option>
                        <option value="Tidak Beroperasi">Tidak Beroperasi</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Material</label>
                <input type="text" name="jenis_material" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="Contoh: Sabodam Pasangan Batu">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tahun Dibangun</label>
                <input type="number" name="tahun_dibangun" min="1900" max="<?= date('Y') ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kabupaten/Kota</label>
                    <input type="text" name="kabupaten_kota" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kelurahan/Desa</label>
                    <input type="text" name="kelurahan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalTambahSedimen')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT PENGENDALI SEDIMEN -->
<!-- ========================================== -->
<div id="modalEditSedimen" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalEditSedimen')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Edit Pengendali Sedimen</h3>
            <button onclick="closeModal('modalEditSedimen')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_pengendali_sedimen') ?>" method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_sedimen" id="edit_id_sedimen">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_aset" id="edit_nama_aset" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kode Integrasi</label>
                    <input type="text" name="kode_integrasi" id="edit_kode_integrasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Bangunan <span class="text-red-500">*</span></label>
                    <select name="jenis_bangunan" id="edit_jenis_bangunan" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="Cekdam">Cekdam</option>
                        <option value="Sabodam">Sabodam</option>
                        <option value="Check Dam">Check Dam</option>
                    </select>
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
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sungai <span class="text-red-500">*</span></label>
                    <input type="text" name="sungai" id="edit_sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Daerah Aliran Sungai (DAS)</label>
                    <input type="text" name="daerah_aliran_sungai" id="edit_daerah_aliran_sungai" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Latitude</label>
                    <input type="number" step="any" name="lat" id="edit_lat" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Longitude</label>
                    <input type="number" step="any" name="lng" id="edit_lng" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Daya Tampung (m³)</label>
                    <input type="number" step="any" name="daya_tampung" id="edit_daya_tampung" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Panjang (m)</label>
                    <input type="number" step="any" name="panjang" id="edit_panjang" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Lebar (m)</label>
                    <input type="number" step="any" name="lebar" id="edit_lebar" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tinggi (m)</label>
                    <input type="number" step="any" name="tinggi" id="edit_tinggi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kondisi</label>
                    <select name="kondisi" id="edit_kondisi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="">Pilih Kondisi</option>
                        <option value="Baik / Beroperasi">Baik / Beroperasi</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Operasi</label>
                    <select name="status_operasi" id="edit_status_operasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="">Pilih Status</option>
                        <option value="Beroperasi">Beroperasi</option>
                        <option value="Tidak Beroperasi">Tidak Beroperasi</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Material</label>
                <input type="text" name="jenis_material" id="edit_jenis_material" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tahun Dibangun</label>
                <input type="number" name="tahun_dibangun" id="edit_tahun_dibangun" min="1900" max="<?= date('Y') ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kabupaten/Kota</label>
                    <input type="text" name="kabupaten_kota" id="edit_kabupaten_kota" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" id="edit_kecamatan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kelurahan/Desa</label>
                    <input type="text" name="kelurahan" id="edit_kelurahan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalEditSedimen')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL KONFIRMASI HAPUS -->
<!-- ========================================== -->
<div id="modalHapusSedimen" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalHapusSedimen')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-center text-darkblue mb-2">Hapus Pengendali Sedimen?</h3>
            <p class="text-center text-slate-500 text-sm mb-6">Apakah Anda yakin ingin menghapus <strong id="hapus_nama_sedimen" class="text-darkblue"></strong>? Data yang telah dihapus tidak dapat dikembalikan.</p>
            <div class="flex justify-center gap-3">
                <button onclick="closeModal('modalHapusSedimen')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <a href="#" id="hapus_link_sedimen" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-sm transition-all">Hapus</a>
            </div>
        </div>
    </div>
</div>

<style>
    #modalTambahSedimen, #modalEditSedimen, #modalHapusSedimen {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    #modalTambahSedimen.show, #modalEditSedimen.show, #modalHapusSedimen.show {
        opacity: 1 !important;
        visibility: visible !important;
    }
    #modalTambahSedimen .bg-white, #modalEditSedimen .bg-white, #modalHapusSedimen .bg-white {
        transition: transform 0.3s ease;
    }
    #modalTambahSedimen.show .bg-white, #modalEditSedimen.show .bg-white, #modalHapusSedimen.show .bg-white {
        transform: scale(1) !important;
    }
    /* Custom scroll */
    #modalTambahSedimen::-webkit-scrollbar, #modalEditSedimen::-webkit-scrollbar {
        width: 4px;
    }
    #modalTambahSedimen::-webkit-scrollbar-thumb, #modalEditSedimen::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
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
// EDIT PENGENDALI SEDIMEN
// ==========================================
function openEditSedimen(data) {
    document.getElementById('edit_id_sedimen').value = data.id_sedimen;
    document.getElementById('edit_nama_aset').value = data.nama_aset || '';
    document.getElementById('edit_kode_integrasi').value = data.kode_integrasi || '';
    document.getElementById('edit_jenis_bangunan').value = data.jenis_bangunan || 'Cekdam';
    document.getElementById('edit_wilayah_sungai').value = data.wilayah_sungai || '';
    document.getElementById('edit_sungai').value = data.sungai || '';
    document.getElementById('edit_daerah_aliran_sungai').value = data.daerah_aliran_sungai || '';
    document.getElementById('edit_lat').value = data.lat || '';
    document.getElementById('edit_lng').value = data.lng || '';
    document.getElementById('edit_daya_tampung').value = data.daya_tampung || '';
    document.getElementById('edit_panjang').value = data.panjang || '';
    document.getElementById('edit_lebar').value = data.lebar || '';
    document.getElementById('edit_tinggi').value = data.tinggi || '';
    document.getElementById('edit_kondisi').value = data.kondisi || '';
    document.getElementById('edit_status_operasi').value = data.status_operasi || '';
    document.getElementById('edit_tahun_dibangun').value = data.tahun_dibangun || '';
    document.getElementById('edit_kabupaten_kota').value = data.kabupaten_kota || '';
    document.getElementById('edit_kecamatan').value = data.kecamatan || '';
    document.getElementById('edit_kelurahan').value = data.kelurahan || '';
    document.getElementById('edit_jenis_material').value = data.jenis_material || '';
    document.getElementById('edit_keterangan').value = data.keterangan || '';
    
    openModal('modalEditSedimen');
}

// ==========================================
// HAPUS PENGENDALI SEDIMEN
// ==========================================
function confirmDeleteSedimen(id, nama) {
    document.getElementById('hapus_nama_sedimen').textContent = nama;
    document.getElementById('hapus_link_sedimen').href = '<?= base_url('superadmin/hapus_pengendali_sedimen/') ?>' + id;
    openModal('modalHapusSedimen');
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