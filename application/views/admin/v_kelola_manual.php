<?php
function fmtNilai($val, $dec = 3) {
    if ($val === null || $val === '') return '-';
    $formatted = rtrim(rtrim(number_format((float)$val, $dec, '.', ''), '0'), '.');
    return str_replace('.', ',', $formatted);
}
$is_bendungan = isset($pos->is_bendungan) && $pos->is_bendungan == 1;
$is_bendung = isset($pos->is_bendung) && $pos->is_bendung == 1;
$segment = $this->uri->segment(1);

// Tentukan label tipe
if ($is_bendung) {
    $tipe_label = 'Bendung';
} elseif ($is_bendungan) {
    $tipe_label = 'Bendungan';
} else {
    $tipe_label = 'Pos';
}
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Kelola Laporan Manual</h1>
        <p class="text-slate-500 text-sm mt-1">
            <?= $tipe_label ?>: <span class="font-bold text-darkblue"><?= htmlspecialchars($pos->nama_pos) ?> (<?= $pos->tipe_pos ?>)</span>
        </p>
    </div>
    
    <button type="button" onclick="openModalPilihInput()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Data
    </button>
</div>

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

<!-- Filter -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4">
    <form method="GET" action="<?= base_url($segment.'/kelola_manual') ?>" class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="w-full sm:flex-1">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Pos</label>
            <select name="pos" onchange="this.form.submit()" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium">
                <?php if(!empty($pos_list)): foreach($pos_list as $pl): ?>
                    <option value="<?= $pl->id_pos ?>" <?= $pl->id_pos == $pos->id_pos ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pl->nama_pos) ?> (<?= $pl->tipe_pos ?>)
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bulan</label>
            <input type="month" name="bulan" value="<?= $bulan ?>" onchange="this.form.submit()" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium">
        </div>
    </form>
</div>

<!-- ============================================ -->
<!-- TABEL POS BIASA (PCH / PDA) -->
<!-- ============================================ -->
<?php if(!$is_bendungan && !$is_bendung): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Data Laporan - <?= htmlspecialchars($pos->nama_pos) ?></h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($data_list) ?> data</span>
    </div>
    
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-xs md:text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] md:text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold w-8">#</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Tanggal</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-right font-bold">Petugas</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Nilai</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($data_list)): $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-2 md:px-3 py-2 md:py-3 text-slate-400 text-[10px] md:text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <p class="font-semibold text-darkblue text-[10px] md:text-xs leading-tight"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                        <p class="text-[9px] md:text-[10px] text-slate-400"><?= date('H:i', strtotime($d->created_at)) ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs text-slate-600 text-right">
                        <?= !empty($d->nama_petugas) ? htmlspecialchars($d->nama_petugas) : '<span class="text-slate-400">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <?php if($pos->tipe_pos == 'PCH'): ?>
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] md:text-xs font-bold bg-blue-50 text-blue-600"><?= $d->rain !== null ? fmt_rain($d->rain) : '-' ?></span>
                        <?php else: ?>
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] md:text-xs font-bold bg-green-50 text-green-600"><?= $d->wlevel !== null ? fmt_tma($d->wlevel) : '-' ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEditPos('<?= $d->id_manual ?>')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url($segment.'/hapus_manual/'.$d->id_manual.'?pos='.$pos->id_pos) ?>" onclick="return confirm('Hapus data ini?')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" class="px-5 py-12 text-center text-slate-400"><p class="text-xs md:text-sm font-medium">Belum ada data</p></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================ -->
<!-- TABEL BENDUNGAN -->
<!-- ============================================ -->
<?php elseif($is_bendungan): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Data Bendungan - <?= htmlspecialchars($pos->nama_pos) ?></h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($data_list) ?> data</span>
    </div>
    
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-xs md:text-sm min-w-[600px]">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] md:text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold w-8">#</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Tanggal</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Petugas</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Curah Hujan</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Elevasi/TMA</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Inflow</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Outflow</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($data_list)): $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-2 md:px-3 py-2 md:py-3 text-slate-400 text-[10px] md:text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <p class="font-semibold text-darkblue text-[10px] md:text-xs leading-tight"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                        <p class="text-[9px] md:text-[10px] text-slate-400"><?= date('H:i', strtotime($d->created_at)) ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs text-slate-600">
                        <?= !empty($d->nama_user) ? htmlspecialchars($d->nama_user) : '<span class="text-slate-400">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] md:text-xs font-bold bg-blue-50 text-blue-600"><?= $d->rain !== null ? fmt_rain($d->rain) : '-' ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] md:text-xs font-bold bg-green-50 text-green-600"><?= $d->elevasi !== null ? fmt_tma($d->elevasi) : '-' ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="text-[10px] md:text-xs font-bold text-blue-600"><?= fmtNilai($d->inflow, 1) ?> m³/s</span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="text-[10px] md:text-xs font-bold text-orange-600"><?= fmtNilai($d->total_outflow, 1) ?> m³/s</span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEditBendungan('<?= $d->id_bendungan ?>')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url($segment.'/hapus_bendungan/'.$d->id_bendungan.'?pos='.$pos->id_pos) ?>" onclick="return confirm('Hapus data bendungan ini?')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="8" class="px-5 py-12 text-center text-slate-400"><p class="text-sm font-medium">Belum ada data</p></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================ -->
