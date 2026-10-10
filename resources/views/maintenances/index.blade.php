@extends('layouts.app', ['title' => 'Monitoring Servis Berkala Armada'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800">Dashboard</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-amber-600">Servis Berkala</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Monitoring Servis Berkala Armada</h1>
            <p class="text-xs text-slate-500">Modul 4: Pantau jadwal jatuh tempo perawatan armada & auto-update status armada saat servis.</p>
        </div>

        <a href="{{ route('maintenances.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 shadow-md shadow-amber-500/20 transition-all">
            <i class="fa-solid fa-plus text-xs"></i> Jadwalkan Servis Baru
        </a>
    </div>

    <!-- Alert / Due Badge Metric Highlights -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <a href="{{ route('maintenances.index', ['filter' => 'overdue']) }}" class="p-4 rounded-2xl border {{ request('filter') === 'overdue' ? 'bg-rose-100 border-rose-300' : 'bg-rose-50 border-rose-200' }} hover:shadow-sm transition-all flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="font-bold text-rose-900">Lewat Jatuh Tempo (Overdue)</div>
                    <div class="text-[11px] text-rose-700">Segera jadwalkan bengkel</div>
                </div>
            </div>
            <div class="text-2xl font-black text-rose-700">{{ $overdueCount }}</div>
        </a>

        <a href="{{ route('maintenances.index', ['filter' => 'due_soon']) }}" class="p-4 rounded-2xl border {{ request('filter') === 'due_soon' ? 'bg-amber-100 border-amber-300' : 'bg-amber-50 border-amber-200' }} hover:shadow-sm transition-all flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div>
                    <div class="font-bold text-amber-900">Mendekati Jatuh Tempo (&le;7 Hari)</div>
                    <div class="text-[11px] text-amber-700">Perlu dipersiapkan</div>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-700">{{ $dueSoonCount }}</div>
        </a>

        <a href="{{ route('maintenances.index') }}" class="p-4 rounded-2xl border {{ !request('filter') ? 'bg-blue-100 border-blue-300' : 'bg-blue-50 border-blue-200' }} hover:shadow-sm transition-all flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <div class="font-bold text-blue-900">Semua Catatan Servis</div>
                    <div class="text-[11px] text-blue-700">Riwayat & jadwal perawatan</div>
                </div>
            </div>
            <div class="text-2xl font-black text-blue-700">{{ $maintenances->total() }}</div>
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('maintenances.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Vehicle -->
            <div class="sm:col-span-5">
                <select name="vehicle_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-100 focus:border-amber-500 outline-none">
                    <option value="">-- Semua Armada Mobil --</option>
                    @foreach($vehicles as $veh)
                        <option value="{{ $veh->id }}" {{ request('vehicle_id') == $veh->id ? 'selected' : '' }}>
                            {{ $veh->license_plate }} &bull; {{ $veh->brand }} {{ $veh->model }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="sm:col-span-4">
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-100 focus:border-amber-500 outline-none">
                    <option value="">Semua Status Pengerjaan</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal (Scheduled)</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Servis (In Progress)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                </select>
            </div>

            <!-- Action -->
            <div class="sm:col-span-3 flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                    Filter Servis
                </button>
                @if(request()->anyFilled(['vehicle_id', 'status', 'filter']))
                    <a href="{{ route('maintenances.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs" title="Reset">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Maintenances Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Armada</th>
                        <th class="py-3.5 px-4">Jenis Pemeliharaan</th>
                        <th class="py-3.5 px-4">Tgl Servis</th>
                        <th class="py-3.5 px-4">Jatuh Tempo Berikutnya (Due Date)</th>
                        <th class="py-3.5 px-4">Biaya Perawatan</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($maintenances as $maint)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-slate-900 px-2 py-0.5 rounded bg-slate-100 border border-slate-200">
                                    {{ $maint->vehicle->license_plate }}
                                </span>
                                <div class="text-[11px] text-slate-600 font-medium mt-1">{{ $maint->vehicle->brand }} {{ $maint->vehicle->model }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-sm">{{ $maint->service_type }}</div>
                                @if($maint->description)
                                    <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $maint->description }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                {{ $maint->service_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $maint->next_service_due_date->format('d/m/Y') }}</div>
                                @if($maint->status !== 'completed')
                                    @if($maint->is_overdue)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 mt-0.5">
                                            Lewat Jatuh Tempo
                                        </span>
                                    @elseif($maint->days_until_due <= 7)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 mt-0.5">
                                            {{ $maint->days_until_due }} Hari Lagi
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $maint->formatted_cost }}
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $maintStatus = match($maint->status) {
                                        'completed' => ['bg-emerald-50 text-emerald-800 border-emerald-200', 'Selesai'],
                                        'in_progress' => ['bg-blue-50 text-blue-800 border-blue-200', 'Sedang Dikerjakan'],
                                        'scheduled' => ['bg-amber-50 text-amber-800 border-amber-200', 'Terjadwal'],
                                        default => ['bg-slate-50 text-slate-800 border-slate-200', $maint->status]
                                    };
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $maintStatus[0] }}">
                                    {{ $maintStatus[1] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($maint->status !== 'completed')
                                        <form action="{{ route('maintenances.complete', $maint) }}" method="POST" class="inline" onsubmit="return confirm('Tandai servis selesai dan pulihkan status armada?');">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700" title="Tandai Selesai Servis">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('maintenances.edit', $maint) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700" title="Edit Jadwal Servis">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>

                                    <form action="{{ route('maintenances.destroy', $maint) }}" method="POST" class="inline" onsubmit="return confirm('Hapus catatan servis ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600" title="Hapus">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-500">
                                <i class="fa-solid fa-wrench text-4xl mb-2 text-slate-300"></i>
                                <div class="font-bold text-sm">Belum ada jadwal servis yang sesuai filter.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($maintenances->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $maintenances->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
