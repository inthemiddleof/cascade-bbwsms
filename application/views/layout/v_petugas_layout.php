<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= (isset($app_name) ? $app_name : 'CASCADE') ?> - <?= (isset($title) ? $title : 'Petugas') ?></title>
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
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        .sidebar-scroll::-webkit-scrollbar { width: 3px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .menu-item { border-left: 3px solid transparent; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .menu-item:hover { background: rgba(255,255,255,0.06); border-left-color: rgba(250,204,21,0.4); }
        .menu-active { background: rgba(250,204,21,0.1) !important; border-left-color: #facc15 !important; color: #facc15 !important; font-weight: 700 !important; }
        input, select, textarea, button { font-size: 16px !important; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">

    <?php 
        $current_url = $this->uri->uri_string();
        $petugas_name = $this->session->userdata('nama_lengkap');
        $id_pos = $this->session->userdata('id_pos');
        $pos = $this->db->where('id_pos', $id_pos)->get('master_pos')->row();
    ?>

    <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden backdrop-blur-sm" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-64 bg-darkblue text-white flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl">
        
        <!-- Logo -->
        <div class="px-5 py-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <img src="<?= base_url('assets/img/logobbwsmsbaru.png') ?>" 
                    alt="Logo"
                    class="h-10 w-auto flex-shrink-0 bg-white rounded-xl p-1">               
                <div class="min-w-0">
                    <h1 class="font-black text-sm tracking-tight leading-tight">Sistem Informasi <span class="text-brandyellow">Hidrologi</span></h1>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-4 px-3">
            <p class="px-3 mb-3 text-[9px] font-bold text-blue-300/40 uppercase tracking-[0.2em]">Menu Petugas</p>
            
            <div class="space-y-1">
                <a href="<?= base_url('petugas') ?>" class="menu-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm <?= ($current_url == 'petugas' || $current_url == 'petugas/index') ? 'menu-active' : 'text-slate-300 font-medium' ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zm0 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="<?= base_url('petugas/input') ?>" class="menu-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm <?= ($current_url == 'petugas/input') ? 'menu-active' : 'text-slate-300 font-medium' ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Laporan</span>
                </a>
                
                <a href="<?= base_url('petugas/kelola') ?>" class="menu-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm <?= ($current_url == 'petugas/kelola') ? 'menu-active' : 'text-slate-300 font-medium' ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Kelola Laporan</span>
                </a>
            </div>
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 bg-white/10 rounded-xl px-3 py-2.5 mb-3">
                <div class="w-8 h-8 rounded-lg bg-brandyellow flex items-center justify-center flex-shrink-0">
                    <span class="text-darkblue font-bold text-xs"><?= strtoupper(substr($petugas_name, 0, 2)) ?></span>
                </div>
                <div class="min-w-0">
                    <p class="text-white font-semibold text-xs truncate"><?= $petugas_name ?></p>
                    <p class="text-[10px] text-blue-300 mt-0.5">Petugas Pos</p>
                </div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium bg-red-500/10 text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-all duration-200 group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                <span>Keluar</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="lg:ml-64 min-h-screen flex flex-col">
        <main class="flex-1 p-4 lg:p-6">
            <?= $content ?? '' ?>
        </main>
    </div>

    <script>
        function toggleSidebar() { document.getElementById('sidebar').classList.toggle('-translate-x-full'); document.getElementById('overlay').classList.toggle('hidden'); }
        document.getElementById('overlay').addEventListener('click', toggleSidebar);
    </script>
</body>
</html>