import type { AbstractBlock } from '../blocks/AbstractBlock'
import type { EventBus } from '../core/EventBus'
import type { StateManager } from '../core/StateManager'

/**
 * Canvas manages the iframe that renders blocks as real HTML.
 *
 * Handles block rendering, click-to-select, selection overlay,
 * and contenteditable zones within the iframe.
 */
export class Canvas {
    private iframe: HTMLIFrameElement | null = null
    private selectedOverlay: HTMLElement | null = null

    constructor(
        private readonly state: StateManager,
        private readonly eventBus: EventBus,
        private readonly frameworkHead: string,
    ) {
        this.setupEventListeners()
    }

    /** Attach the canvas to an iframe element */
    mount(iframe: HTMLIFrameElement): void {
        this.iframe = iframe
        this.render()
    }

    /** Full re-render of all blocks in the iframe */
    render(): void {
        if (!this.iframe) return

        const blocks = this.state.getRootBlocks()
        const blocksHtml = blocks.map((block) => this.renderBlock(block)).join('\n')

        const doc = `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    ${this.frameworkHead}
    <style>
        body {
            margin: 0;
            padding: 16px;
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }
        [data-nuwa-block] {
            position: relative;
            cursor: pointer;
            transition: outline 0.15s ease;
        }
        [data-nuwa-block]:hover {
            outline: 2px dashed rgba(59, 130, 246, 0.5);
            outline-offset: 2px;
        }
        [data-nuwa-block].nuwa-selected {
            outline: 2px solid rgb(59, 130, 246);
            outline-offset: 2px;
        }
        .nuwa-block-label {
            position: absolute;
            top: -22px;
            left: 0;
            background: rgb(59, 130, 246);
            color: white;
            font-size: 11px;
            font-family: system-ui, sans-serif;
            padding: 2px 8px;
            border-radius: 4px 4px 0 0;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease;
            z-index: 1000;
        }
        [data-nuwa-block]:hover .nuwa-block-label,
        [data-nuwa-block].nuwa-selected .nuwa-block-label {
            opacity: 1;
        }
        .nuwa-empty-canvas {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 300px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            color: #9ca3af;
            font-size: 14px;
        }
        ${this.collectBlockCss(blocks)}
    </style>
</head>
<body>
    ${blocksHtml || '<div class="nuwa-empty-canvas">Click "Add Block" to start building your page</div>'}
    <script>
        document.addEventListener('click', (e) => {
            const blockEl = e.target.closest('[data-nuwa-block]');
            if (blockEl) {
                e.preventDefault();
                e.stopPropagation();
                window.parent.postMessage({
                    type: 'nuwa:block:select',
                    blockId: blockEl.dataset.nuwaBlock
                }, '*');
            } else {
                window.parent.postMessage({ type: 'nuwa:block:deselect' }, '*');
            }
        });

        window.addEventListener('message', (e) => {
            if (e.data?.type === 'nuwa:block:highlight') {
                document.querySelectorAll('[data-nuwa-block]').forEach(el => {
                    el.classList.toggle('nuwa-selected', el.dataset.nuwaBlock === e.data.blockId);
                });
            }
        });
    </script>
</body>
</html>`

        const blob = new Blob([doc], { type: 'text/html' })
        this.iframe.src = URL.createObjectURL(blob)

        this.eventBus.emit('canvas:rendered')
    }

    /** Render a single block wrapper with its content */
    private renderBlock(block: AbstractBlock): string {
        const childrenHtml = this.state
            .getChildren(block.id)
            .map((child) => this.renderBlock(child))
            .join('\n')

        const content = block.render()
        const hasChildren = block.isContainer && childrenHtml

        return `<div data-nuwa-block="${block.id}" data-nuwa-type="${block.type}">
    <span class="nuwa-block-label">${block.type}</span>
    ${content}
    ${hasChildren ? childrenHtml : ''}
</div>`
    }

    /** Collect all block CSS into one stylesheet */
    private collectBlockCss(blocks: AbstractBlock[]): string {
        let css = ''
        for (const block of blocks) {
            if (block.css) {
                css += `/* Block: ${block.id} */\n${block.css}\n`
            }
            const children = this.state.getChildren(block.id)
            if (children.length) {
                css += this.collectBlockCss(children)
            }
        }
        return css
    }

    /** Highlight a block in the iframe */
    highlightBlock(blockId: string | null): void {
        if (!this.iframe?.contentWindow) return
        this.iframe.contentWindow.postMessage(
            { type: 'nuwa:block:highlight', blockId },
            '*',
        )
    }

    /** Setup cross-iframe message listeners */
    private setupEventListeners(): void {
        window.addEventListener('message', (e: MessageEvent) => {
            if (e.data?.type === 'nuwa:block:select') {
                this.state.selectBlock(e.data.blockId)
            } else if (e.data?.type === 'nuwa:block:deselect') {
                this.state.deselectBlock()
            }
        })
    }

    /** Unmount and cleanup */
    destroy(): void {
        this.iframe = null
    }
}
