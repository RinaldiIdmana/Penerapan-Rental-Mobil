<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sistem Informasi Reservasi Armada Rental' }} | RentFleet Pro</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .gradient-brand {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        }
        .gradient-accent {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
    </style>

    @stack('styles')
</head>
<body class="h-full flex flex-col text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ auth()->check() && auth()->user()->hasAdminPrivileges() ? route('admin.dashboard') : route('customer.catalog') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                            <i class="fa-solid fa-car-side text-lg"></i>
                        </div>
                        <div>
                            <span class="text-lg font-extrabold tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">RentFleet<span class="text-blue-600">Pro</span></span>
                            <span class="hidden sm:inline-block text-[10px] font-semibold tracking-wider text-slate-600 uppercase ml-1.5 px-1.5 py-0.5 bg-slate-100 rounded">Armada</span>
                        </div>
                    </a>

                    <!-- Main Navigation Links -->
                    <nav class="hidden md:flex items-center ml-8 space-x-1">
                        @auth
                            @if(auth()->user()->hasAdminPrivileges())
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-chart-pie mr-1.5 text-xs"></i> Dashboard
                                </a>
                                <a href="{{ route('vehicles.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('vehicles.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-car mr-1.5 text-xs"></i> Armada
                                </a>
                                <a href="{{ route('reservations.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reservations.index', 'reservations.show') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-calendar-check mr-1.5 text-xs"></i> Reservasi
                                </a>
                                <a href="{{ route('calendar.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('calendar.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-calendar-days mr-1.5 text-xs"></i> Kalender
                                </a>
                                <a href="{{ route('returns.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('returns.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-rotate-left mr-1.5 text-xs"></i> Pengembalian
                                </a>
                                <a href="{{ route('invoices.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-receipt mr-1.5 text-xs"></i> Invoice
                                </a>
                                <a href="{{ route('maintenances.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('maintenances.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-wrench mr-1.5 text-xs"></i> Servis
                                </a>
                            @else
                                <a href="{{ route('customer.catalog') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('customer.catalog') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-car mr-1.5 text-xs"></i> Katalog Mobil
                                </a>
                                <a href="{{ route('reservations.my') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('reservations.my') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-clock-rotate-left mr-1.5 text-xs"></i> Sewa Saya
                                </a>
                                <a href="{{ route('invoices.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                    <i class="fa-solid fa-receipt mr-1.5 text-xs"></i> Invoice Saya
                                </a>
                            @endif
                        @else
                            <a href="{{ route('customer.catalog') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('customer.catalog') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                                <i class="fa-solid fa-car mr-1.5 text-xs"></i> Katalog Armada
                            </a>
                        @endauth
                    </nav>
                </div>

                <!-- Right Action Bar -->
                <div class="flex items-center gap-3">
                    @auth
                        @if(!auth()->user()->hasAdminPrivileges())
                            <a href="{{ route('reservations.create') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white gradient-brand shadow-sm shadow-blue-500/30 hover:opacity-95 transition-all">
                                <i class="fa-solid fa-plus text-xs"></i> Pesan Mobil
                            </a>
                        @endif

                        <!-- User Profile Pill -->
                        <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
                            <div class="text-right hidden sm:block">
                                <div class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</div>
                                <div class="text-[11px] font-medium text-slate-600 flex items-center justify-end gap-1">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full {{ auth()->user()->isAdmin() ? 'bg-indigo-500' : (auth()->user()->isStaff() ? 'bg-emerald-500' : 'bg-blue-500') }}"></span>
                                    <span class="uppercase tracking-wider font-semibold text-[10px]">{{ auth()->user()->role }}</span>
                                </div>
                            </div>

                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" title="Keluar dari akun" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white gradient-brand shadow-sm shadow-blue-500/25 hover:opacity-95 transition-all">
                            Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alerts -->
    <main class="flex-1 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('success'))
                <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm animate-fade-in" role="alert">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm" role="alert">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 shadow-sm" role="alert">
                    <div class="w-8 h-8 rounded-xl bg-blue-500 text-white flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div class="text-sm font-medium">{{ session('info') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm">
                    <div class="flex items-center gap-2.5 font-bold text-sm text-rose-800 mb-1.5">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span>Mohon periksa kesalahan input berikut:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-rose-700 ml-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-600">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-800">RentFleet Pro</span> &copy; {{ date('Y') }} - Sistem Reservasi & Manajemen Armada Rental Kendaraan.
            </div>
            <div class="flex items-center gap-4 text-slate-600">
                <span>Laravel 12 &bull; MySQL &bull; Clean Architecture</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
