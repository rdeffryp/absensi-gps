<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - @yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- Overlay Mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-blue-800 to-blue-600 shadow-xl z-30 transition-transform duration-300 lg:translate-x-0">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-blue-500">
            <div class="bg-white rounded-full p-2">
                <i class="fa-solid fa-fingerprint text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-white font-bold text-lg">AbsensiGPS</p>
                <p class="text-blue-200 text-xs">Panel Admin</p>
            </div>
        </div>

        {{-- Menu --}}
        <nav class="px-4 py-6 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                {{ request()->routeIs('admin.dashboard') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
                <i class="fa-solid fa-chart-line w-5 text-center"></i> Dashboard
            </a>
            <a href="{{ route('admin.karyawan') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                {{ request()->routeIs('admin.karyawan') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
                <i class="fa-solid fa-users w-5 text-center"></i> Karyawan
            </a>
            <a href="{{ route('admin.rekap') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                {{ request()->routeIs('admin.rekap') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
                <i class="fa-solid fa-calendar-check w-5 text-center"></i> Rekap Absensi
            </a>
            <a href="{{ route('admin.settings') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                {{ request()->routeIs('admin.settings') ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-blue-700' }}">
                <i class="fa-solid fa-gear w-5 text-center"></i> Pengaturan
            </a>
        </nav>

        {{-- User Info --}}
        <div class="absolute bottom-0 left-0 right-0 px-4 py-4 border-t border-blue-500">
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.index') }}">
                    <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=ffffff&color=2563eb&size=40' }}"
                        class="w-9 h-9 rounded-full object-cover border-2 border-white">
                </a>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-blue-200 text-xs">Administrator</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-blue-200 hover:text-white transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">

        {{-- Topbar --}}
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-blue-600">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h1 class="text-gray-800 font-bold text-lg">@yield('title')</h1>
            </div>
            <div class="text-gray-500 text-sm hidden sm:block">
                <i class="fa-regular fa-clock mr-1"></i>
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>