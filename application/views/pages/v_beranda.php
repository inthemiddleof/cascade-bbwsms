<?php
$selected_date = $this->input->get('date') ?: date('Y-m-d');
$total_pos = count($pch_db) + count($pda_db) + count($bendungan_db);
$bendung_count = isset($bendung_count) ? $bendung_count : 0;
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
        <div class="mb-3 bg-white/50 rounded-xl p-3">
            <div class="flex gap-1.5 mt-1">
                <input type="date" id="history-date" value="<?= $selected_date ?>" class="flex-1 px-2.5 py-2 rounded-lg bg-white/80 border border-white/40 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandyellow font-medium">
                <button onclick="applyHistoryDate()" class="px-3 py-2 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg text-[10px] transition-all">Cari</button>
            </div>
        </div>
        <div class="relative">
            <input type="text" id="search-input-desktop" placeholder="Ketik nama pos..." class="w-full pl-10 pr-4 py-2.5 bg-white/70 rounded-xl border border-white/40 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brandyellow focus:bg-white transition-all font-medium" onkeyup="handleSearch(this.value,'desktop')" autocomplete="off">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <div id="search-results-desktop" class="max-h-60 overflow-y-auto mt-3">
            <div id="search-results-list-desktop" class="space-y-0.5"></div>
            <div id="search-no-results-desktop" class="px-4 py-6 text-center text-xs text-slate-400 hidden"><svg class="w-6 h-6 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><p class="font-medium">Tidak ditemukan</p></div>
        </div>
    </div>

    <!-- DESKTOP: INFO PANEL -->
    <div id="desktop-panel-info" class="desktop-slide-panel hidden lg:block absolute top-1/2 -translate-y-1/2 left-[76px] z-[1000] bg-white/40 backdrop-blur-xl rounded-2xl shadow-xl shadow-black/5 border border-white/30 p-4 w-60 opacity-0 invisible -translate-y-2 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between mb-3"><p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Informasi</p><button onclick="closeAllDesktopPanels()" class="w-6 h-6 rounded-lg bg-white/60 flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-700 transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        <div class="grid grid-cols-2 gap-1.5 mb-3">
            <div class="bg-red-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">Bendungan</p><p class="text-sm font-black text-darkblue"><?= count($bendungan_db) ?></p></div>
            <div class="bg-cyan-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">Bendung</p><p class="text-sm font-black text-darkblue"><?= $bendung_count ?></p></div>
            <div class="bg-blue-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">PCH</p><p class="text-sm font-black text-darkblue"><?= count($pch_db) ?></p></div>
            <div class="bg-purple-50/60 rounded-xl px-3 py-2 text-center"><p class="text-[9px] text-slate-500 font-medium">PDA</p><p class="text-sm font-black text-darkblue"><?= count($pda_db) ?></p></div>
        </div>
        <div class="border-t border-white/30 my-3"></div>
        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-2">Keterangan</p>
        <div class="space-y-1">
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span><span class="text-[10px] text-slate-600">Bendungan</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-cyan-600 flex-shrink-0"></span><span class="text-[10px] text-slate-600">Bendung</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-blue-600 flex-shrink-0"></span><span class="text-[10px] text-slate-600">PCH (Hujan)</span></div>
            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg"><span class="w-2 h-2 rounded-full bg-purple-600 flex-shrink-0"></span><span class="text-[10px] text-slate-600">PDA (TMA)</span></div>
        </div>
    </div>

    <!-- DESKTOP: LAYER PANEL (4 LAYER TERPISAH) -->
    <div id="desktop-panel-layer" class="desktop-slide-panel hidden lg:block absolute top-1/2 -translate-y-1/2 left-[76px] z-[1000] bg-white/40 backdrop-blur-xl rounded-2xl shadow-xl shadow-black/5 border border-white/30 p-4 w-56 opacity-0 invisible -translate-y-2 transition-all duration-300 ease-out">
        <div class="flex items-center justify-between mb-4"><p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Layers</p><button onclick="closeAllDesktopPanels()" class="w-6 h-6 rounded-lg bg-white/60 flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-700 transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        <div class="space-y-1">
            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer bg-white/50 hover:bg-white/80 transition-all border border-white/40"><input type="checkbox" checked onchange="toggleLayer('bendungan',this)" class="w-4 h-4 rounded accent-red-500"><div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span><span class="text-xs font-medium text-slate-700">Bendungan</span></div></label>
            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer bg-white/50 hover:bg-white/80 transition-all border border-white/40"><input type="checkbox" checked onchange="toggleLayer('bendung',this)" class="w-4 h-4 rounded accent-cyan-500"><div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-cyan-500"></span><span class="text-xs font-medium text-slate-700">Bendung</span></div></label>
            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer bg-white/50 hover:bg-white/80 transition-all border border-white/40"><input type="checkbox" onchange="toggleLayer('pch',this)" class="w-4 h-4 rounded accent-blue-500"><div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-500"></span><span class="text-xs font-medium text-slate-700">PCH (Curah Hujan)</span></div></label>
            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer bg-white/50 hover:bg-white/80 transition-all border border-white/40"><input type="checkbox" onchange="toggleLayer('pda',this)" class="w-4 h-4 rounded accent-purple-500"><div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-purple-500"></span><span class="text-xs font-medium text-slate-700">PDA (TMA)</span></div></label>
        </div>
        <div class="my-3 border-t border-white/30"></div>
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
function idNum(val,dec){if(val===null||val===undefined||val===''||isNaN(parseFloat(val)))return null;return parseFloat(val).toFixed(dec).replace('.',',');}
function fmtUpdate(dtStr){if(!dtStr)return'-';var s=String(dtStr).replace('T',' ');var parts=s.split(' ');var d=parts[0];var t=parts[1]?parts[1].substr(0,5):'';var dp=d.split('-');if(dp.length!==3)return s;var tgl=dp[2]+'/'+dp[1]+'/'+dp[0];return t?(tgl+' '+t):tgl;}
var osm=L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png',{attribution:'© OpenStreetMap',subdomains:'abcd',maxZoom:20});
var satellite=L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{subdomains:['mt0','mt1','mt2','mt3']});
var isMobile=window.innerWidth<1024;
var heroMap=L.map('hero-map',{zoomControl:false,dragging:true,scrollWheelZoom:true,doubleClickZoom:true,boxZoom:true,touchZoom:true,layers:[osm],minZoom:isMobile?7:8}).setView([-5.3971,105.2668],isMobile?8:9);
window.heroMap=heroMap;

