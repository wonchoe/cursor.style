<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/cursors.css" rel="stylesheet">
    <script src="/js/cursors.js" defer></script>
</head>

<body>


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
                        <input type="range" id="offsetY_slider" min="0" max="300" value="0" style="writing-mode: bt-lr; height: 100px;">
                    </div>
                </div>


                <div class="col-md-6 text-center">
                    <label class="form-label">Pointer Image</label>
                    <!-- ДЛЯ POINTER Image -->
                    <div class="position-relative d-inline-block" style="width: 300px; height: 300px;">
                        <div class="img-drop" id="pointerDrop">Click to select</div>
                        <div class="crosshair-x" id="pointerCrossX"></div>
                        <div class="crosshair-y" id="pointerCrossY"></div>
                        <input type="file" id="p_file" name="p_file" accept="image/*"  class="visually-hidden" required>
                    </div>

                    <div class="d-none">
                        <input type="range" id="offsetX_slider" min="0" max="300" value="0">
                        <input type="range" id="offsetY_slider" min="0" max="300" value="0" style="writing-mode: bt-lr; height: 100px;">
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