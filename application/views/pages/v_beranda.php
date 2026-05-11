<header class="relative w-full min-h-[85vh] flex items-center bg-darkblue pt-20 lg:pt-24 overflow-hidden" style="background-image: url('<?= $hero_bg ?>'); background-size: cover; background-position: center;">
    
    <div class="absolute inset-0 bg-darkblue/70 mix-blend-multiply"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-darkblue/90 via-transparent to-transparent"></div>
    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-[0] z-[5]">
        <svg class="relative block w-full h-[60px] md:h-[80px] lg:h-[100px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path class="wave-top" d="M0,64L80,58.7C160,53,320,43,480,48C640,53,800,75,960,80C1120,85,1280,75,1360,69.3L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z" fill="#0a2a4a" fill-opacity="0.4"/>
            <path class="wave-middle" d="M0,96L80,90.7C160,85,320,75,480,74.7C640,75,800,85,960,90.7C1120,96,1280,96,1360,96L1440,96L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z" fill="#0d3d66" fill-opacity="0.6"/>
            <path class="wave-bottom" d="M0,106L80,101.3C160,96,320,85,480,85.3C640,85,800,96,960,101.3C1120,107,1280,107,1360,107L1440,107L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z" fill="#134a7a" fill-opacity="0.8"/>
        </svg>
    </div>

    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-[0] z-[4] opacity-50">
        <svg class="relative block w-full h-[40px] md:h-[60px] lg:h-[80px] animate-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,64L80,58.7C160,53,320,43,480,48C640,53,800,75,960,80C1120,85,1280,75,1360,69.3L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z" fill="#1a6bbf" fill-opacity="0.3"/>
        </svg>
    </div>
    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-12 flex flex-col lg:flex-row items-center justify-between gap-12 py-16">
        
        <div class="w-full lg:w-1/2 text-white">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-brandyellow text-darkblue text-xs font-bold rounded-full mb-6 tracking-widest uppercase">
                <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
            </span>
                Live Portal
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-[1.1] mb-6">
                Sistem Informasi<br>
                <span class="text-brandyellow">Hidrologi</span>
            </h1>
            <p class="text-lg text-slate-300 font-light max-w-lg mb-8 leading-relaxed">
                Menyajikan data riil curah hujan dan ketinggian muka air pada wilayah sungai mesuji-sekampung dan wilayah mesuji-tulang bawang.
            </p>
            <a href="<?= base_url('CurahHujan') ?>" class="inline-flex items-center justify-center px-8 py-3.5 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg transition-all shadow-[0_0_20px_rgba(254,183,0,0.3)] hover:shadow-[0_0_25px_rgba(254,183,0,0.5)]">
                Jelajahi Data
            </a>
        </div>

        <div class="w-full lg:w-1/2 flex justify-end lg:justify-end justify-center">
            <div class="w-full sm:w-96 bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-xl border border-white/20 p-7 rounded-[2rem] shadow-[0_8px_32px_rgba(0,0,0,0.3)] relative overflow-hidden group">
                
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-brandyellow/20 rounded-full blur-3xl transition-all duration-500 group-hover:bg-brandyellow/30"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-500/20 rounded-full blur-3xl transition-all duration-500"></div>

                <div class="relative z-10">
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brandyellow opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-brandyellow"></span>
                            </span>
                            <h3 class="text-white/60 text-[11px] font-semibold tracking-[0.2em] uppercase">Pantauan pos duga air</h3>
                        </div>
                        <span class="bg-red-500/20 text-red-400 text-[10px] font-bold px-2.5 py-1 rounded uppercase tracking-widest">
                            <?= $dam_status['status'] ?>
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <h4 class="text-white/90 font-medium text-base mb-1"><?= $dam_status['nama'] ?></h4>
                        <div class="flex items-end gap-2">
                            <span class="text-5xl font-bold text-white leading-none tracking-tight">
                                <?= $dam_status['level'] ?>
                            </span>
                            <span class="text-white/50 text-sm font-medium mb-1">meter</span>
                        </div>
                    </div>

                    <hr class="border-white/10 my-6">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-white/5 flex items-center justify-center text-brandyellow">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-white font-medium text-base"><?= $weather_data['kondisi'] ?></h3>
                                <p class="text-white/50 text-[11px] mt-0.5 tracking-wide"><?= $weather_data['prediksi'] ?></p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-2xl font-bold text-white leading-none"><?= $weather_data['curah'] ?></span>
                            <span class="text-brandyellow/80 text-[10px] font-semibold uppercase tracking-widest mt-1.5">mm/24h</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
    </div>
