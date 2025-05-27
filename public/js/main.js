
// .how__btn click logic
document.querySelectorAll('.how__btn').forEach((btn, index) => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.how__btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const tabs = document.querySelectorAll('.how__tab');
    tabs.forEach(tab => tab.classList.remove('active'));
    if (tabs[index]) {
      tabs[index].classList.add('active');
    }
  });
});

// .burger click logic
document.querySelector('.burger')?.addEventListener('click', () => {
  const nav = document.querySelector('.mobile__nav');
  if (nav) {
    nav.classList.add('active');
    nav.style.display = 'flex';
  }
});

// .close click logic
document.querySelector('.close')?.addEventListener('click', () => {
  const nav = document.querySelector('.mobile__nav');
  if (nav) {
    nav.classList.remove('active');
    nav.style.display = 'none';
  }
});

// banner hover logic
document.querySelector('.banner__tab_1 .banner__text')?.addEventListener('mouseenter', () => {
  const tab1 = document.querySelector('.banner__tab_1');
  const tab2 = document.querySelector('.banner__tab_2');
  if (tab1 && tab2) {
    tab1.style.display = 'none';
    tab2.style.display = 'flex';
  }
});

document.querySelector('.banner__tab_2 .banner__text')?.addEventListener('mouseleave', () => {
  const tab1 = document.querySelector('.banner__tab_1');
  const tab2 = document.querySelector('.banner__tab_2');
  if (tab1 && tab2) {
    tab2.style.display = 'none';
    tab1.style.display = 'flex';
  }
});



const COOKIE_NAME = 'first_visit_time';
const HOURS_LIMIT = 10;
const rewardBanner = document.getElementById('rewardBanner');

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
  return match ? match[2] : null;
}

function setCookie(name, value, days = 365) {
  const date = new Date();
  date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
  const expires = "expires=" + date.toUTCString();
  document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
}

if (rewardBanner) {
  if (getCookie('hide_item_2082') === 'true') {
    rewardBanner.style.display = 'none';
  } else {
    const now = Date.now();
    const storedTime = parseInt(getCookie(COOKIE_NAME));
    if (!storedTime) {
      setCookie(COOKIE_NAME, now);
    } else {
      const hoursPassed = (now - storedTime) / (1000 * 60 * 60);
      if (hoursPassed >= HOURS_LIMIT) {
        rewardBanner.style.display = 'block';
        if (document.querySelector('#seoBlock')) {
          document.querySelector('#seoBlock').style.display = 'none';
        }
      }
    }
  }
}

let redirectTimerStarted = false;

function startLoading() {
  const rewardBlock = document.getElementById('rewardBlock');
  const loader = document.getElementById('loader');

  if (rewardBlock) rewardBlock.style.display = 'none';
  if (loader) loader.style.display = 'flex';

  setCookie("hide_item_2082", "true", 3650);

  window.open('https://chromewebstore.google.com/detail/cursor-style-custom-curso/bmjmipppabdlpjccanalncobmbacckjn/reviews', '_blank');

  window.addEventListener('focus', function handleFocus() {
    if (!redirectTimerStarted) {
      redirectTimerStarted = true;
      window.removeEventListener('focus', handleFocus);

      setTimeout(function () {
        window.location.href = '/details/2082-cursor-style';
      }, 5000);
    }
  });
}


function showMyCollection() {
  if (!document.documentElement.dataset.cursorstyle) return;
  if (document.querySelector('#mycollection_menu')) {
    document.querySelector('#mycollection_menu').style.display = 'block';
  }
}

showMyCollection();


setTimeout(() => {
  if (document.querySelector('#preloader')) {
    document.querySelector('#preloader').style.display = 'none';
  }
}, 2000);