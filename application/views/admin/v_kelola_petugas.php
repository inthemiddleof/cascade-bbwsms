<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Admin Wilayah</h1>
        <p class="text-slate-500 text-sm mt-1">Manajemen akun admin wilayah dan hak akses infrastruktur</p>
        <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-900 text-white text-[10px] font-bold rounded-lg uppercase tracking-wider">
            Otoritas: <?= $this->session->userdata('role') === 'superadmin' ? 'Super Admin (Pusat)' : 'Admin Wilayah' ?>
        </div>
    </div>
    <button onclick="openModalTambah()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah User
    </button>
</div>

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
            <option value="all">Semua Akses</option>
            <option value="ada">Memiliki Akses Pos</option>
            <option value="tidak">Belum Memiliki Akses</option>
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Admin Wilayah</h3>
        <span class="text-[10px] text-slate-400 font-bold" id="totalCounter"><?= count($petugas_list) ?> ADMIN</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-xs" id="petugasTable">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left font-bold w-10">#</th>
                    <th class="px-5 py-3 text-left font-bold">Nama / Username</th>
                    <th class="px-5 py-3 text-left font-bold">Email</th>
                    <th class="px-5 py-3 text-left font-bold w-1/3">Cakupan Wilayah Tugas</th>
                    <th class="px-5 py-3 text-center font-bold w-24">Status</th>
                    <th class="px-5 py-3 text-left font-bold">Login Terakhir</th>
                    <th class="px-5 py-3 text-center font-bold w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(!empty($petugas_list)): $no = 1; foreach($petugas_list as $p): ?>
                <tr class="hover:bg-slate-50/50 transition-colors petugas-row" 
                    data-nama="<?= strtolower(addslashes($p->nama_lengkap)) ?>"
                    data-username="<?= strtolower(addslashes($p->username)) ?>"
                    data-email="<?= strtolower(addslashes($p->email ?? '')) ?>"
                    data-status="<?= $p->status ?>" 
                    data-pos="<?= !empty($p->list_id_pos) ? 'ada' : 'tidak' ?>">
                    <td class="px-5 py-3.5 text-slate-400 index-number"><?= $no++ ?></td>
                    
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
                        <?php if(!empty($p->list_nama_pos)): ?>
                            <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto pr-1">
                                <?php 
                                    $nama_pos_arr = explode(',', $p->list_nama_pos);
                                    foreach($nama_pos_arr as $nama_pos): 
                                ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 text-slate-700 font-medium rounded-md border border-slate-200 text-[10px]">
                                        <span class="w-1 h-1 rounded-full bg-blue-500"></span>
                                        <?= trim($nama_pos) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-orange-500 text-[10px] font-bold bg-orange-50 px-2 py-1 rounded-full">Belum Ada Akses Wilayah</span>
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
                                '<?= $p->list_id_pos ?? '' ?>' 
                            )" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Edit Admin">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            
                            <?php if($p->status == 'aktif'): ?>
                                <a href="<?= base_url('admin/nonaktifkan_petugas/'.$p->id_user) ?>" onclick="return confirm('Nonaktifkan admin ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-orange-50 hover:text-orange-600 transition-colors" title="Nonaktifkan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('admin/aktifkan_petugas/'.$p->id_user) ?>" onclick="return confirm('Aktifkan admin ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Aktifkan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?= base_url('admin/hapus_petugas/'.$p->id_user) ?>" onclick="return confirm('HAPUS permanen admin ini?\n\nSemua hak akses wilayah juga akan dihapus.')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr id="emptyRow">
                    <td colspan="7" class="px-5 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-slate-400 font-semibold">Belum ada admin terdaftar</p>
                                <p class="text-slate-300 text-[11px] mt-1">Klik tombol "Tambah Admin" untuk memulai</p>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <tr id="noResultsRow" class="hidden">
                    <td colspan="7" class="px-5 py-12 text-center text-slate-400 italic">
                        Tidak ada admin yang cocok dengan filter pencarian Anda.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Tambah Admin Wilayah</h3>
                <p class="text-xs text-slate-400 mt-0.5">Lengkapi data akun dan pilih cakupan wilayah</p>
            </div>
            <button onclick="closeModalTambah()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('admin/tambah_petugas', ['class' => 'p-5 space-y-4']) ?>
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
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
    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Akses Otoritas Wilayah <span class="text-red-500">*</span></label>
    
    <?php if ($this->session->userdata('role') === 'superadmin'): ?>
        <p class="text-[10px] text-amber-600 mb-2">*Superadmin: Pilih satu atau beberapa bendungan/pos yang dikelola admin ini.</p>
        <div class="relative mb-2">
            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="searchPosTambah" placeholder="Cari nama atau nomor pos..." class="w-full pl-8 pr-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium text-slate-600">
        </div>

        <div class="border border-slate-200 rounded-xl bg-slate-50 p-3 max-h-48 overflow-y-auto space-y-2">
            <?php if(isset($pos_list) && !empty($pos_list)): foreach($pos_list as $pos): ?>
            <label class="pos-item-tambah flex items-start gap-3 p-2 rounded-lg bg-white border border-slate-100 shadow-sm cursor-pointer hover:bg-slate-50/50 transition-colors"
                   data-nama="<?= strtolower(addslashes($pos->nama_pos)) ?>"
                   data-nomor="<?= strtolower(addslashes($pos->nomor_pos ?? '')) ?>">
                <input type="checkbox" name="id_pos[]" value="<?= $pos->id_pos ?>" class="mt-0.5 w-4 h-4 rounded text-brandyellow focus:ring-brandyellow border-slate-300">
                <div class="text-xs">
                    <p class="font-semibold text-slate-700 name-target"><?= $pos->nama_pos ?></p>
                    <p class="text-[10px] text-slate-400"><?= $pos->nomor_pos ?> · <?= $pos->tipe_pos ?></p>
                </div>
            </label>
            <?php endforeach; else: ?>
                <p class="text-xs text-slate-400 text-center py-4">Tidak ada data pos tersedia</p>
            <?php endif; ?>
            <p id="noResultsTambah" class="text-xs text-slate-400 italic text-center py-2 hidden">Pos tidak ditemukan</p>
        </div>
    <?php else: ?>
        <p class="text-[10px] text-blue-600 mb-2">*Admin Wilayah: Petugas lapangan hanya boleh ditempatkan di 1 pos kontrol.</p>
        <select name="id_pos[]" required class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium text-slate-600">
            <option value="">-- Pilih Pos Penempatan --</option>
            <?php if(isset($pos_list) && !empty($pos_list)): foreach($pos_list as $pos): ?>
                <option value="<?= $pos->id_pos ?>">
                    <?= $pos->nama_pos ?> (<?= $pos->nomor_pos ? $pos->nomor_pos . ' · ' : '' ?><?= $pos->tipe_pos ?>)
                </option>
            <?php endforeach; else: ?>
                <option value="" disabled>Data pos tidak ditemukan / kosong</option>
            <?php endif; ?>
        </select>
    <?php endif; ?>
