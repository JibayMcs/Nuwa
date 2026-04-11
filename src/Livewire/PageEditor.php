<?php

namespace JibayMcs\Nuwa\Livewire;

use Illuminate\Contracts\View\View;
use JibayMcs\Nuwa\Blocks\BlockTypeRegistry;
use JibayMcs\Nuwa\Blocks\FrameworkManager;
use JibayMcs\Nuwa\Models\Block;
use JibayMcs\Nuwa\Models\Page;
use Livewire\Component;

class PageEditor extends Component
{
    public Page $page;

    /**
     * Load the page and prepare editor data.
     */
    public function mount(Page $page): void
    {
        $this->page = $page;
    }

    /**
     * Get the block type definitions for the JS registry.
     */
    public function getBlockTypesProperty(): array
    {
        return app(BlockTypeRegistry::class)->toArray();
    }

    /**
     * Get the serialized blocks for initial load.
     */
    public function getInitialBlocksProperty(): array
    {
        return $this->page->allBlocks
            ->map(fn (Block $block) => [
                'id' => (string) $block->id,
                'type' => $block->type,
                'data' => $block->data ?? [],
                'html' => $block->html,
                'css' => $block->css,
                'js' => $block->js,
                'order' => $block->order,
                'parentId' => $block->parent_id ? (string) $block->parent_id : null,
                'children' => $block->children()->orderBy('order')->pluck('id')->map(fn ($id) => (string) $id)->toArray(),
            ])
            ->toArray();
    }

    /**
     * Get root-level block IDs in order.
     */
    public function getRootOrderProperty(): array
    {
        return $this->page->blocks()
            ->orderBy('order')
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    /**
     * Get the active framework name.
     */
    public function getFrameworkProperty(): string
    {
        return app(FrameworkManager::class)->active();
    }

    /**
     * Get the framework head HTML for the canvas.
     */
    public function getFrameworkHeadProperty(): string
    {
        return app(FrameworkManager::class)->renderHead();
    }

    /**
     * Save all blocks from the editor.
     * Called by the Alpine EditorApp component.
     */
    public function saveBlocks(array $data): void
    {
        $existingIds = $this->page->allBlocks()->pluck('id')->toArray();
        $incomingIds = collect($data['blocks'] ?? [])->pluck('id')->toArray();

        // Delete removed blocks
        $removedIds = array_diff($existingIds, $incomingIds);
        if (! empty($removedIds)) {
            Block::whereIn('id', $removedIds)->get()->each->delete();
        }

        // Upsert blocks
        foreach ($data['blocks'] ?? [] as $index => $blockData) {
            $block = Block::find($blockData['id']);

            if ($block) {
                $block->update([
                    'type' => $blockData['type'],
                    'data' => $blockData['data'] ?? [],
                    'order' => $blockData['order'] ?? $index,
                    'parent_id' => $blockData['parentId'] ?? null,
                ]);
            } else {
                $block = Block::create([
                    'page_id' => $this->page->id,
                    'type' => $blockData['type'],
                    'data' => $blockData['data'] ?? [],
                    'order' => $blockData['order'] ?? $index,
                    'parent_id' => $blockData['parentId'] ?? null,
                ]);
            }

            // Save content to storage
            $block->saveContent([
                'html' => $blockData['html'] ?? '',
                'css' => $blockData['css'] ?? '',
                'js' => $blockData['js'] ?? '',
            ]);
        }

        $this->page->touch();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('nuwa::nuwa.editor.saved'),
        ]);
    }

    public function render(): View
    {
        return view('nuwa::livewire.page-editor');
    }
}
