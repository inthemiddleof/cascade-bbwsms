<?php
function fmtNilai($val, $dec = 3) {
    if ($val === null || $val === '') return '-';
    $formatted = rtrim(rtrim(number_format((float)$val, $dec, '.', ''), '0'), '.');
    return str_replace('.', ',', $formatted);
}

if (!function_exists('fmt_rain')) {
    function fmt_rain($val) {
        if ($val === null || $val === '') return '-';
        return rtrim(rtrim(number_format((float)$val, 1, ',', ''), '0'), ',');
    }
}
if (!function_exists('fmt_tma')) {
    function fmt_tma($val) {
        if ($val === null || $val === '') return '-';
        return rtrim(rtrim(number_format((float)$val, 2, ',', ''), '0'), ',');
    }
}
if (!function_exists('fmt_mercu')) {
    function fmt_mercu($val) {
        if ($val === null || $val === '') return '-';
        return rtrim(rtrim(number_format((float)$val, 2, ',', ''), '0'), ',');
    }
}

$is_bendungan = isset($pos->is_bendungan) && $pos->is_bendungan == 1;
$is_bendung = isset($pos->is_bendung) && $pos->is_bendung == 1;
$segment = $this->uri->segment(1);
$admin_type = isset($admin_type) ? $admin_type : 'hidrologi';

// Tentukan label tipe
if ($is_bendung) {
    $tipe_label = 'Bendung';
} elseif ($is_bendungan) {
    $tipe_label = 'Bendungan';
} elseif ($admin_type == 'irigasi') {
    $tipe_label = 'Irigasi';
} elseif ($admin_type == 'embung') {
    $tipe_label = 'Embung';
} elseif ($admin_type == 'pantai') {
    $tipe_label = 'Pengaman Pantai';
} elseif ($admin_type == 'sedimen') {
    $tipe_label = 'Pengendali Sedimen';
} else {
    $tipe_label = 'Pos';
}

$nama_pos_display = isset($pos->nama_pos) ? htmlspecialchars($pos->nama_pos) : 'Pilih Pos';
$tipe_pos_display = isset($pos->tipe_pos) ? $pos->tipe_pos : '';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Kelola Laporan Manual</h1>
        <p class="text-slate-500 text-sm mt-1">
            <?php if(!empty($pos) && $admin_type != 'irigasi' && $admin_type != 'pantai' && $admin_type != 'sedimen'): ?>
            <?= $tipe_label ?>: <span class="font-bold text-darkblue"><?= $nama_pos_display ?> <?= $tipe_pos_display ? '(' . $tipe_pos_display . ')' : '' ?></span>
            <?php elseif($admin_type == 'irigasi'): ?>
            <span class="font-bold text-darkblue">Data Irigasi</span>
            <?php elseif($admin_type == 'pantai'): ?>
            <span class="font-bold text-darkblue">Data Pengaman Pantai</span>
            <?php elseif($admin_type == 'sedimen'): ?>
            <span class="font-bold text-darkblue">Data Pengendali Sedimen</span>
            <?php else: ?>
            <span class="text-slate-400">Silakan pilih pos di filter bawah</span>
            <?php endif; ?>
        </p>
    </div>
    
    <?php if($admin_type != 'irigasi' && $admin_type != 'embung' && $admin_type != 'pantai' && $admin_type != 'sedimen'): ?>
        <?php if($is_bendungan): ?>
        <button type="button" onclick="openModalBendungan()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Data
        </button>
        <?php elseif($is_bendung): ?>
        <button type="button" onclick="openModalBendung()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Data
        </button>
        <?php elseif(!empty($pos)): ?>
        <button type="button" onclick="openModalPos()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Data
        </button>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if($admin_type == 'irigasi'): ?>
    <button type="button" onclick="openModalIrigasi()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Data
    </button>
    <?php elseif($admin_type == 'pantai'): ?>
    <button type="button" onclick="openModalPantai()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Data
    </button>
    <?php elseif($admin_type == 'sedimen'): ?>
    <button type="button" onclick="openModalSedimen()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Data
    </button>
    <?php elseif($admin_type == 'embung'): ?>
    <button type="button" onclick="openModalEmbung()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Data
    </button>
    <?php endif; ?>
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
<?php if($this->session->flashdata('info')): ?>
<div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2" id="alert-info">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <?= $this->session->flashdata('info') ?>
</div>
<?php endif; ?>

<!-- FILTER -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4">
    <form method="GET" action="<?= base_url($segment.'/kelola_manual') ?>" class="flex flex-col sm:flex-row gap-3 items-end">
        <?php if($admin_type != 'irigasi' && $admin_type != 'pantai' && $admin_type != 'sedimen'): ?>
        <div class="w-full sm:flex-1">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                <?php if($admin_type == 'embung'): ?>
                Pilih Pos Embung
                <?php else: ?>
                Pilih Pos
                <?php endif; ?>
            </label>
            <?php if(!empty($pos_list)): ?>
            <select name="pos" onchange="this.form.submit()" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium">
                <?php foreach($pos_list as $pl): ?>
                    <option value="<?= $pl->id_pos ?>" <?= (!empty($pos) && $pl->id_pos == $pos->id_pos) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pl->nama_pos) ?> <?= isset($pl->tipe_pos) ? '(' . $pl->tipe_pos . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php else: ?>
            <div class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm bg-slate-50 text-slate-400">
                Tidak ada pos tersedia
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bulan</label>
            <input type="month" name="bulan" value="<?= $bulan ?>" onchange="this.form.submit()" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white font-medium">
        </div>
    </form>
