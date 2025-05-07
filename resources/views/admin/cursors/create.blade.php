<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/cursors.css" rel="stylesheet">
    <script src="/js/cursors.js" defer></script>
    <style>
        .text-center {
            text-align: center !important;
            display: flex;
            flex-direction: column;
            align-content: center;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>

    @include('other.menu')

    <div class="container">
        <details>
            <summary>Collections</summary>
            <h4 class="mt-5">🖱️ Останні 50 курсорів</h4>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Назва</th>
                        <th>Категорія</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cursors as $cursor)
                        <tr class="cursor-row" data-id="{{ $cursor->id }}">
                            <td class="cursor-name" style="cursor: pointer;">
                                ▶ {{ $cursor->name }}
                            </td>
                            <td>{{ $cursor->cat }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-cursor">🗑 Видалити</button>
                            </td>
                        </tr>
                        <tr class="cursor-details" style="display: none;">
                            <td colspan="3">
                                <div class="details-box">
                                    <strong>ID:</strong> {{ $cursor->id }}<br>
                                    <strong>Назва:</strong> {{ $cursor->name_en }}<br>
                                    <strong>Файл курсора:</strong> {{ $cursor->c_file }}<br>
                                    <strong>Файл pointer:</strong> {{ $cursor->p_file }}<br>
                                    <strong>Превʼю курсора:</strong> {{ $cursor->c_file_prev }}<br>
                                    <strong>Превʼю pointer:</strong> {{ $cursor->p_file_prev }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </details>
    </div>

    <script>
        document.querySelectorAll('.cursor-name').forEach(nameCell => {
            nameCell.addEventListener('click', () => {
                const row = nameCell.closest('tr');
                const nextRow = row.nextElementSibling;
                if (nextRow && nextRow.classList.contains('cursor-details')) {
                    const isHidden = nextRow.style.display === 'none';
                    nextRow.style.display = isHidden ? 'table-row' : 'none';
                    nameCell.textContent = (isHidden ? '▼' : '▶') + ' ' + nameCell.textContent.slice(2);
                }
            });
        });

        document.querySelectorAll('.delete-cursor').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.closest('tr');
                const id = row.dataset.id;
                if (!confirm('Підтвердити видалення курсора?')) return;
                fetch(`/admin/cursors/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(resp => resp.json()).then(data => {
                    if (data.success) {
                        row.nextElementSibling.remove(); // details row
                        row.remove(); // main row
                    } else {
                        alert('Помилка при видаленні');
                    }
                });
            });
        });
    </script>

    <style>
        .details-box {
            background: #f9f9f9;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .cursor-name:hover {
            background-color: #f1f1f1;
        }
    </style>

 <!-- Updated create.blade.php for bulk upload -->
<div class="container py-4">
    <h2 class="mb-4">Bulk Create Cursors</h2>

    <!-- Mass upload input -->
    <div class="mb-3">
        <input type="file" id="bulkUpload" accept="image/*" multiple class="form-control">
    </div>

    <form method="POST" id="formId" action="{{ route('cursors.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="schedule" value="{{ date('Y-m-d') }}">

        <!-- Category selector -->
        <div class="mb-3">
            <label for="cat_select" class="form-label">Category</label>
            <select class="form-select" name="cat_select" id="cat_select">
                <option value="-1">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->base_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Where new cards will be appended -->
        <div id="bulkWrapper"></div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary">Submit All</button>
        </div>
    </form>
</div>

<script>
function setupImageDrop(dropId, inputId, offsetXId, offsetYId) {
    const drop = document.getElementById(dropId);
    const offsetXInput = document.getElementById(offsetXId);
    const offsetYInput = document.getElementById(offsetYId);

    drop.style.pointerEvents = 'auto';
    drop.addEventListener('click', (e) => {
        e.preventDefault();

        const rect = drop.getBoundingClientRect();
        const rawX = Math.round(e.clientX - rect.left);
        const rawY = Math.round(e.clientY - rect.top);

        const scale = 128 / 300;
        const scaledX = Math.round(rawX * scale);
        const scaledY = Math.round(rawY * scale);

        offsetXInput.value = scaledX;
        offsetYInput.value = scaledY;

        drop.querySelectorAll('.crosshair-x, .crosshair-y').forEach(el => el.remove());

        const crossX = document.createElement('div');
        crossX.className = 'crosshair-x';
        crossX.style.left = `${rawX}px`;
        drop.appendChild(crossX);

        const crossY = document.createElement('div');
        crossY.className = 'crosshair-y';
        crossY.style.top = `${rawY}px`;
        drop.appendChild(crossY);
    });
}

document.getElementById('formId').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData();

    const category = document.getElementById('cat_select').value;
    if (category === '-1') {
        alert('Please select a category.');
        return;
    }

    formData.append('schedule', form.querySelector('[name="schedule"]').value);

    const blocks = document.querySelectorAll('#bulkWrapper .card');
    if (blocks.length === 0) {
        alert('No cursor blocks to submit.');
        return;
    }

    blocks.forEach((block, index) => {
        formData.append(`name[]`, block.querySelector(`[name="name[]"]`).value);
        formData.append(`cat_id[]`, category);

        formData.append(`offsetX[]`, block.querySelector(`[name="offsetX[]"]`).value);
        formData.append(`offsetY[]`, block.querySelector(`[name="offsetY[]"]`).value);
        formData.append(`offsetX_p[]`, block.querySelector(`[name="offsetX_p[]"]`).value);
        formData.append(`offsetY_p[]`, block.querySelector(`[name="offsetY_p[]"]`).value);

        formData.append(`c_file[]`, block.querySelector(`[name="c_file[]"]`).files[0]);
        formData.append(`p_file[]`, block.querySelector(`[name="p_file[]"]`).files[0]);
    });

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
        },
    })
    .then(response => response.ok ? location.reload() : response.text().then(text => alert(text)))
    .catch(err => {
        console.error(err);
        alert('Upload failed');
    });
});


// Bulk upload logic (unchanged)
document.getElementById('bulkUpload').addEventListener('change', function (e) {
    const files = Array.from(e.target.files);
    const container = document.getElementById('bulkWrapper');
    const selectedCat = document.getElementById('cat_select').value;
    container.innerHTML = '';

    for (let i = 0; i < files.length; i += 2) {
        const cursorFile = files[i];
        const pointerFile = files[i + 1];
        if (!cursorFile || !pointerFile) continue;

        const idSuffix = 'bulk_' + (i / 2);
        const rawName = cursorFile.name.split('.')[0];
        const match = rawName.match(/cursor-(.+)$/i);
        const parsedName = match && match[1] ? match[1] : rawName;

        const html = `
        <div class="card p-3 mb-3 shadow-sm">
            <input type="text" name="name[]" id="name_${idSuffix}" class="form-control mb-2" value="${parsedName}" placeholder="Name" required>
            <input type="hidden" name="cat_id[]" value="${selectedCat}">
            <div class="d-flex gap-3">
                <div class="position-relative" style="width:300px;height:300px;">
                    <div class="img-drop" id="cursorDrop_${idSuffix}"></div>
                    <input type="hidden" id="offsetX_${idSuffix}" name="offsetX[]" value="0">
                    <input type="hidden" id="offsetY_${idSuffix}" name="offsetY[]" value="0">
                </div>
                <div class="position-relative" style="width:300px;height:300px;">
                    <div class="img-drop" id="pointerDrop_${idSuffix}"></div>
                    <input type="hidden" id="offsetX_p_${idSuffix}" name="offsetX_p[]" value="0">
                    <input type="hidden" id="offsetY_p_${idSuffix}" name="offsetY_p[]" value="0">
                </div>
            </div>
            <input type="file" name="c_file[]" class="d-none" id="c_file_${idSuffix}">
            <input type="file" name="p_file[]" class="d-none" id="p_file_${idSuffix}">
        </div>`;

        container.insertAdjacentHTML('beforeend', html);

        const cursorDrop = document.getElementById(`cursorDrop_${idSuffix}`);
        const pointerDrop = document.getElementById(`pointerDrop_${idSuffix}`);

        const cursorInput = document.getElementById(`c_file_${idSuffix}`);
        const pointerInput = document.getElementById(`p_file_${idSuffix}`);

        const cursorReader = new FileReader();
        cursorReader.onload = (event) => {
            cursorDrop.style.backgroundImage = `url('${event.target.result}')`;
        };
        cursorReader.readAsDataURL(cursorFile);

        const pointerReader = new FileReader();
        pointerReader.onload = (event) => {
            pointerDrop.style.backgroundImage = `url('${event.target.result}')`;
        };
        pointerReader.readAsDataURL(pointerFile);

        const cursorDT = new DataTransfer();
        cursorDT.items.add(cursorFile);
        cursorInput.files = cursorDT.files;

        const pointerDT = new DataTransfer();
        pointerDT.items.add(pointerFile);
        pointerInput.files = pointerDT.files;

        setupImageDrop(`cursorDrop_${idSuffix}`, `c_file_${idSuffix}`, `offsetX_${idSuffix}`, `offsetY_${idSuffix}`);
        setupImageDrop(`pointerDrop_${idSuffix}`, `p_file_${idSuffix}`, `offsetX_p_${idSuffix}`, `offsetY_p_${idSuffix}`);
    }
});
</script>

<style>
.crosshair-x {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 1px;
    background: red;
    z-index: 10;
}
.crosshair-y {
    position: absolute;
    left: 0;
    right: 0;
    height: 1px;
    background: red;
    z-index: 10;
}
.img-drop {
    background-color: #eee;
    border: 1px dashed #ccc;
    cursor: crosshair;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
</style>

</body>

</html>