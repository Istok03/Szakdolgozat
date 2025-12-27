@extends('admin.layout')

@section('content')
<h1>Kategória szerkesztése</h1>

<form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Név</label>
    <input type="text" name="name" value="{{ $category->name }}" required>
    <button type="submit">Mentés</button>
</form>
@endsection
