var block_cursor = false;

function clearCustomStylesForDomElements() {
    r = document.getElementsByTagName('*');
    for (i = 0; i < r.length; i++) {
        if (r[i]) {
            if ((r[i].style.cursor == 'none') && (r[i].dataset.cur)) {
                r[i].style.setProperty('cursor','default');
                r[i].removeAttribute('data-cur');
            }
        }
    }
}

function getOffset(c_with, c_offset) {
    pr = (c_with / 85) * 100;
    offset = (c_offset * pr) / 100;
    return offset;
}

function clearCsCursor() {
    if (document.getElementById('cs_no_cursor_css'))
        cs_no_cursor_css.remove();
    if (document.getElementById('cs_cursor_css'))
        cs_cursor_css.remove();
    hideCsCursor();
}





function init_cs_cursor() {
    if (!localStorage.hasOwnProperty('storage_data'))
        return;
    
    storage_data = JSON.parse(localStorage.getItem('storage_data'));

    if (!storage_data.hasOwnProperty('enabled'))
        return;
    
    if (storage_data.enabled == false) {
        document.body.dataset.csCursorState = false;
        return;
    }
    
    document.body.dataset.csCursorState = true;

    cs_cursor_img.src = storage_data.cursor.path;
    cs_cursor_img_p.src = storage_data.pointer.path;
    setCursorParams(storage_data);
    showCsCursor();

    setEvent();
}


function createCursor() {
    curs = Array.from(document.querySelectorAll('#cs_cursor'));
    curs.forEach(function (el) {
        el.remove();
    });
    if (!document.getElementById('cs_crusor')) {
        cs_cursor = document.createElement('div');
        cs_cursor.className = 'cs_cursor';
        cs_cursor.id = 'cs_cursor';
        cs_cursor.style.position = 'fixed';
        cs_cursor.style.pointerEvents = 'none';
        cs_cursor.style.zIndex = '2147483647';
        cs_cursor.innerHTML = '<img src="" id="cs_cursor_img" style="width:64px; height:64px;position: fixed;"><img src="" id="cs_cursor_img_p" style="width:64px; height:64px;position: fixed;">';
        document.documentElement.appendChild(cs_cursor);
    }
    cs_cursor_img = document.getElementById('cs_cursor_img');
    cs_cursor_img_p = document.getElementById('cs_cursor_img_p');
    hideCsCursor();
}



function updateFromExt(cs_data) {

    cs_data = JSON.parse(cs_data);
    localStorage.setItem('storage_data', JSON.stringify(cs_data));

    if (typeof checkDisabledButtons != 'undefined')
        checkDisabledButtons();

    if ((!cs_data.hasOwnProperty('cursor')) || (!cs_data.hasOwnProperty('pointer')))
        return;

    if (cs_data.hasOwnProperty('anibase_ext'))
        anibase_ext = cs_data.anibase_ext;
    if (cs_data.hasOwnProperty('statbase_ext'))
        statbase_ext = cs_data.statbase_ext;


    if (cs_data.enabled == false) {
        document.body.dataset.csCursorState = false;
        block_cursor = true;
        clearCsCursor();
        clearCustomStylesForDomElements();
        return;
    } else {
        block_cursor = false;
    }

    document.body.dataset.csCursorState = true;
    
    cs_cursor_img.src = cs_data.cursor.path;
    cs_cursor_img_p.src = cs_data.pointer.path;
    setCursorParams(cs_data);
    showCsCursor();

    setEvent();
    //setCursorPointerInClass();
}


