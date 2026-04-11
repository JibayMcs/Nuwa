import type { EditableZone } from './BlockInterface'

/**
 * Contract for blocks with inline-editable zones (contenteditable, etc.).
 */
export interface EditableInterface {
    /** Get the list of editable zones for this block */
    getEditableZones(): EditableZone[]

    /** Enable editing mode on the block's DOM */
    enableEditing(): void

    /** Disable editing mode and commit changes */
    disableEditing(): void

    /** Update a specific zone's content */
    updateZone(key: string, value: string): void
}
