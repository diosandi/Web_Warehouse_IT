<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerialNumberCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'barang_masuk_id',
        'user_id',
        'old_serial_number',
        'new_serial_number',
        'reason',
    ];

    public function item()
    {
        return $this->belongsTo(Items::class);
    }

    public function barangMasuk()
    {
        return $this->belongsTo(Barang_masuk::class, 'barang_masuk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
