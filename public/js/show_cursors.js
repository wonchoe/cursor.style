var cursors = '';

function remove(e) {
    var result = confirm("Удалить курсор?");
    if (result) {
        $.ajax({
            method: "POST",
            url: "/admin/cursors/delete",
            beforeSend: function () {
                showLoader();
            },
            data: {
                "_token": "{{ csrf_token() }}",
                "id": e.dataset.id,
                "c_file": e.dataset.cfile,
                "p_file": e.dataset.pfile
            },
            success: function (data) {
                if (data.result) {
                    e.parentNode.remove();
                } else
                    alert(data.message);
            }
        }).always(function () {
            hideLoader();
        });
    }
}

function createCard(title, id, st) {
    var card = '<div class="card mt-3 ' + st + '">\
  <div class="card-header" style="font-weight:600;">\
    ' + title + '\
  </div>\
  <div class="card-body d-flex flex-wrap" id="' + id + '">\
  </div>\
</div>';
    return card;
}

function createCursorBlock(id, name, c_file, p_file) {
    var cur = '<div class="crop"><h8 style="padding: 10px;">' + name + '</h8></div>\
    <div class="admin_cur" id="c_' + id + '" style="background-image: url(&quot;/resources/cursors/' + c_file + '&quot;);margin: 12px;"></div></div>\
    <button data-id="' + id + '" data-cfile="' + c_file + '" data-pfile="' + p_file + '" type="button" onClick="remove(this)" class="btn btn-light mt-1">Удалить</button>';
    return cur;
}

function sortResults(prop, asc) {
    cursors.sort(function (a, b) {
        if (asc) {
            return (a[prop] > b[prop]) ? 1 : ((a[prop] < b[prop]) ? -1 : 0);
        } else {
            return (b[prop] > a[prop]) ? 1 : ((b[prop] < a[prop]) ? -1 : 0);
        }
    });
}

$(function () {
       $.get('/admin/cursors/getAll', function (data) {
        cursors = data.data;

        sortResults('id', false);

        // LATEST
        cursors_container.innerHTML += (createCard('Последние загруженные', 'latest_cursors', ''));


        cursors.slice(0,9).forEach(function (e) {
            r = document.createElement('div');
            r.innerHTML = createCursorBlock(e.id, e.name, e.c_file, e.p_file);
            r.className = 'cursor_block';
            latest_cursors.appendChild(r);
        });

        // BY CATEGORIES
        sortResults('base_name', true);

        cur_cat = '';
        cursors.forEach(function (e) {
            if (cur_cat != e.alt_name) {
                cursors_container.innerHTML += (createCard(e.base_name, e.alt_name, 'bgr'));
                cur_cat = e.alt_name;
            }

            r = document.createElement('div');
            r.innerHTML = createCursorBlock(e.id, e.name, e.c_file);
            r.className = 'cursor_block';

            document.getElementById(e.alt_name).appendChild(r);
        });

    })
})