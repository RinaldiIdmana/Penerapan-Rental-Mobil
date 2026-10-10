@extends('layouts.app', ['title' => 'Manajemen Reservasi Armada'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800">Dashboard</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Reservasi</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Daftar Transaksi Reservasi Armada</h1>
            <p class="text-xs text-slate-500">Kelola konfirmasi pemesanan, verifikasi tanggal sewa, dan proses serah terima armada.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('calendar.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-all shadow-sm">
                <i class="fa-solid fa-calendar-days text-blue-400"></i> Kalender Ketersediaan
            </a>
            <a href="{{ route('reservations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white gradient-brand shadow-md shadow-blue-500/25 hover:opacity-95 transition-all">
                <i class="fa-solid fa-plus text-xs"></i> Buat Reservasi
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('reservations.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="sm:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kode reservasi, nama pelanggan, atau plat nomor armada..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
            </div>

            <!-- Status -->
            <div class="sm:col-span-4">
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
                    <option value="">Semua Status Reservasi</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed (Terkonfirmasi)</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Sedang Berjalan)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled (Batal)</option>
                </select>
            </div>

            <!-- Action -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('reservations.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs" title="Reset">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Reservations Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Kode Reservasi</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Armada</th>
                        <th class="py-3.5 px-4">Rentang Tanggal Sewa</th>
                        <th class="py-3.5 px-4">Durasi & Biaya</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reservations as $res)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-600 text-xs">
                                <a href="{{ route('reservations.show', $res) }}" class="hover:underline">
                                    {{ $res->reservation_code }}
                                </a>
                                @if($res->invoice)
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $res->invoice->invoice_number }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $res->user->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $res->user->phone ?? $res->user->email }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $res->vehicle->license_plate }}</div>
                                <div class="text-slate-500 text-[11px]">{{ $res->vehicle->brand }} {{ $res->vehicle->model }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <div>{{ $res->start_date->format('d M Y') }} &rarr; {{ $res->end_date->format('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900">{{ $res->formatted_base_amount }}</div>
                                <div class="text-[11px] text-slate-500">{{ $res->total_days }} Hari Sewa</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $res->status_badge }}">
                                    {{ ucfirst($res->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Detail -->
                                    <a href="{{ route('reservations.show', $res) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700" title="Detail Reservasi">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    <!-- Confirm Action -->
                                    @if($res->status === 'pending')
                                        <form action="{{ route('reservations.confirm', $res) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700" title="Konfirmasi Reservasi">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Return Action (Modul 3) -->
                                    @if(in_array($res->status, ['confirmed', 'active']))
                                        <a href="{{ route('returns.create', $res) }}" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold" title="Proses Pengembalian">
                                            <i class="fa-solid fa-rotate-left text-xs"></i>
                                        </a>
                                    @endif

                                    <!-- Invoice PDF (Modul 2) -->
                                    @if($res->invoice)
                                        <a href="{{ route('invoices.download', $res->invoice) }}" class="p-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700" title="Unduh Invoice PDF">
                                            <i class="fa-solid fa-file-pdf text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-500">
                                <i class="fa-solid fa-calendar-xmark text-4xl mb-2 text-slate-300"></i>
                                <div class="font-bold text-sm">Tidak ada data reservasi ditemukan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservations->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
