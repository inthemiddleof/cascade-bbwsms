<?php
$selected_date = $this->input->get('date') ?: date('Y-m-d');
$total_pos = count($pch_db) + count($pda_db) + count($bendungan_db);
$bendung_count = isset($bendung_count) ? $bendung_count : 0;
$embung_count = isset($embung_count) ? $embung_count : 0;
$pengaman_count = isset($pengaman_count) ? $pengaman_count : 0;
$sedimen_count = isset($sedimen_count) ? $sedimen_count : 0;
$irigasi_count = isset($irigasi_count) ? $irigasi_count : 0;
$embung_db = isset($embung_db) ? $embung_db : [];
$pengaman_db = isset($pengaman_db) ? $pengaman_db : [];
$sedimen_db = isset($sedimen_db) ? $sedimen_db : [];
$irigasi_db = isset($irigasi_db) ? $irigasi_db : [];
?>

<!-- ============================================ -->
<!-- TOP INFO BAR (Desktop Only) -->
<!-- ============================================ -->
<div class="hidden lg:block fixed top-0 left-0 right-0 z-[2000] px-5 pt-3 pointer-events-none">
    <div class="max-w-7xl mx-auto flex justify-between items-start">
        <div class="flex items-center gap-3 bg-white/70 backdrop-blur-xl rounded-2xl px-5 py-3 shadow-lg shadow-black/5 border border-white/40 pointer-events-auto">
            <img src="<?= base_url('assets/img/logobbwsmsbaru.png') ?>" alt="Logo BBWS Mesuji Sekampung" class="h-9 w-auto">
            <div>
                <h1 class="font-black text-darkblue text-sm leading-tight">HydroSmart</h1>
                <p class="text-[10px] text-slate-500 font-medium">BBWS Mesuji Sekampung</p>
            </div>
        </div>
        <div class="flex gap-2 pointer-events-auto">
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl px-4 py-3 shadow-lg shadow-black/5 border border-white/40 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <div><p class="text-[9px] text-slate-400 uppercase font-bold">Tanggal</p><p class="text-xs font-bold text-darkblue"><?= date('d M Y', strtotime($selected_date)) ?></p></div>
            </div>
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl px-4 py-3 shadow-lg shadow-black/5 border border-white/40 flex items-center gap-2 cursor-pointer hover:bg-white/90 transition-all" onclick="toggleDesktopPanel('info')">
                <svg class="w-4 h-4 text-darkblue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div><p class="text-[9px] text-slate-400 uppercase font-bold">Info</p><p class="text-xs font-bold text-darkblue"><?= $total_pos ?> Pos</p></div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- HERO SECTION -->
