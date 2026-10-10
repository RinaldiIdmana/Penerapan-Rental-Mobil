@extends('layouts.app', ['title' => 'Jadwalkan Servis Armada'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('maintenances.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Servis Berkala</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-amber-600">Catat Baru</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Jadwalkan Pemeliharaan Armada</h1>
            <p class="text-xs text-slate-500">Modul 4: Catat servis berkala dan tetapkan tanggal jatuh tempo (Next Service Due Date).</p>
        </div>
        <a href="{{ route('maintenances.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-8">
        <form action="{{ route('maintenances.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Vehicle Selection -->
                <div class="sm:col-span-2">
                    <label for="vehicle_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pilih Armada Mobil <span class="text-rose-500">*</span>
                    </label>
                    <select name="vehicle_id" id="vehicle_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 text-sm outline-none">
                        <option value="">-- Pilih Armada --</option>
                        @foreach($vehicles as $veh)
                            <option value="{{ $veh->id }}" {{ (old('vehicle_id', $selectedVehicleId) == $veh->id) ? 'selected' : '' }}>
                                {{ $veh->license_plate }} &bull; {{ $veh->brand }} {{ $veh->model }} (Status Saat Ini: {{ ucfirst($veh->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Service Date -->
                <div>
                    <label for="service_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Pelaksanaan Servis <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="service_date" id="service_date" required value="{{ old('service_date', date('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 text-sm outline-none">
                </div>

                <!-- Next Due Date -->
                <div>
                    <label for="next_service_due_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jatuh Tempo Servis Berikutnya (Due Date) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="next_service_due_date" id="next_service_due_date" required value="{{ old('next_service_due_date', date('Y-m-d', strtotime('+3 months'))) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 text-sm outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Digunakan sistem untuk memicu alert di dashboard.</p>
                </div>

                <!-- Service Type -->
                <div>
                    <label for="service_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Perawatan / Servis <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="service_type" id="service_type" required value="{{ old('service_type') }}"
                           placeholder="Contoh: Ganti Oli Mesin & Filter, Servis Rem, Tune Up"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 text-sm outline-none">
                </div>

                <!-- Cost -->
                <div>
                    <label for="cost" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Estimasi / Biaya Perawatan (IDR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                            Rp
                        </div>
                        <input type="number" name="cost" id="cost" required value="{{ old('cost', 0) }}" min="0" step="5000"
                               class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 text-sm font-bold outline-none">
                    </div>
                </div>

                <!-- Status -->
                <div class="sm:col-span-2">
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Pengerjaan Servis <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 text-sm outline-none">
                        <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal (Scheduled)</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan di Bengkel (In Progress - Armada Auto-Maintenance)</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Sudah Selesai (Completed)</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi / Catatan Tambahan (Bengkel, Sparepart, Garansi)
                </label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Catatan bengkel resmi, nomor nota part, keluhan pengemudi..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 text-sm outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('maintenances.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 shadow-md shadow-amber-500/20 transition-all">
                    Simpan Jadwal Servis
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
