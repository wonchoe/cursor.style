var editorExtensionId = document.getElementsByTagName('html')[0].dataset.chromeId;
var c_loaded = '';
var p_loaded = '';
var c_data_file = '';
var p_data_file = '';
var selected_object = '';
var need_to_upload = false;


function onError() {
    alert('Error detected');
}

function getBase64FromImage(url, onSuccess, onError) {
    var xhr = new XMLHttpRequest();

    xhr.responseType = "arraybuffer";
    xhr.open("GET", url);

    xhr.onload = function () {
        var base64, binary, bytes, mediaType;

        bytes = new Uint8Array(xhr.response);
        binary = [].map.call(bytes, function (byte) {
            return String.fromCharCode(byte);
        }).join('');
        mediaType = xhr.getResponseHeader('content-type');
        base64 = [
            'data:',
            mediaType ? mediaType + ';' : '',
            'base64,',
            btoa(binary)
        ].join('');
        onSuccess(base64);
    };
    xhr.onerror = onError;
    xhr.send();
}

function secondImg(e) {
    p_loaded = e;

    e = selected_object;
    catId = (e.dataset.cat) ? e.dataset.cat : -1;
    if (catId != -1) {
        catAlt = e.dataset.cataltname;
        catBase = e.dataset.catbasename;
        catBaseEn = e.dataset.catbasename_en;
        catBaseEs = e.dataset.catbasename_es;
    } else {
        catAlt = -1;
        catBase = -1;
        catBaseEn = -1;
        catBaseEs = -1;
    }

    chrome.runtime.sendMessage(editorExtensionId, {
        cursorType: e.dataset.type,
        cursorCat: catId,
        cursorBase: catBase,
        cursorBaseEn: catBaseEn,
        cursorBaseEs: catBaseEs,
        cursorAlt: catAlt,
        cursorId: e.dataset.id,
        cursorName: e.dataset.name,
        offsetX: e.dataset.offsetX,
        offsetX_p: e.dataset.offsetX_p,
        offsetY: e.dataset.offsetY,
        offsetY_p: e.dataset.offsetY_p,
        c_file_data: c_loaded,
        p_file_data: p_loaded
    }, function (response) {
        getBaseFromExtension();
        //        setTimeout(function () {
        //            checkEnabledButtons();
        //        }, 100);
    });

    //    checkEnabledButtons();
}

function firstImg(e) {
    c_loaded = e;
    getBase64FromImage(p_data_file, secondImg, onError);
}

function isCursorUploaded(e) {

    if (e.dataset.type == 'stat') {
        if (statbase_ext.hasOwnProperty(catAlt) == false) {
            return true;
        }

        list = statbase_ext[catAlt].items;
        for (i = 0; i < list.length; i++) {
            if (statbase_ext[catAlt].items[i].id == e.dataset.id) {
                return false;
                break;
            }
        }
        return true;
    } else if (e.dataset.type == 'anim') {
        for (i = 0; i < anibase_ext.length; i++) {
            console.log(anibase_ext[i].id + '==' + e.dataset.id);
            if (anibase_ext[i].id == e.dataset.id) {
                return false;
                break;
            }
        }
        return true;
    }
}

function addToCollection(e) {
    //setTop(e.dataset.type, e.dataset.id);
    selected_object = e;
    setStateInLocalBase(e.dataset.id, e.dataset.cat, e.dataset.type);

    catId = (e.dataset.cat) ? e.dataset.cat : -1;
    if (catId != -1) {
        catAlt = e.dataset.cataltname;
        catBase = e.dataset.catbasename;
        catBaseEn = e.dataset.catbasename_en;
        catBaseEs = e.dataset.catbasename_es;
    } else {
        catAlt = -1;
        catBase = -1;
        catBaseEn = -1;
        catBaseEs = -1;
    }



    c_data_file = e.dataset.c_file;
    p_data_file = e.dataset.p_file;


    chrome.runtime.sendMessage(editorExtensionId, {
        cursorType: e.dataset.type,
        cursorCat: catId,
        cursorBase: catBase,
        cursorBaseEn: catBaseEn,
        cursorBaseEs: catBaseEs,
        cursorAlt: catAlt,
        cursorId: e.dataset.id,
        cursorName: e.dataset.name,
        offsetX: e.dataset.offsetX,
        offsetX_p: e.dataset.offsetX_p,
        offsetY: e.dataset.offsetY,
        offsetY_p: e.dataset.offsetY_p,
        c_file_data: window.location.origin + c_data_file,
        p_file_data: window.location.origin + p_data_file
    }, function (response) {
        getBaseFromExtension();
    });

    //    if (isCursorUploaded(e)) {
    //        getBase64FromImage(c_data_file, firstImg, onError);
    //    } else {
    //        chrome.runtime.sendMessage(editorExtensionId, {
    //            cursorType: e.dataset.type,
    //            cursorCat: catId,
    //            cursorBase: catBase,
    //            cursorAlt: catAlt,
    //            cursorId: e.dataset.id,
    //            cursorName: e.dataset.name,
    //            offsetX: e.dataset.offsetX,
    //            offsetX_p: e.dataset.offsetX_p,
    //            offsetY: e.dataset.offsetY,
    //            offsetY_p: e.dataset.offsetY_p,
    //            c_file_data: c_loaded,
    //            p_file_data: p_loaded
    //        }, function (response) {
    //            getBaseFromExtension();
    //        });
    //    }
}

function getInstalled(){
    isInstalled = !!document.querySelector('[data-cursor-style]');
    if (isInstalled) {
        if (document.getElementById('banner_hidden')) {
            document.getElementById('banner_hidden').style.display = 'block';
        }

        if ($('#should_install').length > 0) {
            should_install.innerHTML = i18n.messages['test_area_hover_cursor'];
            should_install.parentNode.style.marginTop = '45px';
            should_install.style.color = '#4a8cf5';
            should_install.style.padding = '8px';
            should_install.style.border = '1px dashed #d0d0d0';
            banner_cur_size.style.visibility = 'visible';
        }

        if ($('#banner_in_prev').length > 0) {
            banner_in_prev.onmousemove = function (e) {
                if (e.target.id == 'should_install') {
                    cs_cursor_img.src = banner_in_prev.dataset.pointer;
                } else {
                    cs_cursor_img.src = banner_in_prev.dataset.cursor;
                }

            }
        }

        if ($('#arrow_for_install').length > 0)
            arrow_for_install.remove();
    }
}

setTimeout(getInstalled, 1500);