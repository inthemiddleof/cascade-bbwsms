<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-darkblue">Kelola Mata Air</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data sumber mata air di Wilayah Sungai Mesuji Sekampung</p>
        </div>
        <button onclick="openModal('modalTambahMataAir')" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Mata Air
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

    <!-- Tabel Mata Air -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama Mata Air</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Lokasi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Koordinat</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Debit (l/dtk)</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($mata_air_list) && count($mata_air_list) > 0): ?>
                        <?php $no = 1; foreach ($mata_air_list as $m): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-all">
                                <td class="px-4 py-3 text-slate-500"><?= $no++ ?></td>
                                <td class="px-4 py-3 font-medium text-darkblue">
                                    <?= htmlspecialchars($m->nama_mata_air) ?>
                                    <?php if(!empty($m->warna) || !empty($m->bau) || !empty($m->rasa)): ?>
                                        <span class="ml-2 text-[8px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Fisik</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?= htmlspecialchars($m->desa ?? '-') ?>, <?= htmlspecialchars($m->kecamatan ?? '-') ?>
                                    <p class="text-[10px] text-slate-400"><?= htmlspecialchars($m->kabupaten ?? '-') ?>, <?= htmlspecialchars($m->provinsi ?? '-') ?></p>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <span class="font-mono text-xs"><?= number_format($m->latitude ?? 0, 6) ?></span>
                                    <br>
                                    <span class="font-mono text-xs"><?= number_format($m->longitude ?? 0, 6) ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= 
                                        $m->jenis_mata_air == 'Artesis' ? 'bg-blue-50 text-blue-600' :
                                        ($m->jenis_mata_air == 'Freatik' ? 'bg-emerald-50 text-emerald-600' :
                                        ($m->jenis_mata_air == 'Retensi' ? 'bg-amber-50 text-amber-600' :
                                        'bg-slate-50 text-slate-500'))
                                    ?>">
                                        <?= htmlspecialchars($m->jenis_mata_air ?? 'Belum Ditentukan') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?= $m->debit !== null ? number_format($m->debit, 2) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditMataAir(<?= $m->id_mata_air ?>)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button onclick="confirmDeleteMataAir(<?= $m->id_mata_air ?>, '<?= htmlspecialchars($m->nama_mata_air, ENT_QUOTES) ?>')" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                                <p class="font-medium">Belum ada data mata air</p>
                                <p class="text-xs mt-1">Klik "Tambah Mata Air" untuk menambahkan</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH MATA AIR -->
<!-- ========================================== -->
<div id="modalTambahMataAir" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalTambahMataAir')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Mata Air</h3>
            <button onclick="closeModal('modalTambahMataAir')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/tambah_mata_air') ?>" method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            
            <!-- Data Dasar -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Data Dasar</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Mata Air <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_mata_air" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Mata Air</label>
                        <select name="jenis_mata_air" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Jenis</option>
                            <option value="Artesis">Artesis</option>
                            <option value="Freatik">Freatik</option>
                            <option value="Retensi">Retensi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Latitude <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="latitude" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="-5.438720">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Longitude <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="longitude" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="105.245680">
                    </div>
                </div>
            </div>
            
            <!-- Lokasi Administratif -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Lokasi Administratif</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Provinsi <span class="text-red-500">*</span></label>
                        <input type="text" name="provinsi" value="Lampung" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kabupaten <span class="text-red-500">*</span></label>
                        <input type="text" name="kabupaten" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kecamatan <span class="text-red-500">*</span></label>
                        <input type="text" name="kecamatan" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Desa <span class="text-red-500">*</span></label>
                        <input type="text" name="desa" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Karakteristik Fisik -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Karakteristik Fisik</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tipe Geologi</label>
                        <input type="text" name="tipe_geologi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Debit (l/dtk)</label>
                        <input type="number" step="any" name="debit" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="0.00">
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-3 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Warna</label>
                        <input type="text" name="warna" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="Bening/Kuning">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Bau</label>
                        <input type="text" name="bau" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="Tidak Berbau">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kekeruhan</label>
                        <input type="text" name="kekeruhan" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="Jernih">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Rasa</label>
                        <input type="text" name="rasa" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="Tawar">
                    </div>
                </div>
            </div>
            
            <!-- Pemanfaatan & Keterangan -->
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Pemanfaatan & Keterangan</p>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Pemanfaatan Air</label>
                        <input type="text" name="pemanfaatan_air" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="Air Minum, Irigasi, Industri">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent" placeholder="Informasi tambahan..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalTambahMataAir')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT MATA AIR -->
