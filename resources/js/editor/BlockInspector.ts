import type { AbstractBlock } from '../blocks/AbstractBlock'
import type { EventBus } from '../core/EventBus'
import type { StateManager } from '../core/StateManager'
import type { ClassCategory, FrameworkAdapter } from '../frameworks/FrameworkAdapter'

export type InspectorTab = 'properties' | 'styles' | 'code'

/**
 * Block inspector manages the side panel that shows
 * properties, styles, and code for the selected block.
 *
 * The inspector doesn't own DOM — it exposes reactive data
 * consumed by the Alpine component template.
 */
export class BlockInspector {
    activeTab: InspectorTab = 'properties'
    block: AbstractBlock | null = null
    classCategories: ClassCategory[] = []

    constructor(
        private readonly state: StateManager,
        private readonly eventBus: EventBus,
        private readonly frameworkAdapter: FrameworkAdapter,
    ) {
        this.classCategories = frameworkAdapter.getClassCategories()
        this.setupEventListeners()
    }

    /** Switch the active tab */
    setTab(tab: InspectorTab): void {
        this.activeTab = tab
    }

    /** Get the currently inspected block's data for the properties tab */
    getProperties(): Record<string, unknown> {
        return this.block?.data ?? {}
    }

    /** Update a property on the current block */
    updateProperty(key: string, value: unknown): void {
        if (!this.block) return

        this.block.data[key] = value
        this.eventBus.emit('block:updated', {
            blockId: this.block.id,
            field: `data.${key}`,
            value,
        })
    }

    /** Get the current block's HTML */
    getHtml(): string {
        return this.block?.html ?? ''
    }

    /** Update the current block's HTML */
    updateHtml(html: string): void {
        if (!this.block) return

        this.block.html = html
        this.eventBus.emit('block:updated', {
            blockId: this.block.id,
            field: 'html',
            value: html,
        })
    }

    /** Get the current block's CSS */
    getCss(): string {
        return this.block?.css ?? ''
    }

    /** Update the current block's CSS */
    updateCss(css: string): void {
        if (!this.block) return

        this.block.css = css
        this.eventBus.emit('block:updated', {
            blockId: this.block.id,
            field: 'css',
            value: css,
        })
    }

    /** Get the current block's JS */
    getJs(): string {
        return this.block?.js ?? ''
    }

    /** Update the current block's JS */
    updateJs(js: string): void {
        if (!this.block) return

        this.block.js = js
        this.eventBus.emit('block:updated', {
            blockId: this.block.id,
            field: 'js',
            value: js,
        })
    }

    /** Whether a block is currently selected */
    get hasBlock(): boolean {
        return this.block !== null
    }

    /** Get the block type label */
    get blockTypeLabel(): string {
        return this.block?.type ?? ''
    }

    /** Get the editable zones for the current block */
    get editableZones() {
        return this.block?.getEditableZones() ?? []
    }

    /** Setup listeners for selection changes */
    private setupEventListeners(): void {
        this.eventBus.on('block:selected', ({ blockId }) => {
            this.block = this.state.getBlock(blockId) ?? null
        })

        this.eventBus.on('block:deselected', () => {
            this.block = null
        })
    }
}
