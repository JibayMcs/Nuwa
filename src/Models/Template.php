<?php

namespace JibayMcs\Nuwa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{

    protected $table = 'nuwa_templates';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
        ];
    }

    // --- Relations ---

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
