<?php

namespace JibayMcs\Nuwa\Blocks;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use JibayMcs\Nuwa\Blocks\Contracts\BlockTypeInterface;

abstract class AbstractBlockType implements BlockTypeInterface
{
    public function label(): string
    {
        return __("nuwa::nuwa.blocks.{$this->name()}");
    }

    public function category(): string
    {
        return 'content';
    }

    public function bladeView(): string
    {
        return "nuwa::blocks.{$this->name()}";
    }

    public function defaultData(): array
    {
        return [];
    }

    public function editableZones(): array
    {
        return [];
    }

    public function scripts(): array
    {
        return [];
    }

    public function styles(): array
    {
        return [];
    }

    public function inlineScript(): ?string
    {
        return null;
    }

    public function rules(): array
    {
        return [];
    }

    public function isContainer(): bool
    {
        return false;
    }

    public function supportedFrameworks(): array
    {
        return [];
    }

    public function render(array $data): string
    {
        $mergedData = array_merge($this->defaultData(), $data);

        return View::make($this->bladeView(), ['data' => $mergedData])->render();
    }

    /**
     * Valide les donnees du block. Retourne les erreurs ou un tableau vide.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, array<string>>
     */
    public function validate(array $data): array
    {
        if (empty($this->rules())) {
            return [];
        }

        $validator = Validator::make($data, $this->rules());

        return $validator->fails() ? $validator->errors()->toArray() : [];
    }

    /**
     * Retourne les metadonnees du block pour l'API et le registre JS.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'label' => $this->label(),
            'icon' => $this->icon(),
            'category' => $this->category(),
            'isContainer' => $this->isContainer(),
            'defaultData' => $this->defaultData(),
            'editableZones' => $this->editableZones(),
            'supportedFrameworks' => $this->supportedFrameworks(),
        ];
    }
}
