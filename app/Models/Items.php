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
        'asset',
        'serial_number',
        'service_tag',
        'processor',
        'ram_gb',
        'storage_gb',
        'vga',
        'os',
        'tahun',
        'status',
        'barang_masuk_id',
        'storage_location_id',
        'condition_note'
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
        return $this->hasOne(Device_details::class,'item_id');
    }

    public function distributionItems()
    {
        return $this->hasMany(DistributionItem::class,'item_id');
    }

    public function serialNumberCorrections()
    {
        return $this->hasMany(SerialNumberCorrection::class, 'item_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ItemStatusHistory::class, 'item_id');
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
            'Scanner' => 'Scanner',
            'Lainnya' => 'Lainnya'
        ];
    }

    // 🔥 cek apakah masih dipakai
    public function hasActiveDistribution(): bool
    {
        return $this->distributionItems()
            ->active()
            ->exists();
    }

    public function refreshStatus()
    {
        $isUsed = $this->hasActiveDistribution();

        if (!$isUsed && in_array($this->status, ['maintenance', 'retired', 'vendor'])) {
            return;
        }

        $newStatus = $isUsed ? 'used' : 'available';
        $changes = ['status' => $newStatus];

        if ($isUsed) {
            $changes['storage_location_id'] = null;
        }

        $hasChanges = collect($changes)
            ->contains(fn ($value, $key) => $this->{$key} !== $value);

        if ($hasChanges) {
            $this->update($changes);
        }
    }

    public function isUsed()
    {
        return $this->hasActiveDistribution();
    }

    public function storageLocation()
    {
        return $this->belongsTo(Locations::class, 'storage_location_id');
    }

    // protected static function booted()
    // {
    //     static::created(function ($item) {
    //         $item->deviceDetail()->create([]);
    //     });
    // }
}
