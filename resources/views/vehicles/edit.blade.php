@extends('layouts.app', ['title' => 'Edit Armada: ' . $vehicle->license_plate])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('vehicles.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Armada</a>
                <span class="text-slate-400 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Edit Armada</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Perbarui Data: {{ $vehicle->license_plate }}</h1>
            <p class="text-xs text-slate-500">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</p>
        </div>
        <a href="{{ route('vehicles.show', $vehicle) }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Batal / Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-8">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- License Plate -->
                <div>
                    <label for="license_plate" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Plat Polisi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="license_plate" id="license_plate" required value="{{ old('license_plate', $vehicle->license_plate) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm uppercase font-mono font-bold outline-none">
                </div>

                <!-- Brand -->
                <div>
                    <label for="brand" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Merk / Pabrikan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="brand" id="brand" required value="{{ old('brand', $vehicle->brand) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
                </div>

                <!-- Model -->
                <div>
                    <label for="model" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Model / Tipe Mobil <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="model" id="model" required value="{{ old('model', $vehicle->model) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
                </div>

                <!-- Year -->
                <div>
                    <label for="year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tahun Pembuatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="year" id="year" required value="{{ old('year', $vehicle->year) }}" min="1990" max="{{ date('Y') + 1 }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
                </div>

                <!-- Transmission -->
                <div>
                    <label for="transmission" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tipe Transmisi <span class="text-rose-500">*</span>
                    </label>
                    <select name="transmission" id="transmission" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
                        <option value="automatic" {{ old('transmission', $vehicle->transmission) === 'automatic' ? 'selected' : '' }}>Automatic (A/T)</option>
                        <option value="manual" {{ old('transmission', $vehicle->transmission) === 'manual' ? 'selected' : '' }}>Manual (M/T)</option>
                    </select>
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kapasitas Penumpang <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="capacity" id="capacity" required value="{{ old('capacity', $vehicle->capacity) }}" min="1" max="50"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
                </div>

                <!-- Rental Rate Per Day -->
                <div>
                    <label for="rental_rate_per_day" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tarif Sewa / Hari (IDR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                            Rp
                        </div>
                        <input type="number" name="rental_rate_per_day" id="rental_rate_per_day" required value="{{ old('rental_rate_per_day', (int)$vehicle->rental_rate_per_day) }}" min="0" step="5000"
                               class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm font-bold outline-none">
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Operasional <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
                        <option value="available" {{ old('status', $vehicle->status) === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                        <option value="rented" {{ old('status', $vehicle->status) === 'rented' ? 'selected' : '' }}>Disewa (Rented)</option>
                        <option value="maintenance" {{ old('status', $vehicle->status) === 'maintenance' ? 'selected' : '' }}>Perawatan (Maintenance)</option>
                        <option value="out_of_service" {{ old('status', $vehicle->status) === 'out_of_service' ? 'selected' : '' }}>Non-Aktif (Out of Service)</option>
                    </select>
                </div>
            </div>

            <!-- Image URL -->
            <div>
                <label for="image_url" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    URL Foto Armada (Opsional)
                </label>
                <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $vehicle->image_url) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white gradient-brand hover:opacity-95 shadow-md shadow-blue-500/25 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
