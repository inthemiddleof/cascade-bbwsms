<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($app_name) ? $app_name : 'CASCADE') ?> - <?= (isset($title) ? $title : 'BBWS MS') ?></title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        #main-nav {
            position: sticky !important;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 5000 !important;
        }

        .nav-link {
            position: relative;
            padding: 0.5rem 0;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #facc15;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased h-screen flex flex-col overflow-x-hidden">

    <?php 
        $current_page = ucfirst($this->uri->segment(1));         
        $is_home = empty($current_page) || in_array($current_page, ['Dashboard', 'Beranda']);
        $is_data_active = in_array($current_page, ['CurahHujan', 'Tma']);
    ?>

    <nav id="main-nav" class="bg-white border-b border-brandyellow shadow-sm">
        <div class="max-w-full mx-auto px-4 lg:px-6">
            <div class="flex justify-between items-center h-14 lg:h-16">
                
                <a href="<?= base_url() ?>" class="flex items-center gap-2 md:gap-3 group">
                    <img src="<?= base_url('assets/img/logobbwsmsbaru.png') ?>"
                         alt="Logo BBWS MS"
                         class="h-8 md:h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                    
                    <div class="hidden sm:block h-8 w-px bg-gray-200"></div>
                    
                    <div class="flex flex-col justify-center">
                        <span class="text-[8px] font-bold tracking-[0.15em] text-slate-400 uppercase leading-none">
                            HydroSmart
                        </span>
                        <span class="font-black text-sm md:text-base tracking-tight text-darkblue leading-none group-hover:text-brandyellow transition-colors">
                            BBWS MESUJI SEKAMPUNG
                        </span>
                    </div>
                </a>
                
                <div class="hidden md:flex items-center gap-6 text-[13px] font-bold tracking-tight h-full">
                    <a href="<?= base_url('Dashboard') ?>" class="nav-link flex items-center transition-colors <?= $is_home ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                        Beranda
                    </a>
                    
                    <div class="relative group h-full flex items-center">
                        <button class="flex items-center gap-1 transition-colors <?= $is_data_active ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                            Data
                            <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                        <div class="absolute top-[90%] left-0 w-44 bg-white shadow-xl rounded-b-lg border border-gray-100 opacity-0 invisible translate-y-1 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200">
                            <a href="<?= base_url('CurahHujan') ?>" class="block px-4 py-3 hover:bg-gray-50 hover:text-darkblue border-b border-gray-50 <?= ($current_page == 'CurahHujan') ? 'bg-gray-50 text-darkblue' : '' ?>">Curah Hujan</a>
                            <a href="<?= base_url('Tma') ?>" class="block px-4 py-3 hover:bg-gray-50 hover:text-darkblue <?= ($current_page == 'Tma') ? 'bg-gray-50 text-darkblue' : '' ?>">Tinggi Muka Air</a>
                        </div>
                    </div>

                    <a href="<?= base_url('Peta') ?>" class="nav-link flex items-center transition-colors <?= ($current_page == 'Peta') ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                        Peta
                    </a>

                    <a href="<?= base_url('Login') ?>" class="px-4 py-1.5 bg-darkblue text-white text-xs rounded-lg hover:bg-brandyellow hover:text-darkblue transition-all duration-300">
                        Login
                    </a>
                </div>

                <button id="mobile-menu-btn" class="md:hidden text-darkblue focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="icon-menu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path id="icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-inner">
            <div class="px-4 py-4 space-y-3 text-sm">
                <a href="<?= base_url('Dashboard') ?>" class="block font-bold <?= $is_home ? 'text-brandyellow' : 'text-darkblue' ?>">Beranda</a>
                <a href="<?= base_url('CurahHujan') ?>" class="block pl-2 text-darkblue">Curah Hujan</a>
                <a href="<?= base_url('Tma') ?>" class="block pl-2 text-darkblue">Tinggi Muka Air</a>
                <a href="<?= base_url('Peta') ?>" class="block font-bold text-darkblue">Peta</a>
                <a href="<?= base_url('Login') ?>" class="block w-full py-2 text-center bg-darkblue text-white rounded-md font-bold">Login</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow relative overflow-y-auto">

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const iconMenu = document.getElementById('icon-menu');
        const iconClose = document.getElementById('icon-close');

        if (btn) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
                iconMenu.classList.toggle('hidden');
                iconClose.classList.toggle('hidden');
            });
        }
    </script>