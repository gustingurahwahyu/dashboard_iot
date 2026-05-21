@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <section class="grid gap-4 grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-slate-400">Total Items</p>
            <p id="stat-total" class="mt-3 text-3xl font-bold text-slate-900">-</p>
            <p class="text-xs text-slate-500 mt-1">Registered in system</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-slate-400">Available</p>
            <p id="stat-ada" class="mt-3 text-3xl font-bold text-emerald-600">-</p>
            <p class="text-xs text-slate-500 mt-1">Ready to use</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-slate-400">Checked Out</p>
            <p id="stat-keluar" class="mt-3 text-3xl font-bold text-orange-600">-</p>
            <p class="text-xs text-slate-500 mt-1">Currently in use</p>
        </div>
    </section>

    <!-- Recent Items Grid -->
    <section>
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Recent Items</h3>
                <p class="text-sm text-slate-500">Real-time updates every 3 seconds</p>
            </div>
            <div class="flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                Auto refresh
            </div>
        </div>

        <div class="grid gap-4 grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10M4 12.5v6.5a2 2 0 002 2h12a2 2 0 002-2v-6.5" />
                        </svg>
                    </div>
                    <button class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                        </svg>
                    </button>
                </div>
                <p class="mt-2 text-xs font-semibold text-slate-400 uppercase">SCANNED</p>
                <h4 class="mt-1 text-sm font-semibold text-slate-900">Item 1</h4>
                <p class="text-xs text-slate-500 mt-1">Last seen 5 minutes ago</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.5a2 2 0 00-1 .267" />
                        </svg>
                    </div>
                    <button class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                        </svg>
                    </button>
                </div>
                <p class="mt-2 text-xs font-semibold text-slate-400 uppercase">TRACKED</p>
                <h4 class="mt-1 text-sm font-semibold text-slate-900">Item 2</h4>
                <p class="text-xs text-slate-500 mt-1">Active status</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <button class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                        </svg>
                    </button>
                </div>
                <p class="mt-2 text-xs font-semibold text-slate-400 uppercase">LIVE</p>
                <h4 class="mt-1 text-sm font-semibold text-slate-900">Item 3</h4>
                <p class="text-xs text-slate-500 mt-1">Updated now</p>
            </div>
        </div>
    </section>

    <!-- Continue Monitoring Section -->
    <section>
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Continue Monitoring</h3>
        </div>
        <div class="grid gap-4 grid-cols-3 overflow-x-auto pb-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-0 shadow-sm overflow-hidden hover:shadow-md transition flex-shrink-0 min-w-[calc(33.333%-1rem)]">
                <div class="h-32 bg-gradient-to-br from-blue-400 to-blue-600 relative group cursor-pointer">
                    <button class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                        </svg>
                    </button>
                    <button class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-white/80 hover:bg-white">
                        <svg class="h-4 w-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase">UPDATE</p>
                    <h4 class="mt-1 text-sm font-semibold text-slate-900">Warehouse Scan</h4>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">A</div>
                        <span class="text-xs text-slate-500">Automated System</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-0 shadow-sm overflow-hidden hover:shadow-md transition flex-shrink-0 min-w-[calc(33.333%-1rem)]">
                <div class="h-32 bg-gradient-to-br from-purple-400 to-pink-600 relative group cursor-pointer">
                    <button class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                        </svg>
                    </button>
                    <button class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-white/80 hover:bg-white">
                        <svg class="h-4 w-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase">TRACKING</p>
                    <h4 class="mt-1 text-sm font-semibold text-slate-900">Real-Time Monitor</h4>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">R</div>
                        <span class="text-xs text-slate-500">RFID System</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-0 shadow-sm overflow-hidden hover:shadow-md transition flex-shrink-0 min-w-[calc(33.333%-1rem)]">
                <div class="h-32 bg-gradient-to-br from-emerald-400 to-cyan-600 relative group cursor-pointer">
                    <button class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                        </svg>
                    </button>
                    <button class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-white/80 hover:bg-white">
                        <svg class="h-4 w-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-xs font-semibold text-slate-400 uppercase">INVENTORY</p>
                    <h4 class="mt-1 text-sm font-semibold text-slate-900">Inventory Check</h4>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">I</div>
                        <span class="text-xs text-slate-500">Inventory System</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Activity Table -->
    <section>
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Recent Scans</h3>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">UID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Last Seen</th>
                    </tr>
                </thead>
                <tbody id="dashboard-latest-body" class="divide-y divide-slate-200"></tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Wait for InventoryUI to be defined
        const waitForInventoryUI = () => {
            if (typeof InventoryUI === 'undefined') {
                setTimeout(waitForInventoryUI, 100);
                return;
            }

            (() => {
                const totalEl = document.getElementById('stat-total');
                const adaEl = document.getElementById('stat-ada');
                const keluarEl = document.getElementById('stat-keluar');
                const totalSideEl = document.getElementById('stat-total-side');
                const adaSideEl = document.getElementById('stat-ada-side');
                const keluarSideEl = document.getElementById('stat-keluar-side');
                const latestBody = document.getElementById('dashboard-latest-body');
                const lastUpdatedEl = document.getElementById('last-updated');

                const previousStatus = new Map();

                const statusBadge = (status) => {
                    const isAda = status === 'ADA';
                    const color = isAda
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                        : 'border-orange-200 bg-orange-50 text-orange-800';
                    const dot = isAda ? 'bg-emerald-500' : 'bg-orange-500';
                    return `<span class="inline-flex items-center gap-1 rounded-full border px-2 py-1 text-xs font-semibold ${color}">
                        <span class="h-1.5 w-1.5 rounded-full ${dot}"></span>${status}
                    </span>`;
                };

                const renderRows = (items) => {
                    if (!items.length) {
                        latestBody.innerHTML = `<tr><td colspan="4" class="py-6 text-center text-sm text-slate-400">No items yet.</td></tr>`;
                        return;
                    }

                    latestBody.innerHTML = items
                        .map((item) => {
                            return `<tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3 font-mono text-xs text-slate-600">${item.uid}</td>
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900">${item.nama_barang}</td>
                                <td class="px-6 py-3">${statusBadge(item.status)}</td>
                                <td class="px-6 py-3 text-sm text-slate-500">${InventoryUI.formatTime(item.last_seen)}</td>
                            </tr>`;
                        })
                        .join('');
                };

                const loadDashboard = async () => {
                    try {
                        InventoryUI.setLoading(true);
                        const response = await fetch('/api/barang?limit=8');
                        const data = await response.json();

                        totalEl.textContent = data.meta?.stats?.total ?? '-';
                        adaEl.textContent = data.meta?.stats?.ada ?? '-';
                        keluarEl.textContent = data.meta?.stats?.keluar ?? '-';
                        totalSideEl.textContent = data.meta?.stats?.total ?? '-';
                        adaSideEl.textContent = data.meta?.stats?.ada ?? '-';
                        keluarSideEl.textContent = data.meta?.stats?.keluar ?? '-';
                        lastUpdatedEl.textContent = InventoryUI.formatTime(data.meta?.last_update);
                        renderRows(data.items ?? []);
                    } catch (error) {
                        InventoryUI.toast('Failed to load dashboard', 'danger');
                    } finally {
                        InventoryUI.setLoading(false);
                    }
                };

                loadDashboard();
                setInterval(loadDashboard, 3000);
            })();
        };

        waitForInventoryUI();
    </script>
@endpush
