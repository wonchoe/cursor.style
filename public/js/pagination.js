

function renderPagination(currentPage, totalPages, maxVisible = 7) {
    const wrapperWidth = document.querySelector('.pagination-wrapper');
    wrapperWidth.style.display = 'block';                
    const pagination = document.querySelector('.pagination');
    pagination.innerHTML = '';

    const createPage = (label, page = null, isCurrent = false) => {
        const el = document.createElement(page ? 'a' : 'span');
        el.className = 'page-numbers-js' + (isCurrent ? ' current' : '');
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

    const sideVisible = maxVisible - 2; // -2 бо 30 і 31 завжди праворуч
    const rightEdgeStart = totalPages - 2; // напр. 29, 30, 31

    for (let i = 1; i <= Math.min(sideVisible, totalPages); i++) {
        pages.push(i);
    }

    if (totalPages > maxVisible) {
        pages.push('...');
        pages.push(totalPages - 1);
        pages.push(totalPages);
    }

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

renderPagination(currentPage, totalPages, maxVisible);

window.addEventListener('resize', () => {
    let maxVisible = getMaxVisiblePages();
    renderPagination(currentPage, totalPages, maxVisible);
});


function setAdsBlockCorrect() {
    const grid = document.querySelector('.main__list');
    const items = Array.from(grid.children);

    items.forEach((item, index) => {
        if (item.classList.contains('gads-wrapper') && index > 1) {
            const prev = items[index - 1];
            const beforePrev = items[index - 2];

            const topPrev = prev.getBoundingClientRect().top;
            const topBeforePrev = beforePrev.getBoundingClientRect().top;

            // Якщо останній перед рекламою вже на новому рядку — переносимо його за рекламу
            if (topPrev !== topBeforePrev) {
                grid.insertBefore(prev, item.nextSibling);
            }
        }
    });
}
setAdsBlockCorrect();