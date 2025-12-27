@extends('admin.layout')

@section('content')
<h1>Kategóriák</h1>
<a href="{{ route('admin.categories.create') }}" class="btn-add">+ Új kategória</a>

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
                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-edit">Szerkesztés</a>
                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-form">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('Biztos törlöd?')" class="btn-delete">Törlés</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
