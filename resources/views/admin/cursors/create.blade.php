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

    <div class="container py-4">
        <h2 class="mb-4">Create Cursor</h2>

        <form method="POST" id="formId" action="{{ route('cursors.store') }}" enctype="multipart/form-data"
            class="card p-4 shadow-sm">
            @csrf

            <div class="form-floating mb-3">
                <input type="text" name="name" class="form-control" id="name" placeholder="Name" required>
                <label for="name">Name (Default)</label>
            </div>

            <div class="mb-3">
                <label for="cat" class="form-label">Category</label>
                <select class="form-select" name="cat_select" id="cat_select">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->base_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row justify-content-center mb-3">
                <div class="col-md-6 text-center">
                    <label class="form-label">Cursor Image</label>
                    <div class="position-relative d-inline-block" style="width: 300px; height: 300px;">
                        <div class="img-drop" id="cursorDrop">Click to select</div>
                        <div class="crosshair-x" id="cursorCrossX"></div>
                        <div class="crosshair-y" id="cursorCrossY"></div>
                        <input type="file" id="c_file" name="c_file" accept="image/*" class="visually-hidden" required>
                    </div>

                    <div class="d-none">
                        <input type="range" id="offsetX_slider" min="0" max="300" value="0">
                        <input type="range" id="offsetY_slider" min="0" max="300" value="0"
                            style="writing-mode: bt-lr; height: 100px;">
                    </div>
                </div>


                <div class="col-md-6 text-center">
                    <label class="form-label">Pointer Image</label>
                    <!-- ДЛЯ POINTER Image -->
                    <div class="position-relative d-inline-block" style="width: 300px; height: 300px;">
                        <div class="img-drop" id="pointerDrop">Click to select</div>
                        <div class="crosshair-x" id="pointerCrossX"></div>
                        <div class="crosshair-y" id="pointerCrossY"></div>
                        <input type="file" id="p_file" name="p_file" accept="image/*" class="visually-hidden" required>
                    </div>

                    <div class="d-none">
                        <input type="range" id="offsetX_slider" min="0" max="300" value="0">
                        <input type="range" id="offsetY_slider" min="0" max="300" value="0"
                            style="writing-mode: bt-lr; height: 100px;">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="offsetX" class="form-label">Offset X (Cursor)</label>
                    <input type="number" name="offsetX" id="offsetX" class="form-control" value="0">
                </div>
                <div class="col">
                    <label for="offsetY" class="form-label">Offset Y (Cursor)</label>
                    <input type="number" name="offsetY" id="offsetY" class="form-control" value="0">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="offsetX_p" class="form-label">Offset X (Pointer)</label>
                    <input type="number" name="offsetX_p" id="offsetX_p" class="form-control" value="0">
                </div>
                <div class="col">
                    <label for="offsetY_p" class="form-label">Offset Y (Pointer)</label>
                    <input type="number" name="offsetY_p" id="offsetY_p" class="form-control" value="0">
                </div>
            </div>

            <div class="mb-3">
                <label for="schedule" class="form-label">Schedule Date</label>
                <input type="date" name="schedule" id="schedule" class="form-control" value="{{ date('Y-m-d') }}">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Add Cursor</button>
            </div>
        </form>
    </div>
</body>

</html>