<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Petugas</h1>
        <p class="text-slate-500 text-sm mt-1">Manajemen akun petugas pos monitoring</p>
    </div>
    <button onclick="openModalTambah()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Petugas
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
        <input type="text" id="searchPetugas" placeholder="Cari nama, username, atau email..." class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50">
    </div>
    <div class="flex gap-2">
        <select id="filterStatus" class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-slate-50 font-medium text-slate-600">
            <option value="all">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
        </select>
        <select id="filterPos" class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-slate-50 font-medium text-slate-600">
            <option value="all">Semua Pos</option>
            <option value="ada">Sudah Ditugaskan</option>
            <option value="tidak">Belum Ditugaskan</option>
        </select>
    </div>
</div>

<!-- Tabel Data -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Petugas</h3>
        <span class="text-[10px] text-slate-400 font-bold" id="totalCounter"><?= count($petugas_list) ?> PETUGAS</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-xs" id="petugasTable">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left font-bold w-10">#</th>
                    <th class="px-5 py-3 text-left font-bold">Nama / Username</th>
                    <th class="px-5 py-3 text-left font-bold">Email</th>
                    <th class="px-5 py-3 text-left font-bold">Pos Tugas</th>
                    <th class="px-5 py-3 text-center font-bold w-24">Status</th>
                    <th class="px-5 py-3 text-left font-bold">Login Terakhir</th>
                    <th class="px-5 py-3 text-center font-bold w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(!empty($petugas_list)): $no = 1; foreach($petugas_list as $p): ?>
                <tr class="hover:bg-slate-50/50 transition-colors petugas-row" 
                    data-status="<?= $p->status ?>" 
                    data-pos="<?= !empty($p->nama_pos) ? 'ada' : 'tidak' ?>">
                    <td class="px-5 py-3.5 text-slate-400"><?= $no++ ?></td>
                    
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-brandyellow/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-darkblue font-bold text-[11px]"><?= strtoupper(substr($p->nama_lengkap, 0, 2)) ?></span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-darkblue truncate"><?= $p->nama_lengkap ?></p>
                                <p class="text-[10px] text-slate-400 font-mono">@<?= $p->username ?></p>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-5 py-3.5 text-slate-500">
                        <?= $p->email ?: '<span class="text-slate-300 italic">Belum diisi</span>' ?>
                    </td>
                    
                    <td class="px-5 py-3.5">
                        <?php if($p->nama_pos): ?>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($p->tipe_pos == 'PCH') ? 'bg-blue-500' : 'bg-green-500' ?>"></span>
                                <div>
                                    <p class="font-semibold text-slate-700"><?= $p->nama_pos ?></p>
                                    <p class="text-[10px] text-slate-400"><?= $p->nomor_pos ?> · <?= $p->tipe_pos ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-orange-500 text-[10px] font-bold bg-orange-50 px-2 py-1 rounded-full">Belum Ditugaskan</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold <?= ($p->status == 'aktif') ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= ($p->status == 'aktif') ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' ?>"></span>
                            <?= ucfirst($p->status) ?>
                        </span>
                    </td>
                    
                    <td class="px-5 py-3.5 text-slate-500 text-[11px]">
                        <?php if($p->last_login): ?>
                            <div>
                                <p><?= date('d M Y', strtotime($p->last_login)) ?></p>
                                <p class="text-[10px] text-slate-400"><?= date('H:i', strtotime($p->last_login)) ?> WIB</p>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-300 italic text-[10px]">Belum pernah</span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="openModalEdit(
                                '<?= $p->id_user ?>',
                                '<?= addslashes($p->nama_lengkap) ?>',
                                '<?= addslashes($p->username) ?>',
                                '<?= addslashes($p->email ?? '') ?>',
                                '<?= $p->id_pos ?>'
                            )" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Edit Petugas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            
                            <?php if($p->status == 'aktif'): ?>
                                <a href="<?= base_url('admin/nonaktifkan_petugas/'.$p->id_user) ?>" onclick="return confirm('Nonaktifkan petugas ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-orange-50 hover:text-orange-600 transition-colors" title="Nonaktifkan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('admin/aktifkan_petugas/'.$p->id_user) ?>" onclick="return confirm('Aktifkan petugas ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Aktifkan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?= base_url('admin/hapus_petugas/'.$p->id_user) ?>" onclick="return confirm('HAPUS permanen petugas ini?\n\nSemua data akan dihapus dan tidak dapat dikembalikan.')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" class="px-5 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-slate-400 font-semibold">Belum ada petugas terdaftar</p>
                                <p class="text-slate-300 text-[11px] mt-1">Klik tombol "Tambah Petugas" untuk memulai</p>
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
<!-- MODAL TAMBAH PETUGAS -->
<!-- ============================================ -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Tambah Petugas Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Lengkapi data petugas di bawah ini</p>
            </div>
            <button onclick="closeModalTambah()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('admin/tambah_petugas', ['class' => 'p-5 space-y-4']) ?>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_lengkap" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="Contoh: Ahmad Fauzi" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="Minimal 4 karakter" required minlength="4">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" name="email" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="contoh@email.com">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="Minimal 8 karakter" required minlength="8">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pos Tugas <span class="text-red-500">*</span></label>
                <select name="id_pos" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
                    <option value="">-- Pilih Pos --</option>
                    <?php foreach($pos_list as $pos): ?>
                    <option value="<?= $pos->id_pos ?>"><?= $pos->nomor_pos ?> - <?= $pos->nama_pos ?> (<?= $pos->tipe_pos ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalTambah()" class="flex-1 px-4 py-3 border border-slate-300 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">Simpan Petugas</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT PETUGAS -->
<!-- ============================================ -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Edit Data Petugas</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi petugas</p>
            </div>
            <button onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('admin/edit_petugas', ['class' => 'p-5 space-y-4']) ?>
            <input type="hidden" name="id_user" id="edit_id_user">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_lengkap" id="edit_nama_lengkap" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" id="edit_username" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required minlength="4">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" name="email" id="edit_email" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="contoh@email.com">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Password <span class="text-slate-400 font-normal text-[10px]">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" placeholder="••••••••" minlength="8">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pos Tugas <span class="text-red-500">*</span></label>
                <select name="id_pos" id="edit_id_pos" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
                    <option value="">-- Pilih Pos --</option>
                    <?php foreach($pos_list as $pos): ?>
                    <option value="<?= $pos->id_pos ?>"><?= $pos->nomor_pos ?> - <?= $pos->nama_pos ?> (<?= $pos->tipe_pos ?>)</option>
                    <?php endforeach; ?>
                </select>
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

function openModalEdit(id, nama, username, email, id_pos) {
    document.getElementById('edit_id_user').value = id;
    document.getElementById('edit_nama_lengkap').value = nama;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_id_pos').value = id_pos;
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
document.getElementById('searchPetugas').addEventListener('input', applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);
document.getElementById('filterPos').addEventListener('change', applyFilters);

function applyFilters() {
    const query = document.getElementById('searchPetugas').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const posFilter = document.getElementById('filterPos').value;
    
    const rows = document.querySelectorAll('.petugas-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const status = row.dataset.status;
        const pos = row.dataset.pos;
        
        const matchSearch = text.includes(query);
        const matchStatus = statusFilter === 'all' || status === statusFilter;
        const matchPos = posFilter === 'all' || pos === posFilter;
        
        if (matchSearch && matchStatus && matchPos) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('totalCounter').textContent = visibleCount + ' PETUGAS';
}
</script>