<!-- ============================================ -->
<header class="relative w-full h-screen min-h-[500px] md:min-h-[600px] overflow-hidden">
    <div id="hero-map" class="absolute top-0 left-0 w-full h-full z-0"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-transparent to-black/30 z-[1] pointer-events-none"></div>

    <!-- MOBILE: Floating Buttons -->
    <div class="lg:hidden fixed bottom-6 left-0 right-0 z-[1000] px-4">
        <div class="flex justify-center gap-2">
            <button onclick="openSlideUp('info')" class="bg-white/90 backdrop-blur-md rounded-full px-5 py-3 shadow-lg border border-white/40 text-xs font-semibold text-darkblue hover:bg-white transition-all flex items-center gap-2 active:scale-95" aria-label="Informasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Info
            </button>
            <button onclick="openSlideUp('layer')" class="bg-white/90 backdrop-blur-md rounded-full w-12 h-12 shadow-lg border border-white/40 flex items-center justify-center text-darkblue hover:bg-white transition-all active:scale-95" aria-label="Layer Peta">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 8h18M3 12h18M3 16h18M3 20h18"/></svg>
            </button>
            <button onclick="openSlideUp('search')" class="bg-white/90 backdrop-blur-md rounded-full w-12 h-12 shadow-lg border border-white/40 flex items-center justify-center text-darkblue hover:bg-white transition-all active:scale-95" aria-label="Cari Pos">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </div>
    </div>

    <!-- SLIDE UP PANEL -->
    <div id="slide-up-panel" class="lg:hidden fixed inset-x-0 bottom-0 z-[6000] transform translate-y-full transition-transform duration-400 ease-out">
        <div class="bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 max-h-[70vh] overflow-y-auto">
            <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1.5 bg-slate-300 rounded-full"></div></div>
            <div class="flex justify-between items-center px-5 pb-3">
                <h3 id="slide-up-title" class="text-base font-bold text-darkblue"></h3>
                <button onclick="closeSlideUp()" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-all" aria-label="Tutup panel"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div id="slide-up-content" class="px-5 pb-8"></div>
        </div>
    </div>
    <div id="slide-up-backdrop" class="lg:hidden fixed inset-0 bg-black/40 z-[5000] opacity-0 invisible transition-all duration-300" onclick="closeSlideUp()"></div>

    <!-- DESKTOP: LEFT BUTTONS -->
    <div class="hidden lg:flex absolute top-1/2 -translate-y-1/2 left-5 z-[1000] flex-col gap-3">
        <button onclick="toggleDesktopPanel('info')" class="w-11 h-11 rounded-2xl bg-white/40 backdrop-blur-xl border border-white/30 shadow-lg shadow-black/5 flex items-center justify-center text-slate-600 hover:bg-white/60 hover:shadow-xl transition-all duration-300" title="Informasi & Filter" aria-label="Informasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </button>
        <button onclick="toggleDesktopPanel('layer')" class="w-11 h-11 rounded-2xl bg-white/40 backdrop-blur-xl border border-white/30 shadow-lg shadow-black/5 flex items-center justify-center text-slate-600 hover:bg-white/60 hover:shadow-xl transition-all duration-300" title="Layer Peta" aria-label="Layer Peta">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 8h18M3 12h18M3 16h18M3 20h18"/></svg>
        </button>
        <button onclick="toggleDesktopPanel('search')" class="w-11 h-11 rounded-2xl bg-white/40 backdrop-blur-xl border border-white/30 shadow-lg shadow-black/5 flex items-center justify-center text-slate-600 hover:bg-white/60 hover:shadow-xl transition-all duration-300" title="Cari Pos" aria-label="Cari Pos">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </button>
    </div>

    <!-- DESKTOP: SEARCH PANEL -->
    <div id="desktop-panel-search" class="desktop-slide-panel hidden lg:block absolute top-1/2 -translate-y-1/2 left-[76px] z-[1000] bg-white/40 backdrop-blur-xl rounded-2xl shadow-xl shadow-black/5 border border-white/30 p-4 w-64 opacity-0 invisible -translate-y-2 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between mb-3"><p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Cari & Filter</p><button onclick="closeAllDesktopPanels()" class="w-6 h-6 rounded-lg bg-white/60 flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-700 transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        <div class="mb-3 bg-white/50 rounded-xl p-3"><div class="flex gap-1.5 mt-1"><input type="date" id="history-date" value="<?= $selected_date ?>" class="flex-1 px-2.5 py-2 rounded-lg bg-white/80 border border-white/40 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandyellow font-medium"><button onclick="applyHistoryDate()" class="px-3 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-[10px] transition-all">Cari</button></div></div>
        <div class="relative"><input type="text" id="search-input-desktop" placeholder="Ketik nama pos..." class="w-full pl-10 pr-4 py-2.5 bg-white/70 rounded-xl border border-white/40 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brandyellow focus:bg-white transition-all font-medium" onkeyup="handleSearch(this.value,'desktop')" autocomplete="off"><svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
        <div id="search-results-desktop" class="max-h-60 overflow-y-auto mt-3"><div id="search-results-list-desktop" class="space-y-0.5"></div><div id="search-no-results-desktop" class="px-4 py-6 text-center text-xs text-slate-400 hidden"><svg class="w-6 h-6 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><p class="font-medium">Tidak ditemukan</p></div></div>
    </div>

    <!-- DESKTOP: INFO PANEL -->
    <div id="desktop-panel-info" class="desktop-slide-panel hidden lg:block absolute top-1/2 -translate-y-1/2 left-[76px] z-[1000] bg-white/40 backdrop-blur-xl rounded-2xl shadow-xl shadow-black/5 border border-white/30 p-4 w-72 opacity-0 invisible -translate-y-2 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between mb-3"><p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Informasi</p><button onclick="closeAllDesktopPanels()" class="w-6 h-6 rounded-lg bg-white/60 flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-700 transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        <div class="grid grid-cols-3 gap-1.5 mb-3">
            <div class="bg-red-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">Bendungan</p><p class="text-sm font-black text-darkblue"><?= count($bendungan_db) ?></p></div>
            <div class="bg-cyan-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">Bendung</p><p class="text-sm font-black text-darkblue"><?= $bendung_count ?></p></div>
            <div class="bg-emerald-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">Embung</p><p class="text-sm font-black text-darkblue"><?= $embung_count ?></p></div>
            <div class="bg-orange-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">Pengaman Pantai</p><p class="text-sm font-black text-darkblue"><?= $pengaman_count ?></p></div>
            <div class="bg-yellow-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">Pengendali Sedimen</p><p class="text-sm font-black text-darkblue"><?= $sedimen_count ?></p></div>
            <div class="bg-blue-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">PCH</p><p class="text-sm font-black text-darkblue"><?= count($pch_db) ?></p></div>
            <div class="bg-purple-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">PDA</p><p class="text-sm font-black text-darkblue"><?= count($pda_db) ?></p></div>
            <div class="bg-green-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">Irigasi</p><p class="text-sm font-black text-darkblue"><?= $irigasi_count ?></p></div>
        </div>
        <div class="border-t border-white/30 my-3"></div>
        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</p>
        <div class="space-y-1">
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span><span class="text-[10px] text-slate-600">Bendungan</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-cyan-600 flex-shrink-0"></span><span class="text-[10px] text-slate-600">Bendung</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span><span class="text-[10px] text-slate-600">Embung</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-orange-500 flex-shrink-0"></span><span class="text-[10px] text-slate-600">Pengaman Pantai</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-yellow-500 flex-shrink-0"></span><span class="text-[10px] text-slate-600">Pengendali Sedimen</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-green-600 flex-shrink-0"></span><span class="text-[10px] text-slate-600">Daerah Irigasi</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-blue-600 flex-shrink-0"></span><span class="text-[10px] text-slate-600">PCH (Hujan)</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-purple-600 flex-shrink-0"></span><span class="text-[10px] text-slate-600">PDA (TMA)</span></div>
        </div>
    </div>

    <!-- DESKTOP: LAYER PANEL - DEFAULT BENDUNGAN & BENDUNG SAJA -->
    <div id="desktop-panel-layer" class="desktop-slide-panel hidden lg:block absolute top-1/2 -translate-y-1/2 left-[76px] z-[1000] bg-white/40 backdrop-blur-xl rounded-2xl shadow-xl shadow-black/5 border border-white/30 p-4 w-64 opacity-0 invisible -translate-y-2 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between mb-2"><p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Layers</p><button onclick="closeAllDesktopPanels()" class="w-6 h-6 rounded-lg bg-white/60 flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-700 transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        
        <!-- WS MESUJI-TULANG BAWANG -->
        <div class="mt-1 mb-0.5"><p class="text-[8px] font-bold text-indigo-600 uppercase tracking-wider">Mesuji-Tulang Bawang</p></div>
        <label class="flex items-center gap-2 px-2 py-1 rounded-lg cursor-pointer bg-indigo-50/50 hover:bg-indigo-100/50 transition-all border border-indigo-200/30"><input type="checkbox" onchange="toggleWS('MTB',this)" class="w-3.5 h-3.5 rounded accent-indigo-500"><span class="text-[9px] font-medium text-slate-600">Semua</span></label>
        <div class="grid grid-cols-2 gap-0.5 ml-1">
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" checked onchange="toggleLayer('bendungan_MTB',this)" class="w-3 h-3 rounded accent-red-500"><span class="text-[8px] text-slate-500">Bendungan</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" checked onchange="toggleLayer('bendung_MTB',this)" class="w-3 h-3 rounded accent-cyan-500"><span class="text-[8px] text-slate-500">Bendung</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('embung_MTB',this)" class="w-3 h-3 rounded accent-emerald-500"><span class="text-[8px] text-slate-500">Embung</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pengaman_MTB',this)" class="w-3 h-3 rounded accent-orange-500"><span class="text-[8px] text-slate-500">Pengaman Pantai</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('sedimen_MTB',this)" class="w-3 h-3 rounded accent-yellow-500"><span class="text-[8px] text-slate-500">Pengendali Sedimen</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('irigasi_MTB',this)" class="w-3 h-3 rounded accent-green-500"><span class="text-[8px] text-slate-500">Daerah Irigasi</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pch_MTB',this)" class="w-3 h-3 rounded accent-blue-500"><span class="text-[8px] text-slate-500">PCH</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pda_MTB',this)" class="w-3 h-3 rounded accent-purple-500"><span class="text-[8px] text-slate-500">PDA</span></label>
        </div>

        <!-- WS SEPUTIH-SEKAMPUNG -->
        <div class="mt-1 mb-0.5"><p class="text-[8px] font-bold text-blue-600 uppercase tracking-wider">Seputih-Sekampung</p></div>
        <label class="flex items-center gap-2 px-2 py-1 rounded-lg cursor-pointer bg-blue-50/50 hover:bg-blue-100/50 transition-all border border-blue-200/30"><input type="checkbox" onchange="toggleWS('SS',this)" class="w-3.5 h-3.5 rounded accent-blue-500"><span class="text-[9px] font-medium text-slate-600">Semua</span></label>
        <div class="grid grid-cols-2 gap-0.5 ml-1">
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" checked onchange="toggleLayer('bendungan_SS',this)" class="w-3 h-3 rounded accent-red-500"><span class="text-[8px] text-slate-500">Bendungan</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" checked onchange="toggleLayer('bendung_SS',this)" class="w-3 h-3 rounded accent-cyan-500"><span class="text-[8px] text-slate-500">Bendung</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('embung_SS',this)" class="w-3 h-3 rounded accent-emerald-500"><span class="text-[8px] text-slate-500">Embung</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pengaman_SS',this)" class="w-3 h-3 rounded accent-orange-500"><span class="text-[8px] text-slate-500">Pengaman Pantai</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('sedimen_SS',this)" class="w-3 h-3 rounded accent-yellow-500"><span class="text-[8px] text-slate-500">Pengendali Sedimen</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('irigasi_SS',this)" class="w-3 h-3 rounded accent-green-500"><span class="text-[8px] text-slate-500">Daerah Irigasi</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pch_SS',this)" class="w-3 h-3 rounded accent-blue-500"><span class="text-[8px] text-slate-500">PCH</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pda_SS',this)" class="w-3 h-3 rounded accent-purple-500"><span class="text-[8px] text-slate-500">PDA</span></label>
        </div>

        <!-- WS SEMANGKA -->
        <div class="mt-1 mb-0.5"><p class="text-[8px] font-bold text-green-600 uppercase tracking-wider">Semangka</p></div>
        <label class="flex items-center gap-2 px-2 py-1 rounded-lg cursor-pointer bg-green-50/50 hover:bg-green-100/50 transition-all border border-green-200/30"><input type="checkbox" onchange="toggleWS('SM',this)" class="w-3.5 h-3.5 rounded accent-green-500"><span class="text-[9px] font-medium text-slate-600">Semua</span></label>
        <div class="grid grid-cols-2 gap-0.5 ml-1">
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" checked onchange="toggleLayer('bendungan_SM',this)" class="w-3 h-3 rounded accent-red-500"><span class="text-[8px] text-slate-500">Bendungan</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" checked onchange="toggleLayer('bendung_SM',this)" class="w-3 h-3 rounded accent-cyan-500"><span class="text-[8px] text-slate-500">Bendung</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('embung_SM',this)" class="w-3 h-3 rounded accent-emerald-500"><span class="text-[8px] text-slate-500">Embung</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pengaman_SM',this)" class="w-3 h-3 rounded accent-orange-500"><span class="text-[8px] text-slate-500">Pengaman Pantai</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('sedimen_SM',this)" class="w-3 h-3 rounded accent-yellow-500"><span class="text-[8px] text-slate-500">Pengendali Sedimen</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('irigasi_SM',this)" class="w-3 h-3 rounded accent-green-500"><span class="text-[8px] text-slate-500">Daerah Irigasi</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pch_SM',this)" class="w-3 h-3 rounded accent-blue-500"><span class="text-[8px] text-slate-500">PCH</span></label>
            <label class="flex items-center gap-1.5 px-1.5 py-0.5 rounded cursor-pointer hover:bg-white/30 transition-all"><input type="checkbox" onchange="toggleLayer('pda_SM',this)" class="w-3 h-3 rounded accent-purple-500"><span class="text-[8px] text-slate-500">PDA</span></label>
        </div>

        <div class="my-2 border-t border-white/30"></div>
        <div class="flex gap-1.5">
            <button onclick="switchBaseMap('osm')" id="btn-osm" class="flex-1 py-2 text-[10px] font-semibold rounded-xl bg-white/60 text-slate-700 hover:bg-white transition-all">Peta</button>
            <button onclick="switchBaseMap('satellite')" id="btn-satellite" class="flex-1 py-2 text-[10px] font-semibold rounded-xl text-slate-500 hover:bg-white/60 transition-all">Satelit</button>
        </div>
    </div>

    <!-- ZOOM CONTROL -->
    <div class="absolute bottom-4 md:bottom-6 right-4 md:right-5 z-[1000]">
        <div class="flex flex-col rounded-2xl bg-white/40 backdrop-blur-xl border border-white/30 shadow-lg overflow-hidden">
            <button onclick="heroMap.zoomIn()" class="w-10 h-10 md:w-11 md:h-11 flex items-center justify-center text-slate-600 hover:bg-white/60 transition-all text-base font-medium border-b border-white/20" aria-label="Perbesar peta">+</button>
            <button onclick="heroMap.zoomOut()" class="w-10 h-10 md:w-11 md:h-11 flex items-center justify-center text-slate-600 hover:bg-white/60 transition-all text-base font-medium" aria-label="Perkecil peta">&minus;</button>
        </div>
    </div>
