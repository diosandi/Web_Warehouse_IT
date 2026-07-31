<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;
    protected $fillable = [
        'location_id',
        'user_id',
        'nama_user',
        'divisi',
        'tanggal_distribusi',
        'status',
        'keterangan'
    ];

    // public function item()
    // {
    //     return $this->belongsTo(Items::class);
    // }

    public function location()
    {
        return $this->belongsTo(Locations::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function distributionItems()
    {
        return $this->hasMany(DistributionItem::class);
    }

    public function issueReports()
    {
        return $this->hasMany(IssueReport::class);
    }
}
