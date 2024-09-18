function getOffset(c_with, c_offset) {
    pr = (c_with / 85) * 100;
    offset = (c_offset * pr) / 100;
    return offset;
}


window.onload = function () {

    if (!document.getElementById('cs_test_crusor')) {

        cs_test_cursor = document.createElement('div');
        cs_test_cursor.className = 'cs_test_cursor';
        cs_test_cursor.id = 'cs_test_cursor';
        cs_test_cursor.style.position = 'fixed';
        cs_test_cursor.style.pointerEvents = 'none';
        cs_test_cursor.style.zIndex = '2147483647';
        cs_test_cursor.style.visibility = 'hidden';
        cs_test_cursor.innerHTML = '<img src="" id="cs_test_cursor_img" style="width:48px; height:48px;position: fixed;"><img src="" id="cs_test_cursor_img_p" style="width:48px; height:48px;position: fixed;">';
        document.body.insertBefore(cs_test_cursor, document.body.children[0]);
    }
    cs_test_cursor_img = document.getElementById('cs_test_cursor_img');
    cs_test_cursor_img_p = document.getElementById('cs_test_cursor_img_p');


    banner_in_prev.onmousemove = function (e) {
        block_cursor = true;
        cs_test_cursor.style.top = e.clientY + 'px';
        cs_test_cursor.style.left = e.clientX + 'px';

        if (typeof hideCsCursor !== "undefined")
            hideCsCursor();        
        cs_test_cursor.style.visibility = 'visible';
 


        if (!isInstalled) {
            cs_test_cursor_img.src = '/images/no_access.svg';
            cs_test_cursor_img_p.src = '/images/no_access.svg';
        } else {
            cs_test_cursor_img.src = banner_in_prev.dataset.cursor;
            cs_test_cursor_img_p.src = banner_in_prev.dataset.pointer;
        }


        cs_test_cursor_img.style.marginLeft = '-' + getOffset(48, banner_in_prev.dataset.offsetX) + 'px';
        cs_test_cursor_img.style.marginTop = '-' + getOffset(48, banner_in_prev.dataset.offsetY) + 'px';
        cs_test_cursor_img_p.style.marginLeft = '-' + getOffset(48, banner_in_prev.dataset.offsetX_p) + 'px';
        cs_test_cursor_img_p.style.marginTop = '-' + getOffset(48, banner_in_prev.dataset.offsetY_p) + 'px';
        should_install.setAttribute('style','cursor: none !important; color: rgb(74, 140, 245); padding: 8px; border: 1px dashed rgb(208, 208, 208);');
        if (e.target.id == 'should_install') {
            cs_test_cursor_img.style.visibility = 'hidden';
            cs_test_cursor_img_p.style.visibility = 'visible';
        } else {
            cs_test_cursor_img.style.visibility = 'visible';
            cs_test_cursor_img_p.style.visibility = 'hidden';
        }

    }
    
 
    banner_in_prev.onmouseleave = function () {
        block_cursor = false;
        cs_test_cursor_img.style.visibility = 'hidden';
        if (document.body.dataset.csCursorState == 'true') {
            if (typeof showCsCursor !== "undefined")
                showCsCursor();
            
        }
        cs_test_cursor.style.visibility = 'hidden';
    }

//    banner_in_prev.onmousemove = function (e) {
//        cs_test_cursor.style.top = e.clientY + 'px';
//        cs_test_cursor.style.left = e.clientX + 'px';
//    }

}; 