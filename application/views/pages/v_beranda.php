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