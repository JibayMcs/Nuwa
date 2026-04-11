@php
    $cols = $data['columns'] ?? 2;
    $gap = $data['gap'] ?? 'gap-8';
    $responsive = $data['responsive'] ?? true;

    $gridClass = match($data['layout'] ?? 'equal') {
        '1-2' => 'grid-cols-1 md:grid-cols-3',
        '2-1' => 'grid-cols-1 md:grid-cols-3',
        default => $responsive
            ? "grid-cols-1 md:grid-cols-{$cols}"
            : "grid-cols-{$cols}",
    };
@endphp
<div class="grid {{ $gridClass }} {{ $gap }} {{ $data['classes'] ?? '' }}">
    {!! $slot ?? '' !!}
</div>
