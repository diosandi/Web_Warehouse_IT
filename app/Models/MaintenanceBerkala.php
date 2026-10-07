<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceBerkala extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_item_id',
        'tanggal_cek',
        'periode_bulan',
        'kondisi',
        'checklist',
        'catatan',
        'teknisi',
        'status',
    ];

    protected $casts = [
        'tanggal_cek' => 'date',
        'checklist' => 'array',
    ];

    public function distributionItem()
    {
        return $this->belongsTo(DistributionItem::class);
    }
}
