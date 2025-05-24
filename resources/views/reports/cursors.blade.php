@extends('adminlte::page')

@section('title', 'Cursors')
@section('content_header')
    <h1>Cursors</h1>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <form method="GET" action="{{ route('cursors.index') }}" class="d-flex align-items-center flex-wrap gap-2">
        <label class="me-2 mb-0 fw-bold" for="collection">Collection:</label>
        <select name="collection" id="collection"
                onchange="this.form.submit()"
                class="form-select form-select-sm"
                style="width:220px;max-width:100%;border-radius: .40rem;    width: 220px;max-width: 100%; border-radius: 0.4rem; padding: 5px; margin-left: 10px;    border: 1px solid #e2e2e2;">
            @foreach($collections as $coll)
                <option value="{{ $coll->id }}" {{ $coll->id == $collectionId ? 'selected' : '' }}>
                    {{ $coll->base_name_en }}
                </option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('cursors.create', ['collection' => $collectionId]) }}"
       class="btn btn-primary btn-sm shadow-sm ms-2">
        <i class="fas fa-plus"></i> Add Cursor
    </a>
</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
@if($cursors->isEmpty())
    <div class="alert alert-info">No cursors found for this collection.</div>
@else
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Preview</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($cursors as $cursor)
            <tr>
                <td>{{ $cursor->name_en }}</td>
                <td>
                    @if($cursor->c_file)
                        <img src="{{ asset( $cursor->c_file) }}" style="height:36px">
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('cursors.destroy', $cursor->id) }}" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this cursor?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $cursors->links('pagination::bootstrap-4') }}
    </div>
@endif
@endsection
