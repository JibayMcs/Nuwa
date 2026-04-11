@php
    $variant = match($data['variant'] ?? 'primary') {
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300',
        'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white',
        'ghost' => 'text-blue-600 hover:bg-blue-50',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        default => $data['variant'],
    };

    $size = match($data['size'] ?? 'md') {
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-base',
        'lg' => 'px-8 py-4 text-lg',
        default => 'px-6 py-3 text-base',
    };
@endphp
<a
    href="{{ $data['url'] ?? '#' }}"
    target="{{ $data['target'] ?? '_self' }}"
    class="inline-block rounded-lg font-semibold transition {{ $variant }} {{ $size }} {{ $data['classes'] ?? '' }}"
>
    {{ $data['label'] }}
</a>
