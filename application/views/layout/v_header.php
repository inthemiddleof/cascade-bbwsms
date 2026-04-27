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
        .dropdown-menu { display: none; }
        .group:hover .dropdown-menu { display: block; }
        nav { transition: all 0.3s ease-in-out; }
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
    // Inisialisasi variabel agar tidak error jika lupa dikirim dari Controller
    $app_name = isset($app_name) ? $app_name : 'CASCADE';
    
    if (!isset($current_page)) {
        $current_page = $this->uri->segment(2); 
    }

    $is_data_active = in_array($current_page, ['curah_hujan', 'tma', 'kualitas_air']);
?>

    <nav id="main-nav" class="bg-darkblue text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 flex justify-between items-center h-20">
            <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-white rounded flex items-center justify-center p-1 overflow-hidden">
                    <span class="text-darkblue font-black text-xl">PU</span> 
                </div>
                <div class="flex flex-col">
                    <span class="font-bold tracking-widest text-lg leading-tight group-hover:text-brandyellow transition-colors"><?= $app_name ?></span>
                    <span class="text-[10px] text-slate-300 tracking-wider uppercase">BBWS Mesuji Sekampung</span>
                </div>
            </a>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide h-full">
                <a href="<?= base_url() ?>" 
                   class="h-full flex items-center border-b-4 transition-all duration-300 <?= (!$current_page) ? 'text-brandyellow border-brandyellow' : 'text-white border-transparent hover:text-brandyellow' ?>">
                    Beranda
                </a>
                
                <div class="relative group h-full">
                    <button class="h-full flex items-center gap-1 border-b-4 transition-all duration-300 <?= ($is_data_active) ? 'text-brandyellow border-brandyellow' : 'text-white border-transparent hover:text-brandyellow' ?>">
                        Data <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute top-full left-0 w-48 bg-white text-slate-800 shadow-xl rounded-b-lg overflow-hidden border border-slate-100">
                        <a href="<?= base_url('welcome/curah_hujan') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition <?= ($current_page == 'curah_hujan') ? 'bg-slate-100 text-darkblue font-bold' : '' ?>">Curah Hujan</a>
                        <a href="<?= base_url('welcome/tma') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition <?= ($current_page == 'tma') ? 'bg-slate-100 text-darkblue font-bold' : '' ?>">Tinggi Muka Air</a>
                        <a href="<?= base_url('welcome/kualitas_air') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition <?= ($current_page == 'kualitas_air') ? 'bg-slate-100 text-darkblue font-bold' : '' ?>">Kualitas Air</a>
                    </div>
                </div>

                <div class="relative group h-full">
                    <button class="h-full flex items-center gap-1 border-b-4 transition-all duration-300 <?= ($current_page == 'peta') ? 'text-brandyellow border-brandyellow' : 'text-white border-transparent hover:text-brandyellow' ?>">
                        Peta <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute top-full left-0 w-56 bg-white text-slate-800 shadow-2xl rounded-b-xl border border-slate-100 overflow-hidden">
                        <div class="p-2 flex flex-col gap-1">
                            <a href="<?= base_url('welcome/peta') ?>" 
                               class="block px-4 py-3 rounded-lg hover:bg-slate-100 hover:text-darkblue transition <?= ($current_page == 'peta') ? 'bg-slate-50 text-darkblue font-bold' : '' ?>">
                               Sebaran Stasiun
                            </a>
                            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-100 hover:text-darkblue transition">
                               Peta Rawan Banjir
                            </a>
                        </div>
                    </div>
                </div>

                <a href="#" class="h-full flex items-center border-b-4 border-transparent hover:text-brandyellow transition-colors">Laporan</a>
            </div>

            <button class="md:hidden text-white hover:text-brandyellow">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </nav>

    <script>
        window.onscroll = function() {
            const nav = document.getElementById('main-nav');
            const container = nav.querySelector('.max-w-7xl');
            if (window.pageYOffset > 50) {
                nav.classList.remove('bg-darkblue');
                nav.classList.add('bg-darkblue/80', 'backdrop-blur-md');
                container.classList.replace('h-20', 'h-16');
            } else {
                nav.classList.add('bg-darkblue');
                nav.classList.remove('bg-darkblue/80', 'backdrop-blur-md');
                container.classList.replace('h-16', 'h-20');
            }
        };
    </script>