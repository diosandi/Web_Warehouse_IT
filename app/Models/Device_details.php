<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device_details extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'pc_name',
        'user_account',
        'ip_address',
        'mac_lan',
        'mac_wifi',
        'os_version',
        'build',
        'office_version',
        'office_key',
        'antivirus',
        ];

    public function item()
    {
        return $this->belongsTo(Items::class);
    }
}
