<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemNotification extends Model
{
    public const CATEGORY_PESANAN_BARU = 'pesanan_baru';

    public const CATEGORY_USER_ACTION = 'user_action';

    public const CATEGORY_PERUBAHAN_DATA = 'perubahan_data';

    public const CATEGORY_KEUANGAN = 'keuangan';

    public const CATEGORY_OPERASIONAL = 'operasional';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Metadata for notification category styling & labels.
     *
     * @return array<string, array{label: string, badge: string, dot: string}>
     */
    public static function categoryDefinitions(): array
    {
        return [
            self::CATEGORY_PESANAN_BARU => [
                'label' => 'Pesanan & Prospek',
                'badge' => 'bg-blue-50 text-blue-700 border-blue-200',
                'dot' => 'bg-blue-600',
            ],
            self::CATEGORY_USER_ACTION => [
                'label' => 'Aksi User',
                'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'dot' => 'bg-indigo-600',
            ],
            self::CATEGORY_PERUBAHAN_DATA => [
                'label' => 'Perubahan Data',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500',
            ],
            self::CATEGORY_KEUANGAN => [
                'label' => 'Keuangan',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-600',
            ],
            self::CATEGORY_OPERASIONAL => [
                'label' => 'Proyek & QC',
                'badge' => 'bg-purple-50 text-purple-700 border-purple-200',
                'dot' => 'bg-purple-600',
            ],
        ];
    }
}
