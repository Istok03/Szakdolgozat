@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile-page" lang="hu">
    <header class="profile-heading">
        <div>
            <h1>Profilom</h1>
            <p>Itt módosíthatod a személyes adataidat és a jelszavadat.</p>
        </div>
        <a href="{{ route('products') }}" class="profile-back">Vissza a vásárláshoz</a>
    </header>
    <div class="profile-grid">
        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
    </div>
    @include('profile.partials.delete-user-form')
</div>
@endsection