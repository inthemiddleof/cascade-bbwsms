<!-- VIEW: superadmin/v_kelola_irigasi.php -->
<!-- Style konsisten dengan halaman kelola pos -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Kelola Daerah Irigasi</h1>
        <p class="text-slate-500 text-sm mt-1">Manajemen data daerah irigasi di Wilayah Sungai Mesuji Sekampung</p>
    </div>
    <button type="button" onclick="openModalTambah()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Irigasi
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
        <input type="text" id="searchIrigasi" placeholder="Cari nama irigasi..." class="w-full pl-10 pr-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
    </div>
    <select id="filterJenis" class="px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium text-slate-600">
        <option value="all">Semua Jenis</option>
        <option value="Irigasi Permukaan">Irigasi Permukaan</option>
        <option value="Rawa">Rawa</option>
        <option value="Tambak">Tambak</option>
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
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Daerah Irigasi</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full" id="totalCounter"><?= count($irigasi_list) ?> Irigasi</span>
    </div>
    
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-sm min-w-[900px] md:min-w-[1100px]" id="irigasiTable">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-3 text-left font-bold w-8 md:w-10">#</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold">Nama Irigasi</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-24 hidden sm:table-cell">Jenis</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold hidden lg:table-cell">WS</th>
                    <th class="px-2 md:px-3 py-3 text-left font-bold hidden xl:table-cell">DAS</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden sm:table-cell">Luas Baku</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden sm:table-cell">Luas Fungsional</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 hidden lg:table-cell">Luas Potensial</th>
                    <th class="px-2 md:px-3 py-3 text-center font-bold w-20 md:w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($irigasi_list) && count($irigasi_list) > 0): $no = 1; foreach($irigasi_list as $i): ?>
                <tr class="hover:bg-slate-50 transition-colors irigasi-row" data-jenis="<?= $i->jenis_daerah_irigasi ?>" data-wilayah="<?= $i->wilayah_sungai ?>">
                    <td class="px-2 md:px-3 py-3 text-slate-400 text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-3">
                        <p class="font-semibold text-darkblue text-xs"><?= htmlspecialchars($i->nama_aset) ?></p>
                        <p class="text-[10px] text-slate-400"><?= $i->kode_integrasi ?: 'Tanpa Kode' ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden sm:table-cell">
                        <?php 
                            $badge_class = 'bg-emerald-50 text-emerald-600';
                            if($i->jenis_daerah_irigasi == 'Rawa') {
                                $badge_class = 'bg-blue-50 text-blue-600';
                            } elseif($i->jenis_daerah_irigasi == 'Tambak') {
                                $badge_class = 'bg-cyan-50 text-cyan-600';
                            }
                        ?>
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold <?= $badge_class ?>">
                            <?= htmlspecialchars($i->jenis_daerah_irigasi ?? 'Irigasi Permukaan') ?>
                        </span>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-slate-500 text-xs hidden lg:table-cell">
                        <?= !empty($i->wilayah_sungai) ? htmlspecialchars($i->wilayah_sungai) : '<span class="text-slate-300">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-slate-500 text-xs hidden xl:table-cell">
                        <?= !empty($i->daerah_aliran_sungai) ? htmlspecialchars($i->daerah_aliran_sungai) : '<span class="text-slate-300">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden sm:table-cell">
                        <?php if($i->luas_baku): ?>
                            <span class="font-medium text-xs text-slate-600"><?= number_format($i->luas_baku, 0, ',', '.') ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden sm:table-cell">
                        <?php if($i->luas_fungsional): ?>
                            <span class="font-medium text-xs text-slate-600"><?= number_format($i->luas_fungsional, 0, ',', '.') ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3 text-center hidden lg:table-cell">
                        <?php if($i->luas_potensial): ?>
                            <span class="font-medium text-xs text-slate-600"><?= number_format($i->luas_potensial, 0, ',', '.') ?></span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEdit(<?= htmlspecialchars(json_encode($i)) ?>)" class="p-1.5 md:p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="confirmDeleteIrigasi(<?= $i->id_irigasi ?>, '<?= htmlspecialchars($i->nama_aset, ENT_QUOTES) ?>')" class="p-1.5 md:p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="9" class="px-5 py-16 text-center text-slate-400">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium">Belum ada data daerah irigasi</p>
                            <p class="text-xs text-slate-400">Klik "Tambah Irigasi" untuk menambahkan data</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL TAMBAH DAERAH IRIGASI -->
<!-- ============================================ -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Tambah Daerah Irigasi</h3>
            <button type="button" onclick="closeModalTambah()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tambah_irigasi') ?>" method="POST" class="p-5 space-y-4">
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
                        <input type="text" name="kode_integrasi" id="tambah_kode_integrasi" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="06.09.xxx">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Daerah Irigasi <span class="text-red-500">*</span></label>
                        <select name="jenis_daerah_irigasi" id="tambah_jenis_irigasi" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="Irigasi Permukaan">Irigasi Permukaan</option>
                            <option value="Rawa">Rawa</option>
                            <option value="Tambak">Tambak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kode Identifikasi</label>
                        <input type="text" name="kode_identifikasi" id="tambah_kode_identifikasi" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
            </div>
            
            <!-- Wilayah -->
            <div class="border border-green-200 rounded-xl p-4 bg-green-50/30">
                <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-green-500 rounded-full"></span>Wilayah
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Wilayah Sungai <span class="text-red-500">*</span></label>
                        <select name="wilayah_sungai" id="tambah_wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="">-- Pilih Wilayah Sungai --</option>
                            <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                            <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                            <option value="SEMANGKA">SEMANGKA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Daerah Aliran Sungai <span class="text-red-500">*</span></label>
                        <select name="daerah_aliran_sungai" id="tambah_das" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="">-- Pilih DAS --</option>
                            <option value="TULANG BAWANG">TULANG BAWANG</option>
                            <option value="MESUJI">MESUJI</option>
                            <option value="SEKAMPUNG">SEKAMPUNG</option>
                            <option value="SEPUTIH">SEPUTIH</option>
                            <option value="JEPARA">JEPARA</option>
                            <option value="COASTAL">COASTAL</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kewenangan</label>
                        <select name="kewenangan" id="tambah_kewenangan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih Kewenangan --</option>
                            <option value="Pusat">Pusat</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Lintas Kewenangan</label>
                        <select name="lintas_kewenangan" id="tambah_lintas_kewenangan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="Dalam Kabupaten / Kota">Dalam Kabupaten / Kota</option>
                            <option value="Lintas Kabupaten / Kota">Lintas Kabupaten / Kota</option>
                            <option value="Lintas Provinsi">Lintas Provinsi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Provinsi</label>
                        <input type="text" name="provinsi" id="tambah_provinsi" value="LAMPUNG" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" id="tambah_kabupaten" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" id="tambah_kecamatan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" id="tambah_kelurahan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude</label>
                        <input type="number" step="any" name="latitude" id="tambah_lat" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="-5.3971">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude</label>
                        <input type="number" step="any" name="longitude" id="tambah_lng" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="105.2668">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan Lokasi</label>
                    <textarea name="keterangan_lokasi" id="tambah_keterangan_lokasi" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
                </div>
            </div>
            
            <!-- Data Teknis -->
            <div class="border border-purple-200 rounded-xl p-4 bg-purple-50/30">
                <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>Data Teknis
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Data</label>
                        <input type="text" name="tahun_data" id="tambah_tahun_data" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="2024">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Bangunan Pengambilan</label>
                        <input type="text" name="bangunan_pengambilan" id="tambah_bangunan_pengambilan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Status Pemeliharaan</label>
                        <select name="status_pemeliharaan" id="tambah_status_pemeliharaan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih Status --</option>
                            <option value="Sudah">Sudah</option>
                            <option value="Tidak/Belum">Tidak/Belum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Di OP Kan Oleh</label>
                        <select name="di_op_kan_oleh" id="tambah_di_op_kan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="Pusat">Pusat</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Luas Area -->
            <div class="border border-amber-200 rounded-xl p-4 bg-amber-50/30">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>Luas Area (Ha)
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Permen</label>
                        <input type="number" step="any" name="luas_permen" id="tambah_luas_permen" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Baku</label>
                        <input type="number" step="any" name="luas_baku" id="tambah_luas_baku" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Potensial</label>
                        <input type="number" step="any" name="luas_potensial" id="tambah_luas_potensial" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Fungsional</label>
                        <input type="number" step="any" name="luas_fungsional" id="tambah_luas_fungsional" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                    </div>
                </div>
            </div>
            
            <!-- Bangunan Utama & Sumber Air -->
            <div class="border border-indigo-200 rounded-xl p-4 bg-indigo-50/30">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span>Bangunan Utama & Sumber Air
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Bangunan Utama</label>
                        <select name="jenis_bangunan_utama" id="tambah_jenis_bangunan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Bendung">Bendung</option>
                            <option value="Bendungan">Bendungan</option>
                            <option value="Free Intake">Free Intake</option>
                            <option value="Intake Tegak">Intake Tegak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sumber Air</label>
                        <input type="text" name="sumber_air" id="tambah_sumber_air" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Bendungan</label>
                        <input type="text" name="nama_bangunan_utama_bendungan" id="tambah_nama_bendungan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Bendung</label>
                        <input type="text" name="nama_bangunan_utama_bendung" id="tambah_nama_bendung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Free Intake</label>
                        <input type="text" name="nama_bangunan_utama_free_intake" id="tambah_nama_free_intake" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Tangkapan Hujan (Km²)</label>
                    <input type="number" step="any" name="luas_tangkapan_hujan" id="tambah_luas_tangkapan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0.00">
                </div>
            </div>
            
            <!-- Rawa / Tambak -->
            <div class="border border-pink-200 rounded-xl p-4 bg-pink-50/30">
                <p class="text-xs font-bold text-pink-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-pink-500 rounded-full"></span>Khusus Rawa / Tambak
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Rawa</label>
                        <select name="jenis_rawa" id="tambah_jenis_rawa" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="Pasang Surut">Pasang Surut</option>
                            <option value="Lebak">Lebak</option>
                            <option value="Polder">Polder</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Fungsi Jaringan Irigasi</label>
                        <select name="fungsi_jaringan_irigasi" id="tambah_fungsi_jaringan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="Drainase">Drainase</option>
                            <option value="Irigasi">Irigasi</option>
                            <option value="Irigasi & Drainase">Irigasi & Drainase</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Status & Deskripsi -->
            <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-slate-500 rounded-full"></span>Status & Deskripsi
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Status Data</label>
                        <select name="status_data" id="tambah_status_data" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="Tidak Terkunci">Tidak Terkunci</option>
                            <option value="Terkunci">Terkunci</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Status Verifikasi</label>
                        <select name="status_verifikasi" id="tambah_status_verifikasi" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="Tidak Terverifikasi">Tidak Terverifikasi</option>
                            <option value="Terverifikasi">Terverifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Deskripsi Aset</label>
                    <textarea name="deskripsi_aset" id="tambah_deskripsi_aset" rows="3" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan Tambahan</label>
                    <textarea name="keterangan_tambahan" id="tambah_keterangan_tambahan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
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
<!-- MODAL EDIT DAERAH IRIGASI -->
<!-- ============================================ -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Edit Daerah Irigasi</h3>
            <button type="button" onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_irigasi') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_irigasi" id="edit_id_irigasi">
            
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Daerah Irigasi <span class="text-red-500">*</span></label>
                        <select name="jenis_daerah_irigasi" id="edit_jenis_irigasi" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="Irigasi Permukaan">Irigasi Permukaan</option>
                            <option value="Rawa">Rawa</option>
                            <option value="Tambak">Tambak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kode Identifikasi</label>
                        <input type="text" name="kode_identifikasi" id="edit_kode_identifikasi" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
            </div>
            
            <!-- Wilayah -->
            <div class="border border-green-200 rounded-xl p-4 bg-green-50/30">
                <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-green-500 rounded-full"></span>Wilayah
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Wilayah Sungai <span class="text-red-500">*</span></label>
                        <select name="wilayah_sungai" id="edit_wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                            <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                            <option value="SEMANGKA">SEMANGKA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Daerah Aliran Sungai <span class="text-red-500">*</span></label>
                        <select name="daerah_aliran_sungai" id="edit_das" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" required>
                            <option value="TULANG BAWANG">TULANG BAWANG</option>
                            <option value="MESUJI">MESUJI</option>
                            <option value="SEKAMPUNG">SEKAMPUNG</option>
                            <option value="SEPUTIH">SEPUTIH</option>
                            <option value="JEPARA">JEPARA</option>
                            <option value="COASTAL">COASTAL</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kewenangan</label>
                        <select name="kewenangan" id="edit_kewenangan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih Kewenangan --</option>
                            <option value="Pusat">Pusat</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Lintas Kewenangan</label>
                        <select name="lintas_kewenangan" id="edit_lintas_kewenangan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="Dalam Kabupaten / Kota">Dalam Kabupaten / Kota</option>
                            <option value="Lintas Kabupaten / Kota">Lintas Kabupaten / Kota</option>
                            <option value="Lintas Provinsi">Lintas Provinsi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Provinsi</label>
                        <input type="text" name="provinsi" id="edit_provinsi" value="LAMPUNG" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten_kota" id="edit_kabupaten" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" id="edit_kecamatan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" id="edit_kelurahan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Latitude</label>
                        <input type="number" step="any" name="latitude" id="edit_lat" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Longitude</label>
                        <input type="number" step="any" name="longitude" id="edit_lng" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan Lokasi</label>
                    <textarea name="keterangan_lokasi" id="edit_keterangan_lokasi" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
                </div>
            </div>
            
            <!-- Data Teknis -->
            <div class="border border-purple-200 rounded-xl p-4 bg-purple-50/30">
                <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>Data Teknis
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tahun Data</label>
                        <input type="text" name="tahun_data" id="edit_tahun_data" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Bangunan Pengambilan</label>
                        <input type="text" name="bangunan_pengambilan" id="edit_bangunan_pengambilan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Status Pemeliharaan</label>
                        <select name="status_pemeliharaan" id="edit_status_pemeliharaan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih Status --</option>
                            <option value="Sudah">Sudah</option>
                            <option value="Tidak/Belum">Tidak/Belum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Di OP Kan Oleh</label>
                        <select name="di_op_kan_oleh" id="edit_di_op_kan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="Pusat">Pusat</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Luas Area -->
            <div class="border border-amber-200 rounded-xl p-4 bg-amber-50/30">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>Luas Area (Ha)
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Permen</label>
                        <input type="number" step="any" name="luas_permen" id="edit_luas_permen" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Baku</label>
                        <input type="number" step="any" name="luas_baku" id="edit_luas_baku" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Potensial</label>
                        <input type="number" step="any" name="luas_potensial" id="edit_luas_potensial" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Fungsional</label>
                        <input type="number" step="any" name="luas_fungsional" id="edit_luas_fungsional" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
            </div>
            
            <!-- Bangunan Utama & Sumber Air -->
            <div class="border border-indigo-200 rounded-xl p-4 bg-indigo-50/30">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span>Bangunan Utama & Sumber Air
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Bangunan Utama</label>
                        <select name="jenis_bangunan_utama" id="edit_jenis_bangunan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Bendung">Bendung</option>
                            <option value="Bendungan">Bendungan</option>
                            <option value="Free Intake">Free Intake</option>
                            <option value="Intake Tegak">Intake Tegak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sumber Air</label>
                        <input type="text" name="sumber_air" id="edit_sumber_air" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Bendungan</label>
                        <input type="text" name="nama_bangunan_utama_bendungan" id="edit_nama_bendungan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Bendung</label>
                        <input type="text" name="nama_bangunan_utama_bendung" id="edit_nama_bendung" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Free Intake</label>
                        <input type="text" name="nama_bangunan_utama_free_intake" id="edit_nama_free_intake" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas Tangkapan Hujan (Km²)</label>
                    <input type="number" step="any" name="luas_tangkapan_hujan" id="edit_luas_tangkapan" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                </div>
            </div>
            
            <!-- Rawa / Tambak -->
            <div class="border border-pink-200 rounded-xl p-4 bg-pink-50/30">
                <p class="text-xs font-bold text-pink-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-pink-500 rounded-full"></span>Khusus Rawa / Tambak
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Rawa</label>
                        <select name="jenis_rawa" id="edit_jenis_rawa" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="Pasang Surut">Pasang Surut</option>
                            <option value="Lebak">Lebak</option>
                            <option value="Polder">Polder</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Fungsi Jaringan Irigasi</label>
                        <select name="fungsi_jaringan_irigasi" id="edit_fungsi_jaringan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="Drainase">Drainase</option>
                            <option value="Irigasi">Irigasi</option>
                            <option value="Irigasi & Drainase">Irigasi & Drainase</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Status & Deskripsi -->
            <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-slate-500 rounded-full"></span>Status & Deskripsi
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Status Data</label>
                        <select name="status_data" id="edit_status_data" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="Tidak Terkunci">Tidak Terkunci</option>
                            <option value="Terkunci">Terkunci</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Status Verifikasi</label>
                        <select name="status_verifikasi" id="edit_status_verifikasi" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white">
                            <option value="Tidak Terverifikasi">Tidak Terverifikasi</option>
                            <option value="Terverifikasi">Terverifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Deskripsi Aset</label>
                    <textarea name="deskripsi_aset" id="edit_deskripsi_aset" rows="3" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan Tambahan</label>
                    <textarea name="keterangan_tambahan" id="edit_keterangan_tambahan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white"></textarea>
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
            <h3 class="text-xl font-bold text-darkblue mb-2">Hapus Daerah Irigasi?</h3>
            <p class="text-slate-500 text-sm mb-6">Apakah Anda yakin ingin menghapus <strong id="hapus_nama_irigasi" class="text-darkblue"></strong>? Data yang telah dihapus tidak dapat dikembalikan.</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeModalHapus()" class="px-6 py-2.5 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <a href="#" id="hapus_link_irigasi" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition-all shadow-md">Hapus</a>
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
        document.getElementById('tambah_provinsi').value = 'LAMPUNG';
        document.getElementById('tambah_jenis_irigasi').value = 'Irigasi Permukaan';
        document.getElementById('tambah_status_data').value = 'Tidak Terkunci';
        document.getElementById('tambah_status_verifikasi').value = 'Tidak Terverifikasi';
    }
    
    function closeModalTambah(){
        document.getElementById('modalTambah').style.display = 'none';
    }

    // ==========================================
    // FUNGSI MODAL EDIT
    // ==========================================
    function openModalEdit(data){
        document.getElementById('edit_id_irigasi').value = data.id_irigasi;
        document.getElementById('edit_nama_aset').value = data.nama_aset || '';
        document.getElementById('edit_kode_integrasi').value = data.kode_integrasi || '';
        document.getElementById('edit_jenis_irigasi').value = data.jenis_daerah_irigasi || 'Irigasi Permukaan';
        document.getElementById('edit_kode_identifikasi').value = data.kode_identifikasi || '';
        document.getElementById('edit_wilayah_sungai').value = data.wilayah_sungai || '';
        document.getElementById('edit_das').value = data.daerah_aliran_sungai || '';
        document.getElementById('edit_kewenangan').value = data.kewenangan || '';
        document.getElementById('edit_lintas_kewenangan').value = data.lintas_kewenangan || '';
        document.getElementById('edit_provinsi').value = data.provinsi || 'LAMPUNG';
        document.getElementById('edit_kabupaten').value = data.kabupaten_kota || '';
        document.getElementById('edit_kecamatan').value = data.kecamatan || '';
        document.getElementById('edit_kelurahan').value = data.kelurahan || '';
        document.getElementById('edit_lat').value = data.latitude || '';
        document.getElementById('edit_lng').value = data.longitude || '';
        document.getElementById('edit_keterangan_lokasi').value = data.keterangan_lokasi || '';
        document.getElementById('edit_tahun_data').value = data.tahun_data || '';
        document.getElementById('edit_bangunan_pengambilan').value = data.bangunan_pengambilan || '';
        document.getElementById('edit_status_pemeliharaan').value = data.status_pemeliharaan || '';
        document.getElementById('edit_di_op_kan').value = data.di_op_kan_oleh || '';
        document.getElementById('edit_luas_permen').value = data.luas_permen || '';
        document.getElementById('edit_luas_baku').value = data.luas_baku || '';
        document.getElementById('edit_luas_potensial').value = data.luas_potensial || '';
        document.getElementById('edit_luas_fungsional').value = data.luas_fungsional || '';
        document.getElementById('edit_jenis_bangunan').value = data.jenis_bangunan_utama || '';
        document.getElementById('edit_sumber_air').value = data.sumber_air || '';
        document.getElementById('edit_nama_bendungan').value = data.nama_bangunan_utama_bendungan || '';
        document.getElementById('edit_nama_bendung').value = data.nama_bangunan_utama_bendung || '';
        document.getElementById('edit_nama_free_intake').value = data.nama_bangunan_utama_free_intake || '';
        document.getElementById('edit_luas_tangkapan').value = data.luas_tangkapan_hujan || '';
        document.getElementById('edit_jenis_rawa').value = data.jenis_rawa || '';
        document.getElementById('edit_fungsi_jaringan').value = data.fungsi_jaringan_irigasi || '';
        document.getElementById('edit_status_data').value = data.status_data || 'Tidak Terkunci';
        document.getElementById('edit_status_verifikasi').value = data.status_verifikasi || 'Tidak Terverifikasi';
        document.getElementById('edit_deskripsi_aset').value = data.deskripsi_aset || '';
        document.getElementById('edit_keterangan_tambahan').value = data.keterangan_tambahan || '';
        
        document.getElementById('modalEdit').style.display = 'flex';
    }
    
    function closeModalEdit(){
        document.getElementById('modalEdit').style.display = 'none';
    }

    // ==========================================
    // FUNGSI MODAL HAPUS
    // ==========================================
    function confirmDeleteIrigasi(id, nama) {
        document.getElementById('hapus_nama_irigasi').textContent = nama;
        document.getElementById('hapus_link_irigasi').href = '<?= base_url('superadmin/hapus_irigasi/') ?>' + id;
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
    document.getElementById('searchIrigasi').addEventListener('input', applyFilters);
    document.getElementById('filterJenis').addEventListener('change', applyFilters);
    document.getElementById('filterWilayah').addEventListener('change', applyFilters);
    
    function applyFilters(){
        var q = document.getElementById('searchIrigasi').value.toLowerCase();
        var j = document.getElementById('filterJenis').value;
        var w = document.getElementById('filterWilayah').value;
        var rows = document.querySelectorAll('.irigasi-row');
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
        
        document.getElementById('totalCounter').textContent = c + ' Irigasi';
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