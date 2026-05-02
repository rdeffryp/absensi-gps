@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    {{-- Tombol Kembali --}}
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('karyawan.dashboard') }}"
            class="text-blue-600 hover:text-blue-700 transition flex items-center gap-2 font-semibold text-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
    <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-2xl shadow p-6 flex items-center gap-5">
        <div class="relative">
            <img src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=ffffff&color=2563eb&size=100' }}"
                class="w-20 h-20 rounded-full object-cover border-4 border-white shadow">
        </div>
        <div>
            <h2 class="text-white font-bold text-xl">{{ $user->name }}</h2>
            <p class="text-blue-100 text-sm">{{ $user->email }}</p>
            <span class="bg-white text-blue-600 text-xs font-bold px-3 py-1 rounded-full mt-2 inline-block">
                {{ $user->role === 'admin' ? 'Administrator' : 'Karyawan' }}
            </span>
        </div>
    </div>

    {{-- Form Update Profil --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="text-gray-800 font-bold text-lg mb-5">
            <i class="fa-solid fa-user-pen mr-2 text-blue-500"></i> Edit Profil
        </h3>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- Foto Profil --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-image mr-1 text-blue-500"></i> Foto Profil
                </label>
                <input type="file" name="photo" accept="image/*"
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            {{-- Nama --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-user mr-1 text-blue-500"></i> Nama Lengkap
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-envelope mr-1 text-blue-500"></i> Email
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- No HP --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-phone mr-1 text-blue-500"></i> No. HP
                </label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    placeholder="Contoh: 08123456789"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            {{-- Departemen --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-building mr-1 text-blue-500"></i> Departemen
                </label>
                <input type="text" name="department" value="{{ old('department', $user->department) }}"
                    placeholder="Contoh: IT, HRD, Finance"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-4 text-sm">
                <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
            </div>
            @endif

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold py-3 rounded-xl transition shadow">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Form Ganti Password --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="text-gray-800 font-bold text-lg mb-5">
            <i class="fa-solid fa-lock mr-2 text-blue-500"></i> Ganti Password
        </h3>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Lama</label>
                <input type="password" name="current_password"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-gray-700 to-gray-600 hover:from-gray-800 hover:to-gray-700 text-white font-bold py-3 rounded-xl transition shadow">
                <i class="fa-solid fa-key mr-2"></i> Ganti Password
            </button>
        </form>
    </div>

</div>

@endsection