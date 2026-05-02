@extends('layouts.admin')

@section('title', 'Rekap Absensi')

@section('content')

{{-- Filter --}}
<div class="bg-white rounded-2xl shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.rekap') }}" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
            <select name="bulan" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach(range(1,12) as $b)
                <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
            <select name="tahun" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach(range(now()->year, now()->year - 3) as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
        <a href="{{ route('admin.rekap.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">
            <i class="fa-solid fa-file-excel mr-1"></i> Export Excel
        </a>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Nama</th>
                <th class="px-6 py-4 text-left">Tipe</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Waktu</th>
                <th class="px-6 py-4 text-left">Foto</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($absensi as $index => $a)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                <td class="px-6 py-4 font-semibold text-gray-800">{{ $a->user->name }}</td>
                <td class="px-6 py-4">
                    @if($a->type === 'masuk')
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i> Masuk
                    </span>
                    @else
                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Pulang
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($a->status === 'tepat_waktu')
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-circle-check mr-1"></i> Tepat Waktu
                    </span>
                    @else
                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-clock mr-1"></i> Terlambat
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $a->checked_at->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-4">
                    <img src="{{ asset('storage/' . $a->photo) }}"
                        class="w-10 h-10 rounded-full object-cover cursor-pointer border-2 border-blue-300"
                        onclick="Swal.fire({ imageUrl: '{{ asset('storage/' . $a->photo) }}', imageWidth: 300, title: '{{ $a->user->name }}' })">
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                    <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block"></i>
                    Tidak ada data absensi
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection