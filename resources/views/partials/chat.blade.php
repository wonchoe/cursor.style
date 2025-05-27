@php
    $animations = [
        asset_cdn('/lottie/among_us.lottie'),
        asset_cdn('/lottie/elephant.lottie'),
        asset_cdn('/lottie/fish.lottie'),
    ];
    $randomAnimation = $animations[array_rand($animations)];
@endphp

<div class="chat-container" id="chatContainer">

    @php
        // Список webm-файлів (оновлюй якщо додаєш нові)
        $loaders = ['among_us.webm', 'Bus.webm'];
        $loader = $loaders[array_rand($loaders)];
    @endphp

    <div id="preloader" class="preloader">
        <video preload="auto" src="{{ asset_cdn('loaders/'.$loader) }}" autoplay loop muted style="width:200px;height:200px;display:block"></video>
    </div>

    <div class="chat-header">
        <div id="welcomeUser" data-lang-tag="welcome_to_the_chat">@lang('messages.chat_welcome')</div>
        <div class="selector-container glass-container">
            <div class="pickaroom" data-lang-tag="choose_a_room">@lang('messages.chat_choose_room') 💠</div>
            <div class="selector-glass" id="roomSelectorContainer">
                <select id="roomSelector">
                </select>
            </div>
        </div>
    </div>

    <div class="messages-wrapper">
        <div id="floatingDate"></div>
        <div id="chatDimmer" class="cchat-dimmer hidden"></div>
        <div id="chatHistoryLoader" class="chat-history-loader hidden">Loading...</div>
        <div id="newMessagesBadge" class="newNotReadMessages">New messages (1)</div>        
        <ol class="cchat-messages" id="chatMessages"></ol>
    </div>



    <div class="chat-preinput" id="preChat">
        <button class="start-chat-btn" id="startChatBtn" data-lang-tag="click_to_start_chatting">@lang('messages.chat_click_to_start_chatting')</button>
    </div>
    <div class="chat-input-area" id="chatInputArea">

        <div id="replyPreview" class="hidden reply-preview">
            <div class="replyBodyMessage">
                <div class="reply-header">
                    <span id="replyToUsername"></span>
                </div>
                <div id="replyToText" class="reply-text"></div>
            </div>
            <div class="replyToCloseButton">
                <button id="cancelReplyBtn" title="Cancel reply">✕</button>
            </div>
        </div>


        <div id="chatMessageAva" class="sendMessageAva"></div>

        <!-- replyToPreview -->
        <div id="replyPreview" class="hidden cchat-reply-preview">
            <div class="reply-text">
                <b id="replyToUsername" data-lang-tag="chat_user">@lang('messages.chat_user')</b>: <span id="replyToText" data-lang-tag="chat_reply_to">@lang('messages.chat_message')...</span>
            </div>
            <button id="cancelReplyBtn" title="Cancel reply">×</button>
        </div>
        <!-- ----------- -->


        <!-- inputError Preview -->
        <div id="chatInputErrorHandler"></div>
        <!-- ----------- -->


        <div class="emojiInputWrapper">
            <input type="text" id="messageInput" maxlength="1000" data-lang-placeholder="type_your_message" placeholder="@lang('messages.chat_type_your_message')...">
        </div>

        <button id="chatSendBtn" data-lang-tag="chat_send">@lang('messages.chat_send')</button>
        <div class="avatar-menu" id="avatarMenu">
            <ul>
                <li id="editProfile" data-lang-tag="edit_profile">@lang('messages.chat_edit_profile')</li>
            </ul>
        </div>
    </div>
</div>





<div id="registerPopup" class="popup">
    <h2 class="avatarh2" data-lang-tag="join_chat">@lang('messages.chat_join_chat')</h2>
    <input type="text" id="loginInput" class="md-input" data-lang-placeholder="pick_your_cool_name" placeholder="@lang('messages.chat_pick_your_cool_name')" maxlength="24">
    <div id="userNameFeedback" style="font-size: 12px; margin-top: 5px;"></div>
    <div class="avatar-preview">
        <img id="selectedAvatar" src="{{ asset_cdn('images/avatars/avatar_17.svg') }}" width="48">
        <button class="md-button md-button-secondary" onclick="showAvatarSelector()" data-lang-tag="choose_avatar">@lang('messages.chat_choose_avatar')</button>
    </div>
    <div class="button-group">
        <button class="md-button md-button-secondary" id="cancelRegistrationBtn" data-lang-tag="chat_cancel">@lang('messages.chat_cancel')</button>
        <button class="md-button md-button-primary" id="submitRegisterBtn" data-lang-tag="chat_register">@lang('messages.chat_register')</button>
    </div>
</div>

<div id="avatarSelector" class="popup avatar-popup">
    <h2 class="avatarh2" data-lang-tag="find_your_character">@lang('messages.chat_find_your_character')</h2>
    <div class="avatar-grid" id="avatarGrid"></div>
    <div class="button-group">
        <button class="md-button md-button-secondary" onclick="closeAvatarSelector()" data-lang-tag="close">@lang('messages.chat_close')</button>
    </div>
</div>