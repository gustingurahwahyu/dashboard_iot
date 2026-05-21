<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RFIDController extends Controller
{
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'uid' => ['required', 'string', 'max:64'],
        ]);

        $uid = strtoupper(trim($validated['uid']));
        $now = now();

        $barang = Barang::where('uid', $uid)->first();

        if (!$barang) {
            Cache::put('rfid:last_scan', [
                'uid' => $uid,
                'status' => 'new',
                'at' => $now->toIso8601String(),
            ], $now->copy()->addHours(4));

            return response()->json([
                'status' => 'new',
                'uid' => $uid,
            ]);
        }

        $aksi = $barang->status === 'ADA' ? 'KELUAR' : 'MASUK';
        $barang->status = $aksi === 'MASUK' ? 'ADA' : 'KELUAR';
        $barang->last_seen = $now;
        $barang->save();

        LogBarang::create([
            'barang_id' => $barang->id,
            'uid' => $uid,
            'aksi' => $aksi,
            'waktu' => $now,
        ]);

        Cache::put('rfid:last_scan', [
            'uid' => $uid,
            'status' => 'updated',
            'aksi' => $aksi,
            'barang' => [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'status' => $barang->status,
            ],
            'at' => $now->toIso8601String(),
        ], $now->copy()->addHours(4));

        return response()->json([
            'status' => 'updated',
            'aksi' => $aksi,
            'uid' => $uid,
            'barang' => [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'status' => $barang->status,
                'last_seen' => optional($barang->last_seen)->toIso8601String(),
            ],
        ]);
    }
}
