@extends('layouts.app', ['title' => 'Dashboard Manajemen Armada'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700">
                    <i class="fa-solid fa-gauge-high mr-1"></i> Control Panel
                </span>
                <span class="text-xs text-slate-600">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Sistem Manajemen & Reservasi Armada</h1>
            <p class="text-xs text-slate-600">Pantau ketersediaan armada, status reservasi, dan pemeliharaan servis secara real-time.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('calendar.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-all shadow-sm">
                <i class="fa-solid fa-calendar-days text-sm text-blue-400"></i> Kalender Ketersediaan
            </a>
            <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white gradient-brand hover:opacity-95 shadow-md shadow-blue-500/20 transition-all">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Armada
            </a>
            <a href="{{ route('maintenances.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-amber-500 text-white hover:bg-amber-600 transition-all shadow-sm">
                <i class="fa-solid fa-wrench text-xs"></i> Catat Servis
            </a>
        </div>
    </div>

    <!-- Modul 4: Alert Banner Monitoring Servis Berkala -->
    @if($maintenanceAlerts->count() > 0)
        <div class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-500/30">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-amber-900">
                            Peringatan Pemeliharaan Servis Berkala ({{ $maintenanceAlerts->count() }} Armada)
                        </h2>
                        <p class="text-xs text-amber-700 mt-0.5">
                            Terdapat kendaraan yang mendekati atau telah melewati batas jadwal jatuh tempo servis (Next Service Due Date).
                        </p>
                    </div>
                </div>
                <a href="{{ route('maintenances.index') }}" class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-colors shrink-0">
                    Buka Modul Servis <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Badges List -->
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($maintenanceAlerts as $alert)
                    <div class="bg-white/80 backdrop-blur rounded-2xl p-3 border border-amber-200 flex items-center justify-between gap-2 shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full {{ $alert->is_overdue ? 'bg-rose-500 animate-ping' : 'bg-amber-500' }}"></span>
                            <div>
                                <div class="text-xs font-black text-slate-800">{{ $alert->vehicle->license_plate }} &bull; {{ $alert->vehicle->model }}</div>
                                <div class="text-[11px] text-slate-500">{{ $alert->service_type }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($alert->is_overdue)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    LEWAT JATUH TEMPO
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ $alert->days_until_due }} Hari Lagi
                                </span>
                            @endif
                            <div class="text-[10px] text-slate-600 mt-0.5">{{ Carbon\Carbon::parse($alert->next_service_due_date)->format('d/m/Y') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Key Metrics 4-Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Fleet -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">Total Armada</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalVehicles }}</div>
                <div class="flex items-center gap-1.5 mt-2 text-[11px]">
                    <span class="text-emerald-700 font-bold">{{ $availableVehicles }} Siap Sewa</span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-amber-700 font-medium">{{ $maintenanceVehicles }} Servis</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-car-side"></i>
            </div>
        </div>

        <!-- Metric 2: Active Rentals -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">Sedang Disewa</div>
                <div class="text-2xl font-black text-emerald-700 mt-1">{{ $rentedVehicles }}</div>
                <div class="flex items-center gap-1.5 mt-2 text-[11px] text-slate-600">
                    <span>{{ $activeReservations }} Reservasi Aktif Jalan</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-key"></i>
            </div>
        </div>

        <!-- Metric 3: Pending Reservations -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">Menunggu Konfirmasi</div>
                <div class="text-2xl font-black text-amber-700 mt-1">{{ $pendingReservations }}</div>
                <div class="flex items-center gap-1.5 mt-2 text-[11px] text-slate-600">
                    <span>{{ $confirmedReservations }} Terkonfirmasi Siap Ambil</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Metric 4: Total Revenue -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">Pendapatan Lunas</div>
                <div class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="flex items-center gap-1 mt-2 text-[11px] text-slate-600">
                    <span>Unpaid: Rp {{ number_format($unpaidInvoicesSum, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Main Content 2-Columns: Recent Reservations & Recent Returns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1: Recent Reservations (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-black text-slate-900">Reservasi Terbaru</h2>
                    <p class="text-xs text-slate-600">Daftar transaksi penyewaan armada paling mutakhir</p>
                </div>
                <a href="{{ route('reservations.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3 px-2">Kode / Pelanggan</th>
                            <th class="py-3 px-2">Armada</th>
                            <th class="py-3 px-2">Periode Sewa</th>
                            <th class="py-3 px-2">Total Biaya</th>
                            <th class="py-3 px-2">Status</th>
                            <th class="py-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentReservations as $res)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-2">
                                    <div class="font-mono font-bold text-blue-600">{{ $res->reservation_code }}</div>
                                    <div class="text-slate-800 font-medium">{{ $res->user->name }}</div>
                                </td>
                                <td class="py-3 px-2">
                                    <div class="font-bold text-slate-800">{{ $res->vehicle->license_plate }}</div>
                                    <div class="text-slate-600">{{ $res->vehicle->brand }} {{ $res->vehicle->model }}</div>
                                </td>
                                <td class="py-3 px-2 text-slate-700">
                                    <div>{{ $res->start_date->format('d/m/Y') }} - {{ $res->end_date->format('d/m/Y') }}</div>
                                    <div class="text-slate-600 font-medium">{{ $res->total_days }} Hari</div>
                                </td>
                                <td class="py-3 px-2 font-bold text-slate-900">
                                    {{ $res->formatted_base_amount }}
                                </td>
                                <td class="py-3 px-2">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $res->status_badge }}">
                                        {{ ucfirst($res->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('reservations.show', $res) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700" title="Detail">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        @if(in_array($res->status, ['confirmed', 'active']))
                                            <a href="{{ route('returns.create', $res) }}" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold" title="Proses Pengembalian">
                                                <i class="fa-solid fa-rotate-left text-xs"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-slate-600">Belum ada data reservasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Column 2: Recent Returns & Late Fines (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Pengembalian & Denda</h2>
                        <p class="text-xs text-slate-600">Modul 3: Log denda keterlambatan</p>
                    </div>
                    <a href="{{ route('returns.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                        Lihat <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentReturns as $ret)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center justify-between font-bold">
                                <span class="text-slate-800">{{ $ret->reservation->vehicle->license_plate }}</span>
                                <span class="font-mono text-blue-600">{{ $ret->reservation->reservation_code }}</span>
                            </div>
                            <div class="text-slate-600 text-[11px] mt-0.5">
                                Kembali: {{ $ret->return_date->format('d/m/Y') }}
                            </div>

                            @if($ret->late_days > 0)
                                <div class="mt-2 flex items-center justify-between p-2 rounded-xl bg-rose-50 border border-rose-100 text-rose-800">
                                    <span class="font-bold"><i class="fa-solid fa-clock text-rose-500 mr-1"></i> Telat {{ $ret->late_days }} Hari</span>
                                    <span class="font-black">Denda: {{ $ret->formatted_total_fine }}</span>
                                </div>
                            @else
                                <div class="mt-2 flex items-center justify-between p-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-[11px]">
                                    <span><i class="fa-solid fa-check text-emerald-500 mr-1"></i> Tepat Waktu</span>
                                    <span class="font-semibold">Tanpa Denda</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-600 text-xs">
                            Belum ada riwayat pengembalian armada.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Calendar Banner -->
            <div class="mt-6 p-4 rounded-2xl gradient-brand text-white flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold">Cek Ketersediaan Tanggal</div>
                    <div class="text-[11px] text-blue-100">Validasi anti-overlapping sistem</div>
                </div>
                <a href="{{ route('calendar.index') }}" class="px-3 py-1.5 bg-white text-blue-700 rounded-xl text-xs font-bold hover:bg-blue-50 transition-colors">
                    Buka Kalender
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
