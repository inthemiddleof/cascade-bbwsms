<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Kelola Admin Wilayah</h1>
        <p class="text-slate-500 text-sm mt-1">Manajemen akun admin wilayah dan hak akses infrastruktur</p>
    </div>
    <button type="button" onclick="openModalTambah()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Admin
    </button>
</div>

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
        <input type="text" id="searchAdmin" placeholder="Cari nama atau username..." class="w-full pl-10 pr-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
    </div>
    <select id="filterStatus" class="px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium text-slate-600">
        <option value="all">Semua Status</option>
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
    </select>
</div>

<!-- Tabel -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Admin Wilayah</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full" id="totalCounter"><?= count($admin_list) ?> Admin</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="adminTable">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs">
                <tr>
                    <th class="px-5 py-3 text-left font-bold w-10">#</th>
                    <th class="px-5 py-3 text-left font-bold">Nama / Username</th>
                    <th class="px-5 py-3 text-left font-bold">Cakupan Wilayah</th>
                    <th class="px-5 py-3 text-center font-bold w-24">Status</th>
                    <th class="px-5 py-3 text-center font-bold w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($admin_list)): $no = 1; foreach($admin_list as $a): ?>
                <tr class="hover:bg-slate-50 transition-colors admin-row" data-nama="<?= strtolower($a->nama_lengkap) ?>" data-username="<?= strtolower($a->username) ?>" data-status="<?= $a->status ?>">
                    <td class="px-5 py-3.5 text-slate-400 index-number"><?= $no++ ?></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-darkblue flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold text-[10px]"><?= strtoupper(substr($a->nama_lengkap, 0, 2)) ?></span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-darkblue text-xs"><?= htmlspecialchars($a->nama_lengkap) ?></p>
                                <p class="text-[10px] text-slate-400">@<?= htmlspecialchars($a->username) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <?php if(!empty($a->nama_pos) && $a->nama_pos != 'Belum Ditugaskan'): ?>
                            <div class="flex flex-wrap gap-1">
                                <?php $arr = explode(', ', $a->nama_pos); foreach(array_slice($arr, 0, 3) as $n): ?>
                                <span class="inline-flex px-2 py-0.5 bg-purple-50 text-purple-600 font-medium rounded-md text-[10px]"><?= trim($n) ?></span>
                                <?php endforeach; if(count($arr) > 3): ?>
                                <span class="text-[10px] text-slate-400">+<?= count($arr)-3 ?> lagi</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-[10px] text-slate-400">Belum ditugaskan</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold <?= ($a->status == 'aktif') ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' ?>">
                            <?= ucfirst($a->status) ?>
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEdit('<?= $a->id_user ?>','<?= htmlspecialchars($a->nama_lengkap, ENT_QUOTES) ?>','<?= htmlspecialchars($a->username, ENT_QUOTES) ?>','<?= htmlspecialchars($a->email ?? '', ENT_QUOTES) ?>','<?= $a->id_pos ?? '' ?>')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <?php if($a->status == 'aktif'): ?>
                            <a href="<?= base_url('admin/nonaktifkan_admin/'.$a->id_user) ?>" onclick="return confirm('Nonaktifkan admin ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-orange-50 hover:text-orange-600 transition-colors" title="Nonaktifkan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </a>
                            <?php else: ?>
                            <a href="<?= base_url('admin/aktifkan_admin/'.$a->id_user) ?>" onclick="return confirm('Aktifkan admin ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Aktifkan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </a>
                            <?php endif; ?>
                            <a href="<?= base_url('admin/hapus_admin/'.$a->id_user) ?>" onclick="return confirm('HAPUS permanen admin ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400"><p class="text-sm font-medium">Belum ada admin wilayah terdaftar</p></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Tambah Admin Wilayah</h3>
            <button type="button" onclick="closeModalTambah()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('admin/tambah_admin') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label><input type="text" name="nama_lengkap" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="Nama lengkap" required></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Username <span class="text-red-500">*</span></label><input type="text" name="username" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="Minimal 4 karakter" required minlength="4"></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Email</label><input type="email" name="email" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="contoh@email.com"></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Password <span class="text-red-500">*</span></label><input type="password" name="password" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="Minimal 8 karakter" required minlength="8"></div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Cakupan Wilayah <span class="text-red-500">*</span></label>
                <div class="border border-slate-200 rounded-xl p-3 max-h-48 overflow-y-auto space-y-1">
                    <?php if(!empty($pos_list)): foreach($pos_list as $pos): ?>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="id_pos[]" value="<?= $pos->id_pos ?>" class="w-4 h-4 rounded text-brandyellow focus:ring-brandyellow border-slate-300">
                        <span class="text-xs text-slate-700"><?= htmlspecialchars($pos->nama_pos) ?> (<?= $pos->tipe_pos ?>) <?= $pos->is_bendungan ? '🔹' : '' ?></span>
                    </label>
                    <?php endforeach; else: ?>
                    <p class="text-xs text-slate-400 text-center py-4">Tidak ada pos tersedia</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModalTambah()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button><button type="submit" class="flex-1 px-4 py-3 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md">Simpan</button></div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Edit Admin Wilayah</h3>
            <button type="button" onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url('superadmin/edit_admin') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_user" id="edit_id_user">
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label><input type="text" name="nama_lengkap" id="edit_nama" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" required></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Username <span class="text-red-500">*</span></label><input type="text" name="username" id="edit_username" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" required minlength="4"></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Email</label><input type="email" name="email" id="edit_email" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white"></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Password <span class="text-slate-400 text-[10px]">(kosongkan jika tidak diubah)</span></label><input type="password" name="password" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="••••••••" minlength="8"></div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Cakupan Wilayah <span class="text-red-500">*</span></label>
                <div class="border border-slate-200 rounded-xl p-3 max-h-48 overflow-y-auto space-y-1">
                    <?php if(!empty($pos_list)): foreach($pos_list as $pos): ?>
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="id_pos[]" value="<?= $pos->id_pos ?>" class="edit-pos-checkbox w-4 h-4 rounded text-brandyellow focus:ring-brandyellow border-slate-300">
                        <span class="text-xs text-slate-700"><?= htmlspecialchars($pos->nama_pos) ?> (<?= $pos->tipe_pos ?>) <?= $pos->is_bendungan ? '🔹' : '' ?></span>
                    </label>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModalEdit()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button><button type="submit" class="flex-1 px-4 py-3 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md">Simpan</button></div>
        </form>
    </div>