</div>
            
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalTambah()" class="flex-1 px-4 py-3 border border-slate-300 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">Simpan Admin</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Edit Data Admin</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi dan hak akses pos</p>
            </div>
            <button onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('admin/edit_petugas', ['class' => 'p-5 space-y-4']) ?>
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
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
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Akses Otoritas Wilayah <span class="text-red-500">*</span></label>
                
                <?php if ($this->session->userdata('role') === 'superadmin'): ?>
                    <p class="text-[10px] text-amber-600 mb-2">*Sesuaikan bendungan/pos yang dapat dikelola admin ini.</p>
                    <div class="relative mb-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="searchPosEdit" placeholder="Cari nama atau nomor pos..." class="w-full pl-8 pr-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium text-slate-600">
                    </div>

                    <div class="border border-slate-200 rounded-xl bg-slate-50 p-3 max-h-48 overflow-y-auto space-y-2">
                        <?php if(!empty($pos_list)): foreach($pos_list as $pos): ?>
                        <label class="pos-item-edit flex items-start gap-3 p-2 rounded-lg bg-white border border-slate-100 shadow-sm cursor-pointer hover:bg-slate-50/50 transition-colors"
                               data-nama="<?= strtolower(addslashes($pos->nama_pos)) ?>"
                               data-nomor="<?= strtolower(addslashes($pos->nomor_pos ?? '')) ?>">
                            <input type="checkbox" name="id_pos[]" value="<?= $pos->id_pos ?>" class="edit-pos-checkbox mt-0.5 w-4 h-4 rounded text-brandyellow focus:ring-brandyellow border-slate-300">
                            <div class="text-xs">
                                <p class="font-semibold text-slate-700 name-target"><?= $pos->nama_pos ?></p>
                                <p class="text-[10px] text-slate-400"><?= $pos->nomor_pos ?> · <?= $pos->tipe_pos ?></p>
                            </div>
                        </label>
                        <?php endforeach; endif; ?>
                        <p id="noResultsEdit" class="text-xs text-slate-400 italic text-center py-2 hidden">Pos tidak ditemukan</p>
                    </div>
                <?php else: ?>
                    <p class="text-[10px] text-blue-600 mb-2">*Admin Wilayah: Petugas lapangan hanya boleh ditempatkan di 1 pos kontrol.</p>
                    <select name="id_pos[]" id="edit_id_pos_dropdown" required class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50 font-medium text-slate-600">
                        <option value="">-- Pilih Pos Penempatan --</option>
                        <?php if(!empty($pos_list)): foreach($pos_list as $pos): ?>
                            <option value="<?= $pos->id_pos ?>"><?= $pos->nama_pos ?> (<?= $pos->nomor_pos ?> · <?= $pos->tipe_pos ?>)</option>
                        <?php endforeach; endif; ?>
                    </select>
                <?php endif; ?>
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
function openModalTambah() { 
    const form = document.querySelector('#modalTambah form');
    if(form) form.reset();
    document.getElementById('modalTambah').classList.remove('hidden'); 
    document.getElementById('modalTambah').classList.add('flex'); 
}

