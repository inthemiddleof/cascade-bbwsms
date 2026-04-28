<?php 
// Set timezone ke Asia/Jakarta agar waktu sesuai dengan Bandar Lampung (WIB)
date_default_timezone_set('Asia/Jakarta'); 
?>

<div class="flex h-[calc(100vh-80px)] overflow-hidden bg-slate-100">
    
    <aside class="w-96 bg-white shadow-xl z-20 flex flex-col border-r border-slate-200">
        <div class="p-4 border-b bg-white">
            <div class="flex bg-slate-100 p-1 rounded-lg mb-4 text-[10px] font-bold uppercase">
                <button onclick="filterType('all')" class="flex-1 py-2 bg-white shadow rounded-md text-darkblue">Semua</button>
                <button onclick="filterType('PDA')" class="flex-1 py-2 text-slate-500">PDA</button>
                <button onclick="filterType('PCH')" class="flex-1 py-2 text-slate-500">PCH</button>
            </div>
            <div class="relative">
                <input type="text" id="searchInput" onkeyup="searchPos()" placeholder="Cari nama pos..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none transition-all">
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="posList" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/50">
            <?php if(!empty($semua_pos)): foreach($semua_pos as $pos): ?>
            <div class="pos-item p-4 border border-slate-100 rounded-2xl hover:border-brandyellow hover:shadow-lg transition-all cursor-pointer bg-white" 
                 data-nama="<?= strtolower($pos['nama_tampil'] ?? '') ?>" 
                 data-tipe="<?= $pos['tipe_tampil'] ?? '' ?>"
                 onclick="focusMap(<?= $pos['latitude'] ?? 0 ?>, <?= $pos['longitude'] ?? 0 ?>, '<?= $pos['id_tampil'] ?? '' ?>')">
                
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-darkblue text-sm mb-1"><?= $pos['nama_tampil'] ?></h4>
                        <span class="text-[10px] text-slate-400 italic"><?= $pos['id_tampil'] ?></span>
                        <span class="block text-[8px] text-slate-300 font-bold"><?= $pos['asal_data'] ?></span>
                    </div>
                    
                    <?php 
                        $status_text = (!empty($pos['last_update'])) ? 'ONLINE' : 'OFFLINE';
                        $status_bg = (!empty($pos['last_update'])) ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400';
                    ?>
                    <span class="text-[9px] px-2 py-1 rounded-full font-black <?= $status_bg ?>">
                        <?= $status_text ?>
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-100 text-center">
                        <span class="block text-blue-400 text-[8px] font-bold uppercase">
                            <?= ($pos['tipe_tampil'] == 'PDA') ? 'TMA' : 'Hujan' ?>
                        </span>
                        <span class="font-bold text-blue-700">
                            <?= ($pos['tipe_tampil'] == 'PDA') ? ($pos['w_level'] ?? '0').' m' : ($pos['rain'] ?? '0').' mm' ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </aside>

    <main class="flex-1 relative z-10">
        <div style="position: absolute; top: 24px; left: 24px; z-index: 1001;" class="flex bg-white/95 backdrop-blur-sm p-1 rounded-2xl shadow-xl border border-white/50">
            <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-xl border border-slate-100">
                <span class="text-darkblue font-black text-sm"><?= date('d/m/Y') ?></span>
                <div class="w-[1px] h-4 bg-slate-200 mx-1"></div>
                <div class="flex flex-col">
                    <span class="text-[8px] font-bold text-slate-400 uppercase leading-none mb-1 text-left">Update Terakhir</span>
                    <span class="text-darkblue font-black text-sm leading-none"><?= date('H:i') ?> WIB</span>
                </div>
            </div>
        </div>

        <div id="map" class="w-full h-full"></div>
    </main>

    <aside class="w-80 bg-white shadow-xl z-20 p-6 border-l border-slate-200 overflow-y-auto">
        <h3 class="font-black text-darkblue text-lg mb-6 italic flex items-center gap-2">
            <span class="w-2 h-6 bg-brandyellow rounded-full"></span> RINGKASAN
        </h3>
        <div class="p-4 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl text-white shadow-lg mb-6">
            <span class="text-[10px] font-bold opacity-80 uppercase tracking-widest leading-none">Total Pos Gabungan</span>
            <span class="block text-4xl font-black mt-2 leading-none"><?= $summary['total'] ?? '0' ?></span>
        </div>

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs font-bold text-darkblue mb-6">
            <p class="mb-3 text-[10px] text-slate-400 uppercase tracking-widest border-b border-slate-200 pb-2 italic text-left">Filter Layer</p>
            <div class="space-y-4">
                <label class="flex items-center justify-between cursor-pointer group">
                    <span class="text-slate-600">Pos Duga Air (PDA)</span>
                    <input type="checkbox" id="filterPDA" checked onchange="toggleLayers()" class="w-4 h-4 rounded text-blue-600">
                </label>
                <label class="flex items-center justify-between cursor-pointer group">
                    <span class="text-slate-600">Curah Hujan (PCH)</span>
                    <input type="checkbox" id="filterPCH" checked onchange="toggleLayers()" class="w-4 h-4 rounded text-green-600">
                </label>
            </div>
        </div>
    </aside>
