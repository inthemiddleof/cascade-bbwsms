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
                 data-nama="<?= strtolower($pos['nama_alat'] ?? '') ?>" 
                 data-tipe="<?= $pos['id_tipe'] ?? '' ?>"
                 onclick="focusMap(<?= $pos['lat'] ?>, <?= $pos['lon'] ?>, '<?= $pos['device_id'] ?>')">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-darkblue text-sm mb-1"><?= $pos['nama_alat'] ?></h4>
                        <span class="text-[10px] text-slate-400"><?= $pos['device_id'] ?></span>
                    </div>
                    <?php $status_bg = (strtolower($pos['status'] ?? '') == 'normal' || strtolower($pos['status'] ?? '') == 'cerah') ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'; ?>
                    <span class="text-[9px] px-2 py-1 rounded-full font-black <?= $status_bg ?>">
                        <?= strtoupper($pos['status'] ?? 'NORMAL') ?>
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-100 text-center">
                        <span class="block text-blue-400 text-[8px] font-bold uppercase"><?= ($pos['id_tipe'] == 'PDA') ? 'TMA' : 'Hujan' ?></span>
                        <span class="font-bold text-blue-700"><?= ($pos['id_tipe'] == 'PDA') ? ($pos['w_level'] ?? '0').' m' : ($pos['rain'] ?? '0').' mm' ?></span>
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
                    <span class="text-[8px] font-bold text-slate-400 uppercase leading-none mb-1 text-left">Update</span>
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
            <span class="text-[10px] font-bold opacity-80 uppercase tracking-widest leading-none">Total Pos Terpasang</span>
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
    var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-5.397, 105.266], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    var layerPDA = L.layerGroup().addTo(map);
    var layerPCH = L.layerGroup().addTo(map);
    var markersById = {};

    // 2. GENERATE MARKERS
    <?php foreach($semua_pos as $pos): if(!empty($pos['lat'])): ?>
        (function() {
            var lat = <?= $pos['lat'] ?>;
            var lon = <?= $pos['lon'] ?>;
            var nama = "<?= $pos['nama_alat'] ?>";
            var id_tipe = "<?= $pos['id_tipe'] ?>";
            var device_id = "<?= $pos['device_id'] ?>";
            var status = "<?= strtoupper($pos['status']) ?>";
            
            // Format Nilai & Satuan
            var value = (id_tipe === 'PDA') ? "<?= $pos['w_level'] ?>" : "<?= $pos['rain'] ?>";
            var unit = (id_tipe === 'PDA') ? "mdpl" : "mm";
            
            // Waktu Update (WIB)
            var updateTime = "<?= isset($pos['tgl']) ? date('d F Y H:i', strtotime($pos['tgl'])) : date('d F Y H:i') ?> WIB";
            
            var color = (id_tipe === 'PDA') ? 'blue' : 'green';
            var customIcon = new L.Icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Template Popup Berdasarkan Gambar
            var popupContent = `
                <div class="p-2 min-w-[280px] font-sans">
                    <div class="flex justify-between items-start mb-1">
                        <h5 class="font-black text-blue-900 italic text-sm uppercase">${id_tipe} ${nama}</h5>
                        <span class="text-[10px] font-bold text-green-500">online</span>
                    </div>
                    <p class="text-slate-400 text-[10px] font-bold mb-4">${device_id}</p>
                    
                    <div class="space-y-1 text-[11px] mb-4">
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-bold">Sungai</span>
                            <span class="text-slate-700 font-bold">: -</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-bold">Update</span>
                            <span class="text-slate-700 font-bold">: ${updateTime}</span>
                        </div>
                    </div>

                    <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100 text-center mb-4">
                        <div class="flex items-end justify-center gap-1 mb-1">
                            <span class="text-3xl font-black text-blue-900">${value}</span>
                            <span class="text-sm font-bold text-slate-400 pb-1">${unit}</span>
                        </div>
                        <div class="w-full h-[1px] bg-slate-200 my-2"></div>
                        <div class="text-[10px] font-black text-green-500 uppercase tracking-widest">
                            ${status}
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div class="py-2 rounded-xl text-[9px] font-black text-center italic ${id_tipe === 'PDA' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 text-slate-400'}">TMA</div>
                        <div class="py-2 rounded-xl text-[9px] font-black text-center italic bg-slate-100 text-slate-400">DEBIT</div>
                        <div class="py-2 rounded-xl text-[9px] font-black text-center italic ${id_tipe === 'PCH' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 text-slate-400'}">STATUS</div>
                    </div>
                </div>
            `;

            var marker = L.marker([lat, lon], { icon: customIcon })
                .bindPopup(popupContent, {
                    maxWidth: 400,
                    className: 'custom-popup'
                });

            if(id_tipe === 'PDA') {
                marker.addTo(layerPDA);
            } else {
                marker.addTo(layerPCH);
            }

            markersById["<?= $pos['device_id'] ?>"] = marker;
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
</script>

<style>
    .leaflet-popup-content-wrapper { border-radius: 1.5rem; padding: 4px; background: rgba(255,255,255,0.98); }
    .leaflet-popup-tip-container { display: none; }
</style>