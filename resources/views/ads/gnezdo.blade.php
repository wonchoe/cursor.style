<script src='https://news.2xclick.ru/loader.js' async></script>
<div id='containerId20238' style="opacity: 0; pointer-events: none; position: absolute;"></div>
<script>
    var all_ads = [];
    (function (w, d, c, s, t) {
        w[c] = w[c] || [];
        w[c].push(function () {
            gnezdo.create({
                tizerId: 20238,
                containerId: 'containerId20238'
            });
            gnezdo._imgLoaded = function (e) {
                addAds();
            }
        });
    })(window, document, 'gnezdoAsyncCallbacks');

    function _ad_close(obj) {
        if (!localStorage.hasOwnProperty('_ad_excludes'))
            localStorage.setItem('_ad_excludes', JSON.stringify([]));
        _ad_excludes = JSON.parse(localStorage.getItem('_ad_excludes'));
        _ad_excludes.push(obj.dataset.adid);
        localStorage.setItem('_ad_excludes', JSON.stringify(_ad_excludes));
        obj.parentNode.parentNode.style.display = 'none';
    }

    function createBlock() {
        if (!localStorage.hasOwnProperty('_ad_excludes'))
            localStorage.setItem('_ad_excludes', JSON.stringify([]));
        _ad_excludes = JSON.parse(localStorage.getItem('_ad_excludes'));
        _ad_excludes.forEach(function (_ad_el) {
            if (_ad_el in all_ads) {
                delete all_ads[_ad_el];
            }
        });

        if (Object.keys(all_ads).length < 1)
            return false;

        // FORMATING BLOCK
        _ad_keys = Object.keys(all_ads)
        _ad_block_id = _ad_keys[Math.floor(Math.random() * _ad_keys.length)];
        _ad_chosen = all_ads[_ad_block_id];

        _ad_container = document.createElement('div');
        _ad_container.id = 'ad_cont';
        _ad_container.setAttribute('style', 'margin-top: 23px;text-align:left;display:flex; flex-direction: column;font-size: 16px;width:100%;justify-content: center; align-items: center;');
        _ad_container.innerHTML = '<div style="position: relative;width: 100%;"><div onclick="window.open(\'https://gnezdo.ru\');" style="cursor: pointer;position: absolute;right: 14px;top: -21px;background: #ffffff;color: #7c9b80;font-size: 18px;width: 20px;height: 21px;line-height: 1.3;" https:="">🛈</div><div onclick="_ad_close(this)" style="cursor: pointer;position: absolute;right: -5px;top: -21px;background: #ffffff;color: #7c9b80;font-size: 18px;width: 20px;height: 21px;" data-adid="'+_ad_block_id+'">×</div><div style="position: relative;"></div>\
    <div style="font-size: 14px; font-family: sans-serif; font-weight: 500;text-align:left;margin-left: 11px; padding-bottom: 7px;">' + _ad_chosen.text + '</div>\
    <a style="width: 95%; margin: 0 auto;border: 1px solid silver;display: flex; flex-direction: column;color: #424242;text-decoration: none;" target="_blank" href="' + _ad_chosen.link + '">\
    <img style="width: 79%; margin-left: 10px;margin: 0 auto;" src="' + _ad_chosen.img + '"/></a>\
    <div style="position: relative;"><a href="' + _ad_chosen.link + '" target="_blank" style="text-align: center;margin: 6px;\
    border-radius: 2px; padding: 7px 14px 7px 14px; background: #6889a9; font-size: 13px; font-family: sans-serif; color: white;font-weight: 100; text-decoration: none; right: 0px; position: absolute; margin-top: 12px;\
    ">Подробно...</a></div>\
    </a>';
        return _ad_container;
    }

    function addAds() {
        if (!localStorage.hasOwnProperty('_ad_excludes'))
            localStorage.setItem('_ad_excludes', JSON.stringify([]));
        _ad_excludes = JSON.parse(localStorage.getItem('_ad_excludes'));

        if (document.querySelector('iframe')) {
            cs_all = document.querySelector('iframe').contentWindow.document.querySelectorAll('.gnezdo_cell');
            cs_all.forEach(function (el) {
                _ad_img = el.querySelector('img').src;
                _ad_link = el.querySelector('a').href;
                _ad_text = el.textContent;
                id = _ad_link.substr(0, _ad_link.search('token')).split('/');
                _ad_id = id[id.length - 2];
                if ((!(_ad_id in all_ads)) && (!(_ad_id in _ad_excludes))) {
                    all_ads[_ad_id] = {
                        img: _ad_img,
                        link: _ad_link,
                        text: _ad_text
                    };
                }
            });
            showAds();
        }
        setTimeout(addAds, 300);
    }

    function showAds() {
        if (document.querySelector('#ad_cont'))
            return;
        div = createBlock();
        if (div) {
            document.body.appendChild(div);
        }
        setTimeout(showAds, 300);
    }

    window.onload = function () {
        console.log(document);
    }

</script>