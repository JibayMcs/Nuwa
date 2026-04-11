/**
 * Contract for blocks that can render HTML in the canvas.
 */
export interface RenderableInterface {
    /** Render the block as an HTML string for the canvas */
    render(): string

    /** Re-render the block after data changes */
    rerender(): void

    /** Get the DOM element in the canvas (once mounted) */
    getElement(): HTMLElement | null
}