// 4 Layer terpisah
var layerBendungan=L.layerGroup().addTo(heroMap);
var layerBendung=L.layerGroup().addTo(heroMap);
var layerPCH=L.layerGroup();
var layerPDA=L.layerGroup();

window.addEventListener('resize',function(){var n=window.innerWidth<1024;if(n!==isMobile){isMobile=n;heroMap.setMinZoom(isMobile?7:8)}});

function createMask(d){var o=[[[-90,-180],[-90,180],[90,180],[90,-180],[-90,-180]]];d.features.forEach(function(f){if(f.geometry.type==='Polygon'){var c=f.geometry.coordinates.map(function(r){return r.map(function(c){return[c[1],c[0]]})});o.push(c[0])}else if(f.geometry.type==='MultiPolygon'){f.geometry.coordinates.forEach(function(p){var c=p.map(function(r){return r.map(function(c){return[c[1],c[0]]})});o.push(c[0])})}});return L.polygon(o,{color:'none',weight:0,fillColor:'#f1f5f9',fillOpacity:1,interactive:false})}

function createCustomIcon(color, shouldPulse) {
    var pulseHTML = shouldPulse ? '<div style="position:absolute;top:-5px;left:-5px;width:24px;height:24px;border-radius:50%;background:'+color+';opacity:0.3;animation:pulse-marker 2s infinite;"></div>' : '';
    return L.divIcon({html:'<div style="position:relative;width:14px;height:14px;">'+pulseHTML+'<div style="position:absolute;top:1px;left:1px;width:12px;height:12px;border-radius:50%;background:'+color+';box-shadow:0 0 8px '+color+'99;border:2px solid #fff;"></div></div>',iconSize:[20,20],iconAnchor:[10,10]});
}

function getPopupWidth(){if(window.innerWidth<640)return 280;if(window.innerWidth<1024)return 340;return 360}

