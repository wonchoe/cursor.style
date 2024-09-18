function showLoader() {
    loader.style.opacity = 0.6;
    loader.style.visibility = 'visible';
}

function hideLoader() {
    loader.style.opacity = 0;
    loader.style.visibility = 'hidden';
}

function clearForm() {
    $('#upload_form').trigger("reset");
    imagePreviewCursor.style.backgroundImage = 'url(/images/cursor.png)';
    imagePreviewPointer.style.backgroundImage = 'url(/images/pointer.png)';
    imagePreviewCursor_prev.style.backgroundImage = 'url(/images/cursor.png)';
    imagePreviewPointer_prev.style.backgroundImage = 'url(/images/pointer.png)';    
    clearStyle();
    $('#offsetY').slider('setValue', 0);
    $('#offsetX').slider('setValue', 0);
}

$(function () {
    $('#upload_form').on('submit', function (event) {
        event.preventDefault();

        $.ajax({
            url: "/admin/animated/upload",
            method: "POST",
            data: new FormData(this),
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                loader.style.opacity = 0.6;
                loader.style.visibility = 'visible';
            },
            success: function (data)
            {
                c_name.classList.remove('is-invalid');
                errors = data.message;
                error_message = '';
                for (var value in errors) {
                    if (value == 'c_name') {
                        c_name.classList.add('is-invalid');
                    }
                    error_message = error_message + errors[value] + "\n";
                }
                if (error_message.length > 0) {
                    alert(error_message);
                    return;
                }
                clearForm();
                console.log(data);
            }
        }).always(function () {
            loader.style.opacity = 0;
            loader.style.visibility = 'hidden';
        });

    });
});

$("#offsetX").slider({
    min: 0,
    max: 100,
    value: 0,
    tooltip_position: 'bottom'
});

$("#offsetY").slider({
    min: 0,
    max: 100,
    value: 0,
    tooltip_position: 'bottom'
});

$("#cursorW").slider({
    min: 24,
    max: 64,
    value: 0,
    tooltip_position: 'bottom'
});

$('#imagePreviewCursor').css('background-image', 'url(/images/cursor.png)');
$('#imagePreviewPointer').css('background-image', 'url(/images/pointer.png)');

$('#imagePreviewCursor_prev').css('background-image', 'url(/images/cursor.png)');
$('#imagePreviewPointer_prev').css('background-image', 'url(/images/pointer.png)');

function uploadIcons() {
    cursor = imagePreviewCursor.style.backgroundImage.slice(4, -1).replace(/"/g, "");
    pointer = imagePreviewPointer.style.backgroundImage.slice(4, -1).replace(/"/g, "");
    cursor_prev = imagePreviewCursor_prev.style.backgroundImage.slice(4, -1).replace(/"/g, "");
    pointer_prev = imagePreviewPointer_prev.style.backgroundImage.slice(4, -1).replace(/"/g, "");    
}



function readURLCursor(input, pointer) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {

            var image = new Image();
            image.src = e.target.result;

            image.onload = function () {
                if (this.width !== this.height) {
                    alert('Убедитесь в том что курсор имеет квадратную форму!');
                    return;
                }


                if (this.width > 350) {
                    alert('Иконка не должна превышать 128px');
                    return;
                }

                if (this.width < 16) {
                    alert('Иконка не должна быть меньше 16px');
                    return;
                }

                $('#'+pointer).css('background-image', 'url(' + e.target.result + ')');
                $('#'+pointer).hide();
                $('#'+pointer).fadeIn(650);
            };

        }
        reader.readAsDataURL(input.files[0]);
    }
}


$("#cursorUpload").change(function () {
    readURLCursor(this, 'imagePreviewCursor');
});


$("#cursorUpload_prev").change(function () {
    readURLCursor(this, 'imagePreviewCursor_prev');
});

$("#pointerUpload").change(function () {
    readURLCursor(this, 'imagePreviewPointer');
});

$("#pointerUpload_prev").change(function () {
    readURLCursor(this, 'imagePreviewPointer_prev');    
});

