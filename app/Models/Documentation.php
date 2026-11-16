<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documentation extends Model
{
    protected $table = 'documentations';

    protected $guarded = ['id'];

    public const CATEGORY_DELIVERABLE = 'project_deliverable';

    public const CATEGORY_CONTRACT = 'contract';

    public const CATEGORY_SOP = 'sop';

    public const CATEGORY_ASSET = 'asset';

    public const CATEGORY_PRESENTATION = 'presentation';

    public const CATEGORY_OTHER = 'other';

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
