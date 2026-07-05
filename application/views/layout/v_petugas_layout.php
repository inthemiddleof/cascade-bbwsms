<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel Petugas Lapangan - HydroSmart BBWS Mesuji Sekampung - Sistem Informasi Hidrologi untuk monitoring curah hujan dan tinggi muka air">
    <meta name="keywords" content="hidrologi, bendungan, bendung, curah hujan, TMA, input data, petugas, BBWS, Mesuji, Sekampung, Lampung">
    <meta name="author" content="BBWS Mesuji Sekampung">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#000080">
    <title><?= (isset($app_name) ? $app_name : 'HydroSmart') ?> - <?= (isset($title) ? $title : 'Petugas') ?></title>    
    <meta property="og:title" content="HydroSmart - Panel Petugas Lapangan">
    <meta property="og:description" content="Sistem Informasi Hidrologi BBWS Mesuji Sekampung">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= base_url('petugas') ?>">
    <meta property="og:site_name" content="HydroSmart BBWS Mesuji Sekampung">
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "HydroSmart - Panel Petugas Lapangan",
        "url": "<?= base_url('petugas') ?>",
        "description": "Panel input data manual untuk petugas lapangan pada Sistem Informasi Hidrologi BBWS Mesuji Sekampung",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "author": {
            "@type": "GovernmentOrganization",
            "name": "BBWS Mesuji Sekampung",
            "description": "Balai Besar Wilayah Sungai Mesuji Sekampung - Kementerian PUPR"
        }
    }
    </script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        darkblue: '#000080',
                        brandyellow: '#facc15'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        input, select, textarea { font-size: 16px !important; }
        
        .sr-only {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
        }
        
        .skip-link {
            position: absolute; top: -100px; left: 0; background: #000080; color: #fff;
            padding: 8px 16px; z-index: 10000; transition: top 0.3s;
        }
        .skip-link:focus { top: 0; }
        
        #mobile-nav {
            position: fixed !important;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 24px);
            max-width: 1280px;
            z-index: 5000 !important;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        #mobile-nav.scrolled {
            top: 0;
            width: 100%;
            max-width: 100%;
        }
        
        #mobile-nav-inner {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }
        
        #mobile-nav-inner.scrolled-inner {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .mobile-menu-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 4999;
            display: none;
        }
        
        .mobile-menu-backdrop.active {
            display: block;
        }

        @media (max-width: 640px) {
            #mobile-nav {
                top: 8px;
                width: calc(100% - 16px);
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            #mobile-nav {
                top: 10px;
                width: calc(100% - 20px);
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>

    <?php 
        $current_url = $this->uri->uri_string();
        $petugas_name = $this->session->userdata('nama_lengkap');
    ?>

    <div id="mobile-backdrop" class="mobile-menu-backdrop" role="presentation" aria-hidden="true" onclick="closeMobileMenu()"></div>

    <nav id="mobile-nav" class="lg:hidden" role="navigation" aria-label="Navigasi Utama Mobile">
        <div id="mobile-nav-inner" class="rounded-2xl border border-white/30 bg-white/40 shadow-lg shadow-black/5">
            <div class="px-4 flex justify-between items-center h-12">
                
                <a href="<?= base_url('petugas/input') ?>" class="flex items-center gap-2 shrink-0" aria-label="HydroSmart - Beranda Petugas">
                    <img src="<?= base_url('assets/img/logobbwsmsbaru.png') ?>" alt="Logo BBWS Mesuji Sekampung - HydroSmart" class="h-7 w-auto" loading="lazy">
                    <div class="hidden sm:block h-6 w-px bg-slate-200/40" aria-hidden="true"></div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[7px] font-bold tracking-[0.1em] text-slate-400 uppercase leading-none">HydroSmart</span>
                        <span class="font-black text-xs tracking-tight text-darkblue leading-none">PANEL PETUGAS</span>
                    </div>
                </a>
                
                <button id="mobile-menu-btn" class="text-darkblue p-2 hover:bg-slate-100 rounded-lg transition-colors" 
                        onclick="toggleMobileMenu()" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobile-menu" type="button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden mt-2 mx-1 bg-white/95 backdrop-blur-xl rounded-2xl border border-white/40 shadow-xl overflow-hidden" role="menu" aria-label="Menu Navigasi">
            <ul class="px-4 py-5 space-y-1">
                
                <li class="flex items-center gap-3 px-3 py-3 mb-3 bg-slate-50 rounded-xl" role="presentation">
                    <div class="w-10 h-10 rounded-xl bg-darkblue flex items-center justify-center" aria-hidden="true">
                        <span class="text-white font-bold text-sm"><?= strtoupper(substr($petugas_name, 0, 2)) ?></span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-darkblue"><?= htmlspecialchars($petugas_name) ?></p>
                        <p class="text-xs text-slate-500">Petugas Lapangan</p>
                    </div>
                </li>

                <li role="none">
                    <a href="<?= base_url('petugas/input') ?>" 
                       class="block px-3 py-3 rounded-xl font-bold text-sm <?= (strpos($current_url, 'petugas/input') !== false) ? 'bg-darkblue text-white' : 'text-darkblue hover:bg-slate-50' ?>"
                       role="menuitem"
                       <?= (strpos($current_url, 'petugas/input') !== false) ? 'aria-current="page"' : '' ?>>
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                            Input Laporan
                        </span>
                    </a>
                </li>
                
                <li role="none">
                    <a href="<?= base_url('petugas/kelola') ?>" 
                       class="block px-3 py-3 rounded-xl font-bold text-sm <?= (strpos($current_url, 'petugas/kelola') !== false) ? 'bg-darkblue text-white' : 'text-darkblue hover:bg-slate-50' ?>"
                       role="menuitem"
                       <?= (strpos($current_url, 'petugas/kelola') !== false) ? 'aria-current="page"' : '' ?>>
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Riwayat Data
                        </span>
                    </a>
                </li>

                <li class="border-t border-slate-200 my-3" role="separator" aria-hidden="true"></li>
                <li role="none">
                    <a href="<?= base_url('auth/logout') ?>" 
                       class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50"
                       role="menuitem">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                        Keluar
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <aside id="sidebar" class="hidden lg:flex fixed top-0 left-0 z-50 h-full w-72 bg-white border-r border-slate-200 flex-col shadow-sm" role="complementary" aria-label="Sidebar Navigasi">
        
        <header class="px-5 py-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <img src="<?= base_url('assets/img/logobbwsmsbaru.png') ?>" alt="Logo BBWS Mesuji Sekampung - HydroSmart" class="h-9 w-auto flex-shrink-0" loading="lazy">
                <div>
                    <h1 class="font-black text-darkblue text-sm leading-tight">HydroSmart</h1>
                    <p class="text-[10px] text-slate-400 font-medium">BBWS Mesuji Sekampung</p>
                </div>
            </div>
        </header>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1" aria-label="Menu Petugas">
            <h2 class="px-3 mb-2 text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">Menu Petugas</h2>
            
            <ul class="space-y-1">
                <li>
                    <a href="<?= base_url('petugas/input') ?>" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
                              <?= (strpos($current_url, 'petugas/input') !== false) 
                                  ? 'bg-darkblue text-white font-bold shadow-md shadow-darkblue/10' 
                                  : 'text-slate-600 hover:bg-slate-50 hover:text-darkblue' ?>"
                       <?= (strpos($current_url, 'petugas/input') !== false) ? 'aria-current="page"' : '' ?>>
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Input Laporan</span>
                    </a>
                </li>
                
                <li>
                    <a href="<?= base_url('petugas/kelola') ?>" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
                              <?= (strpos($current_url, 'petugas/kelola') !== false) 
                                  ? 'bg-darkblue text-white font-bold shadow-md shadow-darkblue/10' 
                                  : 'text-slate-600 hover:bg-slate-50 hover:text-darkblue' ?>"
                       <?= (strpos($current_url, 'petugas/kelola') !== false) ? 'aria-current="page"' : '' ?>>
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Riwayat Data</span>
                    </a>
                </li>
            </ul>
        </nav>

        <section class="px-5 py-4 border-b border-slate-100" aria-label="Informasi Pengguna">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-darkblue flex items-center justify-center flex-shrink-0 shadow-sm" aria-hidden="true">
                    <span class="text-white font-bold text-sm"><?= strtoupper(substr($petugas_name, 0, 2)) ?></span>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-darkblue text-sm truncate"><?= htmlspecialchars($petugas_name) ?></p>
                    <p class="text-[10px] text-slate-400 font-medium">Petugas Lapangan</p>
                </div>
            </div>
        </section>

        <footer class="p-4 border-t border-slate-100">
            <a href="<?= base_url('auth/logout') ?>" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition-all"
               aria-label="Keluar dari aplikasi">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                <span>Keluar</span>
            </a>
        </footer>
    </aside>

    <div class="lg:ml-72 min-h-screen">
        <main id="main-content" class="p-4 lg:p-6 pt-20 lg:pt-6" role="main" aria-label="Konten Utama">
            <h1 class="sr-only">Panel Petugas Lapangan - <?= isset($title) ? $title : 'HydroSmart' ?></h1>
            <?= $content ?? '' ?>
        </main>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const backdrop = document.getElementById('mobile-backdrop');
            const menuBtn = document.getElementById('mobile-menu-btn');
            
            const isOpen = !menu.classList.contains('hidden');
            
            if (isOpen) {
                menu.classList.add('hidden');
                backdrop.classList.remove('active');
                menuBtn.setAttribute('aria-expanded', 'false');
            } else {
                menu.classList.remove('hidden');
                backdrop.classList.add('active');
                menuBtn.setAttribute('aria-expanded', 'true');
            }
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const backdrop = document.getElementById('mobile-backdrop');
            const menuBtn = document.getElementById('mobile-menu-btn');
            
            menu.classList.add('hidden');
            backdrop.classList.remove('active');
            menuBtn.setAttribute('aria-expanded', 'false');
        }

        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mobile-nav');
            const navInner = document.getElementById('mobile-nav-inner');
            
            if (nav && navInner) {
                if (window.scrollY > 30) {
                    nav.classList.add('scrolled');
                    navInner.classList.add('scrolled-inner');
                } else {
                    nav.classList.remove('scrolled');
                    navInner.classList.remove('scrolled-inner');
                }
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeMobileMenu();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });
    </script>
</body>
</html>