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
    clearStyle();
    $('#offsetY').slider('setValue', 0);
    $('#offsetX').slider('setValue', 0);
}

$(function () {
    $('#upload_form').on('submit', function (event) {
        cat_input.value = categoriesList.value;
        event.preventDefault();

        $.ajax({
            url: "/admin/cursor/upload",
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

function uploadIcons() {
    cursor = imagePreviewCursor.style.backgroundImage.slice(4, -1).replace(/"/g, "");
    pointer = imagePreviewPointer.style.backgroundImage.slice(4, -1).replace(/"/g, "");

//    uploadedDB.uploaded.push({cursor: cursor, pointer: pointer, id: new Date().getTime()});
//    chrome.storage.local.set({
//        uploaded: uploadedDB.uploaded,
//    });
//    if (uploaded)
//        uploaded.remove();
//    reloadUploaded();
}


//cBtn.onclick = function (e) {
//    mainScreen.style.display = 'block';
//    uploadContainer.style.display = 'none';
//    document.body.className = 'popMain';
//};
//    
//
//saveUploadedBtn.onclick = function () {
//    uploadIcons();
//    mainScreen.style.display = 'block';
//    uploadContainer.style.display = 'none';
//}

function readURLCursor(input) {
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

                if (Math.round(input.files[0].size / 1024) > 1000) {
                    alert('Размер файла не должен превышать 1000 кб');
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

                $('#imagePreviewCursor').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreviewCursor').hide();
                $('#imagePreviewCursor').fadeIn(650);
            };

        }
        reader.readAsDataURL(input.files[0]);
    }
}

function readURLPointer(input) {
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

                if (Math.round(input.files[0].size / 1024) > 500) {
                    alert('Размер файла не должен превышать 500 кб');
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
                console.log(e.target.result);
                $('#imagePreviewPointer').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreviewPointer').hide();
                $('#imagePreviewPointer').fadeIn(650);
            };



        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#cursorUpload").change(function () {
    readURLCursor(this);
});

$("#pointerUpload").change(function () {
    readURLPointer(this);
});