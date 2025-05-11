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

function redirectToSearch(event) {
  event.preventDefault();
  const input = document.getElementById('cs_search');
  const query = encodeURIComponent(input.value.trim());
  if (query.length > 0) {
    window.location.href = '/search/' + query;
  }
}

function handleSearchEnter(e) {
  if (e.key === 'Enter') {
    const query = e.target.value.trim();
    if (query) {
      window.location.href = '/search/' + encodeURIComponent(query);
    }
  }
}

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

function attachEtagToCountClicks() {
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


function showMyCollection() {
  if (!document.documentElement.dataset.cursorstyle) return;
  if (document.querySelector('#mycollection_menu')) {
    document.querySelector('#mycollection_menu').style.display = 'block';
  }
}

document.addEventListener('DOMContentLoaded', function () {
  setTimeout(() => {
    attachEtagToCountClicks();
    showMyCollection();
  }, 500);
  hoverMenu();
  injectButtonHandler();
  injectCloseModalInstaller();
  setupMiliSearch();

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

function cleanStr(string) {
  return string
    .replace(/\s+/g, '-')              
    .replace(/[^A-Za-z0-9\-]/g, '') 
    .toLowerCase(); 
}

function setupMiliSearch() {

  let timeout = null;
  const input = document.getElementById('cs_search');
  const resultsBox = document.getElementById('search-results');

  input.addEventListener.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault(); // optional, stops form submit if inside a form
        const query = this.value.trim();
        if (query) {
            window.location.href = '/search/' + encodeURIComponent(query);
        }
    }
});

  input.addEventListener('input', function () {
    clearTimeout(timeout);
    const query = this.value.trim();

    if (query.length < 2) {
      resultsBox.classList.add('hidden');
      document.querySelector('.search-hr').classList.add('hidden');
      resultsBox.innerHTML = '';
      return;
    }

    timeout = setTimeout(() => {
      fetch(`/search`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer masterKey123',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ lang: 'en', query: query, limit: 15 })
      })
        .then(res => res.json())
        .then(data => {
          if (!data.hits || data.hits.length === 0) {
            resultsBox.innerHTML = '<div style="padding:10px">No results</div>';
          } else {
            resultsBox.innerHTML = renderGroupedResults(data.hits);
          }
          resultsBox.classList.remove('hidden');
          document.querySelector('.search-hr').classList.remove('hidden');
        });
    }, 300);
  });

  input.addEventListener('blur', () => setTimeout(() => resultsBox.classList.add('hidden'), 1200));
  input.addEventListener('focus', () => {
    if (resultsBox.innerHTML.trim()) {
      resultsBox.classList.remove('hidden');
      document.querySelector('.search-hr').classList.remove('hidden');
    }
  });

  function renderGroupedResults(hits) {
    const grouped = {};

    hits.forEach(hit => {
      const key = hit.cat || 'uncategorized';
      if (!grouped[key]) {
        grouped[key] = {
          name: hit.cat_name || 'Uncategorized',
          alt: hit.cat || 'Uncategorized',
          img: hit.cat_img || null,
          items: []
        };
      }
      grouped[key].items.push(hit);
    });

    let html = '';
    Object.values(grouped).forEach(group => {
      html += `
        <div class="category-block" style="margin-bottom:16px;">
          <div class="category-header" onclick="window.location='https://uk.cursor.style/collections/${group.alt}'" style="display:flex;align-items:center;margin-bottom:5px;">
            ${group.img ? `<img src="/collection/${group.alt}.png" height="32">` : ''}
            <strong>${group.name}</strong>
          </div>
          <ul style="list-style:none;padding-left:10px;margin:0;">
            ${group.items.map(cursor => `
              <li onclick="window.location='/details/${cursor.id}-${cursor.name}'" style="cursor:pointer;display:flex;align-items:center;">
                <img src="/collections/${group.alt}/${cursor.id}-${cursor.name}-cursor" width="32" height="32" style="margin-right:10px;">
                ${cursor.name}
              </li>
            `).join('')}
          </ul>
        </div>`;
    });

    return html;
  }
}
