@extends('layouts.app', ['title' => 'Detail Armada: ' . $vehicle->license_plate])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('vehicles.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Armada</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">{{ $vehicle->license_plate }}</span>
            </div>
            <div class="flex items-center gap-3 mt-1">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                <span class="px-3 py-1 font-mono font-bold text-sm bg-slate-100 rounded-xl border border-slate-200 text-slate-800">
                    {{ $vehicle->license_plate }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reservations.create', ['vehicle_id' => $vehicle->id]) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white gradient-brand shadow-sm shadow-blue-500/20 hover:opacity-95 transition-all">
                <i class="fa-solid fa-calendar-plus mr-1"></i> Buat Reservasi
            </a>
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition-colors">
                <i class="fa-solid fa-pen mr-1"></i> Edit
            </a>
            <a href="{{ route('maintenances.create', ['vehicle_id' => $vehicle->id]) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white transition-colors">
                <i class="fa-solid fa-wrench mr-1"></i> Input Servis
            </a>
        </div>
    </div>

    <!-- Main Grid: Info Sheet & Specs -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Image & Specs (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="h-56 bg-slate-100 relative overflow-hidden">
                    @if($vehicle->image_url)
                        <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-5xl">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                    @endif
                    <div class="absolute top-3 right-3">
                        @php
                            $statusConfig = match($vehicle->status) {
                                'available' => ['bg-emerald-500', 'Tersedia'],
                                'rented' => ['bg-blue-600', 'Sedang Disewa'],
                                'maintenance' => ['bg-amber-500', 'Dalam Perawatan'],
                                'out_of_service' => ['bg-rose-500', 'Non-Aktif'],
                                default => ['bg-slate-500', $vehicle->status]
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold text-white {{ $statusConfig[0] }} shadow-md">
                            {{ $statusConfig[1] }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tarif Sewa Harian</span>
                        <span class="text-xl font-black text-blue-600">{{ $vehicle->formatted_rate }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 text-xs">
                        <div class="p-3 rounded-2xl bg-slate-50">
                            <div class="text-slate-400 font-medium">Tahun Pembuatan</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $vehicle->year }}</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50">
                            <div class="text-slate-400 font-medium">Tipe Transmisi</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5 uppercase">{{ $vehicle->transmission }}</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50">
                            <div class="text-slate-400 font-medium">Kapasitas Muat</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $vehicle->capacity }} Penumpang</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50">
                            <div class="text-slate-400 font-medium">Total Reservasi</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $vehicle->reservations->count() }} Kali</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Maintenance History & Reservations (7 cols) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Maintenance History -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Riwayat Servis & Pemeliharaan</h2>
                        <p class="text-xs text-slate-500">Log servis berkala armada ini</p>
                    </div>
                    <a href="{{ route('maintenances.create', ['vehicle_id' => $vehicle->id]) }}" class="text-xs font-bold text-amber-600 hover:text-amber-800">
                        + Catat Servis
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($vehicle->maintenances as $maint)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-bold text-slate-800">{{ $maint->service_type }}</div>
                                <div class="text-slate-500 text-[11px] mt-0.5">
                                    Servis: {{ $maint->service_date->format('d/m/Y') }} &bull; Biaya: {{ $maint->formatted_cost }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $maint->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($maint->status) }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1">Due: {{ $maint->next_service_due_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada riwayat servis untuk armada ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Riwayat Reservasi Terakhir</h2>
                        <p class="text-xs text-slate-500">Penggunaan sewa armada oleh pelanggan</p>
                    </div>
                </div>

                <div class="space-y-2.5">
                    @forelse($vehicle->reservations as $res)
                        <div class="p-3 rounded-2xl bg-slate-50 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-mono font-bold text-blue-600">{{ $res->reservation_code }}</span>
                                <div class="text-slate-700 font-medium mt-0.5">{{ $res->user->name }}</div>
                                <div class="text-slate-400 text-[11px]">{{ $res->start_date->format('d/m/Y') }} s/d {{ $res->end_date->format('d/m/Y') }} ({{ $res->total_days }} hari)</div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $res->status_badge }}">
                                    {{ ucfirst($res->status) }}
                                </span>
                                <div class="font-extrabold text-slate-800 mt-1">{{ $res->formatted_base_amount }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada reservasi untuk armada ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
