// ON SLIDERS CHANGE
$("#cursorW").change(function () {
    if (pic_row.dataset.cursor == 'cursor') {
        cursor_data.dataset.size = 64;
    } else {
        pointer_data.dataset.size = 64;
    }
});

$("#offsetX").change(function () {
    if (pic_row.dataset.cursor == 'cursor') {
        oX.value = Math.round($('#offsetX').slider('getValue') * step);
        oY.value = Math.round($('#offsetY').slider('getValue') * step);
    } else {
        oXp.value = Math.round($('#offsetX').slider('getValue') * step);
        oYp.value = Math.round($('#offsetY').slider('getValue') * step);
    }
});

$("#offsetY").change(function () {
    if (pic_row.dataset.cursor == 'cursor') {
        oX.value = Math.round($('#offsetX').slider('getValue') * step);
        oY.value = Math.round($('#offsetY').slider('getValue') * step);
    } else {
        oXp.value = Math.round($('#offsetX').slider('getValue') * step);
        oYp.value = Math.round($('#offsetY').slider('getValue') * step);
    }
});
//


function clearStyle() {
    $('.cursor-preview').css('border', '6px solid #F8F8F8');
    $('.pointer-preview').css('border', '6px solid #F8F8F8');
}

$('.cursor-preview').click(function (e) {
    pic_row.dataset.cursor = 'cursor';
    clearStyle();
    $('.cursor-preview').css('border', '6px solid #ecff87');

    size = (cursor_data.dataset.size) ? cursor_data.dataset.size : 24;
    $('#cursorW').slider('setValue', size);

    offsetX = (cursor_data.dataset.offsetX) ? cursor_data.dataset.offsetX : 0;
    $('#offsetX').slider('setValue', offsetX);

    offsetY = (cursor_data.dataset.offsetY) ? cursor_data.dataset.offsetY : 0;
    $('#offsetY').slider('setValue', offsetY);
})

$('.pointer-preview').click(function (e) {
    pic_row.dataset.cursor = 'pointer';
    clearStyle();
    $('.pointer-preview').css('border', '6px solid #ecff87');

    size = (pointer_data.dataset.size) ? pointer_data.dataset.size : 24;
    $('#cursorW').slider('setValue', size);

    offsetX = (pointer_data.dataset.offsetX) ? pointer_data.dataset.offsetX : 0;
    $('#offsetX').slider('setValue', offsetX);

    offsetY = (pointer_data.dataset.offsetY) ? pointer_data.dataset.offsetY : 0;
    $('#offsetY').slider('setValue', offsetY);
})

$('#categoriesList').change(function () {
    cat_input.value = categoriesList.value;
    localStorage.setItem('cur_cat', categoriesList.selectedIndex);
});

$("#test_box").mouseenter(function () {
    if (!pic_row.dataset.cursor) {
        $('.cursor-preview').click();
    }
    if (pic_row.dataset.cursor == 'cursor') {
        cursor_img.src = imagePreviewCursor.style.backgroundImage.slice(4, -1).replace(/"/g, "");
    } else {
        cursor_img.src = imagePreviewPointer.style.backgroundImage.slice(4, -1).replace(/"/g, "");
    }

    cursor_img.style.width = '64px';
    cursor_img.style.height = '64px';
    $("#test_cursor").show();
});

$("#test_box").mouseleave(function () {
    $("#test_cursor").hide();
});

$("#test_box").mousemove(function (event) {
    //step = ($('#cursorW').slider('getValue') / 100);   
    step = (64 / 100);
    test_cursor.style.left = ((event.pageX) - ($('#offsetX').slider('getValue') * step)) + 'px';
    test_cursor.style.top = ((event.pageY) - ($('#offsetY').slider('getValue') * step)) + 'px';
    cursor_img.style.width = '64px';
    cursor_img.style.height = '64px';
});


