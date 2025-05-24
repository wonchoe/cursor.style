@if($all_cursors->isNotEmpty())
<div class="near-wrapper">
    <div class="main__list_near">
        @foreach ($all_cursors as $loopCursor)
            <div 
                onclick='location.href="{{ $loopCursor->detailsSlug }}"'
                class="main__item{{ $loopCursor->id === $current_id ? ' active' : '' }}"
                style="opacity: {{ $loopCursor->id === $current_id ? '1' : '0.2' }};"
                @if($loopCursor->id === $current_id) aria-current="true" @endif>
                <div class="main__item-img cs_pointer" data-cur-id="{{ $loopCursor->id }}" cursorshover="true">
                    <img class="cursorimg"
                         style="cursor: url({{ asset_cdn($loopCursor->c_file) }}) 0 0, auto !important;"
                         src="{{ asset_cdn($loopCursor->c_file) }}">
                    <img class="cursorimg"
                         style="cursor: url({{ asset_cdn($loopCursor->p_file) }}) 0 0, auto !important;"
                         src="{{ asset_cdn($loopCursor->p_file) }}">
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif