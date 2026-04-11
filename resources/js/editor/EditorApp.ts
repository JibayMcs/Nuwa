import loader from '@monaco-editor/loader'
import type { editor } from 'monaco-editor'
import type { BlockTypeDefinition, SerializedBlock } from '../core/interfaces'
import { BlockRegistry } from '../core/BlockRegistry'
import { EventBus } from '../core/EventBus'
import { StateManager } from '../core/StateManager'
import type { EditorMode } from '../core/StateManager'
import { AbstractBlock } from '../blocks/AbstractBlock'
import { BlockFactory } from '../serialization/BlockFactory'
import { PageSerializer } from '../serialization/PageSerializer'
import { PageDeserializer } from '../serialization/PageDeserializer'
import { Canvas } from './Canvas'
import { BlockInspector } from './BlockInspector'
import type { InspectorTab } from './BlockInspector'
import { TailwindAdapter } from '../frameworks/TailwindAdapter'
import { BootstrapAdapter } from '../frameworks/BootstrapAdapter'
import { NoneAdapter } from '../frameworks/NoneAdapter'
import type { FrameworkAdapter } from '../frameworks/FrameworkAdapter'

/** Default block implementation for the factory */
class DefaultBlock extends AbstractBlock {}

export type ViewportMode = 'desktop' | 'tablet' | 'mobile'

export interface EditorAppConfig {
    /** Block type definitions from the server (PHP BlockTypeRegistry::toArray()) */
    blockTypes: BlockTypeDefinition[]
    /** Initial blocks data (serialized from DB/storage) */
    initialBlocks: SerializedBlock[]
    /** Root block order */
    rootOrder: string[]
    /** Active CSS framework name */
    framework: string
    /** Framework head HTML for preview */
    frameworkHead: string
    /** Page ID */
    pageId: number
}

const VIEWPORT_WIDTHS: Record<ViewportMode, string> = {
    desktop: '100%',
    tablet: '768px',
    mobile: '375px',
}

/**
 * Main Alpine.js component for the visual page editor.
 *
 * Orchestrates: Canvas, BlockInspector, StateManager, EventBus.
 * Communicates with the Livewire PageEditor component for persistence.
 */
