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
    </style>
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
                    <span class="text-[10px] text-slate-300 tracking-wider uppercase">BBWS Mesuji Sekampung</span>
                </div>
            </a>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold tracking-wide">
                <a href="<?= base_url() ?>" class="text-brandyellow border-b-2 border-brandyellow py-2">Beranda</a>
                
                <div class="relative group py-6">
                    <button class="hover:text-brandyellow transition flex items-center gap-1">
                        Data <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute top-full left-0 w-48 bg-white text-slate-800 shadow-xl rounded-lg overflow-hidden border border-slate-100">
                        <a href="<?= base_url('welcome/curah_hujan') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition border-b border-slate-50">Curah Hujan</a>
                        <a href="<?= base_url('welcome/tma') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition border-b border-slate-50">Tinggi Muka Air</a>
                        <a href="<?= base_url('welcome/kualitas_air') ?>" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition">Kualitas Air</a>
                    </div>
                </div>

                <div class="relative group py-6">
                    <button class="hover:text-brandyellow transition flex items-center gap-1">
                        Peta <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="dropdown-menu absolute top-full left-0 w-48 bg-white text-slate-800 shadow-xl rounded-lg overflow-hidden border border-slate-100">
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 hover:text-darkblue transition border-b border-slate-50">Sebaran Stasiun</a>
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