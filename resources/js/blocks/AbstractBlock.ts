import type {
    BlockInterface,
    BlockTypeDefinition,
    EditableZone,
    RenderableInterface,
    SerializableInterface,
    SerializedBlock,
    EditableInterface,
} from '../core/interfaces'

/**
 * Base class for all editor blocks (Template Method pattern).
 *
 * Provides default implementations for rendering, serialization,
 * and editing. Concrete blocks override specific methods.
 */
export abstract class AbstractBlock
    implements BlockInterface, RenderableInterface, SerializableInterface, EditableInterface
{
    readonly id: string
    readonly type: string
    data: Record<string, unknown>
    html: string
    css: string
    js: string
    order: number
    parentId: string | null
    children: string[]
    selected: boolean

    protected element: HTMLElement | null = null
    protected editing: boolean = false

    constructor(
        id: string,
        protected readonly definition: BlockTypeDefinition,
        initial?: Partial<SerializedBlock>,
    ) {
        this.id = id
        this.type = definition.name
        this.data = initial?.data ?? { ...definition.defaultData }
        this.html = initial?.html ?? definition.defaultHtml
        this.css = initial?.css ?? ''
        this.js = initial?.js ?? ''
        this.order = initial?.order ?? 0
        this.parentId = initial?.parentId ?? null
        this.children = initial?.children ?? []
        this.selected = false
    }

    // ── RenderableInterface ─────────────────────────────────

    render(): string {
        return this.html
    }

    rerender(): void {
        if (!this.element) return
        this.element.innerHTML = this.render()
        this.applyStyles()
    }

    getElement(): HTMLElement | null {
        return this.element
    }

    /** Attach this block to a DOM element (called after canvas mount) */
    mount(element: HTMLElement): void {
        this.element = element
        this.element.dataset.blockId = this.id
        this.element.dataset.blockType = this.type
        this.applyStyles()
    }

    /** Detach from the DOM */
    unmount(): void {
        this.element = null
    }

    // ── SerializableInterface ───────────────────────────────

    serialize(): SerializedBlock {
        return {
            id: this.id,
            type: this.type,
            data: { ...this.data },
            html: this.html,
            css: this.css,
            js: this.js,
            order: this.order,
            parentId: this.parentId,
            children: [...this.children],
        }
    }

    deserialize(data: SerializedBlock): void {
        this.data = { ...data.data }
        this.html = data.html
        this.css = data.css
        this.js = data.js
        this.order = data.order
        this.parentId = data.parentId
        this.children = [...data.children]
        this.rerender()
    }

    // ── EditableInterface ───────────────────────────────────

    getEditableZones(): EditableZone[] {
        return this.definition.editableZones
    }

    enableEditing(): void {
        if (!this.element || this.editing) return
        this.editing = true

        for (const zone of this.getEditableZones()) {
            const el = this.element.querySelector(zone.selector)
            if (el instanceof HTMLElement && zone.type === 'text') {
                el.contentEditable = 'true'
                el.addEventListener('blur', () => this.commitZone(zone.key, el))
            }
        }
    }

    disableEditing(): void {
        if (!this.element || !this.editing) return
        this.editing = false

        for (const zone of this.getEditableZones()) {
            const el = this.element.querySelector(zone.selector)
            if (el instanceof HTMLElement) {
                el.contentEditable = 'false'
            }
        }
    }

    updateZone(key: string, value: string): void {
        this.data[key] = value
    }

    // ── Helpers ─────────────────────────────────────────────

    /** Whether this block type can contain child blocks */
    get isContainer(): boolean {
        return this.definition.isContainer
    }

    /** Apply block-level CSS via a <style> tag scoped to this block */
    protected applyStyles(): void {
        if (!this.element || !this.css) return

        const styleId = `nuwa-block-style-${this.id}`
        let styleEl = document.getElementById(styleId)

        if (!styleEl) {
            styleEl = document.createElement('style')
            styleEl.id = styleId
            this.element.appendChild(styleEl)
        }

        styleEl.textContent = this.css
    }

    /** Commit an editable zone value back to data */
    private commitZone(key: string, el: HTMLElement): void {
        const value = el.innerHTML
        this.updateZone(key, value)
    }
}