<!-- TABEL BENDUNG (SESUAI STRUKTUR TERBARU) -->
<!-- ============================================ -->
<?php elseif($is_bendung): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Data Bendung - <?= htmlspecialchars($pos->nama_pos) ?></h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($data_list) ?> data</span>
    </div>
    
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-xs md:text-sm min-w-[1100px]">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] md:text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold w-8">#</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Tanggal</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Petugas</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Curah Hujan</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Elevasi Mercu</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Q-Total</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Q-FC1</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Q-FC2</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold hidden sm:table-cell">Q-Sal. Induk</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold hidden sm:table-cell">Q-Limpas</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold hidden lg:table-cell">Q-Sungai</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold hidden lg:table-cell">Q-SPAM KPBU</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold hidden sm:table-cell">Sluice Gate</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold hidden sm:table-cell">Bukaan Pintu</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($data_list)): $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-2 md:px-3 py-2 md:py-3 text-slate-400 text-[10px] md:text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <p class="font-semibold text-darkblue text-[10px] md:text-xs leading-tight"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                        <p class="text-[9px] md:text-[10px] text-slate-400"><?= date('H:i', strtotime($d->created_at)) ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs text-slate-600">
                        <?= !empty($d->nama_user) ? htmlspecialchars($d->nama_user) : '<span class="text-slate-400">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] md:text-xs font-bold bg-blue-50 text-blue-600"><?= $d->rain !== null ? fmt_rain($d->rain) : '-' ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] md:text-xs font-bold bg-green-50 text-green-600"><?= $d->elevasi_mercu !== null ? fmt_mercu($d->elevasi_mercu) : '-' ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="text-[10px] md:text-xs font-bold text-slate-700"><?= fmtNilai($d->q_total, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="text-[10px] md:text-xs font-bold text-emerald-600"><?= fmtNilai($d->q_fc1, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="text-[10px] md:text-xs font-bold text-blue-600"><?= fmtNilai($d->q_fc2, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center hidden sm:table-cell">
                        <span class="text-[10px] md:text-xs font-bold text-amber-600"><?= fmtNilai($d->q_sal_induk, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center hidden sm:table-cell">
                        <span class="text-[10px] md:text-xs font-bold text-red-500"><?= fmtNilai($d->q_limpas, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center hidden lg:table-cell">
                        <span class="text-[10px] md:text-xs font-bold text-cyan-600"><?= fmtNilai($d->q_sungai, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center hidden lg:table-cell">
                        <span class="text-[10px] md:text-xs font-bold text-purple-600"><?= fmtNilai($d->q_spam_kpbu, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center hidden sm:table-cell">
                        <span class="text-[10px] md:text-xs font-bold text-slate-700"><?= fmtNilai($d->sluice_gate, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center hidden sm:table-cell">
                        <span class="text-[10px] md:text-xs font-bold text-slate-700"><?= fmtNilai($d->bukaan_pintu, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEditBendung('<?= $d->id_bendung ?>')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url($segment.'/hapus_bendung/'.$d->id_bendung.'?pos='.$pos->id_pos) ?>" onclick="return confirm('Hapus data bendung ini?')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="15" class="px-5 py-12 text-center text-slate-400"><p class="text-sm font-medium">Belum ada data</p></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ============================================ -->
<!-- MODAL PILIH INPUT -->
<!-- ============================================ -->
<div id="modalPilihInput" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Data Manual
            </h3>
            <button type="button" onclick="closeModalPilihInput()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5 space-y-3">
            <p class="text-xs text-slate-500">Pilih tipe infrastruktur untuk input data manual:</p>
            <div class="grid grid-cols-1 gap-2.5">
                <?php if($is_bendung): ?>
                <button type="button" onclick="closeModalPilihInput(); openModalInputBendung()" class="flex items-center gap-4 p-4 border-2 border-slate-200 rounded-xl hover:border-darkblue hover:bg-slate-50 transition-all text-left w-full">
                    <div class="w-10 h-10 bg-darkblue text-white rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 8h18M3 12h18M3 16h18M3 20h18"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-slate-700">Data Bendung</p><p class="text-[11px] text-slate-400 mt-0.5">Elevasi Mercu, Q-Total, Q-FC1, Q-FC2, dll.</p></div>
                </button>
                <?php elseif($is_bendungan): ?>
                <button type="button" onclick="closeModalPilihInput(); openModalInputBendungan()" class="flex items-center gap-4 p-4 border-2 border-slate-200 rounded-xl hover:border-darkblue hover:bg-slate-50 transition-all text-left w-full">
                    <div class="w-10 h-10 bg-darkblue text-white rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-slate-700">Data Bendungan</p><p class="text-[11px] text-slate-400 mt-0.5">TMA, Volume, Inflow, Outflow, dll.</p></div>
                </button>
                <?php else: ?>
                <button type="button" onclick="closeModalPilihInput(); openModalInputPos()" class="flex items-center gap-4 p-4 border-2 border-slate-200 rounded-xl hover:border-darkblue hover:bg-slate-50 transition-all text-left w-full">
                    <div class="w-10 h-10 bg-darkblue text-white rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-slate-700">Data Pos Biasa</p><p class="text-[11px] text-slate-400 mt-0.5">Curah Hujan (PCH) / TMA (PDA)</p></div>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL INPUT POS (PCH/PDA) -->
<!-- ============================================ -->
<?php if(!$is_bendungan && !$is_bendung): ?>
<div id="modalInputPos" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Data Pos
            </h3>
            <button type="button" onclick="closeModalInputPos()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_data_pos') ?>" method="POST" class="p-5 space-y-4" onsubmit="return validateFormPos()">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Pos <span class="text-red-500">*</span></label>
                <select name="id_pos" id="selectPosInput" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium" onchange="ubahFormPos()">
                    <option value="">-- Pilih Pos --</option>
                    <?php foreach($pos_list as $pl): if($pl->is_bendungan == 0 && $pl->is_bendung == 0): ?>
                        <option value="<?= $pl->id_pos ?>" data-tipe="<?= $pl->tipe_pos ?>" <?= (!$is_bendungan && !$is_bendung && $pl->id_pos == $pos->id_pos) ? 'selected' : '' ?>><?= htmlspecialchars($pl->nama_pos) ?> (<?= $pl->tipe_pos ?>)</option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Pengukuran <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_input" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium">
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-darkblue flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    </div>
                    <div><h3 class="font-bold text-white text-sm">Data Pengukuran Harian</h3></div>
                </div>
                <div class="p-5 space-y-5">
                    <div id="form-pch" class="border border-slate-200 rounded-xl p-4">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">Curah Hujan</p>
                        <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nilai Curah Hujan (mm) <span class="text-red-500">*</span></label><input type="number" step="any" name="rain" id="input-rain" min="0" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-semibold" placeholder="0" oninput="validateMin(this, 0)"><p class="text-[10px] text-slate-400 mt-1">Satuan: milimeter (mm)</p></div>
                    </div>
                    <div id="form-pda" class="border border-slate-200 rounded-xl p-4 hidden">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">Tinggi Muka Air</p>
                        <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nilai TMA (cm) <span class="text-red-500">*</span></label><input type="number" step="any" name="wlevel" id="input-wlevel" min="0" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-semibold" placeholder="0" oninput="validateMin(this, 0)"><p class="text-[10px] text-slate-400 mt-1">Satuan: centimeter (cm) - Otomatis dikonversi ke meter</p></div>
                    </div>
                    <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label><textarea name="keterangan" rows="2" maxlength="500" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white resize-none" placeholder="Tambahkan keterangan jika diperlukan..."></textarea></div>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-blue-700">Jam input akan tercatat otomatis: <b class="text-blue-900"><?= date('H:i') ?> WIB</b></p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalInputPos()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-lg shadow-brandyellow/20">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ============================================ -->
<!-- MODAL INPUT BENDUNGAN -->
<!-- ============================================ -->
<?php if($is_bendungan): ?>
<div id="modalInputBendungan" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Data Bendungan
            </h3>
            <button type="button" onclick="closeModalInputBendungan()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_bendungan') ?>" method="POST" class="p-5 space-y-4" onsubmit="return confirm('Simpan data bendungan ini?')">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Bendungan <span class="text-red-500">*</span></label><select name="id_pos" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium"><option value="">-- Pilih Bendungan --</option><?php foreach($pos_list as $pl): if($pl->is_bendungan == 1): ?><option value="<?= $pl->id_pos ?>" <?= ($is_bendungan && $pl->id_pos == $pos->id_pos) ? 'selected' : '' ?>><?= htmlspecialchars($pl->nama_pos) ?></option><?php endif; endforeach; ?></select></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Pengukuran <span class="text-red-500">*</span></label><input type="date" name="tanggal_input" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium"></div>
            </div>
            
            <!-- Data Tetap Bendungan -->
            <div class="bg-white rounded-2xl border-2 border-amber-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-amber-200 bg-amber-50"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg></div><div><h3 class="font-bold text-amber-700 text-sm uppercase tracking-wider">Data Tetap Bendungan</h3><p class="text-amber-500 text-[10px]">Jarang berubah. Diambil dari data master, ubah hanya jika diperlukan.</p></div></div></div>
                <div class="p-4"><div class="grid grid-cols-1 sm:grid-cols-3 gap-3"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">NWL (m)</label><input type="number" step="any" name="nwl" class="w-full px-3 py-2.5 border-2 border-amber-200 rounded-lg text-sm bg-white font-semibold" placeholder="0"><p class="text-[9px] text-amber-500 mt-0.5">Satuan: meter</p></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Volume NWL (jt.m³)</label><input type="number" step="any" name="nwl_volume" class="w-full px-3 py-2.5 border-2 border-amber-200 rounded-lg text-sm bg-white font-semibold" placeholder="0"><p class="text-[9px] text-amber-500 mt-0.5">Satuan: jt.m³</p></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Luas NWL (km²)</label><input type="number" step="any" name="nwl_luas" class="w-full px-3 py-2.5 border-2 border-amber-200 rounded-lg text-sm bg-white font-semibold" placeholder="0"><p class="text-[9px] text-amber-500 mt-0.5">Satuan: km²</p></div></div></div>
            </div>
            
            <!-- Data Pengukuran Harian -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-darkblue flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center"><svg class="w-4 h-4 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg></div><div><h3 class="font-bold text-white text-sm uppercase tracking-wider">Data Pengukuran Harian</h3><p class="text-blue-200 text-[10px]">Data yang diinputkan setiap kali melakukan pengukuran.</p></div></div>
                <div class="p-4 space-y-4">
                    <div class="border border-slate-200 rounded-xl p-3"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">Hidrologi Dasar</p><div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Curah Hujan (mm)</label><input type="number" step="any" name="rain" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm bg-white font-semibold" placeholder="0"></div><div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi / TMA (cm)</label><input type="number" step="any" name="elevasi" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm bg-white font-semibold" placeholder="0" title="Satuan cm, otomatis dikonversi ke meter"></div></div></div>
                    <div class="border border-slate-200 rounded-xl p-3"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">Parameter Utama</p><div class="grid grid-cols-2 sm:grid-cols-4 gap-2"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Volume (jt.m³)</label><input type="number" step="any" name="volume" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Luas Genangan (km²)</label><input type="number" step="any" name="luas" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Inflow (m³/s)</label><input type="number" step="any" name="inflow" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Total Outflow (m³/s)</label><input type="number" step="any" name="total_outflow" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div></div></div>
                    <div class="border border-slate-200 rounded-xl p-3"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">Outflow & Status Operasional</p><div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">PLTM (m³/s)</label><input type="number" step="any" name="pltm" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Spillway (m³/s)</label><input type="number" step="any" name="spillway" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Tail Water</label><input type="text" name="tail_water" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="-"></div></div><div class="grid grid-cols-2 gap-2"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Status PLTA</label><select name="plta_status" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white"><option value="">-- Pilih --</option><option value="on">ON</option><option value="off">OFF</option><option value="maintenance">Maintenance</option></select></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Status Irigasi</label><select name="irigasi_status" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white"><option value="">-- Pilih --</option><option value="on">ON</option><option value="off">OFF</option><option value="maintenance">Maintenance</option></select></div></div></div>
                    <div class="border border-slate-200 rounded-xl p-3"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">Data Rembesan</p><div class="mb-3"><p class="text-[10px] font-bold text-slate-400 mb-2">SM Chamber (V-Notch)</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h (cm)</label><input type="number" step="any" name="rembesan_vnotch_h" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label><input type="number" step="any" name="rembesan_vnotch_q" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div></div></div><div class="grid grid-cols-2 gap-3"><div><p class="text-[10px] font-bold text-slate-400 mb-2">Pump Pit Kiri</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h (cm)</label><input type="number" step="any" name="rembesan_pump_pit_l_h" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label><input type="number" step="any" name="rembesan_pump_pit_l_q" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div></div></div><div><p class="text-[10px] font-bold text-slate-400 mb-2">Pump Pit Kanan</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h (cm)</label><input type="number" step="any" name="rembesan_pump_pit_r_h" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label><input type="number" step="any" name="rembesan_pump_pit_r_q" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div></div></div></div></div>
                    <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label><textarea name="keterangan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm bg-white resize-none" placeholder="Tambahkan keterangan jika diperlukan..."></textarea></div>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2"><svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-xs text-blue-700">Jam input akan tercatat otomatis: <b class="text-blue-900"><?= date('H:i') ?> WIB</b></p></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModalInputBendungan()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button><button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-lg shadow-brandyellow/20">Simpan Data Bendungan</button></div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ============================================ -->
<!-- MODAL INPUT BENDUNG (SESUAI STRUKTUR TERBARU) -->
<!-- ============================================ -->
<?php if($is_bendung): ?>
<div id="modalInputBendung" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Data Bendung
            </h3>
            <button type="button" onclick="closeModalInputBendung()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_bendung') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Bendung <span class="text-red-500">*</span></label>
                    <select name="id_pos" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium">
                        <option value="">-- Pilih Bendung --</option>
                        <?php foreach($pos_list as $pl): if($pl->is_bendung == 1): ?>
                            <option value="<?= $pl->id_pos ?>" <?= ($is_bendung && $pl->id_pos == $pos->id_pos) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pl->nama_pos) ?>
                            </option>
                        <?php endif; endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Pengukuran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_input" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow bg-white font-medium">
                </div>
            </div>
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-darkblue flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                        <svg class="w-4 h-4 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    </div>
                    <div><h3 class="font-bold text-white text-sm">Data Pengukuran Harian</h3></div>
                </div>
                <div class="p-4 space-y-4">
                    <div class="border border-slate-200 rounded-xl p-3">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3"><span class="w-1.5 h-4 bg-blue-500 rounded-full"></span> Hidrologi Dasar</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                                <input type="number" step="any" name="rain" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm bg-white font-semibold" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Elevasi Air thd Mercu (m)</label>
                                <input type="number" step="any" name="elevasi_mercu" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm bg-white font-semibold" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    
                    <div class="border border-slate-200 rounded-xl p-3">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3"><span class="w-1.5 h-4 bg-purple-500 rounded-full"></span> Parameter Debit</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Q-Total (m³/dt)</label>
                                <input type="number" step="any" name="q_total" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Q-FC1 (m³/dt)</label>
                                <input type="number" step="any" name="q_fc1" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Q-FC2 (m³/dt)</label>
                                <input type="number" step="any" name="q_fc2" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Q-Sal. Induk (m³/dt)</label>
                                <input type="number" step="any" name="q_sal_induk" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Q-Limpas (m³/dt)</label>
                                <input type="number" step="any" name="q_limpas" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Q-Sungai (m³/dt)</label>
                                <input type="number" step="any" name="q_sungai" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Q-SPAM KPBU (m³/dt)</label>
                                <input type="number" step="any" name="q_spam_kpbu" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Sluice Gate</label>
                                <input type="number" step="any" name="sluice_gate" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Bukaan Pintu (m)</label>
                                <input type="number" step="any" name="bukaan_pintu" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm bg-white resize-none" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-blue-700">Jam input akan tercatat otomatis: <b class="text-blue-900"><?= date('H:i') ?> WIB</b></p>
            </div>
            
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModalInputBendung()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all shadow-lg shadow-brandyellow/20">Simpan Data Bendung</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ============================================ -->
<!-- MODAL EDIT POS (PCH/PDA) -->
<!-- ============================================ -->
<div id="modalEditPos" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Data Manual
            </h3>
            <button type="button" onclick="closeModalEditPos()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/update_manual') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_manual" id="edit_pos_id">
            <input type="hidden" name="id_pos" value="<?= $pos->id_pos ?>">
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal <span class="text-red-500">*</span></label><input type="date" name="tanggal" id="edit_pos_tanggal" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" required></div>
            <?php if($pos->tipe_pos == 'PCH'): ?>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Curah Hujan (mm)</label><input type="number" step="any" name="rain" id="edit_pos_rain" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0"></div>
            <?php else: ?>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">TMA (cm)</label><input type="number" step="any" name="wlevel" id="edit_pos_wlevel" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" placeholder="0"></div>
            <?php endif; ?>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label><textarea name="keterangan" id="edit_pos_keterangan" rows="2" maxlength="500" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white resize-none" placeholder="Tambahkan keterangan jika diperlukan..."></textarea></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModalEditPos()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button><button type="submit" class="flex-1 px-4 py-3 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md">Simpan</button></div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT BENDUNGAN -->
<!-- ============================================ -->
<div id="modalEditBendungan" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Data Bendungan
            </h3>
            <button type="button" onclick="closeModalEditBendungan()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/update_bendungan') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_bendungan" id="edit_b_id">
            <input type="hidden" name="id_pos" value="<?= $pos->id_pos ?>">
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal <span class="text-red-500">*</span></label><input type="date" name="tanggal" id="edit_b_tanggal" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" required></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Data Tetap</p><div class="grid grid-cols-1 sm:grid-cols-3 gap-3"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">NWL (m)</label><input type="number" step="any" name="nwl" id="edit_b_nwl" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Vol NWL (jt.m³)</label><input type="number" step="any" name="nwl_volume" id="edit_b_nwl_volume" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Luas NWL (km²)</label><input type="number" step="any" name="nwl_luas" id="edit_b_nwl_luas" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white"></div></div></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Hidrologi Dasar</p><div class="grid grid-cols-2 gap-3"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Curah Hujan (mm)</label><input type="number" step="any" name="rain" id="edit_b_rain" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Elevasi / TMA (cm)</label><input type="number" step="any" name="elevasi" id="edit_b_elevasi" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0" title="Satuan cm, otomatis dikonversi ke meter"></div></div></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Parameter Utama</p><div class="grid grid-cols-2 sm:grid-cols-4 gap-3"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Volume (jt.m³)</label><input type="number" step="any" name="volume" id="edit_b_volume" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Luas (km²)</label><input type="number" step="any" name="luas" id="edit_b_luas" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Inflow (m³/s)</label><input type="number" step="any" name="inflow" id="edit_b_inflow" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Total Outflow</label><input type="number" step="any" name="total_outflow" id="edit_b_total_outflow" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div></div></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Outflow & Status</p><div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-3"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">PLTM</label><input type="number" step="any" name="pltm" id="edit_b_pltm" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Spillway</label><input type="number" step="any" name="spillway" id="edit_b_spillway" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Tail Water</label><input type="text" name="tail_water" id="edit_b_tail_water" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="-"></div></div><div class="grid grid-cols-2 gap-3"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Status PLTA</label><select name="plta_status" id="edit_b_plta_status" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white"><option value="">-- Pilih --</option><option value="on">ON</option><option value="off">OFF</option><option value="maintenance">Maintenance</option></select></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Status Irigasi</label><select name="irigasi_status" id="edit_b_irigasi_status" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white"><option value="">-- Pilih --</option><option value="on">ON</option><option value="off">OFF</option><option value="maintenance">Maintenance</option></select></div></div></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Rembesan</p><div class="grid grid-cols-1 sm:grid-cols-3 gap-3"><div><p class="text-[10px] font-bold text-slate-400 mb-2">V-Notch</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h (cm)</label><input type="number" step="any" name="rembesan_vnotch_h" id="edit_b_rvh" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label><input type="number" step="any" name="rembesan_vnotch_q" id="edit_b_rvq" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div></div></div><div><p class="text-[10px] font-bold text-slate-400 mb-2">Pump Pit Kiri</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h (cm)</label><input type="number" step="any" name="rembesan_pump_pit_l_h" id="edit_b_rplh" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label><input type="number" step="any" name="rembesan_pump_pit_l_q" id="edit_b_rplq" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div></div></div><div><p class="text-[10px] font-bold text-slate-400 mb-2">Pump Pit Kanan</p><div class="grid grid-cols-2 gap-2"><div><label class="block text-[9px] text-slate-400 mb-1">h (cm)</label><input type="number" step="any" name="rembesan_pump_pit_r_h" id="edit_b_rprh" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div><div><label class="block text-[9px] text-slate-400 mb-1">Q (lt/s)</label><input type="number" step="any" name="rembesan_pump_pit_r_q" id="edit_b_rprq" class="w-full px-2 py-2 border-2 border-slate-200 rounded-lg text-xs bg-white" placeholder="0"></div></div></div></div></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label><textarea name="keterangan" id="edit_b_keterangan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white resize-none" placeholder="Tambahkan keterangan..."></textarea></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModalEditBendungan()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button><button type="submit" class="flex-1 px-4 py-3 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md">Simpan</button></div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT BENDUNG (SESUAI STRUKTUR TERBARU) -->
<!-- ============================================ -->
<div id="modalEditBendung" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Data Bendung
            </h3>
            <button type="button" onclick="closeModalEditBendung()" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/update_bendung') ?>" method="POST" class="p-5 space-y-4">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="id_bendung" id="edit_bd_id">
            <input type="hidden" name="id_pos" value="<?= $pos->id_pos ?>">
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal <span class="text-red-500">*</span></label><input type="date" name="tanggal" id="edit_bd_tanggal" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white" required></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Hidrologi Dasar</p><div class="grid grid-cols-2 gap-3"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Curah Hujan (mm)</label><input type="number" step="any" name="rain" id="edit_bd_rain" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Elevasi Mercu (m)</label><input type="number" step="any" name="elevasi_mercu" id="edit_bd_elevasi_mercu" class="w-full px-3 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0.00"></div></div></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Parameter Debit</p><div class="grid grid-cols-2 sm:grid-cols-3 gap-2"><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Q-Total</label><input type="number" step="any" name="q_total" id="edit_bd_q_total" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Q-FC1</label><input type="number" step="any" name="q_fc1" id="edit_bd_q_fc1" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Q-FC2</label><input type="number" step="any" name="q_fc2" id="edit_bd_q_fc2" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Q-Sal. Induk</label><input type="number" step="any" name="q_sal_induk" id="edit_bd_q_sal_induk" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Q-Limpas</label><input type="number" step="any" name="q_limpas" id="edit_bd_q_limpas" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Q-Sungai</label><input type="number" step="any" name="q_sungai" id="edit_bd_q_sungai" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Q-SPAM KPBU</label><input type="number" step="any" name="q_spam_kpbu" id="edit_bd_q_spam_kpbu" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Sluice Gate</label><input type="number" step="any" name="sluice_gate" id="edit_bd_sluice_gate" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div><div><label class="block text-[10px] font-bold text-slate-500 mb-1">Bukaan Pintu (m)</label><input type="number" step="any" name="bukaan_pintu" id="edit_bd_bukaan_pintu" class="w-full px-2 py-2.5 border-2 border-slate-200 rounded-lg text-sm bg-white" placeholder="0"></div></div></div>
            <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Keterangan</label><textarea name="keterangan" id="edit_bd_keterangan" rows="2" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white resize-none" placeholder="Tambahkan keterangan..."></textarea></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModalEditBendung()" class="flex-1 px-4 py-3 border-2 border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-50 transition-colors">Batal</button><button type="submit" class="flex-1 px-4 py-3 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md">Simpan</button></div>
        </form>
    </div>
</div>

<script>
// Auto-hide alerts
setTimeout(function(){
    var s = document.getElementById('alert-success');
    var e = document.getElementById('alert-error');
    if(s) s.style.display = 'none';
    if(e) e.style.display = 'none';
}, 5000);

// ==========================================
// DATA DARI PHP
// ==========================================
var posData = <?= !empty($pos_data_js) ? json_encode($pos_data_js) : '{}' ?>;
var bendunganData = <?= !empty($bendungan_data_js) ? json_encode($bendungan_data_js) : '{}' ?>;
var bendungData = <?= !empty($bendung_data_js) ? json_encode($bendung_data_js) : '{}' ?>;

// ==========================================
// MODAL FUNCTIONS
// ==========================================
function openModalPilihInput() { document.getElementById('modalPilihInput').style.display = 'flex'; }
function closeModalPilihInput() { document.getElementById('modalPilihInput').style.display = 'none'; }
function openModalInputPos() { document.getElementById('modalInputPos').style.display = 'flex'; ubahFormPos(); }
function closeModalInputPos() { document.getElementById('modalInputPos').style.display = 'none'; }
function openModalInputBendungan() { document.getElementById('modalInputBendungan').style.display = 'flex'; }
function closeModalInputBendungan() { document.getElementById('modalInputBendungan').style.display = 'none'; }
function openModalInputBendung() { document.getElementById('modalInputBendung').style.display = 'flex'; }
function closeModalInputBendung() { document.getElementById('modalInputBendung').style.display = 'none'; }

// ==========================================
// EDIT POS
// ==========================================
function openModalEditPos(id) {
    var d = posData[id];
    if (!d) { alert('Data tidak ditemukan!'); return; }
    document.getElementById('edit_pos_id').value = id;
    document.getElementById('edit_pos_tanggal').value = d.tanggal;
    var r = document.getElementById('edit_pos_rain');
    var w = document.getElementById('edit_pos_wlevel');
    var k = document.getElementById('edit_pos_keterangan');
    if (r) r.value = (d.rain !== null && d.rain !== '' && d.rain !== undefined) ? parseFloat(d.rain) : '';
    if (w) w.value = (d.wlevel !== null && d.wlevel !== '' && d.wlevel !== undefined) ? (parseFloat(d.wlevel) * 100) : '';
    if (k) k.value = (d.keterangan && d.keterangan !== 'null') ? d.keterangan : '';
    document.getElementById('modalEditPos').style.display = 'flex';
}
function closeModalEditPos() { document.getElementById('modalEditPos').style.display = 'none'; }

// ==========================================
// EDIT BENDUNGAN
// ==========================================
function openModalEditBendungan(id) {
    var d = bendunganData[id];
    if (!d) { alert('Data bendungan tidak ditemukan!'); return; }
    document.getElementById('edit_b_id').value = id;
    document.getElementById('edit_b_tanggal').value = d.tanggal;
    setVal('edit_b_nwl', d.nwl); setVal('edit_b_nwl_volume', d.nwl_volume); setVal('edit_b_nwl_luas', d.nwl_luas);
    setVal('edit_b_rain', d.rain); setVal('edit_b_elevasi', (d.elevasi !== null && d.elevasi !== '' && d.elevasi !== undefined) ? (parseFloat(d.elevasi) * 100) : d.elevasi); setVal('edit_b_volume', d.volume);
    setVal('edit_b_luas', d.luas); setVal('edit_b_inflow', d.inflow); setVal('edit_b_pltm', d.pltm);
    setVal('edit_b_spillway', d.spillway); setVal('edit_b_total_outflow', d.total_outflow);
    setVal('edit_b_rvh', d.rvh); setVal('edit_b_rvq', d.rvq); setVal('edit_b_rplh', d.rplh);
    setVal('edit_b_rplq', d.rplq); setVal('edit_b_rprh', d.rprh); setVal('edit_b_rprq', d.rprq);
    var pltaEl = document.getElementById('edit_b_plta_status'); if (pltaEl && d.plta_status) pltaEl.value = d.plta_status;
    var irigasiEl = document.getElementById('edit_b_irigasi_status'); if (irigasiEl && d.irigasi_status) irigasiEl.value = d.irigasi_status;
    var twEl = document.getElementById('edit_b_tail_water'); if (twEl && d.tail_water) twEl.value = d.tail_water;
    var ketEl = document.getElementById('edit_b_keterangan'); if (ketEl && d.keterangan && d.keterangan !== 'null') ketEl.value = d.keterangan;
    document.getElementById('modalEditBendungan').style.display = 'flex';
}
function closeModalEditBendungan() { document.getElementById('modalEditBendungan').style.display = 'none'; }

// ==========================================
// EDIT BENDUNG (SESUAI STRUKTUR TERBARU)
// ==========================================
function openModalEditBendung(id) {
    var d = bendungData[id];
    if (!d) { alert('Data bendung tidak ditemukan!'); return; }
    document.getElementById('edit_bd_id').value = id;
    document.getElementById('edit_bd_tanggal').value = d.tanggal;
    setVal('edit_bd_rain', d.rain);
    setVal('edit_bd_elevasi_mercu', (d.elevasi_mercu !== null && d.elevasi_mercu !== '' && d.elevasi_mercu !== undefined) ? (parseFloat(d.elevasi_mercu) * 100) : d.elevasi_mercu);
    setVal('edit_bd_q_total', d.q_total);
    setVal('edit_bd_q_fc1', d.q_fc1);
    setVal('edit_bd_q_fc2', d.q_fc2);
    setVal('edit_bd_q_sal_induk', d.q_sal_induk);
    setVal('edit_bd_q_limpas', d.q_limpas);
    setVal('edit_bd_q_sungai', d.q_sungai);
    setVal('edit_bd_q_spam_kpbu', d.q_spam_kpbu);
    setVal('edit_bd_sluice_gate', d.sluice_gate);
    setVal('edit_bd_bukaan_pintu', d.bukaan_pintu);
    var ketEl = document.getElementById('edit_bd_keterangan');
    if (ketEl && d.keterangan && d.keterangan !== 'null') ketEl.value = d.keterangan;
    document.getElementById('modalEditBendung').style.display = 'flex';
}
function closeModalEditBendung() { document.getElementById('modalEditBendung').style.display = 'none'; }

function setVal(id, val) {
    var el = document.getElementById(id);
    if (el && val !== null && val !== '' && val !== undefined && val !== 'null') { el.value = parseFloat(val); }
    else if (el) { el.value = ''; }
}

// ==========================================
// FORM POS HELPERS
// ==========================================
function ubahFormPos() {
    var s = document.getElementById('selectPosInput');
    if (!s || s.selectedIndex < 0) return;
    var t = s.options[s.selectedIndex].getAttribute('data-tipe');
    var pch = document.getElementById('form-pch'); var pda = document.getElementById('form-pda');
    var ri = document.getElementById('input-rain'); var wi = document.getElementById('input-wlevel');
    if (t === 'PCH') { if(pch) pch.classList.remove('hidden'); if(pda) pda.classList.add('hidden'); if(ri) ri.required = true; if(wi) wi.required = false; }
    else { if(pch) pch.classList.add('hidden'); if(pda) pda.classList.remove('hidden'); if(ri) ri.required = false; if(wi) wi.required = true; }
}
function validateFormPos() {
    var s = document.getElementById('selectPosInput');
    if (!s || s.selectedIndex < 0) { alert('Pilih pos terlebih dahulu!'); return false; }
    var t = s.options[s.selectedIndex].getAttribute('data-tipe');
    var v;
    if (t === 'PCH') { v = document.getElementById('input-rain').value; }
    else { v = document.getElementById('input-wlevel').value; }
    if (!v || parseFloat(v) < 0) { alert('Nilai harus diisi dengan benar!'); return false; }
    return true;
}
function validateMin(input, min) { var val = parseFloat(input.value); if (isNaN(val)) return; if (val < min) input.value = min; }

// ==========================================
// CLOSE MODAL ON OVERLAY CLICK & ESC
// ==========================================
var modalIds = ['modalPilihInput', 'modalInputPos', 'modalInputBendungan', 'modalInputBendung', 'modalEditPos', 'modalEditBendungan', 'modalEditBendung'];
modalIds.forEach(function(id) {
    var el = document.getElementById(id);
    if (el) { el.addEventListener('click', function(e) { if (e.target === this) { this.style.display = 'none'; } }); }
});
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { modalIds.forEach(function(id) { var el = document.getElementById(id); if (el) el.style.display = 'none'; }); } });
</script>