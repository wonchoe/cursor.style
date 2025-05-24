@php
$translation = $cursor->currentTranslation->name ?? $cursor->name_en;
$catName = $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en;
@endphp

<div class="main__btns" {!! isset($opacity) ? 'style="opacity: ' . $opacity . ';"' : '' !!}>
    <div class="btn-container">
            <span class="pointerevent">
                <button class="img-btn" data-action="apply" data-type="stat"
                    data-label="@lang('messages.apply')"
                    data-disabled="@lang('messages.add_to_collection_added')"
                    data-cataltname="{{ $cursor->collection->alt_name }}"
                    data-catbasename="{{ $catName }}"
                    data-cat="{{ $cursor->cat }}" data-id="{{ $cursor->id }}"
                    data-name="{{ $translation }}"
                    data-nameEn="{{ $translation }}"
                    data-offset-x="{{ $cursor->offsetX }}"
                    data-offset-x_p="{{ $cursor->offsetX_p }}"
                    data-offset-y="{{ $cursor->offsetY }}"
                    data-offset-y_p="{{ $cursor->offsetY_p }}"
                    data-c_file="{{ $cursor->c_file }}"
                    data-p_file="{{ $cursor->p_file }}">
                    <img title="@lang('messages.add_to_collection')" src="{{ asset_cdn('images/apply.svg') }}">
                </button>
            </span>
            <span class="pointerevent">
                <button class="img-btn" data-action="add" data-type="stat"
                    data-label="@lang('messages.add_to_collection')"
                    data-disabled="@lang('messages.add_to_collection_added')"
                    data-cataltname="{{ $cursor->collection->alt_name }}"
                    data-catbasename="{{ $catName }}"
                    data-cat="{{ $cursor->cat }}" data-id="{{ $cursor->id }}"
                    data-name="{{ $translation }}"
                    data-nameEn="{{ $translation }}"
                    data-offset-x="{{ $cursor->offsetX }}"
                    data-offset-x_p="{{ $cursor->offsetX_p }}"
                    data-offset-y="{{ $cursor->offsetY }}"
                    data-offset-y_p="{{ $cursor->offsetY_p }}"
                    data-c_file="{{ $cursor->c_file }}"
                    data-p_file="{{ $cursor->p_file }}">
                    <img title="@lang('messages.add_to_collection')" src="{{ asset_cdn('images/plus.svg') }}">
                </button>
            </span>            
    </div>
</div>