<!-- ========================================== -->
<div id="modalEditMataAir" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalEditMataAir')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Edit Mata Air</h3>
            <button onclick="closeModal('modalEditMataAir')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_mata_air') ?>" method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_mata_air" id="edit_id_mata_air">
            
            <!-- Data Dasar -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Data Dasar</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Mata Air <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_mata_air" id="edit_nama_mata_air" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Mata Air</label>
                        <select name="jenis_mata_air" id="edit_jenis_mata_air" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                            <option value="">Pilih Jenis</option>
                            <option value="Artesis">Artesis</option>
                            <option value="Freatik">Freatik</option>
                            <option value="Retensi">Retensi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Latitude <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="latitude" id="edit_latitude" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Longitude <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="longitude" id="edit_longitude" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Lokasi Administratif -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Lokasi Administratif</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Provinsi <span class="text-red-500">*</span></label>
                        <input type="text" name="provinsi" id="edit_provinsi" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kabupaten <span class="text-red-500">*</span></label>
                        <input type="text" name="kabupaten" id="edit_kabupaten" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kecamatan <span class="text-red-500">*</span></label>
                        <input type="text" name="kecamatan" id="edit_kecamatan" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Desa <span class="text-red-500">*</span></label>
                        <input type="text" name="desa" id="edit_desa" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Karakteristik Fisik -->
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Karakteristik Fisik</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tipe Geologi</label>
                        <input type="text" name="tipe_geologi" id="edit_tipe_geologi" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Debit (l/dtk)</label>
                        <input type="number" step="any" name="debit" id="edit_debit" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-3 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Warna</label>
                        <input type="text" name="warna" id="edit_warna" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Bau</label>
                        <input type="text" name="bau" id="edit_bau" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kekeruhan</label>
                        <input type="text" name="kekeruhan" id="edit_kekeruhan" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Rasa</label>
                        <input type="text" name="rasa" id="edit_rasa" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Pemanfaatan & Keterangan -->
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Pemanfaatan & Keterangan</p>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Pemanfaatan Air</label>
                        <input type="text" name="pemanfaatan_air" id="edit_pemanfaatan_air" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalEditMataAir')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL KONFIRMASI HAPUS -->
<!-- ========================================== -->
<div id="modalHapusMataAir" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalHapusMataAir')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-center text-darkblue mb-2">Hapus Mata Air?</h3>
            <p class="text-center text-slate-500 text-sm mb-6">Apakah Anda yakin ingin menghapus <strong id="hapus_nama_mata_air" class="text-darkblue"></strong>? Data yang telah dihapus tidak dapat dikembalikan.</p>
            <div class="flex justify-center gap-3">
                <button onclick="closeModal('modalHapusMataAir')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <a href="#" id="hapus_link_mata_air" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-sm transition-all">Hapus</a>
            </div>
        </div>
    </div>
</div>

<style>
    #modalTambahMataAir, #modalEditMataAir, #modalHapusMataAir {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    #modalTambahMataAir.show, #modalEditMataAir.show, #modalHapusMataAir.show {
        opacity: 1 !important;
        visibility: visible !important;
    }
    #modalTambahMataAir .bg-white, #modalEditMataAir .bg-white, #modalHapusMataAir .bg-white {
        transition: transform 0.3s ease;
    }
    #modalTambahMataAir.show .bg-white, #modalEditMataAir.show .bg-white, #modalHapusMataAir.show .bg-white {
        transform: scale(1) !important;
    }
    /* Custom scroll */
    #modalTambahMataAir::-webkit-scrollbar, #modalEditMataAir::-webkit-scrollbar {
        width: 4px;
    }
    #modalTambahMataAir::-webkit-scrollbar-thumb, #modalEditMataAir::-webkit-scrollbar-thumb {
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
// EDIT MATA AIR
// ==========================================
function openEditMataAir(id) {
    // Fetch data via AJAX
    fetch('<?= base_url('superadmin/get_mata_air_json') ?>/' + id)
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                alert(data.error);
                return;
            }
            
            document.getElementById('edit_id_mata_air').value = data.id_mata_air;
            document.getElementById('edit_nama_mata_air').value = data.nama_mata_air || '';
            document.getElementById('edit_jenis_mata_air').value = data.jenis_mata_air || '';
            document.getElementById('edit_latitude').value = data.latitude || '';
            document.getElementById('edit_longitude').value = data.longitude || '';
            document.getElementById('edit_provinsi').value = data.provinsi || '';
            document.getElementById('edit_kabupaten').value = data.kabupaten || '';
            document.getElementById('edit_kecamatan').value = data.kecamatan || '';
            document.getElementById('edit_desa').value = data.desa || '';
            document.getElementById('edit_tipe_geologi').value = data.tipe_geologi || '';
            document.getElementById('edit_debit').value = data.debit || '';
            document.getElementById('edit_warna').value = data.warna || '';
            document.getElementById('edit_bau').value = data.bau || '';
            document.getElementById('edit_kekeruhan').value = data.kekeruhan || '';
            document.getElementById('edit_rasa').value = data.rasa || '';
            document.getElementById('edit_pemanfaatan_air').value = data.pemanfaatan_air || '';
            document.getElementById('edit_keterangan').value = data.keterangan || '';
            
            openModal('modalEditMataAir');
        })
        .catch(error => {
            alert('Gagal mengambil data: ' + error);
        });
}

// ==========================================
// HAPUS MATA AIR
// ==========================================
function confirmDeleteMataAir(id, nama) {
    document.getElementById('hapus_nama_mata_air').textContent = nama;
    document.getElementById('hapus_link_mata_air').href = '<?= base_url('superadmin/hapus_mata_air/') ?>' + id;
    openModal('modalHapusMataAir');
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