</div>

<!-- ============================================ -->
<!-- TABEL UNTUK ADMIN IRIGASI -->
<!-- ============================================ -->
<?php if($admin_type == 'irigasi'): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Data Irigasi</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($data_list) ?> data</span>
    </div>
    
    <?php if(!empty($data_list)): ?>
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-xs md:text-sm min-w-[800px]">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] md:text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold w-8">#</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Nama Irigasi</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Tanggal</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Wilayah</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Luas Fungsional</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Status</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-2 md:px-3 py-2 md:py-3 text-slate-400 text-[10px] md:text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs font-semibold text-darkblue">
                        <?= isset($d->nama_pos) ? htmlspecialchars($d->nama_pos) : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <p class="font-semibold text-darkblue text-[10px] md:text-xs leading-tight"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                        <p class="text-[9px] md:text-[10px] text-slate-400"><?= date('H:i', strtotime($d->created_at)) ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs text-slate-600">
                        <?php
                        $wilayah = [];
                        if (isset($d->kabupaten_kota) && $d->kabupaten_kota) $wilayah[] = htmlspecialchars($d->kabupaten_kota);
                        if (isset($d->kecamatan) && $d->kecamatan) $wilayah[] = htmlspecialchars($d->kecamatan);
                        echo !empty($wilayah) ? implode(' - ', $wilayah) : '-';
                        ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center text-[10px] md:text-xs">
                        <?= isset($d->nilai_1) ? number_format($d->nilai_1, 0, ',', '.') . ' ha' : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center">
                        <?php
                        $status = isset($d->status) ? $d->status : '';
                        if ($status == 'Sudah' || $status == 'Aktif') {
                            echo '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">' . htmlspecialchars($status) . '</span>';
                        } elseif ($status == 'Tidak/Belum' || $status == 'Nonaktif') {
                            echo '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">' . htmlspecialchars($status) . '</span>';
                        } else {
                            echo '<span class="text-slate-400">-</span>';
                        }
                        ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEditIrigasi(<?= htmlspecialchars(json_encode($d)) ?>)" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url($segment.'/hapus_irigasi/'.$d->id_manual) ?>" onclick="return confirm('Hapus data irigasi ini?')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="px-5 py-12 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-sm font-medium">Belum ada data irigasi</p>
        <p class="text-xs mt-1">Klik tombol Tambah Data untuk menambahkan</p>
    </div>
    <?php endif; ?>
</div>

<!-- ============================================ -->
<!-- TABEL UNTUK ADMIN PANTAI -->
<!-- ============================================ -->
<?php elseif($admin_type == 'pantai'): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Data Pengaman Pantai</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($data_list) ?> data</span>
    </div>
    
    <?php if(!empty($data_list)): ?>
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-xs md:text-sm min-w-[700px]">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] md:text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold w-8">#</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Nama Aset</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Tanggal</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Jenis Bangunan</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Panjang (m)</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-2 md:px-3 py-2 md:py-3 text-slate-400 text-[10px] md:text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs font-semibold text-darkblue">
                        <?= isset($d->nama_pos) ? htmlspecialchars($d->nama_pos) : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <p class="font-semibold text-darkblue text-[10px] md:text-xs leading-tight"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                        <p class="text-[9px] md:text-[10px] text-slate-400"><?= date('H:i', strtotime($d->created_at)) ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs text-slate-600">
                        <?= isset($d->nilai_1) ? htmlspecialchars($d->nilai_1) : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center text-[10px] md:text-xs">
                        <?= isset($d->nilai_2) ? number_format($d->nilai_2, 0, ',', '.') : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEditPantai(<?= htmlspecialchars(json_encode($d)) ?>)" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url($segment.'/hapus_pantai/'.$d->id_manual) ?>" onclick="return confirm('Hapus data pengaman pantai ini?')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="px-5 py-12 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-sm font-medium">Belum ada data pengaman pantai</p>
        <p class="text-xs mt-1">Klik tombol Tambah Data untuk menambahkan</p>
    </div>
    <?php endif; ?>
</div>

<!-- ============================================ -->
<!-- TABEL UNTUK ADMIN SEDIMEN -->
<!-- ============================================ -->
<?php elseif($admin_type == 'sedimen'): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">Data Pengendali Sedimen</h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($data_list) ?> data</span>
    </div>
    
    <?php if(!empty($data_list)): ?>
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-xs md:text-sm min-w-[700px]">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] md:text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold w-8">#</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Nama Aset</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Tanggal</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Jenis Bangunan</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Daya Tampung (m³)</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Panjang (m)</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-2 md:px-3 py-2 md:py-3 text-slate-400 text-[10px] md:text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs font-semibold text-darkblue">
                        <?= isset($d->nama_pos) ? htmlspecialchars($d->nama_pos) : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <p class="font-semibold text-darkblue text-[10px] md:text-xs leading-tight"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                        <p class="text-[9px] md:text-[10px] text-slate-400"><?= date('H:i', strtotime($d->created_at)) ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs text-slate-600">
                        <?= isset($d->nilai_1) ? htmlspecialchars($d->nilai_1) : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center text-[10px] md:text-xs">
                        <?= isset($d->nilai_2) ? number_format($d->nilai_2, 0, ',', '.') : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center text-[10px] md:text-xs">
                        <?= isset($d->nilai_3) ? number_format($d->nilai_3, 0, ',', '.') : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEditSedimen(<?= htmlspecialchars(json_encode($d)) ?>)" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url($segment.'/hapus_sedimen/'.$d->id_manual) ?>" onclick="return confirm('Hapus data pengendali sedimen ini?')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="px-5 py-12 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-sm font-medium">Belum ada data pengendali sedimen</p>
        <p class="text-xs mt-1">Klik tombol Tambah Data untuk menambahkan</p>
    </div>
    <?php endif; ?>
