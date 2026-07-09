<!-- VIEW: superadmin/v_kelola_pengaman_pantai.php -->
<!-- Style konsisten dengan halaman kelola pos -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Kelola Pengaman Pantai</h1>
        <p class="text-slate-500 text-sm mt-1">Manajemen data bangunan pengaman pantai di wilayah BBWS Mesuji Sekampung</p>
    </div>
    <button type="button" onclick="openModalTambah()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Pengaman Pantai
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
        <input type="text" id="searchPengaman" placeholder="Cari nama aset..." class="w-full pl-10 pr-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
    </div>
    <select id="filterJenis" class="px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium text-slate-600">
        <option value="all">Semua Jenis</option>
        <option value="JETTY">JETTY</option>
        <option value="REVETMENT">REVETMENT</option>
        <option value="TANGGUL LAUT">TANGGUL LAUT</option>
        <option value="TEMBOK LAUT">TEMBOK LAUT</option>
    </select>
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
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Pengaman Pantai</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full" id="totalCounter"><?= count($pengaman_list) ?> Data</span>
    </div>
    
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-sm min-w-[800px] md:min-w-[1000px]" id="pengamanTable">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-3 text-left font-bold w-8 md:w-10">#</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold">Nama Aset</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold hidden sm:table-cell">Jenis</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold hidden lg:table-cell">Wilayah</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden sm:table-cell">Panjang (m)</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden lg:table-cell">Elevasi (m)</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden xl:table-cell">Tahun</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 md:w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($pengaman_list) && count($pengaman_list) > 0): $no = 1; foreach($pengaman_list as $p): ?>
                <tr class="hover:bg-slate-50 transition-colors pengaman-row" data-jenis="<?= $p->jenis_bangunan ?>" data-wilayah="<?= $p->wilayah_sungai ?>">
                    <td class="px-2 md:px-3 py-3 text-slate-400 text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-3">
                        <p class="font-semibold text-darkblue text-xs"><?= htmlspecialchars($p->nama_aset) ?></p>
                        <p class="text-[10px] text-slate-400"><?= $p->kode_integrasi ?: 'Tanpa Kode' ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-3 hidden sm:table-cell">
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold bg-orange-50 text-orange-600">
                            <?= htmlspecialchars($p->jenis_bangunan ?? '-') ?>
                        </span>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-slate-500 text-xs hidden lg:table-cell">
                        <?= !empty($p->wilayah_sungai) ? htmlspecialchars($p->wilayah_sungai) : '<span class="text-slate-300">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden sm:table-cell">
                        <?php if($p->panjang): ?>
                            <span class="font-medium text-xs text-slate-600"><?= number_format($p->panjang, 0, ',', '.') ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden lg:table-cell">
                        <?php if($p->elevasi_puncak): ?>
                            <span class="font-mono text-xs text-slate-600"><?= number_format($p->elevasi_puncak, 2) ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden xl:table-cell">
                        <?php if($p->tahun_dibangun): ?>
                            <span class="text-xs text-slate-600"><?= $p->tahun_dibangun ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEdit(<?= htmlspecialchars(json_encode($p)) ?>)" class="p-1.5 md:p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="confirmDeletePengaman(<?= $p->id_pengaman ?>, '<?= htmlspecialchars($p->nama_aset, ENT_QUOTES) ?>')" class="p-1.5 md:p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center text-slate-400">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium">Belum ada data pengaman pantai</p>
                            <p class="text-xs text-slate-400">Klik "Tambah Pengaman Pantai" untuk menambahkan data</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TAMBAH PENGAMAN PANTAI -->
