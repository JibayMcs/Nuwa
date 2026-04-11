<?php

namespace JibayMcs\Nuwa\Blocks;

class FrameworkManager
{
    protected string $active;

    /**
     * @var array<string, array{label: string, cdn: string|null, version: string}>
     */
    protected array $frameworks = [
        'tailwind4' => [
            'label' => 'TailwindCSS v4',
            'cdn' => 'https://cdn.tailwindcss.com',
            'version' => '4',
        ],
        'bootstrap' => [
            'label' => 'Bootstrap',
            'cdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css',
            'version' => '5',
        ],
        'none' => [
            'label' => 'No framework',
            'cdn' => null,
            'version' => '',
        ],
    ];

    public function __construct()
    {
        $this->active = config('nuwa.css_framework', 'tailwind4');
    }

    public function active(): string
    {
        return $this->active;
    }

    public function setActive(string $framework): static
    {
        $this->active = $framework;

        return $this;
    }

    public function label(): string
    {
        return $this->frameworks[$this->active]['label'] ?? $this->active;
    }

    public function cdn(): ?string
    {
        return $this->frameworks[$this->active]['cdn'] ?? null;
    }

    /**
     * Retourne le tag HTML pour charger le framework dans le preview/front.
     */
    public function renderHead(): string
    {
        $cdn = $this->cdn();

        if (! $cdn) {
            return '';
        }

        if ($this->active === 'tailwind4') {
            return '<script src="' . $cdn . '"></script>';
        }

        return '<link rel="stylesheet" href="' . $cdn . '">';
    }

    /**
     * Enregistre un framework CSS custom.
     *
     * @param  array{label: string, cdn: string|null, version: string}  $config
     */
    public function registerFramework(string $name, array $config): static
    {
        $this->frameworks[$name] = $config;

        return $this;
    }

    /**
     * @return array<string, array{label: string, cdn: string|null, version: string}>
     */
    public function available(): array
    {
        return $this->frameworks;
    }
}
