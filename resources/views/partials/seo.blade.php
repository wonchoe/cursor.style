<div class="seo-block collapsed" id="{{ $id ?? 'seoBlock' }}">
    <p class="seo-info">
        {{ $shortText }}
        <button class="seo-toggle" onclick="document.getElementById('{{ $id ?? 'seoBlock' }}').classList.remove('collapsed'); this.remove()">
            {{ __('messages.read_more') }}
        </button>
    </p>
    <div class="seo-text">
        <p>{{ $fullText }}</p>
    </div>
</div>