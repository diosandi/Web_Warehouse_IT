<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueReport extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';
    public const CATEGORY_DEVICE = 'device';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'ticket_number',
        'reporter_id',
        'distribution_id',
        'distribution_item_id',
        'item_id',
        'location_id',
        'issue_category',
        'title',
        'description',
        'evidence_path',
        'priority',
        'status',
        'admin_note',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (IssueReport $report) {
            if ($report->ticket_number) {
                return;
            }

            $date = now()->format('Ymd');
            $dailyNumber = static::whereDate('created_at', now()->toDateString())->count() + 1;

            $report->ticket_number = 'LK-' . $date . '-' . str_pad((string) $dailyNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_OPEN => 'Baru',
            self::STATUS_IN_PROGRESS => 'Diproses',
            self::STATUS_RESOLVED => 'Selesai',
            self::STATUS_CLOSED => 'Ditutup',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            'low' => 'Rendah',
            'normal' => 'Normal',
            'high' => 'Tinggi',
            'urgent' => 'Urgent',
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_DEVICE => 'Perangkat',
            self::CATEGORY_OTHER => 'Lainnya',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorityOptions()[$this->priority] ?? $this->priority;
    }

    public function getIssueCategoryLabelAttribute(): string
    {
        return self::categoryOptions()[$this->issue_category] ?? $this->issue_category ?? '-';
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }

    public function distributionItem()
    {
        return $this->belongsTo(DistributionItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Items::class);
    }

    public function location()
    {
        return $this->belongsTo(Locations::class);
    }

    public function messages()
    {
        return $this->hasMany(IssueReportMessage::class)
            ->oldest('created_at')
            ->oldest('id');
    }
}
