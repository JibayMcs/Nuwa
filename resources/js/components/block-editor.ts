import loader from '@monaco-editor/loader'
import type { editor } from 'monaco-editor'

export interface BlockEditorState {
    html: string
    css: string
    js: string
}

export interface BlockEditorConfig {
    state: BlockEditorState | null
    frameworkHead: string
    defaultHtml: string
    previewHeight: number
    isDisabled: boolean
}

export type EditorTab = 'html' | 'css' | 'js'
export type ViewportMode = 'desktop' | 'tablet' | 'mobile'

const VIEWPORT_WIDTHS: Record<ViewportMode, string> = {
    desktop: '100%',
    tablet: '768px',
    mobile: '375px',
}

const EDITOR_LANGUAGES: Record<EditorTab, string> = {
    html: 'html',
    css: 'css',
    js: 'javascript',
}

const EDITOR_OPTIONS: editor.IStandaloneEditorConstructionOptions = {
    minimap: { enabled: false },
    fontSize: 13,
    lineNumbers: 'on',
    scrollBeyondLastLine: false,
    wordWrap: 'on',
    tabSize: 4,
    automaticLayout: true,
    padding: { top: 8, bottom: 8 },
    renderWhitespace: 'selection',
    bracketPairColorization: { enabled: true },
    scrollbar: {
        verticalScrollbarSize: 8,
        horizontalScrollbarSize: 8,
    },
}

const DEBOUNCE_MS = 500

type BlockEditorComponent = AlpineMagics & {
    state: BlockEditorState
    activeTab: EditorTab
    viewportMode: ViewportMode
    frameworkHead: string
    previewHeight: number
    isDisabled: boolean
    editors: Record<EditorTab, editor.IStandaloneCodeEditor>
    monacoReady: boolean
    viewportWidth: string
    init(): Promise<void>
    switchTab(tab: EditorTab): void
    refreshPreview(): void
    destroy(): void
}

export default function nuwaBlockEditor(config: BlockEditorConfig) {
    let previewTimer: ReturnType<typeof setTimeout> | null = null
    let currentBlobUrl: string | null = null

    return {
        state: config.state ?? { html: '', css: '', js: '' },
        activeTab: 'html' as EditorTab,
        viewportMode: 'desktop' as ViewportMode,
        frameworkHead: config.frameworkHead,
        previewHeight: config.previewHeight,
        isDisabled: config.isDisabled,

        editors: {} as Record<EditorTab, editor.IStandaloneCodeEditor>,
        monacoReady: false,

        get viewportWidth(): string {
            return VIEWPORT_WIDTHS[this.viewportMode as ViewportMode] ?? '100%'
        },

        async init(this: BlockEditorComponent) {
            if (!this.state) {
                this.state = { html: '', css: '', js: '' }
            }

            if (!this.state.html && config.defaultHtml) {
                this.state.html = config.defaultHtml
            }

            const monaco = await loader.init()

            const tabs: EditorTab[] = ['html', 'css', 'js']

            for (const tab of tabs) {
                const container = (this.$refs as Record<string, HTMLElement>)[`${tab}Editor`]
                if (!container) continue

                const instance = monaco.editor.create(container, {
                    ...EDITOR_OPTIONS,
                    value: (this.state as BlockEditorState)[tab] ?? '',
                    language: EDITOR_LANGUAGES[tab],
                    theme: 'vs-dark',
                    readOnly: config.isDisabled,
                })

                instance.onDidChangeModelContent(() => {
                    // Update local state without triggering Livewire sync immediately
                    ;(this.state as BlockEditorState)[tab] = instance.getValue()

                    // Debounce the preview refresh
                    if (previewTimer) clearTimeout(previewTimer)
                    previewTimer = setTimeout(() => this.refreshPreview(), DEBOUNCE_MS)
                })

                this.editors[tab] = instance
            }

            this.monacoReady = true
            this.$nextTick(() => this.refreshPreview())
        },

        switchTab(this: BlockEditorComponent, tab: EditorTab) {
            this.activeTab = tab

            this.$nextTick(() => {
                this.editors[tab]?.layout()
            })
        },

        refreshPreview(this: BlockEditorComponent) {
            const iframe = this.$refs.previewFrame as HTMLIFrameElement | undefined
            if (!iframe) return

            // Revoke previous blob URL to prevent memory leak
            if (currentBlobUrl) {
                URL.revokeObjectURL(currentBlobUrl)
            }

            const state = this.state as BlockEditorState
            const html = state?.html ?? ''
            const css = state?.css ?? ''
            const js = state?.js ?? ''

            const doc = `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    ${this.frameworkHead}
    <style>
        body { margin: 0; padding: 16px; font-family: system-ui, sans-serif; }
        ${css}
    </style>
</head>
<body>
    ${html}
    ${js ? '<script>' + js + '</' + 'script>' : ''}
</body>
</html>`

            const blob = new Blob([doc], { type: 'text/html' })
            currentBlobUrl = URL.createObjectURL(blob)
            iframe.src = currentBlobUrl
        },

        destroy() {
            if (previewTimer) clearTimeout(previewTimer)
            if (currentBlobUrl) URL.revokeObjectURL(currentBlobUrl)
            Object.values(this.editors).forEach((e) => e.dispose())
        },
    }
}
