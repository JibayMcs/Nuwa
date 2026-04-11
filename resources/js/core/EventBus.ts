/**
 * Typed pub/sub event bus for decoupled communication between editor components.
 *
 * Usage:
 *   const bus = new EventBus()
 *   const off = bus.on('block:selected', (payload) => { ... })
 *   bus.emit('block:selected', { blockId: '123' })
 *   off() // unsubscribe
 */

export type EventHandler<T = unknown> = (payload: T) => void

/** All editor events with their payload types */
export interface EditorEvents {
    'block:selected': { blockId: string }
    'block:deselected': { blockId: string }
    'block:added': { blockId: string; type: string; parentId: string | null }
    'block:removed': { blockId: string }
    'block:moved': { blockId: string; fromIndex: number; toIndex: number; parentId: string | null }
    'block:updated': { blockId: string; field: string; value: unknown }
    'block:reordered': { parentId: string | null; order: string[] }
    'canvas:rendered': void
    'canvas:cleared': void
    'editor:dirty': { isDirty: boolean }
    'editor:saved': void
    'editor:mode': { mode: 'edit' | 'preview' }
    'history:undo': void
    'history:redo': void
    'history:changed': { canUndo: boolean; canRedo: boolean }
    'drag:start': { blockId: string }
    'drag:end': { blockId: string }
    'drag:over': { targetId: string; position: 'before' | 'after' | 'inside' }
}

export class EventBus {
    private listeners: Map<string, Set<EventHandler>> = new Map()

    /** Subscribe to an event. Returns an unsubscribe function. */
    on<K extends keyof EditorEvents>(
        event: K,
        handler: EventHandler<EditorEvents[K]>,
    ): () => void {
        const handlers = this.listeners.get(event) ?? new Set()
        handlers.add(handler as EventHandler)
        this.listeners.set(event, handlers)

        return () => {
            handlers.delete(handler as EventHandler)
            if (handlers.size === 0) {
                this.listeners.delete(event)
            }
        }
    }

    /** Subscribe to an event, auto-unsubscribe after first call */
    once<K extends keyof EditorEvents>(
        event: K,
        handler: EventHandler<EditorEvents[K]>,
    ): () => void {
        const off = this.on(event, (payload) => {
            off()
            handler(payload)
        })
        return off
    }

    /** Emit an event to all subscribers */
    emit<K extends keyof EditorEvents>(
        event: K,
        ...args: EditorEvents[K] extends void ? [] : [EditorEvents[K]]
    ): void {
        const handlers = this.listeners.get(event)
        if (!handlers) return

        const payload = args[0]
        for (const handler of handlers) {
            handler(payload)
        }
    }

    /** Remove all listeners for an event, or all listeners entirely */
    off(event?: keyof EditorEvents): void {
        if (event) {
            this.listeners.delete(event)
        } else {
            this.listeners.clear()
        }
    }
}
