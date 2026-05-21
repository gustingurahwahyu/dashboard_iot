<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>@yield('title', config('app.name'))</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen gap-6 bg-slate-50 p-6">
            <!-- Left Sidebar -->
            <x-sidebar />

            <!-- Main Content -->
            <div class="flex-1 space-y-6 min-w-0">
                <!-- Top Bar: Search + User -->
                <div class="flex items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="search"
                            placeholder="Search your course..."
                            class="w-full rounded-full border border-slate-200 bg-white py-2 pl-10 pr-4 text-sm text-slate-600 placeholder:text-slate-400 focus:border-blue-300 focus:outline-none"
                        />
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="rounded-full border border-slate-200 bg-white p-2 hover:bg-slate-100">
                            <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        @auth
                            <div class="flex items-center gap-3 rounded-full border border-slate-200 bg-white px-3 py-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-xs font-bold text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden flex-col md:flex">
                                    <span class="text-xs font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                                </div>
                                <button class="text-slate-400 hover:text-slate-600">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Main Grid: Content + Right Panel -->
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Main Content Area (2 columns) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Hero Banner -->
                        <div class="rounded-3xl border border-blue-200 bg-gradient-to-br from-blue-600 to-blue-500 p-8 text-white shadow-md">
                            <p class="text-sm font-semibold uppercase tracking-[0.1em] text-blue-100">Smart Inventory Management</p>
                            <h2 class="mt-3 text-3xl font-bold">Real-Time RFID Tracking</h2>
                            <p class="mt-2 text-blue-100">Monitor semua barang Anda dengan teknologi RFID terkini</p>
                            <button class="mt-5 flex items-center gap-2 rounded-full bg-black px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">
                                <span>Mulai Tracking</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content Sections -->
                        <main class="space-y-6">
                            @yield('content')
                            {{ $slot ?? '' }}
                        </main>
                    </div>

                    <!-- Right Sidebar: Statistics -->
                    <div class="space-y-4">
                        <!-- Stats Card -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h3 class="text-sm font-semibold text-slate-900">Statistic</h3>
                            <div class="mt-4 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Total Items</span>
                                    <span id="stat-total-side" class="text-lg font-bold text-slate-900">-</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Available (ADA)</span>
                                    <span id="stat-ada-side" class="text-lg font-bold text-emerald-600">-</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Checked Out (KELUAR)</span>
                                    <span id="stat-keluar-side" class="text-lg font-bold text-orange-600">-</span>
                                </div>
                            </div>
                            <div class="mt-4 h-32 rounded-2xl bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 p-4">
                                <div id="mini-chart" class="flex h-full items-end justify-between gap-1">
                                    <div class="w-1 rounded-t-sm bg-blue-400" style="height: 30%"></div>
                                    <div class="w-1 rounded-t-sm bg-purple-400" style="height: 60%"></div>
                                    <div class="w-1 rounded-t-sm bg-pink-400" style="height: 40%"></div>
                                    <div class="w-1 rounded-t-sm bg-blue-400" style="height: 50%"></div>
                                    <div class="w-1 rounded-t-sm bg-purple-400" style="height: 70%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Good Morning Card -->
                        <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-500">Welcome Back</p>
                            <div class="mt-4 flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-sm font-bold text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500">Continue your tracking</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full rounded-full border border-slate-300 bg-white py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Logout
                                </button>
                            </form>
                        </div>

                        <!-- Last Updated -->
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center text-xs text-slate-500">
                            Last update: <span id="last-updated" class="font-semibold">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="global-loading" class="fixed right-8 top-8 hidden items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-600 shadow">
            <span class="inline-flex h-3 w-3 animate-spin rounded-full border-2 border-slate-300 border-t-slate-600"></span>
            Memuat data...
        </div>

        <div id="toast-container" class="pointer-events-none fixed right-6 top-20 z-50 flex max-w-sm flex-col gap-2"></div>

        @stack('scripts')
    </body>
</html>
