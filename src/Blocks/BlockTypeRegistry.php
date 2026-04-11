<?php

namespace JibayMcs\Nuwa\Blocks;

use InvalidArgumentException;
use JibayMcs\Nuwa\Blocks\Contracts\BlockTypeInterface;

class BlockTypeRegistry
{
    /**
     * @var array<string, BlockTypeInterface>
     */
    protected array $types = [];

    /**
     * Enregistre un type de block.
     */
    public function register(BlockTypeInterface $type): static
    {
        $this->types[$type->name()] = $type;

        return $this;
    }

    /**
     * Enregistre plusieurs types de blocks.
     *
     * @param  array<BlockTypeInterface>  $types
     */
    public function registerMany(array $types): static
    {
        foreach ($types as $type) {
            $this->register($type);
        }

        return $this;
    }

    /**
     * Recupere un type de block par son nom.
     */
    public function get(string $name): BlockTypeInterface
    {
        if (! $this->has($name)) {
            throw new InvalidArgumentException("Block type [{$name}] is not registered.");
        }

        return $this->types[$name];
    }

    /**
     * Verifie si un type de block est enregistre.
     */
    public function has(string $name): bool
    {
        return isset($this->types[$name]);
    }

    /**
     * Retourne tous les types de blocks enregistres.
     *
     * @return array<string, BlockTypeInterface>
     */
    public function all(): array
    {
        return $this->types;
    }

    /**
     * Retourne les types de blocks groupes par categorie.
     *
     * @return array<string, array<BlockTypeInterface>>
     */
    public function grouped(): array
    {
        $grouped = [];

        foreach ($this->types as $type) {
            $grouped[$type->category()][] = $type;
        }

        return $grouped;
    }

    /**
     * Retourne les metadonnees de tous les blocks pour le JS.
     *
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            fn (BlockTypeInterface $type) => $type->toArray(),
            $this->types,
        );
    }

    /**
     * Filtre les blocks compatibles avec le framework CSS actif.
     *
     * @return array<string, BlockTypeInterface>
     */
    public function forFramework(string $framework): array
    {
        return array_filter($this->types, function (BlockTypeInterface $type) use ($framework) {
            $supported = $type->supportedFrameworks();

            return empty($supported) || in_array($framework, $supported);
        });
    }
}
