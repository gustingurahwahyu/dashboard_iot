@extends('layouts.app')

@section('title', 'Scan RFID')
@section('page-title', 'Scan RFID')
@section('page-subtitle', 'Pantau UID terakhir dan registrasi barang baru.')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="grid gap-6 lg:col-span-2">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Hasil Scan Terakhir</h3>
                <p class="text-sm text-slate-500">UID terbaru dari ESP32 akan muncul di sini.</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">UID</p>
                        <p id="scan-uid" class="mt-2 text-xl font-semibold text-slate-900">-</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Status</p>
                        <p id="scan-status" class="mt-2 text-sm font-semibold text-slate-700">-</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Waktu</p>
                        <p id="scan-time" class="mt-2 text-sm text-slate-600">-</p>
                    </div>
                </div>
            </section>

            <section id="scan-form-wrapper" class="hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Registrasi Barang Baru</h3>
                <p class="text-sm text-slate-500">Lengkapi data jika UID belum terdaftar.</p>
                <form id="scan-form" class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">UID</label>
                        <input id="scan-form-uid" type="text" readonly class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-2.5 text-sm text-slate-600" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Nama Barang</label>
                        <input id="scan-form-name" type="text" placeholder="Contoh: Sensor Gudang" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-2.5 text-sm focus:border-emerald-300 focus:outline-none" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Kategori (Opsional)</label>
                        <input id="scan-form-kategori" type="text" placeholder="Contoh: Elektronik" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-2.5 text-sm focus:border-emerald-300 focus:outline-none" />
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500">
                            Simpan Barang
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-lg font-semibold text-slate-900">Aktivitas Terbaru</h3>
                <p class="text-sm text-slate-500">Ringkasan 5 log terakhir.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">UID</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="scan-log-body" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const uidEl = document.getElementById('scan-uid');
            const statusEl = document.getElementById('scan-status');
            const timeEl = document.getElementById('scan-time');
            const formWrapper = document.getElementById('scan-form-wrapper');
            const form = document.getElementById('scan-form');
            const formUid = document.getElementById('scan-form-uid');
            const formName = document.getElementById('scan-form-name');
            const formKategori = document.getElementById('scan-form-kategori');
            const logBody = document.getElementById('scan-log-body');
            const lastUpdatedEl = document.getElementById('last-updated');

            let lastScanSeen = null;

            const actionBadge = (action) => {
                const isMasuk = action === 'MASUK';
                const color = isMasuk
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                    : 'border-rose-200 bg-rose-50 text-rose-800';
                return `<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold ${color}">${action}</span>`;
            };

            const renderLogs = (items) => {
                if (!items.length) {
                    logBody.innerHTML = `<tr><td colspan="3" class="px-4 py-6 text-center text-sm text-slate-400">Belum ada log aktivitas.</td></tr>`;
                    return;
                }

                logBody.innerHTML = items
                    .map((item) => {
                        return `<tr>
                            <td class="px-4 py-3 text-sm text-slate-500">${InventoryUI.formatTime(item.waktu)}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">${item.uid}</td>
                            <td class="px-4 py-3">${actionBadge(item.aksi)}</td>
                        </tr>`;
                    })
                    .join('');
            };

            const updateScan = (scan) => {
                if (!scan) {
                    uidEl.textContent = '-';
                    statusEl.textContent = '-';
                    timeEl.textContent = '-';
                    formWrapper.classList.add('hidden');
                    return;
                }

                const statusLabel = {
                    new: 'NEW',
                    updated: 'UPDATED',
                    registered: 'REGISTERED',
                };

                uidEl.textContent = scan.uid ?? '-';
                statusEl.textContent = statusLabel[scan.status] ?? scan.status ?? '-';
                timeEl.textContent = InventoryUI.formatTime(scan.at);

                const showForm = scan.status === 'new';
                formWrapper.classList.toggle('hidden', !showForm);
                if (showForm) {
                    formUid.value = scan.uid ?? '';
                }

                if (scan.at && scan.at !== lastScanSeen) {
                    lastScanSeen = scan.at;
                    if (scan.status === 'new') {
                        InventoryUI.toast(`UID ${scan.uid} belum terdaftar`, 'warning');
                    } else if (scan.status === 'updated') {
                        InventoryUI.toast(`Status ${scan.uid} berubah`, 'success');
                    } else if (scan.status === 'registered') {
                        InventoryUI.toast(`Barang ${scan.uid} berhasil didaftarkan`, 'success');
                    }
                }
            };

            const fetchScanData = async () => {
                try {
                    InventoryUI.setLoading(true);
                    const response = await fetch('/api/log?limit=5');
                    const data = await response.json();
                    renderLogs(data.items ?? []);
                    updateScan(data.meta?.last_scan);
                    lastUpdatedEl.textContent = InventoryUI.formatTime(data.meta?.last_update);
                } catch (error) {
                    InventoryUI.toast('Gagal memuat data scan', 'danger');
                } finally {
                    InventoryUI.setLoading(false);
                }
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const uid = formUid.value.trim();
                const namaBarang = formName.value.trim();
                const kategori = formKategori.value.trim();

                if (!uid) {
                    InventoryUI.toast('UID belum tersedia', 'warning');
                    return;
                }

                if (!namaBarang) {
                    InventoryUI.toast('Nama barang wajib diisi', 'warning');
                    return;
                }

                try {
                    InventoryUI.setLoading(true);
                    const response = await fetch('/api/barang', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({
                            uid: uid,
                            nama_barang: namaBarang,
                            kategori: kategori || null,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error('Store failed');
                    }

                    formName.value = '';
                    formKategori.value = '';
                    InventoryUI.toast('Barang baru tersimpan', 'success');
                    await fetchScanData();
                } catch (error) {
                    InventoryUI.toast('Gagal menyimpan barang baru', 'danger');
                } finally {
                    InventoryUI.setLoading(false);
                }
            });

            fetchScanData();
            setInterval(fetchScanData, 3000);
        })();
    </script>
@endpush
