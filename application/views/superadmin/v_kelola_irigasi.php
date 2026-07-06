<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-darkblue">Kelola Daerah Irigasi</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data daerah irigasi di Wilayah Sungai Mesuji Sekampung</p>
        </div>
        <button onclick="openModal('modalTambahIrigasi')" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Irigasi
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

    <!-- Tabel Daerah Irigasi -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama DI</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">WS</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">DAS</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Luas Baku (Ha)</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Luas Fungsional</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($irigasi_list) && count($irigasi_list) > 0): ?>
                        <?php $no = 1; foreach ($irigasi_list as $i): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-all">
                                <td class="px-4 py-3 text-slate-500"><?= $no++ ?></td>
                                <td class="px-4 py-3 font-medium text-darkblue"><?= htmlspecialchars($i->nama_aset) ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= 
                                        $i->jenis_daerah_irigasi == 'Rawa' ? 'bg-blue-50 text-blue-600' :
                                        ($i->jenis_daerah_irigasi == 'Tambak' ? 'bg-cyan-50 text-cyan-600' :
                                        'bg-emerald-50 text-emerald-600')
                                    ?>">
                                        <?= htmlspecialchars($i->jenis_daerah_irigasi ?? 'Irigasi Permukaan') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($i->wilayah_sungai ?? '-') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($i->daerah_aliran_sungai ?? '-') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= number_format($i->luas_baku ?? 0, 0) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= number_format($i->luas_fungsional ?? 0, 0) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditIrigasi(<?= htmlspecialchars(json_encode($i)) ?>)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button onclick="confirmDeleteIrigasi(<?= $i->id_irigasi ?>, '<?= htmlspecialchars($i->nama_aset, ENT_QUOTES) ?>')" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
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
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <p class="font-medium">Belum ada data daerah irigasi</p>
                                <p class="text-xs mt-1">Klik "Tambah Irigasi" untuk menambahkan</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH DAERAH IRIGASI -->
<!-- ========================================== -->
<div id="modalTambahIrigasi" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalTambahIrigasi')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Daerah Irigasi</h3>
            <button onclick="closeModal('modalTambahIrigasi')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tambah_irigasi') ?>" method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            
            <!-- Data Umum -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Data Umum</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Aset <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_aset" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kode Integrasi</label>
                        <input type="text" name="kode_integrasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="06.09.xxx">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Daerah Irigasi <span class="text-red-500">*</span></label>
                        <select name="jenis_daerah_irigasi" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="Irigasi Permukaan">Irigasi Permukaan</option>
                            <option value="Rawa">Rawa</option>
                            <option value="Tambak">Tambak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kode Identifikasi</label>
                        <input type="text" name="kode_identifikasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Wilayah -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Wilayah</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Wilayah Sungai <span class="text-red-500">*</span></label>
                        <select name="wilayah_sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Wilayah Sungai</option>
                            <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                            <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                            <option value="SEMANGKA">SEMANGKA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Daerah Aliran Sungai <span class="text-red-500">*</span></label>
                        <select name="daerah_aliran_sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih DAS</option>
                            <option value="TULANG BAWANG">TULANG BAWANG</option>
                            <option value="MESUJI">MESUJI</option>
                            <option value="SEKAMPUNG">SEKAMPUNG</option>
                            <option value="SEPUTIH">SEPUTIH</option>
                            <option value="JEPARA">JEPARA</option>
                            <option value="COASTAL">COASTAL</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kewenangan</label>
                        <select name="kewenangan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Kewenangan</option>
                            <option value="Pusat">Pusat</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Lintas Kewenangan</label>
                        <select name="lintas_kewenangan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih</option>
                            <option value="Dalam Kabupaten / Kota">Dalam Kabupaten / Kota</option>
                            <option value="Lintas Kabupaten / Kota">Lintas Kabupaten / Kota</option>
                            <option value="Lintas Provinsi">Lintas Provinsi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Provinsi</label>
                        <input type="text" name="provinsi" value="LAMPUNG" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan Lokasi</label>
                    <textarea name="keterangan_lokasi" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
                </div>
            </div>
            
            <!-- Data Teknis -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Data Teknis</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tahun Data</label>
                        <input type="text" name="tahun_data" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tahun Pembangunan</label>
                        <input type="text" name="tahun_pembangunan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Bangunan Pengambilan</label>
                    <input type="text" name="bangunan_pengambilan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Pemeliharaan</label>
                        <select name="status_pemeliharaan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Status</option>
                            <option value="Sudah">Sudah</option>
                            <option value="Tidak/Belum">Tidak/Belum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Di OP Kan Oleh</label>
                        <select name="di_op_kan_oleh" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih</option>
                            <option value="Pusat">Pusat</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Luas Area -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Luas Area (Ha)</p>
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Permen</label>
                        <input type="number" step="any" name="luas_permen" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Baku</label>
                        <input type="number" step="any" name="luas_baku" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Potensial</label>
                        <input type="number" step="any" name="luas_potensial" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Fungsional</label>
                        <input type="number" step="any" name="luas_fungsional" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Bangunan Utama & Sumber Air -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Bangunan Utama & Sumber Air</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Bangunan Utama</label>
                        <select name="jenis_bangunan_utama" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Jenis</option>
                            <option value="Bendung">Bendung</option>
                            <option value="Bendungan">Bendungan</option>
                            <option value="Free Intake">Free Intake</option>
                            <option value="Intake Tegak">Intake Tegak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sumber Air</label>
                        <input type="text" name="sumber_air" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Bendungan</label>
                        <input type="text" name="nama_bangunan_utama_bendungan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Bendung</label>
                        <input type="text" name="nama_bangunan_utama_bendung" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Free Intake</label>
                        <input type="text" name="nama_bangunan_utama_free_intake" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Tangkapan Hujan (Km²)</label>
                        <input type="number" step="any" name="luas_tangkapan_hujan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Rawa / Tambak -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Khusus Rawa / Tambak</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Rawa</label>
                        <select name="jenis_rawa" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih</option>
                            <option value="Pasang Surut">Pasang Surut</option>
                            <option value="Lebak">Lebak</option>
                            <option value="Polder">Polder</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Fungsi Jaringan Irigasi</label>
                        <select name="fungsi_jaringan_irigasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih</option>
                            <option value="Drainase">Drainase</option>
                            <option value="Irigasi">Irigasi</option>
                            <option value="Irigasi & Drainase">Irigasi & Drainase</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Status & Deskripsi -->
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Status & Deskripsi</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Data</label>
                        <select name="status_data" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="Tidak Terkunci">Tidak Terkunci</option>
                            <option value="Terkunci">Terkunci</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Verifikasi</label>
                        <select name="status_verifikasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="Tidak Terverifikasi">Tidak Terverifikasi</option>
                            <option value="Terverifikasi">Terverifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Deskripsi Aset</label>
                    <textarea name="deskripsi_aset" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan Tambahan</label>
                    <textarea name="keterangan_tambahan" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalTambahIrigasi')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT DAERAH IRIGASI -->
