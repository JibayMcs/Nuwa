/**
 * Alpine.js magic properties available on component data objects.
 * These are injected by Alpine at runtime.
 */
interface AlpineMagics {
    $el: HTMLElement
    $refs: Record<string, HTMLElement>
    $watch: (property: string, callback: (value: unknown) => void) => void
    $nextTick: (callback: () => void) => void
    $dispatch: (event: string, detail?: unknown) => void
    $id: (name: string, key?: string | number) => string
    $root: HTMLElement
    $data: Record<string, unknown>
    $store: Record<string, unknown>
    $wire: Record<string, (...args: any[]) => Promise<any>>
}
