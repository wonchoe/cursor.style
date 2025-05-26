let selectedAvatar = 'avatar_0';

function generateAvatarList() {
  const avatarGrid = document.getElementById('avatarGrid');
  if (!avatarGrid) return;
  for (let i = 0; i <= 112; i++) {
    const avatarDiv = document.createElement('div');
    avatarDiv.className = 'avatar-item';
    const img = document.createElement('img');
    img.src =  window.assetCdnBase + `/images/avatars/avatar_${i}.svg`;
    img.loading = "lazy";    
    img.onclick = () => selectAvatar(`avatar_${i}`);        
    avatarDiv.appendChild(img);
    avatarGrid.appendChild(avatarDiv);
  }
}

function showAvatarSelector() {
  document.getElementById('avatarSelector').style.display = 'block';
}

function closeAvatarSelector() {
  document.getElementById('avatarSelector').style.display = 'none';
}

function closePopup() {
  if (document.getElementById('registerPopup')) document.getElementById('registerPopup').classList.add('hidden');
}

function selectAvatar(avatarName) {
  selectedAvatar = avatarName;
  document.getElementById('selectedAvatar').src =  window.assetCdnBase + `/images/avatars/${avatarName}.svg`;
  document.getElementById('selectedAvatar').dataset.avatarName = avatarName;
  closeAvatarSelector();
}

function submitRegistration() {
  const login = document.getElementById('loginInput').value;
  if (login.trim() === '') {
    alert('Type your nickname');
    return;
  }

  const userData = {
    login: login,
    avatar: selectedAvatar
  };

  closePopup();
}


function setAvatarMenu() {
  const avatarTrigger = document.getElementById('chatMessageAva');
  const avatarMenu = document.getElementById('avatarMenu');
  const changeUsernameOption = document.getElementById('changeUsername');
  const changeAvatarOption = document.getElementById('changeAvatar');

  if (!avatarTrigger) return;
  avatarTrigger.addEventListener('click', (e) => {
    avatarMenu.style.display = avatarMenu.style.display === 'block' ? 'none' : 'block';
    if (document.querySelector('#userNameFeedback')) document.querySelector('#userNameFeedback').innerHTML = '';
    e.stopPropagation();
  });


  document.addEventListener('click', (e) => {
    if (!avatarMenu.contains(e.target) && e.target !== avatarTrigger) {
      avatarMenu.style.display = 'none';
    }
  });
}

function drawUsernamePopup(action) {
  if (action == 'register') {
    document.querySelector('#registerPopup h2').innerHTML = 'Join chat';
    document.querySelector('#registerPopup #submitRegisterBtn').innerHTML = 'Register';
    document.querySelector('#registerPopup #submitRegisterBtn').dataset.action = 'register';

  } else {
    document.querySelector('#registerPopup h2').innerHTML = 'Edit profile';
    document.querySelector('#registerPopup #submitRegisterBtn').innerHTML = 'Change';
    document.querySelector('#registerPopup #submitRegisterBtn').dataset.action = 'change';
  }

  document.querySelector('#registerPopup').style.display = "block";
  document.querySelector('#ovelay').style.display = 'block';
}

const startChatBtn = document.querySelector('#startChatBtn');
if (startChatBtn) {
  startChatBtn.addEventListener('click', () => {
    drawUsernamePopup('register');
  });
}

const cancelRegistrationBtn = document.querySelector('#cancelRegistrationBtn');
if (cancelRegistrationBtn) {
  cancelRegistrationBtn.addEventListener('click', () => {
    hideOnClick();
  });
}

function attachEmoji(){

}

generateAvatarList();
setAvatarMenu();
attachEmoji();