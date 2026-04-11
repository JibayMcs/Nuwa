import type { BlockTypeDefinition, SerializedBlock } from '../core/interfaces'
import type { AbstractBlock } from '../blocks/AbstractBlock'
import { BlockRegistry } from '../core/BlockRegistry'

/** Constructor type for block classes */
export type BlockConstructor = new (
    id: string,
    definition: BlockTypeDefinition,
    initial?: Partial<SerializedBlock>,
) => AbstractBlock

/**
 * Factory for creating block instances from type name or serialized data.
 *
 * Uses the BlockRegistry for type definitions and a map of constructors
 * for instantiation. Falls back to a default constructor if no specific
 * class is registered for the type.
 */
export class BlockFactory {
    private constructors: Map<string, BlockConstructor> = new Map()
    private idCounter: number = 0

    constructor(
        private readonly registry: BlockRegistry,
        private readonly defaultConstructor: BlockConstructor,
    ) {}

    /** Register a block class for a specific type */
    registerConstructor(typeName: string, constructor: BlockConstructor): void {
        this.constructors.set(typeName, constructor)
    }

    /** Create a new block instance from a type name (new block) */
    create(typeName: string, parentId: string | null = null): AbstractBlock | null {
        const definition = this.registry.get(typeName)
        if (!definition) return null

        const id = this.generateId()
        const Ctor = this.constructors.get(typeName) ?? this.defaultConstructor

        return new Ctor(id, definition, { parentId })
    }

    /** Restore a block instance from serialized data */
    restore(data: SerializedBlock): AbstractBlock | null {
        const definition = this.registry.get(data.type)
        if (!definition) return null

        const Ctor = this.constructors.get(data.type) ?? this.defaultConstructor

        return new Ctor(data.id, definition, data)
    }

    /** Generate a unique block ID */
    private generateId(): string {
        this.idCounter++
        return `block_${Date.now()}_${this.idCounter}`
    }
}
