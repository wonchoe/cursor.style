@extends('adminlte::page')

@section('title', 'Add Collection')
@section('content_header')
    <h1>Add Collection</h1>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('collections.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" name="base_name" class="form-control" id="base_name" placeholder="Назва (основна)" required value="{{ old('base_name') }}">
                    <label for="base_name">Назва (основна)</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="alt_name" class="form-control" id="alt_name" placeholder="Альт назва" required value="{{ old('alt_name') }}">
                    <label for="alt_name">Альт назва</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="priority" class="form-control" id="priority" value="{{ old('priority', 0) }}">
                    <label for="priority">Пріоритет</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="installed" class="form-control" id="installed" value="{{ old('installed', 0) }}">
                    <label for="installed">Встановлено</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea name="short_descr" class="form-control" placeholder="Короткий опис" id="short_descr" style="height: 80px" required>{{ old('short_descr') }}</textarea>
                    <label for="short_descr">Короткий опис</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea name="description" class="form-control" placeholder="Опис" id="description" style="height: 120px" required>{{ old('description') }}</textarea>
                    <label for="description">Опис</label>
                </div>
                <div class="mb-3">
                    <label for="img" class="form-label">Зображення</label>
                    <input class="form-control" type="file" id="img" name="img" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Додати</button>
                <a href="{{ route('collections.index') }}" class="btn btn-link mt-2">Назад до списку</a>
            </form>
        </div>
    </div>
</div>
@endsection