var dasData=<?=$das_geojson?>;if(dasData){L.geoJSON(dasData,{style:{fillColor:"transparent",weight:1,opacity:0.4,color:'#d4d401',fillOpacity:0.1,dashArray:'4 6'},onEachFeature:function(f,l){if(f.properties&&f.properties.NAMA_DAS)l.bindTooltip(f.properties.NAMA_DAS,{sticky:true,className:'das-label'})}}).addTo(heroMap)}
var wsData=<?=$ws_geojson?>;if(wsData){var maskLayer=createMask(wsData);maskLayer.setStyle({fillColor:'#f1f5f9',fillOpacity:0.9});maskLayer.addTo(heroMap);L.geoJSON(wsData,{style:function(){return{fillColor:"#f55959",weight:1,opacity:0.6,color:'#d40101',fillOpacity:0}},onEachFeature:function(f,l){l.bindTooltip("WS: "+f.properties.WS,{sticky:true})}}).addTo(heroMap)}

var searchIndex=[];var bendunganDataDB=<?=json_encode($bendungan_db)?>;var bendungDataDB=<?=json_encode($bendung_db)?>;var pchDataDB=<?=isset($pch_db)?json_encode($pch_db):'[]'?>;var pdaDataDB=<?=isset($pda_db)?json_encode($pda_db):'[]'?>;
if(bendunganDataDB){bendunganDataDB.forEach(function(p){if(!p.lat||!p.lng)return;searchIndex.push({id:p.id_pos,name:p.nama_pos,type:'Bendungan',color:'#EF4444',bgColor:'#fef2f2',lat:parseFloat(p.lat),lng:parseFloat(p.lng),zoom:16})})}
if(bendungDataDB){bendungDataDB.forEach(function(p){if(!p.lat||!p.lng)return;searchIndex.push({id:p.id_pos,name:p.nama_pos,type:'Bendung',color:'#06B6D4',bgColor:'#ecfeff',lat:parseFloat(p.lat),lng:parseFloat(p.lng),zoom:15})})}
if(pchDataDB){pchDataDB.forEach(function(p){if(!p.lat||!p.lng)return;searchIndex.push({id:p.id_pos,name:p.nama_pos,type:'PCH (Hujan)',color:'#3B82F6',bgColor:'#eff6ff',lat:parseFloat(p.lat),lng:parseFloat(p.lng),zoom:15})})}
if(pdaDataDB){pdaDataDB.forEach(function(p){if(!p.lat||!p.lng)return;searchIndex.push({id:p.id_pos,name:p.nama_pos,type:'PDA (TMA)',color:'#8B5CF6',bgColor:'#f5f3ff',lat:parseFloat(p.lat),lng:parseFloat(p.lng),zoom:15})})}
searchIndex.sort(function(a,b){return a.name.localeCompare(b.name)});

