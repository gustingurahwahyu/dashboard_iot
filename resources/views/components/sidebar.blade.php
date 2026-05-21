@php
    $navLink = function (string $routeName) {
        return request()->routeIs($routeName)
            ? 'flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2.5 text-sm font-semibold text-blue-700'
            : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900';
    };
@endphp

<aside class="w-64 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:sticky lg:top-6 lg:h-[calc(100vh-3rem)]">
    <!-- Logo -->
    <div class="flex items-center gap-2 mb-8">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-sm font-bold text-white">
            SI
        </div>
        <span class="font-semibold text-slate-900">Smart Inventory</span>
    </div>

    <!-- Overview Section -->
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Overview</p>
        <nav class="flex flex-col gap-2">
            <a href="{{ route('dashboard') }}" class="{{ $navLink('dashboard') }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 4l4 4m0 0l4-4m-4 4V8" />
                </svg>
                Dashboard
            </a>
        </nav>
    </div>

    <!-- Manage Section -->
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Manage</p>
        <nav class="flex flex-col gap-2">
            <a href="{{ route('barang.index') }}" class="{{ $navLink('barang.index') }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10M4 12.5v6.5a2 2 0 002 2h12a2 2 0 002-2v-6.5" />
                </svg>
                Barang
            </a>
            <a href="{{ route('log.index') }}" class="{{ $navLink('log.index') }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Log Aktivitas
            </a>
            <a href="{{ route('scan.index') }}" class="{{ $navLink('scan.index') }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Scan RFID
            </a>
        </nav>
    </div>

    <!-- Divider -->
    <div class="mb-6 border-t border-slate-200"></div>

    <!-- Settings Section -->
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Settings</p>
        <nav class="flex flex-col gap-2">
            <button class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Setting
            </button>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </nav>
    </div>

    <!-- Footer Info -->
    <div class="mt-auto border-t border-slate-200 pt-4">
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-3 text-center text-xs text-blue-700">
            <p class="font-semibold">Auto refresh</p>
            <p class="text-blue-600">Data diperbarui setiap 3 detik.</p>
        </div>
    </div>
</aside>
