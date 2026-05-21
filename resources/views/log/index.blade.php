@extends('layouts.app')

@section('title', 'Log & Riwayat')
@section('page-title', 'Log & Riwayat')
@section('page-subtitle', 'Pantau semua aktivitas keluar masuk barang.')

@section('content')
    <div class="grid gap-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Filter Tanggal</label>
                    <input id="log-date" type="date" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-2.5 text-sm focus:border-emerald-300 focus:outline-none" />
                </div>
                <button id="log-clear" class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                    Reset
                </button>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-lg font-semibold text-slate-900">Aktivitas Terbaru</h3>
                <p class="text-sm text-slate-500">Riwayat keluar masuk barang secara real-time.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3">UID</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="log-body" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const dateInput = document.getElementById('log-date');
            const clearButton = document.getElementById('log-clear');
            const body = document.getElementById('log-body');
            const lastUpdatedEl = document.getElementById('last-updated');

            let lastScanSeen = null;

            const actionBadge = (action) => {
                const isMasuk = action === 'MASUK';
                const color = isMasuk
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                    : 'border-rose-200 bg-rose-50 text-rose-800';
                return `<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold ${color}">${action}</span>`;
            };

            const renderRows = (items) => {
                if (!items.length) {
                    body.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-sm text-slate-400">Belum ada log aktivitas.</td></tr>`;
                    return;
                }

                body.innerHTML = items
                    .map((item) => {
                        return `<tr>
                            <td class="px-4 py-3 text-sm text-slate-500">${InventoryUI.formatTime(item.waktu)}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">${item.nama_barang ?? '-'}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">${item.uid}</td>
                            <td class="px-4 py-3">${actionBadge(item.aksi)}</td>
                        </tr>`;
                    })
                    .join('');
            };

            const updateLastScan = (scan) => {
                if (!scan || !scan.at || scan.at === lastScanSeen) {
                    return;
                }
                lastScanSeen = scan.at;
                if (scan.status === 'new') {
                    InventoryUI.toast(`UID ${scan.uid} belum terdaftar`, 'warning');
                } else if (scan.status === 'updated') {
                    InventoryUI.toast(`Status ${scan.uid} berubah`, 'success');
                } else if (scan.status === 'registered') {
                    InventoryUI.toast(`Barang ${scan.uid} berhasil didaftarkan`, 'success');
                }
            };

            const fetchLogs = async () => {
                try {
                    InventoryUI.setLoading(true);
                    const params = new URLSearchParams();
                    if (dateInput.value) {
                        params.set('date', dateInput.value);
                    }
                    const response = await fetch(`/api/log?${params.toString()}`);
                    const data = await response.json();
                    renderRows(data.items ?? []);
                    lastUpdatedEl.textContent = InventoryUI.formatTime(data.meta?.last_update);
                    updateLastScan(data.meta?.last_scan);
                } catch (error) {
                    InventoryUI.toast('Gagal memuat log aktivitas', 'danger');
                } finally {
                    InventoryUI.setLoading(false);
                }
            };

            clearButton.addEventListener('click', () => {
                dateInput.value = '';
                fetchLogs();
            });

            dateInput.addEventListener('change', fetchLogs);

            fetchLogs();
            setInterval(fetchLogs, 3000);
        })();
    </script>
@endpush
