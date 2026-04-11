import type { SerializedBlock } from '../core/interfaces'
import type { StateManager } from '../core/StateManager'

/**
 * Serialized page structure (sent to the server via Livewire).
 */
export interface SerializedPage {
    blocks: SerializedBlock[]
    rootOrder: string[]
}

/**
 * Serializes the editor state into a JSON-safe structure
 * that can be persisted via Livewire.
 */
export class PageSerializer {
    constructor(private readonly state: StateManager) {}

    /** Serialize the full page state */
    serialize(): SerializedPage {
        const blocks = this.state.getAllBlocks().map((block) => block.serialize())

        return {
            blocks,
            rootOrder: this.state.getRootBlockIds(),
        }
    }
}
