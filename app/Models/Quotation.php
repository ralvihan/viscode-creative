<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    protected $guarded = ['id'];

    public const STATUS_DRAFT = 'DRAFT';

    public const STATUS_SENT = 'SENT';

    public const STATUS_ACCEPTED = 'ACCEPTED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_EXPIRED = 'EXPIRED';

    public const STATUS_CONVERTED = 'CONVERTED';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'valid_until' => 'date',
            'estimated_deadline' => 'date',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'quotation_id');
    }
}
