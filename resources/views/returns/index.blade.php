@extends('layouts.app', ['title' => 'Manajemen Pengembalian & Denda'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800">Dashboard</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Pengembalian</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Riwayat Pengembalian & Denda Keterlambatan</h1>
            <p class="text-xs text-slate-500">Modul 3: Rekapitulasi serah terima kembali armada, kalkulasi denda, dan catatan kondisi unit.</p>
        </div>
        <a href="{{ route('reservations.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-colors">
            <i class="fa-solid fa-list-check mr-1.5"></i> Cek Reservasi Aktif
        </a>
    </div>

    <!-- Returns Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Kode Reservasi</th>
                        <th class="py-3.5 px-4">Armada & Pelanggan</th>
                        <th class="py-3.5 px-4">Tgl Kembali Aktual</th>
                        <th class="py-3.5 px-4">Keterlambatan</th>
                        <th class="py-3.5 px-4">Tarif Denda / Hari</th>
                        <th class="py-3.5 px-4">Total Denda</th>
                        <th class="py-3.5 px-4">Total Akhir Tagihan</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $ret)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('reservations.show', $ret->reservation) }}" class="hover:underline">
                                    {{ $ret->reservation->reservation_code }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $ret->reservation->vehicle->license_plate }} &bull; {{ $ret->reservation->vehicle->model }}</div>
                                <div class="text-[11px] text-slate-500">{{ $ret->reservation->user->name }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                {{ $ret->return_date->format('d M Y') }}
                                <div class="text-[10px] text-slate-400">Jadwal: {{ $ret->reservation->end_date->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($ret->late_days > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <i class="fa-solid fa-clock text-rose-600"></i> {{ $ret->late_days }} Hari
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check text-emerald-600"></i> Tepat Waktu
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                Rp {{ number_format($ret->fine_per_day, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold {{ $ret->total_fine > 0 ? 'text-rose-600 font-extrabold' : 'text-slate-500' }}">
                                {{ $ret->formatted_total_fine }}
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900">
                                {{ $ret->formatted_final_total }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('reservations.show', $ret->reservation) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700" title="Detail Reservasi">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if($ret->reservation->invoice)
                                        <a href="{{ route('invoices.show', $ret->reservation->invoice) }}" class="p-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700" title="Lihat Invoice">
                                            <i class="fa-solid fa-receipt text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-500">
                                <i class="fa-solid fa-rotate-left text-4xl mb-2 text-slate-300"></i>
                                <div class="font-bold text-sm">Belum ada riwayat pengembalian armada.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $returns->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
