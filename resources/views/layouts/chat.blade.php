<div class="chat-container" id="chatContainer">
    <div class="chat-header">
        <div id="welcomeUser">Welcome to the Chat</div>
        <div class="selector-container glass-container">
            <div class="pickaroom">Choose a Room 💠</div>
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
        <button class="start-chat-btn" id="startChatBtn">Click to start chatting</button>
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
                <b id="replyToUsername">User</b>: <span id="replyToText">Message...</span>
            </div>
            <button id="cancelReplyBtn" title="Cancel reply">×</button>
        </div>
        <!-- ----------- -->


        <!-- inputError Preview -->
        <div id="chatInputErrorHandler"></div>
        <!-- ----------- -->


        <div class="emojiInputWrapper">
            <input type="text" id="messageInput" maxlength="1000" placeholder="Type your message...">
        </div>

        <button id="chatSendBtn">Send</button>
        <div class="avatar-menu" id="avatarMenu">
            <ul>
                <li id="editProfile">Edit Profile</li>
            </ul>
        </div>
    </div>
</div>





<div id="registerPopup" class="popup">
    <h2 class="avatarh2">Join chat</h2>
    <input type="text" id="loginInput" class="md-input" placeholder="Pick your cool name!" maxlength="24">
    <div id="userNameFeedback" style="font-size: 12px; margin-top: 5px;"></div>
    <div class="avatar-preview">
        <img id="selectedAvatar" src="/images/avatars/avatar_17.svg" width="48">
        <button class="md-button md-button-secondary" onclick="showAvatarSelector()">Choose avatar</button>
    </div>
    <div class="button-group">
        <button class="md-button md-button-secondary" id="cancelRegistrationBtn">Cancel</button>
        <button class="md-button md-button-primary" id="submitRegisterBtn">Register</button>
    </div>
</div>

<div id="avatarSelector" class="popup avatar-popup">
    <h2 class="avatarh2">Find your awesome character!</h2>
    <div class="avatar-grid" id="avatarGrid"></div>
    <div class="button-group">
        <button class="md-button md-button-secondary" onclick="closeAvatarSelector()">Close</button>
    </div>
</div>