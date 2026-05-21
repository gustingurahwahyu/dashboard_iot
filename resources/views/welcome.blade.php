<x-guest-layout>
    <div class="space-y-4 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Smart Inventory</p>
        <h1 class="text-2xl font-semibold text-slate-900">RFID Inventory Dashboard</h1>
        <p class="text-sm text-slate-500">Masuk untuk mengelola barang dan aktivitas RFID.</p>
        @if (Route::has('login'))
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Login
            </a>
        @endif
    </div>
</x-guest-layout>
