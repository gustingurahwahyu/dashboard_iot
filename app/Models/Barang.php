<?php

namespace App\Models;

use App\Models\LogBarang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'uid',
        'nama_barang',
        'kategori',
        'status',
        'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(LogBarang::class, 'barang_id');
    }
}
