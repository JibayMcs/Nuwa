/**
 * Core contract for all blocks in the editor.
 * Every block type must implement this interface.
 */
export interface BlockInterface {
    /** Unique instance identifier */
    readonly id: string

    /** Block type key (matches PHP BlockTypeRegistry) */
    readonly type: string

    /** Block data payload */
    data: Record<string, unknown>

    /** HTML content of the block */
    html: string

    /** CSS content of the block */
    css: string

    /** JavaScript content of the block */
    js: string

    /** Display order within parent */
    order: number

    /** Parent block ID (for nesting) */
    parentId: string | null

    /** Child block IDs */
    children: string[]

    /** Whether the block is currently selected */
    selected: boolean
}

/**
 * Static metadata about a block type.
 * Mirrors the PHP BlockTypeInterface definition.
 */
export interface BlockTypeDefinition {
    /** Unique type key */
    name: string

    /** Human-readable label */
    label: string

    /** Icon identifier (heroicon) */
    icon: string

    /** Category for grouping in the picker */
    category: string

    /** Default data for new instances */
    defaultData: Record<string, unknown>

    /** Default HTML template */
    defaultHtml: string

    /** Editable zones configuration */
    editableZones: EditableZone[]

    /** Whether this block can contain children */
    isContainer: boolean

    /** Supported CSS frameworks */
    supportedFrameworks: string[]
}

/**
 * An editable zone within a block.
 */
export interface EditableZone {
    /** Zone key */
    key: string

    /** Zone type: text, richtext, image, link */
    type: 'text' | 'richtext' | 'image' | 'link'

    /** CSS selector to find the zone in the rendered HTML */
    selector: string
}
