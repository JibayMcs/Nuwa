<?php

namespace JibayMcs\Nuwa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use JibayMcs\Nuwa\Enums\PageStatus;

class Page extends Model
{
    use SoftDeletes;

    protected $table = 'nuwa_pages';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
        ];
    }

    // --- Relations ---

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->whereNull('parent_id')->orderBy('order');
    }

    public function allBlocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('order');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    // --- Scopes ---

    public function scopePublished($query)
    {
        return $query->where('status', PageStatus::Published)
            ->where(fn($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeDraft($query)
    {
        return $query->where('status', PageStatus::Draft);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', PageStatus::Scheduled);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', PageStatus::Archived);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    // --- Helpers ---

    public function isPublished(): bool
    {
        return $this->status === PageStatus::Published
            && ($this->published_at === null || $this->published_at->isPast());
    }

    public function isDraft(): bool
    {
        return $this->status === PageStatus::Draft;
    }

    public function getUrlAttribute(): string
    {
        $prefix = config('nuwa.route_prefix', 'pages');

        return "/{$prefix}/{$this->slug}";
    }
}
