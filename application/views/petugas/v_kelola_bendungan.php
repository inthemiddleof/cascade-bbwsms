<?php
function fmtNilai($val, $dec = 3) {
    if ($val === null || $val === '') return '-';
    return rtrim(rtrim(number_format($val, $dec, '.', ''), '0'), '.');
}
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Laporan Bendungan</h1>
        <p class="text-slate-500 text-sm mt-1">Pos: <span class="font-bold text-darkblue"><?= $pos->nama_pos ?></span></p>
    </div>
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

<!-- Filter Bulan -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col sm:flex-row gap-3">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
        <span class="text-sm font-bold text-slate-600 uppercase tracking-wider">Bulan:</span>
    </div>
    <input type="month" value="<?= $bulan ?>" onchange="window.location='<?= base_url('petugas/kelola') ?>?bulan='+this.value" class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50 font-medium">
    <span class="text-[10px] text-slate-400 flex items-center">Menampilkan <b class="mx-1"><?= count($data_list) ?></b> data</span>
</div>

<!-- Tabel Data -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Daftar Laporan Bendungan</h3>
        <span class="text-[10px] text-slate-400 font-bold"><?= count($data_list) ?> DATA</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left font-bold w-10">#</th>
                    <th class="px-4 py-3 text-left font-bold">Tanggal</th>
                    <th class="px-4 py-3 text-left font-bold">Curah Hujan</th>
                    <th class="px-4 py-3 text-left font-bold">Elevasi / TMA</th>
                    <th class="px-4 py-3 text-left font-bold">Volume</th>
                    <th class="px-4 py-3 text-left font-bold">Luas Genangan</th>
                    <th class="px-4 py-3 text-left font-bold">Inflow</th>
                    <th class="px-4 py-3 text-left font-bold">Total Outflow</th>
                    <th class="px-4 py-3 text-center font-bold">PLTA</th>
                    <th class="px-4 py-3 text-center font-bold">Irigasi</th>
                    <th class="px-4 py-3 text-center font-bold w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(!empty($data_list)): $no = 1; foreach($data_list as $d): 
                    // Ambil data rain & elevasi dari data_manual terkait
                    $manual = $this->db->where('id_pos', $d->id_pos)
                                       ->where('tanggal_input', $d->tanggal_input)
                                       ->where('created_at', $d->created_at)
                                       ->get('data_manual')
                                       ->row();
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400"><?= $no++ ?></td>
                    
                    <!-- Tanggal -->
                    <td class="px-4 py-3.5">
                        <div class="min-w-0">
                            <p class="font-semibold text-darkblue"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                            <p class="text-[10px] text-slate-400"><?= date('H:i', strtotime($d->created_at)) ?> WIB</p>
                        </div>
                    </td>
                    
                    <!-- Curah Hujan -->
                    <td class="px-4 py-3.5">
                        <?php if($manual && $manual->rain !== null): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                                <?= $manual->rain ?> mm
                            </span>
                        <?php else: ?>
                            <span class="text-slate-300">-</span>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Elevasi / TMA -->
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-600">
                            <?= fmtNilai($d->elevasi, 2) ?> m
                        </span>
                    </td>
                    
                    <!-- Volume -->
                    <td class="px-4 py-3.5">
                        <span class="font-bold text-darkblue text-[11px]"><?= fmtNilai($d->volume, 3) ?> <span class="text-[9px] text-slate-400">jt.m³</span></span>
                    </td>
                    
                    <!-- Luas Genangan -->
                    <td class="px-4 py-3.5">
                        <span class="font-bold text-darkblue text-[11px]"><?= fmtNilai($d->luas, 3) ?> <span class="text-[9px] text-slate-400">km²</span></span>
                    </td>
                    
                    <!-- Inflow -->
                    <td class="px-4 py-3.5">
                        <span class="font-bold text-green-600 text-[11px]"><?= fmtNilai($d->inflow, 3) ?> <span class="text-[9px] text-slate-400">m³/s</span></span>
                    </td>
                    
                    <!-- Total Outflow -->
                    <td class="px-4 py-3.5">
                        <span class="font-bold text-orange-600 text-[11px]"><?= fmtNilai($d->total_outflow, 3) ?> <span class="text-[9px] text-slate-400">m³/s</span></span>
                    </td>
                    
                    <!-- PLTA -->
                    <td class="px-4 py-3.5 text-center">
                        <?php if($d->plta_status): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold <?= $d->plta_status == 'on' ? 'bg-green-100 text-green-600' : ($d->plta_status == 'off' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $d->plta_status == 'on' ? 'bg-green-500' : ($d->plta_status == 'off' ? 'bg-red-500' : 'bg-amber-500') ?>"></span>
                            <?= ucfirst($d->plta_status) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-300">-</span>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Irigasi -->
                    <td class="px-4 py-3.5 text-center">
                        <?php if($d->irigasi_status): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold <?= $d->irigasi_status == 'on' ? 'bg-green-100 text-green-600' : ($d->irigasi_status == 'off' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $d->irigasi_status == 'on' ? 'bg-green-500' : ($d->irigasi_status == 'off' ? 'bg-red-500' : 'bg-amber-500') ?>"></span>
                            <?= ucfirst($d->irigasi_status) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-300">-</span>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Aksi -->
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="openModalEdit(
                                '<?= $d->id_bendungan ?>','<?= $d->tanggal_input ?>','<?= $d->nwl ?>','<?= $d->elevasi ?>','<?= $d->volume ?>','<?= $d->luas ?>',
                                '<?= $d->inflow ?>','<?= $d->pltm ?>','<?= $d->spillway ?>','<?= $d->total_outflow ?>','<?= $d->plta_status ?>','<?= $d->irigasi_status ?>',
                                '<?= $d->tail_water ?>','<?= $d->rembesan_vnotch_h ?>','<?= $d->rembesan_vnotch_q ?>','<?= $d->rembesan_pump_pit_l_h ?>',
                                '<?= $d->rembesan_pump_pit_l_q ?>','<?= $d->rembesan_pump_pit_r_h ?>','<?= $d->rembesan_pump_pit_r_q ?>',
                                '<?= str_replace(["\r","\n"],['\\r','\\n'],addslashes($d->keterangan ?? '')) ?>'
                            )" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url('petugas/hapus_bendungan/'.$d->id_bendungan) ?>" onclick="return confirm('Hapus data ini?')" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="11" class="px-5 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <div>
                                <p class="text-slate-400 font-semibold">Belum ada data untuk bulan ini</p>
                                <p class="text-slate-300 text-[11px] mt-1">Gunakan menu Input Laporan untuk menambahkan data</p>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
            <div>
                <h3 class="font-bold text-darkblue text-lg">Edit Data Bendungan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui data laporan bendungan</p>
            </div>
            <button onclick="closeModalEdit()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <?= form_open('petugas/update_bendungan', ['class' => 'p-5 space-y-4']) ?>
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_bendungan" id="edit_b_id">
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="edit_b_tanggal" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-slate-50" required>
            </div>
            
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Parameter Utama</p>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">NWL (m)</label><input type="number" step="any" name="nwl" id="edit_b_nwl" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow bg-white"></div>
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">Elevasi (m)</label><input type="number" step="any" name="elevasi" id="edit_b_elevasi" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow bg-white"></div>
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">Volume (jt.m³)</label><input type="number" step="any" name="volume" id="edit_b_volume" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow bg-white"></div>
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">Luas (km²)</label><input type="number" step="any" name="luas" id="edit_b_luas" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow bg-white"></div>
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">Inflow (m³/s)</label><input type="number" step="any" name="inflow" id="edit_b_inflow" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brandyellow bg-white"></div>
                </div>
            </div>
            
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Outflow & Status</p>
                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">PLTM</label><input type="number" step="any" name="pltm" id="edit_b_pltm" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white"></div>
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">Spillway</label><input type="number" step="any" name="spillway" id="edit_b_spillway" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white"></div>
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">Total Out</label><input type="number" step="any" name="total_outflow" id="edit_b_total_outflow" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">PLTA</label><select name="plta_status" id="edit_b_plta_status" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white"><option value="">-- Pilih --</option><option value="on">ON</option><option value="off">OFF</option><option value="maintenance">Maintenance</option></select></div>
                    <div><label class="block text-[10px] font-bold text-slate-500 mb-1">Irigasi</label><select name="irigasi_status" id="edit_b_irigasi_status" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white"><option value="">-- Pilih --</option><option value="on">ON</option><option value="off">OFF</option><option value="maintenance">Maintenance</option></select></div>
                </div>
                <div><label class="block text-[10px] font-bold text-slate-500 mb-1">Tail Water</label><input type="text" name="tail_water" id="edit_b_tail_water" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white"></div>
            </div>
            
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Rembesan</p>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div><p class="text-[10px] font-bold text-slate-400 mb-2">V-Notch</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h</label><input type="number" step="any" name="rembesan_vnotch_h" id="edit_b_rvh" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-xs bg-white"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q</label><input type="number" step="any" name="rembesan_vnotch_q" id="edit_b_rvq" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-xs bg-white"></div></div></div>
                    <div><p class="text-[10px] font-bold text-slate-400 mb-2">Pump Pit Kiri</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h</label><input type="number" step="any" name="rembesan_pump_pit_l_h" id="edit_b_rplh" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-xs bg-white"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q</label><input type="number" step="any" name="rembesan_pump_pit_l_q" id="edit_b_rplq" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-xs bg-white"></div></div></div>
                </div>
                <div><p class="text-[10px] font-bold text-slate-400 mb-2">Pump Pit Kanan</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h</label><input type="number" step="any" name="rembesan_pump_pit_r_h" id="edit_b_rprh" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-xs bg-white"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q</label><input type="number" step="any" name="rembesan_pump_pit_r_q" id="edit_b_rprq" class="w-full px-2 py-2 border border-slate-300 rounded-lg text-xs bg-white"></div></div></div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Keterangan</label>
                <textarea name="keterangan" id="edit_b_keterangan" rows="2" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-slate-50 resize-none"></textarea>
            </div>
            
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalEdit()" class="flex-1 px-4 py-3 border border-slate-300 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-sm">Simpan Perubahan</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script>
function openModalEdit(id,t,n,e,v,l,i,pm,sp,to,ps,is,tw,rvh,rvq,rplh,rplq,rprh,rprq,ket){
    document.getElementById('edit_b_id').value=id;document.getElementById('edit_b_tanggal').value=t;
    document.getElementById('edit_b_nwl').value=(n!=='null'&&n!=='')?parseFloat(n):'';document.getElementById('edit_b_elevasi').value=(e!=='null'&&e!=='')?parseFloat(e):'';
    document.getElementById('edit_b_volume').value=(v!=='null'&&v!=='')?parseFloat(v):'';document.getElementById('edit_b_luas').value=(l!=='null'&&l!=='')?parseFloat(l):'';
    document.getElementById('edit_b_inflow').value=(i!=='null'&&i!=='')?parseFloat(i):'';document.getElementById('edit_b_pltm').value=(pm!=='null'&&pm!=='')?parseFloat(pm):'';
    document.getElementById('edit_b_spillway').value=(sp!=='null'&&sp!=='')?parseFloat(sp):'';document.getElementById('edit_b_total_outflow').value=(to!=='null'&&to!=='')?parseFloat(to):'';
    document.getElementById('edit_b_plta_status').value=(ps!=='null'&&ps!=='')?ps:'';document.getElementById('edit_b_irigasi_status').value=(is!=='null'&&is!=='')?is:'';
    document.getElementById('edit_b_tail_water').value=(tw!=='null'&&tw!=='')?tw:'';
    document.getElementById('edit_b_rvh').value=(rvh!=='null'&&rvh!=='')?parseFloat(rvh):'';document.getElementById('edit_b_rvq').value=(rvq!=='null'&&rvq!=='')?parseFloat(rvq):'';
    document.getElementById('edit_b_rplh').value=(rplh!=='null'&&rplh!=='')?parseFloat(rplh):'';document.getElementById('edit_b_rplq').value=(rplq!=='null'&&rplq!=='')?parseFloat(rplq):'';
    document.getElementById('edit_b_rprh').value=(rprh!=='null'&&rprh!=='')?parseFloat(rprh):'';document.getElementById('edit_b_rprq').value=(rprq!=='null'&&rprq!=='')?parseFloat(rprq):'';
    document.getElementById('edit_b_keterangan').value=(ket!=='null'&&ket!=='')?ket.replace(/\\r/g,'\r').replace(/\\n/g,'\n'):'';
    document.getElementById('modalEdit').classList.remove('hidden');document.getElementById('modalEdit').classList.add('flex');
}
function closeModalEdit(){document.getElementById('modalEdit').classList.add('hidden');document.getElementById('modalEdit').classList.remove('flex');}
document.getElementById('modalEdit').addEventListener('click',function(e){if(e.target===this)closeModalEdit();});
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeModalEdit();});
</script>