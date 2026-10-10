@extends('layouts.app', ['title' => 'Riwayat Sewa Armada Saya'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Area Pelanggan</span>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">Riwayat Pemesanan & Sewa Saya</h1>
            <p class="text-xs text-slate-500">Pantau status konfirmasi armada, unduh invoice resmi, dan cek riwayat pengembalian.</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white gradient-brand shadow-md shadow-blue-500/25 hover:opacity-95 transition-all inline-flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Pesan Mobil Baru
        </a>
    </div>

    <!-- Reservations List -->
    <div class="space-y-4">
        @forelse($reservations as $res)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-14 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                        @if($res->vehicle->image_url)
                            <img src="{{ $res->vehicle->image_url }}" alt="{{ $res->vehicle->model }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <i class="fa-solid fa-car"></i>
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="font-mono font-bold text-blue-600 text-sm">{{ $res->reservation_code }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $res->status_badge }}">
                                {{ ucfirst($res->status) }}
                            </span>
                        </div>

                        <h3 class="text-base font-black text-slate-900 mt-1">
                            {{ $res->vehicle->brand }} {{ $res->vehicle->model }}
                            <span class="font-mono text-xs text-slate-500 font-bold ml-1">({{ $res->vehicle->license_plate }})</span>
                        </h3>

                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-2">
                            <span><i class="fa-regular fa-calendar mr-1"></i> {{ $res->start_date->format('d M Y') }} &rarr; {{ $res->end_date->format('d M Y') }}</span>
                            <span>&bull;</span>
                            <span><i class="fa-regular fa-clock mr-1"></i> {{ $res->total_days }} Hari Sewa</span>
                            <span>&bull;</span>
                            <span class="font-bold text-slate-800">{{ $res->formatted_base_amount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 self-end md:self-center">
                    <a href="{{ route('reservations.show', $res) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition-colors">
                        Detail Sewa
                    </a>

                    @if($res->invoice)
                        <a href="{{ route('invoices.show', $res->invoice) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-receipt"></i> Invoice
                        </a>
                        <a href="{{ route('invoices.download', $res->invoice) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-file-pdf"></i> Unduh PDF
                        </a>
                    @endif

                    @if($res->status === 'pending')
                        <form action="{{ route('reservations.cancel', $res) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan pengajuan reservasi ini?');">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors">
                                Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center text-slate-400">
                <i class="fa-solid fa-car-side text-5xl mb-3 text-slate-300"></i>
                <h3 class="text-base font-bold text-slate-800">Anda belum memiliki riwayat reservasi mobil.</h3>
                <p class="text-xs text-slate-400 mt-1 mb-6">Pilih armada favorit Anda sekarang untuk perjalanan nyaman dan aman.</p>
                <a href="{{ route('customer.catalog') }}" class="px-6 py-3 rounded-xl text-xs font-bold text-white gradient-brand shadow-md shadow-blue-500/25">
                    Jelajahi Katalog Mobil
                </a>
            </div>
        @endforelse
    </div>

    @if($reservations->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $reservations->links() }}
        </div>
    @endif

</div>
@endsection
