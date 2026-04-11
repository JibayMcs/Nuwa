import type { StateManager } from '../core/StateManager'
import type { BlockFactory } from './BlockFactory'
import type { SerializedPage } from './PageSerializer'

/**
 * Restores editor state from serialized page data.
 */
export class PageDeserializer {
    constructor(
        private readonly state: StateManager,
        private readonly factory: BlockFactory,
    ) {}

    /** Deserialize a page and populate the state manager */
    deserialize(data: SerializedPage): void {
        this.state.clear()

        // First pass: create all block instances
        const blocksById = new Map<string, ReturnType<BlockFactory['restore']>>()

        for (const serialized of data.blocks) {
            const block = this.factory.restore(serialized)
            if (block) {
                blocksById.set(block.id, block)
            }
        }

        // Second pass: add blocks in root order, then children follow naturally
        for (const rootId of data.rootOrder) {
            const block = blocksById.get(rootId)
            if (block) {
                this.addBlockRecursive(block, blocksById)
            }
        }
    }

    /** Recursively add a block and its children to the state */
    private addBlockRecursive(
        block: NonNullable<ReturnType<BlockFactory['restore']>>,
        allBlocks: Map<string, ReturnType<BlockFactory['restore']>>,
    ): void {
        const childIds = [...block.children]
        block.children = []

        this.state.addBlock(block)

        for (const childId of childIds) {
            const child = allBlocks.get(childId)
            if (child) {
                this.addBlockRecursive(child, allBlocks)
            }
        }
    }
}
