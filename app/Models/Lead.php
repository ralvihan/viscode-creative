<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    protected $guarded = ['id'];

    public const STATUS_NEW = 'NEW';

    public const STATUS_REVIEWED = 'REVIEWED';

    public const STATUS_FOLLOW_UP = 'FOLLOW_UP';

    public const STATUS_QUOTATION_SENT = 'QUOTATION_SENT';

    public const STATUS_CONTACTED = 'CONTACTED';

    public const STATUS_OFFERED = 'OFFERED';

    public const STATUS_WON = 'WON';

    public const STATUS_LOST = 'LOST';

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'last_contact_date' => 'date',
            'next_follow_up_date' => 'date',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function latestQuotation(): HasOne
    {
        return $this->hasOne(Quotation::class)->latestOfMany();
    }
}
