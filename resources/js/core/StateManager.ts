import type { AbstractBlock } from '../blocks/AbstractBlock'
import { EventBus } from './EventBus'

export type EditorMode = 'edit' | 'preview'

/**
 * Centralized state manager for the editor.
 *
 * Holds the current page state: blocks, selection, dirty flag, mode.
 * All mutations go through this class so the EventBus can track changes.
 */
export class StateManager {
    private blocks: Map<string, AbstractBlock> = new Map()
    private rootBlockIds: string[] = []
    private selectedBlockId: string | null = null
    private dirty: boolean = false
    private mode: EditorMode = 'edit'

    constructor(private readonly eventBus: EventBus) {}

    // ── Blocks ──────────────────────────────────────────────

    /** Add a block to the state */
    addBlock(block: AbstractBlock, index?: number): void {
        this.blocks.set(block.id, block)

        if (block.parentId === null) {
            if (index !== undefined) {
                this.rootBlockIds.splice(index, 0, block.id)
            } else {
                this.rootBlockIds.push(block.id)
            }
        } else {
            const parent = this.blocks.get(block.parentId)
            if (parent) {
                if (index !== undefined) {
                    parent.children.splice(index, 0, block.id)
                } else {
                    parent.children.push(block.id)
                }
            }
        }

        this.markDirty()
        this.eventBus.emit('block:added', {
            blockId: block.id,
            type: block.type,
            parentId: block.parentId,
        })
    }

    /** Remove a block by ID (and its children recursively) */
    removeBlock(blockId: string): void {
        const block = this.blocks.get(blockId)
        if (!block) return

        // Recursively remove children
        for (const childId of [...block.children]) {
            this.removeBlock(childId)
        }

        // Remove from parent's children list
        if (block.parentId) {
            const parent = this.blocks.get(block.parentId)
            if (parent) {
                parent.children = parent.children.filter((id) => id !== blockId)
            }
        } else {
            this.rootBlockIds = this.rootBlockIds.filter((id) => id !== blockId)
        }

        // Deselect if this was the selected block
        if (this.selectedBlockId === blockId) {
            this.deselectBlock()
        }

        block.unmount()
        this.blocks.delete(blockId)
        this.markDirty()
        this.eventBus.emit('block:removed', { blockId })
    }

    /** Get a block by ID */
    getBlock(blockId: string): AbstractBlock | undefined {
        return this.blocks.get(blockId)
    }

    /** Get all blocks as an array */
    getAllBlocks(): AbstractBlock[] {
        return Array.from(this.blocks.values())
    }

    /** Get root-level block IDs (in order) */
    getRootBlockIds(): string[] {
        return [...this.rootBlockIds]
    }

    /** Get root-level blocks (in order) */
    getRootBlocks(): AbstractBlock[] {
        return this.rootBlockIds
            .map((id) => this.blocks.get(id))
            .filter((b): b is AbstractBlock => b !== undefined)
    }

    /** Get children of a block (in order) */
    getChildren(parentId: string): AbstractBlock[] {
        const parent = this.blocks.get(parentId)
        if (!parent) return []

        return parent.children
            .map((id) => this.blocks.get(id))
            .filter((b): b is AbstractBlock => b !== undefined)
    }

    /** Move a block to a new position */
    moveBlock(blockId: string, newParentId: string | null, newIndex: number): void {
        const block = this.blocks.get(blockId)
        if (!block) return

        const oldParentId = block.parentId

        // Remove from old parent
        if (oldParentId) {
            const oldParent = this.blocks.get(oldParentId)
            if (oldParent) {
                oldParent.children = oldParent.children.filter((id) => id !== blockId)
            }
        } else {
            this.rootBlockIds = this.rootBlockIds.filter((id) => id !== blockId)
        }

        // Add to new parent
        block.parentId = newParentId

        if (newParentId) {
            const newParent = this.blocks.get(newParentId)
            if (newParent) {
                newParent.children.splice(newIndex, 0, blockId)
            }
        } else {
            this.rootBlockIds.splice(newIndex, 0, blockId)
        }

        this.markDirty()
        this.eventBus.emit('block:moved', {
            blockId,
            fromIndex: 0,
            toIndex: newIndex,
            parentId: newParentId,
        })
    }

    /** Update block order within its parent */
    reorderBlocks(parentId: string | null, orderedIds: string[]): void {
        if (parentId) {
            const parent = this.blocks.get(parentId)
            if (parent) {
                parent.children = orderedIds
            }
        } else {
            this.rootBlockIds = orderedIds
        }

        // Update order property on each block
        orderedIds.forEach((id, index) => {
            const block = this.blocks.get(id)
            if (block) block.order = index
        })

        this.markDirty()
        this.eventBus.emit('block:reordered', { parentId, order: orderedIds })
    }

    // ── Selection ───────────────────────────────────────────

    /** Select a block */
    selectBlock(blockId: string): void {
        if (this.selectedBlockId === blockId) return

        if (this.selectedBlockId) {
            this.deselectBlock()
        }

        const block = this.blocks.get(blockId)
        if (!block) return

        block.selected = true
        this.selectedBlockId = blockId
        this.eventBus.emit('block:selected', { blockId })
    }

    /** Deselect the current block */
    deselectBlock(): void {
        if (!this.selectedBlockId) return

        const block = this.blocks.get(this.selectedBlockId)
        if (block) {
            block.selected = false
            block.disableEditing()
        }

        const blockId = this.selectedBlockId
        this.selectedBlockId = null
        this.eventBus.emit('block:deselected', { blockId })
    }

    /** Get the currently selected block */
    getSelectedBlock(): AbstractBlock | undefined {
        return this.selectedBlockId ? this.blocks.get(this.selectedBlockId) : undefined
    }

    /** Get the currently selected block ID */
    getSelectedBlockId(): string | null {
        return this.selectedBlockId
    }

    // ── Dirty flag ──────────────────────────────────────────

    /** Mark the state as modified */
    markDirty(): void {
        if (!this.dirty) {
            this.dirty = true
            this.eventBus.emit('editor:dirty', { isDirty: true })
        }
    }

    /** Mark the state as saved */
    markClean(): void {
        if (this.dirty) {
            this.dirty = false
            this.eventBus.emit('editor:dirty', { isDirty: false })
        }
    }

    /** Whether the state has unsaved changes */
    isDirty(): boolean {
        return this.dirty
    }

    // ── Mode ────────────────────────────────────────────────

    /** Switch editor mode */
    setMode(mode: EditorMode): void {
        if (this.mode === mode) return
        this.mode = mode
        this.eventBus.emit('editor:mode', { mode })
    }

    /** Get current editor mode */
    getMode(): EditorMode {
        return this.mode
    }

    // ── Reset ───────────────────────────────────────────────

    /** Clear all blocks and reset state */
    clear(): void {
        for (const block of this.blocks.values()) {
            block.unmount()
        }

        this.blocks.clear()
        this.rootBlockIds = []
        this.selectedBlockId = null
        this.dirty = false
        this.eventBus.emit('canvas:cleared')
    }
}
