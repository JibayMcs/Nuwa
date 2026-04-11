<{{ $data['tag'] ?? 'section' }} class="{{ $data['classes'] ?? '' }}">
    @if($data['container'] ?? true)
        <div class="{{ $data['container_class'] ?? 'max-w-7xl mx-auto' }}">
            {!! $slot ?? '' !!}
        </div>
    @else
        {!! $slot ?? '' !!}
    @endif
</{{ $data['tag'] ?? 'section' }}>