window.showAllPos=function(d){var l=document.getElementById('search-results-list-'+d),n=document.getElementById('search-no-results-'+d);if(!l)return;if(searchIndex.length>0){l.innerHTML=searchIndex.map(function(i){return createResultItem(i,d)}).join('');if(n)n.classList.add('hidden')}};
window.handleSearch=function(q,d){var l=document.getElementById('search-results-list-'+d),n=document.getElementById('search-no-results-'+d);if(!l)return;if(!q||q.trim().length===0){showAllPos(d);return}var r=searchIndex.filter(function(i){return i.name.toLowerCase().includes(q.toLowerCase().trim())||i.type.toLowerCase().includes(q.toLowerCase().trim())});if(r.length>0){l.innerHTML=r.slice(0,20).map(function(i){return createResultItem(i,d)}).join('');if(n)n.classList.add('hidden')}else{l.innerHTML='';if(n)n.classList.remove('hidden')}};
function createResultItem(i,d){return'<button type="button" onclick="flyToLocation('+i.lat+','+i.lng+','+i.zoom+',\''+i.name.replace(/'/g,"\\'")+'\',\''+d+'\')" class="w-full text-left px-3 py-2.5 rounded-xl hover:bg-white/80 transition-all flex items-center gap-3 border border-transparent hover:border-white/60"><div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:'+i.bgColor+';"><span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:'+i.color+';"></span></div><div class="min-w-0 flex-1"><p class="text-xs font-bold text-slate-700 truncate leading-tight">'+i.name+'</p><p class="text-[9px] text-slate-400 mt-0.5">'+i.type+'</p></div><svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>'}
window.flyToLocation=function(lat,lng,z,n,d){heroMap.flyTo([lat,lng],z,{animate:true,duration:1.5});setTimeout(function(){heroMap.eachLayer(function(l){if(l instanceof L.Marker){var ml=l.getLatLng();if(Math.abs(ml.lat-lat)+Math.abs(ml.lng-lng)<0.0005){l.openPopup();return}}})},1600);if(d==='desktop'){closeAllDesktopPanels();document.getElementById('search-input-desktop').value=''}else{closeSlideUp()}};
var so=new MutationObserver(function(m){m.forEach(function(m){if(m.target.id==='desktop-panel-search'&&m.target.classList.contains('show')){showAllPos('desktop')}})});var sp=document.getElementById('desktop-panel-search');if(sp)so.observe(sp,{attributes:true,attributeFilter:['class']});

// ==========================================
// BENDUNGAN - Merah (#EF4444)
// ==========================================
if(bendunganDataDB){bendunganDataDB.forEach(function(p){if(!p.lat||!p.lng)return;var hasRain=(p.rain>0);var m=L.marker([parseFloat(p.lat),parseFloat(p.lng)],{icon:createCustomIcon('#EF4444',hasRain)}).addTo(layerBendungan);m.on('click',function(e){heroMap.flyTo(e.latlng,16,{animate:true,duration:1.5})});var pw=getPopupWidth();var fh=pw<300?'15px':'17px';var fv=pw<300?'20px':'24px';var pop='<div style="width:'+pw+'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#EF4444;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#FCA5A5;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Bendungan</span></div><h4 style="font-size:'+fh+';font-weight:800;margin:0;line-height:1.2;">'+p.nama_pos+'</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">ID: '+p.id_pos+' | Sungai: '+(p.sungai||'-')+'</p></div><div style="padding:12px 14px;"><div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;"><div style="background:#fef2f2;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #fecaca;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#EF4444;margin:0 0 2px 0;">Elevasi / TMA</p><p style="font-size:'+fv+';font-weight:800;color:#1e293b;margin:0;line-height:1;">'+(idNum(p.elevasi,2)||'0,00')+'</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">m</p></div><div style="background:#fefce8;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #fef08a;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#eab308;margin:0 0 2px 0;">Curah Hujan</p><p style="font-size:'+fv+';font-weight:800;color:#1e293b;margin:0;line-height:1;">'+(idNum(p.rain,1)||'0')+'</p><p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">mm</p></div></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Volume</span><span style="font-size:9px;font-weight:700;color:#1e293b;">'+(idNum(p.volume,2)||'-')+' jt.m³</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Inflow</span><span style="font-size:9px;font-weight:700;color:#059669;">'+(idNum(p.inflow,3)||'0')+' m³/s</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Outflow</span><span style="font-size:9px;font-weight:700;color:#EF4444;">'+(idNum(p.total_outflow,3)||'0')+' m³/s</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">NWL</span><span style="font-size:9px;font-weight:700;color:#1e293b;">'+(idNum(p.nwl,2)||'-')+' m</span></div></div><div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">'+fmtUpdate(p.created_at)+'</span></div></div></div></div>';m.bindPopup(pop,{maxWidth:pw,minWidth:pw,className:'custom-leaflet-popup',offset:[0,-6]});m.bindTooltip(p.nama_pos,{direction:'top',offset:[0,-10]})})}


// ==========================================
// BENDUNG - Cyan (#06B6D4) - Denyut jika ada hujan atau elevasi
// ==========================================
if(bendungDataDB&&bendungDataDB.length>0){bendungDataDB.forEach(function(p){if(!p.lat||!p.lng)return;
    var hasRain = (p.rain > 0);
    var hasElevasi = (p.elevasi_mercu != null && parseFloat(p.elevasi_mercu) > 0);
    var shouldPulse = hasRain || hasElevasi;
    var m=L.marker([parseFloat(p.lat),parseFloat(p.lng)],{icon:createCustomIcon('#06B6D4', shouldPulse)}).addTo(layerBendung);
    m.on('click',function(e){heroMap.flyTo(e.latlng,15,{animate:true,duration:1.5})});
    var pw=getPopupWidth();var fh=pw<300?'15px':'17px';var fv=pw<300?'20px':'24px';
    var pop='<div style="width:'+pw+'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;">'+
        '<div style="background:#06B6D4;padding:12px 14px;color:#fff;">'+
            '<div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">'+
                '<span style="width:7px;height:7px;border-radius:50%;background:#67E8F9;flex-shrink:0;"></span>'+
                '<span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Bendung</span>'+
            '</div>'+
            '<h4 style="font-size:'+fh+';font-weight:800;margin:0;line-height:1.2;">'+p.nama_pos+'</h4>'+
            '<p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">Sungai: '+(p.sungai||'-')+' | ID: '+p.id_pos+'</p>'+
        '</div>'+
        '<div style="padding:12px 14px;">'+
            '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">'+
                '<div style="background:#ecfeff;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #a5f3fc;">'+
                    '<p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#06B6D4;margin:0 0 2px 0;">Elevasi / TMA</p>'+
                    '<p style="font-size:'+fv+';font-weight:800;color:#1e293b;margin:0;line-height:1;">'+(idNum(p.elevasi_mercu,2)||'0,00')+'</p>'+
                    '<p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">m</p>'+
                '</div>'+
                '<div style="background:#fefce8;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #fef08a;">'+
                    '<p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#eab308;margin:0 0 2px 0;">Curah Hujan</p>'+
                    '<p style="font-size:'+fv+';font-weight:800;color:#1e293b;margin:0;line-height:1;">'+(idNum(p.rain,1)||'0,0')+'</p>'+
                    '<p style="font-size:8px;color:#94a3b8;margin:1px 0 0 0;">mm</p>'+
                '</div>'+
            '</div>'+
            '<div style="border-top:1px solid #f1f5f9;padding-top:8px;">'+
                '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-Total</span><span style="font-size:9px;font-weight:700;color:#1e293b;">'+(idNum(p.q_total,3)||'0,000')+' m³/dt</span></div>'+
                '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-FC1</span><span style="font-size:9px;font-weight:700;color:#059669;">'+(idNum(p.q_fc1,3)||'0,000')+' m³/dt</span></div>'+
                '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-FC2</span><span style="font-size:9px;font-weight:700;color:#3B82F6;">'+(idNum(p.q_fc2,3)||'0,000')+' m³/dt</span></div>'+
                '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-Limpas</span><span style="font-size:9px;font-weight:700;color:#EF4444;">'+(idNum(p.q_limpas,3)||'0,000')+' m³/dt</span></div>'+
                '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Q-SPAM KPBU</span><span style="font-size:9px;font-weight:700;color:#8B5CF6;">'+(idNum(p.q_spam_kpbu,3)||'0,000')+' m³/dt</span></div>'+
                '<div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;"><span style="font-size:9px;color:#64748b;">Sluice Gate</span><span style="font-size:9px;font-weight:700;color:#1e293b;">'+(idNum(p.sluice_gate,3)||'0,000')+' m³/dt</span></div>'+
            '</div>'+
            '<div style="border-top:1px solid #f1f5f9;margin-top:8px;padding-top:8px;">'+
                '<div style="display:flex;justify-content:space-between;align-items:center;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">'+fmtUpdate(p.created_at)+'</span></div>'+
            '</div>'+
        '</div>'+
    '</div>';m.bindPopup(pop,{maxWidth:pw,minWidth:pw,className:'custom-leaflet-popup',offset:[0,-6]});m.bindTooltip(p.nama_pos,{direction:'top',offset:[0,-10]})})}

// ==========================================
// PCH - Biru (#3B82F6)
// ==========================================
if(pchDataDB&&pchDataDB.length>0){pchDataDB.forEach(function(p){if(!p.lat||!p.lng)return;var hasRain=(p.ch_hari_ini>0);var m=L.marker([parseFloat(p.lat),parseFloat(p.lng)],{icon:createCustomIcon('#3B82F6',hasRain)}).addTo(layerPCH);m.on('click',function(e){heroMap.flyTo(e.latlng,15,{animate:true,duration:1.5})});var pw=getPopupWidth();var fh=pw<300?'15px':'17px';var fv=pw<300?'24px':'28px';var pop='<div style="width:'+pw+'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#3B82F6;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#93C5FD;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Pos Curah Hujan</span></div><h4 style="font-size:'+fh+';font-weight:800;margin:0;line-height:1.2;">'+p.nama_pos+'</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">ID: '+p.id_pos+' | PCH</p></div><div style="padding:12px 14px;"><div style="background:#eff6ff;border-radius:12px;padding:14px 10px;text-align:center;margin-bottom:10px;border:1px solid #bfdbfe;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#3B82F6;margin:0 0 4px 0;">Curah Hujan</p><p style="font-size:'+fv+';font-weight:800;color:#1e293b;margin:0;line-height:1;">'+(idNum(p.ch_hari_ini,2)||'0,00')+'</p><p style="font-size:9px;color:#94a3b8;margin:2px 0 0 0;">mm</p></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Intensitas</span><span style="font-size:9px;font-weight:700;color:#1e293b;">'+(p.intensitas||'Normal')+'</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">'+fmtUpdate(p.tgl_terakhir)+'</span></div></div></div></div>';m.bindPopup(pop,{maxWidth:pw,minWidth:pw,className:'custom-leaflet-popup',offset:[0,-6]});m.bindTooltip("PCH: "+p.nama_pos,{direction:'top',offset:[0,-10]})})}

// ==========================================
// PDA - Ungu (#8B5CF6)
// ==========================================
if(pdaDataDB&&pdaDataDB.length>0){pdaDataDB.forEach(function(p){if(!p.lat||!p.lng)return;var isSiaga=(p.status_siaga&&p.status_siaga.toLowerCase()!=='normal');var m=L.marker([parseFloat(p.lat),parseFloat(p.lng)],{icon:createCustomIcon('#8B5CF6',isSiaga)}).addTo(layerPDA);m.on('click',function(e){heroMap.flyTo(e.latlng,15,{animate:true,duration:1.5})});var pw=getPopupWidth();var fh=pw<300?'15px':'17px';var fv=pw<300?'24px':'28px';var pop='<div style="width:'+pw+'px;background:#fff;border-radius:16px;overflow:hidden;font-family:Inter,sans-serif;"><div style="background:#8B5CF6;padding:12px 14px;color:#fff;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;"><span style="width:7px;height:7px;border-radius:50%;background:#C4B5FD;flex-shrink:0;"></span><span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#fff;">Pos TMA</span></div><h4 style="font-size:'+fh+';font-weight:800;margin:0;line-height:1.2;">'+p.nama_pos+'</h4><p style="font-size:9px;color:rgba(255,255,255,0.7);margin:3px 0 0 0;">ID: '+p.id_pos+' | PDA</p></div><div style="padding:12px 14px;"><div style="background:#f5f3ff;border-radius:12px;padding:14px 10px;text-align:center;margin-bottom:10px;border:1px solid #ddd6fe;"><p style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#8B5CF6;margin:0 0 4px 0;">TMA Saat Ini</p><p style="font-size:'+fv+';font-weight:800;color:#1e293b;margin:0;line-height:1;">'+(idNum(p.tma_sekarang,2)||'0,00')+'</p><p style="font-size:9px;color:#94a3b8;margin:2px 0 0 0;">m</p></div><div style="border-top:1px solid #f1f5f9;padding-top:8px;"><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:9px;color:#64748b;">Status</span><span style="font-size:9px;font-weight:700;color:'+(isSiaga?'#EF4444':'#16a34a')+';">'+(p.status_siaga||'Normal')+'</span></div><div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;"><span style="font-size:8px;color:#94a3b8;">Update</span><span style="font-size:8px;font-weight:600;color:#475569;">'+fmtUpdate(p.tgl_terakhir)+'</span></div></div></div></div>';m.bindPopup(pop,{maxWidth:pw,minWidth:pw,className:'custom-leaflet-popup',offset:[0,-6]});m.bindTooltip("PDA: "+p.nama_pos,{direction:'top',offset:[0,-10]})})}

// MOBILE SLIDE UP
window.openSlideUp=function(t){var p=document.getElementById('slide-up-panel'),b=document.getElementById('slide-up-backdrop'),ti=document.getElementById('slide-up-title'),c=document.getElementById('slide-up-content');
if(t==='info'){ti.textContent='Informasi';c.innerHTML='<div class="space-y-4"><div class="grid grid-cols-2 gap-3"><div class="bg-red-50 rounded-2xl p-4 text-center border border-red-100"><p class="text-[11px] font-bold text-red-600 uppercase mb-1">Bendungan</p><p class="text-3xl font-black text-darkblue"><?=count($bendungan_db)?></p></div><div class="bg-cyan-50 rounded-2xl p-4 text-center border border-cyan-100"><p class="text-[11px] font-bold text-cyan-600 uppercase mb-1">Bendung</p><p class="text-3xl font-black text-darkblue"><?=$bendung_count?></p></div><div class="bg-blue-50 rounded-2xl p-4 text-center border border-blue-100"><p class="text-[11px] font-bold text-blue-600 uppercase mb-1">PCH</p><p class="text-3xl font-black text-darkblue"><?=count($pch_db)?></p></div><div class="bg-purple-50 rounded-2xl p-4 text-center border border-purple-100"><p class="text-[11px] font-bold text-purple-600 uppercase mb-1">PDA</p><p class="text-3xl font-black text-darkblue"><?=count($pda_db)?></p></div></div><div class="border-t border-slate-200 pt-4"><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Keterangan Warna</p><div class="space-y-2"><div class="flex items-center gap-3 px-3 py-2 bg-red-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-red-500"></span><span class="text-sm text-slate-700">Bendungan</span></div><div class="flex items-center gap-3 px-3 py-2 bg-cyan-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-cyan-500"></span><span class="text-sm text-slate-700">Bendung</span></div><div class="flex items-center gap-3 px-3 py-2 bg-blue-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-blue-500"></span><span class="text-sm text-slate-700">PCH (Hujan)</span></div><div class="flex items-center gap-3 px-3 py-2 bg-purple-50 rounded-xl"><span class="w-3 h-3 rounded-full bg-purple-500"></span><span class="text-sm text-slate-700">PDA (TMA)</span></div></div></div></div>'}
else if(t==='layer'){ti.textContent='Layer Peta';c.innerHTML='<div class="space-y-3"><label class="flex items-center gap-4 px-4 py-4 rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all border border-slate-200"><input type="checkbox" checked onchange="toggleLayer(\'bendungan\',this)" class="w-5 h-5 rounded accent-red-500"><div class="flex items-center gap-3"><span class="w-3 h-3 rounded-full bg-red-500"></span><span class="text-sm font-medium text-slate-700">Bendungan</span></div></label><label class="flex items-center gap-4 px-4 py-4 rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all border border-slate-200"><input type="checkbox" checked onchange="toggleLayer(\'bendung\',this)" class="w-5 h-5 rounded accent-cyan-500"><div class="flex items-center gap-3"><span class="w-3 h-3 rounded-full bg-cyan-500"></span><span class="text-sm font-medium text-slate-700">Bendung</span></div></label><label class="flex items-center gap-4 px-4 py-4 rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all border border-slate-200"><input type="checkbox" onchange="toggleLayer(\'pch\',this)" class="w-5 h-5 rounded accent-blue-500"><div class="flex items-center gap-3"><span class="w-3 h-3 rounded-full bg-blue-500"></span><span class="text-sm font-medium text-slate-700">PCH (Curah Hujan)</span></div></label><label class="flex items-center gap-4 px-4 py-4 rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all border border-slate-200"><input type="checkbox" onchange="toggleLayer(\'pda\',this)" class="w-5 h-5 rounded accent-purple-500"><div class="flex items-center gap-3"><span class="w-3 h-3 rounded-full bg-purple-500"></span><span class="text-sm font-medium text-slate-700">PDA (TMA)</span></div></label><div class="border-t border-slate-200 my-3 pt-3"><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Tampilan Peta</p><div class="flex gap-3"><button onclick="switchBaseMap(\'osm\');updateBaseMapButtons(\'osm\')" id="btn-osm-mobile" class="flex-1 py-3 text-sm font-semibold rounded-xl bg-slate-200 text-slate-700 transition-all">Peta</button><button onclick="switchBaseMap(\'satellite\');updateBaseMapButtons(\'satellite\')" id="btn-satellite-mobile" class="flex-1 py-3 text-sm font-semibold rounded-xl text-slate-500 bg-slate-50 transition-all">Satelit</button></div></div></div>'}
else if(t==='search'){ti.textContent='Cari & Filter';c.innerHTML='<div class="space-y-4"><div class="bg-slate-50 rounded-2xl p-4 border border-slate-200"><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Filter Tanggal</label><div class="flex gap-2"><input type="date" id="history-date-mobile" value="<?=$selected_date?>" class="flex-1 px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brandyellow font-medium"><button onclick="applyHistoryDateMobile()" class="px-4 py-3 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-xl text-sm transition-all">Cari</button></div><p class="text-xs text-slate-400 text-center mt-2"><?=($selected_date==date('Y-m-d'))?'Data hari ini':'Data '.date('d M Y',strtotime($selected_date))?></p><?php if($selected_date!=date('Y-m-d')):?><a href="<?=base_url('Beranda')?>" class="block text-center text-sm text-slate-500 hover:text-darkblue mt-2 font-medium">→ Kembali ke Hari Ini</a><?php endif;?></div><div class="relative"><input type="text" id="search-input-mobile" placeholder="Ketik nama pos..." class="w-full pl-12 pr-4 py-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brandyellow focus:bg-white transition-all font-medium" onkeyup="handleSearch(this.value,\'mobile\')" autocomplete="off"><svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div><div id="search-results-mobile" class="max-h-96 overflow-y-auto"><div id="search-results-list-mobile" class="space-y-1"></div><div id="search-no-results-mobile" class="px-4 py-8 text-center text-sm text-slate-400 hidden"><p class="font-medium">Tidak ditemukan</p></div></div></div>';setTimeout(function(){showAllPos('mobile')},100)}
p.classList.add('open');b.classList.add('show');document.body.style.overflow='hidden'};
window.closeSlideUp=function(){document.getElementById('slide-up-panel').classList.remove('open');document.getElementById('slide-up-backdrop').classList.remove('show');document.body.style.overflow=''};
var sp2=document.getElementById('slide-up-panel'),ts=0;if(sp2){sp2.addEventListener('touchstart',function(e){ts=e.touches[0].clientY},{passive:true});sp2.addEventListener('touchmove',function(e){if(e.touches[0].clientY-ts>80&&sp2.scrollTop<=0)closeSlideUp()},{passive:true})}
window.applyHistoryDateMobile=function(){var d=document.getElementById('history-date-mobile').value;if(d)window.location.href='<?=base_url('Beranda')?>?date='+d};
window.updateBaseMapButtons=function(t){var o=document.getElementById('btn-osm-mobile'),s=document.getElementById('btn-satellite-mobile');if(t==='osm'){o.className='flex-1 py-3 text-sm font-semibold rounded-xl bg-slate-200 text-slate-700 transition-all';s.className='flex-1 py-3 text-sm font-semibold rounded-xl text-slate-500 bg-slate-50 transition-all'}else{s.className='flex-1 py-3 text-sm font-semibold rounded-xl bg-slate-200 text-slate-700 transition-all';o.className='flex-1 py-3 text-sm font-semibold rounded-xl text-slate-500 bg-slate-50 transition-all'}};

var adp=null;
window.toggleDesktopPanel=function(t){var p=document.getElementById('desktop-panel-'+t);if(!p)return;if(adp&&adp!==p)adp.classList.remove('show');if(p.classList.contains('show')){p.classList.remove('show');adp=null}else{p.classList.add('show');adp=p}};
window.closeAllDesktopPanels=function(){document.querySelectorAll('.desktop-slide-panel').forEach(function(p){p.classList.remove('show')});adp=null};
window.applyHistoryDate=function(){var d=document.getElementById('history-date').value;if(d)window.location.href='<?=base_url('Beranda')?>?date='+d};
window.toggleLayer=function(t,cb){if(t==='bendungan')cb.checked?layerBendungan.addTo(heroMap):heroMap.removeLayer(layerBendungan);if(t==='bendung')cb.checked?layerBendung.addTo(heroMap):heroMap.removeLayer(layerBendung);if(t==='pch')cb.checked?layerPCH.addTo(heroMap):heroMap.removeLayer(layerPCH);if(t==='pda')cb.checked?layerPDA.addTo(heroMap):heroMap.removeLayer(layerPDA)};
window.switchBaseMap=function(t){if(t==='osm'){heroMap.removeLayer(satellite);heroMap.addLayer(osm)}else{heroMap.removeLayer(osm);heroMap.addLayer(satellite)}var o=document.getElementById('btn-osm'),s=document.getElementById('btn-satellite');if(o&&s){if(t==='osm'){o.classList.add('bg-white/60','text-slate-700');o.classList.remove('text-slate-500');s.classList.remove('bg-white/60','text-slate-700');s.classList.add('text-slate-500')}else{s.classList.add('bg-white/60','text-slate-700');s.classList.remove('text-slate-500');o.classList.remove('bg-white/60','text-slate-700');o.classList.add('text-slate-500')}}};
heroMap.on('click',function(){closeAllDesktopPanels();closeSlideUp()});
document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeAllDesktopPanels();closeSlideUp()}});
});
</script>