<?php 
function fmtNilai($val, $dec = 3) {
    if ($val === null || $val === '') return '-';
    return rtrim(rtrim(number_format($val, $dec, '.', ''), '0'), '.');
}
$segment = 'petugas';
?>

<div class="mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-slate-800">Riwayat Laporan</h1>
    <p class="text-slate-500 text-sm mt-1">
        Bendung: <span class="font-bold text-darkblue"><?= htmlspecialchars($pos->nama_pos) ?></span>
    </p>
</div>

<!-- Dropdown Pilih Pos -->
<?php if(count($daftar_pos_petugas) > 1): ?>
<div class="bg-white rounded-2xl border border-slate-200 p-4 mb-4">
    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Bendung</label>
    <select onchange="window.location='<?= base_url('petugas/kelola') ?>?pos='+this.value+'&tanggal=<?= $tanggal ?>'" 
            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white">
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

<!-- Filter Tanggal -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4">
    <form method="GET" action="<?= base_url('petugas/kelola') ?>" class="flex flex-col sm:flex-row gap-3 items-end">
        <input type="hidden" name="pos" value="<?= $id_pos_active ?>">
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Tanggal</label>
            <input type="date" name="tanggal" value="<?= $tanggal ?>" onchange="this.form.submit()" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium cursor-pointer">
        </div>
    </form>
</div>

<!-- Alert Messages -->
<?php if($this->session->flashdata('success')): ?>
<div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2" id="alert-success"><svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if($this->session->flashdata('error')): ?>
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2" id="alert-error"><svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?= $this->session->flashdata('error') ?></div>
<?php endif; ?>

<!-- TABEL BENDUNG -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Data Bendung - <?= htmlspecialchars($pos->nama_pos) ?></h3>
        <div class="flex items-center gap-2">
            <span class="text-[10px] text-slate-400"><?= date('d M Y', strtotime($tanggal)) ?></span>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($data_list) ?> data</span>
        </div>
    </div>
    
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-xs md:text-sm min-w-[700px]">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] md:text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold w-8">#</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Jam</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Petugas</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Hujan</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Elevasi</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Q-Total</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Q-FC1</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Q-FC2</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Q-Limpas</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Sluice Gate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($data_list)): $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-2 md:px-3 py-2 md:py-3 text-slate-400 text-[10px] md:text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <p class="font-semibold text-darkblue text-[10px] md:text-xs leading-tight"><?= date('H:i', strtotime($d->created_at)) ?> WIB</p>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs text-slate-600">
                        <?= !empty($d->nama_user) ? htmlspecialchars($d->nama_user) : '<span class="text-slate-400">-</span>' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] md:text-xs font-bold bg-blue-50 text-blue-600"><?= $d->rain !== null ? $d->rain.' mm' : '-' ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] md:text-xs font-bold bg-green-50 text-green-600"><?= $d->elevasi_mercu !== null ? $d->elevasi_mercu.' m' : '-' ?></span>
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
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="text-[10px] md:text-xs font-bold text-red-500"><?= fmtNilai($d->q_limpas, 3) ?></span>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <span class="text-[10px] md:text-xs font-bold text-slate-700"><?= fmtNilai($d->sluice_gate, 3) ?></span>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="10" class="px-5 py-12 text-center text-slate-400"><p class="text-xs md:text-sm font-medium">Belum ada data pada tanggal ini</p></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    setTimeout(function(){
        var s = document.getElementById('alert-success');
        var e = document.getElementById('alert-error');
        if(s) s.style.display = 'none';
        if(e) e.style.display = 'none';
    }, 5000);
</script>
