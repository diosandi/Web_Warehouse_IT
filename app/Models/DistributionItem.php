<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'distribution_id',
        'status',
        'returned_at',
        'return_condition_status',
        'return_note'
    ];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Items::class);
    }

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }
}