</div>

<!-- ============================================ -->
<!-- TABEL UNTUK ADMIN EMBUNG -->
<!-- ============================================ -->
<?php elseif($admin_type == 'embung'): ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-darkblue text-sm uppercase tracking-wider">
            Data Embung <?= !empty($pos) ? '- ' . htmlspecialchars($pos->nama_pos) : '' ?>
        </h3>
        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full"><?= count($data_list) ?> data</span>
    </div>
    
    <?php if(!empty($data_list)): ?>
    <div class="overflow-auto max-h-[500px]">
        <table class="w-full text-xs md:text-sm min-w-[700px]">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] md:text-xs sticky top-0 z-10">
                <tr>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold w-8">#</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Nama Embung</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-left font-bold">Tanggal</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Kapasitas (m³)</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold">Elevasi (mdpl)</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-3 text-center font-bold w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $no = 1; foreach($data_list as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-2 md:px-3 py-2 md:py-3 text-slate-400 text-[10px] md:text-xs"><?= $no++ ?></td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-[10px] md:text-xs font-semibold text-darkblue">
                        <?= isset($d->nama_pos) ? htmlspecialchars($d->nama_pos) : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <p class="font-semibold text-darkblue text-[10px] md:text-xs leading-tight"><?= date('d M Y', strtotime($d->tanggal_input)) ?></p>
                        <p class="text-[9px] md:text-[10px] text-slate-400"><?= date('H:i', strtotime($d->created_at)) ?></p>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center text-[10px] md:text-xs">
                        <?= isset($d->nilai_1) ? number_format($d->nilai_1, 0, ',', '.') : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3 text-center text-[10px] md:text-xs">
                        <?= isset($d->nilai_2) ? number_format($d->nilai_2, 2, ',', '.') : '-' ?>
                    </td>
                    <td class="px-2 md:px-3 py-2 md:py-3">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button" onclick="openModalEditEmbung(<?= htmlspecialchars(json_encode($d)) ?>)" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <a href="<?= base_url($segment.'/hapus_embung/'.$d->id_manual) ?>" onclick="return confirm('Hapus data embung ini?')" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="px-5 py-12 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-sm font-medium">Belum ada data embung</p>
        <p class="text-xs mt-1">Klik tombol Tambah Data untuk menambahkan</p>
    </div>
    <?php endif; ?>
</div>

<!-- ============================================ -->
<!-- TABEL POS BIASA (PCH / PDA) -->
<!-- ============================================ -->
<?php elseif(!$is_bendungan && !$is_bendung && !empty($pos)): ?>
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
                        <?= !empty($d->nama_user) ? htmlspecialchars($d->nama_user) : '<span class="text-slate-400">-</span>' ?>
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
                            <button type="button" onclick="openModalEditPos(<?= htmlspecialchars(json_encode($d)) ?>)" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
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
                            <button type="button" onclick="openModalEditBendungan(<?= htmlspecialchars(json_encode($d)) ?>)" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
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
<!-- TABEL BENDUNG -->
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
                            <button type="button" onclick="openModalEditBendung(<?= htmlspecialchars(json_encode($d)) ?>)" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" title="Edit">
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

<?php else: ?>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">
    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <h3 class="text-lg font-bold text-slate-700">Belum Ada Data</h3>
    <p class="text-slate-500 text-sm mt-1">Silakan pilih pos atau tambahkan data baru.</p>
</div>
<?php endif; ?>

<!-- ============================================ -->
<!-- MODAL UNTUK POS (PCH/PDA) -->
<!-- ============================================ -->
<div id="modalPos" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-darkblue">Tambah Data Manual</h3>
            <button onclick="closeModal('modalPos')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_data_pos') ?>" method="POST" class="p-6">
            <input type="hidden" name="id_pos" value="<?= isset($pos->id_pos) ? $pos->id_pos : 0 ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal *</label>
                    <input type="date" name="tanggal_input" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <?php if(isset($pos->tipe_pos) && $pos->tipe_pos == 'PCH'): ?>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                    <input type="number" step="0.1" name="rain" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow" placeholder="0.0">
                </div>
                <?php else: ?>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi Muka Air (cm)</label>
                    <input type="number" step="0.01" name="wlevel" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow" placeholder="0.00">
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow" placeholder="Keterangan tambahan..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Simpan</button>
                <button type="button" onclick="closeModal('modalPos')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL UNTUK BENDUNGAN -->
