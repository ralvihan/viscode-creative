<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyReport extends Model
{
    protected $guarded = ['id'];

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_FINALIZED = 'FINALIZED';

    protected function casts(): array
    {
        return [
            'total_income' => 'decimal:2',
            'total_expense' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
            'finalized_at' => 'datetime',
        ];
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getPeriodNameAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
    }

    public function isLocked(): bool
    {
        return $this->status === self::STATUS_FINALIZED;
    }
}
