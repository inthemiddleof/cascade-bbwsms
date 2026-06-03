<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-slate-800">Input Laporan</h1>
    <p class="text-slate-500 text-sm mt-1">
        Pos: <span class="font-bold text-darkblue"><?= htmlspecialchars($pos->nama_pos) ?> (<?= $pos->tipe_pos ?>)</span>
    </p>
</div>

<!-- Dropdown Pilih Pos -->
<?php if(count($daftar_pos_petugas) > 1): ?>
<div class="bg-white rounded-2xl border border-slate-200 p-4 mb-4">
    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Pos</label>
    <select onchange="window.location='<?= base_url('petugas/input') ?>?pos='+this.value+'&tanggal=<?= $tanggal ?>'" 
            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
        <?php foreach($daftar_pos_petugas as $p): ?>
        <option value="<?= $p->id_pos ?>" <?= $p->id_pos == $id_pos_active ? 'selected' : '' ?>>
            <?= htmlspecialchars($p->nama_pos) ?> (<?= $p->tipe_pos ?>)
        </option>
        <?php endforeach; ?>
    </select>
</div>
<?php endif; ?>

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

<!-- Card: Tanggal -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4">
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            <span class="text-sm font-bold text-slate-600 uppercase tracking-wider">Tanggal Pengukuran</span>
        </div>
        <input type="date" value="<?= $tanggal ?>" 
               onchange="window.location='<?= base_url('petugas/input') ?>?pos=<?= $id_pos_active ?>&tanggal='+this.value" 
               class="px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium cursor-pointer">
    </div>
</div>

<form action="<?= base_url('petugas/simpan') ?>" method="POST" id="form-input" onsubmit="return validateForm()">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
    <input type="hidden" name="id_pos" value="<?= $id_pos_active ?>">
    
    <div class="space-y-4">
        
        <!-- Data Pengukuran -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-darkblue flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <?php if($pos->tipe_pos == 'PCH'): ?>
                    <svg class="w-4 h-4 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    <?php else: ?>
                    <svg class="w-4 h-4 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="font-bold text-white text-sm">Data Pengukuran Harian</h3>
                </div>
            </div>
            
            <div class="p-5 space-y-5">
                
                <?php if($pos->tipe_pos == 'PCH'): ?>
                <!-- Curah Hujan -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        Curah Hujan
                    </p>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nilai Curah Hujan (mm)</label>
                        <input type="number" step="any" name="rain" id="input-value" required min="0"
                               class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-semibold" 
                               placeholder="0"
                               oninput="validateMin(this, 0)">
                        <p class="text-[10px] text-slate-400 mt-1">Satuan: milimeter (mm)</p>
                    </div>
                </div>
                <?php else: ?>
                <!-- TMA -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        Tinggi Muka Air
                    </p>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nilai TMA (m)</label>
                        <input type="number" step="any" name="wlevel" id="input-value" required min="0"
                               class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-semibold" 
                               placeholder="0"
                               oninput="validateMin(this, 0)">
                        <p class="text-[10px] text-slate-400 mt-1">Satuan: meter (m)</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Keterangan -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" maxlength="500"
                              class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white resize-none" 
                              placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                </div>
            </div>
        </div>

        <!-- Info & Submit -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-blue-700">Jam input akan tercatat otomatis: <b class="text-blue-900"><?= date('H:i') ?> WIB</b></p>
        </div>

        <button type="submit" id="submit-btn" class="w-full bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold py-4 rounded-2xl text-sm transition-all shadow-lg shadow-brandyellow/20 active:scale-[0.98] flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span id="btn-text">Simpan Data</span>
            <span id="btn-loading" class="hidden">Menyimpan...</span>
        </button>
    </div>
</form>

<script>
    setTimeout(() => {
        document.getElementById('alert-success')?.style.display = 'none';
        document.getElementById('alert-error')?.style.display = 'none';
    }, 5000);

    function validateMin(input, min) {
        const val = parseFloat(input.value);
        if (isNaN(val)) return;
        if (val < min) input.value = min;
    }

    function validateForm() {
        const value = document.getElementById('input-value').value;
        if (!value || parseFloat(value) < 0) {
            alert('Nilai harus diisi!');
            return false;
        }
        
        if (!confirm('Simpan data ini?')) return false;
        
        document.getElementById('btn-text').classList.add('hidden');
        document.getElementById('btn-loading').classList.remove('hidden');
        document.getElementById('submit-btn').disabled = true;
        return true;
    }
</script>