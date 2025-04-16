$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function hideLoader() {
    document.body.style.overflow = 'scroll';
    loader.style.display = 'none';
}

function showLoader() {
    document.body.style.overflow = 'none';
    loader.style.display = 'block';
}

$('#feedback').on('submit', function (event) {
    event.preventDefault();

    $.ajax({
        url: "/feedback",
        method: "POST",
        data: new FormData(this),
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            showLoader();
            inp = document.getElementsByTagName('input');
            message.className = 'input1 textarea_f';
            for (var k in inp) {
                if (inp[k].id) {
                    if (inp[k].classList.contains('invalid')) {
                        inp[k].classList.remove('invalid');
                    }
                }
            }
        },
        success: function (data)
        {    
            hideLoader();
            for (var k in data) {
                if (document.getElementById(k)) document.getElementById(k).classList.add('invalid');
            } 
            if (data.result){
                contact_form.style.display = 'none';
                thanks_form.style.display = 'block';
            }
        }
    }).always(function () {
        hideLoader();
    });

});