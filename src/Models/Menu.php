<?php

namespace JibayMcs\Nuwa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{

    protected $table = 'nuwa_menus';

    protected $guarded = [];

    // --- Relations ---

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order');
    }

    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    // --- Helpers ---

    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->first();
    }
}