</div>

<script>
   // 1. Inisialisasi Map
   var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-5.15, 105.266], 8);
    
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    var layerPDA = L.layerGroup().addTo(map);
    var layerPCH = L.layerGroup().addTo(map);
    var layerBanjir = L.layerGroup().addTo(map); 
    var markersById = {};

    // 2. Logika Warna Wilayah (GeoJSON)
    function getStyle(feature) {
        // Mendeteksi nama kabupaten (huruf besar/kecil sering jadi masalah, jadi kita toUpperCase)
        const props = feature.properties;
        const kab = (props.nmkab || props.name || props.KABUPATEN || "").toUpperCase();
        
        let warna = '#f1f5f9'; 
        let opasitas = 0.2;

        if (kab.includes("BANDAR LAMPUNG") || kab.includes("LAMPUNG SELATAN")) {
            warna = '#ef4444'; // Merah
            opasitas = 0.5;
        } else if (kab.includes("LAMPUNG TIMUR") || kab.includes("PESAWARAN")) {
            warna = '#f59e0b'; // Orange
            opasitas = 0.5;
        }

        return {
            fillColor: warna,
            weight: 1,
            opacity: 1,
            color: 'white',
            fillOpacity: opasitas
        };
    }

    // 3. Memuat GeoJSON (Pastikan Path Benar)
    fetch("<?= base_url('assets/geojson/Lampung.json') ?>")
        .then(response => response.json())
        .then(data => {
            L.geoJSON(data, {
                style: getStyle,
                onEachFeature: function (feature, layer) {
                    const name = feature.properties.nmkab || feature.properties.name || "Wilayah";
                    layer.bindTooltip(name);
                    
                    layer.on('mouseover', function() {
                        this.setStyle({ fillOpacity: 0.8, weight: 2 });
                    });
                    layer.on('mouseout', function() {
                        this.setStyle(getStyle(feature));
                    });
                }
            }).addTo(layerBanjir);
        })
        .catch(err => console.error("GeoJSON Error:", err));
 
    // Tambahkan fungsi ini di dalam tag <script> sebelum loop marker
function getMarkerConfig(pos) {
    let icon = '❓';
    let color = 'bg-slate-400'; // Default abu-abu jika tidak ada data
    let status = 'TIDAK ADA DATA';
    let animation = '';

    // Jika ada data telemetri
    if (pos.last_update) {
        if (pos.tipe_pos === 'PCH') {
            const rain = parseFloat(pos.rain || 0);
            if (rain <= 0) { icon = '☁️'; color = 'bg-sky-400'; status = 'CERAH'; }
            else if (rain <= 20) { icon = '🌦️'; color = 'bg-blue-400'; status = 'RINGAN'; }
            else if (rain <= 50) { icon = '🌧️'; color = 'bg-blue-600'; status = 'SEDANG'; }
            else { icon = '⛈️'; color = 'bg-indigo-900'; status = 'LEBAT'; }
        } 
        else if (pos.tipe_pos === 'PDA') {
            const tma = parseFloat(pos.w_level || 0);
            const merah = parseFloat(pos.siaga_merah || 999);
            const kuning = parseFloat(pos.siaga_kuning || 999);
            const hijau = parseFloat(pos.siaga_hijau || 999);

            if (tma >= merah) { icon = '⚠️'; color = 'bg-red-600'; status = 'AWAS'; animation = 'animate-awas'; }
            else if (tma >= kuning) { icon = '🌊'; color = 'bg-yellow-500'; status = 'WASPADA'; }
            else if (tma >= hijau) { icon = '🌊'; color = 'bg-green-500'; status = 'SIAGA'; }
            else { icon = '💧'; color = 'bg-blue-500'; status = 'NORMAL'; }
        }
    }

    return { icon, color, status, animation };
}

// Bagian loop PHP untuk Generate Markers
// SEBELUMNYA (Penyebab Error):
// L.marker([pos.lat, pos.lon])...
// pos.nama_alat
// pos.id_tipe

