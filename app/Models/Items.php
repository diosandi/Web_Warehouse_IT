<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori',
        'merk',
        'type',
        'serial_number',
        'service_tag',
        'processor',
        'ram_gb',
        'storage_gb',
        'vga',
        'os',
        'tahun',
        'status',
        'barang_masuk_id'
    ];

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function barang_masuk()
    {
        return $this->belongsTo(Barang_masuk::class,'barang_masuk_id');
    }

    public function device_detail()
    {
        return $this->hasOne(Device_details::class);
    }

    public function distributionItems()
    {
        return $this->hasMany(DistributionItem::class);
    }
        /**
     * Get kategori options untuk dropdown
     */
    public static function getKategoriOptions()
    {
        return [
            'PC' => 'PC',
            'Monitor' => 'Monitor',
            'Printer Kertas' => 'Printer Kertas',
            'Printer Barcode' => 'Printer Barcode',
            'Scanner' => 'Scanner'
        ];
    }
}
