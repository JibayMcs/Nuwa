/**
 * Contract for blocks that can be serialized to/from JSON.
 */
export interface SerializableInterface {
    /** Serialize the block to a plain JSON-safe object */
    serialize(): SerializedBlock

    /** Restore the block state from a serialized object */
    deserialize(data: SerializedBlock): void
}

/**
 * JSON representation of a block instance.
 */
export interface SerializedBlock {
    id: string
    type: string
    data: Record<string, unknown>
    html: string
    css: string
    js: string
    order: number
    parentId: string | null
    children: string[]
}
