@extends('layouts.app', ['title' => 'Manajemen Armada Kendaraan'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800">Dashboard</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Armada</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Daftar Armada Rental Kendaraan</h1>
            <p class="text-xs text-slate-500">Kelola inventaris armada mobil, tarif sewa harian, serta status operasional.</p>
        </div>

        <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white gradient-brand shadow-md shadow-blue-500/25 hover:opacity-95 transition-all">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Armada Baru
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('vehicles.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="sm:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari plat nomor, merk, atau model..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
            </div>

            <!-- Status -->
            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                    <option value="rented" {{ request('status') === 'rented' ? 'selected' : '' }}>Disewa (Rented)</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Perawatan (Maintenance)</option>
                    <option value="out_of_service" {{ request('status') === 'out_of_service' ? 'selected' : '' }}>Tidak Aktif (Out of Service)</option>
                </select>
            </div>

            <!-- Transmission -->
            <div class="sm:col-span-2">
                <select name="transmission" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
                    <option value="">Semua Transmisi</option>
                    <option value="automatic" {{ request('transmission') === 'automatic' ? 'selected' : '' }}>Automatic</option>
                    <option value="manual" {{ request('transmission') === 'manual' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'transmission']))
                    <a href="{{ route('vehicles.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs" title="Reset Filter">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Vehicles Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Armada</th>
                        <th class="py-3.5 px-4">Plat Nomor</th>
                        <th class="py-3.5 px-4">Tahun & Transmisi</th>
                        <th class="py-3.5 px-4">Kapasitas</th>
                        <th class="py-3.5 px-4">Tarif Sewa / Hari</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-10 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                        @if($vehicle->image_url)
                                            <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <i class="fa-solid fa-car text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                        <div class="text-[11px] text-slate-400">ID: #{{ $vehicle->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800 text-xs">
                                <span class="px-2.5 py-1 bg-slate-100 rounded-lg border border-slate-200">
                                    {{ $vehicle->license_plate }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <div>{{ $vehicle->year }}</div>
                                <div class="text-[11px] uppercase font-semibold text-slate-500">{{ $vehicle->transmission }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                <i class="fa-solid fa-users text-slate-400 mr-1"></i> {{ $vehicle->capacity }} Kursi
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-blue-600 text-sm">
                                {{ $vehicle->formatted_rate }}
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $statusConfig = match($vehicle->status) {
                                        'available' => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'Tersedia'],
                                        'rented' => ['bg-blue-50', 'text-blue-700', 'border-blue-200', 'Disewa'],
                                        'maintenance' => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'Perawatan'],
                                        'out_of_service' => ['bg-rose-50', 'text-rose-700', 'border-rose-200', 'Non-Aktif'],
                                        default => ['bg-slate-50', 'text-slate-700', 'border-slate-200', $vehicle->status]
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusConfig[0] }} {{ $statusConfig[1] }} {{ $statusConfig[2] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $vehicle->status === 'available' ? 'bg-emerald-500' : ($vehicle->status === 'rented' ? 'bg-blue-500' : 'bg-amber-500') }}"></span>
                                    {{ $statusConfig[3] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700" title="Detail Armada">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700" title="Edit Armada">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus armada ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600" title="Hapus Armada">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-500">
                                <div class="text-4xl text-slate-300 mb-2"><i class="fa-solid fa-car-tunnel"></i></div>
                                <div class="font-bold text-sm">Tidak ada data armada ditemukan.</div>
                                <div class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci atau filter pencarian Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
