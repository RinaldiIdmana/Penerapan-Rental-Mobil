@extends('layouts.app', ['title' => 'Katalog Armada Rental Mobil'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Hero Banner -->
    <div class="gradient-brand rounded-3xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="max-w-xl z-10">
            <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-white/20 text-white backdrop-blur-md">
                Armada Bersih, Nyaman, & Terawat
            </span>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight mt-3 mb-2 leading-tight">
                Sewa Mobil Mudah, Cepat & Terpercaya
            </h1>
            <p class="text-blue-100 text-sm leading-relaxed mb-6">
                Pilih kendaraan terbaik sesuai kebutuhan bisnis, wisata keluarga, atau mobilitas harian Anda dengan proses reservasi instan.
            </p>
            <div class="flex items-center gap-4 text-xs font-bold text-blue-200">
                <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> Servis Terjadwal</div>
                <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> Asuransi Siap</div>
                <div class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-400"></i> Invoice Otomatis</div>
            </div>
        </div>

        <div class="hidden lg:block z-10 w-72 h-44 rounded-2xl bg-white/10 backdrop-blur-md p-4 border border-white/20 text-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-blue-200 mb-2">Mengapa Memilih Kami?</div>
            <ul class="space-y-2 text-blue-50">
                <li class="flex items-center gap-2"><i class="fa-solid fa-car text-blue-300"></i> Unit tahun muda & prima</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-calendar-check text-blue-300"></i> Kalender real-time tanpa overlap</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-file-pdf text-blue-300"></i> Unduh Invoice PDF instan</li>
            </ul>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('customer.catalog') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <!-- Brand -->
            <div>
                <label for="brand" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Merk Mobil</label>
                <select name="brand" id="brand" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
                    <option value="">Semua Merk</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}" {{ request('brand') === $brand ? 'selected' : '' }}>{{ $brand }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Transmission -->
            <div>
                <label for="transmission" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Transmisi</label>
                <select name="transmission" id="transmission" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
                    <option value="">Semua Transmisi</option>
                    <option value="automatic" {{ request('transmission') === 'automatic' ? 'selected' : '' }}>Automatic</option>
                    <option value="manual" {{ request('transmission') === 'manual' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>

            <!-- Capacity -->
            <div>
                <label for="capacity" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Min. Kursi</label>
                <select name="capacity" id="capacity" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
                    <option value="">Semua Kapasitas</option>
                    <option value="5" {{ request('capacity') == '5' ? 'selected' : '' }}>5 Penumpang</option>
                    <option value="7" {{ request('capacity') == '7' ? 'selected' : '' }}>7 Penumpang</option>
                </select>
            </div>

            <!-- Action -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl font-bold text-xs text-white gradient-brand hover:opacity-95 transition-all">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['brand', 'transmission', 'capacity']))
                    <a href="{{ route('customer.catalog') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs" title="Reset">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Vehicles Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($vehicles as $vehicle)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-lg transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                <div>
                    <!-- Vehicle Image Container -->
                    <div class="h-48 bg-slate-100 relative overflow-hidden">
                        @if($vehicle->image_url)
                            <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 text-4xl">
                                <i class="fa-solid fa-car-side"></i>
                            </div>
                        @endif

                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-white/90 backdrop-blur-md text-slate-800 shadow-sm">
                                {{ $vehicle->license_plate }}
                            </span>
                        </div>

                        <div class="absolute top-3 right-3">
                            @if($vehicle->status === 'available')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500 text-white shadow-sm flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span> Siap Sewa
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-600 text-white shadow-sm">
                                    Sedang Disewa
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details Body -->
                    <div class="p-5">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-blue-600">{{ $vehicle->brand }}</div>
                        <h3 class="text-lg font-black text-slate-900 mt-0.5">{{ $vehicle->model }}</h3>

                        <!-- Pills Specs -->
                        <div class="flex flex-wrap items-center gap-2 mt-3 text-[11px] text-slate-600">
                            <span class="px-2 py-1 rounded-lg bg-slate-100 font-medium">
                                <i class="fa-solid fa-calendar text-slate-400 mr-1"></i> {{ $vehicle->year }}
                            </span>
                            <span class="px-2 py-1 rounded-lg bg-slate-100 font-medium uppercase">
                                <i class="fa-solid fa-gears text-slate-400 mr-1"></i> {{ $vehicle->transmission }}
                            </span>
                            <span class="px-2 py-1 rounded-lg bg-slate-100 font-medium">
                                <i class="fa-solid fa-users text-slate-400 mr-1"></i> {{ $vehicle->capacity }} Kursi
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Price & Book Button -->
                <div class="p-5 pt-0 border-t border-slate-100/80 mt-4 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tarif Sewa</div>
                        <div class="text-base font-black text-blue-600">{{ $vehicle->formatted_rate }} <span class="text-xs text-slate-400 font-normal">/hari</span></div>
                    </div>

                    @auth
                        <a href="{{ route('reservations.create', ['vehicle_id' => $vehicle->id]) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white gradient-brand hover:opacity-95 shadow-md shadow-blue-500/20 transition-all">
                            Pesan Unit <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white gradient-brand hover:opacity-95 shadow-md shadow-blue-500/20 transition-all">
                            Masuk & Sewa <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    @endauth
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400">
                <i class="fa-solid fa-car-burst text-4xl mb-2 text-slate-300"></i>
                <div class="text-sm font-bold text-slate-700">Tidak ada armada yang sesuai filter saat ini.</div>
                <div class="text-xs text-slate-400 mt-1">Silakan coba ganti kriteria pencarian Anda.</div>
            </div>
        @endforelse
    </div>

    @if($vehicles->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $vehicles->links() }}
        </div>
    @endif

</div>
@endsection
