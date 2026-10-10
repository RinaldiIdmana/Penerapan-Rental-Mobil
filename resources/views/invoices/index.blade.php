@extends('layouts.app', ['title' => 'Daftar Invoice Tagihan'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ auth()->user()->hasAdminPrivileges() ? route('admin.dashboard') : route('customer.catalog') }}" class="text-xs text-slate-500 hover:text-slate-800">
                    {{ auth()->user()->hasAdminPrivileges() ? 'Dashboard' : 'Beranda' }}
                </a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Invoice</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Daftar Tagihan & Invoice Resmi</h1>
            <p class="text-xs text-slate-500">Modul 2: Auto-generate nomor invoice (INV/YYYYMMDD/XXXX) dan unduh format PDF resmi.</p>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="sm:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nomor invoice (INV/...), kode reservasi, atau nama..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
            </div>

            <!-- Status -->
            <div class="sm:col-span-4">
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none">
                    <option value="">Semua Status Pembayaran</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Lunas (Unpaid)</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Jatuh Tempo (Overdue)</option>
                </select>
            </div>

            <!-- Action -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('invoices.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs" title="Reset">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Nomor Invoice</th>
                        <th class="py-3.5 px-4">Kode Reservasi & Pelanggan</th>
                        <th class="py-3.5 px-4">Subtotal Sewa</th>
                        <th class="py-3.5 px-4">Denda</th>
                        <th class="py-3.5 px-4">Grand Total</th>
                        <th class="py-3.5 px-4">Status Pembayaran</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('invoices.show', $inv) }}" class="text-blue-600 hover:underline">
                                    {{ $inv->invoice_number }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-sans mt-0.5">{{ $inv->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $inv->reservation->user->name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $inv->reservation->reservation_code }} &bull; {{ $inv->reservation->vehicle->license_plate }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                {{ $inv->formatted_subtotal }}
                            </td>
                            <td class="py-3.5 px-4 {{ $inv->fine_amount > 0 ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                {{ $inv->fine_amount > 0 ? $inv->formatted_fine_amount : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900 text-sm">
                                {{ $inv->formatted_grand_total }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $inv->payment_status_badge }}">
                                    {{ ucfirst($inv->payment_status) }}
                                </span>
                                @if($inv->paid_at)
                                    <div class="text-[10px] text-emerald-600 mt-0.5">Lunas: {{ $inv->paid_at->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('invoices.show', $inv) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700" title="Buka Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('invoices.download', $inv) }}" class="p-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700" title="Unduh PDF">
                                        <i class="fa-solid fa-file-pdf text-xs"></i>
                                    </a>

                                    @if(auth()->user()->hasAdminPrivileges() && $inv->payment_status !== 'paid')
                                        <form action="{{ route('invoices.mark-paid', $inv) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700" title="Tandai Lunas">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-500">
                                <i class="fa-solid fa-receipt text-4xl mb-2 text-slate-300"></i>
                                <div class="font-bold text-sm">Belum ada invoice yang diterbitkan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