function setCursorParams(cursor_data) {
    size = cursor_data.width;
    cs_cursor_img.style.width = size + 'px';
    cs_cursor_img.style.height = size + 'px';
    cs_cursor_img.style.maxWidth = size + 'px';
    cs_cursor_img.style.minWidth = size + 'px';
    cs_cursor_img.style.maxHeight = size + 'px';
    cs_cursor_img.style.maxHeight = size + 'px';
    cs_cursor_img.style.marginLeft = '-' + getOffset(size, cursor_data.cursor.offsetX) + 'px';
    cs_cursor_img.style.marginTop = '-' + getOffset(size, cursor_data.cursor.offsetY) + 'px';

    cs_cursor_img_p.style.width = size + 'px';
    cs_cursor_img_p.style.height = size + 'px';
    cs_cursor_img_p.style.maxWidth = size + 'px';
    cs_cursor_img_p.style.minWidth = size + 'px';
    cs_cursor_img_p.style.maxHeight = size + 'px';
    cs_cursor_img_p.style.maxHeight = size + 'px';
    cs_cursor_img_p.style.marginLeft = '-' + getOffset(size, cursor_data.pointer.offsetX) + 'px';
    cs_cursor_img_p.style.marginTop = '-' + getOffset(size, cursor_data.pointer.offsetY) + 'px';
}

function hideCsCursor() {
    cs_cursor.style.left = '9999px';
    cs_cursor.style.visibility = 'hidden';
    cs_cursor_img.style.visibility = 'hidden';
    cs_cursor_img_p.style.visibility = 'hidden';
}

function showCsCursor() {   
    cs_cursor.style.visibility = 'visible';
    cs_cursor_img.style.visibility = 'visible';
}


function setMuatation(){
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            block_cursor = true;
            clearCsCursor();
        });
    });
    var config = {childList: true, subtree: true};
    observer.observe(cs_div_reload, config);
    
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            updateFromExt(elem_ext.innerHTML);
        });
    });
    var config = {childList: true, subtree: true};
    observer.observe(elem_ext, config);    
}

setMuatation();
createCursor();
init_cs_cursor();














































//==============================================================================
// SET ELEMENT DATASET CUR IF ELEMENT HAVE A POINTER INSIDE A CLASS
//==============================================================================


//
//function setCursorPointerInClass() {
//    r = document.getElementsByTagName('*');
//    for (i = 0; i < r.length; i++) {
//        if (r[i]) {
//            st = getComputedStyle(r[i]);
//            cs_attr = (r[i].getAttribute('style') !== null) ? r[i].getAttribute('style') + ';' : '';
//            if (st.cursor == 'pointer') {
//                r[i].dataset.cur = 'pointer';
//                r[i].setAttribute('style', cs_attr + ' cursor:none  !important');
//            } else if (st.cursor == 'auto') {
//                r[i].setAttribute('style', cs_attr + ' cursor:none  !important');
//            } else if (st.cursor == 'default') {
//                r[i].setAttribute('style', cs_attr + ' cursor:none  !important');
//            }
//        }
//    }
//}

function setCursorPointerInClass(el) {
    st = getComputedStyle(el);
    if (st.cursor == 'pointer') {
        el.dataset.cur = 'pointer';
        el.style.setProperty('cursor','none','important');
    } else if (st.cursor == 'auto') {
        el.dataset.cur = 'cursor';
        el.style.setProperty('cursor','none','important');
    } else if (st.cursor == 'default') {
        el.dataset.cur = 'cursor';
        el.style.setProperty('cursor','none','important');
    }
}


function changeCursorState(type = 'cursor') {
    if (type == 'cursor') {
        cs_cursor_img.style.visibility = 'visible';
        cs_cursor_img_p.style.visibility = 'hidden';
    } else if (type == 'pointer') {
        cs_cursor_img.style.visibility = 'hidden';
        cs_cursor_img_p.style.visibility = 'visible';
}
}




function hideStyled() {

    if (document.getElementById('cs_cursor_css')) {
        cs_cursor_css.remove();
    }

    if (!document.getElementById('cs_no_cursor_css')) {
        cStyle = document.createElement('style');
        cStyle.id = 'cs_no_cursor_css';
        cStyle.innerHTML = '* { cursor: none !important;} *:before,*:after{cursor: none !important;}';
        document.documentElement.appendChild(cStyle);
    }
}


if (!Element.prototype.matches) {
    Element.prototype.matches =
            Element.prototype.matchesSelector ||
            Element.prototype.webkitMatchesSelector ||
            Element.prototype.mozMatchesSelector ||
            Element.prototype.msMatchesSelector ||
            Element.prototype.oMatchesSelector ||
            function (s) {
                var matches = (this.document || this.ownerDocument).querySelectorAll(s),
                        i = matches.length;
                while (--i >= 0 && matches.item(i) !== this) {
                }
                return i > -1;
            };
}

