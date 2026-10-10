@extends('layouts.app', ['title' => 'Detail Reservasi: ' . $reservation->reservation_code])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ auth()->user()->hasAdminPrivileges() ? route('reservations.index') : route('reservations.my') }}" class="text-xs text-slate-500 hover:text-slate-800">
                    {{ auth()->user()->hasAdminPrivileges() ? 'Daftar Reservasi' : 'Sewa Saya' }}
                </a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600 font-mono">{{ $reservation->reservation_code }}</span>
            </div>
            <div class="flex items-center gap-3 mt-1">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight font-mono">{{ $reservation->reservation_code }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-extrabold border {{ $reservation->status_badge }}">
                    {{ ucfirst($reservation->status) }}
                </span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Confirm button (Admin only) -->
            @if(auth()->user()->hasAdminPrivileges() && $reservation->status === 'pending')
                <form action="{{ route('reservations.confirm', $reservation) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition-colors flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-check"></i> Konfirmasi Reservasi
                    </button>
                </form>
            @endif

            <!-- Process Return (Admin only) -->
            @if(auth()->user()->hasAdminPrivileges() && in_array($reservation->status, ['confirmed', 'active']))
                <a href="{{ route('returns.create', $reservation) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition-colors flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-rotate-left"></i> Proses Pengembalian Armada
                </a>
            @endif

            <!-- Cancel button -->
            @if(in_array($reservation->status, ['pending', 'confirmed']))
                <form action="{{ route('reservations.cancel', $reservation) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors">
                        <i class="fa-solid fa-xmark mr-1"></i> Batalkan
                    </button>
                </form>
            @endif

            <!-- Invoice Link -->
            @if($reservation->invoice)
                <a href="{{ route('invoices.show', $reservation->invoice) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-receipt"></i> Lihat Invoice
                </a>
                <a href="{{ route('invoices.download', $reservation->invoice) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-file-pdf"></i> Unduh PDF
                </a>
            @endif
        </div>
    </div>

    <!-- 2 Columns Grid: Reservation Summary & Vehicle Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1: Details & Invoice Summary (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Rental Details Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-6">
                <h2 class="text-base font-black text-slate-900 pb-3 border-b border-slate-100">
                    Informasi Penyewaan
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="text-slate-400 font-medium">Tanggal Mulai</div>
                        <div class="text-sm font-bold text-slate-800 mt-1">{{ $reservation->start_date->translatedFormat('l, d F Y') }}</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="text-slate-400 font-medium">Tanggal Selesai (Rencana)</div>
                        <div class="text-sm font-bold text-slate-800 mt-1">{{ $reservation->end_date->translatedFormat('l, d F Y') }}</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 text-blue-900">
                        <div class="text-blue-600 font-medium">Durasi Sewa</div>
                        <div class="text-base font-black mt-1">{{ $reservation->total_days }} Hari</div>
                    </div>
                </div>

                @if($reservation->notes)
                    <div class="p-4 rounded-2xl bg-slate-50 text-xs">
                        <span class="font-bold text-slate-700">Catatan Pemesan:</span>
                        <p class="text-slate-600 mt-1 italic">"{{ $reservation->notes }}"</p>
                    </div>
                @endif
            </div>

            <!-- Return Status Card (If already returned) -->
            @if($reservation->returnRecord)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-rotate-left text-emerald-600"></i> Data Pengembalian Armada
                        </h2>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Unit Sudah Dikembalikan
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-3.5 rounded-2xl bg-slate-50">
                            <div class="text-slate-400">Tanggal Pengembalian</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $reservation->returnRecord->return_date->format('d/m/Y') }}</div>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50">
                            <div class="text-slate-400">Keterlambatan</div>
                            <div class="text-sm font-bold {{ $reservation->returnRecord->late_days > 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-0.5">
                                {{ $reservation->returnRecord->late_days > 0 ? $reservation->returnRecord->late_days . ' Hari Telat' : 'Tepat Waktu' }}
                            </div>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50">
                            <div class="text-slate-400">Total Denda</div>
                            <div class="text-sm font-bold text-rose-600 mt-0.5">{{ $reservation->returnRecord->formatted_total_fine }}</div>
                        </div>
                    </div>

                    @if($reservation->returnRecord->condition_notes)
                        <div class="p-3.5 rounded-2xl bg-slate-50 text-xs">
                            <span class="font-bold text-slate-700">Catatan Kondisi Pengembalian:</span>
                            <p class="text-slate-600 mt-0.5">{{ $reservation->returnRecord->condition_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Invoice Breakdown Card -->
            @if($reservation->invoice)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-black text-slate-900">Rincian Invoice Resmi</h2>
                            <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $reservation->invoice->invoice_number }}</div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $reservation->invoice->payment_status_badge }}">
                            Status: {{ ucfirst($reservation->invoice->payment_status) }}
                        </span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between py-1 text-slate-600">
                            <span>Biaya Pokok Sewa ({{ $reservation->total_days }} Hari &times; {{ $reservation->vehicle->formatted_rate }}):</span>
                            <span class="font-bold text-slate-800">{{ $reservation->invoice->formatted_subtotal }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 text-slate-600">
                            <span>Denda Keterlambatan:</span>
                            <span class="font-bold {{ $reservation->invoice->fine_amount > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ $reservation->invoice->formatted_fine_amount }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-200 text-sm">
                            <span class="font-extrabold text-slate-900">Grand Total Tagihan:</span>
                            <span class="text-lg font-black text-blue-600">{{ $reservation->invoice->formatted_grand_total }}</span>
                        </div>
                    </div>

                    @if(auth()->user()->hasAdminPrivileges() && $reservation->invoice->payment_status !== 'paid')
                        <div class="pt-3 border-t border-slate-100 flex justify-end">
                            <form action="{{ route('invoices.mark-paid', $reservation->invoice) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition-colors flex items-center gap-1.5">
                                    <i class="fa-solid fa-check-double"></i> Tandai Lunas (Telah Dibayar)
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- Column 2: Vehicle & Customer Info (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Vehicle Summary Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="h-40 bg-slate-100 relative">
                    @if($reservation->vehicle->image_url)
                        <img src="{{ $reservation->vehicle->image_url }}" alt="{{ $reservation->vehicle->model }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-4xl">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                    @endif
                    <div class="absolute bottom-3 left-3">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-white/90 text-slate-900 shadow-sm">
                            {{ $reservation->vehicle->license_plate }}
                        </span>
                    </div>
                </div>

                <div class="p-5 text-xs space-y-3">
                    <div>
                        <div class="text-[10px] font-bold uppercase text-blue-600">{{ $reservation->vehicle->brand }}</div>
                        <h3 class="text-base font-black text-slate-900">{{ $reservation->vehicle->model }}</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-slate-400">Tahun:</span>
                            <span class="font-bold text-slate-700 ml-1">{{ $reservation->vehicle->year }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400">Transmisi:</span>
                            <span class="font-bold text-slate-700 ml-1 uppercase">{{ $reservation->vehicle->transmission }}</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('vehicles.show', $reservation->vehicle) }}" class="block text-center w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                            Lihat Detail Armada
                        </a>
                    </div>
                </div>
            </div>

            <!-- Customer Summary Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-5 text-xs space-y-3">
                <h3 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] pb-2 border-b border-slate-100">
                    Data Penyewa
                </h3>
                <div>
                    <div class="text-slate-400">Nama Lengkap:</div>
                    <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $reservation->user->name }}</div>
                </div>
                <div>
                    <div class="text-slate-400">Email:</div>
                    <div class="font-medium text-slate-800 mt-0.5">{{ $reservation->user->email }}</div>
                </div>
                <div>
                    <div class="text-slate-400">Nomor Telepon:</div>
                    <div class="font-medium text-slate-800 mt-0.5">{{ $reservation->user->phone ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-slate-400">Nomor SIM:</div>
                    <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $reservation->user->driver_license_number ?? '-' }}</div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
