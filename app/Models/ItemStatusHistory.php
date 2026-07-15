<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'old_location_id',
        'new_location_id',
        'source',
        'event',
        'old_status',
        'new_status',
        'old_condition_note',
        'new_condition_note',
        'note',
    ];

    public function item()
    {
        return $this->belongsTo(Items::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldLocation()
    {
        return $this->belongsTo(Locations::class, 'old_location_id');
    }

    public function newLocation()
    {
        return $this->belongsTo(Locations::class, 'new_location_id');
    }
}