</header>
<section class="relative py-24 overflow-hidden bg-slate-50">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full opacity-5 pointer-events-none">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full fill-darkblue">
            <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <div class="lg:col-span-5">
                <div class="inline-block px-4 py-1.5 bg-darkblue/5 text-darkblue text-[10px] font-bold rounded-md mb-6 tracking-[0.2em] uppercase border-l-4 border-brandyellow">
                    Digital Monitoring Hidrologi
                </div>
                <h2 class="text-4xl font-black text-darkblue leading-tight mb-6 uppercase tracking-tighter">
                    Solusi Digital Untuk <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-darkblue to-blue-600">Memonitoring Curah Hujan dan Tinggi Muka Air</span>
                </h2>
                <p class="text-slate-600 leading-relaxed mb-8 font-light italic border-l-2 border-slate-200 pl-4">
                    "Sistem informasi ini merupakan manifestasi transformasi digital BBWS Mesuji Sekampung dalam memantau siklus hidrologi secara akurat, transparan, dan real-time."
                </p>
                <div class="flex gap-4">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[10px] font-bold">IT</div>
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-darkblue text-white flex items-center justify-center text-[10px] font-bold">HY</div>
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-brandyellow flex items-center justify-center text-[10px] font-bold">PU</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="group p-8 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-darkblue group-hover:text-white transition-colors duration-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue mb-3">Monitoring Riil</h3>
                    <p class="text-sm text-slate-500 font-light leading-relaxed">Pantau data TMA dan curah hujan dari puluhan stasiun otomatis (ARR/AWLR) secara langsung.</p>
                </div>

                <div class="group p-8 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-500 md:mt-8">
                    <div class="w-14 h-14 bg-yellow-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-brandyellow transition-colors duration-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue mb-3">Analisis Cepat</h3>
                    <p class="text-sm text-slate-500 font-light leading-relaxed">Sistem cerdas yang mengolah data mentah menjadi informasi grafik dan tren yang mudah dipahami pengambil kebijakan.</p>
                </div>

                <div class="group p-8 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                    <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-darkblue group-hover:text-white transition-colors duration-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue mb-3">Peta Interaktif</h3>
                    <p class="text-sm text-slate-500 font-light leading-relaxed">Visualisasi sebaran pos curah hujan dan pos duga air dalam satu peta interaktif.</p>
                </div>

                <div class="group p-8 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-500 md:mt-8">
                    <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-red-500 group-hover:text-white transition-colors duration-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue mb-3">Peringatan Dini</h3>
                    <p class="text-sm text-slate-500 font-light leading-relaxed">Notifikasi otomatis saat parameter hidrologi melewati ambang batas normal demi keselamatan publik.</p>
                </div>

            </div>
        </div>
    </div>
