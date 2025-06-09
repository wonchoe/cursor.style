<div class="main__list" id="main_list">
    @forelse($cursors as $key => $cursor)
        @if ($key > 0 && $key % 18 === 0)
            <div class="gads-wrapper">
                @include('ads.google.infeed')
            </div>
        @endif

        @php
            $translation = $cursor->currentTranslation->name ?? $cursor->name_en;
            $slug = Str::slug($translation) ?: Str::slug($cursor->name_en);
        @endphp

        <div class="main__item" data-container-id="{{ $cursor->id }}"
            onclick="handleItemClick(event, '{{ $cursor->details_url }}')">
            <div class="div_ar_p">
                <p>{{ $translation }}</p>
            </div>
            <div class="main__item-img cs_pointer" data-cur-id="{{ $cursor->id }}" cursorshover="true">
                <img alt="{{ $translation }}" title="{{ $translation }}" loading="{{ $key < 3 ? 'eager' : 'lazy' }}"
                    class="cursorimg" style="cursor: url({{ asset_cdn($cursor->c_file) }}) 0 0, auto !important;"
                    src="{{ asset_cdn($cursor->c_file) }}">
                <img alt="{{ $translation }}" title="{{ $translation }}" loading="{{ $key < 3 ? 'eager' : 'lazy' }}"
                    class="cursorimg" style="cursor: url({{ asset_cdn($cursor->p_file) }}) 0 0, auto !important;"
                    src="{{ asset_cdn($cursor->p_file) }}">
            </div>

            <span class="downloads-badge">
                <img src="{{ asset_cdn('images/icons/download.png') }}" alt="Downloads" title="Downloads"
                    class="download-count-img">
                {{ number_format($cursor->totalClick + $cursor->todayClick) }}
            </span>

            @include('partials.cursor-buttons', ['cursor' => $cursor])
        </div>
    @empty
        <div class="no_result">@lang('messages.no_result')</div>
    @endforelse
</div>