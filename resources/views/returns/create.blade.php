@extends('layouts.app', ['title' => 'Proses Pengembalian: ' . $reservation->reservation_code])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reservations.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Reservasi</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Proses Pengembalian</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Form Pengembalian Armada Kendaraan</h1>
            <p class="text-xs text-slate-500">Kalkulasi otomatis denda keterlambatan dan pelepasan status operasional armada.</p>
        </div>
        <a href="{{ route('reservations.show', $reservation) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- 2-Columns Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1: Info Summary (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Vehicle Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-5 text-xs space-y-3">
                <span class="font-bold text-[11px] uppercase tracking-wider text-slate-400">Armada Yang Disewa</span>
                <div class="pt-1">
                    <span class="px-2.5 py-1 rounded-lg font-mono font-bold text-xs bg-slate-100 text-slate-800">
                        {{ $reservation->vehicle->license_plate }}
                    </span>
                    <h3 class="text-base font-black text-slate-900 mt-2">{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</h3>
                    <div class="text-slate-500 mt-0.5">Tarif: {{ $reservation->vehicle->formatted_rate }}/hari</div>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Penyewa:</span>
                        <span class="font-bold text-slate-800">{{ $reservation->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jadwal Selesai:</span>
                        <span class="font-bold text-slate-800">{{ $reservation->end_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Biaya Pokok:</span>
                        <span class="font-bold text-slate-800">{{ $reservation->formatted_base_amount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2: Form & Live Penalty Calculator (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-xs p-8">
            <form action="{{ route('returns.store', $reservation) }}" method="POST" id="returnForm" class="space-y-6">
                @csrf

                <!-- Actual Return Date -->
                <div>
                    <label for="return_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Pengembalian Aktual <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="return_date" id="return_date" required value="{{ old('return_date', $today) }}"
                           onchange="updateFinePreview()"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-semibold outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Tanggal terjadwal adalah: <strong>{{ $reservation->end_date->format('d F Y') }}</strong></p>
                </div>

                <!-- Custom Fine Rate per Day -->
                <div>
                    <label for="fine_per_day" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tarif Denda Keterlambatan / Hari (IDR)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                            Rp
                        </div>
                        <input type="number" name="fine_per_day" id="fine_per_day" value="{{ old('fine_per_day', (int)$preview['fine_per_day']) }}"
                               min="0" step="5000" oninput="updateFinePreview()"
                               class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-bold outline-none">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Default sistem: 50% dari tarif sewa harian (Rp {{ number_format($preview['fine_per_day'], 0, ',', '.') }}/hari).</p>
                </div>

                <!-- Live Dynamic Late Fine Calculation Box -->
                <div id="penaltyCard" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between text-xs font-bold pb-2 border-b border-slate-200">
                        <span class="text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-calculator mr-1 text-blue-600"></i> Hasil Kalkulasi Denda (Modul 3)
                        </span>
                        <span id="penaltyBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold">
                            <!-- Injected via JS -->
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <div class="text-slate-400">Selisih Keterlambatan:</div>
                            <div id="displayLateDays" class="font-extrabold text-sm text-slate-800 mt-0.5">-</div>
                        </div>
                        <div>
                            <div class="text-slate-400">Total Denda:</div>
                            <div id="displayTotalFine" class="font-extrabold text-sm text-rose-600 mt-0.5">-</div>
                        </div>
                        <div>
                            <div class="text-slate-400">Grand Total Akhir:</div>
                            <div id="displayFinalTotal" class="font-black text-base text-blue-600 mt-0.5">-</div>
                        </div>
                    </div>
                </div>

                <!-- Condition Notes -->
                <div>
                    <label for="condition_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Catatan Kondisi Armada Saat Kembali
                    </label>
                    <textarea name="condition_notes" id="condition_notes" rows="3"
                              placeholder="Kondisi bodi, kebersihan interior, bahan bakar, atau catatan kerusakan jika ada..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">{{ old('condition_notes') }}</textarea>
                </div>

                <!-- Modul 4 Integration: Needs Maintenance Checkbox -->
                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="needs_maintenance" value="1" class="mt-1 w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                        <div class="text-xs">
                            <span class="font-bold text-amber-900">Armada Butuh Servis / Perbaikan (Modul 4)</span>
                            <p class="text-amber-700 text-[11px] mt-0.5">
                                Jika dicentang, status armada akan otomatis diubah menjadi <strong>maintenance</strong> sehingga tidak muncul di daftar armada siap sewa.
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="submit" class="px-7 py-3 rounded-xl font-bold text-sm text-white gradient-brand hover:opacity-95 shadow-md shadow-blue-500/25 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-check-double"></i> Selesaikan Pengembalian & Perbarui Invoice
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

@push('scripts')
<script>
    async function updateFinePreview() {
        const returnDate = document.getElementById('return_date').value;
        const finePerDay = document.getElementById('fine_per_day').value;
        const penaltyBadge = document.getElementById('penaltyBadge');

        if (!returnDate) return;

        try {
            const response = await fetch("{{ route('returns.preview', $reservation) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    return_date: returnDate,
                    fine_per_day: finePerDay
                })
            });

            const data = await response.json();

            document.getElementById('displayLateDays').innerText = data.late_days + ' Hari';
            document.getElementById('displayTotalFine').innerText = data.formatted_fine;
            document.getElementById('displayFinalTotal').innerText = data.formatted_final_total;

            if (data.is_late) {
                penaltyBadge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300";
                penaltyBadge.innerHTML = '<i class="fa-solid fa-clock text-rose-600"></i> Terlambat ' + data.late_days + ' Hari';
            } else {
                penaltyBadge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300";
                penaltyBadge.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i> Tepat Waktu (Bebas Denda)';
            }
        } catch (e) {
            console.error("Error previewing fine calculation:", e);
        }
    }

    document.addEventListener('DOMContentLoaded', updateFinePreview);
</script>
@endpush
@endsection
