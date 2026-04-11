/**
 * Contract for CSS framework adapters.
 *
 * Each adapter provides the necessary head tags and utility
 * class information for a specific CSS framework.
 */
export interface FrameworkAdapter {
    /** Framework identifier */
    readonly name: string

    /** Human-readable label */
    readonly label: string

    /** HTML to inject in the <head> (CDN links, etc.) */
    getHeadHtml(): string

    /** Available utility class categories for the style manager */
    getClassCategories(): ClassCategory[]
}

/**
 * A category of CSS classes available in the framework.
 * Used by the style manager to offer autocomplete/suggestions.
 */
export interface ClassCategory {
    /** Category name (e.g. "Spacing", "Typography", "Colors") */
    name: string

    /** CSS class prefixes in this category (e.g. ["p-", "px-", "py-", "m-"]) */
    prefixes: string[]
}
