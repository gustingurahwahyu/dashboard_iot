<?php

namespace App\Http\Controllers;

use App\Models\LogBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = LogBarang::query()->with('barang');

        if ($request->filled('date')) {
            $date = Carbon::createFromFormat('Y-m-d', (string) $request->string('date'));
            if ($date) {
                $query->whereBetween('waktu', [$date->startOfDay(), $date->endOfDay()]);
            }
        }

        $limit = min($request->integer('limit', 50), 200);
        $logs = $query->orderByDesc('waktu')->limit($limit)->get();

        return response()->json([
            'items' => $logs->map(function (LogBarang $log) {
                return [
                    'id' => $log->id,
                    'uid' => $log->uid,
                    'aksi' => $log->aksi,
                    'waktu' => optional($log->waktu)->toIso8601String(),
                    'nama_barang' => $log->barang?->nama_barang,
                ];
            })->values(),
            'meta' => [
                'last_update' => optional($logs->first()?->waktu)->toIso8601String(),
                'last_scan' => Cache::get('rfid:last_scan'),
            ],
        ]);
    }
}