<!-- ============================================ -->
<div id="modalBendungan" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Data Bendungan</h3>
            <button onclick="closeModal('modalBendungan')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_bendungan') ?>" method="POST" class="p-6">
            <input type="hidden" name="id_pos" value="<?= isset($pos->id_pos) ? $pos->id_pos : 0 ?>">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal *</label>
                    <input type="date" name="tanggal_input" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                    <input type="number" step="0.1" name="rain" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NWL (m)</label>
                    <input type="number" step="0.01" name="nwl" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NWL Volume (jt.m³)</label>
                    <input type="number" step="0.0001" name="nwl_volume" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NWL Luas (km²)</label>
                    <input type="number" step="0.0001" name="nwl_luas" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Elevasi (m)</label>
                    <input type="number" step="0.01" name="elevasi" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Volume (jt.m³)</label>
                    <input type="number" step="0.0001" name="volume" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas (km²)</label>
                    <input type="number" step="0.0001" name="luas" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Inflow (m³/s)</label>
                    <input type="number" step="0.01" name="inflow" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">PLTM (m³/s)</label>
                    <input type="number" step="0.01" name="pltm" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Spillway (m³/s)</label>
                    <input type="number" step="0.01" name="spillway" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Total Outflow (m³/s)</label>
                    <input type="number" step="0.01" name="total_outflow" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">PLTA Status</label>
                    <select name="plta_status" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                        <option value="">Pilih</option>
                        <option value="on">ON</option>
                        <option value="off">OFF</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Irigasi Status</label>
                    <select name="irigasi_status" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                        <option value="">Pilih</option>
                        <option value="on">ON</option>
                        <option value="off">OFF</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tail Water</label>
                    <input type="text" name="tail_water" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Remb. V-Notch H (cm)</label>
                    <input type="number" step="0.01" name="rembesan_vnotch_h" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Remb. V-Notch Q (lt/s)</label>
                    <input type="number" step="0.01" name="rembesan_vnotch_q" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pump Pit L H (cm)</label>
                    <input type="number" step="0.01" name="rembesan_pump_pit_l_h" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pump Pit L Q (lt/s)</label>
                    <input type="number" step="0.01" name="rembesan_pump_pit_l_q" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pump Pit R H (cm)</label>
                    <input type="number" step="0.01" name="rembesan_pump_pit_r_h" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pump Pit R Q (lt/s)</label>
                    <input type="number" step="0.01" name="rembesan_pump_pit_r_q" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Mulai</label>
                    <input type="number" name="tahun_mulai_pembangunan" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipe Bendungan</label>
                    <input type="text" name="tipe_bendungan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Elevasi Mercu (mdpl)</label>
                    <input type="number" step="0.01" name="elevasi_mercu" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas DAS (km²)</label>
                    <input type="number" step="0.01" name="luas_das" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow"></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Simpan</button>
                <button type="button" onclick="closeModal('modalBendungan')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL UNTUK BENDUNG -->
<!-- ============================================ -->
<div id="modalBendung" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Data Bendung</h3>
            <button onclick="closeModal('modalBendung')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_bendung') ?>" method="POST" class="p-6">
            <input type="hidden" name="id_pos" value="<?= isset($pos->id_pos) ? $pos->id_pos : 0 ?>">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal *</label>
                    <input type="date" name="tanggal_input" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                    <input type="number" step="0.1" name="rain" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Elevasi Mercu (m)</label>
                    <input type="number" step="0.01" name="elevasi_mercu" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q Total (m³/dt)</label>
                    <input type="number" step="0.001" name="q_total" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q FC1 (m³/dt)</label>
                    <input type="number" step="0.001" name="q_fc1" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q FC2 (m³/dt)</label>
                    <input type="number" step="0.001" name="q_fc2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q Sal. Induk (m³/dt)</label>
                    <input type="number" step="0.001" name="q_sal_induk" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q Limpas (m³/dt)</label>
                    <input type="number" step="0.001" name="q_limpas" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q Sungai (m³/dt)</label>
                    <input type="number" step="0.001" name="q_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q SPAM KPBU (m³/dt)</label>
                    <input type="number" step="0.001" name="q_spam_kpbu" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sluice Gate (m³/dt)</label>
                    <input type="number" step="0.001" name="sluice_gate" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bukaan Pintu (m)</label>
                    <input type="number" step="0.001" name="bukaan_pintu" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow"></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Simpan</button>
                <button type="button" onclick="closeModal('modalBendung')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL UNTUK IRIGASI -->
<!-- ============================================ -->
<div id="modalIrigasi" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Data Irigasi</h3>
            <button onclick="closeModal('modalIrigasi')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_irigasi') ?>" method="POST" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Aset *</label>
                    <input type="text" name="nama_aset" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Daerah Irigasi</label>
                    <input type="text" name="jenis_daerah_irigasi" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kode Identifikasi</label>
                    <input type="text" name="kode_identifikasi" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wilayah Sungai</label>
                    <input type="text" name="wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">DAS</label>
                    <input type="text" name="daerah_aliran_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kewenangan</label>
                    <input type="text" name="kewenangan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Pemeliharaan</label>
                    <select name="status_pemeliharaan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                        <option value="">Pilih</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Tidak/Belum">Tidak/Belum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                    <input type="text" name="kabupaten_kota" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecamatan</label>
                    <input type="text" name="kecamatan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas Potensial (ha)</label>
                    <input type="number" step="0.01" name="luas_potensial" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas Fungsional (ha)</label>
                    <input type="number" step="0.01" name="luas_fungsional" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan Tambahan</label>
                    <textarea name="keterangan_tambahan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow"></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Simpan</button>
                <button type="button" onclick="closeModal('modalIrigasi')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL UNTUK PANTAI -->
