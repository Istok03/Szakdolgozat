@extends('layouts.admin')

@section('content')
<h1 class="mb-4">Kategóriák</h1>
<a href="{{ route('admin.categories.create') }}" class="btn btn-purple mb-4">+ Új kategória</a>

<div class="admin-table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Név</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-admin-light">Szerkesztés</a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-form" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Biztos törlöd?')" class="btn-admin-danger">Törlés</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
