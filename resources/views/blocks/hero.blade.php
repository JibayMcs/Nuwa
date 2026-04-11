<section
    class="{{ $data['bg_color'] }} {{ $data['text_color'] }} py-24 px-6 bg-cover bg-center {{ $data['classes'] ?? '' }}"
    @if($data['bg_image'] ?? null) style="background-image: url('{{ $data['bg_image'] }}')" @endif
>
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-5xl font-bold mb-6">{{ $data['title'] }}</h1>
        <p class="nuwa-hero-subtitle text-xl mb-8 opacity-80">{{ $data['subtitle'] }}</p>
        @if(!empty($data['cta_label']))
            <a
                href="{{ $data['cta_url'] ?? '#' }}"
                class="nuwa-hero-cta inline-block px-8 py-3 bg-white text-gray-900 rounded-lg font-semibold hover:bg-gray-100 transition"
            >
                {{ $data['cta_label'] }}
            </a>
        @endif
    </div>
</section>