</section>
<section id="galeri" class="py-24 bg-slate-50 relative">
    <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-white to-transparent pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <div class="inline-block px-3 py-1 bg-brandyellow/20 text-yellow-600 text-[10px] font-bold rounded-md mb-3 tracking-[0.2em] uppercase">
                    Peta Sebaran
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-darkblue uppercase tracking-tight">Kondisi <span class="text-transparent bg-clip-text bg-gradient-to-r from-darkblue to-blue-500">Bendungan</span></h2>
                <p class="text-slate-500 font-light mt-2 max-w-2xl">Pantau lokasi, level air, dan curah hujan di berbagai titik bendungan secara interaktif. Klik pada ikon marker yang berdenyut untuk melihat detail teknis.</p>
            </div>
        </div>

        <!-- Container Peta -->
        <div class="relative p-2 bg-white rounded-[2.5rem] shadow-2xl border border-slate-100">
            <div id="map" class="w-full h-[450px] md:h-[600px] rounded-[2rem] overflow-hidden z-10 bg-slate-100"></div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    @keyframes waveAnimation {
        0% { transform: translateX(0) translateY(0); }
        25% { transform: translateX(-25px) translateY(-5px); }
        50% { transform: translateX(-50px) translateY(0); }
        75% { transform: translateX(-25px) translateY(5px); }
        100% { transform: translateX(0) translateY(0); }
    }
    .animate-wave {
        animation: waveAnimation 8s ease-in-out infinite;
    }
    .wave-top, .wave-middle, .wave-bottom {
        transition: all 0.3s ease;
    }
    header:hover .wave-top { transform: translateY(-3px); }
    header:hover .wave-middle { transform: translateY(-2px); }
    @media (max-width: 768px) {
        .animate-wave {
            animation-duration: 6s;
        }
    }

    /* ANIMATED CSS MARKER PETA */
    .custom-div-icon {
        background: transparent;
        border: none;
    }
    .marker-pin {
        width: 30px;
        height: 30px;
        border-radius: 50% 50% 50% 0;
        background: #0a2a4a;
        position: absolute;
        transform: rotate(-45deg);
        left: 50%;
        top: 50%;
        margin: -15px 0 0 -15px;
        border: 2px solid #feb700;
        box-shadow: 0 4px 10px rgba(0,0,0,0.4);
        transition: transform 0.3s ease;
    }
    .marker-pin::after {
        content: '';
        width: 14px;
        height: 14px;
        margin: 6px 0 0 6px;
        background: #fff;
        position: absolute;
        border-radius: 50%;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
    }
    .custom-div-icon:hover .marker-pin {
        transform: rotate(-45deg) scale(1.1);
        background: #134a7a;
    }
    .marker-pulse {
        width: 30px;
        height: 30px;
        background: rgba(254, 183, 0, 0.6);
        border-radius: 50%;
        position: absolute;
        left: 50%;
        top: 50%;
        margin: -15px 0 0 -15px;
        animation: map-pulse 2s infinite;
        z-index: -1;
    }
    @keyframes map-pulse {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(2.5); opacity: 0; }
    }

    .leaflet-popup-content-wrapper {
        padding: 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .leaflet-popup-content {
        margin: 0;
        width: 320px !important;
    }
    .leaflet-popup-tip-container {
        margin-top: -1px;
    }
    .custom-popup-container {
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
    }
    .popup-header {
        background: linear-gradient(135deg, #0a2a4a 0%, #134a7a 100%);
        color: white;
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .popup-title {
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.5px;
        margin: 0;
        line-height: 1.2;
    }
    .update-badge {
        background: rgba(255,255,255,0.15);
        color: #fff;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 9px;
        text-align: right;
        min-width: fit-content;
    }
    .popup-body {
        padding: 16px;
        background: #fff;
        max-height: 260px; 
        overflow-y: auto;  
    }
   
    .popup-body::-webkit-scrollbar {
        width: 6px;
    }
    .popup-body::-webkit-scrollbar-track {
        background: #f1f5f9; 
    }
    .popup-body::-webkit-scrollbar-thumb {
        background: #cbd5e1; 
        border-radius: 10px;
    }
    .popup-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8; 
    }

    .data-section {
        margin-bottom: 16px;
    }
    .section-title {
        font-weight: 700;
        color: #64748b;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .section-title::after {
        content: '';
        height: 1px;
        flex: 1;
        background: #f1f5f9;
    }
    
    /* Tabel Data Teknis */
    .tech-table {
        width: 100%;
        font-size: 11px;
        border-collapse: collapse;
    }
    .tech-table tr:nth-child(even) { background: #f8fafc; }
    .tech-table td {
        padding: 6px 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .tech-table td:first-child {
        color: #64748b;
        font-weight: 500;
        width: 50%;
    }
    .tech-table td:last-child {
        color: #0f172a;
        font-weight: 600;
        text-align: left;
    }
    .popup-btn {
        display: block;
        text-align: center;
        background: #f8fafc;
        color: #0a2a4a !important;
        padding: 12px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.2s;
        border-top: 1px solid #e2e8f0;
    }
    .popup-btn:hover {
        background: #feb700;
        color: #0a2a4a !important;
    }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var mapData = <?= json_encode($map_data) ?>;

        var map = L.map('map', {
            zoomControl: false 
        });

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        var googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains:['mt0','mt1','mt2','mt3'],
            attribution: '© Google Maps'
        }).addTo(map);

        var modernIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div class='marker-pulse'></div><div class='marker-pin'></div>",
            iconSize: [30, 42],
            iconAnchor: [15, 42],
            popupAnchor: [0, -40]
        });

        var markersArray = [];

        mapData.forEach(function(item) {
            var latLng = [parseFloat(item.lat), parseFloat(item.lng)];
            markersArray.push(latLng);

            var popupContent = `
                <div class="custom-popup-container">
                    <div class="popup-header">
                        <h4 class="popup-title">${item.nama}</h4>
                        <span class="update-badge">${item.last_update}</span>
                    </div>
                    
                    <div class="popup-body">
                        
                        <!-- CARD TMA & CURAH HUJAN BERSAMAAN -->
                        <div class="data-section">
                            <p class="section-title">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Hidrologi Aktual
                            </p>
                            <div class="flex gap-2">
                                <div class="flex-1 bg-blue-50 border border-blue-100 rounded-lg p-3 flex flex-col items-center justify-center text-center">
                                    <span class="text-[9px] text-blue-600 font-bold uppercase mb-1 tracking-wider">TMA (Level Air)</span>
                                    <span class="text-xl font-black text-darkblue leading-none">${item.wlevel} <span class="text-xs font-medium text-slate-500">m</span></span>
                                </div>
                                <div class="flex-1 bg-yellow-50 border border-yellow-100 rounded-lg p-3 flex flex-col items-center justify-center text-center">
                                    <span class="text-[9px] text-yellow-600 font-bold uppercase mb-1 tracking-wider">Curah Hujan</span>
                                    <span class="text-xl font-black text-darkblue leading-none">${item.rain} <span class="text-xs font-medium text-slate-500">mm</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL DATA TEKNIS -->
                        <div class="data-section">
                            <p class="section-title">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Detail Teknis
                            </p>
                            <table class="tech-table">
                                <tr><td>Tipe</td><td>: ${item.tipe || '-'}</td></tr>
                                <tr><td>Pj. Puncak</td><td>: ${item.pj_puncak || '-'} m</td></tr>
                                <tr><td>Elev. Puncak (Tepi)</td><td>: ${item.el_tepi || '-'} m</td></tr>
                                <tr><td>Elev. Puncak (Tengah)</td><td>: ${item.el_tengah || '-'} m</td></tr>
                                <tr><td>Lebar Puncak</td><td>: ${item.lb_puncak || '-'} m</td></tr>
                                <tr><td>Tinggi Max</td><td>: ${item.tinggi_max || '-'} m</td></tr>
                                <tr><td>Vol. Timbunan</td><td>: ${item.vol_timbunan || '-'} m³</td></tr>
                                <tr><td>Pj. Inspeksi</td><td>: ${item.pj_inspeksi || '-'} m</td></tr>
                                <tr><td>Pj. Akses</td><td>: ${item.pj_akses || '-'} m</td></tr>
                            </table>
                        </div>
                    </div>

                    <a href="<?= base_url('Peta') ?>" class="popup-btn">LIHAT PETA PENUH</a>
                </div>
            `;
            
            var marker = L.marker(latLng, {icon: modernIcon})
                .addTo(map)
                // Menambahkan autoPanPadding agar popup tidak mepet ke batas atas map
                .bindPopup(popupContent, { autoPanPadding: [20, 20] });

            marker.on('click', function() {
                map.flyTo(latLng, 16, {
                    animate: true,
                    duration: 1.5
                });
            });
        });

        if (markersArray.length > 0) {
            var bounds = L.latLngBounds(markersArray);
            map.fitBounds(bounds, {
                padding: [50, 50],
                maxZoom: 12
            });
        }
    });
</script>