<!-- ============================================ -->
<div id="modalPantai" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Data Pengaman Pantai</h3>
            <button onclick="closeModal('modalPantai')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_pantai') ?>" method="POST" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Aset *</label>
                    <input type="text" name="nama_aset" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Bangunan</label>
                    <select name="jenis_bangunan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                        <option value="">Pilih</option>
                        <option value="REVETMENT">REVETMENT</option>
                        <option value="JETTY">JETTY</option>
                        <option value="TANGGUL LAUT">TANGGUL LAUT</option>
                        <option value="TEMBOK LAUT">TEMBOK LAUT</option>
                        <option value="KRIB">KRIB</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sungai</label>
                    <input type="text" name="sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wilayah Sungai</label>
                    <input type="text" name="wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Panjang (m)</label>
                    <input type="number" step="0.01" name="panjang" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                    <input type="text" name="kabupaten_kota" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecamatan</label>
                    <input type="text" name="kecamatan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow"></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Simpan</button>
                <button type="button" onclick="closeModal('modalPantai')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL UNTUK SEDIMEN -->
<!-- ============================================ -->
<div id="modalSedimen" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Data Pengendali Sedimen</h3>
            <button onclick="closeModal('modalSedimen')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_sedimen') ?>" method="POST" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Aset *</label>
                    <input type="text" name="nama_aset" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Bangunan</label>
                    <select name="jenis_bangunan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                        <option value="">Pilih</option>
                        <option value="Cekdam">Cekdam</option>
                        <option value="Sabodam">Sabodam</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sungai</label>
                    <input type="text" name="sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">DAS</label>
                    <input type="text" name="daerah_aliran_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wilayah Sungai</label>
                    <input type="text" name="wilayah_sungai" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Daya Tampung (m³)</label>
                    <input type="number" step="0.01" name="daya_tampung" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Panjang (m)</label>
                    <input type="number" step="0.01" name="panjang" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi (m)</label>
                    <input type="number" step="0.01" name="tinggi" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                    <input type="text" name="kabupaten_kota" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecamatan</label>
                    <input type="text" name="kecamatan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow"></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Simpan</button>
                <button type="button" onclick="closeModal('modalSedimen')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL UNTUK EMBUNG -->
<!-- ============================================ -->
<div id="modalEmbung" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-darkblue">Tambah Data Embung</h3>
            <button onclick="closeModal('modalEmbung')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?= base_url($segment.'/simpan_embung') ?>" method="POST" class="p-6">
            <?php if(!empty($pos_list)): ?>
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Pos Embung *</label>
                <select name="id_pos" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                    <option value="">Pilih Pos Embung</option>
                    <?php foreach($pos_list as $pl): ?>
                    <option value="<?= $pl->id_pos ?>" <?= (!empty($pos) && $pl->id_pos == $pos->id_pos) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pl->nama_pos) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
            <input type="hidden" name="id_pos" value="<?= isset($pos->id_pos) ? $pos->id_pos : 0 ?>">
            <?php endif; ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kapasitas Volume (m³)</label>
                    <input type="number" step="0.01" name="kapasitas_volume" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Elevasi Puncak (mdpl)</label>
                    <input type="number" step="0.001" name="elevasi_puncak" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi Embung (m)</label>
                    <input type="number" step="0.01" name="tinggi_embung" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Panjang Tubuh (m)</label>
                    <input type="number" step="0.01" name="panjang_tubuh" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Mulai Pembangunan</label>
                    <input type="number" name="tahun_mulai_pembangunan" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Simpan</button>
                <button type="button" onclick="closeModal('modalEmbung')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT (GENERIC) - Diisi via AJAX -->
<!-- ============================================ -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
            <h3 id="modalEditTitle" class="text-lg font-bold text-darkblue">Edit Data</h3>
            <button onclick="closeModal('modalEdit')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="modalEditContent" class="p-6">
            <!-- Isi akan dimuat via AJAX -->
            <div class="text-center py-8 text-slate-400">Memuat data...</div>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts
setTimeout(function(){
    var s = document.getElementById('alert-success');
    var e = document.getElementById('alert-error');
    var i = document.getElementById('alert-info');
    if(s) s.style.display = 'none';
    if(e) e.style.display = 'none';
    if(i) i.style.display = 'none';
}, 5000);

// ==========================================
// FUNGSI MODAL UMUM
// ==========================================
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}

// Tutup modal jika klik di luar
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('fixed')) {
        e.target.classList.add('hidden');
        e.target.classList.remove('flex');
    }
});

// ==========================================
// MODAL EDIT GENERIC - Load via AJAX
// ==========================================
function openModalEdit(url, title) {
    document.getElementById('modalEditTitle').innerHTML = title;
    document.getElementById('modalEditContent').innerHTML = '<div class="text-center py-8 text-slate-400">Memuat data...</div>';
    openModal('modalEdit');
    
    // Load data via AJAX
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('modalEditContent').innerHTML = '<div class="text-center py-8 text-red-500">' + data.error + '</div>';
                return;
            }
            // Render form berdasarkan tipe
            document.getElementById('modalEditContent').innerHTML = renderEditForm(data);
        })
        .catch(error => {
            document.getElementById('modalEditContent').innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat data</div>';
        });
}

function renderEditForm(data) {
    // Deteksi tipe data dari field yang ada
    if (data.id_bendungan) {
        return renderEditBendungan(data);
    } else if (data.id_bendung) {
        return renderEditBendung(data);
    } else if (data.id_irigasi) {
        return renderEditIrigasi(data);
    } else if (data.id_pengaman) {
        return renderEditPantai(data);
    } else if (data.id_sedimen) {
        return renderEditSedimen(data);
    } else if (data.id_embung) {
        return renderEditEmbung(data);
    } else {
        return renderEditPos(data);
    }
}

