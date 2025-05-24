<div id="votepls" style="display:none">
    <div class="star_container">
        <div class="star_main_title">@lang('messages.vote_1')</div>
        <div class="star_title">@lang('messages.vote_2')</div>
        <a href="#" id="star_img_container">
            <img style="width: 256px; margin: 20px;" src="{{ asset_cdn('images/star.gif') }}">
        </a>
        <button class="cursor-button" data-label="@lang('messages.vote_3')" id="star_vote"></button>
        <div class="maybelater" id="maybelater">@lang('messages.vote_4')</div>
    </div>
    <div class="dontshow">
        <input type="checkbox" id="dontshow"
            style="vertical-align:middle; display: inline-block;-webkit-appearance: checkbox;" />
        <label for="dontshow" id="css_cs_cursor">@lang('messages.vote_5')</label>
    </div>
    <div class="star_close" id="star_close_btn"></div>
</div>


<div class="header">
    <a class="logo" href="/">
        <img src="{{ asset_cdn('images/logo.webp') }}" alt="@lang('messages.cursor_style')"
            title="@lang('messages.cursor_style_logo_title')" />
    </a>
    <div class="container">
        <div class="nav">
            <ul>
                <li><a class="top_menu_link" href="/">@lang('messages.menu_main')</a></li>
                <li><a class="top_menu_link" href="/collections">@lang('messages.allcollections')</a></li>
                <li><a class="top_menu_link" data-class="rate" target="_blank"
                        href="https://chromewebstore.google.com/detail/bmjmipppabdlpjccanalncobmbacckjn/reviews">@lang('messages.rateus')</a>
                </li>
            </ul>
            <ul>
                <li>
                    <div class="nav-icon-search">
                        <input type="text" class="nav-icon-search-input"
                            placeholder="@lang('messages.main_page_search')" value="{{ $query ?? '' }}"
                            onkeypress="handleSearchEnter(event)">
                        <span class="icon-search-top"></span>
                    </div>
                </li>
                <li><a class="top_menu_link" id="mycollection_menu"
                        href="/mycollection">@lang('messages.mycollection')</a></li>
            </ul>
        </div>
        <div class="burger">
            <div class="burger__line"></div>
            <div class="burger__line"></div>
            <div class="burger__line"></div>
        </div>
    </div>
</div>

<div class="mobile__nav">
    <div class="mobile__nav__panel">
        <div class="close"></div>
        <a class="nav__link active" href="/">@lang('messages.menu_main')</a>
        <a class="nav__link" href="/collections">@lang('messages.allcollections')</a>
        <a class="nav__link" href="/contact">@lang('messages.menu_contact')</a>
        <a class="nav__link" href="/rateus">@lang('messages.menu_contact')</a>
    </div>
</div>