@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Admin felület</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Termékek</div>
                <div class="card-body">
                    <p class="card-text">Termékek kezelése, létrehozás, szerkesztés, törlés.</p>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light">Megnyitás</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Rendelések</div>
                <div class="card-body">
                    <p class="card-text">Rendelések áttekintése és státusz kezelése.</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light">Megnyitás</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-dark mb-3">
                <div class="card-header">Felhasználók</div>
                <div class="card-body">
                    <p class="card-text">Felhasználói jogosultságok és adatok kezelése.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Megnyitás</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
