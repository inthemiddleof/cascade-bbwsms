<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-darkblue">Kelola Telemetri</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola perangkat telemetri (PCH / PDA) yang sudah memiliki device ID</p>
        </div>
        <div class="flex gap-3">
            <span class="px-3 py-2 bg-emerald-50 text-emerald-600 rounded-lg text-sm font-medium flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                <?php 
                    $online_count = 0;
                    foreach ($telemetri_list ?? [] as $t) {
                        if ($t->is_online) $online_count++;
                    }
                ?>
                <?= $online_count ?> Online
            </span>
            <span class="px-3 py-2 bg-slate-50 text-slate-600 rounded-lg text-sm font-medium">
                Total <?= count($telemetri_list ?? []) ?> Device
            </span>
        </div>
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

    <!-- Tabel Telemetri -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama Pos</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Tipe</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Device ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Data Terakhir</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Total Data</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($telemetri_list) && count($telemetri_list) > 0): ?>
                        <?php $no = 1; foreach ($telemetri_list as $t): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-all">
                                <td class="px-4 py-3 text-slate-500"><?= $no++ ?></td>
                                <td class="px-4 py-3 font-medium text-darkblue"><?= htmlspecialchars($t->nama_pos) ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= $t->tipe_pos == 'PCH' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' ?>">
                                        <?= htmlspecialchars($t->tipe_pos) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <code class="px-2 py-1 bg-slate-100 rounded text-xs font-mono text-slate-600">
                                        <?= htmlspecialchars($t->device_id_telemetry) ?>
                                    </code>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($t->is_online): ?>
                                        <span class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span class="text-xs font-medium text-emerald-600">Online</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                            <span class="text-xs font-medium text-red-600">Offline</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?php if ($t->last_data && !empty($t->last_data->received_at)): ?>
                                        <div class="text-xs">
                                            <div><?= date('d-m-Y', strtotime($t->last_data->received_at)) ?></div>
                                            <div class="text-slate-400"><?= date('H:i:s', strtotime($t->last_data->received_at)) ?></div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400">Belum ada data</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-center">
                                    <?= number_format($t->total_data ?? 0) ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="openEditTelemetri(<?= htmlspecialchars(json_encode($t)) ?>)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-xs font-medium transition-all flex items-center gap-1 mx-auto">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                <p class="font-medium">Belum ada perangkat telemetri</p>
                                <p class="text-xs mt-1">Pastikan pos memiliki <code class="px-1 py-0.5 bg-slate-100 rounded text-xs">device_id_telemetry</code> yang terisi</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDIT TELEMETRI -->
<!-- ========================================== -->
<div id="modalEditTelemetri" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalEditTelemetri')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-darkblue">Edit Perangkat Telemetri</h3>
            <button onclick="closeModal('modalEditTelemetri')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_telemetri') ?>" method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_pos" id="edit_id_pos">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Device ID Telemetry <span class="text-red-500">*</span></label>
                    <input type="text" name="device_id_telemetry" id="edit_device_id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">ID unik dari perangkat telemetri</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Pos <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pos" id="edit_nama_pos" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tipe Pos <span class="text-red-500">*</span></label>
                    <select name="tipe_pos" id="edit_tipe_pos" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                        <option value="PCH">PCH (Curah Hujan)</option>
                        <option value="PDA">PDA (TMA)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Nomor Pos</label>
                    <input type="text" name="nomor_pos" id="edit_nomor_pos" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sungai / Lokasi</label>
                <input type="text" name="sungai" id="edit_sungai" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Wilayah Sungai</label>
                <select name="wilayah_sungai" id="edit_wilayah_sungai" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                    <option value="">Pilih Wilayah Sungai</option>
                    <option value="MESUJI-TULANG BAWANG">MESUJI-TULANG BAWANG</option>
                    <option value="SEPUTIH-SEKAMPUNG">SEPUTIH-SEKAMPUNG</option>
                    <option value="SEMANGKA">SEMANGKA</option>
                </select>
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
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">NWL (Normal Water Level) - Khusus PDA</label>
                <input type="number" step="any" name="nwl" id="edit_nwl" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-brandyellow focus:border-transparent">
                <p class="text-[10px] text-slate-400 mt-1">Tinggi muka air normal (meter)</p>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" onclick="closeModal('modalEditTelemetri')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-sm transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL HAPUS DEVICE ID -->
<!-- ========================================== -->
<div id="modalHapusTelemetri" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="if(event.target===this) closeModal('modalHapusTelemetri')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-center text-darkblue mb-2">Hapus Device ID?</h3>
            <p class="text-center text-slate-500 text-sm mb-6">Apakah Anda yakin ingin menghapus device ID dari <strong id="hapus_nama_telemetri" class="text-darkblue"></strong>? Data telemetri yang sudah masuk akan tetap tersimpan.</p>
            <div class="flex justify-center gap-3">
                <button onclick="closeModal('modalHapusTelemetri')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-all">Batal</button>
                <a href="#" id="hapus_link_telemetri" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-bold rounded-lg text-sm transition-all">Hapus Device ID</a>
            </div>
        </div>
    </div>
</div>

<style>
    #modalEditTelemetri, #modalHapusTelemetri {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    #modalEditTelemetri.show, #modalHapusTelemetri.show {
        opacity: 1 !important;
        visibility: visible !important;
    }
    #modalEditTelemetri .bg-white, #modalHapusTelemetri .bg-white {
        transition: transform 0.3s ease;
    }
    #modalEditTelemetri.show .bg-white, #modalHapusTelemetri.show .bg-white {
        transform: scale(1) !important;
    }
    #modalEditTelemetri::-webkit-scrollbar {
        width: 4px;
    }
    #modalEditTelemetri::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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
// EDIT TELEMETRI
// ==========================================
function openEditTelemetri(data) {
    document.getElementById('edit_id_pos').value = data.id_pos;
    document.getElementById('edit_device_id').value = data.device_id_telemetry || '';
    document.getElementById('edit_nama_pos').value = data.nama_pos || '';
    document.getElementById('edit_tipe_pos').value = data.tipe_pos || 'PCH';
    document.getElementById('edit_nomor_pos').value = data.nomor_pos || '';
    document.getElementById('edit_sungai').value = data.sungai || '';
    document.getElementById('edit_wilayah_sungai').value = data.wilayah_sungai || '';
    document.getElementById('edit_lat').value = data.lat || '';
    document.getElementById('edit_lng').value = data.lng || '';
    document.getElementById('edit_nwl').value = data.nwl || '';
    
    openModal('modalEditTelemetri');
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