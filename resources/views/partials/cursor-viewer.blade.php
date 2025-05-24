<div class="main__item-wrapper">
    <div class="main-cursor">
        <div class="left-cursor">
            @if ($id_prev)
                <button
                    onclick='location.href="{{ route("cursor.details", ["id" => $id_prev[0], "name" => $id_prev[1]]) }}"'
                    class="nav-arrow left">‹</button>
            @endif
        </div>

        <div class="middle-cursor">
            <div class="main__item active"> {{-- Додаємо клас active для центрального --}}

                <div class="main__item-img cs_pointer" data-cur-id="{{ $cursor->id }}" cursorshover="true">
                    <img loading="lazy" class="cursorimg" style="cursor: url({{ asset_cdn($cursor->c_file) }}) 0 0, auto !important;"
                        src="{{ asset_cdn($cursor->c_file) }}">
                    <img loading="lazy" class="cursorimg" style="cursor: url({{ asset_cdn($cursor->p_file) }}) 0 0, auto !important;"
                        src="{{ asset_cdn($cursor->p_file) }}">
                </div>

                <span class="downloads-badge" style="opacity: 1;">
                    <img src="/images/icons/download.png" style="width: 10px;">
                    {{ number_format($cursor->totalClick + $cursor->todayClick) }}
                </span>

                @include('partials.cursor-buttons', ['cursor' => $cursor, 'opacity' => '1'])

            </div>

            <p class="detail-page dropcap">

                {{ $cursor->seo_page ?? $cursor->currentTranslation->name ?? $cursor->name_n ?? $cursor->name_en }}
            </p>

        </div>

        <div class="right-cursor">
            @if ($id_next)
                <button
                    onclick='location.href="{{ route("cursor.details", ["id" => $id_next[0], "name" => $id_next[1]]) }}"'
                    class="nav-arrow right">›</button>
            @endif
        </div>
    </div>

    <div class="main-tags">
        @if(!empty($cursor->tags))
            <div class="cursor-tags mt-4 flex flex-wrap gap-2">
                @foreach($cursor->tags as $tag)
                    <span class="tag-chip"><a href="/search/{{ $tag }}">#{{ $tag }}</a></span>
                @endforeach
            </div>
        @endif
    </div>

</div>