export default function nuwaEditorApp(this: AlpineMagics, config: EditorAppConfig) {
    // ── Internal services (not exposed to Alpine template) ──
    const registry = new BlockRegistry()
    registry.registerMany(config.blockTypes)

    const eventBus = new EventBus()
    const state = new StateManager(eventBus)
    const factory = new BlockFactory(registry, DefaultBlock)
    const serializer = new PageSerializer(state)
    const deserializer = new PageDeserializer(state, factory)

    const frameworkAdapter = resolveFrameworkAdapter(config.framework)
    const canvas = new Canvas(state, eventBus, config.frameworkHead)
    const inspector = new BlockInspector(state, eventBus, frameworkAdapter)

    type EditorComponent = AlpineMagics & {
        // Block picker
        blockTypes: BlockTypeDefinition[]
        blockCategories: string[]
        pickerOpen: boolean
        pickerFilter: string
        // Inspector
        inspectorTab: InspectorTab
        selectedBlock: AbstractBlock | null
        selectedBlockData: Record<string, unknown>
        // Editor state
        viewportMode: ViewportMode
        mode: EditorMode
        isDirty: boolean
        saving: boolean
        // Monaco editors
        monacoEditors: Record<string, editor.IStandaloneCodeEditor>
        monacoReady: boolean
        // Methods
        init(): Promise<void>
        addBlock(typeName: string, parentId?: string | null): void
        removeSelectedBlock(): void
        selectBlock(blockId: string): void
        deselectBlock(): void
        setInspectorTab(tab: InspectorTab): void
        updateProperty(key: string, value: unknown): void
        setViewport(mode: ViewportMode): void
        togglePreview(): void
        save(): Promise<void>
        filteredBlockTypes(): BlockTypeDefinition[]
        mountInspectorEditors(monaco: any): void
        viewportWidth: string
        destroy(): void
    }

    return {
        // ── Block picker state ──────────────────────────────
        blockTypes: registry.all(),
        blockCategories: registry.categories(),
        pickerOpen: false,
        pickerFilter: '',

        // ── Inspector state ─────────────────────────────────
        inspectorTab: 'properties' as InspectorTab,
        selectedBlock: null as AbstractBlock | null,
        selectedBlockData: {} as Record<string, unknown>,

        // ── Editor state ────────────────────────────────────
        viewportMode: 'desktop' as ViewportMode,
        mode: 'edit' as EditorMode,
        isDirty: false,
        saving: false,

        // ── Monaco ──────────────────────────────────────────
        monacoEditors: {} as Record<string, editor.IStandaloneCodeEditor>,
        monacoReady: false,

        get viewportWidth(): string {
            return VIEWPORT_WIDTHS[this.viewportMode as ViewportMode] ?? '100%'
        },

        // ── Lifecycle ───────────────────────────────────────

        async init(this: EditorComponent) {
            // Load initial blocks
            if (config.initialBlocks.length > 0) {
                deserializer.deserialize({
                    blocks: config.initialBlocks,
                    rootOrder: config.rootOrder,
                })
            }

            // Mount canvas
            this.$nextTick(() => {
                const iframe = this.$refs.editorCanvas as HTMLIFrameElement
                if (iframe) canvas.mount(iframe)
            })

            // Listen for selection changes
            eventBus.on('block:selected', ({ blockId }) => {
                const block = state.getBlock(blockId)
                this.selectedBlock = block ?? null
                this.selectedBlockData = block ? { ...block.data } : {}
                canvas.highlightBlock(blockId)
            })

            eventBus.on('block:deselected', () => {
                this.selectedBlock = null
                this.selectedBlockData = {}
                canvas.highlightBlock(null)
            })

            // Track dirty state
            eventBus.on('editor:dirty', ({ isDirty }) => {
                this.isDirty = isDirty
            })

            // Re-render canvas on any block change
            eventBus.on('block:added', () => canvas.render())
            eventBus.on('block:removed', () => canvas.render())
            eventBus.on('block:moved', () => canvas.render())
            eventBus.on('block:updated', () => canvas.render())
            eventBus.on('block:reordered', () => canvas.render())

            // Init Monaco for inspector code tab
            const monaco = await loader.init()
            this.monacoReady = true

            // We'll create Monaco editors lazily when the code tab is shown
            this.$watch('inspectorTab', (tab: unknown) => {
                if (tab === 'code' && this.selectedBlock) {
                    this.$nextTick(() => this.mountInspectorEditors(monaco))
                }
            })
        },

        // ── Block operations ────────────────────────────────

        addBlock(this: EditorComponent, typeName: string, parentId: string | null = null) {
            const block = factory.create(typeName, parentId)
            if (!block) return

            state.addBlock(block)
            this.pickerOpen = false
            this.pickerFilter = ''
        },

        removeSelectedBlock(this: EditorComponent) {
            if (!this.selectedBlock) return
            state.removeBlock(this.selectedBlock.id)
        },

        selectBlock(this: EditorComponent, blockId: string) {
            state.selectBlock(blockId)
        },

        deselectBlock(this: EditorComponent) {
            state.deselectBlock()
        },

        // ── Inspector ───────────────────────────────────────

        setInspectorTab(this: EditorComponent, tab: InspectorTab) {
            this.inspectorTab = tab
            inspector.setTab(tab)
        },

        updateProperty(this: EditorComponent, key: string, value: unknown) {
            inspector.updateProperty(key, value)
            this.selectedBlockData = { ...this.selectedBlock!.data }
        },

        // ── Viewport ────────────────────────────────────────

        setViewport(this: EditorComponent, mode: ViewportMode) {
            this.viewportMode = mode
        },

        togglePreview(this: EditorComponent) {
            const newMode = this.mode === 'edit' ? 'preview' : 'edit'
            this.mode = newMode
            state.setMode(newMode)
        },

        // ── Persistence ─────────────────────────────────────

        async save(this: EditorComponent) {
            this.saving = true

            const data = serializer.serialize()

            try {
                await (this.$wire as any).saveBlocks(data)
                state.markClean()
            } catch (err) {
                console.error('Failed to save:', err)
            } finally {
                this.saving = false
            }
        },

        // ── Block picker helpers ────────────────────────────

        filteredBlockTypes(this: EditorComponent): BlockTypeDefinition[] {
            if (!this.pickerFilter) return this.blockTypes

            const filter = this.pickerFilter.toLowerCase()
            return this.blockTypes.filter(
                (bt) =>
                    bt.label.toLowerCase().includes(filter) ||
                    bt.category.toLowerCase().includes(filter),
            )
        },

        // ── Monaco (inspector code editors) ─────────────────

        mountInspectorEditors(this: EditorComponent, monaco: any) {
            if (!this.selectedBlock) return

            const languages = { html: 'html', css: 'css', js: 'javascript' }
            const fields = ['html', 'css', 'js'] as const

            for (const field of fields) {
                const ref = this.$refs[`inspector_${field}`] as HTMLElement | undefined
                if (!ref) continue

                // Dispose existing editor if any
                this.monacoEditors[field]?.dispose()

                const instance = monaco.editor.create(ref, {
                    value: (this.selectedBlock as any)[field] ?? '',
                    language: languages[field],
                    theme: 'vs-dark',
                    minimap: { enabled: false },
                    fontSize: 12,
                    lineNumbers: 'on',
                    scrollBeyondLastLine: false,
                    wordWrap: 'on',
                    automaticLayout: true,
                    padding: { top: 4, bottom: 4 },
                })

                instance.onDidChangeModelContent(() => {
                    const value = instance.getValue()
                    if (field === 'html') inspector.updateHtml(value)
                    else if (field === 'css') inspector.updateCss(value)
                    else inspector.updateJs(value)
                })

                this.monacoEditors[field] = instance
            }
        },

        // ── Cleanup ─────────────────────────────────────────

        destroy(this: EditorComponent) {
            Object.values(this.monacoEditors).forEach((e) => e.dispose())
            canvas.destroy()
            eventBus.off()
        },
    }
}

/** Resolve the framework adapter from its name */
function resolveFrameworkAdapter(name: string): FrameworkAdapter {
    switch (name) {
        case 'tailwind4':
            return new TailwindAdapter()
        case 'bootstrap':
            return new BootstrapAdapter()
        default:
            return new NoneAdapter()
    }
}