</header>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .ws-label{background:rgba(10,42,74,0.85);border:1px solid #feb700;color:#fff;font-weight:600;font-size:10px;padding:3px 8px;border-radius:6px;box-shadow:0 4px 6px rgba(0,0,0,0.1)}
    .das-label{background:rgba(211,84,0,0.8);border:1px solid #fff;color:#fff;font-size:9px;padding:2px 5px;border-radius:3px}
    .custom-marker,.custom-hero-icon,.leaflet-div-icon{background:transparent!important;border:none!important;outline:none!important}
    .leaflet-marker-icon{border:none!important;background:transparent!important}
    .custom-leaflet-popup .leaflet-popup-content-wrapper{padding:0!important;overflow:hidden;border-radius:16px!important;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1)!important;border:none!important}
    .custom-leaflet-popup .leaflet-popup-content{margin:0!important}
    .custom-leaflet-popup .leaflet-popup-close-button{top:10px!important;right:10px!important;width:22px!important;height:22px!important;display:flex!important;align-items:center!important;justify-content:center!important;background:rgba(0,0,0,0.2)!important;border-radius:50%!important;color:#fff!important;font-size:13px!important;font-weight:400!important;line-height:1!important;padding:0!important;transition:all 0.2s ease;z-index:10}
    .custom-leaflet-popup .leaflet-popup-close-button:hover{background:rgba(0,0,0,0.4)!important;color:#fff!important}
    path.leaflet-interactive:focus,.leaflet-container :focus{outline:none}
    .desktop-slide-panel.show{opacity:1!important;visibility:visible!important;transform:translateY(-50%)!important}
    #slide-up-panel{transition:transform 0.4s cubic-bezier(0.4,0,0.2,1)}
    #slide-up-panel.open{transform:translateY(0)!important}
    #slide-up-backdrop.show{opacity:1!important;visibility:visible!important}
    @keyframes pulse-marker{0%{transform:scale(1);opacity:0.4}100%{transform:scale(3);opacity:0}}
    @media(max-width:640px){.leaflet-popup{max-width:280px!important}.custom-leaflet-popup .leaflet-popup-content-wrapper{max-width:280px!important}}
    @media(min-width:641px) and (max-width:1024px){.leaflet-popup{max-width:320px!important}}
    #slide-up-content::-webkit-scrollbar{width:4px}
    #slide-up-content::-webkit-scrollbar-track{background:transparent}
    #slide-up-content::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:2px}
    #search-results-desktop::-webkit-scrollbar{width:3px}
    #search-results-desktop::-webkit-scrollbar-track{background:transparent}
    #search-results-desktop::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:2px}
</style>

<script>
document.addEventListener('DOMContentLoaded',function(){
// =============================================
// FUNGSI FORMAT TANGGAL UNTUK POPUP (KONSISTEN)
// =============================================
function fmtUpdate(dtStr) {
    if (!dtStr) return '-';
    var d = new Date(dtStr);
    if (isNaN(d.getTime())) return '-';
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    var dayName = days[d.getDay()];
    var day = d.getDate().toString().padStart(2, '0');
    var month = months[d.getMonth()];
    var year = d.getFullYear();
    var hours = d.getHours().toString().padStart(2, '0');
    var minutes = d.getMinutes().toString().padStart(2, '0');
    return dayName + ', ' + day + ' ' + month + ' ' + year + ' ' + hours + ':' + minutes + ' WIB';
}

// =============================================
// FUNGSI FORMAT ANGKA
// =============================================
function idNum(val, dec) {
    if (val === null || val === undefined || val === '' || isNaN(parseFloat(val))) return null;
    return parseFloat(val).toFixed(dec).replace('.', ',');
}

// =============================================
// INISIALISASI MAP
// =============================================
var isMobile = window.innerWidth < 1024;
var osm = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
    attribution: '© OpenStreetMap',
    subdomains: 'abcd',
    maxZoom: 20
});
var satellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
});

var heroMap = L.map('hero-map', {
    zoomControl: false,
    dragging: true,
    scrollWheelZoom: true,
    doubleClickZoom: true,
    boxZoom: true,
    touchZoom: true,
    layers: [osm],
    minZoom: isMobile ? 7 : 8
}).setView([-5.3971, 105.2668], isMobile ? 8 : 9);
window.heroMap = heroMap;

// =============================================
// LAYER PER WILAYAH SUNGAI (3 WS)
// =============================================

// WS MESUJI-TULANG BAWANG (MTB)
var layerBendungan_MTB = L.layerGroup().addTo(heroMap);
var layerBendung_MTB = L.layerGroup().addTo(heroMap);
var layerEmbung_MTB = L.layerGroup();
var layerPengaman_MTB = L.layerGroup();
var layerSedimen_MTB = L.layerGroup();
var layerIrigasi_MTB = L.layerGroup();
var layerPCH_MTB = L.layerGroup();
var layerPDA_MTB = L.layerGroup();

// WS SEPUTIH-SEKAMPUNG (SS)
var layerBendungan_SS = L.layerGroup().addTo(heroMap);
var layerBendung_SS = L.layerGroup().addTo(heroMap);
var layerEmbung_SS = L.layerGroup();
var layerPengaman_SS = L.layerGroup();
var layerSedimen_SS = L.layerGroup();
var layerIrigasi_SS = L.layerGroup();
var layerPCH_SS = L.layerGroup();
var layerPDA_SS = L.layerGroup();

// WS SEMANGKA (SM)
var layerBendungan_SM = L.layerGroup().addTo(heroMap);
var layerBendung_SM = L.layerGroup().addTo(heroMap);
var layerEmbung_SM = L.layerGroup();
var layerPengaman_SM = L.layerGroup();
var layerSedimen_SM = L.layerGroup();
var layerIrigasi_SM = L.layerGroup();
var layerPCH_SM = L.layerGroup();
var layerPDA_SM = L.layerGroup();

window.addEventListener('resize', function() {
    var n = window.innerWidth < 1024;
    if (n !== isMobile) {
        isMobile = n;
        heroMap.setMinZoom(isMobile ? 7 : 8);
    }
});

// =============================================
// FUNGSI MASK & ICON
// =============================================
function createMask(d) {
    var o = [[[-90, -180], [-90, 180], [90, 180], [90, -180], [-90, -180]]];
    d.features.forEach(function(f) {
        if (f.geometry.type === 'Polygon') {
            var c = f.geometry.coordinates.map(function(r) {
                return r.map(function(c) {
                    return [c[1], c[0]];
                });
            });
            o.push(c[0]);
        } else if (f.geometry.type === 'MultiPolygon') {
            f.geometry.coordinates.forEach(function(p) {
                var c = p.map(function(r) {
                    return r.map(function(c) {
                        return [c[1], c[0]];
                    });
                });
                o.push(c[0]);
            });
        }
    });
    return L.polygon(o, {
        color: 'none',
        weight: 0,
        fillColor: '#f1f5f9',
        fillOpacity: 1,
        interactive: false
    });
}

function createCustomIcon(color, shouldPulse) {
    var pulseHTML = shouldPulse ? '<div style="position:absolute;top:-5px;left:-5px;width:24px;height:24px;border-radius:50%;background:' + color + ';opacity:0.3;animation:pulse-marker 2s infinite;"></div>' : '';
    return L.divIcon({
        html: '<div style="position:relative;width:14px;height:14px;">' + pulseHTML + '<div style="position:absolute;top:1px;left:1px;width:12px;height:12px;border-radius:50%;background:' + color + ';box-shadow:0 0 8px ' + color + '99;border:2px solid #fff;"></div></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
}

function getPopupWidth() {
    if (window.innerWidth < 640) return 280;
    if (window.innerWidth < 1024) return 340;
    return 360;
}

// =============================================
// FILTER DATA BERDASARKAN WS
// =============================================
function filterByWS(data, ws) {
    if (!data || data.length === 0) return [];
    return data.filter(function(item) {
        return item.wilayah_sungai === ws;
    });
}

// =============================================
// GEOJSON DAS & WS
// =============================================
var dasData = <?= $das_geojson ?>;
if (dasData) {
    L.geoJSON(dasData, {
        style: {
            fillColor: "transparent",
            weight: 1,
            opacity: 0.4,
            color: '#d4d401',
            fillOpacity: 0.1,
            dashArray: '4 6'
        },
        onEachFeature: function(f, l) {
            if (f.properties && f.properties.NAMA_DAS) l.bindTooltip(f.properties.NAMA_DAS, {
                sticky: true,
                className: 'das-label'
            });
        }
    }).addTo(heroMap);
}

var wsData = <?= $ws_geojson ?>;
if (wsData) {
    var maskLayer = createMask(wsData);
    maskLayer.setStyle({ fillColor: '#f1f5f9', fillOpacity: 0.9 });
    maskLayer.addTo(heroMap);
    L.geoJSON(wsData, {
        style: function() {
            return {
                fillColor: "#f55959",
                weight: 1,
                opacity: 0.6,
                color: '#d40101',
                fillOpacity: 0
            };
        },
        onEachFeature: function(f, l) {
            l.bindTooltip("WS: " + f.properties.WS, { sticky: true });
        }
    }).addTo(heroMap);
}

// =============================================
// DATA DARI PHP
// =============================================
var searchIndex = [];
var bendunganDataDB = <?= json_encode($bendungan_db) ?>;
var bendungDataDB = <?= json_encode($bendung_db) ?>;
var embungDataDB = <?= json_encode($embung_db) ?>;
var pengamanDataDB = <?= json_encode($pengaman_db) ?>;
var sedimenDataDB = <?= json_encode($sedimen_db) ?>;
var irigasiDataDB = <?= json_encode($irigasi_db) ?>;
var pchDataDB = <?= isset($pch_db) ? json_encode($pch_db) : '[]' ?>;
var pdaDataDB = <?= isset($pda_db) ? json_encode($pda_db) : '[]' ?>;

// Filter data berdasarkan WS
var bendungan_MTB = filterByWS(bendunganDataDB, 'MESUJI-TULANG BAWANG');
var bendungan_SS = filterByWS(bendunganDataDB, 'SEPUTIH-SEKAMPUNG');
var bendungan_SM = filterByWS(bendunganDataDB, 'SEMANGKA');
var bendung_MTB = filterByWS(bendungDataDB, 'MESUJI-TULANG BAWANG');
var bendung_SS = filterByWS(bendungDataDB, 'SEPUTIH-SEKAMPUNG');
var bendung_SM = filterByWS(bendungDataDB, 'SEMANGKA');
var embung_MTB = filterByWS(embungDataDB, 'MESUJI-TULANG BAWANG');
var embung_SS = filterByWS(embungDataDB, 'SEPUTIH-SEKAMPUNG');
var embung_SM = filterByWS(embungDataDB, 'SEMANGKA');
var pengaman_MTB = filterByWS(pengamanDataDB, 'MESUJI-TULANG BAWANG');
var pengaman_SS = filterByWS(pengamanDataDB, 'SEPUTIH-SEKAMPUNG');
var pengaman_SM = filterByWS(pengamanDataDB, 'SEMANGKA');
var sedimen_MTB = filterByWS(sedimenDataDB, 'MESUJI-TULANG BAWANG');
var sedimen_SS = filterByWS(sedimenDataDB, 'SEPUTIH-SEKAMPUNG');
var sedimen_SM = filterByWS(sedimenDataDB, 'SEMANGKA');
var irigasi_MTB = filterByWS(irigasiDataDB, 'MESUJI-TULANG BAWANG');
var irigasi_SS = filterByWS(irigasiDataDB, 'SEPUTIH-SEKAMPUNG');
var irigasi_SM = filterByWS(irigasiDataDB, 'SEMANGKA');
var pch_MTB = filterByWS(pchDataDB, 'MESUJI-TULANG BAWANG');
var pch_SS = filterByWS(pchDataDB, 'SEPUTIH-SEKAMPUNG');
var pch_SM = filterByWS(pchDataDB, 'SEMANGKA');
var pda_MTB = filterByWS(pdaDataDB, 'MESUJI-TULANG BAWANG');
var pda_SS = filterByWS(pdaDataDB, 'SEPUTIH-SEKAMPUNG');
var pda_SM = filterByWS(pdaDataDB, 'SEMANGKA');

// =============================================
// SEARCH INDEX
// =============================================
function addToSearchIndex(data, type, color, bgColor, zoom, latField, lngField, nameField) {
    if (data) {
        data.forEach(function(p) {
            var lat = latField ? parseFloat(p[latField]) : parseFloat(p.lat);
            var lng = lngField ? parseFloat(p[lngField]) : parseFloat(p.lng);
            if (!lat || !lng) return;
            var name = nameField ? p[nameField] : (p.nama_pos || p.nama_aset);
            searchIndex.push({
                id: p.id_pos || p.id_sedimen || p.id_pengaman || p.id_irigasi || p.kode_integrasi,
                name: name,
                type: type,
                color: color,
                bgColor: bgColor,
                lat: lat,
                lng: lng,
                zoom: zoom || 15
            });
        });
    }
}

addToSearchIndex(bendunganDataDB, 'Bendungan', '#EF4444', '#fef2f2', 16);
addToSearchIndex(bendungDataDB, 'Bendung', '#06B6D4', '#ecfeff', 15);
addToSearchIndex(embungDataDB, 'Embung', '#10B981', '#ecfdf5', 15);
addToSearchIndex(sedimenDataDB, 'Pengendali Sedimen', '#EAB308', '#fefce8', 16);
addToSearchIndex(irigasiDataDB, 'Daerah Irigasi', '#22C55E', '#f0fdf4', 13, 'latitude', 'longitude', 'nama_aset');
addToSearchIndex(pchDataDB, 'PCH (Hujan)', '#3B82F6', '#eff6ff', 15);
addToSearchIndex(pdaDataDB, 'PDA (TMA)', '#8B5CF6', '#f5f3ff', 15);

searchIndex.sort(function(a, b) {
    return a.name.localeCompare(b.name);
});

// =============================================
// SEARCH FUNCTIONS
// =============================================
window.showAllPos = function(d) {
    var l = document.getElementById('search-results-list-' + d);
    var n = document.getElementById('search-no-results-' + d);
    if (!l) return;
    if (searchIndex.length > 0) {
        l.innerHTML = searchIndex.map(function(i) {
            return createResultItem(i, d);
        }).join('');
        if (n) n.classList.add('hidden');
    }
};

window.handleSearch = function(q, d) {
    var l = document.getElementById('search-results-list-' + d);
    var n = document.getElementById('search-no-results-' + d);
    if (!l) return;
    if (!q || q.trim().length === 0) {
        showAllPos(d);
        return;
    }
    var r = searchIndex.filter(function(i) {
        return i.name.toLowerCase().includes(q.toLowerCase().trim()) || i.type.toLowerCase().includes(q.toLowerCase().trim());
    });
    if (r.length > 0) {
        l.innerHTML = r.slice(0, 20).map(function(i) {
            return createResultItem(i, d);
        }).join('');
        if (n) n.classList.add('hidden');
    } else {
        l.innerHTML = '';
        if (n) n.classList.remove('hidden');
    }
};

function createResultItem(i, d) {
    return '<button type="button" onclick="flyToLocation(' + i.lat + ',' + i.lng + ',' + i.zoom + ',\'' + i.name.replace(/'/g, "\\'") + '\',\'' + d + '\')" class="w-full text-left px-3 py-2.5 rounded-xl hover:bg-white/80 transition-all flex items-center gap-3 border border-transparent hover:border-white/60"><div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:' + i.bgColor + ';"><span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:' + i.color + ';"></span></div><div class="min-w-0 flex-1"><p class="text-xs font-bold text-slate-700 truncate leading-tight">' + i.name + '</p><p class="text-[9px] text-slate-400 mt-0.5">' + i.type + '</p></div><svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>';
}

window.flyToLocation = function(lat, lng, z, n, d) {
    heroMap.flyTo([lat, lng], z, { animate: true, duration: 1.5 });
    setTimeout(function() {
        heroMap.eachLayer(function(l) {
            if (l instanceof L.Marker) {
                var ml = l.getLatLng();
                if (Math.abs(ml.lat - lat) + Math.abs(ml.lng - lng) < 0.0005) {
                    l.openPopup();
                    return;
                }
            }
        });
    }, 1600);
    if (d === 'desktop') {
        closeAllDesktopPanels();
        document.getElementById('search-input-desktop').value = '';
    } else {
        closeSlideUp();
    }
};

var so = new MutationObserver(function(m) {
    m.forEach(function(m) {
        if (m.target.id === 'desktop-panel-search' && m.target.classList.contains('show')) {
            showAllPos('desktop');
        }
    });
});
var sp = document.getElementById('desktop-panel-search');
if (sp) so.observe(sp, { attributes: true, attributeFilter: ['class'] });

// =============================================
// RENDER FUNCTIONS
// =============================================

// RENDER BENDUNGAN (DENGAN KOLOM BARU)
function renderBendungan(data, layer) {
    if (!data) return;
    data.forEach(function(p) {
        if (!p.lat || !p.lng) return;
        var hasRain = (p.rain > 0);
        var hasElevasi = (p.elevasi != null && parseFloat(p.elevasi) > 0);
        var shouldPulse = hasRain || hasElevasi;
        var m = L.marker([parseFloat(p.lat), parseFloat(p.lng)], {
            icon: createCustomIcon('#EF4444', shouldPulse)
        }).addTo(layer);
        m.on('click', function(e) {
            heroMap.flyTo(e.latlng, 16, { animate: true, duration: 1.5 });
        });
        var pw = getPopupWidth();
        var fh = pw < 300 ? '15px' : '17px';
        var fv = pw < 300 ? '20px' : '24px';
        var pop = '<div style="width:' + pw + 'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#EF4444;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#FCA5A5;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Bendungan</span></div><h4 style="font-size:' + fh + ';font-weight:800;margin:0;line-height:1.2;">' + p.nama_pos + '</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">ID: ' + p.id_pos + ' | Sungai: ' + (p.sungai || '-') + '</p></div><div style="padding:12px 14px;"><div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;"><div style="background:#fef2f2;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #fecaca;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#EF4444;margin:0 0 2px 0;">Elevasi / TMA</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + (idNum(p.elevasi, 2) || '0,00') + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">m</p></div><div style="background:#fefce8;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #fef08a;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#eab308;margin:0 0 2px 0;">Curah Hujan</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + (idNum(p.rain, 1) || '0') + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">mm</p></div></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Volume</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (idNum(p.volume, 2) || '-') + ' jt.m³</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Inflow</span><span style="font-size:9px;font-weight:700;color:#059669;">' + (idNum(p.inflow, 3) || '0') + ' m³/s</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Outflow</span><span style="font-size:9px;font-weight:700;color:#EF4444;">' + (idNum(p.total_outflow, 3) || '0') + ' m³/s</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">NWL</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (idNum(p.nwl, 2) || '-') + ' m</span></div>' + 
            // KOLOM BARU
            (p.tahun_mulai_pembangunan ? '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Tahun Mulai</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + p.tahun_mulai_pembangunan + '</span></div>' : '') +
            (p.tipe_bendungan ? '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Tipe Bendungan</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + p.tipe_bendungan + '</span></div>' : '') +
            (p.elevasi_mercu != null && p.elevasi_mercu !== '' ? '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Elevasi Mercu</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + idNum(p.elevasi_mercu, 2) + ' m</span></div>' : '') +
            (p.luas_das != null && p.luas_das !== '' ? '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Luas DAS</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + idNum(p.luas_das, 2) + ' km²</span></div>' : '') +
            '</div><div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">' + fmtUpdate(p.created_at) + '</span></div></div></div></div>';
        m.bindPopup(pop, { maxWidth: pw, minWidth: pw, className: 'custom-leaflet-popup', offset: [0, -6] });
        m.bindTooltip(p.nama_pos, { direction: 'top', offset: [0, -10] });
    });
}

// RENDER BENDUNG (SESUAI STRUKTUR TERBARU)
function renderBendung(data, layer) {
    if (!data) return;
    data.forEach(function(p) {
        if (!p.lat || !p.lng) return;
        var hasRain = (p.rain > 0);
        var hasElevasi = (p.elevasi_mercu != null && parseFloat(p.elevasi_mercu) > 0);
        var shouldPulse = hasRain || hasElevasi;
        var m = L.marker([parseFloat(p.lat), parseFloat(p.lng)], {
            icon: createCustomIcon('#06B6D4', shouldPulse)
        }).addTo(layer);
        m.on('click', function(e) {
            heroMap.flyTo(e.latlng, 15, { animate: true, duration: 1.5 });
        });
        var pw = getPopupWidth();
        var fh = pw < 300 ? '15px' : '17px';
        var fv = pw < 300 ? '20px' : '24px';
        var pop = '<div style="width:' + pw + 'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#06B6D4;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#67E8F9;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Bendung</span></div><h4 style="font-size:' + fh + ';font-weight:800;margin:0;line-height:1.2;">' + p.nama_pos + '</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">Sungai: ' + (p.sungai || '-') + ' | ID: ' + p.id_pos + '</p></div><div style="padding:12px 14px;"><div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;"><div style="background:#ecfeff;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #a5f3fc;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#06B6D4;margin:0 0 2px 0;">Elevasi Mercu</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + (idNum(p.elevasi_mercu, 2) || '0,00') + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">m</p></div><div style="background:#fefce8;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #fef08a;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#eab308;margin:0 0 2px 0;">Curah Hujan</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + (idNum(p.rain, 1) || '0,0') + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">mm</p></div></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-Total</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (idNum(p.q_total, 3) || '0,000') + ' m³/dt</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-FC1</span><span style="font-size:9px;font-weight:700;color:#059669;">' + (idNum(p.q_fc1, 3) || '0,000') + ' m³/dt</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-FC2</span><span style="font-size:9px;font-weight:700;color:#3B82F6;">' + (idNum(p.q_fc2, 3) || '0,000') + ' m³/dt</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-Sal. Induk</span><span style="font-size:9px;font-weight:700;color:#D97706;">' + (idNum(p.q_sal_induk, 3) || '0,000') + ' m³/dt</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-Limpas</span><span style="font-size:9px;font-weight:700;color:#EF4444;">' + (idNum(p.q_limpas, 3) || '0,000') + ' m³/dt</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-Sungai</span><span style="font-size:9px;font-weight:700;color:#0891B2;">' + (idNum(p.q_sungai, 3) || '0,000') + ' m³/dt</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-SPAM KPBU</span><span style="font-size:9px;font-weight:700;color:#8B5CF6;">' + (idNum(p.q_spam_kpbu, 3) || '0,000') + ' m³/dt</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Sluice Gate</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (idNum(p.sluice_gate, 3) || '0,000') + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Bukaan Pintu</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (idNum(p.bukaan_pintu, 3) || '0,000') + ' m</span></div></div><div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">' + fmtUpdate(p.created_at) + '</span></div></div></div></div>';
        m.bindPopup(pop, { maxWidth: pw, minWidth: pw, className: 'custom-leaflet-popup', offset: [0, -6] });
        m.bindTooltip(p.nama_pos, { direction: 'top', offset: [0, -10] });
    });
}

// RENDER EMBUNG
function renderEmbung(data, layer) {
    if (!data) return;
    data.forEach(function(p) {
        if (!p.lat || !p.lng) return;
        var m = L.marker([parseFloat(p.lat), parseFloat(p.lng)], {
            icon: createCustomIcon('#10B981', false)
        }).addTo(layer);
        m.on('click', function(e) {
            heroMap.flyTo(e.latlng, 15, { animate: true, duration: 1.5 });
        });
        var pw = getPopupWidth();
        var fh = pw < 300 ? '15px' : '17px';
        var fv = pw < 300 ? '20px' : '24px';
        var pop = '<div style="width:' + pw + 'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#10B981;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#6EE7B7;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Embung</span></div><h4 style="font-size:' + fh + ';font-weight:800;margin:0;line-height:1.2;">' + p.nama_pos + '</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">ID: ' + (p.id_embung || p.id_pos || '-') + ' | Sungai: ' + (p.sungai || '-') + '</p></div><div style="padding:12px 14px;"><div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;"><div style="background:#ecfdf5;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #a7f3d0;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#10B981;margin:0 0 2px 0;">Kapasitas Volume</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + (idNum(p.kapasitas_volume, 0) || '0') + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">m³</p></div><div style="background:#ecfdf5;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #a7f3d0;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#10B981;margin:0 0 2px 0;">Elevasi Puncak</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + (idNum(p.elevasi_puncak, 2) || '0,00') + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">m</p></div></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Tinggi Embung</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (idNum(p.tinggi_embung, 2) || '-') + ' m</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Panjang Tubuh</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (idNum(p.panjang_tubuh, 2) || '-') + ' m</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Tahun Pembangunan</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (p.tahun_mulai_pembangunan || '-') + '</span></div></div><div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">' + fmtUpdate(p.created_at) + '</span></div></div></div></div>';
        m.bindPopup(pop, { maxWidth: pw, minWidth: pw, className: 'custom-leaflet-popup', offset: [0, -6] });
        m.bindTooltip(p.nama_pos, { direction: 'top', offset: [0, -10] });
    });
}

// RENDER PENGAMAN PANTAI (TANPA KONDISI & STATUS OPERASI)
function renderPengaman(data, layer) {
    if (!data) return;
    data.forEach(function(p) {
        if (!p.lat_awal || !p.lng_awal || !p.lat_akhir || !p.lng_akhir) return;
        if (parseFloat(p.lat_awal) == parseFloat(p.lat_akhir) && parseFloat(p.lng_awal) == parseFloat(p.lng_akhir)) return;
        var latAwal = parseFloat(p.lat_awal),
            lngAwal = parseFloat(p.lng_awal),
            latAkhir = parseFloat(p.lat_akhir),
            lngAkhir = parseFloat(p.lng_akhir);
        var polyline = L.polyline([
            [latAwal, lngAwal],
            [latAkhir, lngAkhir]
        ], {
            color: '#F97316',
            weight: 4,
            opacity: 0.9
        }).addTo(layer);
        polyline.bindTooltip(p.nama_aset, { direction: 'top', offset: [0, -5], sticky: true });
        var pw = getPopupWidth();
        var fh = pw < 300 ? '15px' : '17px';
        var fv = pw < 300 ? '20px' : '24px';
        var jenis = p.jenis_bangunan || '-';
        var panjang = p.panjang ? parseFloat(p.panjang).toFixed(0) : '-';
        var elevasi = p.elevasi_puncak ? parseFloat(p.elevasi_puncak).toFixed(2) : '-';
        var lebar = p.lebar_puncak ? parseFloat(p.lebar_puncak).toFixed(2) : '-';
        var tahun = p.tahun_dibangun || '-';
        var kab = p.kabupaten_kota || '-';
        var kec = p.kecamatan || '-';
        var desa = p.kelurahan || '-';
        var manfaat = p.manfaat || '-';
        var das = p.sungai || '-';
        var ws = p.wilayah_sungai || '-';
        var pop = '<div style="width:' + pw + 'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#F97316;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#FDBA74;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Pengaman Pantai</span></div><h4 style="font-size:' + fh + ';font-weight:800;margin:0;line-height:1.2;">' + p.nama_aset + '</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">' + jenis + ' | ' + kab + '</p></div><div style="padding:12px 14px;"><div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:10px;"><div style="background:#fff7ed;border-radius:10px;padding:10px 6px;text-align:center;border:1px solid #fdba74;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#F97316;margin:0 0 2px 0;">Panjang</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + panjang + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">m</p></div><div style="background:#fff7ed;border-radius:10px;padding:10px 6px;text-align:center;border:1px solid #fdba74;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#F97316;margin:0 0 2px 0;">Elevasi Puncak</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + elevasi + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">mdpl</p></div><div style="background:#fff7ed;border-radius:10px;padding:10px 6px;text-align:center;border:1px solid #fdba74;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#F97316;margin:0 0 2px 0;">Lebar Puncak</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + lebar + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">m</p></div></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Jenis</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + jenis + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Wilayah Sungai</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + ws + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">DAS</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + das + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Tahun</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + tahun + '</span></div></div><div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Kecamatan</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + kec + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Desa</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + desa + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Manfaat</span><span style="font-size:9px;font-weight:700;color:#F97316;max-width:180px;text-align:right;">' + manfaat + '</span></div></div><div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">' + fmtUpdate(p.created_at) + '</span></div></div></div></div>';
        polyline.bindPopup(pop, { maxWidth: pw, minWidth: pw, className: 'custom-leaflet-popup', offset: [0, -6] });
        var midLat = (latAwal + latAkhir) / 2,
            midLng = (lngAwal + lngAkhir) / 2;
        var marker = L.marker([midLat, midLng], { icon: createCustomIcon('#F97316', false) }).addTo(layer);
        marker.on('click', function(e) { polyline.openPopup(); });
        polyline.on('click', function(e) {
            heroMap.fitBounds(polyline.getBounds(), { padding: [30, 30], maxZoom: 15 });
        });
    });
}

// RENDER PENGENDALI SEDIMEN (TANPA KONDISI & STATUS OPERASI)
function renderSedimen(data, layer) {
    if (!data) return;
    data.forEach(function(p) {
        if (!p.lat || !p.lng) return;
        var m = L.marker([parseFloat(p.lat), parseFloat(p.lng)], {
            icon: createCustomIcon('#EAB308', false)
        }).addTo(layer);
        m.on('click', function(e) {
            heroMap.flyTo(e.latlng, 16, { animate: true, duration: 1.5 });
        });
        var pw = getPopupWidth();
        var fh = pw < 300 ? '15px' : '17px';
        var fv = pw < 300 ? '20px' : '24px';
        var tahun = p.tahun_dibangun || '-';
        var material = p.jenis_material || '-';
        var das = p.daerah_aliran_sungai || '-';
        var sungai = p.sungai || '-';
        var ws = p.wilayah_sungai || '-';
        var kab = p.kabupaten_kota || '-';
        var kec = p.kecamatan || '-';
        var desa = p.kelurahan || '-';
        var jenis = p.jenis_bangunan || 'Cekdam';
        var tampung = p.daya_tampung ? parseFloat(p.daya_tampung).toFixed(0) : '0';
        var panjang = p.panjang ? parseFloat(p.panjang).toFixed(1) : '-';
        var lebar = p.lebar ? parseFloat(p.lebar).toFixed(1) : '-';
        var tinggi = p.tinggi ? parseFloat(p.tinggi).toFixed(1) : '-';
        var pop = '<div style="width:' + pw + 'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#EAB308;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#FDE68A;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Pengendali Sedimen</span></div><h4 style="font-size:' + fh + ';font-weight:800;margin:0;line-height:1.2;">' + p.nama_aset + '</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">' + jenis + ' | ' + sungai + '</p></div><div style="padding:12px 14px;"><div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:10px;"><div style="background:#fefce8;border-radius:10px;padding:8px 6px;text-align:center;border:1px solid #fde68a;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#EAB308;margin:0 0 2px 0;">Daya Tampung</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + tampung + '</p><p style="font-size:7px;color:#94a3b8;margin:1px 0 0 0;">m³</p></div><div style="background:#fefce8;border-radius:10px;padding:8px 6px;text-align:center;border:1px solid #fde68a;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#EAB308;margin:0 0 2px 0;">Panjang</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + panjang + '</p><p style="font-size:7px;color:#94a3b8;margin:1px 0 0 0;">m</p></div><div style="background:#fefce8;border-radius:10px;padding:8px 6px;text-align:center;border:1px solid #fde68a;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#EAB308;margin:0 0 2px 0;">Tinggi</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + tinggi + '</p><p style="font-size:7px;color:#94a3b8;margin:1px 0 0 0;">m</p></div></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Lebar</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + lebar + ' m</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Material</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + material + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Tahun</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + tahun + '</span></div></div><div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Wilayah Sungai</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + ws + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">DAS</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + das + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Kab/Kota</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + kab + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Kecamatan</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + kec + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Desa</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + desa + '</span></div></div><div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">' + fmtUpdate(p.created_at) + '</span></div></div></div></div>';
        m.bindPopup(pop, { maxWidth: pw, minWidth: pw, className: 'custom-leaflet-popup', offset: [0, -6] });
        m.bindTooltip(p.nama_aset, { direction: 'top', offset: [0, -10] });
    });
}

// RENDER DAERAH IRIGASI (3 KOLOM LUAS)
function renderIrigasi(data, layer) {
    if (!data) return;
    data.forEach(function(p) {
        var lat = parseFloat(p.latitude);
        var lng = parseFloat(p.longitude);
        if (!lat || !lng) return;
        var m = L.marker([lat, lng], { icon: createCustomIcon('#22C55E', false) }).addTo(layer);
        m.on('click', function(e) {
            heroMap.flyTo(e.latlng, 14, { animate: true, duration: 1.5 });
        });
        var pw = getPopupWidth();
        var fh = pw < 300 ? '15px' : '17px';
        var fv = pw < 300 ? '20px' : '24px';
        var jenisDI = p.jenis_daerah_irigasi || 'Irigasi Permukaan';
        var namaDI = p.nama_aset || '-';
        var kab = p.kabupaten_kota || '-';
        var luasBaku = idNum(p.luas_baku, 0) || '0';
        var luasFungsional = idNum(p.luas_fungsional, 0) || '0';
        var luasPotensial = idNum(p.luas_potensial, 0) || '0';
        var sumberAir = p.sumber_air || '-';
        var bangunanUtama = p.jenis_bangunan_utama || '-';
        var das = p.daerah_aliran_sungai || '-';
        var ws = p.wilayah_sungai || '-';
        var deskripsi = p.deskripsi_aset || '';
        var pop = '<div style="width:' + pw + 'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#22C55E;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#86EFAC;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Daerah Irigasi</span></div><h4 style="font-size:' + fh + ';font-weight:800;margin:0;line-height:1.2;">' + namaDI + '</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">' + jenisDI + ' | ' + kab + '</p></div><div style="padding:12px 14px;"><div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:10px;"><div style="background:#f0fdf4;border-radius:10px;padding:10px 6px;text-align:center;border:1px solid #bbf7d0;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#22C55E;margin:0 0 2px 0;">Luas Baku</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + luasBaku + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">Ha</p></div><div style="background:#f0fdf4;border-radius:10px;padding:10px 6px;text-align:center;border:1px solid #bbf7d0;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#22C55E;margin:0 0 2px 0;">Luas Fungsional</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + luasFungsional + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">Ha</p></div><div style="background:#f0fdf4;border-radius:10px;padding:10px 6px;text-align:center;border:1px solid #bbf7d0;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#22C55E;margin:0 0 2px 0;">Luas Potensial</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + luasPotensial + '</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">Ha</p></div></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Sumber Air</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + sumberAir + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Bangunan Utama</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + bangunanUtama + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">DAS</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + das + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">WS</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + ws + '</span></div></div>' + (deskripsi ? '<div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><p style="font-size:8px;color:#64748b;margin:0;line-height:1.4;">' + deskripsi.substring(0, 150) + (deskripsi.length > 150 ? '...' : '') + '</p></div>' : '') + '<div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">' + fmtUpdate(p.created_at) + '</span></div></div></div></div>';
        m.bindPopup(pop, { maxWidth: pw, minWidth: pw, className: 'custom-leaflet-popup', offset: [0, -6] });
        m.bindTooltip(namaDI, { direction: 'top', offset: [0, -10] });
    });
}

// RENDER PCH
function renderPCH(data, layer) {
    if (!data) return;
    data.forEach(function(p) {
        if (!p.lat || !p.lng) return;
        var hasRain = (p.ch_hari_ini > 0);
        var m = L.marker([parseFloat(p.lat), parseFloat(p.lng)], {
            icon: createCustomIcon('#3B82F6', hasRain)
        }).addTo(layer);
        m.on('click', function(e) {
            heroMap.flyTo(e.latlng, 15, { animate: true, duration: 1.5 });
        });
        var pw = getPopupWidth();
        var fh = pw < 300 ? '15px' : '17px';
        var fv = pw < 300 ? '24px' : '28px';
        var pop = '<div style="width:' + pw + 'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#3B82F6;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#93C5FD;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Pos Curah Hujan</span></div><h4 style="font-size:' + fh + ';font-weight:800;margin:0;line-height:1.2;">' + p.nama_pos + '</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">ID: ' + p.id_pos + ' | PCH</p></div><div style="padding:12px 14px;"><div style="background:#eff6ff;border-radius:12px;padding:14px 10px;text-align:center;margin-bottom:10px;border:1px solid #bfdbfe;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#3B82F6;margin:0 0 4px 0;">Curah Hujan</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + (idNum(p.ch_hari_ini, 2) || '0,00') + '</p><p style="font-size:9px;color:#94a3b8;margin:2px 0 0 0;">mm</p></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Intensitas</span><span style="font-size:9px;font-weight:700;color:#1e293b;">' + (p.intensitas || 'Normal') + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">' + fmtUpdate(p.tgl_terakhir) + '</span></div></div></div></div>';
        m.bindPopup(pop, { maxWidth: pw, minWidth: pw, className: 'custom-leaflet-popup', offset: [0, -6] });
        m.bindTooltip("PCH: " + p.nama_pos, { direction: 'top', offset: [0, -10] });
    });
}

// RENDER PDA
function renderPDA(data, layer) {
    if (!data) return;
    data.forEach(function(p) {
        if (!p.lat || !p.lng) return;
        var isSiaga = (p.status_siaga && p.status_siaga.toLowerCase() !== 'normal');
        var m = L.marker([parseFloat(p.lat), parseFloat(p.lng)], {
            icon: createCustomIcon('#8B5CF6', isSiaga)
        }).addTo(layer);
        m.on('click', function(e) {
            heroMap.flyTo(e.latlng, 15, { animate: true, duration: 1.5 });
        });
        var pw = getPopupWidth();
        var fh = pw < 300 ? '15px' : '17px';
        var fv = pw < 300 ? '24px' : '28px';
        var pop = '<div style="width:' + pw + 'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#8B5CF6;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#C4B5FD;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Pos TMA</span></div><h4 style="font-size:' + fh + ';font-weight:800;margin:0;line-height:1.2;">' + p.nama_pos + '</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">ID: ' + p.id_pos + ' | PDA</p></div><div style="padding:12px 14px;"><div style="background:#f5f3ff;border-radius:12px;padding:14px 10px;text-align:center;margin-bottom:10px;border:1px solid #ddd6fe;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#8B5CF6;margin:0 0 4px 0;">TMA Saat Ini</p><p style="font-size:' + fv + ';font-weight:800;color:#1e293b;margin:0;line-height:1;">' + (idNum(p.tma_sekarang, 2) || '0,00') + '</p><p style="font-size:9px;color:#94a3b8;margin:2px 0 0 0;">m</p></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Status</span><span style="font-size:9px;font-weight:700;color:' + (isSiaga ? '#EF4444' : '#16a34a') + ';">' + (p.status_siaga || 'Normal') + '</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">' + fmtUpdate(p.tgl_terakhir) + '</span></div></div></div></div>';
        m.bindPopup(pop, { maxWidth: pw, minWidth: pw, className: 'custom-leaflet-popup', offset: [0, -6] });
        m.bindTooltip("PDA: " + p.nama_pos, { direction: 'top', offset: [0, -10] });
    });
}

// =============================================
// RENDER ALL DATA
// =============================================
renderBendungan(bendungan_MTB, layerBendungan_MTB);
renderBendung(bendung_MTB, layerBendung_MTB);
renderEmbung(embung_MTB, layerEmbung_MTB);
renderPengaman(pengaman_MTB, layerPengaman_MTB);
renderSedimen(sedimen_MTB, layerSedimen_MTB);
renderIrigasi(irigasi_MTB, layerIrigasi_MTB);
renderPCH(pch_MTB, layerPCH_MTB);
renderPDA(pda_MTB, layerPDA_MTB);

renderBendungan(bendungan_SS, layerBendungan_SS);
renderBendung(bendung_SS, layerBendung_SS);
renderEmbung(embung_SS, layerEmbung_SS);
renderPengaman(pengaman_SS, layerPengaman_SS);
renderSedimen(sedimen_SS, layerSedimen_SS);
renderIrigasi(irigasi_SS, layerIrigasi_SS);
renderPCH(pch_SS, layerPCH_SS);
renderPDA(pda_SS, layerPDA_SS);

renderBendungan(bendungan_SM, layerBendungan_SM);
renderBendung(bendung_SM, layerBendung_SM);
renderEmbung(embung_SM, layerEmbung_SM);
renderPengaman(pengaman_SM, layerPengaman_SM);
renderSedimen(sedimen_SM, layerSedimen_SM);
renderIrigasi(irigasi_SM, layerIrigasi_SM);
renderPCH(pch_SM, layerPCH_SM);
renderPDA(pda_SM, layerPDA_SM);

// =============================================
// MOBILE SLIDE UP
// =============================================
window.openSlideUp = function(t) {
    var p = document.getElementById('slide-up-panel'),
        b = document.getElementById('slide-up-backdrop'),
        ti = document.getElementById('slide-up-title'),
        c = document.getElementById('slide-up-content');
    if (t === 'info') {
        ti.textContent = 'Informasi';
        c.innerHTML = '<div class="space-y-4"><div class="grid grid-cols-3 gap-3"><div class="bg-red-50 rounded-2xl p-4 text-center border border-red-100"><p class="text-[11px] font-bold text-red-600 uppercase mb-1">Bendungan</p><p class="text-3xl font-black text-darkblue"><?= count($bendungan_db) ?></p></div><div class="bg-cyan-50 rounded-2xl p-4 text-center border border-cyan-100"><p class="text-[11px] font-bold text-cyan-600 uppercase mb-1">Bendung</p><p class="text-3xl font-black text-darkblue"><?= $bendung_count ?></p></div><div class="bg-emerald-50 rounded-2xl p-4 text-center border border-emerald-100"><p class="text-[11px] font-bold text-emerald-600 uppercase mb-1">Embung</p><p class="text-3xl font-black text-darkblue"><?= $embung_count ?></p></div><div class="bg-orange-50 rounded-2xl p-4 text-center border border-orange-100"><p class="text-[11px] font-bold text-orange-600 uppercase mb-1">Pengaman Pantai</p><p class="text-3xl font-black text-darkblue"><?= $pengaman_count ?></p></div><div class="bg-yellow-50 rounded-2xl p-4 text-center border border-yellow-100"><p class="text-[11px] font-bold text-yellow-600 uppercase mb-1">Pengendali Sedimen</p><p class="text-3xl font-black text-darkblue"><?= $sedimen_count ?></p></div><div class="bg-blue-50 rounded-2xl p-4 text-center border border-blue-100"><p class="text-[11px] font-bold text-blue-600 uppercase mb-1">PCH</p><p class="text-3xl font-black text-darkblue"><?= count($pch_db) ?></p></div><div class="bg-purple-50 rounded-2xl p-4 text-center border border-purple-100"><p class="text-[11px] font-bold text-purple-600 uppercase mb-1">PDA</p><p class="text-3xl font-black text-darkblue"><?= count($pda_db) ?></p></div><div class="bg-green-50 rounded-2xl p-4 text-center border border-green-100"><p class="text-[11px] font-bold text-green-600 uppercase mb-1">Irigasi</p><p class="text-3xl font-black text-darkblue"><?= $irigasi_count ?></p></div></div><div class="border-t border-slate-200 pt-4"><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Keterangan Warna</p><div class="space-y-2"><div class="flex items-center gap-3 px-3 py-2 bg-red-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-red-500"></span><span class="text-sm text-slate-700">Bendungan</span></div><div class="flex items-center gap-3 px-3 py-2 bg-cyan-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-cyan-500"></span><span class="text-sm text-slate-700">Bendung</span></div><div class="flex items-center gap-3 px-3 py-2 bg-emerald-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-emerald-500"></span><span class="text-sm text-slate-700">Embung</span></div><div class="flex items-center gap-3 px-3 py-2 bg-orange-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-orange-500"></span><span class="text-sm text-slate-700">Pengaman Pantai</span></div><div class="flex items-center gap-3 px-3 py-2 bg-yellow-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-yellow-500"></span><span class="text-sm text-slate-700">Pengendali Sedimen</span></div><div class="flex items-center gap-3 px-3 py-2 bg-green-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-green-500"></span><span class="text-sm text-slate-700">Daerah Irigasi</span></div><div class="flex items-center gap-3 px-3 py-2 bg-blue-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-blue-500"></span><span class="text-sm text-slate-700">PCH (Hujan)</span></div><div class="flex items-center gap-3 px-3 py-2 bg-purple-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-purple-500"></span><span class="text-sm text-slate-700">PDA (TMA)</span></div></div></div></div>';
    } else if (t === 'layer') {
        ti.textContent = 'Layer Peta';
        c.innerHTML = '<div class="space-y-4"><div class="bg-indigo-50 rounded-2xl p-3 border border-indigo-200"><p class="text-xs font-bold text-indigo-600 uppercase mb-2">Mesuji-Tulang Bawang</p><label class="flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer bg-white/70 hover:bg-white transition-all"><input type="checkbox" onchange="toggleWSMobile(\'MTB\',this)" class="w-5 h-5 rounded accent-indigo-500"><span class="text-sm font-medium text-slate-700">Semua</span></label><div class="grid grid-cols-2 gap-1 ml-4 mt-1"><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" checked onchange="toggleLayer(\'bendungan_MTB\',this)" class="w-4 h-4 rounded accent-red-500"><span class="text-xs text-slate-600">Bendungan</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" checked onchange="toggleLayer(\'bendung_MTB\',this)" class="w-4 h-4 rounded accent-cyan-500"><span class="text-xs text-slate-600">Bendung</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'embung_MTB\',this)" class="w-4 h-4 rounded accent-emerald-500"><span class="text-xs text-slate-600">Embung</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pengaman_MTB\',this)" class="w-4 h-4 rounded accent-orange-500"><span class="text-xs text-slate-600">Pengaman</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'sedimen_MTB\',this)" class="w-4 h-4 rounded accent-yellow-500"><span class="text-xs text-slate-600">Sedimen</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'irigasi_MTB\',this)" class="w-4 h-4 rounded accent-green-500"><span class="text-xs text-slate-600">Irigasi</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pch_MTB\',this)" class="w-4 h-4 rounded accent-blue-500"><span class="text-xs text-slate-600">PCH</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pda_MTB\',this)" class="w-4 h-4 rounded accent-purple-500"><span class="text-xs text-slate-600">PDA</span></label></div></div><div class="bg-blue-50 rounded-2xl p-3 border border-blue-200"><p class="text-xs font-bold text-blue-600 uppercase mb-2">Seputih-Sekampung</p><label class="flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer bg-white/70 hover:bg-white transition-all"><input type="checkbox" onchange="toggleWSMobile(\'SS\',this)" class="w-5 h-5 rounded accent-blue-500"><span class="text-sm font-medium text-slate-700">Semua</span></label><div class="grid grid-cols-2 gap-1 ml-4 mt-1"><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" checked onchange="toggleLayer(\'bendungan_SS\',this)" class="w-4 h-4 rounded accent-red-500"><span class="text-xs text-slate-600">Bendungan</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" checked onchange="toggleLayer(\'bendung_SS\',this)" class="w-4 h-4 rounded accent-cyan-500"><span class="text-xs text-slate-600">Bendung</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'embung_SS\',this)" class="w-4 h-4 rounded accent-emerald-500"><span class="text-xs text-slate-600">Embung</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pengaman_SS\',this)" class="w-4 h-4 rounded accent-orange-500"><span class="text-xs text-slate-600">Pengaman</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'sedimen_SS\',this)" class="w-4 h-4 rounded accent-yellow-500"><span class="text-xs text-slate-600">Sedimen</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'irigasi_SS\',this)" class="w-4 h-4 rounded accent-green-500"><span class="text-xs text-slate-600">Irigasi</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pch_SS\',this)" class="w-4 h-4 rounded accent-blue-500"><span class="text-xs text-slate-600">PCH</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pda_SS\',this)" class="w-4 h-4 rounded accent-purple-500"><span class="text-xs text-slate-600">PDA</span></label></div></div><div class="bg-green-50 rounded-2xl p-3 border border-green-200"><p class="text-xs font-bold text-green-600 uppercase mb-2">Semangka</p><label class="flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer bg-white/70 hover:bg-white transition-all"><input type="checkbox" onchange="toggleWSMobile(\'SM\',this)" class="w-5 h-5 rounded accent-green-500"><span class="text-sm font-medium text-slate-700">Semua</span></label><div class="grid grid-cols-2 gap-1 ml-4 mt-1"><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" checked onchange="toggleLayer(\'bendungan_SM\',this)" class="w-4 h-4 rounded accent-red-500"><span class="text-xs text-slate-600">Bendungan</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" checked onchange="toggleLayer(\'bendung_SM\',this)" class="w-4 h-4 rounded accent-cyan-500"><span class="text-xs text-slate-600">Bendung</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'embung_SM\',this)" class="w-4 h-4 rounded accent-emerald-500"><span class="text-xs text-slate-600">Embung</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pengaman_SM\',this)" class="w-4 h-4 rounded accent-orange-500"><span class="text-xs text-slate-600">Pengaman</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'sedimen_SM\',this)" class="w-4 h-4 rounded accent-yellow-500"><span class="text-xs text-slate-600">Sedimen</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'irigasi_SM\',this)" class="w-4 h-4 rounded accent-green-500"><span class="text-xs text-slate-600">Irigasi</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pch_SM\',this)" class="w-4 h-4 rounded accent-blue-500"><span class="text-xs text-slate-600">PCH</span></label><label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-white/50"><input type="checkbox" onchange="toggleLayer(\'pda_SM\',this)" class="w-4 h-4 rounded accent-purple-500"><span class="text-xs text-slate-600">PDA</span></label></div></div><div class="border-t border-slate-200 my-3 pt-3"><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Tampilan Peta</p><div class="flex gap-3"><button onclick="switchBaseMap(\'osm\');updateBaseMapButtons(\'osm\')" id="btn-osm-mobile" class="flex-1 py-3 text-sm font-semibold rounded-xl bg-slate-200 text-slate-700 transition-all">Peta</button><button onclick="switchBaseMap(\'satellite\');updateBaseMapButtons(\'satellite\')" id="btn-satellite-mobile" class="flex-1 py-3 text-sm font-semibold rounded-xl text-slate-500 bg-slate-50 transition-all">Satelit</button></div></div></div>';
    } else if (t === 'search') {
        ti.textContent = 'Cari & Filter';
        c.innerHTML = '<div class="space-y-4"><div class="bg-slate-50 rounded-2xl p-4 border border-slate-200"><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Filter Tanggal</label><div class="flex gap-2"><input type="date" id="history-date-mobile" value="<?= $selected_date ?>" class="flex-1 px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandyellow font-medium"><button onclick="applyHistoryDateMobile()" class="px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all">Cari</button></div><p class="text-xs text-slate-400 text-center mt-2"><?= ($selected_date == date('Y-m-d')) ? 'Data hari ini' : 'Data ' . date('d M Y', strtotime($selected_date)) ?></p><?php if ($selected_date != date('Y-m-d')): ?><a href="<?= base_url('Beranda') ?>" class="block text-center text-sm text-slate-500 hover:text-darkblue mt-2 font-medium">→ Kembali ke Hari Ini</a><?php endif; ?></div><div class="relative"><input type="text" id="search-input-mobile" placeholder="Ketik nama pos..." class="w-full pl-12 pr-4 py-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brandyellow focus:bg-white transition-all font-medium" onkeyup="handleSearch(this.value,\'mobile\')" autocomplete="off"><svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div><div id="search-results-mobile" class="max-h-96 overflow-y-auto"><div id="search-results-list-mobile" class="space-y-1"></div><div id="search-no-results-mobile" class="px-4 py-8 text-center text-sm text-slate-400 hidden"><p class="font-medium">Tidak ditemukan</p></div></div></div>';
        setTimeout(function() { showAllPos('mobile'); }, 100);
    }
    p.classList.add('open');
    b.classList.add('show');
    document.body.style.overflow = 'hidden';
};

window.closeSlideUp = function() {
    document.getElementById('slide-up-panel').classList.remove('open');
    document.getElementById('slide-up-backdrop').classList.remove('show');
    document.body.style.overflow = '';
};

var sp2 = document.getElementById('slide-up-panel'),
    ts = 0;
if (sp2) {
    sp2.addEventListener('touchstart', function(e) {
        ts = e.touches[0].clientY;
    }, { passive: true });
    sp2.addEventListener('touchmove', function(e) {
        if (e.touches[0].clientY - ts > 80 && sp2.scrollTop <= 0) closeSlideUp();
    }, { passive: true });
}

window.applyHistoryDateMobile = function() {
    var d = document.getElementById('history-date-mobile').value;
    if (d) window.location.href = '<?= base_url('Beranda') ?>?date=' + d;
};

window.updateBaseMapButtons = function(t) {
    var o = document.getElementById('btn-osm-mobile'),
        s = document.getElementById('btn-satellite-mobile');
    if (t === 'osm') {
        o.className = 'flex-1 py-3 text-sm font-semibold rounded-xl bg-slate-200 text-slate-700 transition-all';
        s.className = 'flex-1 py-3 text-sm font-semibold rounded-xl text-slate-500 bg-slate-50 transition-all';
    } else {
        s.className = 'flex-1 py-3 text-sm font-semibold rounded-xl bg-slate-200 text-slate-700 transition-all';
        o.className = 'flex-1 py-3 text-sm font-semibold rounded-xl text-slate-500 bg-slate-50 transition-all';
    }
};

// =============================================
// DESKTOP PANELS
// =============================================
var adp = null;

window.toggleDesktopPanel = function(t) {
    var p = document.getElementById('desktop-panel-' + t);
    if (!p) return;
    if (adp && adp !== p) adp.classList.remove('show');
    if (p.classList.contains('show')) {
        p.classList.remove('show');
        adp = null;
    } else {
        p.classList.add('show');
        adp = p;
    }
};

window.closeAllDesktopPanels = function() {
    document.querySelectorAll('.desktop-slide-panel').forEach(function(p) {
        p.classList.remove('show');
    });
    adp = null;
};

window.applyHistoryDate = function() {
    var d = document.getElementById('history-date').value;
    if (d) window.location.href = '<?= base_url('Beranda') ?>?date=' + d;
};

// =============================================
// TOGGLE LAYER & WS
// =============================================
window.toggleLayer = function(t, cb) {
    if (t === 'bendungan_MTB') cb.checked ? layerBendungan_MTB.addTo(heroMap) : heroMap.removeLayer(layerBendungan_MTB);
    if (t === 'bendung_MTB') cb.checked ? layerBendung_MTB.addTo(heroMap) : heroMap.removeLayer(layerBendung_MTB);
    if (t === 'embung_MTB') cb.checked ? layerEmbung_MTB.addTo(heroMap) : heroMap.removeLayer(layerEmbung_MTB);
    if (t === 'pengaman_MTB') cb.checked ? layerPengaman_MTB.addTo(heroMap) : heroMap.removeLayer(layerPengaman_MTB);
    if (t === 'sedimen_MTB') cb.checked ? layerSedimen_MTB.addTo(heroMap) : heroMap.removeLayer(layerSedimen_MTB);
    if (t === 'irigasi_MTB') cb.checked ? layerIrigasi_MTB.addTo(heroMap) : heroMap.removeLayer(layerIrigasi_MTB);
    if (t === 'pch_MTB') cb.checked ? layerPCH_MTB.addTo(heroMap) : heroMap.removeLayer(layerPCH_MTB);
    if (t === 'pda_MTB') cb.checked ? layerPDA_MTB.addTo(heroMap) : heroMap.removeLayer(layerPDA_MTB);
    if (t === 'bendungan_SS') cb.checked ? layerBendungan_SS.addTo(heroMap) : heroMap.removeLayer(layerBendungan_SS);
    if (t === 'bendung_SS') cb.checked ? layerBendung_SS.addTo(heroMap) : heroMap.removeLayer(layerBendung_SS);
    if (t === 'embung_SS') cb.checked ? layerEmbung_SS.addTo(heroMap) : heroMap.removeLayer(layerEmbung_SS);
    if (t === 'pengaman_SS') cb.checked ? layerPengaman_SS.addTo(heroMap) : heroMap.removeLayer(layerPengaman_SS);
    if (t === 'sedimen_SS') cb.checked ? layerSedimen_SS.addTo(heroMap) : heroMap.removeLayer(layerSedimen_SS);
    if (t === 'irigasi_SS') cb.checked ? layerIrigasi_SS.addTo(heroMap) : heroMap.removeLayer(layerIrigasi_SS);
    if (t === 'pch_SS') cb.checked ? layerPCH_SS.addTo(heroMap) : heroMap.removeLayer(layerPCH_SS);
    if (t === 'pda_SS') cb.checked ? layerPDA_SS.addTo(heroMap) : heroMap.removeLayer(layerPDA_SS);
    if (t === 'bendungan_SM') cb.checked ? layerBendungan_SM.addTo(heroMap) : heroMap.removeLayer(layerBendungan_SM);
    if (t === 'bendung_SM') cb.checked ? layerBendung_SM.addTo(heroMap) : heroMap.removeLayer(layerBendung_SM);
    if (t === 'embung_SM') cb.checked ? layerEmbung_SM.addTo(heroMap) : heroMap.removeLayer(layerEmbung_SM);
    if (t === 'pengaman_SM') cb.checked ? layerPengaman_SM.addTo(heroMap) : heroMap.removeLayer(layerPengaman_SM);
    if (t === 'sedimen_SM') cb.checked ? layerSedimen_SM.addTo(heroMap) : heroMap.removeLayer(layerSedimen_SM);
    if (t === 'irigasi_SM') cb.checked ? layerIrigasi_SM.addTo(heroMap) : heroMap.removeLayer(layerIrigasi_SM);
    if (t === 'pch_SM') cb.checked ? layerPCH_SM.addTo(heroMap) : heroMap.removeLayer(layerPCH_SM);
    if (t === 'pda_SM') cb.checked ? layerPDA_SM.addTo(heroMap) : heroMap.removeLayer(layerPDA_SM);
};

window.toggleWS = function(ws, cb) {
    var layers = [];
    if (ws === 'MTB') {
        layers = ['bendungan_MTB', 'bendung_MTB', 'embung_MTB', 'pengaman_MTB', 'sedimen_MTB', 'irigasi_MTB', 'pch_MTB', 'pda_MTB'];
    } else if (ws === 'SS') {
        layers = ['bendungan_SS', 'bendung_SS', 'embung_SS', 'pengaman_SS', 'sedimen_SS', 'irigasi_SS', 'pch_SS', 'pda_SS'];
    } else if (ws === 'SM') {
        layers = ['bendungan_SM', 'bendung_SM', 'embung_SM', 'pengaman_SM', 'sedimen_SM', 'irigasi_SM', 'pch_SM', 'pda_SM'];
    }
    layers.forEach(function(id) {
        var el = document.querySelector('input[onchange*="toggleLayer(\'' + id + '\',this)"]');
        if (el) {
            el.checked = cb.checked;
            toggleLayer(id, { target: el, checked: cb.checked });
        }
    });
};

window.toggleWSMobile = function(ws, cb) {
    var prefix = '';
    if (ws === 'MTB') prefix = '_MTB';
    else if (ws === 'SS') prefix = '_SS';
    else if (ws === 'SM') prefix = '_SM';
    var layerIds = ['bendungan' + prefix, 'bendung' + prefix, 'embung' + prefix, 'pengaman' + prefix, 'sedimen' + prefix, 'irigasi' + prefix, 'pch' + prefix, 'pda' + prefix];
    layerIds.forEach(function(id) {
        var el = document.querySelector('input[onchange*="toggleLayer(\'' + id + '\',this)"]');
        if (el) {
            el.checked = cb.checked;
            toggleLayer(id, { target: el, checked: cb.checked });
        }
    });
};

// =============================================
// SWITCH BASE MAP
// =============================================
window.switchBaseMap = function(t) {
    if (t === 'osm') {
        heroMap.removeLayer(satellite);
        heroMap.addLayer(osm);
    } else {
        heroMap.removeLayer(osm);
        heroMap.addLayer(satellite);
    }
    var o = document.getElementById('btn-osm'),
        s = document.getElementById('btn-satellite');
    if (o && s) {
        if (t === 'osm') {
            o.classList.add('bg-white/60', 'text-slate-700');
            o.classList.remove('text-slate-500');
            s.classList.remove('bg-white/60', 'text-slate-700');
            s.classList.add('text-slate-500');
        } else {
            s.classList.add('bg-white/60', 'text-slate-700');
            s.classList.remove('text-slate-500');
            o.classList.remove('bg-white/60', 'text-slate-700');
            o.classList.add('text-slate-500');
        }
    }
};

// =============================================
// CLOSE PANELS ON MAP CLICK & ESC
// =============================================
heroMap.on('click', function() {
    closeAllDesktopPanels();
    closeSlideUp();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllDesktopPanels();
        closeSlideUp();
    }
});

});
</script>