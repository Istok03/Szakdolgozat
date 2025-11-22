@extends('layouts.app')

@section('title', 'Bejelentkezés')

@section('content')
<link rel="stylesheet" href="Login.css">
<div class="card border-0 shadow-sm bg-dark text-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm bg-dark text-light">
                <div class="card-body">
                    <h4 class="mb-4 text-center text-purple">Bejelentkezés</h4>


                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label text-light">Email cím</label>
                            <input type="email" name="email" class="form-control bg-dark text-light border-purple" required autofocus>
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-light">Jelszó</label>
                            <input type="password" name="password" class="form-control bg-dark text-light border-purple" required>
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Emlékezz rám</label>
                        </div>

                        <button type="submit" class="btn btn-purple w-100">Belépés</button>

                        <div class="mt-3 text-center">
                            <a href="{{ route('password.request') }}" class="text-purple">Elfelejtetted a jelszavad?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
