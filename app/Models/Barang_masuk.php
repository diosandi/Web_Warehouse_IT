<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang_masuk extends Model
{
    use HasFactory;
    
    protected $table = 'barang_masuk';
    
    protected $fillable = [
        'tanggal_masuk',
        'quantity',
        'po_number',
        'supplier',
        'keterangan',
    ];

    public function items()
    {
        return $this->hasMany(Items::class,'barang_masuk_id');
    }
}
