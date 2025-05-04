document.documentElement.dataset.block_cursor = false;
var block_cursor = document.documentElement.dataset.block_cursor;
var storage_data = {};

var anibase_ext = {}
var statbase_ext = {}

var click_x = 0;
var click_y = 0;
var data_base = '';
var ext_link = 'https://chrome.google.com/webstore/detail/bmjmipppabdlpjccanalncobmbacckjn';
var top_base = '';
var base = '';
var ani_base = '';
var base_selected = 'top';
var sort_type = 'top';
var preview_index = 0;
var cur_base = 'top';
var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
var ovf = (isChrome) ? 'overlay' : 'scroll';
var isInstalled = !!document.documentElement.dataset.cursorStyle;

var editorExtensionId = document.documentElement.dataset.chromeId;



function injectButtonHandler() {
  const containers = document.querySelectorAll(".main__list, .main__list_cursor");

  containers.forEach(container => {
    container.addEventListener("click", function (e) {
      const target = e.target.closest("button[data-type='stat']");

      if (target) {
        if (!document.documentElement.dataset.cursorstyle) {
          const ex1 = document.getElementById("ex1");
          if (ex1) {
            ex1.style.display = "flex";
            const overlay = document.querySelector('.overlay');
            if (overlay) overlay.style.display = 'block';
          }
        }
      }
    });
  });
}


document.addEventListener('keyup', function (e) {
  if (e.key === "Escape") {
    hideOnClick();

    // if (prev_container.style.display === 'block') {
    //     prev_container.style.display = 'none';
    // }
  }

  // if (e.key === 'ArrowLeft' || e.keyCode === 65) {
  //     if (prev_container.style.display === 'block') {
  //         plusSlides(-1);
  //     }
  // }

  // if (e.key === 'ArrowRight' || e.keyCode === 68) {
  //     if (prev_container.style.display === 'block') {
  //         plusSlides(1);
  //     }
  // }
});


function hideOnClick() {
  ex1.style.display = "none";
  document.querySelector('.overlay').style.display = 'none';
  if (document.querySelector('#registerPopup')) document.querySelector('#registerPopup').style.display = 'none';
  if (document.getElementById('avatarSelector')) document.getElementById('avatarSelector').style.display = 'none';
}

function injectCloseModalInstaller() {
  if (location.href.includes('mycollection')) return;

  const closeBtn = document.querySelector('[rel="modal:close"]');
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      const ex1 = document.getElementById("ex1");
      if (ex1) {
        hideOnClick();
      }
    });
  }

  const installBtn = document.querySelector('.modal-btn-install');
  if (installBtn) {
    installBtn.addEventListener('click', () => {
      hideOnClick();
    });
  }

  const overlay = document.querySelector('#ovelay');
  if (overlay) {
    overlay.addEventListener('click', () => {
      hideOnClick();
    });
  }
}

function hoverMenu() {
  const tabs = document.querySelectorAll('.tabs a');
  const activeTab = document.querySelector('.tabs .cur_menu.active');

  tabs.forEach(tab => {
    tab.addEventListener('mouseenter', () => {
      if (tab !== activeTab) {
        activeTab.classList.add('hidden-by-hover');
      }
    });
    tab.addEventListener('mouseleave', () => {
      activeTab.classList.remove('hidden-by-hover');
    });
  }); 
}

function attachEtagToCountClicks(){
  if (!document.documentElement.dataset.cursorstyle) return;
  const btnContainers = document.querySelectorAll('.btn-container');

  btnContainers.forEach(function (container) {
      const statButtons = container.querySelectorAll('button[data-type="stat"]');

      statButtons.forEach(function (btn) {
          btn.addEventListener('click', function (e) {
              const isAnyDisabled = Array.from(statButtons).some(b => b.disabled);

              if (isAnyDisabled) return;
              const mainBtn = statButtons[0];
              const cursorId = mainBtn.dataset.id;
              const category = mainBtn.dataset.catbasename;
              gtag('event', 'cursor_click', {
                  cursor_id: cursorId,
                  cursor_category: category
              });

              console.log('Tracked click for cursor:', cursorId);
          });
      });
  });  
}

document.addEventListener('DOMContentLoaded', function () {
  setTimeout(() => {
    attachEtagToCountClicks();  
  }, 1000);
  
  hoverMenu();
  injectButtonHandler();
  injectCloseModalInstaller();

  if (location.pathname.search('howto') > 0)
    howtoScaleImg();
  links = document.getElementsByClassName('top_menu_link');
  [].forEach.call(links, function (cl) {
    cl.className = 'top_menu_link';
    if (location.href == cl.href)
      cl.classList.add('active');
  });
});

function handleItemClick(event, url) {
  if (event.target.closest('.cursor-button')) return;
  if (event.target.closest('.pointerevent')) return;
  window.location.href = url;
}
