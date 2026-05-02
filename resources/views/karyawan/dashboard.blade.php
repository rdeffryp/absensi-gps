@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="max-w-lg mx-auto">

    {{-- Greeting --}}
    <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-2xl shadow p-6 mb-6 text-white">
        <p class="text-blue-100 text-sm">Selamat datang,</p>
        <h2 class="text-2xl font-bold">{{ auth()->user()->name }}</h2>
        <p class="text-blue-100 text-sm mt-1">
            <i class="fa-regular fa-calendar mr-1"></i>
            {{ now()->isoFormat('dddd, D MMMM Y') }}
        </p>
    </div>

    {{-- Status Hari Ini --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow p-4 text-center">
            <i class="fa-solid fa-arrow-right-to-bracket text-2xl {{ $sudahMasuk ? 'text-green-500' : 'text-gray-300' }} mb-2"></i>
            <p class="text-xs text-gray-500 font-semibold">Absen Masuk</p>
            @if($sudahMasuk)
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $sudahMasuk->checked_at->format('H:i') }}</p>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $sudahMasuk->status === 'tepat_waktu' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                {{ $sudahMasuk->status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
            </span>
            @else
            <p class="text-xs text-gray-400 mt-1">Belum absen</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow p-4 text-center">
            <i class="fa-solid fa-arrow-right-from-bracket text-2xl {{ $sudahPulang ? 'text-purple-500' : 'text-gray-300' }} mb-2"></i>
            <p class="text-xs text-gray-500 font-semibold">Absen Pulang</p>
            @if($sudahPulang)
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $sudahPulang->checked_at->format('H:i') }}</p>
            <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-600">Selesai</span>
            @else
            <p class="text-xs text-gray-400 mt-1">Belum absen</p>
            @endif
        </div>
    </div>

    {{-- Tombol Absen --}}
    <div class="bg-white rounded-2xl shadow p-6 mb-6" x-data="absensi()">

        {{-- Step 1: Pilih Tombol --}}
        <div x-show="step === 1">
            <h3 class="text-gray-800 font-bold text-center mb-4">Pilih Absensi</h3>
            <div class="grid grid-cols-2 gap-4">
                <button
                    @click="mulaiAbsen('masuk')"
                    {{ $sudahMasuk ? 'disabled' : '' }}
                    class="flex flex-col items-center gap-2 py-6 rounded-2xl border-2 transition font-semibold text-sm
                    {{ $sudahMasuk ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-blue-500 text-blue-600 hover:bg-blue-50' }}">
                    <i class="fa-solid fa-arrow-right-to-bracket text-3xl"></i>
                    Absen Masuk
                </button>
                <button
                    @click="mulaiAbsen('pulang')"
                    {{ !$sudahMasuk || $sudahPulang ? 'disabled' : '' }}
                    class="flex flex-col items-center gap-2 py-6 rounded-2xl border-2 transition font-semibold text-sm
                    {{ !$sudahMasuk || $sudahPulang ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-purple-500 text-purple-600 hover:bg-purple-50' }}">
                    <i class="fa-solid fa-arrow-right-from-bracket text-3xl"></i>
                    Absen Pulang
                </button>
            </div>
        </div>

        {{-- Step 2: Kamera --}}
        <div x-show="step === 2">
            <h3 class="text-gray-800 font-bold text-center mb-4">
                <i class="fa-solid fa-camera mr-2 text-blue-500"></i> Ambil Foto Selfie
            </h3>
            <div class="relative rounded-2xl overflow-hidden bg-black mb-4" style="aspect-ratio:4/3">
                <video id="video" autoplay playsinline class="w-full h-full object-cover"></video>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button @click="batalAbsen()" class="py-3 rounded-xl border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
                    <i class="fa-solid fa-xmark mr-1"></i> Batal
                </button>
                <button @click="ambilFoto()" class="py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
                    <i class="fa-solid fa-camera mr-1"></i> Ambil Foto
                </button>
            </div>
        </div>

        {{-- Step 3: Preview Foto --}}
        <div x-show="step === 3">
            <h3 class="text-gray-800 font-bold text-center mb-4">
                <i class="fa-solid fa-image mr-2 text-blue-500"></i> Konfirmasi Foto
            </h3>
            <div class="rounded-2xl overflow-hidden mb-4" style="aspect-ratio:4/3">
                <canvas id="canvas" class="w-full h-full object-cover"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button @click="ulangi()" class="py-3 rounded-xl border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Ulangi
                </button>
                <button @click="kirimAbsen()" class="py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition">
                    <i class="fa-solid fa-check mr-1"></i> Kirim Absen
                </button>
            </div>
        </div>

        {{-- Step 4: Loading --}}
        <div x-show="step === 4" class="text-center py-8">
            <i class="fa-solid fa-spinner fa-spin text-blue-500 text-4xl mb-4"></i>
            <p class="text-gray-600 font-semibold" x-text="loadingText"></p>
        </div>

    </div>

    {{-- Link Riwayat --}}
    <a href="{{ route('karyawan.riwayat') }}"
        class="block bg-white rounded-2xl shadow p-4 text-center text-blue-600 font-semibold text-sm hover:bg-blue-50 transition">
        <i class="fa-solid fa-clock-rotate-left mr-2"></i> Lihat Riwayat Absensi
    </a>

</div>

@endsection

@push('scripts')
<script>
function absensi() {
    return {
        step: 1,
        type: '',
        photoData: '',
        loadingText: '',
        stream: null,

        mulaiAbsen(type) {
            this.type = type;
            this.step = 2;
            this.$nextTick(() => this.startCamera());
        },

        startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(s => {
                    this.stream = s;
                    document.getElementById('video').srcObject = s;
                })
                .catch(() => {
                    Swal.fire('Error', 'Tidak bisa mengakses kamera.', 'error');
                    this.step = 1;
                });
        },

        ambilFoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.photoData = canvas.toDataURL('image/png');
            this.stopCamera();
            this.step = 3;
        },

        ulangi() {
            this.step = 2;
            this.$nextTick(() => this.startCamera());
        },

        batalAbsen() {
            this.stopCamera();
            this.step = 1;
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
        },

        kirimAbsen() {
            this.step = 4;
            this.loadingText = 'Mendeteksi lokasi GPS...';

            navigator.geolocation.getCurrentPosition(pos => {
                this.loadingText = 'Mengirim data absensi...';

                fetch('{{ route('karyawan.absen') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        type: this.type,
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                        photo: this.photoData,
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, confirmButtonColor: '#2563eb' })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#2563eb' });
                        this.step = 1;
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Terjadi kesalahan. Coba lagi.', 'error');
                    this.step = 1;
                });
            }, () => {
                Swal.fire('Error', 'Tidak bisa mendapatkan lokasi GPS. Pastikan izin lokasi diberikan.', 'error');
                this.step = 1;
            });
        }
    }
}
</script>
@endpush