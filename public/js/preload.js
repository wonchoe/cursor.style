function plusSlides(ind) {
    if (base_selected == 'anim') {
        top_base_pr = JSON.parse(data_base_anim);
        sortResults(top_base_pr, 'id', false);

        replaceInstallBtn(top_base_pr, preview_index + ind, 'anim');

        banner_in_prev.dataset.cursor = '/resources/animated/cursors/' + top_base_pr[preview_index + ind].c_file;
        banner_in_prev.dataset.pointer = '/resources/animated/pointers/' + top_base_pr[preview_index + ind].p_file;
    } else {

        replaceInstallBtn(top_base_pr, preview_index + ind, 'stat');

        banner_in_prev.dataset.cursor = '/resources/cursors/' + top_base_pr[preview_index + ind].c_file;
        banner_in_prev.dataset.pointer = '/resources/pointers/' + top_base_pr[preview_index + ind].p_file;
    }

    img_1 = new Image();
    img_1.src = banner_in_prev.dataset.cursor;

    img_2 = new Image();
    img_2.src = banner_in_prev.dataset.pointer;



    preview_index = preview_index + ind;
    if (preview_index >= top_base_pr.length)
        preview_index = 0;
    if (preview_index < 0)
        preview_index = top_base_pr.length - 1;

    if (base_selected == 'anim') {
        c_preview.innerHTML = '<img src="/resources/animated/cursors/prev/' + top_base_pr[preview_index].c_file_prev + '">';
        p_preview.innerHTML = '<img src="/resources/animated/pointers/prev/' + top_base_pr[preview_index].p_file_prev + '">';
    } else {
        c_preview.innerHTML = '<img src="/resources/cursors/' + top_base_pr[preview_index].c_file + '">';
        p_preview.innerHTML = '<img src="/resources/pointers/' + top_base_pr[preview_index].p_file + '">';
    }


    var pr_1 = preview_index + 1;
    if (pr_1 >= top_base_pr.length)
        pr_1 = 0;

    var pr_2 = preview_index - 1;
    if (pr_2 < 0)
        pr_2 = top_base_pr.length - 1;

    if (base_selected == 'anim') {
        var img_1_c = new Image();
        img_1_c.src = '/resources/animated/cursors/prev/' + top_base_pr[pr_2].c_file_prev;

        var img_1_p = new Image();
        img_1_p.src = '/resources/animated/pointers/prev/' + top_base_pr[pr_2].p_file_prev;

        var img_2_c = new Image();
        img_2_c.src = '/resources/animated/cursors/prev/' + top_base_pr[pr_1].c_file_prev;

        var img_2_p = new Image();
        img_2_p.src = '/resources/animated/pointers/prev/' + top_base_pr[pr_1].p_file_prev;
    } else {
        var img_1_c = new Image();
        img_1_c.src = '/resources/cursors/' + top_base_pr[pr_2].c_file;

        var img_1_p = new Image();
        img_1_p.src = '/resources/pointers/' + top_base_pr[pr_2].p_file;

        var img_2_c = new Image();
        img_2_c.src = '/resources/cursors/' + top_base_pr[pr_1].c_file;

        var img_2_p = new Image();
        img_2_p.src = '/resources/pointers/' + top_base_pr[pr_1].p_file;
    }


}


var decodeHTML = function (html) {
    var txt = document.createElement('textarea');
    txt.innerHTML = html;
    data = txt.value;
    s = data.replace(/\\n/g, "\\n")
            .replace(/\\'/g, "\\'")
            .replace(/\\"/g, '\\"')
            .replace(/\\&/g, "\\&")
            .replace(/\\r/g, "\\r")
            .replace(/\\t/g, "\\t")
            .replace(/\\b/g, "\\b")
            .replace(/\\f/g, "\\f");
    s = s.replace(/[\u0000-\u0019]+/g, "");

    return s;
};


function hideLoader() {
    document.body.style.overflow = ovf;
    loader.style.display = 'none';
}

function sortResults(cur_base, prop, asc) {
    cur_base.sort(function (a, b) {
        if (asc) {
            return (a[prop] > b[prop]) ? 1 : ((a[prop] < b[prop]) ? -1 : 0);
        } else {
            return (b[prop] > a[prop]) ? 1 : ((b[prop] < a[prop]) ? -1 : 0);
        }
    });
}

function drawCatBlock(k) {
    main_item = document.createElement('div');
    main_item.className = 'collection__item_cat';
    main_item.style.cursor = 'pointer';
    main_item.onclick = function () {
        location.href = 'collections/' + k.alt_name;
    }
    main_item.innerHTML = '<div><p class="collection__item_cat_title">' + k.base_name + '</p></div>';


//    CREATING IMAGE CONTAINER
    main_item_img = document.createElement('div');
    main_item_img.className = 'main__cat-img';
    main_item.appendChild(main_item_img);

    img_1 = document.createElement('img');
    img_1.src = '/resources/categories/' + k.img;

    main_item_img.appendChild(img_1);
    descr = document.createElement('div');
    descr.className = 'collection__description';
    descr.innerHTML = k.short_descr;
    main_item.appendChild(descr);
    return main_item;
}

function preview_cursor(e) {

    if (cs_search.value.length == 0) {
        top_base_pr = data_base.data;
    }
    sortResults(top_base_pr, sort_type, false);
    for (i = 0; i < top_base_pr.length; i++) {
        if (e == top_base_pr[i].id) {
            preview_index = i;
            break;
        }
    }


    banner_in_prev.dataset.cursor = '/resources/cursors/' + top_base_pr[preview_index].c_file;
    banner_in_prev.dataset.offsetX = top_base_pr[preview_index].offsetX;
    banner_in_prev.dataset.offsetY = top_base_pr[preview_index].offsetY;
    banner_in_prev.dataset.pointer = '/resources/pointers/' + top_base_pr[preview_index].p_file;
    banner_in_prev.dataset.offsetX_p = top_base_pr[preview_index].offsetX_p;
    banner_in_prev.dataset.offsetY_p = top_base_pr[preview_index].offsetY_p;    


    prev_container.style.display = 'block';
    document.body.style.overflow = 'hidden';
    cursor_preview.onclick = function () {
        prev_container.style.display = 'none';
        document.body.style.overflow = ovf;
    }

    if (!isInstalled) {

    } else {
        if (document.getElementById('prev_btn')) {
            prev_btn.style.marginTop = '9px';
        }
        replaceInstallBtn(top_base_pr, preview_index, 'stat');
    }


    c_preview.innerHTML = '<img src="/resources/cursors/' + top_base_pr[preview_index].c_file + '">';
    p_preview.innerHTML = '<img src="/resources/pointers/' + top_base_pr[preview_index].p_file + '">';
}


function drawCursorBlock(k) {
    main_item = document.createElement('div');
    main_item.className = 'main__item';
    main_item.innerHTML = '<div class="div_ar_p"><p>' + i18n.cursors['c_'+k.id] + '</p></div>';


//    CREATING IMAGE CONTAINER
    main_item_img = document.createElement('div');
    main_item_img.className = 'main__item-img';

    main_item_img.dataset.curId = k.id;
    main_item_img.onclick = function (e) {
        preview_cursor(k.id);
    }

    
    main_item.appendChild(main_item_img);

    img_1 = document.createElement('img');
    img_1.src = '/resources/cursors/' + k.c_file;
    img_1.alt = i18n.cursors['c_'+k.id];
    img_1.title = i18n.cursors['c_'+k.id]+' '+i18n.collections.cursor;

    img_2 = document.createElement('img');
    img_2.src = '/resources/pointers/' + k.p_file;
    img_2.alt = i18n.cursors['c_'+k.id];
    img_2.title = i18n.cursors['c_'+k.id]+' '+i18n.collections.pointer;

    main_item_img.appendChild(img_1);
    main_item_img.appendChild(img_2);

//    CREATING BUTTON CONTAINER
    main__btns = document.createElement('div');
    main__btns.className = 'main__btns';
    main_item.appendChild(main__btns);

    btn_1 = document.createElement('a');
    btn_1.dataset.type = 'stat';
    btn_1.dataset.cat = k.cat;
    btn_1.dataset.id = k.id;
    btn_1.dataset.name = i18n.cursors['c_'+k.id];
    btn_1.dataset.offsetX = k.offsetX;
    btn_1.dataset.offsetX_p = k.offsetX_p;
    btn_1.dataset.offsetY = k.offsetY;
    btn_1.dataset.offsetY_p = k.offsetY_p;
    btn_1.dataset.c_file = '/resources/cursors/'+k.c_file;
    btn_1.dataset.p_file = '/resources/pointers/'+k.p_file;
    //btn_1.id = 'btn_add';
    btn_1.className = 'hvr-shutter-out-horizontal-g';
    btn_1.onclick = function () {
        addToBtn(this);
    };
    btn_1.innerHTML = i18n.collections['add_button'];

    btn_2 = document.createElement('a');
    btn_2.className = 'hvr-shutter-out-horizontal';
    btn_2.innerHTML = i18n.collections['preview_button'];
    btn_2.onclick = function (e) {
        preview_cursor(k.id);
    }
    main__btns.appendChild(btn_1);
    main__btns.appendChild(btn_2);

    checkButton(btn_1);
    return main_item;
}

function preload(img_array) {
    var images = [];
    img_array.forEach(function (k) {
        images.push(new Image());
        images[images.length - 1].src = '/resources/cursors/' + k.c_file;

        images.push(new Image());
        images[images.length - 1].src = '/resources/pointers/' + k.p_file;
    })
}

function drawTopCategories() {
    sortResults(top_base_c, 'priority', false);
    top_base_c.forEach(function (k) {
        block = drawCatBlock(k);
        main_list.appendChild(block);
    });
    $('#show_more').hide();
}

function drawTop(reset, where, sort) {
    if (reset) {
        top_base = data_base.data;
    }

    sortResults(top_base, where, sort);

    top_base.slice(0, 24).forEach(function (k) {
        block = drawCursorBlock(k);
        main_list.appendChild(block);
    });

    preload(top_base.slice(24, 48));
    top_base = top_base.slice(24);
    if (top_base.length === 0) {
        $('#show_more').hide();
    }
}

function drawBaseFromSearch() {

    base.slice(0, 16).forEach(function (k) {
        block = drawCursorBlock(k);
        main_list.appendChild(block);
    });

    preload(base.slice(12, 32));
    base = base.slice(16);

    if (base.length === 0) {
        $('#show_more').hide();
    }

}

function search_json(value) {
    base = data_base.data;
    base_filtered = [];
    base.forEach(function (e) {
        name = e.name.toLowerCase();
        if (name.search(value.toLowerCase()) != -1) {
            base_filtered.push(e);
        }
    })

    search_base = base_filtered;
    top_base_pr = search_base;

    main_list.innerHTML = '';
    base = base_filtered;

    if (base.length == 0) {
        not_found = document.createElement('div');
        not_found.className = 'not_found';
        not_found.id = 'not_found';
        not_found.innerHTML = i18n.messages['main_page_no_result'];
        main_list.appendChild(not_found);
    } else if (document.getElementById('not_found'))
        not_found.remove();

    drawBaseFromSearch();

    $('#show_more').show();
    show_more.onclick = function () {
        drawBaseFromSearch();
    }

}

function db_loaded() {
    top_base = data_base.data;
    setTimeout(function () {
        drawTop(true, 'top', false);
    }, 100);
}
;


$(function () {
    data_base = JSON.parse(decodeHTML(data_base_c));
    categories = JSON.parse(decodeHTML(data_base_cat));
    top_base_c = categories.data_cat;
    db_loaded();
    drawNew();


    $('#cs_search').focusout(function () {
        if (!cs_search.value) {
            menu_top.click();
        }
    });


    cs_search.oninput = function (e) {
        base_selected = 'search';
        search_json(e.target.value);
        if (base.length === 0) {
            $('#show_more').hide();
        }

        r = document.getElementsByClassName('cur_menu');
        for (i = 0; i < r.length; i++) {
            if (r[i].classList.contains('active'))
                r[i].classList.remove('active');
        }
        menu_selector.style.display = 'none';
    }

    menu_top.onclick = function (e) {
        if (e.target.classList.contains('active'))
            return;
        sort_type = 'top';
        base_selected = 'top';
        cs_search.value = '';
        main_list.innerHTML = '';
        drawTop(true, 'top', false);
        $('#show_more').show();
        show_more.onclick = function () {
            drawTop(false, 'top', false);
        }
    }

    menu_animated.onclick = function (e) {
        base_selected = 'anim';
        if (e.target.classList.contains('active'))
            return;
        cs_search.value = '';
        main_list.innerHTML = '';
        $('#show_more').show();
        drawAnimated(true, 'id', false);
        show_more.onclick = function () {
            drawAnimated(false, 'id', false);
        }
    }



    menu_new.onclick = function (e) {
        if (e.target.classList.contains('active'))
            return;
        base_selected = 'new';
        sort_type = 'new';
        cs_search.value = '';
        main_list.innerHTML = '';
        drawTop(true, 'id', false);
        $('#show_more').show();
        show_more.onclick = function () {
            drawTop(false, 'id', false);
        }
    }

    menu_collections.onclick = function (e) {
        if (e.target.classList.contains('active'))
            return;
        base_selected = 'collection';
        cs_search.value = '';
        main_list.innerHTML = '';
        drawTopCategories(true, 'id', false);
    }

});



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