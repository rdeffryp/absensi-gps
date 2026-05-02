@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- Kartu Statistik --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center">
            <i class="fa-solid fa-users text-blue-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Total Karyawan</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalKaryawan }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center">
            <i class="fa-solid fa-user-check text-green-600 text-2xl"></i>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Hadir Hari Ini</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalHadir }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
            <i class="fa-solid fa-user-clock text-red-500 text-2xl"></i>
        </div>
        <div>
            <p class="text-gray-500 text-sm">Terlambat Hari Ini</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalTerlambat }}</p>
        </div>
    </div>
</div>

{{-- Grafik --}}
<div class="bg-white rounded-2xl shadow p-6">
    <h2 class="text-gray-800 font-bold text-lg mb-4">
        <i class="fa-solid fa-chart-bar text-blue-500 mr-2"></i>
        Grafik Absensi Tahun {{ now()->year }}
    </h2>
    <canvas id="grafikAbsensi" height="100"></canvas>
</div>

@endsection

@push('scripts')
<script>
    const data = @json($rekapBulanan);
    const bulanLabel = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    const labels = bulanLabel;
    const values = Array(12).fill(0);
    data.forEach(item => { values[item.bulan - 1] = item.total; });

    new Chart(document.getElementById('grafikAbsensi'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Absensi',
                data: values,
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endpush