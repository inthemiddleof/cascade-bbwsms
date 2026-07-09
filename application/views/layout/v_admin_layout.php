<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($app_name) ? $app_name : 'HydroSmart') ?> - <?= (isset($title) ? $title : 'Admin Panel') ?></title>
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
        
        #mobile-nav {
            position: fixed !important;
            top: 12px; left: 50%; transform: translateX(-50%);
            width: calc(100% - 24px); max-width: 1280px; z-index: 5000 !important;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #mobile-nav.scrolled { top: 0; width: 100%; max-width: 100%; }
        #mobile-nav-inner {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
        }
        #mobile-nav-inner.scrolled-inner {
            border-radius: 0 !important; border-left: none !important; border-right: none !important; border-top: none !important;
            background: rgba(255, 255, 255, 0.95) !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        }
        .mobile-menu-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.3); z-index: 4999; display: none; }
        .mobile-menu-backdrop.active { display: block; }
        @media (max-width: 640px) { #mobile-nav { top: 8px; width: calc(100% - 16px); } }
        @media (min-width: 641px) and (max-width: 1024px) { #mobile-nav { top: 10px; width: calc(100% - 20px); } }

        /* Sidebar scroll */
        #sidebar-nav::-webkit-scrollbar { width: 3px; }
        #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar-nav::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        #sidebar-nav::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        /* Sidebar link styles */
        .sidebar-link {
            transition: all 0.2s ease;
            position: relative;
        }
        .sidebar-link.active {
            background: #000080;
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 128, 0.1);
        }
        .sidebar-link.active .sidebar-icon {
            color: #facc15;
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: #facc15;
            border-radius: 0 4px 4px 0;
        }
        .sidebar-link:not(.active):hover {
            background: #f1f5f9;
            color: #000080;
        }
        .sidebar-link:not(.active):hover .sidebar-icon {
            color: #000080;
        }
        .sidebar-icon {
            color: #94a3b8;
            transition: color 0.2s ease;
            flex-shrink: 0;
        }
        .sidebar-section-title {
            letter-spacing: 0.1em;
            font-weight: 700;
            color: #94a3b8;
        }
        .sidebar-divider {
            position: relative;
            margin: 12px 0;
        }
        .sidebar-divider::before {
            content: '';
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            border-top: 1px solid #e2e8f0;
        }
        .sidebar-divider span {
            position: relative;
            background: #ffffff;
            padding: 0 10px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-left: 12px;
        }
        .sidebar-badge {
            font-size: 8px;
            font-weight: 700;
            padding: 1px 8px;
            border-radius: 9999px;
            background: #fef3c7;
            color: #d97706;
        }
        
        /* Badge untuk menu Export & Import */
        .menu-badge {
            font-size: 7px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 9999px;
            background: #dbeafe;
            color: #1d4ed8;
            margin-left: auto;
        }
        .sidebar-link.active .menu-badge {
            background: rgba(255,255,255,0.2);
            color: #facc15;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <?php 
        $current_url = $this->uri->uri_string();
        $admin_name = $this->session->userdata('nama_lengkap');
        $admin_role = $this->session->userdata('role');
        $base_url = 'admin'; // Khusus admin
        
        // Helper untuk cek active menu
        function is_active($url, $current) {
            return strpos($current, $url) !== false ? 'active' : '';
        }
        
        // Helper untuk cek apakah menu sedang aktif (exact match)
        function is_exact_active($url, $current) {
            return $current === $url ? 'active' : '';
        }
    ?>

    <!-- ============================================ -->
    <!-- MOBILE BACKDROP -->
    <!-- ============================================ -->
    <div id="mobile-backdrop" class="mobile-menu-backdrop" onclick="closeMobileMenu()"></div>

    <!-- ============================================ -->
    <!-- MOBILE NAVBAR -->
    <!-- ============================================ -->
    <nav id="mobile-nav" class="lg:hidden">
        <div id="mobile-nav-inner" class="rounded-2xl border border-white/30 bg-white/40 shadow-lg shadow-black/5">
            <div class="px-4 flex justify-between items-center h-12">
                <a href="<?= base_url('admin') ?>" class="flex items-center gap-2 shrink-0">
                    <img src="<?= base_url('assets/img/logobbwsmsbaru.png') ?>" alt="Logo" class="h-7 w-auto">
                    <div class="hidden sm:block h-6 w-px bg-slate-200/40"></div>
                    <div class="flex flex-col justify-center">
                        <span class="text-[7px] font-bold tracking-[0.1em] text-slate-400 uppercase leading-none">HydroSmart</span>
                        <span class="font-black text-xs tracking-tight text-darkblue leading-none">PANEL ADMIN</span>
                    </div>
                </a>
                <button id="mobile-menu-btn" class="text-darkblue p-2 hover:bg-slate-100 rounded-lg transition-colors" onclick="toggleMobileMenu()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        
        <div id="mobile-menu" class="hidden mt-2 mx-1 bg-white/95 backdrop-blur-xl rounded-2xl border border-white/40 shadow-xl overflow-hidden">
            <div class="px-4 py-5 space-y-1">
                <!-- User Info -->
                <div class="flex items-center gap-3 px-3 py-3 mb-3 bg-slate-50 rounded-xl">
                    <div class="w-10 h-10 rounded-xl bg-darkblue flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-sm"><?= strtoupper(substr($admin_name, 0, 2)) ?></span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-darkblue"><?= htmlspecialchars($admin_name) ?></p>
                        <p class="text-xs text-slate-500 capitalize"><?= $admin_role ?></p>
                    </div>
                </div>
                
                <!-- ======================================== -->
                <!-- MENU UTAMA -->
                <!-- ======================================== -->
                <div class="px-2 py-1 text-[9px] font-bold text-slate-400 uppercase tracking-wider">Menu Utama</div>
                
                <!-- Dashboard -->
                <a href="<?= base_url('admin') ?>" class="block px-3 py-3 rounded-xl font-bold text-sm <?= ($current_url == 'admin' || $current_url == 'admin/index') ? 'bg-darkblue text-white' : 'text-darkblue hover:bg-slate-50' ?>">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zm0 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                        Dashboard
                    </span>
                </a>

                <!-- Kelola Petugas -->
                <a href="<?= base_url('admin/kelola_petugas') ?>" class="block px-3 py-3 rounded-xl font-bold text-sm <?= (strpos($current_url, 'admin/kelola_petugas') !== false) ? 'bg-darkblue text-white' : 'text-darkblue hover:bg-slate-50' ?>">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        Kelola Petugas
                    </span>
                </a>

                <!-- Kelola Manual -->
                <a href="<?= base_url('admin/kelola_manual') ?>" class="block px-3 py-3 rounded-xl font-bold text-sm <?= (strpos($current_url, 'admin/kelola_manual') !== false) ? 'bg-darkblue text-white' : 'text-darkblue hover:bg-slate-50' ?>">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Kelola Manual
                    </span>
                </a>

                <!-- Export & Import (NEW - Admin) -->
                <a href="<?= base_url('admin/export_import') ?>" class="block px-3 py-3 rounded-xl font-bold text-sm <?= (strpos($current_url, 'admin/export_import') !== false) ? 'bg-darkblue text-white' : 'text-darkblue hover:bg-slate-50' ?>">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export & Import
                        <span class="ml-auto text-[8px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700">Wilayah</span>
                    </span>
                </a>

                <!-- Logout -->
                <div class="border-t border-slate-200 my-3"></div>
                <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                    Keluar
                </a>
            </div>
        </div>
    </nav>

    <!-- ============================================ -->
    <!-- DESKTOP SIDEBAR -->
    <!-- ============================================ -->
    <aside id="sidebar" class="hidden lg:flex fixed top-0 left-0 z-50 h-full w-[280px] bg-white border-r border-slate-200 flex-col shadow-sm">
        <!-- Logo -->
        <div class="px-5 py-5 border-b border-slate-100 flex-shrink-0 bg-white">
            <a href="<?= base_url('admin') ?>" class="flex items-center gap-3">
                <img src="<?= base_url('assets/img/logobbwsmsbaru.png') ?>" alt="Logo" class="h-10 w-auto flex-shrink-0">
                <div>
                    <h1 class="font-black text-darkblue text-sm leading-tight">HydroSmart</h1>
                    <p class="text-[9px] text-slate-400 font-medium leading-tight">BBWS Mesuji Sekampung</p>
                </div>
            </a>
        </div>
        
        <!-- Navigation -->
        <nav id="sidebar-nav" class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">
            
            <!-- ======================================== -->
            <!-- SECTION 1: MENU UTAMA -->
            <!-- ======================================== -->
            <div class="sidebar-divider"><span>Menu Utama</span></div>
            
            <!-- Dashboard -->
            <a href="<?= base_url('admin') ?>" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium <?= ($current_url == 'admin' || $current_url == 'admin/index') ? 'active' : 'text-slate-600' ?>">
                <svg class="sidebar-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zm0 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                <span>Dashboard</span>
            </a>

            <!-- Kelola Petugas -->
            <a href="<?= base_url('admin/kelola_petugas') ?>" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium <?= (strpos($current_url, 'admin/kelola_petugas') !== false) ? 'active' : 'text-slate-600' ?>">
                <svg class="sidebar-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                <span>Kelola Petugas</span>
            </a>

            <!-- Kelola Manual -->
            <a href="<?= base_url('admin/kelola_manual') ?>" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium <?= (strpos($current_url, 'admin/kelola_manual') !== false) ? 'active' : 'text-slate-600' ?>">
                <svg class="sidebar-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Kelola Manual</span>
            </a>

            <!-- Export & Import (NEW - Admin) -->
            <a href="<?= base_url('admin/export_import') ?>" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium <?= (strpos($current_url, 'admin/export_import') !== false) ? 'active' : 'text-slate-600' ?>">
                <svg class="sidebar-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export & Import</span>
                <span class="menu-badge">Wilayah</span>
            </a>

            <!-- ======================================== -->
            <!-- SECTION 2: INFORMASI WILAYAH -->
            <!-- ======================================== -->
            <div class="sidebar-divider"><span>Wilayah Anda</span></div>
            
            <div class="px-4 py-3 bg-blue-50 rounded-xl border border-blue-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="text-xs font-medium text-blue-800"><?= htmlspecialchars($admin_name) ?></span>
                </div>
                <p class="text-[10px] text-blue-600 mt-1">Anda hanya dapat mengakses data di wilayah yang ditangani.</p>
            </div>

        </nav>

        <!-- User Profile & Logout -->
        <div class="flex-shrink-0 border-t border-slate-200 px-4 py-3 bg-white">
            <div class="flex items-center gap-3 px-2 py-2 rounded-xl">
                <div class="w-9 h-9 rounded-xl bg-darkblue flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="text-white font-bold text-xs"><?= strtoupper(substr($admin_name, 0, 2)) ?></span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-darkblue text-sm truncate"><?= htmlspecialchars($admin_name) ?></p>
                    <p class="text-[9px] text-slate-400 font-medium capitalize"><?= $admin_role ?></p>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="p-2 hover:bg-slate-100 rounded-lg text-slate-400 hover:text-red-500 transition-all flex-shrink-0" title="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- ============================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================ -->
    <div class="lg:ml-[280px] min-h-screen">
        <main class="p-4 lg:p-6 pt-20 lg:pt-6">
            <?= $content ?? '' ?>
        </main>
    </div>

    <!-- ============================================ -->
    <!-- SCRIPTS -->
    <!-- ============================================ -->
    <script>
        function toggleMobileMenu(){
            var m = document.getElementById('mobile-menu');
            var b = document.getElementById('mobile-backdrop');
            m.classList.toggle('hidden');
            b.classList.toggle('active');
        }
        function closeMobileMenu(){
            var m = document.getElementById('mobile-menu');
            var b = document.getElementById('mobile-backdrop');
            m.classList.add('hidden');
            b.classList.remove('active');
        }
        window.addEventListener('scroll', function(){
            var n = document.getElementById('mobile-nav');
            var i = document.getElementById('mobile-nav-inner');
            if (n && i) {
                if (window.scrollY > 30) {
                    n.classList.add('scrolled');
                    i.classList.add('scrolled-inner');
                } else {
                    n.classList.remove('scrolled');
                    i.classList.remove('scrolled-inner');
                }
            }
        });
        window.addEventListener('resize', function(){
            if (window.innerWidth >= 1024) closeMobileMenu();
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') closeMobileMenu();
        });
    </script>
</body>
</html>