if (typeof currentPage === 'undefined') currentPage = false;

function renderPagination(currentPage, totalPages, maxVisible = 7) {
    const wrapper = document.querySelector('.pagination-wrapper');
    wrapper.style.display = 'block';

    const pagination = document.querySelector('.pagination');
    pagination.innerHTML = '';

    const createPage = (label, page = null, isCurrent = false) => {
        const el = document.createElement(page ? 'a' : 'span');
        el.className = 'page-numbers-js' + (isCurrent ? ' current' : '');
        if (label === '⬅️') el.dataset.arrow = 'left';
        if (label === '➡️') el.dataset.arrow = 'right';
        el.textContent = label;
        if (page) el.href = `?page=${page}`;
        return el;
    };

    // Prev
    pagination.appendChild(
        currentPage > 1
            ? createPage('⬅️', currentPage - 1)
            : createPage('⬅️')
    );

    const pages = [];

    const sideCount = Math.floor((maxVisible - 3) / 2); // -3: first, last, prev/next
    let start = Math.max(2, currentPage - sideCount);
    let end = Math.min(totalPages - 1, currentPage + sideCount);

    // Корекція початку і кінця
    if (currentPage <= sideCount + 2) {
        start = 2;
        end = Math.min(totalPages - 1, maxVisible - 2);
    }
    if (currentPage >= totalPages - sideCount - 1) {
        start = Math.max(2, totalPages - (maxVisible - 3));
        end = totalPages - 1;
    }

    // Перша
    pages.push(1);

    if (start > 2) pages.push('...');

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    if (end < totalPages - 1) pages.push('...');

    if (totalPages > 1) pages.push(totalPages);

    pages.forEach(p => {
        if (p === '...') {
            const span = document.createElement('span');
            span.className = 'page-numbers-js';
            span.textContent = '...';
            pagination.appendChild(span);
        } else {
            pagination.appendChild(createPage(p, p, p === currentPage));
        }
    });

    // Next
    pagination.appendChild(
        currentPage < totalPages
            ? createPage('➡️', currentPage + 1)
            : createPage('➡️')
    );
}


function getMaxVisiblePages() {
    const wrapperWidth = document.querySelector('.pagination-wrapper')?.offsetWidth || 768;
    const minButtonWidth = 95; // приблизна ширина кнопки
    const reserved = 2; // для Prev і Next
    return Math.floor(wrapperWidth / minButtonWidth) - reserved;
}

let maxVisible = getMaxVisiblePages();

if (currentPage) renderPagination(currentPage, totalPages, maxVisible);

document.addEventListener('DOMContentLoaded', function () {
    if (currentPage) renderPagination(currentPage, totalPages, maxVisible);
});

window.addEventListener('resize', () => {
    let maxVisible = getMaxVisiblePages();
    if (currentPage) renderPagination(currentPage, totalPages, maxVisible);
});


function setAdsBlockCorrect() {
    const grid = document.querySelector('.main__list');
    const items = Array.from(grid.children);

    items.forEach((item, index) => {
        if (item.classList.contains('gads-wrapper') && index > 1) {
            const prev = items[index - 1];
            const beforePrev = items[index - 2];
            
            if (!prev || !beforePrev) return;
            
            const topPrev = prev.getBoundingClientRect().top;
            const topBeforePrev = beforePrev.getBoundingClientRect().top;

            // Якщо останній перед рекламою вже на новому рядку — переносимо його за рекламу
            if (topPrev !== topBeforePrev) {
                if (prev && item.parentElement) {
                    item.parentElement.insertBefore(prev, item.nextSibling);
                }
            }
        }
    });
}
setAdsBlockCorrect();