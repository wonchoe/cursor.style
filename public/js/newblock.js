function drawNew() {
    if (isInstalled) {
        try {
            var catlink = '/collections/';
            newblock.style.display = 'flex';
            sortResults(top_base, 'id', false);
            for (var i = 0; i < 3; i++) {
                for (j = 0; j<top_base_c.length;j++){
                    if (top_base_c[j].id == top_base[i].cat){
                        var catlink = '/collections/'+top_base_c[j].alt_name;
                        break;
                    }
                }
                div = document.createElement('div');
                div.className = 'main__item';
                div.style.width = '100%';
                div.innerHTML = '<div style="position: absolute;width: 64px;margin-left: -17px;margin-top: -14px;">\
        <img src="/images/new.png"></div>\
        <a href="'+catlink+'"><div class="div_ar_p"><p>' + top_base[i].name + '</p></div>\
        <div class="main__item-img" style="height: 127px;" data-cur-id="466"><img src="/resources/cursors/' + top_base[i].c_file + '" alt="' + top_base[i].name + '" title="' + top_base[i].name + ' cursor">\
        <img src="/resources/pointers/' + top_base[i].p_file + '" alt="' + top_base[i].name + '" title="' + top_base[i].name + ' pointer"></div></a>';
                newblock.appendChild(div);
            }
        } catch (e) {
        }
    }
}