@extends('layouts.admin')

@section('content')
<div class="container py-4">

    <h1 class="fw-bold text-white mb-4">Felhasználó szerkesztése</h1>

    <div class="card border-0 shadow" style="background-color: #7c3aed; color: white;">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label text-white">Név</label>
                    <input type="text" name="name" id="name" class="form-control bg-light text-dark" value="{{ old('name', $user->name) }}">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label text-white">Email</label>
                    <input type="email" name="email" id="email" class="form-control bg-light text-dark" value="{{ old('email', $user->email) }}">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label text-white">Szerepkör</label>
                    <select name="role" id="role" class="form-select bg-light text-dark">
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Felhasználó</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn-admin-light">Vissza</a>
                    <button type="submit" class="btn-admin-light">Mentés</button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
