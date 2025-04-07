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
