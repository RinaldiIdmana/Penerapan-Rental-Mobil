@extends('layouts.app', ['title' => 'Invoice: ' . $invoice->invoice_number])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Action Bar -->
    <div class="flex items-center justify-between bg-white p-4 rounded-3xl border border-slate-200/80 shadow-xs">
        <a href="{{ auth()->user()->hasAdminPrivileges() ? route('invoices.index') : route('reservations.my') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                <i class="fa-solid fa-print mr-1"></i> Cetak Halaman
            </button>
            <a href="{{ route('invoices.download', $invoice) }}" class="px-5 py-2 rounded-xl text-xs font-bold text-white gradient-brand shadow-md shadow-blue-500/25 hover:opacity-95 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf"></i> Unduh PDF Resmi
            </a>
            @if(auth()->user()->hasAdminPrivileges() && $invoice->payment_status !== 'paid')
                <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                        <i class="fa-solid fa-check mr-1"></i> Tandai Lunas
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Official Invoice Document Sheet -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl p-8 sm:p-12 space-y-8">

        <!-- Top Header & Letterhead -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl gradient-brand text-white flex items-center justify-center text-lg">
                        <i class="fa-solid fa-car-side"></i>
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">RentFleet<span class="text-blue-600">Pro</span></span>
                </div>
                <div class="text-xs text-slate-500 mt-2 space-y-0.5">
                    <div>PT Armada Rent Nusantara</div>
                    <div>Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan</div>
                    <div>Email: billing@rentfleet.com &bull; Telp: (021) 555-8899</div>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border {{ $invoice->payment_status_badge }}">
                    {{ ucfirst($invoice->payment_status) }}
                </span>
                <h2 class="text-2xl font-black text-slate-900 font-mono tracking-tight mt-2">{{ $invoice->invoice_number }}</h2>
                <div class="text-xs text-slate-500 mt-1">
                    <div>Tanggal Terbit: {{ $invoice->created_at->format('d F Y') }}</div>
                    @if($invoice->paid_at)
                        <div class="text-emerald-600 font-bold">Lunas Pada: {{ $invoice->paid_at->format('d F Y H:i') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Billed To & Reservation Reference -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Ditagihkan Kepada:</span>
                <div class="font-black text-sm text-slate-900 mt-1">{{ $invoice->reservation->user->name }}</div>
                <div class="text-slate-600 mt-1 space-y-0.5">
                    <div>Email: {{ $invoice->reservation->user->email }}</div>
                    <div>Telepon: {{ $invoice->reservation->user->phone ?? '-' }}</div>
                    <div>No. SIM: <span class="font-mono font-bold">{{ $invoice->reservation->user->driver_license_number ?? '-' }}</span></div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Detail Reservasi Armada:</span>
                <div class="font-mono font-black text-sm text-blue-600 mt-1">{{ $invoice->reservation->reservation_code }}</div>
                <div class="text-slate-600 mt-1 space-y-0.5">
                    <div>Armada: <strong>{{ $invoice->reservation->vehicle->brand }} {{ $invoice->reservation->vehicle->model }}</strong> ({{ $invoice->reservation->vehicle->license_plate }})</div>
                    <div>Periode: {{ $invoice->reservation->start_date->format('d/m/Y') }} s/d {{ $invoice->reservation->end_date->format('d/m/Y') }}</div>
                    <div>Durasi: {{ $invoice->reservation->total_days }} Hari Sewa</div>
                </div>
            </div>
        </div>

        <!-- Invoice Line Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b-2 border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-2">Deskripsi Layanan</th>
                        <th class="py-3 px-2 text-center">Durasi / Satuan</th>
                        <th class="py-3 px-2 text-right">Tarif Satuan</th>
                        <th class="py-3 px-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr>
                        <td class="py-3.5 px-2">
                            <div class="font-bold text-slate-900">Sewa Armada: {{ $invoice->reservation->vehicle->full_name }}</div>
                            <div class="text-[11px] text-slate-500">Nomor Polisi: {{ $invoice->reservation->vehicle->license_plate }} &bull; Transmisi: {{ strtoupper($invoice->reservation->vehicle->transmission) }}</div>
                        </td>
                        <td class="py-3.5 px-2 text-center font-bold">{{ $invoice->reservation->total_days }} Hari</td>
                        <td class="py-3.5 px-2 text-right">{{ $invoice->reservation->vehicle->formatted_rate }}</td>
                        <td class="py-3.5 px-2 text-right font-bold text-slate-900">{{ $invoice->formatted_subtotal }}</td>
                    </tr>

                    @if($invoice->fine_amount > 0)
                        <tr class="bg-rose-50/50">
                            <td class="py-3.5 px-2">
                                <div class="font-bold text-rose-800">Denda Keterlambatan Pengembalian (Modul 3)</div>
                                <div class="text-[11px] text-rose-600">
                                    Terlambat: {{ $invoice->returnRecord->late_days ?? 0 }} Hari &times; Rp {{ number_format($invoice->returnRecord->fine_per_day ?? 0, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="py-3.5 px-2 text-center font-bold text-rose-800">{{ $invoice->returnRecord->late_days ?? 0 }} Hari</td>
                            <td class="py-3.5 px-2 text-right text-rose-800">Rp {{ number_format($invoice->returnRecord->fine_per_day ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-2 text-right font-black text-rose-700">{{ $invoice->formatted_fine_amount }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Totals & Payment Summary -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pt-4 border-t border-slate-200">
            <div class="text-xs text-slate-500 max-w-sm space-y-1">
                <div class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Informasi Pembayaran:</div>
                <p>Transfer via Rekening BCA: <strong>8829-0192-33</strong> a.n PT Armada Rent Nusantara.</p>
                <p class="text-[11px] text-slate-400">Harap cantumkan nomor invoice pada berita transfer.</p>
            </div>

            <div class="w-full sm:w-72 space-y-2 text-xs">
                <div class="flex justify-between py-1 text-slate-600">
                    <span>Subtotal Sewa:</span>
                    <span class="font-bold text-slate-800">{{ $invoice->formatted_subtotal }}</span>
                </div>
                <div class="flex justify-between py-1 text-slate-600">
                    <span>Denda Keterlambatan:</span>
                    <span class="font-bold {{ $invoice->fine_amount > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                        {{ $invoice->formatted_fine_amount }}
                    </span>
                </div>
                <div class="flex justify-between pt-3 border-t-2 border-slate-900 text-sm">
                    <span class="font-black text-slate-900">Grand Total:</span>
                    <span class="text-lg font-black text-blue-600">{{ $invoice->formatted_grand_total }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
