<hr class="border-t {{ $data['color'] ?? 'border-gray-300' }} {{ $data['width'] ?? 'w-full' }} {{ $data['classes'] ?? 'my-8' }}"
    @if(($data['style'] ?? 'solid') !== 'solid') style="border-style: {{ $data['style'] }}" @endif
>
