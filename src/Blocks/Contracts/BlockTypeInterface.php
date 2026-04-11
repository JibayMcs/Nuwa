<?php

namespace JibayMcs\Nuwa\Blocks\Contracts;

interface BlockTypeInterface
{
    /**
     * Identifiant unique du block (ex: 'hero', 'text', 'image').
     */
    public function name(): string;

    /**
     * Label affiche dans l'UI (traduit).
     */
    public function label(): string;

    /**
     * Icone Heroicon ou Filament (ex: Heroicon::OutlinedDocumentText).
     */
    public function icon(): string;

    /**
     * Categorie pour le regroupement dans le picker (ex: 'content', 'layout', 'media').
     */
    public function category(): string;

    /**
     * Nom de la vue Blade pour le rendu front ET le preview dans le canvas.
     * Ex: 'nuwa::blocks.hero'
     */
    public function bladeView(): string;

    /**
     * Donnees par defaut du block a la creation.
     *
     * @return array<string, mixed>
     */
    public function defaultData(): array;

    /**
     * Zones editables inline dans le canvas (contenteditable).
     * Format: ['title' => ['type' => 'text', 'selector' => 'h1'], ...]
     *
     * @return array<string, array{type: string, selector: string}>
     */
    public function editableZones(): array;

    /**
     * Scripts JS du block (chemins relatifs ou URLs).
     * Ces scripts sont injectes dans le rendu front.
     *
     * @return array<string>
     */
    public function scripts(): array;

    /**
     * Styles CSS du block (chemins relatifs ou URLs).
     *
     * @return array<string>
     */
    public function styles(): array;

    /**
     * JS inline execute a l'init du block dans le front.
     * Retourne null si pas de JS inline.
     */
    public function inlineScript(): ?string;

    /**
     * Regles de validation Laravel pour les donnees du block.
     *
     * @return array<string, mixed>
     */
    public function rules(): array;

    /**
     * Rendu HTML du block avec les donnees fournies.
     *
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): string;

    /**
     * Ce block peut-il contenir des enfants (container) ?
     */
    public function isContainer(): bool;

    /**
     * Frameworks CSS supportes par ce block.
     * Vide = tous. Ex: ['tailwind4', 'bootstrap']
     *
     * @return array<string>
     */
    public function supportedFrameworks(): array;
}
