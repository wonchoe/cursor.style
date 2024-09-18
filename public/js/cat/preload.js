var preview_index = 0;

function plusSlides(ind) {

    replaceInstallBtn(top_base, preview_index + ind, 'stat');

    banner_in_prev.dataset.cursor = '/resources/cursors/' + top_base[preview_index + ind].c_file;
    banner_in_prev.dataset.pointer = '/resources/pointers/' + top_base[preview_index + ind].p_file;

    preview_index = preview_index + ind;
    if (preview_index >= top_base.length)
        preview_index = 0;
    if (preview_index < 0)
        preview_index = top_base.length - 1;

    img_1 = new Image();
    img_1.src = banner_in_prev.dataset.cursor;

    img_2 = new Image();
    img_2.src = banner_in_prev.dataset.pointer;

    c_preview.innerHTML = '<img src="/resources/cursors/' + top_base[preview_index].c_file + '">';
    p_preview.innerHTML = '<img src="/resources/pointers/' + top_base[preview_index].p_file + '">';
}


function preview_cursor(e) {

    top_base = data_base.data
    //sortResults(top_base, 'id', false);
    for (i = 0; i < top_base.length; i++) {
        if (e == top_base[i].id) {
            preview_index = i;
            break;
        }
    }

    banner_in_prev.dataset.cursor = '/resources/cursors/' + top_base[preview_index].c_file;
    banner_in_prev.dataset.offsetX = top_base[preview_index].offsetX;
    banner_in_prev.dataset.offsetY = top_base[preview_index].offsetY;
    banner_in_prev.dataset.pointer = '/resources/pointers/' + top_base[preview_index].p_file;
    banner_in_prev.dataset.offsetX_p = top_base[preview_index].offsetX_p;
    banner_in_prev.dataset.offsetY_p = top_base[preview_index].offsetY_p; 

    prev_container.style.display = 'block';
    document.body.style.overflow = 'hidden';
    cursor_preview.onclick = function () {
        prev_container.style.display = 'none';
        document.body.style.overflow = ovf;
    }

    if (!isInstalled) {
        
    } else {
        replaceInstallBtn(top_base, preview_index, 'stat');
    }

    c_preview.innerHTML = '<img src="/resources/cursors/' + top_base[preview_index].c_file + '">';
    p_preview.innerHTML = '<img src="/resources/pointers/' + top_base[preview_index].p_file + '">';
}

window.onload = function () {
    if (document.getElementById('banner__prev_cur_img'))
        banner__prev_cur_img.src = '/images/no_access.svg';
    if (typeof data_base != 'undefined') {
        data_base = data_base.data;
    }
    
//    if (isInstalled) data_base = data_base.data
//    else data_base = data_base;    
}


$(document).keyup(function (e) {
    if (e.key === "Escape") {
        if (prev_container.style.display == 'block') {
            prev_container.style.display = 'none';
            document.body.style.overflow = ovf;
        }
    }

    if ((e.key == 'ArrowLeft') || (e.keyCode === 65)) {
        if (prev_container.style.display == 'block')
            plusSlides(-1);
    }
    if ((e.key == 'ArrowRight') || (e.keyCode === 68)) {
        if (prev_container.style.display == 'block')
            plusSlides(1);
    }
});

$(function () {
    setTimeout(function () {
        checkEnabledButtons();
    }, 50);
})
