@extends('layouts.admin')

@section('title', 'Pengaturan Kantor')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fa-solid fa-building text-blue-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-gray-800 font-bold text-lg">Pengaturan Lokasi Kantor</h2>
                <p class="text-gray-500 text-sm">Atur koordinat dan radius absensi</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-building mr-1 text-blue-500"></i> Nama Kantor
                </label>
                <input type="text" name="name" value="{{ $office->name }}"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-map-pin mr-1 text-blue-500"></i> Latitude
                </label>
                <input type="text" name="latitude" value="{{ $office->latitude }}" id="latitude"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-map-pin mr-1 text-blue-500"></i> Longitude
                </label>
                <input type="text" name="longitude" value="{{ $office->longitude }}" id="longitude"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-circle-dot mr-1 text-blue-500"></i> Radius (meter)
                </label>
                <input type="number" name="radius" value="{{ $office->radius }}"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <p class="text-gray-400 text-xs mt-1">Karyawan harus berada dalam radius ini untuk bisa absen</p>
            </div>
{{-- Tombol Atur Lewat Peta --}}
            <div class="mb-4">
                <button type="button" onclick="bukaMap()"
                    class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold py-3 rounded-xl text-sm transition border border-blue-200">
                    <i class="fa-solid fa-map-location-dot mr-2 text-blue-500"></i> Atur Lokasi & Radius Lewat Peta
                </button>
            </div>
            {{-- Tombol Deteksi Lokasi --}}
            <div class="mb-6">
                <button type="button" onclick="deteksiLokasi()"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl text-sm transition">
                    <i class="fa-solid fa-location-crosshairs mr-2 text-blue-500"></i> Gunakan Lokasi Saat Ini sebagai Koordinat Kantor
                </button>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold py-3 rounded-xl transition shadow">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
{{-- Modal Peta --}}
<div id="modalMap" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-bold text-gray-800 text-lg"><i class="fa-solid fa-map-location-dot mr-2 text-blue-500"></i>Atur Lokasi Kantor di Peta</h3>
            <button onclick="tutupMap()" class="text-gray-400 hover:text-gray-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="px-6 py-3 bg-blue-50 border-b text-sm text-blue-700">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Geser <strong>pin merah</strong> untuk menentukan lokasi kantor. Lingkaran biru menunjukkan radius absensi.
        </div>

        <div id="petaKantor" style="height: 400px; width: 100%;"></div>

        <div class="px-6 py-4 border-t">
            <div class="flex items-center gap-4 mb-4">
                <label class="text-sm font-semibold text-gray-700 whitespace-nowrap">
                    <i class="fa-solid fa-circle-dot mr-1 text-blue-500"></i> Radius:
                </label>
                <input type="range" id="sliderRadius" min="50" max="1000" step="10" value="{{ $office->radius }}"
                    class="flex-1" oninput="updateRadius(this.value)">
                <span id="labelRadius" class="text-sm font-bold text-blue-600 w-20 text-right">{{ $office->radius }} meter</span>
            </div>
            <div class="flex gap-3">
                <button onclick="tutupMap()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl text-sm transition">
                    Batal
                </button>
                <button onclick="simpanDariMap()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan dari Peta
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Leaflet CSS & JS
const leafletCSS = document.createElement('link');
leafletCSS.rel = 'stylesheet';
leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
document.head.appendChild(leafletCSS);

const leafletJS = document.createElement('script');
leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
document.head.appendChild(leafletJS);

let petaInstance = null;
let markerInstance = null;
let lingkaranInstance = null;
let radiusMap = {{ $office->radius }};
let latMap = {{ $office->latitude ?: '-6.2088' }};
let lngMap = {{ $office->longitude ?: '106.8456' }};

function bukaMap() {
    document.getElementById('modalMap').classList.remove('hidden');

    setTimeout(() => {
        if (petaInstance) {
            petaInstance.invalidateSize();
            return;
        }

        petaInstance = L.map('petaKantor').setView([latMap, lngMap], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(petaInstance);

        markerInstance = L.marker([latMap, lngMap], { draggable: true }).addTo(petaInstance);
        lingkaranInstance = L.circle([latMap, lngMap], {
            radius: radiusMap,
            color: '#3b82f6',
            fillColor: '#93c5fd',
            fillOpacity: 0.25,
            weight: 2
        }).addTo(petaInstance);

        markerInstance.on('dragend', function () {
            const pos = markerInstance.getLatLng();
            latMap = pos.lat;
            lngMap = pos.lng;
            lingkaranInstance.setLatLng([latMap, lngMap]);
        });

        petaInstance.on('click', function (e) {
            latMap = e.latlng.lat;
            lngMap = e.latlng.lng;
            markerInstance.setLatLng([latMap, lngMap]);
            lingkaranInstance.setLatLng([latMap, lngMap]);
        });

    }, 200);
}

function tutupMap() {
    document.getElementById('modalMap').classList.add('hidden');
}

function updateRadius(val) {
    radiusMap = parseInt(val);
    document.getElementById('labelRadius').textContent = radiusMap + ' meter';
    if (lingkaranInstance) {
        lingkaranInstance.setRadius(radiusMap);
    }
}

function simpanDariMap() {
    document.getElementById('latitude').value = latMap.toFixed(8);
    document.getElementById('longitude').value = lngMap.toFixed(8);
    document.querySelector('input[name="radius"]').value = radiusMap;
    tutupMap();
    Swal.fire({
        icon: 'success',
        title: 'Lokasi diterapkan!',
        text: 'Klik "Simpan Pengaturan" untuk menyimpan ke database.',
        timer: 2500,
        showConfirmButton: false
    });
}
function deteksiLokasi() {
    if (!navigator.geolocation) {
        Swal.fire('Error', 'Browser tidak mendukung GPS', 'error');
        return;
    }
    Swal.fire({ title: 'Mendeteksi lokasi...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    navigator.geolocation.getCurrentPosition(pos => {
        document.getElementById('latitude').value = pos.coords.latitude;
        document.getElementById('longitude').value = pos.coords.longitude;
        Swal.fire('Berhasil!', 'Koordinat kantor berhasil diisi dari lokasi saat ini.', 'success');
    }, () => {
        Swal.fire('Gagal', 'Tidak bisa mendapatkan lokasi. Pastikan izin GPS diberikan.', 'error');
    });
}
</script>
@endpush