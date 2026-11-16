<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['total_paid', 'remaining_balance'];

    public const STATUS_PENDING_PAYMENT = 'PENDING_PAYMENT';

    public const STATUS_CONFIRMED = 'CONFIRMED';

    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELLED = 'CANCELLED';

    public const PAYMENT_UNPAID = 'UNPAID';

    public const PAYMENT_PARTIALLY_PAID = 'PARTIALLY_PAID';

    public const PAYMENT_PAID = 'PAID';

    protected function casts(): array
    {
        return [
            'agreed_price' => 'decimal:2',
            'down_payment_required' => 'decimal:2',
            'deadline' => 'date',
            'delivery_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function totalVerifiedPayments(): float
    {
        return (float) $this->payments()->where('status', Payment::STATUS_VERIFIED)->sum('amount');
    }

    public function getTotalPaidAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->where('status', Payment::STATUS_VERIFIED)->sum('amount');
        }

        return $this->totalVerifiedPayments();
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->agreed_price - $this->total_paid);
    }

    public function remainingBalance(): float
    {
        return max(0, (float) $this->agreed_price - $this->totalVerifiedPayments());
    }

    /**
     * Recalculate payment status based on verified payments
     */
    public function recalculatePaymentStatus(): string
    {
        $paid = $this->totalVerifiedPayments();
        $total = (float) $this->agreed_price;

        if ($paid <= 0) {
            $newStatus = self::PAYMENT_UNPAID;
        } elseif ($paid >= $total) {
            $newStatus = self::PAYMENT_PAID;
        } else {
            $newStatus = self::PAYMENT_PARTIALLY_PAID;
        }

        $this->update(['payment_status' => $newStatus]);

        return $newStatus;
    }
}
