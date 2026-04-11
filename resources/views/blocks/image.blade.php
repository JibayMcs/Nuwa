<figure class="{{ $data['classes'] ?? '' }}">
    <img
        src="{{ $data['src'] }}"
        alt="{{ $data['alt'] ?? '' }}"
        @if($data['width'] ?? null) width="{{ $data['width'] }}" @endif
        @if($data['height'] ?? null) height="{{ $data['height'] }}" @endif
        @if($data['lazy'] ?? true) loading="lazy" @endif
        class="w-full h-auto"
    >
    @if(!empty($data['caption']))
        <figcaption class="mt-2 text-sm text-gray-500 text-center">{{ $data['caption'] }}</figcaption>
    @endif
</figure>
