<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="container py-4">
    <h2 class="mb-4">Додати категорію</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" class="card p-4 shadow-sm">
        @csrf
        <div class="form-floating mb-3">
            <input type="text" name="base_name" class="form-control" id="base_name" placeholder="Назва (основна)" required>
            <label for="base_name">Назва (основна)</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" name="base_name_en" class="form-control" id="base_name_en" placeholder="Назва (EN)" required>
            <label for="base_name_en">Назва (EN)</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" name="base_name_es" class="form-control" id="base_name_es" placeholder="Назва (ES)" required>
            <label for="base_name_es">Назва (ES)</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" name="alt_name" class="form-control" id="alt_name" placeholder="Альт назва" required>
            <label for="alt_name">Альт назва</label>
        </div>
        <div class="form-floating mb-3">
            <input type="number" name="priority" class="form-control" id="priority" value="0">
            <label for="priority">Пріоритет</label>
        </div>
        <div class="form-floating mb-3">
            <input type="number" name="installed" class="form-control" id="installed" value="0">
            <label for="installed">Встановлено</label>
        </div>
        <div class="form-floating mb-3">
            <textarea name="short_descr" class="form-control" placeholder="Короткий опис" id="short_descr" style="height: 100px" required></textarea>
            <label for="short_descr">Короткий опис</label>
        </div>
        <div class="form-floating mb-3">
            <textarea name="description" class="form-control" placeholder="Опис" id="description" style="height: 100px" required></textarea>
            <label for="description">Опис</label>
        </div>
        <div class="mb-3">
            <label for="img" class="form-label">Зображення</label>
            <input class="form-control" type="file" id="img" name="img" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Додати</button>
    </form>
</div>