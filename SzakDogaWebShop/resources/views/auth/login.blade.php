@extends('layouts.app')

@section('title', 'Bejelentkezés')

@section('content')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<div class="register-container">
    <div class="logo-container">
        <img id="logo" src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>

    <h2>Bejelentkezés</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">Email cím</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="text-danger small">{{ $message }}</div>
        @enderror

        <label for="password">Jelszó</label>
        <input type="password" name="password" id="password" required>
        @error('password')
            <div class="text-danger small">{{ $message }}</div>
        @enderror

        <div style="margin-top:12px; text-align:left;">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" style="display:inline;">Emlékezz rám</label>
        </div>

        <button type="submit" class="x-primary-button">Belépés</button>

        <a href="{{ route('password.request') }}">Elfelejtetted a jelszavad?</a>
    </form>
</div>
@endsection
