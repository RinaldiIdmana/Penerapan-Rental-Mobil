@extends('layouts.app', ['title' => 'Daftar Akun Baru'])

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl p-8 sm:p-10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl gradient-brand text-white text-xl mb-3 shadow-md shadow-blue-500/20">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Registrasi Pelanggan Baru</h2>
            <p class="text-xs text-slate-500 mt-1">Daftarkan akun Anda untuk mulai menyewa armada kendaraan impian.</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}"
                       placeholder="Contoh: Budi Santoso"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                           placeholder="budi@example.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Telepon / WA</label>
                    <input type="text" name="phone" id="phone" required value="{{ old('phone') }}"
                           placeholder="08123456789"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
                </div>
            </div>

            <div>
                <label for="driver_license_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nomor SIM (Surat Izin Mengemudi) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="driver_license_number" id="driver_license_number" required value="{{ old('driver_license_number') }}"
                       placeholder="SIM-A-XXXXXXXXXXXX"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
                <p class="text-[11px] text-slate-400 mt-1">Dibutuhkan sebagai verifikasi identitas resmi pengemudi armada.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi kata sandi"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-sm text-white gradient-brand hover:opacity-95 shadow-md shadow-blue-500/25 transition-all">
                    Daftar Sekarang & Sewa Armada
                </button>
            </div>

            <div class="text-center text-xs text-slate-500 pt-2">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800">Masuk di sini</a>
            </div>
        </form>
    </div>
</div>
@endsection
