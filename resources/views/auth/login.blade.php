@extends('layouts.app', ['title' => 'Masuk ke Sistem'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <div class="grid grid-cols-1 md:grid-cols-12 bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden">
        <!-- Left Side: Brand & Quick Demo Switchers -->
        <div class="md:col-span-5 gradient-brand p-8 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl mb-6">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <h2 class="text-2xl font-black tracking-tight mb-2">RentFleet Pro</h2>
                <p class="text-blue-100 text-xs leading-relaxed mb-6">
                    Sistem Enterprise Pengelolaan Armada, Kalender Reservasi Real-Time, & Manajemen Denda Otomatis.
                </p>

                <!-- 1-Click Demo Logins -->
                <div class="space-y-2 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-blue-200 mb-2">
                        <i class="fa-solid fa-bolt text-amber-300 mr-1"></i> Akun Demo Siap Pakai:
                    </div>
                    <button type="button" onclick="fillLogin('admin@rental.com', 'password')" class="w-full text-left px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 transition-all text-xs font-semibold flex items-center justify-between">
                        <span><i class="fa-solid fa-shield-halved mr-1.5 text-blue-300"></i> Admin (Semua Hak)</span>
                        <span class="text-[10px] bg-blue-500/50 px-1.5 py-0.5 rounded font-mono">admin@rental.com</span>
                    </button>
                    <button type="button" onclick="fillLogin('staff@rental.com', 'password')" class="w-full text-left px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 transition-all text-xs font-semibold flex items-center justify-between">
                        <span><i class="fa-solid fa-user-gear mr-1.5 text-emerald-300"></i> Staff Operasional</span>
                        <span class="text-[10px] bg-blue-500/50 px-1.5 py-0.5 rounded font-mono">staff@rental.com</span>
                    </button>
                    <button type="button" onclick="fillLogin('budi@gmail.com', 'password')" class="w-full text-left px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 transition-all text-xs font-semibold flex items-center justify-between">
                        <span><i class="fa-solid fa-user mr-1.5 text-purple-300"></i> Customer (Budi)</span>
                        <span class="text-[10px] bg-blue-500/50 px-1.5 py-0.5 rounded font-mono">budi@gmail.com</span>
                    </button>
                </div>
            </div>

            <div class="text-[11px] text-blue-200 mt-6 relative z-10 flex items-center gap-2">
                <i class="fa-solid fa-lock"></i>
                <span>Password default untuk semua akun: <code class="font-mono bg-black/20 px-1.5 py-0.5 rounded text-white">password</code></span>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-slate-900">Masuk ke Akun Anda</h3>
                <p class="text-xs text-slate-500 mt-1">Silakan masukkan kredensial akun untuk mengakses sistem.</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </div>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm transition-all outline-none">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-sm transition-all outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <span class="text-slate-600 font-medium">Ingat saya</span>
                    </label>
                    <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-800">Belum punya akun?</a>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-sm text-white gradient-brand hover:opacity-95 shadow-md shadow-blue-500/25 transition-all">
                        Masuk Sekarang <i class="fa-solid fa-arrow-right ml-1.5 text-xs"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
