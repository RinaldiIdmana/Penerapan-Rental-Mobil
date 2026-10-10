@extends('layouts.app', ['title' => 'Kalender Ketersediaan Armada'])

@push('styles')
<!-- FullCalendar Styles handled via CDN -->
<style>
    .fc {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .fc-header-toolbar {
        margin-bottom: 1.25rem !important;
    }
    .fc-toolbar-title {
        font-size: 1.15rem !important;
        font-weight: 800 !important;
        color: #0f172a;
    }
    .fc-button-primary {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        border-radius: 0.75rem !important;
        padding: 0.4rem 0.8rem !important;
    }
    .fc-button-primary:hover {
        background-color: #1d4ed8 !important;
    }
    .fc-event {
        border-radius: 0.5rem !important;
        padding: 2px 4px !important;
        cursor: pointer;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        transition: transform 0.15s ease;
    }
    .fc-event:hover {
        transform: scale(1.02);
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800">Dashboard</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Kalender Armada</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Kalender Ketersediaan Armada Rental</h1>
            <p class="text-xs text-slate-500">Visualisasi jadwal booking armada untuk mencegah bentrok tanggal (Double-Booking Prevention).</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Filter by Vehicle Dropdown -->
            <select id="calendarVehicleFilter" onchange="refetchCalendar()"
                    class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-bold bg-white text-slate-700 outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">-- Semua Armada Mobil --</option>
                @foreach($vehicles as $veh)
                    <option value="{{ $veh->id }}" {{ $selectedVehicleId == $veh->id ? 'selected' : '' }}>
                        {{ $veh->license_plate }} &bull; {{ $veh->model }}
                    </option>
                @endforeach
            </select>

            <a href="{{ route('reservations.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white gradient-brand shadow-sm shadow-blue-500/25 hover:opacity-95 transition-all">
                <i class="fa-solid fa-plus text-xs mr-1"></i> Pesan Jadwal
            </a>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-2xl border border-slate-200 text-xs font-medium">
        <span class="font-bold text-slate-700">Keterangan Warna:</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#059669]"></span> Aktif / Sedang Jalan</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#2563eb]"></span> Terkonfirmasi</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#d97706]"></span> Menunggu Konfirmasi</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-[#64748b]"></span> Selesai</span>
    </div>

    <!-- Main Grid: Calendar + Anti-Overlap Checker Widget -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Calendar Container (8 cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div id="calendar"></div>
        </div>

        <!-- Anti-Overlap Live Checker Widget (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Check Form -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center gap-2 text-slate-900 font-black text-sm pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-shield-halved text-blue-600"></i>
                    <span>Cek Anti-Overlapping Instan</span>
                </div>
                <p class="text-xs text-slate-500">
                    Pastikan armada tidak memiliki jadwal bentrok sebelum membuat kesepakatan dengan pelanggan.
                </p>

                <div class="space-y-3 text-xs">
                    <div>
                        <label for="checkVehicle" class="block font-bold text-slate-700 mb-1">Pilih Armada:</label>
                        <select id="checkVehicle" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none">
                            @foreach($vehicles as $veh)
                                <option value="{{ $veh->id }}">{{ $veh->license_plate }} - {{ $veh->model }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="checkStart" class="block font-bold text-slate-700 mb-1">Mulai Tanggal:</label>
                        <input type="date" id="checkStart" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none">
                    </div>

                    <div>
                        <label for="checkEnd" class="block font-bold text-slate-700 mb-1">Sampai Tanggal:</label>
                        <input type="date" id="checkEnd" value="{{ date('Y-m-d', strtotime('+2 days')) }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 outline-none">
                    </div>

                    <button type="button" onclick="performOverlapCheck()" class="w-full py-2.5 rounded-xl font-bold text-xs text-white gradient-brand hover:opacity-95 transition-all">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Periksa Ketersediaan
                    </button>
                </div>

                <div id="checkResult" class="hidden p-3.5 rounded-2xl text-xs font-semibold"></div>
            </div>

            <!-- Event Details Modal / Box -->
            <div id="eventDetailBox" class="hidden bg-white p-6 rounded-3xl border border-blue-200 shadow-md space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-black uppercase text-blue-600 tracking-wider">Detail Booking Terpilih</span>
                    <button type="button" onclick="document.getElementById('eventDetailBox').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="text-xs space-y-1.5">
                    <div><span class="text-slate-400">Kode:</span> <strong id="modalCode" class="font-mono text-blue-600"></strong></div>
                    <div><span class="text-slate-400">Pelanggan:</span> <strong id="modalCustomer" class="text-slate-800"></strong></div>
                    <div><span class="text-slate-400">Armada:</span> <strong id="modalVehicle" class="text-slate-800"></strong></div>
                    <div><span class="text-slate-400">Status:</span> <span id="modalStatus" class="font-bold"></span></div>
                    <div><span class="text-slate-400">Biaya:</span> <strong id="modalAmount" class="text-slate-900"></strong></div>
                </div>
                <div class="pt-2">
                    <a id="modalLink" href="#" class="block text-center w-full py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                        Buka Detail Reservasi &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<!-- FullCalendar JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    let calendar;

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                list: 'Daftar'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                const vehicleId = document.getElementById('calendarVehicleFilter').value;
                let url = "{{ route('calendar.events') }}?start=" + fetchInfo.startStr + "&end=" + fetchInfo.endStr;
                if (vehicleId) url += "&vehicle_id=" + vehicleId;

                fetch(url)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(err => failureCallback(err));
            },
            eventClick: function(info) {
                const props = info.event.extendedProps;
                document.getElementById('modalCode').innerText = props.code;
                document.getElementById('modalCustomer').innerText = props.customer;
                document.getElementById('modalVehicle').innerText = props.vehicle + ' (' + props.plate + ')';
                document.getElementById('modalStatus').innerText = props.status;
                document.getElementById('modalAmount').innerText = props.amount + ' (' + props.totalDays + ' Hari)';
                document.getElementById('modalLink').href = props.url;

                document.getElementById('eventDetailBox').classList.remove('hidden');
            }
        });

        calendar.render();
    });

    function refetchCalendar() {
        if (calendar) {
            calendar.refetchEvents();
        }
    }

    async function performOverlapCheck() {
        const vehicleId = document.getElementById('checkVehicle').value;
        const startDate = document.getElementById('checkStart').value;
        const endDate = document.getElementById('checkEnd').value;
        const resultBox = document.getElementById('checkResult');

        resultBox.className = "p-3.5 rounded-2xl text-xs font-semibold bg-slate-100 text-slate-700";
        resultBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Sedang memeriksa database...';
        resultBox.classList.remove('hidden');

        try {
            const response = await fetch("{{ route('calendar.check') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    vehicle_id: vehicleId,
                    start_date: startDate,
                    end_date: endDate
                })
            });

            const data = await response.json();

            if (data.available) {
                resultBox.className = "p-3.5 rounded-2xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200";
                resultBox.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600 mr-1.5"></i> ' + data.message;
            } else {
                resultBox.className = "p-3.5 rounded-2xl text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200";
                resultBox.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-600 mr-1.5"></i> ' + data.message;
            }
        } catch (e) {
            resultBox.className = "p-3.5 rounded-2xl text-xs font-semibold bg-rose-50 text-rose-800";
            resultBox.innerHTML = 'Gagal memeriksa koneksi.';
        }
    }
</script>
@endpush
@endsection
