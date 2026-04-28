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
    <style> 
        body { font-family: 'Inter', sans-serif; }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .dropdown-menu {
            z-index: 9999 !important; 
        }
        
        .leaflet-control-container {
            z-index: 10 !important;
        }

        #map { 
        z-index: 1 !important;
        }

        #main-nav {
            position: relative;
            z-index: 1100 !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-brandyellow selection:text-darkblue overflow-hidden h-screen flex flex-col">

    <?php 
        $current_page = $this->uri->segment(2); 
        $is_home = empty($current_page) || $current_page === 'index';
        $is_data_active = in_array($current_page, ['curah_hujan', 'tma', 'kualitas_air']);
    ?>

    <nav id="main-nav" class="relative w-full bg-white z-[1100] border-b-2 border-brandyellow shadow-md">
        <div class="max-w-full mx-auto px-4 lg:px-8">
            <div id="nav-container" class="flex justify-between items-center h-16 lg:h-20">
                
                <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                    <img src="<?= base_url('assets/img/logobbwsms.png') ?>" alt="Logo" class="h-10 lg:h-12 w-auto object-contain">
                    <span class="font-bold tracking-widest text-sm lg:text-lg leading-tight text-darkblue group-hover:text-brandyellow transition-colors duration-300 uppercase">
                        <?= $app_name ?>
                    </span>
                </a>
                
                <div class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide h-full">
                    <a href="<?= base_url() ?>" class="nav-link h-full flex items-center transition-colors duration-300 <?= $is_home ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                        <span>Beranda</span>
                    </a>
                    
                    <div class="relative group h-full flex items-center">
                        <button class="flex items-center gap-1 transition-colors duration-300 <?= $is_data_active ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                            Data 
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute top-full left-0 w-52 bg-white shadow-2xl rounded-b-xl border border-gray-100 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 z-[10000]">
                            <a href="<?= base_url('index.php/welcome/curah_hujan') ?>" class="block px-5 py-4 hover:bg-blue-50 hover:text-darkblue transition-colors border-b border-gray-50">Curah Hujan</a>
                            <a href="<?= base_url('index.php/welcome/tma') ?>" class="block px-5 py-4 hover:bg-blue-50 hover:text-darkblue transition-colors">Tinggi Muka Air</a>
                        </div>
                    </div>

                    <div class="relative group h-full flex items-center">
                        <button class="flex items-center gap-1 transition-colors duration-300 <?= ($current_page == 'peta') ? 'text-brandyellow' : 'text-darkblue hover:text-brandyellow' ?>">
                            Peta 
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute top-full left-0 w-52 bg-white shadow-2xl rounded-b-xl border border-gray-100 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 z-[10000]">
                            <a href="<?= base_url('index.php/welcome/peta') ?>" class="block px-5 py-4 hover:bg-blue-50 hover:text-darkblue transition-colors">Sebaran Stasiun</a>
                        </div>
                    </div>
                </div>

                <button id="mobile-menu-btn" class="md:hidden text-darkblue hover:text-brandyellow transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="icon-menu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path id="icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 pb-4">
                <div class="px-2 pt-4 space-y-4">
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
                            <a href="<?= base_url('index.php/welcome/peta') ?>" class="mobile-nav-link <?= ($current_page == 'peta') ? 'text-brandyellow font-semibold' : 'text-darkblue hover:text-brandyellow' ?>">Sebaran Stasiun</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
        // Hapus efek scroll karena pada halaman peta (fullscreen), layar tidak discroll.
        // Cukup biarkan script hamburger menu saja.
        
        // --- Hamburger Mobile Menu ---
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