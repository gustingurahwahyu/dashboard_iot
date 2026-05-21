@extends('layouts.app')

@section('title', 'Manajemen Barang')
@section('page-title', 'Manajemen Barang')
@section('page-subtitle', 'Kelola data barang RFID dan ubah nama dengan cepat.')

@section('content')
    <div class="grid gap-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1">
                    <input id="barang-search" type="text" placeholder="Cari nama, UID, atau kategori..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-2.5 text-sm focus:border-emerald-300 focus:outline-none" />
                </div>
                <div>
                    <select id="barang-status" class="rounded-2xl border border-slate-200 bg-slate-50/80 px-3 py-2.5 text-sm focus:border-emerald-300 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="ADA">ADA</option>
                        <option value="KELUAR">KELUAR</option>
                    </select>
                </div>
                <button id="barang-refresh" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                    Refresh
                </button>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-lg font-semibold text-slate-900">Daftar Barang</h3>
                <p class="text-sm text-slate-500">Klik simpan untuk memperbarui nama atau kategori.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">UID</th>
                            <th class="px-4 py-3">Nama Barang</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Last Seen</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="barang-body" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const searchInput = document.getElementById('barang-search');
            const statusSelect = document.getElementById('barang-status');
            const refreshButton = document.getElementById('barang-refresh');
            const body = document.getElementById('barang-body');
            const lastUpdatedEl = document.getElementById('last-updated');

            const previousStatus = new Map();
            let debounceTimer;

            const escapeHtml = (value) => {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const statusBadge = (status) => {
                const isAda = status === 'ADA';
                const color = isAda
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                    : 'border-rose-200 bg-rose-50 text-rose-800';
                const dot = isAda ? 'bg-emerald-500' : 'bg-rose-500';
                return `<span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold ${color}">
                    <span class="h-2 w-2 rounded-full ${dot}"></span>${status}
                </span>`;
            };

            const renderRows = (items) => {
                if (!items.length) {
                    body.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-sm text-slate-400">Belum ada data barang.</td></tr>`;
                    return;
                }

                body.innerHTML = items
                    .map((item) => {
                        return `<tr data-id="${item.id}" class="transition">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">${item.uid}</td>
                            <td class="px-4 py-3">
                                <input data-field="nama_barang" value="${escapeHtml(item.nama_barang)}" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-sm focus:border-emerald-300 focus:outline-none" />
                            </td>
                            <td class="px-4 py-3">
                                <input data-field="kategori" value="${escapeHtml(item.kategori ?? '')}" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-sm focus:border-emerald-300 focus:outline-none" />
                            </td>
                            <td class="px-4 py-3">${statusBadge(item.status)}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">${InventoryUI.formatTime(item.last_seen)}</td>
                            <td class="px-4 py-3 text-right">
                                <button data-action="save" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">Simpan</button>
                            </td>
                        </tr>`;
                    })
                    .join('');

                items.forEach((item) => {
                    const prev = previousStatus.get(item.id);
                    if (prev && prev !== item.status) {
                        const row = body.querySelector(`tr[data-id="${item.id}"]`);
                        InventoryUI.flashRow(row);
                    }
                    previousStatus.set(item.id, item.status);
                });
            };

            const fetchBarang = async () => {
                try {
                    InventoryUI.setLoading(true);
                    const params = new URLSearchParams();
                    if (searchInput.value.trim()) {
                        params.set('q', searchInput.value.trim());
                    }
                    if (statusSelect.value) {
                        params.set('status', statusSelect.value);
                    }

                    const response = await fetch(`/api/barang?${params.toString()}`);
                    const data = await response.json();
                    renderRows(data.items ?? []);
                    lastUpdatedEl.textContent = InventoryUI.formatTime(data.meta?.last_update);
                } catch (error) {
                    InventoryUI.toast('Gagal memuat daftar barang', 'danger');
                } finally {
                    InventoryUI.setLoading(false);
                }
            };

            const updateBarang = async (row) => {
                const id = row.dataset.id;
                const namaInput = row.querySelector('[data-field="nama_barang"]');
                const kategoriInput = row.querySelector('[data-field="kategori"]');
                const namaBarang = namaInput.value.trim();
                const kategori = kategoriInput.value.trim();

                if (!namaBarang) {
                    InventoryUI.toast('Nama barang wajib diisi', 'warning');
                    return;
                }

                try {
                    InventoryUI.setLoading(true);
                    const response = await fetch(`/api/barang/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({
                            nama_barang: namaBarang,
                            kategori: kategori || null,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error('Update failed');
                    }

                    InventoryUI.toast('Barang berhasil diperbarui', 'success');
                } catch (error) {
                    InventoryUI.toast('Gagal memperbarui barang', 'danger');
                } finally {
                    InventoryUI.setLoading(false);
                }
            };

            body.addEventListener('click', (event) => {
                const button = event.target.closest('[data-action="save"]');
                if (!button) {
                    return;
                }
                const row = button.closest('tr');
                if (row) {
                    updateBarang(row);
                }
            });

            const debouncedFetch = () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchBarang, 300);
            };

            searchInput.addEventListener('input', debouncedFetch);
            statusSelect.addEventListener('change', fetchBarang);
            refreshButton.addEventListener('click', fetchBarang);

            fetchBarang();
        })();
    </script>
@endpush
