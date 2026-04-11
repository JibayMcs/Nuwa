import type { ClassCategory, FrameworkAdapter } from './FrameworkAdapter'

/**
 * Bootstrap 5 adapter.
 */
export class BootstrapAdapter implements FrameworkAdapter {
    readonly name = 'bootstrap'
    readonly label = 'Bootstrap 5'

    getHeadHtml(): string {
        return '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
    }

    getClassCategories(): ClassCategory[] {
        return [
            {
                name: 'Spacing',
                prefixes: ['p-', 'px-', 'py-', 'pt-', 'pe-', 'pb-', 'ps-', 'm-', 'mx-', 'my-', 'mt-', 'me-', 'mb-', 'ms-', 'g-', 'gx-', 'gy-'],
            },
            {
                name: 'Typography',
                prefixes: ['fs-', 'fw-', 'fst-', 'text-', 'lh-', 'display-', 'lead', 'h1', 'h2', 'h3'],
            },
            {
                name: 'Colors',
                prefixes: ['text-', 'bg-', 'border-', 'link-'],
            },
            {
                name: 'Layout',
                prefixes: ['d-', 'container', 'row', 'col-', 'float-', 'overflow-', 'position-'],
            },
            {
                name: 'Flexbox',
                prefixes: ['d-flex', 'flex-', 'justify-content-', 'align-items-', 'align-self-', 'order-'],
            },
            {
                name: 'Borders',
                prefixes: ['border', 'rounded-'],
            },
            {
                name: 'Effects',
                prefixes: ['shadow-', 'opacity-'],
            },
            {
                name: 'Responsive',
                prefixes: ['sm-', 'md-', 'lg-', 'xl-', 'xxl-'],
            },
        ]
    }
}
