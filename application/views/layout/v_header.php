<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($app_name) ? $app_name : 'Hydrosmart') ?> - <?= (isset($title) ? $title : 'BBWSMS') ?></title>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        #main-nav {
            position: fixed !important;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 24px);
            max-width: 1280px;
            z-index: 5000 !important;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        #main-nav.scrolled {
            top: 0;
            width: 100%;
            max-width: 100%;
        }
        
        #nav-inner {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }
        
        #nav-inner.scrolled-inner {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            background: rgba(255, 255, 255, 0.90) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .nav-link {
            position: relative;
            padding: 0.5rem 0;
            font-size: 13px;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #facc15;
            border-radius: 2px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link:hover::after { 
            width: 100%; 
        }

        .dropdown-menu {
            animation: fadeIn 0.2s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            #main-nav {
                top: 8px;
                width: calc(100% - 16px);
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            #main-nav {
                top: 10px;
                width: calc(100% - 20px);
            }
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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <?php 
        $current_page = ucfirst($this->uri->segment(1)); 
        $is_home = empty($current_page) || in_array($current_page, ['Dashboard', 'Beranda']);
        $is_data_active = in_array($current_page, ['CurahHujan', 'Tma']);
        $is_logged_in = $this->session->userdata('logged_in');
        $user_role = $this->session->userdata('role');
        $user_name = $this->session->userdata('nama_lengkap');
    ?>

    <div id="mobile-backdrop" class="mobile-menu-backdrop" onclick="closeMobileMenu()"></div>

    <nav id="main-nav">
        <div id="nav-inner" class="rounded-2xl border border-white/30 bg-white/40 shadow-lg shadow-black/5">
            <div class="px-4 md:px-5 lg:px-8 flex justify-between items-center h-12 md:h-14 lg:h-16">
                
                <a href="<?= base_url() ?>" class="flex items-center gap-2 md:gap-2.5 lg:gap-3 group shrink-0">
                    <img src="<?= base_url('assets/img/logobbwsmsbaru.png') ?>" 
                         alt="Logo" 
                         class="h-7 md:h-8 lg:h-10 w-auto transition-transform group-hover:scale-105">
                    <div class="hidden sm:block h-6 md:h-8 w-px bg-slate-200/40"></div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[7px] md:text-[8px] font-bold tracking-[0.1em] md:tracking-[0.15em] text-slate-400 uppercase leading-none">HydroSmart</span>
                        <span class="font-black text-xs md:text-sm lg:text-base tracking-tight text-darkblue leading-none group-hover:text-brandyellow transition-colors">
                            BBWS MESUJI SEKAMPUNG
                        </span>
                    </div>
                </a>
                
                <div class="hidden lg:flex items-center gap-4 lg:gap-6 text-[11px] lg:text-[13px] font-bold tracking-tight h-full">
                    
                    <a href="<?= base_url('Beranda') ?>" 
                       class="nav-link flex items-center transition-colors <?= $is_home ? 'text-brandyellow' : 'text-slate-700 hover:text-brandyellow' ?>">
                        Beranda
                    </a>
                    
                    <div class="relative group h-full flex items-center">
                        <button class="flex items-center gap-1 transition-colors <?= $is_data_active ? 'text-brandyellow' : 'text-slate-700 hover:text-brandyellow' ?>">
                            Data
                            <span class="text-[10px] transition-transform group-hover:rotate-180 inline-block">&#9660;</span>
                        </button>
                        <div class="absolute top-full left-0 w-44 bg-white/95 backdrop-blur-xl shadow-xl rounded-xl border border-white/50 
                                    opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-1 
                                    transition-all duration-300 z-50 overflow-hidden dropdown-menu">
                            <a href="<?= base_url('CurahHujan') ?>" 
                               class="block px-4 py-3 hover:bg-slate-50 text-sm 
                                      <?= ($current_page=='CurahHujan') ? 'bg-brandyellow/10 text-darkblue font-bold' : 'text-slate-700' ?>">
                                Curah Hujan
                            </a>
                            <a href="<?= base_url('Tma') ?>" 
                               class="block px-4 py-3 hover:bg-slate-50 text-sm 
                                      <?= ($current_page=='Tma') ? 'bg-brandyellow/10 text-darkblue font-bold' : 'text-slate-700' ?>">
                                Tinggi Muka Air
                            </a>
                        </div>
                    </div>

                    <?php if($is_logged_in): ?>
                    <div class="relative group h-full flex items-center ml-2">
                        <button class="flex items-center gap-2 text-slate-700 hover:text-brandyellow transition-colors">
                            <div class="w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-brandyellow flex items-center justify-center">
                                <span class="text-darkblue font-bold text-[9px] lg:text-[10px]">
                                    <?= strtoupper(substr($user_name, 0, 2)) ?>
                                </span>
                            </div>
                            <span class="hidden xl:inline text-xs text-slate-600 max-w-[80px] truncate"><?= $user_name ?></span>
                            <span class="text-[10px] transition-transform group-hover:rotate-180 inline-block">&#9660;</span>
                        </button>
                        <div class="absolute top-full right-0 w-48 bg-white/95 backdrop-blur-xl shadow-xl rounded-xl border border-white/50 
                                    opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-1 
                                    transition-all duration-300 z-50 overflow-hidden dropdown-menu">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-xs font-bold text-darkblue truncate"><?= $user_name ?></p>
                                <p class="text-[10px] text-slate-400"><?= $user_role == 'admin' ? 'Administrator' : 'Petugas Pos' ?></p>
                            </div>
                            <?php if($user_role == 'admin'): ?>
                            <a href="<?= base_url('admin') ?>" class="block px-4 py-2.5 hover:bg-slate-50 text-sm text-slate-700 border-b border-slate-100">
                                Panel Admin
                            </a>
                            <?php else: ?>
                            <a href="<?= base_url('petugas') ?>" class="block px-4 py-2.5 hover:bg-slate-50 text-sm text-slate-700 border-b border-slate-100">
                                Panel Petugas
                            </a>
                            <?php endif; ?>
                            <a href="<?= base_url('auth/logout') ?>" class="block px-4 py-2.5 hover:bg-red-50 text-sm text-red-600 font-medium">
                                Keluar
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="<?= base_url('Auth') ?>" 
                       class="px-4 lg:px-5 py-2 bg-darkblue text-white text-[10px] lg:text-xs rounded-xl 
                              hover:bg-brandyellow hover:text-darkblue transition-all shadow-md shadow-darkblue/15 ml-2">
                        Masuk
                    </a>
                    <?php endif; ?>
                </div>

                <div class="flex lg:hidden items-center gap-2">
                    <?php if($is_logged_in): ?>
                    <div class="w-8 h-8 rounded-lg bg-brandyellow flex items-center justify-center">
                        <span class="text-darkblue font-bold text-[10px]"><?= strtoupper(substr($user_name, 0, 2)) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <button id="mobile-menu-btn" class="text-darkblue p-2 hover:bg-slate-100 rounded-lg transition-colors" 
                            onclick="toggleMobileMenu()" aria-label="Toggle menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden mt-2 mx-1 bg-white/95 backdrop-blur-xl rounded-2xl border border-white/40 shadow-xl overflow-hidden">
            <div class="px-4 py-5 space-y-1">
                
                <?php if($is_logged_in): ?>
                <div class="flex items-center gap-3 px-3 py-3 mb-3 bg-slate-50 rounded-xl">
                    <div class="w-10 h-10 rounded-xl bg-brandyellow flex items-center justify-center">
                        <span class="text-darkblue font-bold text-sm"><?= strtoupper(substr($user_name, 0, 2)) ?></span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-darkblue"><?= $user_name ?></p>
                        <p class="text-xs text-slate-500"><?= $user_role == 'admin' ? 'Administrator' : 'Petugas Pos' ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <a href="<?= base_url('Beranda') ?>" 
                   class="block px-3 py-3 rounded-xl font-bold text-sm <?= $is_home ? 'bg-brandyellow/10 text-brandyellow' : 'text-darkblue hover:bg-slate-50' ?>">
                    Beranda
                </a>

                <div class="border-t border-slate-200 my-2"></div>
                <p class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Data Monitoring</p>
                
                <a href="<?= base_url('CurahHujan') ?>" 
                   class="block px-3 py-3 rounded-xl text-sm font-medium <?= ($current_page=='CurahHujan') ? 'bg-brandyellow/10 text-darkblue font-bold' : 'text-slate-700 hover:bg-slate-50' ?>">
                    Curah Hujan
                </a>
                
                <a href="<?= base_url('Tma') ?>" 
                   class="block px-3 py-3 rounded-xl text-sm font-medium <?= ($current_page=='Tma') ? 'bg-brandyellow/10 text-darkblue font-bold' : 'text-slate-700 hover:bg-slate-50' ?>">
                    Tinggi Muka Air
                </a>

                <div class="border-t border-slate-200 my-3"></div>
                
                <?php if($is_logged_in): ?>
                    <?php if($user_role == 'admin'): ?>
                    <a href="<?= base_url('admin') ?>" 
                       class="block px-3 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Panel Admin
                    </a>
                    <?php else: ?>
                    <a href="<?= base_url('petugas') ?>" 
                       class="block px-3 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Input Data Manual
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('auth/logout') ?>" 
                       class="block px-3 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 mt-1">
                        Keluar
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('Auth') ?>" 
                       class="block px-3 py-3 rounded-xl text-sm font-bold text-white bg-darkblue hover:bg-blue-900 transition-colors text-center">
                        Masuk ke Sistem
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="flex-grow relative">

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const backdrop = document.getElementById('mobile-backdrop');
            
            menu.classList.toggle('hidden');
            backdrop.classList.toggle('active');
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const backdrop = document.getElementById('mobile-backdrop');
            
            menu.classList.add('hidden');
            backdrop.classList.remove('active');
        }

        window.addEventListener('scroll', function() {
            const nav = document.getElementById('main-nav');
            const navInner = document.getElementById('nav-inner');
            
            if (window.scrollY > 30) {
                nav.classList.add('scrolled');
                navInner.classList.add('scrolled-inner');
            } else {
                nav.classList.remove('scrolled');
                navInner.classList.remove('scrolled-inner');
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