import type { ClassCategory, FrameworkAdapter } from './FrameworkAdapter'

/**
 * TailwindCSS v4 adapter.
 * Loads Tailwind via CDN and provides utility class metadata
 * for the style manager.
 */
export class TailwindAdapter implements FrameworkAdapter {
    readonly name = 'tailwind4'
    readonly label = 'TailwindCSS v4'

    getHeadHtml(): string {
        return '<script src="https://cdn.tailwindcss.com"></script>'
    }

    getClassCategories(): ClassCategory[] {
        return [
            {
                name: 'Spacing',
                prefixes: ['p-', 'px-', 'py-', 'pt-', 'pr-', 'pb-', 'pl-', 'm-', 'mx-', 'my-', 'mt-', 'mr-', 'mb-', 'ml-', 'gap-', 'space-x-', 'space-y-'],
            },
            {
                name: 'Sizing',
                prefixes: ['w-', 'h-', 'min-w-', 'min-h-', 'max-w-', 'max-h-', 'size-'],
            },
            {
                name: 'Typography',
                prefixes: ['text-', 'font-', 'leading-', 'tracking-', 'uppercase', 'lowercase', 'capitalize', 'truncate', 'line-clamp-'],
            },
            {
                name: 'Colors',
                prefixes: ['text-', 'bg-', 'border-', 'ring-', 'divide-', 'accent-'],
            },
            {
                name: 'Layout',
                prefixes: ['flex', 'grid', 'block', 'inline', 'hidden', 'container', 'columns-', 'float-', 'clear-', 'overflow-'],
            },
            {
                name: 'Flexbox',
                prefixes: ['flex-', 'justify-', 'items-', 'self-', 'grow', 'shrink', 'basis-', 'order-'],
            },
            {
                name: 'Grid',
                prefixes: ['grid-cols-', 'grid-rows-', 'col-span-', 'row-span-', 'auto-cols-', 'auto-rows-'],
            },
            {
                name: 'Borders',
                prefixes: ['border', 'rounded-', 'divide-', 'outline-', 'ring-'],
            },
            {
                name: 'Effects',
                prefixes: ['shadow-', 'opacity-', 'blur-', 'brightness-', 'contrast-', 'grayscale', 'invert', 'saturate-'],
            },
            {
                name: 'Transitions',
                prefixes: ['transition', 'duration-', 'ease-', 'delay-', 'animate-'],
            },
            {
                name: 'Responsive',
                prefixes: ['sm:', 'md:', 'lg:', 'xl:', '2xl:'],
            },
            {
                name: 'States',
                prefixes: ['hover:', 'focus:', 'active:', 'disabled:', 'group-hover:', 'dark:'],
            },
        ]
    }
}
