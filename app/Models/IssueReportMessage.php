<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueReportMessage extends Model
{
    use HasFactory;

    protected $table = 'issue_report_messages';

    protected $fillable = [
        'issue_report_id',
        'sender_id',
        'message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function issueReport()
    {
        return $this->belongsTo(IssueReport::class);
    }
}
