@if(!empty($data['css']))
<style>{!! $data['css'] !!}</style>
@endif

<div class="nuwa-html-content" @if(!empty($data['js'])) data-nuwa-js="{{ e($data['js']) }}" @endif>
    {!! $data['html'] ?? '' !!}
</div>

@if(!empty($data['js']))
<script>
(function() {
    const el = document.currentScript.previousElementSibling;
    try {
        (new Function('el', {!! json_encode($data['js']) !!}))(el);
    } catch(e) {
        console.warn('[Nuwa] Block JS error:', e);
    }
})();
</script>
@endif
