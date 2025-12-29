@extends('layouts.admin')

@section('content')
<div class="container py-4">

    <h1 class="fw-bold text-white mb-4 text-center">Rendelés szerkesztése #{{ $order->id }}</h1>

    <div class="card border-0 shadow mx-auto" style="background-color: #7c3aed; color: white; max-width: 720px;">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="form-label text-white">Név</label>
                    <input type="text" name="name" id="name" class="form-control bg-light text-dark" value="{{ old('name', $order->name) }}">
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label text-white">Email</label>
                    <input type="email" name="email" id="email" class="form-control bg-light text-dark" value="{{ old('email', $order->email) }}">
                </div>

                <div class="mb-4">
                    <label for="address" class="form-label text-white">Cím</label>
                    <input type="text" name="address" id="address" class="form-control bg-light text-dark" value="{{ old('address', $order->address) }}">
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label text-white">Státusz</label>
                    <select name="status" id="status" class="form-select bg-light text-dark">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Feldolgozás alatt</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Kiszállítva</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Teljesítve</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Törölve</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.orders.index') }}" class="btn-admin-light">Vissza</a>
                    <button type="submit" class="btn-admin-light">Mentés</button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
