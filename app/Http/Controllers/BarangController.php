<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('q')) {
            $term = trim((string) $request->string('q'));
            $query->where(function ($builder) use ($term) {
                $builder
                    ->where('nama_barang', 'like', "%{$term}%")
                    ->orWhere('uid', 'like', "%{$term}%")
                    ->orWhere('kategori', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $status = strtoupper((string) $request->string('status'));
            if (in_array($status, ['ADA', 'KELUAR'], true)) {
                $query->where('status', $status);
            }
        }

        $limit = $request->integer('limit');
        $query->orderByDesc('updated_at');
        if ($limit) {
            $query->limit(min($limit, 200));
        }

        $items = $query->get();

        $stats = [
            'total' => Barang::count(),
            'ada' => Barang::where('status', 'ADA')->count(),
            'keluar' => Barang::where('status', 'KELUAR')->count(),
        ];

        $lastUpdate = Barang::orderByDesc('updated_at')->value('updated_at');
        $lastUpdateIso = $lastUpdate ? Carbon::parse($lastUpdate)->toIso8601String() : null;

        return response()->json([
            'items' => $items->map(function (Barang $barang) {
                return [
                    'id' => $barang->id,
                    'uid' => $barang->uid,
                    'nama_barang' => $barang->nama_barang,
                    'kategori' => $barang->kategori,
                    'status' => $barang->status,
                    'last_seen' => optional($barang->last_seen)->toIso8601String(),
                    'updated_at' => optional($barang->updated_at)->toIso8601String(),
                ];
            })->values(),
            'meta' => [
                'stats' => $stats,
                'last_update' => $lastUpdateIso,
                'last_scan' => Cache::get('rfid:last_scan'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'uid' => strtoupper(trim((string) $request->input('uid'))),
        ]);

        $validated = $request->validate([
            'uid' => ['required', 'string', 'max:64', 'unique:barang,uid'],
            'nama_barang' => ['required', 'string', 'max:120'],
            'kategori' => ['nullable', 'string', 'max:120'],
        ]);

        $barang = Barang::create([
            'uid' => $validated['uid'],
            'nama_barang' => $validated['nama_barang'],
            'kategori' => $validated['kategori'] ?? null,
            'status' => 'ADA',
            'last_seen' => now(),
        ]);

        LogBarang::create([
            'barang_id' => $barang->id,
            'uid' => $barang->uid,
            'aksi' => 'MASUK',
            'waktu' => now(),
        ]);

        Cache::put('rfid:last_scan', [
            'uid' => $barang->uid,
            'status' => 'registered',
            'aksi' => 'MASUK',
            'barang' => [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'status' => $barang->status,
            ],
            'at' => now()->toIso8601String(),
        ], now()->addHours(4));

        return response()->json([
            'status' => 'created',
            'barang' => [
                'id' => $barang->id,
                'uid' => $barang->uid,
                'nama_barang' => $barang->nama_barang,
                'kategori' => $barang->kategori,
                'status' => $barang->status,
                'last_seen' => optional($barang->last_seen)->toIso8601String(),
            ],
        ], 201);
    }

    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama_barang' => ['required', 'string', 'max:120'],
            'kategori' => ['nullable', 'string', 'max:120'],
        ]);

        $barang->fill($validated);
        $barang->save();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'updated',
                'barang' => [
                    'id' => $barang->id,
                    'uid' => $barang->uid,
                    'nama_barang' => $barang->nama_barang,
                    'kategori' => $barang->kategori,
                    'status' => $barang->status,
                    'last_seen' => optional($barang->last_seen)->toIso8601String(),
                ],
            ]);
        }

        return redirect()->route('barang.index')->with('status', 'Barang updated.');
    }
}
