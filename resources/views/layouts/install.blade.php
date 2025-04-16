<div class="container" id="install_container">
    <div class="install-panel">
        <div class="install-panel__logo">
            <img src="{{ asset('images/chrome.png')}}" alt="@lang('messages.install_alt')" title="@lang('messages.install_title')">
        </div>
        <div class="install-panel__left">
            <div class="install-panel__title">@lang('messages.install_text_1')</div>
            <div class="install-panel__description">@lang('messages.install_text_2')</div>
        </div>
        <div class="install-panel__right"> 
            <button class="cursor-button" data-label="@lang('messages.add_to_browser')" onclick="window.open(ext_link,'_blank')"></button>
        </div>
    </div>
</div>