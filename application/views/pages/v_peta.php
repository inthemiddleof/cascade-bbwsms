<?php date_default_timezone_set('Asia/Jakarta'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="flex h-[calc(100vh-64px)] lg:h-[calc(100vh-80px)] overflow-x-hidden overflow-y-visible lg:overflow-y-visible bg-slate-100 relative">
    
    <aside id="sidebar-left" class="fixed inset-y-0 left-0 z-[1001] w-80 lg:w-96 bg-white shadow-2xl lg:shadow-none lg:relative lg:translate-x-0 -translate-x-full transition-transform duration-300 flex flex-col border-r border-slate-200">
        <div class="p-4 border-b bg-white">
            <div class="mb-6 flex bg-slate-50 p-1 rounded-2xl border border-slate-100">
                <div class="flex items-center justify-between w-full gap-3 px-4 py-3 bg-white rounded-xl border border-slate-50">
                    <div class="flex flex-col">
                        <span class="text-[8px] font-bold text-slate-400 uppercase mb-1">Tanggal</span>
                        <span class="text-darkblue font-black text-sm"><?= date('d/m/Y') ?></span>
                    </div>
                    <div class="w-[1px] h-8 bg-slate-100"></div>
                    <div class="flex flex-col text-right">
                        <span class="text-[8px] font-bold text-slate-400 uppercase mb-1">Update</span>
                        <span class="text-darkblue font-black text-sm"><?= date('H:i') ?> WIB</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center lg:hidden mb-4">
                <h3 class="font-black text-darkblue uppercase tracking-tight">Daftar Pos</h3>
                <button onclick="toggleSidebar('left')" class="p-2 -mr-2 text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="mb-2 bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm focus-within:ring-4 focus-within:ring-blue-50 focus-within:border-blue-300 transition-all">
                <div class="relative border-b border-slate-100">
                    <input type="text" id="searchInput" onkeyup="searchPos()" placeholder="Cari nama pos..." class="w-full pl-10 pr-4 py-3.5 bg-transparent text-sm outline-none placeholder:text-slate-400">
                    <svg class="w-5 h-5 absolute left-3 top-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                
                <div class="flex p-1 bg-slate-50/50">
                    <button onclick="filterType('all', this)" class="filter-btn flex-1 py-2 text-[10px] font-bold uppercase tracking-wider rounded-xl bg-white shadow-sm text-blue-600 transition-all">Semua</button>
                    <button onclick="filterType('PDA', this)" class="filter-btn flex-1 py-2 text-[10px] font-bold uppercase tracking-wider rounded-xl text-slate-500 hover:text-darkblue transition-all">PDA</button>
                    <button onclick="filterType('PCH', this)" class="filter-btn flex-1 py-2 text-[10px] font-bold uppercase tracking-wider rounded-xl text-slate-500 hover:text-darkblue transition-all">PCH</button>
                </div>
            </div>
        </div>

        <div id="posList" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/50">
            <?php if(!empty($semua_pos)): foreach($semua_pos as $pos): ?>
            <div class="pos-item p-4 border border-slate-100 rounded-2xl hover:border-brandyellow hover:shadow-lg transition-all cursor-pointer bg-white" 
                 data-nama="<?= strtolower($pos['nama_tampil'] ?? '') ?>" 
                 data-tipe="<?= $pos['tipe_tampil'] ?? '' ?>"
                 onclick="focusMap(<?= $pos['latitude'] ?? 0 ?>, <?= $pos['longitude'] ?? 0 ?>, '<?= $pos['id_tampil'] ?? '' ?>'); triggerGraph('<?= $pos['id_tampil'] ?>'); handleMobileClick()">
                
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-darkblue text-sm mb-1"><?= $pos['nama_tampil'] ?></h4>
                        <span class="text-[10px] text-slate-400 italic"><?= $pos['id_tampil'] ?></span>
                    </div>
                    <?php $status_bg = (!empty($pos['last_update'])) ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400'; ?>
                    <span class="text-[9px] px-2 py-1 rounded-full font-black <?= $status_bg ?>">
                        <?= (!empty($pos['last_update'])) ? 'ONLINE' : 'OFFLINE' ?>
                    </span>
                </div>

                <div class="bg-blue-50 p-2 rounded-lg border border-blue-100 inline-block min-w-[80px] text-center">
                    <span class="block text-blue-400 text-[8px] font-bold uppercase"><?= ($pos['tipe_tampil'] == 'PDA') ? 'TMA' : 'Hujan' ?></span>
                    <span class="font-bold text-blue-700 text-xs">
                        <?= ($pos['tipe_tampil'] == 'PDA') ? ($pos['w_level'] ?? '0').' m' : ($pos['rain'] ?? '0').' mm' ?>
                    </span>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </aside>

    <main class="flex-1 relative z-10 h-full">
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-[1000] flex gap-2 lg:hidden">
            <button onclick="toggleSidebar('left')" class="bg-darkblue text-white px-4 py-2.5 rounded-full shadow-2xl flex items-center gap-2 text-sm font-bold border-2 border-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>Daftar Pos
            </button>
            <button onclick="toggleSidebar('right')" class="bg-white text-darkblue px-4 py-2.5 rounded-full shadow-2xl flex items-center gap-2 text-sm font-bold border-2 border-brandyellow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Info
            </button>
        </div>

        <div id="map" class="w-full h-full"></div>
    </main>

    <aside id="sidebar-right" class="fixed inset-y-0 right-0 z-[1001] w-80 bg-white shadow-2xl lg:shadow-none lg:relative lg:translate-x-0 translate-x-full transition-transform duration-300 p-6 border-l border-slate-200 overflow-y-auto">
        <div class="flex justify-between items-center mb-6 lg:block">
            <h3 class="font-black text-darkblue text-lg italic flex items-center gap-2">
                <span class="w-2 h-6 bg-brandyellow rounded-full"></span> RINGKASAN
            </h3>
            <button onclick="toggleSidebar('right')" class="lg:hidden text-slate-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-4 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl text-white shadow-lg mb-6">
            <span class="text-[10px] font-bold opacity-80 uppercase tracking-widest leading-none">Total Pos Gabungan</span>
            <span class="block text-4xl font-black mt-2 leading-none"><?= $summary['total'] ?? '0' ?></span>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-6">
            <p class="mb-3 text-[10px] text-slate-400 uppercase tracking-widest border-b border-slate-200 pb-2 italic">Grafik 7 Hari Terakhir</p>
            
            <div id="empty-graph-msg" class="text-center py-8">
                <span class="text-3xl mb-2 block">📊</span>
                <p class="text-xs font-bold text-slate-400">Klik pos pada peta atau daftar untuk melihat grafik riil.</p>
            </div>

            <div id="graph-container" class="hidden">
                <h4 id="graph-title" class="font-bold text-darkblue text-sm mb-1 text-center uppercase tracking-tighter"></h4>
                <p id="graph-subtitle" class="text-[9px] text-center text-slate-500 mb-3 font-semibold uppercase"></p>
                <div class="relative h-48 w-full">
                    <canvas id="stationChart"></canvas>
                </div>
            </div>
        </div>

    </aside>

    <div id="sidebar-overlay" onclick="closeAllSidebars()" class="fixed inset-0 bg-black/50 z-[1000] hidden backdrop-blur-sm"></div>
</div>

<script>
    var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-5.15, 105.266], 8);
    
    var googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3']
    }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    var layerPDA = L.layerGroup().addTo(map);
    var layerPCH = L.layerGroup().addTo(map);
    var markersById = {};
    
    let currentChart = null;
    const posDataStore = {}; // Menyimpan data historis dari PHP ke JS

    // Setup Marker Style
    function getMarkerConfig(tipe, val, merah, kuning) {
        let statusClass = 'bg-offline';
        let pulseClass = '';
        let iconContent = (tipe === 'PDA') ? '💧' : '🌤️';

        if (val !== null && val !== undefined) {
            if (tipe === 'PDA') {
                if (val >= merah && merah > 0) { statusClass = 'bg-danger'; pulseClass = 'pulse-danger'; iconContent = '⚠️'; }
                else if (val >= kuning && kuning > 0) { statusClass = 'bg-warning'; iconContent = '🌊'; }
                else { statusClass = 'bg-normal'; }
            } else {
                if (val >= 50) { statusClass = 'bg-danger'; iconContent = '⛈️'; }
                else if (val > 0) { statusClass = 'bg-normal'; iconContent = '🌧️'; }
            }
        } else {
            iconContent = '❓';
        }
        return { statusClass, pulseClass, iconContent };
    }

    // Eksekusi Grafik
    function showGraph(tipe, nama, labels, dataValues) {
        const container = document.getElementById('graph-container');
        const emptyMsg = document.getElementById('empty-graph-msg');
        const title = document.getElementById('graph-title');
        const subtitle = document.getElementById('graph-subtitle');
        const ctx = document.getElementById('stationChart').getContext('2d');

        emptyMsg.classList.add('hidden');
        container.classList.remove('hidden');
        
        title.innerText = nama;
        subtitle.innerText = (tipe === 'PDA') ? "Tinggi Muka Air Harian Terakhir" : "Total Curah Hujan Harian";

        if (currentChart) currentChart.destroy();

        let chartConfig = {};

        if (tipe === 'PDA') {
            chartConfig = {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tinggi Muka Air (m)',
                        data: dataValues,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#1e40af'
                    }]
                }
            };
        } else {
            chartConfig = {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Curah Hujan (mm)',
                        data: dataValues,
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    }]
                }
            };
        }

        chartConfig.options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function(context) { return context.raw + (tipe === 'PDA' ? ' m' : ' mm'); } } }
            },
            scales: { y: { beginAtZero: true } }
        };

        currentChart = new Chart(ctx, chartConfig);
    }

    // Trigger Grafik Dari Daftar Samping
    function triggerGraph(id) {
        if(posDataStore[id]) {
            const d = posDataStore[id];
            showGraph(d.tipe, d.nama, d.labels, d.values);
        }
    }

    // Injeksi Data PHP -> Objek Map & JS
    <?php foreach($semua_pos as $pos): if(!empty($pos['latitude']) && !empty($pos['longitude'])): ?>
    (function() {
        const p = {
            id: '<?= $pos['id_tampil'] ?? "???" ?>',
            nama: '<?= addslashes($pos['nama_tampil'] ?? "Tanpa Nama") ?>',
            tipe: '<?= $pos['tipe_tampil'] ?? "PDA" ?>',
            lat: <?= (float)$pos['latitude'] ?>,
            lng: <?= (float)$pos['longitude'] ?>,
            last_update: '<?= $pos['last_update'] ?? "" ?>',
            val: <?= ($pos['tipe_tampil'] == 'PDA') ? (float)($pos['w_level'] ?? 0) : (float)($pos['rain'] ?? 0) ?>,
            merah: <?= (float)($pos['siaga_merah'] ?? 0) ?>,
            kuning: <?= (float)($pos['siaga_kuning'] ?? 0) ?>,
            labels: <?= json_encode($pos['chart_labels'] ?? []) ?>,
            values: <?= json_encode($pos['chart_values'] ?? []) ?>
        };

        posDataStore[p.id] = p; // Daftarkan di memori JS untuk dipanggil Sidebar Kiri

        let mConfig = getMarkerConfig(p.tipe, p.last_update ? p.val : null, p.merah, p.kuning);

        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="marker-container ${mConfig.statusClass} ${mConfig.pulseClass}"><span>${mConfig.iconContent}</span></div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 36]
        });

        const marker = L.marker([p.lat, p.lng], { icon: customIcon })
            .bindPopup(`
                <div style="font-family: 'Inter', sans-serif; width:200px;">
                    <div class="custom-popup-title" style="margin-bottom:8px;">
                        <div style="font-size:14px; font-weight:800; color:#1e40af;">${p.nama}</div>
                        <div style="font-size:10px; color:#94a3b8;">ID: ${p.id} | ${p.tipe}</div>
                    </div>
                    <div style="font-size:18px; font-weight:900; color:#1e40af; margin-bottom:5px;">
                        ${p.val} ${p.tipe === 'PDA' ? 'm' : 'mm'}
                    </div>
                    <div style="font-size:9px; color:#64748b;">
                        🕒 ${p.last_update || 'Tidak ada data'}
                    </div>
                </div>
            `);

        marker.on('click', function() {
            showGraph(p.tipe, p.nama, p.labels, p.values);
            if (window.innerWidth < 1024) toggleSidebar('right');
        });

        markersById[p.id] = marker;
        if (p.tipe === 'PDA') marker.addTo(layerPDA);
        else marker.addTo(layerPCH);
    })();
    <?php endif; endforeach; ?>

    // Fitur Sidebar dan Pencarian
    function focusMap(lat, lon, id) {
        map.flyTo([lat, lon], 15, { animate: true, duration: 1.5 });
        setTimeout(() => { if(markersById[id]) markersById[id].openPopup(); }, 1200);
    }

    function searchPos() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        let items = document.getElementsByClassName('pos-item');
        for (let item of items) {
            const name = item.getAttribute('data-nama');
            item.style.display = name.includes(query) ? "block" : "none";
        }
    }

    function filterType(type, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
            b.classList.add('text-slate-500');
        });
        btn.classList.remove('text-slate-500');
        btn.classList.add('bg-white', 'shadow-sm', 'text-blue-600');

        let items = document.getElementsByClassName('pos-item');
        for (let item of items) {
            const itemType = item.getAttribute('data-tipe');
            item.style.display = (type === 'all' || itemType === type) ? "block" : "none";
        }
        
        if (type === 'all') {
            map.addLayer(layerPDA); map.addLayer(layerPCH);
        } else if (type === 'PDA') {
            map.addLayer(layerPDA); map.removeLayer(layerPCH);
        } else {
            map.removeLayer(layerPDA); map.addLayer(layerPCH);
        }
    }

    function toggleSidebar(side) {
        const sidebar = document.getElementById(`sidebar-${side}`);
        const overlay = document.getElementById('sidebar-overlay');
        
        if (sidebar.classList.contains(side === 'left' ? '-translate-x-full' : 'translate-x-full')) {
            sidebar.classList.remove(side === 'left' ? '-translate-x-full' : 'translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add(side === 'left' ? '-translate-x-full' : 'translate-x-full');
            checkOverlays();
        }
    }

    function closeAllSidebars() {
        document.getElementById('sidebar-left').classList.add('-translate-x-full');
        document.getElementById('sidebar-right').classList.add('translate-x-full');
        document.getElementById('sidebar-overlay').classList.add('hidden');
    }

    function checkOverlays() {
        if(document.getElementById('sidebar-left').classList.contains('-translate-x-full') && 
           document.getElementById('sidebar-right').classList.contains('translate-x-full')) {
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
    }

    function handleMobileClick() {
        if (window.innerWidth < 1024) closeAllSidebars();
    }
</script>

<style>
    .marker-container {
        display: flex; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .marker-container span { transform: rotate(45deg); font-size: 18px; display: block; }
    .marker-container:hover { transform: rotate(-45deg) scale(1.2); z-index: 1000 !important; }

    .bg-normal { background: #10b981; }  
    .bg-warning { background: #f59e0b; } 
    .bg-danger { background: #ef4444; }  
    .bg-offline { background: #94a3b8; }

    .pulse-danger { animation: pulse-red 1.5s infinite; }
    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
</style>