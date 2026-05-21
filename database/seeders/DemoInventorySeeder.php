<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\LogBarang;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class DemoInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            [
                'uid' => '04A1B2C3D4',
                'nama_barang' => 'Scanner Gudang',
                'kategori' => 'RFID Reader',
                'status' => 'ADA',
                'last_seen' => $now->copy()->subMinutes(5),
            ],
            [
                'uid' => '04A1B2C3D5',
                'nama_barang' => 'Box Sensor Suhu',
                'kategori' => 'Sensor',
                'status' => 'KELUAR',
                'last_seen' => $now->copy()->subMinutes(22),
            ],
            [
                'uid' => '04A1B2C3D6',
                'nama_barang' => 'Kartu Akses Admin',
                'kategori' => 'Kartu',
                'status' => 'ADA',
                'last_seen' => $now->copy()->subMinutes(3),
            ],
            [
                'uid' => '04A1B2C3D7',
                'nama_barang' => 'Tag Rak 03',
                'kategori' => 'Tag',
                'status' => 'KELUAR',
                'last_seen' => $now->copy()->subMinutes(47),
            ],
            [
                'uid' => '04A1B2C3D8',
                'nama_barang' => 'Printer Label',
                'kategori' => 'Periferal',
                'status' => 'ADA',
                'last_seen' => $now->copy()->subMinutes(12),
            ],
        ];

        foreach ($items as $item) {
            $barang = Barang::updateOrCreate(
                ['uid' => $item['uid']],
                [
                    'nama_barang' => $item['nama_barang'],
                    'kategori' => $item['kategori'],
                    'status' => $item['status'],
                    'last_seen' => $item['last_seen'],
                ]
            );

            if (!LogBarang::where('barang_id', $barang->id)->exists()) {
                $aksi = $item['status'] === 'ADA' ? 'MASUK' : 'KELUAR';
                LogBarang::create([
                    'barang_id' => $barang->id,
                    'uid' => $barang->uid,
                    'aksi' => $aksi,
                    'waktu' => $item['last_seen'],
                ]);
            }
        }
    }
}