<!-- ============================================ -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Tambah Pengaman Pantai</h3>
            <button type="button" onclick="closeModalTambah()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tambah_pengaman_pantai') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            
            <!-- Data Umum -->
            <div class="border border-blue-200 rounded-xl p-4 bg-blue-50/30">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>Data Umum
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Aset <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_aset" id="tambah_nama_aset" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kode Integrasi</label>
                        <input type="text" name="kode_integrasi" id="tambah_kode_integrasi" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="06.11.xxx">
                    </div>
                </div>
            </div>
            
            <!-- Wilayah & Jenis -->
            <div class="border border-green-200 rounded-xl p-4 bg-green-50/30">
                <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-green-500 rounded-full"></span>Wilayah & Jenis
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Bangunan <span class="text-red-500">*</span></label>
                        <select name="jenis_bangunan" id="tambah_jenis_bangunan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="JETTY">JETTY</option>
                            <option value="REVETMENT">REVETMENT</option>
                            <option value="TANGGUL LAUT">TANGGUL LAUT</option>
                            <option value="TEMBOK LAUT">TEMBOK LAUT</option>
                            <option value="REVETMENT,JETTY">REVETMENT, JETTY</option>
                            <option value="REVETMENT,KRIB">REVETMENT, KRIB</option>
                            <option value="REVETMENT,JETTY,L-SHAPE">REVETMENT, JETTY, L-SHAPE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Wilayah Sungai <span class="text-red-500">*</span></label>
                        <select name="wilayah_sungai" id="tambah_wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="">-- Pilih Wilayah Sungai --</option>
                            <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                            <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                            <option value="SEMANGKA">SEMANGKA</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sungai / DAS</label>
                    <input type="text" name="sungai" id="tambah_sungai" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
            </div>
            
            <!-- Koordinat -->
            <div class="border border-purple-200 rounded-xl p-4 bg-purple-50/30">
                <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>Koordinat
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude Awal</label>
                        <input type="number" step="any" name="lat_awal" id="tambah_lat_awal" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="-5.3971">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude Awal</label>
                        <input type="number" step="any" name="lng_awal" id="tambah_lng_awal" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="105.2668">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude Akhir</label>
                        <input type="number" step="any" name="lat_akhir" id="tambah_lat_akhir" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="-5.3971">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude Akhir</label>
                        <input type="number" step="any" name="lng_akhir" id="tambah_lng_akhir" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="105.2668">
                    </div>
                </div>
            </div>
            
            <!-- Data Teknis -->
            <div class="border border-amber-200 rounded-xl p-4 bg-amber-50/30">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>Data Teknis
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Panjang (m)</label>
                        <input type="number" step="any" name="panjang" id="tambah_panjang" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Puncak (mdpl)</label>
                        <input type="number" step="any" name="elevasi_puncak" id="tambah_elevasi_puncak" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Lebar Puncak (m)</label>
                        <input type="number" step="any" name="lebar_puncak" id="tambah_lebar_puncak" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                    </div>
                </div>
            </div>
            
            <!-- Lokasi & Tahun -->
            <div class="border border-indigo-200 rounded-xl p-4 bg-indigo-50/30">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span>Lokasi & Tahun
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Dibangun</label>
                        <input type="number" name="tahun_dibangun" id="tambah_tahun_dibangun" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="2000">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" id="tambah_kabupaten" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" id="tambah_kecamatan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kelurahan/Desa</label>
                    <input type="text" name="kelurahan" id="tambah_kelurahan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
            </div>
            
            <!-- Keterangan -->
            <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-slate-500 rounded-full"></span>Keterangan
                </p>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Manfaat</label>
                    <textarea name="manfaat" id="tambah_manfaat" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan Tambahan</label>
                    <textarea name="keterangan" id="tambah_keterangan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
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
<!-- MODAL EDIT PENGAMAN PANTAI -->
<!-- ============================================ -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Edit Pengaman Pantai</h3>
            <button type="button" onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_pengaman_pantai') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_pengaman" id="edit_id_pengaman">
            
            <!-- Data Umum -->
            <div class="border border-blue-200 rounded-xl p-4 bg-blue-50/30">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>Data Umum
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Aset <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_aset" id="edit_nama_aset" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kode Integrasi</label>
                        <input type="text" name="kode_integrasi" id="edit_kode_integrasi" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
            </div>
            
            <!-- Wilayah & Jenis -->
            <div class="border border-green-200 rounded-xl p-4 bg-green-50/30">
                <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-green-500 rounded-full"></span>Wilayah & Jenis
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Bangunan <span class="text-red-500">*</span></label>
                        <select name="jenis_bangunan" id="edit_jenis_bangunan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="JETTY">JETTY</option>
                            <option value="REVETMENT">REVETMENT</option>
                            <option value="TANGGUL LAUT">TANGGUL LAUT</option>
                            <option value="TEMBOK LAUT">TEMBOK LAUT</option>
                            <option value="REVETMENT,JETTY">REVETMENT, JETTY</option>
                            <option value="REVETMENT,KRIB">REVETMENT, KRIB</option>
                            <option value="REVETMENT,JETTY,L-SHAPE">REVETMENT, JETTY, L-SHAPE</option>
                        </select>
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
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sungai / DAS</label>
                    <input type="text" name="sungai" id="edit_sungai" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
            </div>
            
            <!-- Koordinat -->
            <div class="border border-purple-200 rounded-xl p-4 bg-purple-50/30">
                <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>Koordinat
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude Awal</label>
                        <input type="number" step="any" name="lat_awal" id="edit_lat_awal" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude Awal</label>
                        <input type="number" step="any" name="lng_awal" id="edit_lng_awal" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude Akhir</label>
                        <input type="number" step="any" name="lat_akhir" id="edit_lat_akhir" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude Akhir</label>
                        <input type="number" step="any" name="lng_akhir" id="edit_lng_akhir" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
            </div>
            
            <!-- Data Teknis -->
            <div class="border border-amber-200 rounded-xl p-4 bg-amber-50/30">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>Data Teknis
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Panjang (m)</label>
                        <input type="number" step="any" name="panjang" id="edit_panjang" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Puncak (mdpl)</label>
                        <input type="number" step="any" name="elevasi_puncak" id="edit_elevasi_puncak" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Lebar Puncak (m)</label>
                        <input type="number" step="any" name="lebar_puncak" id="edit_lebar_puncak" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
            </div>
            
            <!-- Lokasi & Tahun -->
            <div class="border border-indigo-200 rounded-xl p-4 bg-indigo-50/30">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span>Lokasi & Tahun
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Dibangun</label>
                        <input type="number" name="tahun_dibangun" id="edit_tahun_dibangun" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" id="edit_kabupaten" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" id="edit_kecamatan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kelurahan/Desa</label>
                    <input type="text" name="kelurahan" id="edit_kelurahan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
            </div>
            
            <!-- Keterangan -->
            <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-slate-500 rounded-full"></span>Keterangan
                </p>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Manfaat</label>
                    <textarea name="manfaat" id="edit_manfaat" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan Tambahan</label>
                    <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
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
            <h3 class="text-xl font-bold text-darkblue mb-2">Hapus Pengaman Pantai?</h3>
            <p class="text-slate-500 text-sm mb-6">Apakah Anda yakin ingin menghapus <strong id="hapus_nama_pengaman" class="text-darkblue"></strong>? Data yang telah dihapus tidak dapat dikembalikan.</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeModalHapus()" class="px-6 py-2.5 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <a href="#" id="hapus_link_pengaman" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition-all shadow-md">Hapus</a>
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
        document.querySelectorAll('#modalTambah input, #modalTambah textarea, #modalTambah select').forEach(function(el) {
            if(el.type !== 'hidden') el.value = '';
        });
    }
    
    function closeModalTambah(){
        document.getElementById('modalTambah').style.display = 'none';
    }

    // ==========================================
    // FUNGSI MODAL EDIT
    // ==========================================
    function openModalEdit(data){
        document.getElementById('edit_id_pengaman').value = data.id_pengaman;
        document.getElementById('edit_nama_aset').value = data.nama_aset || '';
        document.getElementById('edit_kode_integrasi').value = data.kode_integrasi || '';
        document.getElementById('edit_jenis_bangunan').value = data.jenis_bangunan || '';
        document.getElementById('edit_wilayah_sungai').value = data.wilayah_sungai || '';
        document.getElementById('edit_sungai').value = data.sungai || '';
        document.getElementById('edit_lat_awal').value = data.lat_awal || '';
        document.getElementById('edit_lng_awal').value = data.lng_awal || '';
        document.getElementById('edit_lat_akhir').value = data.lat_akhir || '';
        document.getElementById('edit_lng_akhir').value = data.lng_akhir || '';
        document.getElementById('edit_panjang').value = data.panjang || '';
        document.getElementById('edit_elevasi_puncak').value = data.elevasi_puncak || '';
        document.getElementById('edit_lebar_puncak').value = data.lebar_puncak || '';
        document.getElementById('edit_tahun_dibangun').value = data.tahun_dibangun || '';
        document.getElementById('edit_kabupaten').value = data.kabupaten_kota || '';
        document.getElementById('edit_kecamatan').value = data.kecamatan || '';
        document.getElementById('edit_kelurahan').value = data.kelurahan || '';
        document.getElementById('edit_manfaat').value = data.manfaat || '';
        document.getElementById('edit_keterangan').value = data.keterangan || '';
        
        document.getElementById('modalEdit').style.display = 'flex';
    }
    
    function closeModalEdit(){
        document.getElementById('modalEdit').style.display = 'none';
    }

    // ==========================================
    // FUNGSI MODAL HAPUS
    // ==========================================
    function confirmDeletePengaman(id, nama) {
        document.getElementById('hapus_nama_pengaman').textContent = nama;
        document.getElementById('hapus_link_pengaman').href = '<?= base_url('superadmin/hapus_pengaman_pantai/') ?>' + id;
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
    document.getElementById('searchPengaman').addEventListener('input', applyFilters);
    document.getElementById('filterJenis').addEventListener('change', applyFilters);
    document.getElementById('filterWilayah').addEventListener('change', applyFilters);
    
    function applyFilters(){
        var q = document.getElementById('searchPengaman').value.toLowerCase();
        var j = document.getElementById('filterJenis').value;
        var w = document.getElementById('filterWilayah').value;
        var rows = document.querySelectorAll('.pengaman-row');
        var c = 0;
        
        rows.forEach(function(r){
            var text = r.textContent.toLowerCase();
            var jenis = r.getAttribute('data-jenis');
            var wilayah = r.getAttribute('data-wilayah');
            var show = true;
            
            if(q && text.indexOf(q) === -1) show = false;
            if(j !== 'all' && jenis !== j) show = false;
            if(w !== 'all' && wilayah !== w) show = false;
            
            if(show){
                r.style.display = '';
                c++;
            } else {
                r.style.display = 'none';
            }
        });
        
        document.getElementById('totalCounter').textContent = c + ' Data';
    }
</script>

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
    
    /* Custom scroll untuk modal */
    #modalTambah::-webkit-scrollbar,
    #modalEdit::-webkit-scrollbar {
        width: 4px;
    }
    #modalTambah::-webkit-scrollbar-thumb,
    #modalEdit::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }
</style>