<!-- ========================================== -->
<div id="modalEditIrigasi" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalEditIrigasi')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Edit Daerah Irigasi</h3>
            <button onclick="closeModal('modalEditIrigasi')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_irigasi') ?>" method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_irigasi" id="edit_id_irigasi">
            
            <!-- Data Umum -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Data Umum</p>
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
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Daerah Irigasi <span class="text-red-500">*</span></label>
                        <select name="jenis_daerah_irigasi" id="edit_jenis_daerah_irigasi" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="Irigasi Permukaan">Irigasi Permukaan</option>
                            <option value="Rawa">Rawa</option>
                            <option value="Tambak">Tambak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kode Identifikasi</label>
                        <input type="text" name="kode_identifikasi" id="edit_kode_identifikasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Wilayah -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Wilayah</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Wilayah Sungai <span class="text-red-500">*</span></label>
                        <select name="wilayah_sungai" id="edit_wilayah_sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                            <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                            <option value="SEMANGKA">SEMANGKA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Daerah Aliran Sungai <span class="text-red-500">*</span></label>
                        <select name="daerah_aliran_sungai" id="edit_daerah_aliran_sungai" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="TULANG BAWANG">TULANG BAWANG</option>
                            <option value="MESUJI">MESUJI</option>
                            <option value="SEKAMPUNG">SEKAMPUNG</option>
                            <option value="SEPUTIH">SEPUTIH</option>
                            <option value="JEPARA">JEPARA</option>
                            <option value="COASTAL">COASTAL</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kewenangan</label>
                        <select name="kewenangan" id="edit_kewenangan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Kewenangan</option>
                            <option value="Pusat">Pusat</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Lintas Kewenangan</label>
                        <select name="lintas_kewenangan" id="edit_lintas_kewenangan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih</option>
                            <option value="Dalam Kabupaten / Kota">Dalam Kabupaten / Kota</option>
                            <option value="Lintas Kabupaten / Kota">Lintas Kabupaten / Kota</option>
                            <option value="Lintas Provinsi">Lintas Provinsi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Provinsi</label>
                        <input type="text" name="provinsi" id="edit_provinsi" value="LAMPUNG" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" id="edit_kabupaten_kota" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" id="edit_kecamatan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" id="edit_kelurahan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" id="edit_latitude" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" id="edit_longitude" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan Lokasi</label>
                    <textarea name="keterangan_lokasi" id="edit_keterangan_lokasi" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
                </div>
            </div>
            
            <!-- Data Teknis -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Data Teknis</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tahun Data</label>
                        <input type="text" name="tahun_data" id="edit_tahun_data" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tahun Pembangunan</label>
                        <input type="text" name="tahun_pembangunan" id="edit_tahun_pembangunan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Bangunan Pengambilan</label>
                    <input type="text" name="bangunan_pengambilan" id="edit_bangunan_pengambilan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Pemeliharaan</label>
                        <select name="status_pemeliharaan" id="edit_status_pemeliharaan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Status</option>
                            <option value="Sudah">Sudah</option>
                            <option value="Tidak/Belum">Tidak/Belum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Di OP Kan Oleh</label>
                        <select name="di_op_kan_oleh" id="edit_di_op_kan_oleh" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih</option>
                            <option value="Pusat">Pusat</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Luas Area -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Luas Area (Ha)</p>
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Permen</label>
                        <input type="number" step="any" name="luas_permen" id="edit_luas_permen" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Baku</label>
                        <input type="number" step="any" name="luas_baku" id="edit_luas_baku" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Potensial</label>
                        <input type="number" step="any" name="luas_potensial" id="edit_luas_potensial" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Fungsional</label>
                        <input type="number" step="any" name="luas_fungsional" id="edit_luas_fungsional" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Bangunan Utama & Sumber Air -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Bangunan Utama & Sumber Air</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Bangunan Utama</label>
                        <select name="jenis_bangunan_utama" id="edit_jenis_bangunan_utama" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Jenis</option>
                            <option value="Bendung">Bendung</option>
                            <option value="Bendungan">Bendungan</option>
                            <option value="Free Intake">Free Intake</option>
                            <option value="Intake Tegak">Intake Tegak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sumber Air</label>
                        <input type="text" name="sumber_air" id="edit_sumber_air" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Bendungan</label>
                        <input type="text" name="nama_bangunan_utama_bendungan" id="edit_nama_bendungan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Bendung</label>
                        <input type="text" name="nama_bangunan_utama_bendung" id="edit_nama_bendung" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Free Intake</label>
                        <input type="text" name="nama_bangunan_utama_free_intake" id="edit_nama_free_intake" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Luas Tangkapan Hujan (Km²)</label>
                        <input type="number" step="any" name="luas_tangkapan_hujan" id="edit_luas_tangkapan_hujan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Rawa / Tambak -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Khusus Rawa / Tambak</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Rawa</label>
                        <select name="jenis_rawa" id="edit_jenis_rawa" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih</option>
                            <option value="Pasang Surut">Pasang Surut</option>
                            <option value="Lebak">Lebak</option>
                            <option value="Polder">Polder</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Fungsi Jaringan Irigasi</label>
                        <select name="fungsi_jaringan_irigasi" id="edit_fungsi_jaringan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih</option>
                            <option value="Drainase">Drainase</option>
                            <option value="Irigasi">Irigasi</option>
                            <option value="Irigasi & Drainase">Irigasi & Drainase</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Status & Deskripsi -->
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Status & Deskripsi</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Data</label>
                        <select name="status_data" id="edit_status_data" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="Tidak Terkunci">Tidak Terkunci</option>
                            <option value="Terkunci">Terkunci</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Verifikasi</label>
                        <select name="status_verifikasi" id="edit_status_verifikasi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="Tidak Terverifikasi">Tidak Terverifikasi</option>
                            <option value="Terverifikasi">Terverifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Deskripsi Aset</label>
                    <textarea name="deskripsi_aset" id="edit_deskripsi_aset" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan Tambahan</label>
                    <textarea name="keterangan_tambahan" id="edit_keterangan_tambahan" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalEditIrigasi')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL KONFIRMASI HAPUS -->