</div>

<script>
    setTimeout(function(){var s=document.getElementById('alert-success');var e=document.getElementById('alert-error');if(s)s.style.display='none';if(e)e.style.display='none';},5000);
    function openModalTambah(){document.getElementById('modalTambah').style.display='flex';}
    function closeModalTambah(){document.getElementById('modalTambah').style.display='none';}
    function openModalEdit(id,nama,username,email,id_pos){
        document.getElementById('edit_id_user').value=id;document.getElementById('edit_nama').value=nama;document.getElementById('edit_username').value=username;document.getElementById('edit_email').value=email||'';
        document.querySelectorAll('.edit-pos-checkbox').forEach(function(cb){cb.checked=false;});
        if(id_pos&&id_pos.trim()!==''){var ids=id_pos.split(',').map(function(i){return i.trim();});document.querySelectorAll('.edit-pos-checkbox').forEach(function(cb){if(ids.indexOf(cb.value)!==-1)cb.checked=true;});}
        document.getElementById('modalEdit').style.display='flex';
    }
    function closeModalEdit(){document.getElementById('modalEdit').style.display='none';}
    document.getElementById('modalTambah').addEventListener('click',function(e){if(e.target===this)closeModalTambah();});
    document.getElementById('modalEdit').addEventListener('click',function(e){if(e.target===this)closeModalEdit();});
    document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeModalTambah();closeModalEdit();}});
    document.getElementById('searchAdmin').addEventListener('input',applyFilters);
    document.getElementById('filterStatus').addEventListener('change',applyFilters);
    function applyFilters(){
        var q=document.getElementById('searchAdmin').value.toLowerCase();
        var s=document.getElementById('filterStatus').value;
        var rows=document.querySelectorAll('.admin-row');
        var c=0;
        rows.forEach(function(r){
            var n=r.getAttribute('data-nama')||'',u=r.getAttribute('data-username')||'',st=r.getAttribute('data-status')||'';
            if((q===''||n.indexOf(q)!==-1||u.indexOf(q)!==-1)&&(s==='all'||st===s)){r.style.display='';c++;r.querySelector('.index-number').textContent=c;}else{r.style.display='none';}
        });
        document.getElementById('totalCounter').textContent=c+' Admin';
    }
</script>