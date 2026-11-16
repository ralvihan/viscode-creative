<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $guarded = ['id'];

    public const STATUS_NOT_STARTED = 'NOT_STARTED';

    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';

    public const STATUS_REVIEW = 'REVIEW';

    public const STATUS_REVISION = 'REVISION';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELLED = 'CANCELLED';

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'target_completion_date' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function documentations(): HasMany
    {
        return $this->hasMany(Documentation::class);
    }

    public function areAllTasksDone(): bool
    {
        if ($this->tasks()->count() === 0) {
            return false;
        }

        return $this->tasks()->where('status', '!=', Task::STATUS_DONE)->count() === 0;
    }
}
