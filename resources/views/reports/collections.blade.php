@extends('adminlte::page')

@section('title', 'Collections')
@section('content_header')
    <h1>Collections</h1>
@endsection

@section('content')
    {{-- КНОПКА ДОДАТИ --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('collections.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Collection
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- СПИСОК КОЛЕКЦІЙ --}}
    <div id="collectionsList">
        <div class="row">
            @foreach($collections as $collection)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card collection-card"
                         onclick="location.href='/admin/cursors?collection={{ $collection->id }}';">
                        <div class="collection-header">
                            <div class="collection-title">{{ $collection->base_name_en }}</div>
                            <button class="btn btn-sm collection-delete"
                                    onclick="event.stopPropagation(); deleteCollection({{ $collection->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @if($collection->img)
                            <img src="{{ asset($collection->img) }}"
                                 class="collection-img"
                                 alt="{{ $collection->base_name_en }}">
                        @else
                            <div class="collection-img d-flex align-items-center justify-content-center bg-light">
                                <span class="text-muted">No Image</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $collections->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <style>
        .collection-card {
            position: relative;
            transition: box-shadow .18s;
            cursor: pointer;
            min-width: 280px;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: 100%;
        }
        .collection-card:hover {
            box-shadow: 0 6px 24px #00308022;
            background: #f8fafb;
        }
        .collection-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 13px 15px 10px 15px;
        }
        .collection-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #181d23;
            margin-bottom: 0;
            margin-right: 10px;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 75%;
        }
        .collection-delete {
            border: 1.2px solid #ee5757;
            color: #ee5757;
            background: #fff;
            border-radius: 7px;
            padding: 4px 8px;
            transition: background 0.18s;
        }
        .collection-delete:hover {
            background: #ffe5e5;
        }
        .collection-img {
            width: 100%;
            max-height: 175px;
            padding: 0 15px 15px 15px;
            object-fit: contain;
            border-radius: 0 0 10px 10px;
        }
        .card.collection-card {
            min-height: 220px;
            height: auto;
        }
    </style>
@endsection

@push('js')
<script>
function deleteCollection(id) {
    if (!confirm('Підтвердити видалення?')) return;
    fetch(`/admin/collections/delete/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    }).then(resp => resp.json()).then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Помилка при видаленні');
        }
    });
}
</script>
@endpush
