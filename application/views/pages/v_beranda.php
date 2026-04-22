<?php
// ==============================================================================
// SIMULASI DATA DARI BACKEND (Nanti data ini dikirim dari Controller Welcome.php)
// ==============================================================================
$app_name = "CASCADE";
$logo_url = base_url('assets/img/logo-pu.png'); // Siapkan gambar ini di folder assets
$hero_bg  = "https://images.unsplash.com/photo-1545459723-861eb1bb3809?q=80&w=1920&auto=format&fit=crop";

// Simulasi data Real-time
$dam_status = [
    'nama' => 'Bendungan Margatiga',
    'level' => '12.45',
    'status' => 'Waspada',
    'trend' => 'naik'
];
$weather_data = [
    'kondisi' => 'Hujan Sedang',
    'curah' => '45',
    'prediksi' => 'Berawan dalam 3 jam'
];
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $app_name ?> - Sistem Informasi Hidrologi</title>
    
    <script type="module" src="http://localhost:5173/@vite/client"></script>
    <link rel="stylesheet" href="http://localhost:5173/src/input.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-brandyellow selection:text-darkblue">

    <nav class="bg-darkblue text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex justify-between items-center h-20">
            <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-white rounded flex items-center justify-center p-1 overflow-hidden">
                    <span class="text-darkblue font-black text-xl">PU</span> 
                </div>
                <div class="flex flex-col">
                    <span class="font-bold tracking-widest text-lg leading-tight group-hover:text-brandyellow transition-colors"><?= $app_name ?></span>
                    <span class="text-[10px] text-slate-300 tracking-wider">BBWS MESUJI SEKAMPUNG</span>
                </div>
            </a>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide">
                <a href="#" class="text-brandyellow border-b-2 border-brandyellow py-2">Beranda</a>
                
                <div class="relative group py-6">
                    <button class="hover:text-brandyellow transition flex items-center gap-1">
                        Data <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute top-full left-0 w-48 bg-white text-slate-800 shadow-xl rounded-lg overflow-hidden border border-slate-100">
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition">Curah Hujan</a>
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition">Tinggi Muka Air</a>
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition">Kualitas Air</a>
                    </div>
                </div>

                <div class="relative group py-6">
                    <button class="hover:text-brandyellow transition flex items-center gap-1">
                        Peta <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute top-full left-0 w-48 bg-white text-slate-800 shadow-xl rounded-lg overflow-hidden border border-slate-100">
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition">Sebaran Stasiun</a>
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition">Peta Rawan Banjir</a>
                    </div>
                </div>

                <a href="#" class="hover:text-brandyellow transition py-2">Laporan</a>
            </div>

            <button class="md:hidden text-white hover:text-brandyellow">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </nav>

    <header class="relative w-full min-h-[85vh] flex items-center bg-darkblue" style="background-image: url('<?= $hero_bg ?>'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-darkblue/70 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-darkblue/90 via-transparent to-transparent"></div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-12 flex flex-col lg:flex-row items-center justify-between gap-12 py-16">
            
            <div class="w-full lg:w-1/2 text-white">
                <div class="inline-block px-3 py-1 bg-brandyellow text-darkblue text-xs font-bold rounded-full mb-6 tracking-widest uppercase">
                    Live Portal
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-[1.1] mb-6">
                    Sistem Informasi<br>
                    <span class="text-brandyellow">Hidrologi & Air</span>
                </h1>
                <p class="text-lg text-slate-300 font-light max-w-lg mb-8 leading-relaxed">
                    Menyajikan data riil dan prediksi akurat untuk pengelolaan sumber daya air di wilayah Sungai Mesuji Sekampung secara terpadu dan transparan.
                </p>
                <a href="#galeri" class="inline-flex items-center justify-center px-8 py-3.5 bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold rounded-lg transition-all shadow-[0_0_20px_rgba(254,183,0,0.3)] hover:shadow-[0_0_25px_rgba(254,183,0,0.5)]">
                    Jelajahi Data
                </a>
            </div>

            <div class="w-full lg:w-1/2 flex flex-col gap-5 sm:flex-row lg:flex-col lg:items-end">
                
                <div class="w-full sm:w-80 bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl shadow-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-brandyellow animate-pulse"></span>
                            <h3 class="text-white text-xs font-bold tracking-widest uppercase">Status Bendungan</h3>
                        </div>
                        <span class="bg-red-500/20 text-red-300 border border-red-500/50 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                            <?= $dam_status['status'] ?>
                        </span>
                    </div>
                    <div class="bg-darkblue/50 rounded-xl p-4 border border-white/10">
                        <p class="text-slate-300 text-xs mb-1"><?= $dam_status['nama'] ?></p>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-white"><?= $dam_status['level'] ?></span>
                            <span class="text-slate-400 text-sm mb-1">meter</span>
                        </div>
                        <p class="text-brandyellow text-xs mt-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            Tren Muka Air <?= ucfirst($dam_status['trend']) ?>
                        </p>
                    </div>
                </div>

                <div class="w-full sm:w-80 bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl shadow-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                            <h3 class="text-white text-xs font-bold tracking-widest uppercase">Cuaca Area</h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between bg-darkblue/50 rounded-xl p-4 border border-white/10">
                        <div>
                            <p class="text-white font-bold text-lg"><?= $weather_data['kondisi'] ?></p>
                            <p class="text-slate-400 text-xs mt-1"><?= $weather_data['prediksi'] ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-black text-brandyellow"><?= $weather_data['curah'] ?></p>
                            <p class="text-slate-400 text-[10px] uppercase tracking-wider">mm / 24h</p>
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
                        Digital Water Management
                    </div>
                    <h2 class="text-4xl font-black text-darkblue leading-tight mb-6 uppercase tracking-tighter">
                        Solusi Digital Untuk <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-darkblue to-blue-600">Ketahanan Air Nasional</span>
                    </h2>
                    <p class="text-slate-600 leading-relaxed mb-8 font-light italic border-l-2 border-slate-200 pl-4">
                        "CASCADE merupakan manifestasi transformasi digital BBWS Mesuji Sekampung dalam memantau siklus hidrologi secara akurat, transparan, dan real-time."
                    </p>
                    <div class="flex gap-4">
                        <div class="flex -space-x-3">
                            <div class="w-10 h-10 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[10px] font-bold">IT</div>
                            <div class="w-10 h-10 rounded-full border-2 border-white bg-brandyellow flex items-center justify-center text-[10px] font-bold">HY</div>
                            <div class="w-10 h-10 rounded-full border-2 border-white bg-darkblue text-white flex items-center justify-center text-[10px] font-bold">PU</div>
                        </div>
                        <div class="text-[11px] text-slate-500 font-medium self-center">
                            Terintegrasi dengan sistem <br> pusdatin pusat.
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="group p-8 bg-white rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-darkblue group-hover:text-white transition-colors duration-500">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-darkblue mb-3">Monitoring Riil</h3>
                        <p class="text-sm text-slate-500 font-light leading-relaxed">Pantau data TMA, curah hujan, dan klimatologi dari puluhan stasiun otomatis (ARR/AWLR) secara langsung.</p>
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
                        <h3 class="text-lg font-bold text-darkblue mb-3">Integrasi Spasial</h3>
                        <p class="text-sm text-slate-500 font-light leading-relaxed">Visualisasi sebaran stasiun dan area rawan bencana dalam satu peta interaktif berbasis GIS.</p>
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

    <section id="galeri" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-black text-darkblue uppercase tracking-tight">Dokumentasi Lapangan</h2>
                    <p class="text-slate-500 mt-2 font-light">Kegiatan pemantauan unit hidrologi terkini.</p>
                </div>
                <a href="#" class="text-darkblue font-semibold hover:text-brandyellow transition flex items-center gap-2 text-sm">
                    Lihat Selengkapnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php for($i=1; $i<=8; $i++): ?>
                <div class="group relative aspect-square overflow-hidden rounded-xl bg-slate-100 cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1542337482-1d7e2e30cc81?q=80&w=600&auto=format&fit=crop&sig=<?= $i ?>" alt="Galeri <?= $i ?>" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    
                    <div class="absolute inset-0 bg-darkblue/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
                        <p class="text-brandyellow text-xs font-bold tracking-widest uppercase mb-1">Inspeksi</p>
                        <p class="text-white font-medium">Pemeliharaan Stasiun <?= $i ?></p>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <div class="mt-10 text-center md:hidden">
                <a href="#" class="inline-flex items-center justify-center px-6 py-3 border-2 border-darkblue text-darkblue font-bold rounded-lg w-full">
                    Lihat Semua Galeri
                </a>
            </div>

        </div>
    </section>

    <footer class="bg-darkblue pt-20 pb-10 border-t-4 border-brandyellow">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8 mb-16">
            
            <div class="lg:col-span-5">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-white rounded flex items-center justify-center p-1">
                        <span class="text-darkblue font-black text-xl">PU</span>
                    </div>
                    <span class="text-white font-bold tracking-widest text-xl"><?= $app_name ?></span>
                </div>
                <p class="text-slate-400 font-light text-sm leading-relaxed mb-8 max-w-sm">
                    Sistem Informasi Hidrologi dan Kualitas Air terpadu untuk monitoring dan pelaporan wilayah Sungai Mesuji Sekampung.
                </p>
                
                <h4 class="text-white font-semibold text-sm tracking-widest mb-4">IKUTI KAMI</h4>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-brandyellow hover:text-darkblue transition-all duration-300" title="Instagram">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-brandyellow hover:text-darkblue transition-all duration-300" title="Facebook">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-brandyellow hover:text-darkblue transition-all duration-300" title="X (Twitter)">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932 6.064-6.932zm-1.292 19.49h2.039L6.486 3.24H4.298l13.311 17.403z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-brandyellow hover:text-darkblue transition-all duration-300" title="TikTok">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.9-.32-1.98-.23-2.81.33-.85.51-1.44 1.43-1.58 2.41-.16 1.61.97 3.26 2.55 3.69 1.05.27 2.22.11 3.12-.49.76-.51 1.2-1.35 1.28-2.25.02-3.62-.01-7.24.01-10.86z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-brandyellow hover:text-darkblue transition-all duration-300" title="YouTube">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-3 text-slate-300 text-sm">
                <h4 class="text-white font-semibold tracking-widest mb-6 uppercase">Kontak</h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-brandyellow shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="leading-relaxed">
                            <strong class="text-white">Gedung BBWS Mesuji Sekampung</strong><br>
                            Jl. Gatot Subroto No.57, Garuntang, Kec. Bumi Waras, Kota Bandar Lampung, Lampung 35401
                        </span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brandyellow shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>bwss.mesujisekampung@pu.go.id</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brandyellow shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span>(0721) 474026</span>
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-4 flex flex-col gap-3">
                <div class="w-full h-64 rounded-xl overflow-hidden shadow-2xl border border-white/20 relative group">
                    <iframe 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        scrolling="no" 
                        marginheight="0" 
                        marginwidth="0" 
                        src="https://www.openstreetmap.org/export/embed.html?bbox=105.2830%2C-5.4385%2C105.2890%2C-5.4335&amp;layer=mapnik&amp;marker=-5.435936%2C105.286031" 
                        style="border: 0;">
                    </iframe>
                    
                    <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <a href="https://www.openstreetmap.org/?mlat=-5.43594&mlon=105.28603#map=18/-5.43594/105.28603" target="_blank" class="bg-brandyellow text-darkblue text-[11px] font-bold py-2 px-4 rounded-lg shadow-xl uppercase tracking-tighter hover:bg-white transition-colors">
                            Lihat Rute
                        </a>
                    </div>
                </div>
                <div class="flex justify-between items-center px-1">
                    <p class="text-[10px] text-slate-500 font-medium flex items-center gap-1.5 uppercase tracking-widest">
                        <span class="w-1.5 h-1.5 rounded-full bg-brandyellow"></span>
                        Lokasi Gedung BBWS Mesuji Sekampung
                    </p>
                    <span class="text-[9px] text-slate-600">OSM Contributors</span>
                </div>
            </div>
            
        </div>

        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center px-6 lg:px-12 max-w-7xl mx-auto text-xs text-slate-500 font-medium">
            <p>&copy; <?= date('Y') ?> Unit Hidrologi & Kualitas Air. Hak Cipta Dilindungi.</p>
            <p class="mt-2 md:mt-0">Dikembangkan untuk BBWS Mesuji Sekampung</p>
        </div>
    </footer>

</body>
</html>