<!-- ========================================== -->
<div id="modalHapusIrigasi" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalHapusIrigasi')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-center text-darkblue mb-2">Hapus Daerah Irigasi?</h3>
            <p class="text-center text-slate-500 text-sm mb-6">Apakah Anda yakin ingin menghapus <strong id="hapus_nama_irigasi" class="text-darkblue"></strong>? Data yang telah dihapus tidak dapat dikembalikan.</p>
            <div class="flex justify-center gap-3">
                <button onclick="closeModal('modalHapusIrigasi')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <a href="#" id="hapus_link_irigasi" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-sm transition-all">Hapus</a>
            </div>
        </div>
    </div>
</div>

<style>
    #modalTambahIrigasi, #modalEditIrigasi, #modalHapusIrigasi {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    #modalTambahIrigasi.show, #modalEditIrigasi.show, #modalHapusIrigasi.show {
        opacity: 1 !important;
        visibility: visible !important;
    }
    #modalTambahIrigasi .bg-white, #modalEditIrigasi .bg-white, #modalHapusIrigasi .bg-white {
        transition: transform 0.3s ease;
    }
    #modalTambahIrigasi.show .bg-white, #modalEditIrigasi.show .bg-white, #modalHapusIrigasi.show .bg-white {
        transform: scale(1) !important;
    }
    /* Custom scroll */
    #modalTambahIrigasi::-webkit-scrollbar, #modalEditIrigasi::-webkit-scrollbar {
        width: 4px;
    }
    #modalTambahIrigasi::-webkit-scrollbar-thumb, #modalEditIrigasi::-webkit-scrollbar-thumb {
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
// EDIT DAERAH IRIGASI
// ==========================================
function openEditIrigasi(data) {
    document.getElementById('edit_id_irigasi').value = data.id_irigasi;
    document.getElementById('edit_nama_aset').value = data.nama_aset || '';
    document.getElementById('edit_kode_integrasi').value = data.kode_integrasi || '';
    document.getElementById('edit_jenis_daerah_irigasi').value = data.jenis_daerah_irigasi || 'Irigasi Permukaan';
    document.getElementById('edit_kode_identifikasi').value = data.kode_identifikasi || '';
    document.getElementById('edit_wilayah_sungai').value = data.wilayah_sungai || '';
    document.getElementById('edit_daerah_aliran_sungai').value = data.daerah_aliran_sungai || '';
    document.getElementById('edit_kewenangan').value = data.kewenangan || '';
    document.getElementById('edit_lintas_kewenangan').value = data.lintas_kewenangan || '';
    document.getElementById('edit_provinsi').value = data.provinsi || 'LAMPUNG';
    document.getElementById('edit_kabupaten_kota').value = data.kabupaten_kota || '';
    document.getElementById('edit_kecamatan').value = data.kecamatan || '';
    document.getElementById('edit_kelurahan').value = data.kelurahan || '';
    document.getElementById('edit_latitude').value = data.latitude || '';
    document.getElementById('edit_longitude').value = data.longitude || '';
    document.getElementById('edit_keterangan_lokasi').value = data.keterangan_lokasi || '';
    document.getElementById('edit_tahun_data').value = data.tahun_data || '';
    document.getElementById('edit_tahun_pembangunan').value = data.tahun_pembangunan || '';
    document.getElementById('edit_bangunan_pengambilan').value = data.bangunan_pengambilan || '';
    document.getElementById('edit_status_pemeliharaan').value = data.status_pemeliharaan || '';
    document.getElementById('edit_di_op_kan_oleh').value = data.di_op_kan_oleh || '';
    document.getElementById('edit_luas_permen').value = data.luas_permen || '';
    document.getElementById('edit_luas_baku').value = data.luas_baku || '';
    document.getElementById('edit_luas_potensial').value = data.luas_potensial || '';
    document.getElementById('edit_luas_fungsional').value = data.luas_fungsional || '';
    document.getElementById('edit_jenis_bangunan_utama').value = data.jenis_bangunan_utama || '';
    document.getElementById('edit_sumber_air').value = data.sumber_air || '';
    document.getElementById('edit_nama_bendungan').value = data.nama_bangunan_utama_bendungan || '';
    document.getElementById('edit_nama_bendung').value = data.nama_bangunan_utama_bendung || '';
    document.getElementById('edit_nama_free_intake').value = data.nama_bangunan_utama_free_intake || '';
    document.getElementById('edit_luas_tangkapan_hujan').value = data.luas_tangkapan_hujan || '';
    document.getElementById('edit_jenis_rawa').value = data.jenis_rawa || '';
    document.getElementById('edit_fungsi_jaringan').value = data.fungsi_jaringan_irigasi || '';
    document.getElementById('edit_status_data').value = data.status_data || 'Tidak Terkunci';
    document.getElementById('edit_status_verifikasi').value = data.status_verifikasi || 'Tidak Terverifikasi';
    document.getElementById('edit_deskripsi_aset').value = data.deskripsi_aset || '';
    document.getElementById('edit_keterangan_tambahan').value = data.keterangan_tambahan || '';
    
    openModal('modalEditIrigasi');
}

// ==========================================
// HAPUS DAERAH IRIGASI
// ==========================================
function confirmDeleteIrigasi(id, nama) {
    document.getElementById('hapus_nama_irigasi').textContent = nama;
    document.getElementById('hapus_link_irigasi').href = '<?= base_url('superadmin/hapus_irigasi/') ?>' + id;
    openModal('modalHapusIrigasi');
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