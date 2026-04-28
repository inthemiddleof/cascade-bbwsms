<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
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
    <style> 
        body { font-family: 'Inter', sans-serif; }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .dropdown-menu { z-index: 9999 !important; }
        #map { z-index: 1 !important; }
        .nav-transition {
            transition: background-color 0.4s ease, backdrop-filter 0.3s ease, box-shadow 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased selection:bg-brandyellow selection:text-darkblue">

    <?php 
        $current_page = $this->uri->segment(2); 
        $is_home = empty($current_page) || $current_page === 'index';
        $is_data_active = in_array($current_page, ['curah_hujan', 'tma', 'kualitas_air']);
    ?>

    <!-- FLOATING NAVBAR -->
    <nav id="main-nav" class="fixed top-4 left-0 right-0 z-50 nav-transition">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
      <div id="nav-inner" class="bg-white rounded-2xl shadow-lg nav-transition" style="border: 1px solid #facc15; box-shadow: 0 0 12px rgba(250, 204, 21, 0.4), 0 4px 24px rgba(0,0,0,0.08);">
                <div id="nav-container" class="px-6 lg:px-8 flex justify-between items-center h-16 lg:h-20 transition-all duration-300 ease-in-out">
                    
                    <!-- LOGO / BRAND -->
                    <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                        <!-- Logo gambar, hapus kotak PU & subtitle -->
                        <img src="<?= base_url('assets/img/logobbwsms.png') ?>"
                             alt="Logo BBWS Mesuji Sekampung"
                             class="h-14 w-auto object-contain transition-all duration-300">
                        <span id="app-name" class="font-bold tracking-widest text-lg leading-tight text-darkblue group-hover:text-brandyellow transition-colors duration-300">
                            <?= $app_name ?>
                        </span>
                    </a>
                    
                    <!-- DESKTOP MENU -->
                    <div class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide h-full">
                        <a href="<?= base_url() ?>" class="nav-link relative h-full flex items-center transition-colors duration-300 <?= $is_home ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                            <span>Beranda</span>
                        </a>
                        
                        <div class="relative group h-full">
                            <button class="nav-link relative h-full flex items-center gap-1 transition-colors duration-300 <?= $is_data_active ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                                Data 
                                <svg class="nav-icon w-4 h-4 -rotate-90 transition-transform duration-300 group-hover:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute top-full left-0 w-52 bg-white text-slate-800 shadow-xl rounded-xl overflow-hidden border border-gray-100 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 ease-out z-50">
                                <a href="<?= base_url('index.php/welcome/curah_hujan') ?>" class="block px-5 py-3.5 hover:bg-blue-50 hover:text-darkblue transition-colors border-b border-gray-50 <?= ($current_page == 'curah_hujan') ? 'bg-blue-50/50 text-darkblue font-black' : '' ?>">Curah Hujan</a>
                                <a href="<?= base_url('index.php/welcome/tma') ?>" class="block px-5 py-3.5 hover:bg-blue-50 hover:text-darkblue transition-colors <?= ($current_page == 'tma') ? 'bg-blue-50/50 text-darkblue font-black' : '' ?>">Tinggi Muka Air</a>
                            </div>
                        </div>

                        <div class="relative group h-full">
                            <button class="nav-link relative h-full flex items-center gap-1 transition-colors duration-300 <?= ($current_page == 'peta') ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                                Peta 
                                <svg class="nav-icon w-4 h-4 -rotate-90 transition-transform duration-300 group-hover:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute top-full left-0 w-52 bg-white text-slate-800 shadow-xl rounded-xl overflow-hidden border border-gray-100 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 ease-out z-50">
                                <a href="<?= base_url('index.php/welcome/peta') ?>" class="block px-5 py-3.5 hover:bg-blue-50 hover:text-darkblue transition-colors border-b border-gray-50">Sebaran Stasiun</a>
                            </div>
                        </div>
                    </div>

                    <!-- MOBILE MENU BUTTON -->
                    <button id="mobile-menu-btn" class="md:hidden text-darkblue hover:text-brandyellow transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="icon-menu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path id="icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- MOBILE MENU -->
                <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100">
                    <div class="px-6 py-4 space-y-4">
                        <a href="<?= base_url() ?>" class="mobile-nav-link block font-semibold <?= $is_home ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">Beranda</a>
                        
                        <div>
                            <p class="font-semibold text-gray-400 mb-2 uppercase text-xs tracking-widest">Data</p>
                            <div class="flex flex-col gap-3 pl-3 border-l border-gray-200">
                                <a href="<?= base_url('index.php/welcome/curah_hujan') ?>" class="mobile-nav-link <?= ($current_page == 'curah_hujan') ? 'text-brandyellow font-semibold' : 'text-darkblue hover:text-brandyellow' ?>">Curah Hujan</a>
                                <a href="<?= base_url('index.php/welcome/tma') ?>" class="mobile-nav-link <?= ($current_page == 'tma') ? 'text-brandyellow font-semibold' : 'text-darkblue hover:text-brandyellow' ?>">Tinggi Muka Air</a>
                            </div>
                        </div>

                        <div>
                            <p class="font-semibold text-gray-400 mb-2 uppercase text-xs tracking-widest">Peta</p>
                            <div class="flex flex-col gap-3 pl-3 border-l border-gray-200">
                                <a href="<?= base_url('index.php/welcome/peta') ?>" class="mobile-nav-link text-darkblue hover:text-brandyellow">Sebaran Stasiun</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
       window.addEventListener('scroll', function() {
    const navInner = document.getElementById('nav-inner');
    const navContainer = document.getElementById('nav-container');
    
    if (window.scrollY > 20) {
        navInner.classList.remove('rounded-2xl', 'bg-white', 'border-yellow-300');
        navInner.classList.add('rounded-xl', 'shadow-md', 'bg-white/80', 'backdrop-blur-md');
        navContainer.classList.remove('h-16', 'lg:h-20');
        navContainer.classList.add('h-14', 'lg:h-16');
    } else {
        navInner.classList.add('rounded-2xl', 'bg-white');
        navInner.classList.remove('rounded-xl', 'shadow-md', 'bg-white/80', 'backdrop-blur-md');
        navContainer.classList.add('h-16', 'lg:h-20');
        navContainer.classList.remove('h-14', 'lg:h-16');
    }
});

        // --- 2. White Theme & Shadow saat scroll ---
        document.addEventListener('DOMContentLoaded', function() {
            const navInner = document.getElementById('nav-inner');

            function ensureWhiteTheme() {
                navInner.classList.remove('bg-darkblue/70', 'bg-darkblue/80', 'bg-darkblue/90', 'bg-darkblue');
                navInner.classList.add('bg-white');
                navInner.classList.remove('border-white/20', 'border-darkblue/20');
                navInner.classList.add('border-white/40');
            }
            ensureWhiteTheme();

            window.addEventListener('scroll', function() {
                if (window.scrollY > 20) {
                    navInner.classList.add('shadow-xl');
                    navInner.classList.remove('shadow-lg');
                } else {
                    navInner.classList.add('shadow-lg');
                    navInner.classList.remove('shadow-xl');
                }
            });
        });

        // --- 3. Hamburger Mobile Menu ---
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