<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'type',
        'notes',
    ];

    public function getWhatsappAttribute(): ?string
    {
        return $this->phone;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
