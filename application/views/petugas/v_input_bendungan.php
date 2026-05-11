<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Input Laporan Bendungan</h1>
    <p class="text-slate-500 text-sm mt-1">Pos: <span class="font-bold text-darkblue"><?= $pos->nama_pos ?></span> · <?= date('d M Y', strtotime($tanggal)) ?></p>
</div>

<!-- Alert -->
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

<!-- Pilih Tanggal -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5 flex flex-wrap items-center gap-3">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
        <span class="text-sm font-bold text-slate-600 uppercase tracking-wider">Tanggal Pengukuran</span>
    </div>
    <input type="date" value="<?= $tanggal ?>" onchange="window.location='<?= base_url('petugas/input') ?>?tanggal='+this.value" class="px-4 py-2.5 border-2 border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium">
</div>

<form action="<?= base_url('petugas/simpan_bendungan') ?>" method="POST">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
    
    <div class="space-y-5">
        
        <!-- ============================================ -->
        <!-- CARD 1: Data Tetap Bendungan -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl border-2 border-amber-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-amber-200 bg-amber-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-amber-700 text-sm uppercase tracking-wider">Data Tetap Bendungan</h3>
                        <p class="text-amber-500 text-[10px] mt-0.5">Jarang berubah. Diambil dari data master, ubah hanya jika diperlukan.</p>
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">NWL (Normal Water Level)</label>
                        <input type="number" step="any" name="nwl" value="<?= $pos->nwl ?>" class="w-full px-4 py-3 border-2 border-amber-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 bg-white font-semibold" placeholder="274.00">
                        <p class="text-[10px] text-amber-500 mt-1">Satuan: meter</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Volume NWL</label>
                        <input type="number" step="any" name="nwl_volume" value="<?= $pos->nwl_volume ?>" class="w-full px-4 py-3 border-2 border-amber-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 bg-white font-semibold" placeholder="687.767">
                        <p class="text-[10px] text-amber-500 mt-1">Satuan: jt.m³</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Luas NWL</label>
                        <input type="number" step="any" name="nwl_luas" value="<?= $pos->nwl_luas ?>" class="w-full px-4 py-3 border-2 border-amber-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 bg-white font-semibold" placeholder="21.100">
                        <p class="text-[10px] text-amber-500 mt-1">Satuan: km²</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- CARD 2: Data Pengukuran Harian -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-darkblue">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm uppercase tracking-wider">Data Pengukuran Harian</h3>
                        <p class="text-blue-200 text-[10px] mt-0.5">Data yang diinputkan setiap kali melakukan pengukuran.</p>
                    </div>
                </div>
            </div>
            <div class="p-5 space-y-5">
                
                <!-- Sub: Hidrologi Dasar -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>
                        Hidrologi Dasar
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                            <input type="number" step="any" name="rain" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi / TMA (m)</label>
                            <input type="number" step="any" name="elevasi" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="264.98">
                        </div>
                    </div>
                </div>

                <!-- Sub: Parameter Utama -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>
                        Parameter Utama
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Volume (jt.m³)</label>
                            <input type="number" step="any" name="volume" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="514.541">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Luas Genangan (km²)</label>
                            <input type="number" step="any" name="luas" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="17.370">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Inflow (m³/s)</label>
                            <input type="number" step="any" name="inflow" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="25.853">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Total Outflow (m³/s)</label>
                            <input type="number" step="any" name="total_outflow" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Sub: Outflow & Status Operasional -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-teal-500 rounded-full"></span>
                        Outflow & Status Operasional
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">PLTM (m³/s)</label>
                            <input type="number" step="any" name="pltm" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Spillway (m³/s)</label>
                            <input type="number" step="any" name="spillway" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Tail Water</label>
                            <input type="text" name="tail_water" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="-">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Status PLTA</label>
                            <select name="plta_status" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
                                <option value="">-- Pilih --</option>
                                <option value="on">ON</option>
                                <option value="off">OFF</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1.5">Status Irigasi</label>
                            <select name="irigasi_status" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
                                <option value="">-- Pilih --</option>
                                <option value="on">ON</option>
                                <option value="off">OFF</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sub: Data Rembesan -->
                <div class="border border-slate-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span>
                        Data Rembesan
                    </p>
                    
                    <div class="mb-3">
                        <p class="text-[10px] font-bold text-slate-400 mb-2">SM Chamber (V-Notch)</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[9px] text-slate-400 mb-1">h (cm)</label>
                                <input type="number" step="any" name="rembesan_vnotch_h" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="15.8">
                            </div>
                            <div>
                                <label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label>
                                <input type="number" step="any" name="rembesan_vnotch_q" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="13.694">
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 mb-2">Pump Pit Kiri</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="block text-[9px] text-slate-400 mb-1">h (cm)</label><input type="number" step="any" name="rembesan_pump_pit_l_h" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="4.5"></div>
                                <div><label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label><input type="number" step="any" name="rembesan_pump_pit_l_q" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0.620"></div>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 mb-2">Pump Pit Kanan</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="block text-[9px] text-slate-400 mb-1">h (cm)</label><input type="number" step="any" name="rembesan_pump_pit_r_h" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="6.9"></div>
                                <div><label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label><input type="number" step="any" name="rembesan_pump_pit_r_q" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="1.767"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white resize-none" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                </div>
                
                <!-- Info Jam -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-blue-700">Jam input akan tercatat otomatis: <b class="text-blue-900"><?= date('H:i') ?> WIB</b></p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold py-4 rounded-2xl text-sm transition-all shadow-lg shadow-brandyellow/20 active:scale-[0.98] flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Simpan Data Bendungan
        </button>
    </div>
</form>