import type { BlockTypeDefinition } from './interfaces'

/**
 * Client-side registry of available block types.
 * Mirrors the PHP BlockTypeRegistry.
 *
 * Populated from server data via Filament script data or API call.
 */
export class BlockRegistry {
    private types: Map<string, BlockTypeDefinition> = new Map()

    /** Register a block type definition */
    register(definition: BlockTypeDefinition): void {
        this.types.set(definition.name, definition)
    }

    /** Register multiple block type definitions */
    registerMany(definitions: BlockTypeDefinition[]): void {
        for (const def of definitions) {
            this.register(def)
        }
    }

    /** Get a block type definition by name */
    get(name: string): BlockTypeDefinition | undefined {
        return this.types.get(name)
    }

    /** Check if a block type is registered */
    has(name: string): boolean {
        return this.types.has(name)
    }

    /** Get all registered block type definitions */
    all(): BlockTypeDefinition[] {
        return Array.from(this.types.values())
    }

    /** Get block types grouped by category */
    grouped(): Map<string, BlockTypeDefinition[]> {
        const groups = new Map<string, BlockTypeDefinition[]>()

        for (const def of this.types.values()) {
            const list = groups.get(def.category) ?? []
            list.push(def)
            groups.set(def.category, list)
        }

        return groups
    }

    /** Get all category names */
    categories(): string[] {
        const cats = new Set<string>()
        for (const def of this.types.values()) {
            cats.add(def.category)
        }
        return Array.from(cats)
    }

    /** Remove a block type */
    unregister(name: string): void {
        this.types.delete(name)
    }

    /** Number of registered types */
    get count(): number {
        return this.types.size
    }
}
