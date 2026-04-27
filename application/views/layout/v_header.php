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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-brandyellow selection:text-darkblue">

    <?php 
        // Logika untuk mendeteksi halaman aktif
        $current_page = $this->uri->segment(2); 
        $is_home = empty($current_page) || $current_page === 'index';
        $is_data_active = in_array($current_page, ['curah_hujan', 'tma', 'kualitas_air']);
    ?>

    <nav id="main-nav" class="bg-darkblue text-white sticky top-0 z-50 transition-all duration-300 ease-in-out border-b border-transparent">
        <div id="nav-container" class="max-w-7xl mx-auto px-6 lg:px-12 flex justify-between items-center h-20 transition-all duration-300 ease-in-out">
            
            <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-white rounded flex items-center justify-center p-1 overflow-hidden shadow-sm">
                    <span class="text-darkblue font-black text-xl">PU</span> 
                </div>
                <div class="flex flex-col">
                    <span class="font-bold tracking-widest text-lg leading-tight group-hover:text-brandyellow transition-colors"><?= $app_name ?></span>
                    <span class="text-[10px] text-slate-300 tracking-wider uppercase">BBWS Mesuji Sekampung</span>
                </div>
            </a>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide h-full">
                <a href="<?= base_url() ?>" class="relative h-full flex items-center transition-colors group <?= $is_home ? 'text-brandyellow' : 'hover:text-brandyellow' ?>">
                    <span>Beranda</span>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-brandyellow rounded-t-md transition-transform duration-300 origin-left <?= $is_home ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' ?>"></div>
                </a>
                
                <div class="relative group h-full">
                    <button class="relative h-full flex items-center gap-1 transition-colors <?= $is_data_active ? 'text-brandyellow' : 'hover:text-brandyellow' ?>">
                        Data 
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-brandyellow rounded-t-md transition-transform duration-300 origin-left <?= $is_data_active ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' ?>"></div>
                    </button>
                    <div class="absolute top-full left-0 w-52 bg-white text-slate-800 shadow-xl rounded-b-xl overflow-hidden border border-slate-100 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 ease-out">
                        <a href="<?= base_url('index.php/welcome/curah_hujan') ?>" class="block px-5 py-3.5 hover:bg-blue-50 hover:text-darkblue transition-colors border-b border-slate-50 <?= ($current_page == 'curah_hujan') ? 'bg-blue-50/50 text-darkblue font-black' : '' ?>">Curah Hujan</a>
                        <a href="<?= base_url('index.php/welcome/tma') ?>" class="block px-5 py-3.5 hover:bg-blue-50 hover:text-darkblue transition-colors <?= ($current_page == 'tma') ? 'bg-blue-50/50 text-darkblue font-black' : '' ?>">Tinggi Muka Air</a>
                    </div>
                </div>

                <div class="relative group h-full">
                    <button class="relative h-full flex items-center gap-1 hover:text-brandyellow transition-colors">
                        Peta 
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-brandyellow rounded-t-md transition-transform duration-300 origin-left scale-x-0 group-hover:scale-x-100"></div>
                    </button>
                    <div class="absolute top-full left-0 w-52 bg-white text-slate-800 shadow-xl rounded-b-xl overflow-hidden border border-slate-100 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 ease-out">
                        <a href="<?= base_url('index.php/welcome/peta') ?>" class="block px-5 py-3.5 hover:bg-blue-50 hover:text-darkblue transition-colors border-b border-slate-50">Sebaran Stasiun</a>
                    </div>
                </div>

                <!-- <a href="#" class="relative h-full flex items-center transition-colors group hover:text-brandyellow">
                    <span>Laporan</span>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-brandyellow rounded-t-md transition-transform duration-300 origin-left scale-x-0 group-hover:scale-x-100"></div>
                </a> -->
            </div>

            <button id="mobile-menu-btn" class="md:hidden text-white hover:text-brandyellow transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="icon-menu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path id="icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-darkblue border-t border-white/10 absolute top-full left-0 w-full shadow-lg">
            <div class="px-6 py-4 space-y-4">
                <a href="<?= base_url() ?>" class="block font-semibold <?= $is_home ? 'text-brandyellow' : 'text-white hover:text-brandyellow' ?>">Beranda</a>
                
                <div>
                    <p class="font-semibold text-slate-400 mb-2 uppercase text-xs tracking-widest">Data</p>
                    <div class="flex flex-col gap-3 pl-3 border-l border-white/20">
                        <a href="<?= base_url('index.php/welcome/curah_hujan') ?>" class="<?= ($current_page == 'curah_hujan') ? 'text-brandyellow font-semibold' : 'text-slate-200 hover:text-white' ?>">Curah Hujan</a>
                        <a href="<?= base_url('index.php/welcome/tma') ?>" class="<?= ($current_page == 'tma') ? 'text-brandyellow font-semibold' : 'text-slate-200 hover:text-white' ?>">Tinggi Muka Air</a>
                    </div>
                </div>

                <div>
                    <p class="font-semibold text-slate-400 mb-2 uppercase text-xs tracking-widest">Peta</p>
                    <div class="flex flex-col gap-3 pl-3 border-l border-white/20">
                        <a href="<?= base_url('index.php/welcome/peta') ?>" class="text-slate-200 hover:text-white">Sebaran Stasiun</a>
                    </div>
                </div>

                <!-- <a href="#" class="block font-semibold text-white hover:text-brandyellow">Laporan</a> -->
            </div>
        </div>
    </nav>

    <script>
        // --- 1. Fungsi Scroll Navbar (Glassmorphism) ---
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('main-nav');
            const navContainer = document.getElementById('nav-container');
            
            if (window.scrollY > 20) {
                nav.classList.remove('bg-darkblue');
                nav.classList.add('bg-darkblue/75', 'backdrop-blur-md', 'border-b', 'border-white/10', 'shadow-lg');
                navContainer.classList.remove('h-20');
                navContainer.classList.add('h-16'); 
            } else {
                nav.classList.add('bg-darkblue');
                nav.classList.remove('bg-darkblue/75', 'backdrop-blur-md', 'border-b', 'border-white/10', 'shadow-lg');
                navContainer.classList.add('h-20');
                navContainer.classList.remove('h-16');
            }
        });

        // --- 2. Fungsi Tombol Hamburger (Mobile Menu) ---
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const iconMenu = document.getElementById('icon-menu');
        const iconClose = document.getElementById('icon-close');

        btn.addEventListener('click', () => {
            // Toggle container menu
            menu.classList.toggle('hidden');
            
            // Mengubah Ikon Garis 3 (Hamburger) menjadi Ikon Silang (X)
            iconMenu.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
            
            // Opsional: berikan background solid pada navbar saat menu terbuka walau sedang scroll
            const nav = document.getElementById('main-nav');
            if (!menu.classList.contains('hidden')) {
                nav.classList.add('bg-darkblue');
                nav.classList.remove('bg-darkblue/75', 'backdrop-blur-md');
            } else if (window.scrollY > 20) {
                nav.classList.remove('bg-darkblue');
                nav.classList.add('bg-darkblue/75', 'backdrop-blur-md');
            }
        });
    </script>