let shouldRun = false;
const emojis = ['🔥', '✨', '⭐', '🥳', '💥', '🙂','🧡','👻','🦄','🔥','💛'];
const poolSize = 150;
const pool = [];
let poolIndex = 0;
let lastSpawn = 0;

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




// function setUpEffect(){
//   if (document.querySelector('[data-cursor-style]')) shouldRun = false;
//   for (let i = 0; i < poolSize; i++) {
//     const el = document.createElement('div');
//     el.className = 'emoji-temp';
//     el.style.display = 'none';
//     el.addEventListener('animationend', () => {
//       el.style.display = 'none';
//       el.classList.remove('emoji-anim');
//     });
//     document.body.appendChild(el);
//     pool.push(el);
//   }

//   document.addEventListener('mousemove', (e) => {
//     if (!shouldRun) return;

//     const now = performance.now();
//     if (now - lastSpawn < 30) return;
//     lastSpawn = now;

//     spawnEmoji(e.clientX, e.clientY);
//   });

//   function spawnEmoji(x, y) {
//     const emoji = pool[poolIndex];
//     poolIndex = (poolIndex + 1) % poolSize;

//     const offsetX = (Math.random() - 0.5) * 20;
//     const offsetY = (Math.random() - 0.5) * 20;
//     const angle = `${Math.floor(Math.random() * 360)}deg`;

//     emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
//     emoji.style.left = `${x + offsetX}px`;
//     emoji.style.top = `${y + offsetY}px`;
//     emoji.style.opacity = '1';
//     emoji.style.fontSize = '15px';
//     emoji.style.setProperty('--angle', angle);
//     emoji.style.display = 'block';

//     void emoji.offsetWidth; // restart animation
//     emoji.classList.add('emoji-anim');
//   }
// }

// setUpEffect();


// setTimeout(()=>{
//   if (!document.querySelector('[data-cursor-style]')) shouldRun = true;
// },2000);

// const observer = new MutationObserver((mutations) => {
//   for (const mutation of mutations) {

//     for (const node of mutation.addedNodes) {
//       if (
//         node.nodeType === 1 &&
//         node instanceof HTMLElement
//       ) {
//         if (node.id === 'cursor-style-objects') {
//           shouldRun = false;
//           if (document.querySelector('[data-cursor-style]')) shouldRun = false;
//           return;
//         }

//         if (node.querySelector && node.querySelector('#cursor-style-objects')) {
//           shouldRun = false;
//           if (document.querySelector('[data-cursor-style]')) shouldRun = false;
//           return;
//         }
//       }
//     }

//     for (const node of mutation.removedNodes) {
//       if (
//         node.nodeType === 1 &&
//         node instanceof HTMLElement
//       ) {
//         if (node.id === 'cursor-style-objects') {
//           shouldRun = true;
//           if (document.querySelector('[data-cursor-style]')) shouldRun = false;
//           return;
//         }

//         if (node.querySelector && node.querySelector('#cursor-style-objects')) {
//           shouldRun = true;
//           if (document.querySelector('[data-cursor-style]')) shouldRun = false;
//           return;
//         }
//       }
//     }
//   }
// });

// observer.observe(document.body, { childList: true, subtree: true });


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

let redirectTimerStarted = false;

function startLoading() {
  document.getElementById('rewardBlock').style.display = 'none';
  document.getElementById('loader').style.display = 'flex';

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
