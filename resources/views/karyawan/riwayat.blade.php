@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('karyawan.dashboard') }}" class="text-blue-600 hover:text-blue-700 transition">
            <i class="fa-solid fa-arrow-left text-xl"></i>
        </a>
        <h2 class="text-gray-800 font-bold text-xl">Riwayat Absensi</h2>
    </div>

    {{-- List Riwayat --}}
    <div class="space-y-4">
        @forelse($riwayat as $r)
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">

            {{-- Foto --}}
            <img src="{{ asset('storage/' . $r->photo) }}"
                class="w-14 h-14 rounded-full object-cover border-2 border-blue-300 cursor-pointer flex-shrink-0"
                onclick="Swal.fire({ imageUrl: '{{ asset('storage/' . $r->photo) }}', imageWidth: 300 })">

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    @if($r->type === 'masuk')
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i> Masuk
                    </span>
                    @else
                    <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Pulang
                    </span>
                    @endif

                    @if($r->status === 'tepat_waktu')
                    <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-circle-check mr-1"></i> Tepat Waktu
                    </span>
                    @else
                    <span class="bg-red-100 text-red-500 px-2 py-0.5 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-clock mr-1"></i> Terlambat
                    </span>
                    @endif
                </div>
                <p class="text-gray-800 font-semibold text-sm">{{ $r->checked_at->isoFormat('dddd, D MMMM Y') }}</p>
                <p class="text-gray-500 text-xs">{{ $r->checked_at->format('H:i') }} WIB</p>
            </div>

        </div>
        @empty
        <div class="bg-white rounded-2xl shadow p-10 text-center text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block"></i>
            <p>Belum ada riwayat absensi</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $riwayat->links() }}
    </div>

</div>

@endsection