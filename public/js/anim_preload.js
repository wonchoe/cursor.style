function preview_cursor_animated(e) {
    if (search.value.length == 0) {
        top_base_pr = JSON.parse(data_base_anim);
    }
    sortResults(top_base_pr, 'id', false);
    for (i = 0; i < top_base_pr.length; i++) {
        if (e == top_base_pr[i].id) {
            preview_index = i;
            break;
        }
    }

    banner_in_prev.dataset.cursor = '/resources/animated/cursors/' + top_base_pr[preview_index].c_file;
    banner_in_prev.dataset.pointer = '/resources/animated/pointers/' + top_base_pr[preview_index].p_file;
    banner_in_prev.dataset.offsetX_p = top_base_pr[preview_index].offsetX_p;
    banner_in_prev.dataset.offsetY_p = top_base_pr[preview_index].offsetY_p;    
    banner_in_prev.dataset.offsetX = top_base_pr[preview_index].offsetX;
    banner_in_prev.dataset.offsetY = top_base_pr[preview_index].offsetY;    

    prev_container.style.display = 'block';
    document.body.style.overflow = 'hidden';
    cursor_preview.onclick = function () {
        prev_container.style.display = 'none';
        document.body.style.overflow = ovf;
    }

    if (!isInstalled) {

    } else {

    replaceInstallBtn(top_base_pr, preview_index, 'anim');

    }


    c_preview.innerHTML = '<img src="/resources/animated/cursors/prev/' + top_base_pr[preview_index].c_file_prev + '">';
    p_preview.innerHTML = '<img src="/resources/animated/pointers/prev/' + top_base_pr[preview_index].p_file_prev + '">';

}

function drawAnimatedBlock(k) {
    main_item = document.createElement('div');
    
    main_item.className = 'main__item';
    ani_sticker = document.createElement('img');
    ani_sticker.src = '/images/ani_sticker.png';
    ani_sticker.style.position = 'absolute';
    ani_sticker.style.width = '19px';
    ani_sticker.style.height = '16px';
    ani_sticker.style.opacity = '0.6';

    ani_sticker.style.marginLeft = '94px';
    main_item.appendChild(ani_sticker);
    
    main_item.innerHTML = main_item.innerHTML + '<div class="div_ar_p"><p>' + i18n.cursors['a_'+k.id]; + '</p></div>';       

//    CREATING IMAGE CONTAINER
    main_item_img = document.createElement('div');
    main_item_img.className = 'main__item-img';

    main_item_img.dataset.curId = k.id;
    main_item_img.onclick = function (e) {
        preview_cursor_animated(k.id);
    }

    main_item.appendChild(main_item_img);

    img_1 = document.createElement('img');
    img_1.src = '/resources/animated/cursors/' + k.c_file_prev;

    img_2 = document.createElement('img');
    img_2.src = '/resources/animated/pointers/' + k.p_file_prev;

    main_item_img.appendChild(img_1);
    main_item_img.appendChild(img_2);

//    CREATING BUTTON CONTAINER
    main__btns = document.createElement('div');
    main__btns.className = 'main__btns';
    main_item.appendChild(main__btns);

    btn_1 = document.createElement('a');
    btn_1.id = 'btn_add';
    btn_1.dataset.type = 'anim';
    btn_1.dataset.id = k.id;
    btn_1.dataset.name = i18n.cursors['a_'+k.id];
    btn_1.dataset.offsetX = k.offsetX;
    btn_1.dataset.offsetX_p = k.offsetX_p;
    btn_1.dataset.offsetY = k.offsetY;
    btn_1.dataset.offsetY_p = k.offsetY_p;
    btn_1.dataset.c_file = '/resources/animated/cursors/'+k.c_file;
    btn_1.dataset.p_file = '/resources/animated/pointers/'+k.p_file;    
    
    btn_1.className = 'hvr-shutter-out-horizontal-g';
    btn_1.onclick = function () {
        addToBtn(this);
    };
    btn_1.innerHTML = i18n.collections['add_button'];

    btn_2 = document.createElement('a');
    btn_2.className = 'hvr-shutter-out-horizontal';
    btn_2.innerHTML = i18n.collections['preview_button'];
    btn_2.onclick = function (e) {
        preview_cursor_animated(k.id);
    }
    main__btns.appendChild(btn_1);
    main__btns.appendChild(btn_2);
    checkButton(btn_1);
    return main_item;
}

function drawAnimated(reset, where, sort) {
    if (reset) {
        ani_base = JSON.parse(data_base_anim);
    }

    sortResults(ani_base, 'id', sort);
    ani_base.slice(0, 24).forEach(function (k) {
        block = drawAnimatedBlock(k);
        main_list.appendChild(block);
    });

    // preload(ani_base.slice(12, 24));
    ani_base = ani_base.slice(24);
    if (ani_base.length === 0) {
        $('#show_more').hide();
    }
    checkEnabledButtons();
}