<?php

namespace JibayMcs\Nuwa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use JibayMcs\Nuwa\Blocks\BlockTypeRegistry;

class Block extends Model
{

    protected $table = 'nuwa_blocks';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    protected static function booted(): void
    {
        // Nettoyer les fichiers storage quand un block est supprime
        static::deleted(function (Block $block) {
            $block->deleteStorageFiles();
        });
    }

    // --- Relations ---

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    // --- Storage ---

    /**
     * Chemin du dossier storage pour ce block.
     */
    public function storagePath(string $file = ''): string
    {
        $base = "nuwa/blocks/{$this->id}";

        return $file ? "{$base}/{$file}" : $base;
    }

    protected function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('nuwa.blocks.disk', 'local'));
    }

    /**
     * Lire un fichier depuis le storage du block.
     */
    public function readFile(string $filename): string
    {
        $path = $this->storagePath($filename);

        if ($this->disk()->exists($path)) {
            return $this->disk()->get($path);
        }

        return '';
    }

    /**
     * Ecrire un fichier dans le storage du block.
     */
    public function writeFile(string $filename, string $content): void
    {
        if (empty(trim($content))) {
            // Supprimer le fichier s'il existe et que le contenu est vide
            $path = $this->storagePath($filename);
            if ($this->disk()->exists($path)) {
                $this->disk()->delete($path);
            }

            return;
        }

        $this->disk()->put($this->storagePath($filename), $content);
    }

    /**
     * Supprimer tous les fichiers storage de ce block.
     */
    public function deleteStorageFiles(): void
    {
        $path = $this->storagePath();

        if ($this->disk()->exists($path)) {
            $this->disk()->deleteDirectory($path);
        }
    }

    // --- Content accessors (lire depuis le storage) ---

    public function getHtmlAttribute(): string
    {
        return $this->readFile('content.html');
    }

    public function getCssAttribute(): string
    {
        return $this->readFile('style.css');
    }

    public function getJsAttribute(): string
    {
        return $this->readFile('script.js');
    }

    // --- Content mutators (ecrire vers le storage) ---

    public function setHtmlAttribute(string $value): void
    {
        $this->writeFile('content.html', $value);
    }

    public function setCssAttribute(string $value): void
    {
        $this->writeFile('style.css', $value);
    }

    public function setJsAttribute(string $value): void
    {
        $this->writeFile('script.js', $value);
    }

    /**
     * Sauvegarder le contenu HTML/CSS/JS depuis un tableau.
     * Utilise par le BlockEditorField.
     *
     * @param  array{html?: string, css?: string, js?: string}  $content
     */
    public function saveContent(array $content): void
    {
        if (array_key_exists('html', $content)) {
            $this->html = $content['html'];
        }
        if (array_key_exists('css', $content)) {
            $this->css = $content['css'];
        }
        if (array_key_exists('js', $content)) {
            $this->js = $content['js'];
        }
    }

    /**
     * Charger le contenu HTML/CSS/JS dans un tableau.
     *
     * @return array{html: string, css: string, js: string}
     */
    public function loadContent(): array
    {
        return [
            'html' => $this->html,
            'css' => $this->css,
            'js' => $this->js,
        ];
    }

    // --- Helpers ---

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Rendu HTML complet du block pour le front.
     * Lit le HTML depuis le storage, pas depuis la BDD.
     */
    public function render(): string
    {
        $html = $this->html;
        $css = $this->css;
        $js = $this->js;

        // Si pas de contenu storage, fallback sur le type de block
        if (empty($html)) {
            $registry = app(BlockTypeRegistry::class);

            if ($registry->has($this->type)) {
                return $registry->get($this->type)->render($this->data ?? []);
            }
        }

        $output = '';

        if ($css) {
            $output .= "<style>{$css}</style>\n";
        }

        $output .= $html;

        if ($js) {
            $output .= "\n<script>{$js}</script>";
        }

        return $output;
    }
}
