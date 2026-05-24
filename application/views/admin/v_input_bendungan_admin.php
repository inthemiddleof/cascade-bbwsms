<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Input Manual Bendungan (Admin)</h1>
    <p class="text-slate-500 text-sm mt-1">Gunakan form ini untuk melakukan input data bendungan secara manual.</p>
</div>

<?php if($this->session->flashdata('success')): ?>
<div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <?= $this->session->flashdata('success') ?>
</div>
<?php endif; ?>

<form action="<?= base_url('admin/simpan_bendungan') ?>" method="POST">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5 space-y-4">
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Pilih Bendungan / Pos:</label>
            <select name="id_pos" class="w-full px-4 py-3 border-2 border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium" required>
                <?php if(count($list_pos) == 1): ?>
                    <option value="<?= $list_pos[0]->id_pos ?>" selected><?= $list_pos[0]->nama_pos ?></option>
                <?php else: ?>
                    <option value="">-- Pilih Bendungan --</option>
                    <?php foreach($list_pos as $lp): ?>
                        <option value="<?= $lp->id_pos ?>"><?= $lp->nama_pos ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal Pengukuran:</label>
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 border-2 border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium" required>
        </div>
    </div>
    
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border-2 border-amber-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-amber-200 bg-amber-50">
                <h3 class="font-bold text-amber-700 text-sm uppercase tracking-wider">Data Tetap Bendungan</h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">NWL (m)</label>
                        <input type="number" step="any" name="nwl" class="w-full px-4 py-3 border-2 border-amber-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 bg-white font-semibold" placeholder="274.00">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Vol NWL (jt.m³)</label>
                        <input type="number" step="any" name="nwl_volume" class="w-full px-4 py-3 border-2 border-amber-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 bg-white font-semibold" placeholder="687.767">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas NWL (km²)</label>
                        <input type="number" step="any" name="nwl_luas" class="w-full px-4 py-3 border-2 border-amber-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 bg-white font-semibold" placeholder="21.100">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-darkblue">
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Data Pengukuran Harian</h3>
            </div>
            <div class="p-5 space-y-5">
                <div class="border border-slate-200 rounded-xl p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                            <input type="number" step="any" name="rain" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi / TMA (m)</label>
                            <input type="number" step="any" name="elevasi" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white" placeholder="264.98">
                        </div>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div><label class="block text-[10px] font-bold text-slate-500 mb-1.5">Volume (jt.m³)</label><input type="number" step="any" name="volume" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="514.541"></div>
                        <div><label class="block text-[10px] font-bold text-slate-500 mb-1.5">Luas Genangan</label><input type="number" step="any" name="luas" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="17.370"></div>
                        <div><label class="block text-[10px] font-bold text-slate-500 mb-1.5">Inflow (m³/s)</label><input type="number" step="any" name="inflow" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="25.853"></div>
                        <div><label class="block text-[10px] font-bold text-slate-500 mb-1.5">Total Outflow</label><input type="number" step="any" name="total_outflow" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div>
                    </div>
                </div>
                

                <button type="submit" class="w-full bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold py-4 rounded-2xl text-sm transition-all shadow-lg active:scale-[0.98] flex items-center justify-center gap-2">
                    Simpan Data Bendungan
                </button>
            </div>
        </div>
    </div>
</form>