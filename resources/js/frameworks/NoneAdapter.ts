import type { ClassCategory, FrameworkAdapter } from './FrameworkAdapter'

/**
 * No framework adapter — plain CSS only.
 */
export class NoneAdapter implements FrameworkAdapter {
    readonly name = 'none'
    readonly label = 'No Framework (CSS only)'

    getHeadHtml(): string {
        return ''
    }

    getClassCategories(): ClassCategory[] {
        return []
    }
}
