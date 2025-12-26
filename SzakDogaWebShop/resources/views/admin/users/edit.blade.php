@extends('admin.layout')

@section('content')
<div class="admin-container">
    <h1 class="admin-title">Felhasználó szerkesztése</h1>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="admin-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Név</label>
            <input type="text" name="name" value="{{ $user->name }}" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required>
        </div>

        <div class="form-group">
            <label>Szerepkör</label>
            <select name="is_admin">
                <option value="0" {{ $user->is_admin == 0 ? 'selected' : '' }}>Felhasználó</option>
                <option value="1" {{ $user->is_admin == 1 ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn-save">Mentés</button>
    </form>
</div>
@endsection