// PERBAIKAN:
<?php foreach($semua_pos as $pos): if(!empty($pos['latitude'])): ?>
(function() {
    const p = <?= json_encode($pos) ?>;
    let val = (p.tipe_tampil === 'PDA') ? parseFloat(p.w_level || 0) : parseFloat(p.rain || 0);
    
    // 1. Tentukan Warna & Class Animasi
    let statusClass = 'bg-offline';
    let pulseClass = '';
    let iconContent = '';
    
    if (p.last_update) {
            if (p.tipe_tampil === 'PDA') {
                const merah = parseFloat(p.siaga_merah || 3.0);
                const kuning = parseFloat(p.siaga_kuning || 2.0);

                if (val >= merah) { statusClass = 'bg-danger'; pulseClass = 'pulse-danger'; iconContent = '⚠️'; }
                else if (val >= kuning) { statusClass = 'bg-warning'; iconContent = '🌊'; }
                else { statusClass = 'bg-normal'; iconContent = '💧'; }
            } else {
                if (val >= 50) { statusClass = 'bg-danger'; pulseClass = 'pulse-danger'; iconContent = '⛈️'; }
                else if (val > 0) { statusClass = 'bg-normal'; iconContent = '🌧️'; }
                else { statusClass = 'bg-normal'; iconContent = '🌤️'; }
            }
        } else { iconContent = '❓'; }

    if (p.tipe_tampil === 'PCH') {
        if (val <= 0) {
            iconContent = '⛅'; // Cerah
        } else if (val > 0 && val < 20) {
            iconContent = '🌧️'; // Berawan/Gerimis
        } else {
            iconContent = '⛈️'; // Hujan Lebat
        }
    } else {
        // Ikon Stasiun PDA (Muka Air)
        iconContent = '💧'; 
    }

    const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="marker-container ${statusClass} ${pulseClass}"><span>${iconContent}</span></div>`,
            iconSize: [36, 36], iconAnchor: [18, 36]
        });

    // 3. Popup Content yang Informatif
    const badgeStatus = p.last_update ? 
        `<span style="background:#dcfce7; color:#15803d; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:bold;">ONLINE</span>` : 
        `<span style="background:#f1f5f9; color:#64748b; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:bold;">OFFLINE</span>`;

    const marker = L.marker([p.latitude, p.longitude], { icon: customIcon })
        .bindPopup(`
            <div style="font-family: 'Inter', sans-serif; width:200px;">
                <div class="custom-popup-title">
                    <div style="font-size:14px; font-weight:800; line-height:1.2;">${p.nama_tampil}</div>
                    <div style="font-size:10px; color:#94a3b8;">ID: ${p.id_tampil} | ${p.tipe_tampil}</div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin:10px 0;">
                    ${badgeStatus}
                    <div style="text-align:right;">
                        <div style="font-size:9px; color:#94a3b8; text-transform:uppercase; font-weight:700;">Data Sekarang</div>
                        <div style="font-size:18px; font-weight:900; color:#1e40af;">
                            ${p.tipe_tampil === 'PDA' ? p.w_level + ' m' : p.rain + ' mm'}
                        </div>
                    </div>
                </div>
                <div style="background:#f8fafc; padding:6px; border-radius:8px; font-size:9px; color:#64748b; border:1px solid #f1f5f9;">
                    <b>🕒 Update Terakhir:</b><br>
                    ${p.last_update || 'Belum ada data masuk'}
                </div>
            </div>
        `);

        markersById[p.id_tampil] = marker;
        if (p.tipe_tampil === 'PDA') marker.addTo(layerPDA);
        else marker.addTo(layerPCH);
})();
<?php endif; endforeach; ?>

    function focusMap(lat, lon, id) {
        map.flyTo([lat, lon], 15, { animate: true, duration: 1.5 });
        setTimeout(() => { if(markersById[id]) markersById[id].openPopup(); }, 1200);
    }

    function toggleLayers() {
        if(document.getElementById('filterPDA').checked) map.addLayer(layerPDA); else map.removeLayer(layerPDA);
        if(document.getElementById('filterPCH').checked) map.addLayer(layerPCH); else map.removeLayer(layerPCH);
    }

    function searchPos() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let items = document.getElementsByClassName('pos-item');
        for (let item of items) {
            item.style.display = item.getAttribute('data-nama').includes(input) ? "block" : "none";
        }
    }
    function toggleBanjir() {
    if(document.getElementById('filterBanjir').checked) map.addLayer(layerBanjir); 
    else map.removeLayer(layerBanjir);
}
</script>

<style>
    /* Container Utama Marker */
    .marker-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 2px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* Memutar balik isi agar tegak */
    .marker-container span {
        transform: rotate(45deg);
        font-size: 18px;
        display: block;
    }

    .marker-container:hover {
        transform: rotate(-45deg) scale(1.2);
        z-index: 1000 !important;
    }

    /* Warna Status Profesional */
    .bg-normal { background: #10b981; }  /* Hijau */
    .bg-warning { background: #f59e0b; } /* Kuning/Oranye */
    .bg-danger { background: #ef4444; }  /* Merah */
    .bg-offline { background: #94a3b8; } /* Abu-abu */

    /* Efek Pulse untuk Kondisi Bahaya */
    .pulse-danger {
        animation: pulse-red 1.5s infinite;
    }
    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
</style>
