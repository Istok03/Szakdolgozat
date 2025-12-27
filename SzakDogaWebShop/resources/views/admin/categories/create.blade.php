@extends('admin.layout')

@section('content')
<h1>Új kategória létrehozása</h1>

<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf
    <label>Név</label>
    <input type="text" name="name" required>
    <button type="submit">Létrehozás</button>
</form>
@endsection