// ==========================================
// RENDER EDIT POS (PCH/PDA)
// ==========================================
function renderEditPos(data) {
    var isPCH = data.tipe_pos == 'PCH';
    return `
    <form action="<?= base_url($segment.'/update_manual') ?>" method="POST">
        <input type="hidden" name="id_manual" value="${data.id_manual}">
        <input type="hidden" name="id_pos" value="${data.id_pos}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal *</label>
                <input type="date" name="tanggal" value="${data.tanggal_input}" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            ${isPCH ? `
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                <input type="number" step="0.1" name="rain" value="${data.rain || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            ` : `
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi Muka Air (cm)</label>
                <input type="number" step="0.01" name="wlevel" value="${data.wlevel || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            `}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">${data.keterangan || ''}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Update</button>
            <button type="button" onclick="closeModal('modalEdit')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
        </div>
    </form>
    `;
}

// ==========================================
// RENDER EDIT BENDUNGAN
// ==========================================
function renderEditBendungan(data) {
    return `
    <form action="<?= base_url($segment.'/update_bendungan') ?>" method="POST">
        <input type="hidden" name="id_bendungan" value="${data.id_bendungan}">
        <input type="hidden" name="id_pos" value="${data.id_pos}">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal *</label>
                <input type="date" name="tanggal" value="${data.tanggal_input}" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                <input type="number" step="0.1" name="rain" value="${data.rain || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NWL (m)</label>
                <input type="number" step="0.01" name="nwl" value="${data.nwl || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NWL Volume (jt.m³)</label>
                <input type="number" step="0.0001" name="nwl_volume" value="${data.nwl_volume || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NWL Luas (km²)</label>
                <input type="number" step="0.0001" name="nwl_luas" value="${data.nwl_luas || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Elevasi (m)</label>
                <input type="number" step="0.01" name="elevasi" value="${data.elevasi || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Volume (jt.m³)</label>
                <input type="number" step="0.0001" name="volume" value="${data.volume || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas (km²)</label>
                <input type="number" step="0.0001" name="luas" value="${data.luas || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Inflow (m³/s)</label>
                <input type="number" step="0.01" name="inflow" value="${data.inflow || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">PLTM (m³/s)</label>
                <input type="number" step="0.01" name="pltm" value="${data.pltm || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Spillway (m³/s)</label>
                <input type="number" step="0.01" name="spillway" value="${data.spillway || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Total Outflow (m³/s)</label>
                <input type="number" step="0.01" name="total_outflow" value="${data.total_outflow || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">PLTA Status</label>
                <select name="plta_status" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                    <option value="">Pilih</option>
                    <option value="on" ${data.plta_status == 'on' ? 'selected' : ''}>ON</option>
                    <option value="off" ${data.plta_status == 'off' ? 'selected' : ''}>OFF</option>
                    <option value="maintenance" ${data.plta_status == 'maintenance' ? 'selected' : ''}>Maintenance</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Irigasi Status</label>
                <select name="irigasi_status" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                    <option value="">Pilih</option>
                    <option value="on" ${data.irigasi_status == 'on' ? 'selected' : ''}>ON</option>
                    <option value="off" ${data.irigasi_status == 'off' ? 'selected' : ''}>OFF</option>
                    <option value="maintenance" ${data.irigasi_status == 'maintenance' ? 'selected' : ''}>Maintenance</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tail Water</label>
                <input type="text" name="tail_water" value="${data.tail_water || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Remb. V-Notch H (cm)</label>
                <input type="number" step="0.01" name="rembesan_vnotch_h" value="${data.rembesan_vnotch_h || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Remb. V-Notch Q (lt/s)</label>
                <input type="number" step="0.01" name="rembesan_vnotch_q" value="${data.rembesan_vnotch_q || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pump Pit L H (cm)</label>
                <input type="number" step="0.01" name="rembesan_pump_pit_l_h" value="${data.rembesan_pump_pit_l_h || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pump Pit L Q (lt/s)</label>
                <input type="number" step="0.01" name="rembesan_pump_pit_l_q" value="${data.rembesan_pump_pit_l_q || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pump Pit R H (cm)</label>
                <input type="number" step="0.01" name="rembesan_pump_pit_r_h" value="${data.rembesan_pump_pit_r_h || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pump Pit R Q (lt/s)</label>
                <input type="number" step="0.01" name="rembesan_pump_pit_r_q" value="${data.rembesan_pump_pit_r_q || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Mulai</label>
                <input type="number" name="tahun_mulai_pembangunan" value="${data.tahun_mulai_pembangunan || ''}" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipe Bendungan</label>
                <input type="text" name="tipe_bendungan" value="${data.tipe_bendungan || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Elevasi Mercu (mdpl)</label>
                <input type="number" step="0.01" name="elevasi_mercu" value="${data.elevasi_mercu || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas DAS (km²)</label>
                <input type="number" step="0.01" name="luas_das" value="${data.luas_das || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">${data.keterangan || ''}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Update</button>
            <button type="button" onclick="closeModal('modalEdit')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
        </div>
    </form>
    `;
}

// ==========================================
// RENDER EDIT BENDUNG
// ==========================================
function renderEditBendung(data) {
    return `
    <form action="<?= base_url($segment.'/update_bendung') ?>" method="POST">
        <input type="hidden" name="id_bendung" value="${data.id_bendung}">
        <input type="hidden" name="id_pos" value="${data.id_pos}">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal *</label>
                <input type="date" name="tanggal" value="${data.tanggal_input}" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Curah Hujan (mm)</label>
                <input type="number" step="0.1" name="rain" value="${data.rain || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Elevasi Mercu (m)</label>
                <input type="number" step="0.01" name="elevasi_mercu" value="${data.elevasi_mercu || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q Total (m³/dt)</label>
                <input type="number" step="0.001" name="q_total" value="${data.q_total || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q FC1 (m³/dt)</label>
                <input type="number" step="0.001" name="q_fc1" value="${data.q_fc1 || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q FC2 (m³/dt)</label>
                <input type="number" step="0.001" name="q_fc2" value="${data.q_fc2 || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q Sal. Induk (m³/dt)</label>
                <input type="number" step="0.001" name="q_sal_induk" value="${data.q_sal_induk || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q Limpas (m³/dt)</label>
                <input type="number" step="0.001" name="q_limpas" value="${data.q_limpas || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q Sungai (m³/dt)</label>
                <input type="number" step="0.001" name="q_sungai" value="${data.q_sungai || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Q SPAM KPBU (m³/dt)</label>
                <input type="number" step="0.001" name="q_spam_kpbu" value="${data.q_spam_kpbu || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sluice Gate (m³/dt)</label>
                <input type="number" step="0.001" name="sluice_gate" value="${data.sluice_gate || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bukaan Pintu (m)</label>
                <input type="number" step="0.001" name="bukaan_pintu" value="${data.bukaan_pintu || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">${data.keterangan || ''}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Update</button>
            <button type="button" onclick="closeModal('modalEdit')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
        </div>
    </form>
    `;
}

// ==========================================
// RENDER EDIT IRIGASI
// ==========================================
function renderEditIrigasi(data) {
    return `
    <form action="<?= base_url($segment.'/update_irigasi') ?>" method="POST">
        <input type="hidden" name="id_manual" value="${data.id_irigasi}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Aset *</label>
                <input type="text" name="nama_aset" value="${data.nama_aset || ''}" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Daerah Irigasi</label>
                <input type="text" name="jenis_daerah_irigasi" value="${data.jenis_daerah_irigasi || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kode Identifikasi</label>
                <input type="text" name="kode_identifikasi" value="${data.kode_identifikasi || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wilayah Sungai</label>
                <input type="text" name="wilayah_sungai" value="${data.wilayah_sungai || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">DAS</label>
                <input type="text" name="daerah_aliran_sungai" value="${data.daerah_aliran_sungai || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kewenangan</label>
                <input type="text" name="kewenangan" value="${data.kewenangan || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Pemeliharaan</label>
                <select name="status_pemeliharaan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                    <option value="">Pilih</option>
                    <option value="Sudah" ${data.status_pemeliharaan == 'Sudah' ? 'selected' : ''}>Sudah</option>
                    <option value="Tidak/Belum" ${data.status_pemeliharaan == 'Tidak/Belum' ? 'selected' : ''}>Tidak/Belum</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                <input type="text" name="kabupaten_kota" value="${data.kabupaten_kota || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecamatan</label>
                <input type="text" name="kecamatan" value="${data.kecamatan || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas Potensial (ha)</label>
                <input type="number" step="0.01" name="luas_potensial" value="${data.luas_potensial || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas Fungsional (ha)</label>
                <input type="number" step="0.01" name="luas_fungsional" value="${data.luas_fungsional || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan Tambahan</label>
                <textarea name="keterangan_tambahan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">${data.keterangan_tambahan || ''}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Update</button>
            <button type="button" onclick="closeModal('modalEdit')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
        </div>
    </form>
    `;
}

// ==========================================
// RENDER EDIT PANTAI
// ==========================================
function renderEditPantai(data) {
    return `
    <form action="<?= base_url($segment.'/update_pantai') ?>" method="POST">
        <input type="hidden" name="id_manual" value="${data.id_pengaman}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Aset *</label>
                <input type="text" name="nama_aset" value="${data.nama_aset || ''}" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Bangunan</label>
                <select name="jenis_bangunan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                    <option value="">Pilih</option>
                    <option value="REVETMENT" ${data.jenis_bangunan == 'REVETMENT' ? 'selected' : ''}>REVETMENT</option>
                    <option value="JETTY" ${data.jenis_bangunan == 'JETTY' ? 'selected' : ''}>JETTY</option>
                    <option value="TANGGUL LAUT" ${data.jenis_bangunan == 'TANGGUL LAUT' ? 'selected' : ''}>TANGGUL LAUT</option>
                    <option value="TEMBOK LAUT" ${data.jenis_bangunan == 'TEMBOK LAUT' ? 'selected' : ''}>TEMBOK LAUT</option>
                    <option value="KRIB" ${data.jenis_bangunan == 'KRIB' ? 'selected' : ''}>KRIB</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sungai</label>
                <input type="text" name="sungai" value="${data.sungai || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wilayah Sungai</label>
                <input type="text" name="wilayah_sungai" value="${data.wilayah_sungai || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Panjang (m)</label>
                <input type="number" step="0.01" name="panjang" value="${data.panjang || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                <input type="text" name="kabupaten_kota" value="${data.kabupaten_kota || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecamatan</label>
                <input type="text" name="kecamatan" value="${data.kecamatan || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">${data.keterangan || ''}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Update</button>
            <button type="button" onclick="closeModal('modalEdit')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
        </div>
    </form>
    `;
}

// ==========================================
// RENDER EDIT SEDIMEN
// ==========================================
function renderEditSedimen(data) {
    return `
    <form action="<?= base_url($segment.'/update_sedimen') ?>" method="POST">
        <input type="hidden" name="id_manual" value="${data.id_sedimen}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Aset *</label>
                <input type="text" name="nama_aset" value="${data.nama_aset || ''}" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Bangunan</label>
                <select name="jenis_bangunan" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
                    <option value="">Pilih</option>
                    <option value="Cekdam" ${data.jenis_bangunan == 'Cekdam' ? 'selected' : ''}>Cekdam</option>
                    <option value="Sabodam" ${data.jenis_bangunan == 'Sabodam' ? 'selected' : ''}>Sabodam</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sungai</label>
                <input type="text" name="sungai" value="${data.sungai || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">DAS</label>
                <input type="text" name="daerah_aliran_sungai" value="${data.daerah_aliran_sungai || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wilayah Sungai</label>
                <input type="text" name="wilayah_sungai" value="${data.wilayah_sungai || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Daya Tampung (m³)</label>
                <input type="number" step="0.01" name="daya_tampung" value="${data.daya_tampung || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Panjang (m)</label>
                <input type="number" step="0.01" name="panjang" value="${data.panjang || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi (m)</label>
                <input type="number" step="0.01" name="tinggi" value="${data.tinggi || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kabupaten/Kota</label>
                <input type="text" name="kabupaten_kota" value="${data.kabupaten_kota || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecamatan</label>
                <input type="text" name="kecamatan" value="${data.kecamatan || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">${data.keterangan || ''}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Update</button>
            <button type="button" onclick="closeModal('modalEdit')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
        </div>
    </form>
    `;
}

// ==========================================
// RENDER EDIT EMBUNG
// ==========================================
function renderEditEmbung(data) {
    return `
    <form action="<?= base_url($segment.'/update_embung') ?>" method="POST">
        <input type="hidden" name="id_manual" value="${data.id_embung}">
        <input type="hidden" name="id_pos" value="${data.id_pos}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kapasitas Volume (m³)</label>
                <input type="number" step="0.01" name="kapasitas_volume" value="${data.kapasitas_volume || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Elevasi Puncak (mdpl)</label>
                <input type="number" step="0.001" name="elevasi_puncak" value="${data.elevasi_puncak || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi Embung (m)</label>
                <input type="number" step="0.01" name="tinggi_embung" value="${data.tinggi_embung || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Panjang Tubuh (m)</label>
                <input type="number" step="0.01" name="panjang_tubuh" value="${data.panjang_tubuh || ''}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Mulai Pembangunan</label>
                <input type="number" name="tahun_mulai_pembangunan" value="${data.tahun_mulai_pembangunan || ''}" min="1900" max="<?= date('Y') ?>" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow">
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="flex-1 px-6 py-2.5 bg-darkblue hover:bg-blue-900 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-darkblue/10">Update</button>
            <button type="button" onclick="closeModal('modalEdit')" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition-all">Batal</button>
        </div>
    </form>
    `;
}

// ==========================================
// FUNGSI OPEN MODAL EDIT UNTUK MASING-MASING TIPE
// ==========================================

// POS
function openModalEditPos(data) {
    var url = '<?= base_url($segment.'/get_manual_json/') ?>' + data.id_manual;
    openModalEdit(url, 'Edit Data Manual');
}

// Bendungan
function openModalEditBendungan(data) {
    var url = '<?= base_url($segment.'/get_bendungan_json/') ?>' + data.id_bendungan;
    openModalEdit(url, 'Edit Data Bendungan');
}

// Bendung
function openModalEditBendung(data) {
    var url = '<?= base_url($segment.'/get_bendung_json/') ?>' + data.id_bendung;
    openModalEdit(url, 'Edit Data Bendung');
}

// Irigasi
function openModalEditIrigasi(data) {
    var url = '<?= base_url($segment.'/get_irigasi_json/') ?>' + data.id_manual;
    openModalEdit(url, 'Edit Data Irigasi');
}

// Pantai
function openModalEditPantai(data) {
    var url = '<?= base_url($segment.'/get_pantai_json/') ?>' + data.id_manual;
    openModalEdit(url, 'Edit Data Pengaman Pantai');
}

// Sedimen
function openModalEditSedimen(data) {
    var url = '<?= base_url($segment.'/get_sedimen_json/') ?>' + data.id_manual;
    openModalEdit(url, 'Edit Data Pengendali Sedimen');
}

// Embung
function openModalEditEmbung(data) {
    var url = '<?= base_url($segment.'/get_embung_json/') ?>' + data.id_manual;
    openModalEdit(url, 'Edit Data Embung');
}

// ==========================================
// FUNGSI OPEN MODAL TAMBAH
// ==========================================
function openModalPos() { openModal('modalPos'); }
function openModalBendungan() { openModal('modalBendungan'); }
function openModalBendung() { openModal('modalBendung'); }
function openModalIrigasi() { openModal('modalIrigasi'); }
function openModalPantai() { openModal('modalPantai'); }
function openModalSedimen() { openModal('modalSedimen'); }
function openModalEmbung() { openModal('modalEmbung'); }
</script>