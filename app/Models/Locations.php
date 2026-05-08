<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    use HasFactory;
    protected $fillable = [
        'gedung',
        'ruangan',
        'type'
    ];

    public static function getTypeOptions()
    {
        return [
         'warehouse' => 'Warehouse',
         'distribution' => 'Distribution',
        //  'maintenance' => 'Maintenance'
        ];
    }

}
