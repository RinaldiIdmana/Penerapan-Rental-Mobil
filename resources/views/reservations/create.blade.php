@extends('layouts.app', ['title' => 'Formulir Pemesanan Armada'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ auth()->user()->hasAdminPrivileges() ? route('reservations.index') : route('customer.catalog') }}" class="text-xs text-slate-500 hover:text-slate-800">
                    {{ auth()->user()->hasAdminPrivileges() ? 'Reservasi' : 'Katalog' }}
                </a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Pemesanan Baru</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Formulir Reservasi Sewa Armada</h1>
            <p class="text-xs text-slate-500">Pilih kendaraan dan tentukan jadwal sewa dengan validasi anti-overlapping otomatis.</p>
        </div>
        <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Booking Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-8">
        <form action="{{ route('reservations.store') }}" method="POST" id="bookingForm" class="space-y-6">
            @csrf

            <!-- If Admin/Staff: Select Customer -->
            @if(auth()->user()->hasAdminPrivileges())
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100">
                    <label for="customer_id" class="block text-xs font-bold text-blue-900 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-user-check mr-1 text-blue-600"></i> Pilih Pelanggan (Admin Mode)
                    </label>
                    <select name="customer_id" id="customer_id" class="w-full px-4 py-2.5 rounded-xl border border-blue-200 bg-white text-sm outline-none">
                        <option value="">-- Booking untuk Pelanggan --</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->email }} - {{ $cust->phone ?? 'Tanpa No. Telp' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Vehicle Selection -->
            <div>
                <label for="vehicle_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Armada Kendaraan <span class="text-rose-500">*</span>
                </label>
                <select name="vehicle_id" id="vehicle_id" required onchange="calculateBooking()"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-semibold outline-none">
                    <option value="">-- Silakan Pilih Armada Mobil --</option>
                    @foreach($vehicles as $veh)
                        <option value="{{ $veh->id }}" data-rate="{{ $veh->rental_rate_per_day }}"
                                {{ (old('vehicle_id', $selectedVehicle?->id) == $veh->id) ? 'selected' : '' }}>
                            {{ $veh->brand }} {{ $veh->model }} ({{ $veh->license_plate }}) &bull; {{ $veh->formatted_rate }}/hari &bull; [{{ strtoupper($veh->transmission) }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range Pickers -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Mulai Sewa <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" required min="{{ date('Y-m-d') }}"
                           value="{{ old('start_date', date('Y-m-d')) }}" onchange="calculateBooking()"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Selesai Sewa <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" required min="{{ date('Y-m-d') }}"
                           value="{{ old('end_date', date('Y-m-d', strtotime('+1 day'))) }}" onchange="calculateBooking()"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
                </div>
            </div>

            <!-- Live Calculation & Anti-Overlap Status Card -->
            <div id="calculationCard" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center justify-between text-xs font-bold">
                    <span class="text-slate-600 uppercase tracking-wider">Kalkulasi Biaya & Ketersediaan</span>
                    <span id="availabilityBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i> Memeriksa jadwal...
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-200/60 text-xs">
                    <div>
                        <div class="text-slate-400">Tarif Harian:</div>
                        <div id="displayDailyRate" class="font-bold text-slate-800 text-sm mt-0.5">-</div>
                    </div>
                    <div>
                        <div class="text-slate-400">Durasi Sewa:</div>
                        <div id="displayTotalDays" class="font-bold text-slate-800 text-sm mt-0.5">-</div>
                    </div>
                    <div>
                        <div class="text-slate-400">Total Estimasi:</div>
                        <div id="displayGrandTotal" class="font-extrabold text-blue-600 text-base mt-0.5">-</div>
                    </div>
                </div>

                <div id="conflictAlert" class="hidden p-3 rounded-xl bg-rose-100 border border-rose-300 text-rose-800 text-xs font-medium">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 mr-1.5"></i>
                    <strong>Peringatan Jadwal Bentrok:</strong> Kendaraan ini sudah memiliki jadwal reservasi aktif pada tanggal tersebut. Silakan pilih armada lain atau ubah tanggal sewa.
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Catatan Perjalanan / Keperluan Khusus (Opsional)
                </label>
                <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Perjalanan dinas ke luar kota, penjemputan di bandara..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="submit" id="submitBtn" class="px-7 py-3 rounded-xl font-bold text-sm text-white gradient-brand hover:opacity-95 shadow-md shadow-blue-500/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Konfirmasi & Terbitkan Invoice
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    async function calculateBooking() {
        const vehicleSelect = document.getElementById('vehicle_id');
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const badge = document.getElementById('availabilityBadge');
        const conflictAlert = document.getElementById('conflictAlert');
        const submitBtn = document.getElementById('submitBtn');

        if (!vehicleSelect.value || !startDate || !endDate) return;

        badge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700";
        badge.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Memeriksa jadwal...';

        try {
            const response = await fetch("{{ route('reservations.cost-preview') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    vehicle_id: vehicleSelect.value,
                    start_date: startDate,
                    end_date: endDate
                })
            });

            const data = await response.json();

            document.getElementById('displayDailyRate').innerText = 'Rp ' + Number(data.rate_per_day).toLocaleString('id-ID');
            document.getElementById('displayTotalDays').innerText = data.total_days + ' Hari';
            document.getElementById('displayGrandTotal').innerText = data.formatted_amount;

            if (data.is_available) {
                badge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300";
                badge.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600"></i> Armada Tersedia';
                conflictAlert.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                badge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300";
                badge.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-600"></i> Jadwal Bentrok!';
                conflictAlert.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        } catch (e) {
            console.error("Error calculating booking preview:", e);
        }
    }

    // Auto-calculate on load
    document.addEventListener('DOMContentLoaded', calculateBooking);
</script>
@endpush
@endsection
