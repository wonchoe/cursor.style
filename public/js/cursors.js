function translate(translateTo, text, translateFrom) {
    if (translateFrom === undefined || translateFrom === null) {
        translateFrom = 'en';
    }
    return new Promise((resolve, reject) => {
        const url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl="
                + translateFrom + "&tl=" + translateTo + "&dt=t&q=" + encodeURI(text);
        fetch(url).then(response => {
            response.json().then(data => {
                resolve(data[0])
            }, reject)
        }, reject)
    });
}

function setDataEn(data_resp) {
    var str = '';
    var str2 = '';
    arr = {};
    for (var i = 0; i < data_resp.length; i++) {
        key = data_resp[i][0].replace('.', '').trim();
        value = data_resp[i][1].replace('.', '').trim();
        arr[value] = key;
		c_name_en.value = key;
    }

}

function setDataEs(data_resp) {
    var str = '';
    var str2 = '';
    arr = {};
    for (var i = 0; i < data_resp.length; i++) {
        key = data_resp[i][0].replace('.', '').trim();
        value = data_resp[i][1].replace('.', '').trim();
        arr[value] = key;
		c_name_es.value = key;
    }

}

translate_btn.addEventListener('click',function(){
    translate('en',c_name.value,'ru').then(setDataEn);
    translate('es',c_name.value,'ru').then(setDataEs);
});

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

function redrawCategories() {

    $.get('/admin/cursors/categories/get', function (data) {
        data = JSON.parse(data);
        categoriesList.innerHTML = '';
        if (data.length == 0) {
            var opt = document.createElement('option');
            opt.appendChild(document.createTextNode('Создайте категорию'));
            categoriesList.appendChild(opt);
            pic_row.style.display = 'none';
        } else
            pic_row.style.display = 'flex';

        data.forEach(function (data) {
            var opt = document.createElement('option');
            opt.appendChild(document.createTextNode(data.base_name));
            opt.dataset.alt_name = data.alt_name;
            opt.dataset.cat_id = data.id;
            opt.value = data.id;
            categoriesList.appendChild(opt);
        });
        ind = (localStorage.getItem('cur_cat')) ? localStorage.getItem('cur_cat') : 0;
        categoriesList.selectedIndex = ind;


    });

}

$(function () {
    inputGroupFile01.onchange = function (e) {
        fileLabel.textContent = inputGroupFile01.files[0].name;
    }
    
    redrawCategories();
    $('#new-cat-form').on('submit', function (event) {
        event.preventDefault();
        $('#loaderNew').show();
        $.ajax({
            url: "/admin/cursors/categories/save",
            method: "POST",
            processData: false,
            contentType: false,
            data: new FormData(this),
            success: function (data)
            {
                $('#loaderNew').hide();
                if (!data.result) {
                    alert('Ошибка создания категории! ' + data.message);
                    return;
                }
                redrawCategories();
                closeModal.click();
            }
        }).fail(function () {
            $('#loaderNew').hide();
            alert("error");
        })
                .always(function () {
                    $('#loaderNew').hide();
                });
    });


})