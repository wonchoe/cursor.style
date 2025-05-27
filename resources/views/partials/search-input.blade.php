<div class="search-wrapper">
    <div class="search-input">
        <span class="search-icon"></span>
        <form onsubmit="return false;">
            <input type="text" id="cs_search" class="search" placeholder="@lang('messages.main_page_search')" autocomplete="off">
            <input type="hidden" name="token" id="token" value="{{ csrf_token() }}" />
        </form>
    </div>
    <hr class="search-hr hidden" />
    <div id="search-results" class="search-dropdown hidden"></div>
</div>