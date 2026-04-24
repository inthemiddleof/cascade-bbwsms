<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $app_name ?> - <?= $title ?></title>
    
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
        
        /* Smooth transition untuk efek scroll */
        nav { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-brandyellow selection:text-darkblue">

    <?php 
        // Logika untuk mendeteksi halaman aktif
        $current_page = $this->uri->segment(2); // mengambil 'curah_hujan' atau 'tma' dari URL
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
                   class="h-full flex items-center transition-colors <?= (!$current_page) ? 'text-brandyellow border-b-4 border-brandyellow' : 'hover:text-brandyellow' ?>">
                   Beranda
                </a>
                
                <div class="relative group h-full">
                    <button class="h-full flex items-center gap-1 transition-colors <?= ($is_data_active) ? 'text-brandyellow border-b-4 border-brandyellow' : 'hover:text-brandyellow' ?>">
                        Data <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute top-full left-0 w-48 bg-white text-slate-800 shadow-xl rounded-b-lg overflow-hidden border border-slate-100">
                        <a href="<?= base_url('welcome/curah_hujan') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition <?= ($current_page == 'curah_hujan') ? 'bg-slate-100 text-darkblue font-bold' : '' ?>">Curah Hujan</a>
                        <a href="<?= base_url('welcome/tma') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition <?= ($current_page == 'tma') ? 'bg-slate-100 text-darkblue font-bold' : '' ?>">Tinggi Muka Air</a>
                        <a href="<?= base_url('welcome/kualitas_air') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition <?= ($current_page == 'kualitas_air') ? 'bg-slate-100 text-darkblue font-bold' : '' ?>">Kualitas Air</a>
                    </div>
                </div>

                <div class="relative group h-full">
                    <button class="h-full flex items-center gap-1 hover:text-brandyellow transition-colors">
                        Peta <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute top-full left-0 w-48 bg-white text-slate-800 shadow-xl rounded-b-lg overflow-hidden border border-slate-100">
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition">Sebaran Stasiun</a>
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition">Peta Rawan Banjir</a>
                    </div>
                </div>

                <a href="#" class="h-full flex items-center hover:text-brandyellow transition-colors">Laporan</a>
            </div>

            <button class="md:hidden text-white hover:text-brandyellow">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </nav>

    <script>
        window.onscroll = function() {
            const nav = document.getElementById('main-nav');
            if (window.pageYOffset > 50) {
                // Saat scroll ke bawah: Transparan (backdrop-blur) dan mengecilkan bayangan
                nav.classList.remove('bg-darkblue');
                nav.classList.add('bg-darkblue/80', 'backdrop-blur-md', 'h-16');
                nav.querySelector('.max-w-7xl').classList.remove('h-20');
                nav.querySelector('.max-w-7xl').classList.add('h-16');
            } else {
                // Saat kembali ke atas: Solid kembali
                nav.classList.add('bg-darkblue');
                nav.classList.remove('bg-darkblue/80', 'backdrop-blur-md', 'h-16');
                nav.querySelector('.max-w-7xl').classList.add('h-20');
                nav.querySelector('.max-w-7xl').classList.remove('h-16');
            }
        };
    </script>