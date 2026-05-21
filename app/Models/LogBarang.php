<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogBarang extends Model
{
    protected $table = 'log_barang';

    protected $fillable = [
        'barang_id',
        'uid',
        'aksi',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