function closeModalTambah() { 
    resetPosSearch(); 
    document.getElementById('modalTambah').classList.add('hidden'); 
    document.getElementById('modalTambah').classList.remove('flex'); 
}

function openModalEdit(id, nama, username, email, list_id_pos) {
    document.getElementById('edit_id_user').value = id;
    document.getElementById('edit_nama_lengkap').value = nama;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    
    // Pengecekan apakah element checkbox ada (Untuk Superadmin)
    const checkboxes = document.querySelectorAll('.edit-pos-checkbox');
    if(checkboxes.length > 0) {
        checkboxes.forEach(cb => cb.checked = false);
        if (list_id_pos.trim() !== '') {
            const checkedIds = list_id_pos.split(',').map(item => item.trim());
            checkboxes.forEach(cb => {
                if (checkedIds.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        }
    }

    // Pengecekan jika element dropdown ada (Untuk Admin Wilayah)
    const dropdownEdit = document.getElementById('edit_id_pos_dropdown');
    if(dropdownEdit) {
        dropdownEdit.value = list_id_pos.split(',')[0].trim(); // Ambil ID pertama jika tersimpan multi-data
    }

    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('modalEdit').classList.add('flex');
}

function closeModalEdit() { 
    resetPosSearch(); 
    document.getElementById('modalEdit').classList.add('hidden'); 
    document.getElementById('modalEdit').classList.remove('flex'); 
}

// Close modal on overlay click
document.getElementById('modalTambah').addEventListener('click', function(e) { if (e.target === this) closeModalTambah(); });
document.getElementById('modalEdit').addEventListener('click', function(e) { if (e.target === this) closeModalEdit(); });

// Close on ESC
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeModalTambah(); closeModalEdit(); } });

