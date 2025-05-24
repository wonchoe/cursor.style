document.onkeydown = function(evt) {
    evt = evt || window.event;
    var isEscape = false;
    if ("key" in evt) {
        isEscape = (evt.key === "Escape" || evt.key === "Esc");
    } else {
        isEscape = (evt.keyCode === 27);
    }
    if (isEscape) {
        starClose();
    }
};

if (localStorage.hasOwnProperty('visit_count')) {
    if (!localStorage.hasOwnProperty('dontshow')) {
        visits = parseInt(localStorage.getItem('visit_count'));
        visits = visits + 1;
        if (visits>=25) {
            ovelay.style.display = 'block';
            votepls.style.display = 'flex';            
        }
        localStorage.setItem('visit_count', visits);       
    }
} else {
     localStorage.setItem('visit_count', 1);       
}

function openChromeCatalog() {
    localStorage.setItem('visit_count', 0);       
    if (dontshow.checked)
        localStorage.setItem('dontshow', 1);
    ovelay.style.display = 'none';
    votepls.style.display = 'none';
    window.open("https://chrome.google.com/webstore/detail/cursor-style-custom-curso/bmjmipppabdlpjccanalncobmbacckjn/reviews");
}

function starClose() {
    localStorage.setItem('visit_count', 0);       
    if (dontshow.checked)
        localStorage.setItem('dontshow', 1);
    ovelay.style.display = 'none';
    votepls.style.display = 'none';
}

document.querySelector('#maybelater').addEventListener('click', function () {
    starClose();
});

document.querySelector('#ovelay').addEventListener('click', function () {
    starClose();
});


document.querySelector('#star_close_btn').addEventListener('click', function () {
    starClose();
});

document.querySelector('#star_img_container').addEventListener('click', function () {
    openChromeCatalog();
});


document.querySelector('#star_vote').addEventListener('click', function () {
    openChromeCatalog();
});