/**
 * Contract for blocks that support drag & drop.
 */
export interface DraggableInterface {
    /** Whether this block can be dragged */
    isDraggable(): boolean

    /** Whether this block can accept a dropped child */
    canAcceptDrop(blockType: string): boolean

    /** Called when drag starts on this block */
    onDragStart(): void

    /** Called when drag ends */
    onDragEnd(): void

    /** Called when a block is dropped into this container */
    onDrop(blockId: string, index: number): void
}