// Search & Filter Utama (Daftar Petugas) Real-time
document.getElementById('searchPetugas').addEventListener('input', applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);
document.getElementById('filterPos').addEventListener('change', applyFilters);

function applyFilters() {
    const query = document.getElementById('searchPetugas').value.trim().toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const posFilter = document.getElementById('filterPos').value;
    
    const rows = document.querySelectorAll('.petugas-row');
    const noResultsRow = document.getElementById('noResultsRow');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const nama = row.dataset.nama || '';
        const username = row.dataset.username || '';
        const email = row.dataset.email || '';
        const status = row.dataset.status || '';
        const pos = row.dataset.pos || '';
        
        const matchSearch = query === '' || nama.includes(query) || username.includes(query) || email.includes(query);
        const matchStatus = statusFilter === 'all' || status === statusFilter;
        const matchPos = posFilter === 'all' || pos === posFilter;
        
        if (matchSearch && matchStatus && matchPos) {
            row.style.display = '';
            visibleCount++;
            
            const indexCell = row.querySelector('.index-number');
            if(indexCell) indexCell.textContent = visibleCount;
        } else {
            row.style.display = 'none';
        }
    });
    
    if (rows.length > 0) {
        if (visibleCount === 0) {
            noResultsRow.classList.remove('hidden');
        } else {
            noResultsRow.classList.add('hidden');
        }
    }
    
    document.getElementById('totalCounter').textContent = visibleCount + ' ADMIN';
}

// Live Search Pilihan Pos di Modal Tambah (Hanya jalan jika inputnya ada)
const searchPosTambahInput = document.getElementById('searchPosTambah');
if(searchPosTambahInput) {
    searchPosTambahInput.addEventListener('input', function(e) {
        const query = e.target.value.trim().toLowerCase();
        const items = document.querySelectorAll('.pos-item-tambah');
        const noResults = document.getElementById('noResultsTambah');
        let matchCount = 0;

        items.forEach(item => {
            const nama = item.dataset.nama || '';
            const nomor = item.dataset.nomor || '';

            if (nama.includes(query) || nomor.includes(query)) {
                item.style.display = 'flex';
                matchCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (matchCount === 0 && query !== '') {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    });
}

// Live Search Pilihan Pos di Modal Edit (Hanya jalan jika inputnya ada)
const searchPosEditInput = document.getElementById('searchPosEdit');
if(searchPosEditInput) {
    searchPosEditInput.addEventListener('input', function(e) {
        const query = e.target.value.trim().toLowerCase();
        const items = document.querySelectorAll('.pos-item-edit');
        const noResults = document.getElementById('noResultsEdit');
        let matchCount = 0;

        items.forEach(item => {
            const nama = item.dataset.nama || '';
            const nomor = item.dataset.nomor || '';

            if (nama.includes(query) || nomor.includes(query)) {
                item.style.display = 'flex';
                matchCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (matchCount === 0 && query !== '') {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    });
}

// Fungsi Reset Input Search Pos & Tampilkan Kembali Semua List
function resetPosSearch() {
    const inputTambah = document.getElementById('searchPosTambah');
    const inputEdit = document.getElementById('searchPosEdit');
    
    if(inputTambah) {
        inputTambah.value = '';
        document.querySelectorAll('.pos-item-tambah').forEach(el => el.style.display = 'flex');
        document.getElementById('noResultsTambah').classList.add('hidden');
    }
    if(inputEdit) {
        inputEdit.value = '';
        document.querySelectorAll('.pos-item-edit').forEach(el => el.style.display = 'flex');
        document.getElementById('noResultsEdit').classList.add('hidden');
    }
}
</script>