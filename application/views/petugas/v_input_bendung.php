<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Input Laporan Bendung</h1>
        <p class="text-slate-500 text-sm mt-1">
            Bendung: <span class="font-bold text-darkblue"><?= htmlspecialchars($pos->nama_pos) ?></span>
        </p>
    </div>
</div>

<!-- Dropdown Pilih Pos -->
<?php if(count($daftar_pos_petugas) > 1): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4">
    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Bendung</label>
    <select onchange="window.location='<?= base_url('petugas/input_bendung') ?>?pos='+this.value+'&tanggal=<?= $tanggal ?>'" 
            class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium">
        <?php foreach($daftar_pos_petugas as $p): ?>
            <?php if($p->is_bendung == 1): ?>
            <option value="<?= $p->id_pos ?>" <?= $p->id_pos == $id_pos_active ? 'selected' : '' ?>>
                <?= htmlspecialchars($p->nama_pos) ?>
            </option>
            <?php endif; ?>
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
               onchange="window.location='<?= base_url('petugas/input_bendung') ?>?pos=<?= $id_pos_active ?>&tanggal='+this.value" 
               class="px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium cursor-pointer">
    </div>
</div>

<form action="<?= base_url('petugas/simpan_bendung') ?>" method="POST" id="form-bendung">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
    <input type="hidden" name="id_pos" value="<?= $id_pos_active ?>">
    
    <div class="space-y-4">
        
        <!-- Data Pengukuran Harian -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-darkblue flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-white text-sm">Data Pengukuran Harian</h3>
                    <p class="text-blue-200 text-[10px]">Isi sesuai dengan data yang tersedia, kosongkan jika tidak ada</p>
                </div>
            </div>
            
            <div class="p-5 space-y-5">
                
                <!-- Info Penting -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-2">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs font-bold text-amber-700">Catatan: Semua field bersifat opsional</p>
                        <p class="text-[10px] text-amber-600 mt-0.5">Setiap bendung memiliki karakteristik data yang berbeda-beda. 
                        <span class="font-medium">Isi hanya field yang tersedia</span> dan biarkan kosong jika data tidak tersedia.</p>
                    </div>
                </div>
                
                <!-- Hidrologi Dasar -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>Hidrologi Dasar
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                            <input type="number" step="any" name="rain" min="0" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-semibold" placeholder="0" oninput="validateMin(this, 0)">
                            <p class="text-[10px] text-slate-400 mt-1">Satuan: milimeter (mm)</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Air thd Mercu (cm)</label>
                            <input type="number" step="any" name="elevasi_mercu" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-semibold" placeholder="0.00">
                            <p class="text-[10px] text-slate-400 mt-1">Satuan: centimeter (cm)</p>
                        </div>
                    </div>
                </div>

                <!-- Parameter Debit (SESUAI STRUKTUR TERBARU) -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>Parameter Debit
                    </p>
                    <p class="text-[10px] text-slate-400 mb-3">Isi hanya parameter debit yang tersedia di bendung ini</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Q-Total (m³/dt)</label>
                            <input type="number" step="any" name="q_total" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Q-FC1 (m³/dt)</label>
                            <input type="number" step="any" name="q_fc1" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Q-FC2 (m³/dt)</label>
                            <input type="number" step="any" name="q_fc2" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Q-Sal. Induk (m³/dt)</label>
                            <input type="number" step="any" name="q_sal_induk" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Q-Limpas (m³/dt)</label>
                            <input type="number" step="any" name="q_limpas" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Q-Sungai (m³/dt)</label>
                            <input type="number" step="any" name="q_sungai" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Q-SPAM KPBU (m³/dt)</label>
                            <input type="number" step="any" name="q_spam_kpbu" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Sluice Gate</label>
                            <input type="number" step="any" name="sluice_gate" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Bukaan Pintu (m)</label>
                            <input type="number" step="any" name="bukaan_pintu" min="0" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0" oninput="validateMin(this, 0)">
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" maxlength="500" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white resize-none" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                </div>
            </div>
        </div>

        <!-- Info & Submit -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-blue-700">Jam input akan tercatat otomatis: <b class="text-blue-900"><?= date('H:i') ?> WIB</b></p>
        </div>

        <button type="submit" class="w-full bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold py-4 rounded-xl text-sm transition-all shadow-lg shadow-brandyellow/20 active:scale-[0.98] flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Simpan Data Bendung
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
</script>