function delegate(el, evt, sel, handler) {
    el.addEventListener(evt, function (event) {
        var t = event.target;
        while (t && t !== this) {
            if (t.matches(sel)) {
                handler.call(t, event);
            }
            t = t.parentNode;
        }
    });
}


function setEvent() {

    delegate(document, "mouseover", "a, button, input[type=range], [data-cur=pointer] ", function (event) {
        if (block_cursor == true)
            return;
        changeCursorState('pointer');
    });

    delegate(document, "mouseout", "a, button, input[type=range], [data-cur='pointer']", function (event) {
        if (block_cursor == true)
            return;
        changeCursorState('cursor');
    });

    document.onmouseover = function (e) {
        try {
            if (block_cursor == true)
                return;

            setCursorPointerInClass(e.target);
            hideStyled();

            if (window.location !== window.parent.location) {
                window.parent.postMessage('iframeleft', '*');
            }


            if (e.target.tagName == 'IFRAME') {
                window.top.postMessage('iframeleft', '*');
            }
            if (e.relatedTarget)
                if (e.relatedTarget.tagName == 'IFRAME') {
                    e.relatedTarget.contentWindow.postMessage('iframeleft', '*');
                }

        } catch (e) {
        }
    }


    document.onmouseenter = function (e) {
        if (e.target == document) {
            hideCsCursor();
        }
    }

    document.documentElement.onmousemove = function (event) {
        if (block_cursor == true)
            return;

        if ((cs_cursor_img.style.visibility == 'hidden') && (cs_cursor_img_p.style.visibility == 'hidden'))
            cs_cursor_img.style.visibility = 'visible';

        if (cs_cursor_img_p.style.visibility == 'visible')
            cs_cursor_img.style.visibility = 'hiden';

        cs_cursor.style.top = event.clientY + 'px';
        cs_cursor.style.left = event.clientX + 'px';
    };

    if (location.href.search('cursor.style') > 0) {
        $('body').on('mouseleave', '#banner_in_prev', function (e) {
            if (document.documentElement.dataset.blocked)
                block_cursor = true;
            if (block_cursor == true)
                return;
        });
    }


    document.documentElement.onmouseleave = function () {
        if (block_cursor == true)
            return;
        hideCsCursor();
    }



    window.oncontextmenu = function () {
        if (block_cursor == true)
            return;
        try {
            hideCsCursor();
        } catch (e) {
        }
    }

    //  setFileDialog();
}


function matches(node, selector) {
    var nativeMatches = (node.matches || node.msMatchesSelector);
    try {
        return(nativeMatches.call(node, selector));
    } catch (error) {
        return(false);
    }
}

//
//
//function setFileDialog() {
//
//    frames = document.getElementsByTagName('iframe');
//    for (i = 0; i < frames.length; i++) {
//        frames[i].onmousemove = function () {
//            cs_cursor.style.visibility = 'hidden';
//        }
//
//        frames[i].onmouseenter = function () {
//            cs_cursor.style.visibility = 'hidden';
//        }
//    }
//
//    $("body").keyup(function (e) {
//        if (27 == e.keyCode) {
//            cs_cursor.style.visibility = 'visible';
//        }
//    });
//
//    var $file = $("input:file");
//    $file.bind("click", function (e) {
//        cs_cursor.style.visibility = 'hidden';
//        $(this).focus();
//    });
//    $file.bind("change", function (e) {
//        cs_cursor.style.visibility = 'visible';
//        $(this).blur();
//    });
//    $file.keyup(function (e) {
//        if (27 == e.keyCode) {
//            cs_cursor.style.visibility = 'visible';
//            $(this).blur();
//            return false;
//        }
//    });
//    setTimeout(setFileDialog, 500);
//}





window.onmessage = function (e) {
    if (e.data == 'iframeleft') {
        hideCsCursor();
    }
}