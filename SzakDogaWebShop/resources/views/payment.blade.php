@extends('layouts.app')

@section('content')
    <div class="payment-page">
        <h1>Fizetés</h1>
        <p>Kérjük, add meg a számlázási és fizetési adataidat.</p>

        <div class="payment-container">
            
            <div class="billing-info">
                <h2>Számlázási adatok</h2>
                <form action="#" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Teljes név</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email cím</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Cím</label>
                        <input type="text" id="address" name="address" required>
                    </div>

                    <div class="form-group">
                        <label for="city">Város</label>
                        <input type="text" id="city" name="city" required>
                    </div>

                    <div class="form-group">
                        <label for="zip">Irányítószám</label>
                        <input type="text" id="zip" name="zip" required>
                    </div>
                </form>
            </div>

        
            <div class="payment-method">
                <h2>Fizetési mód</h2>
                <form>
                    <div class="form-group">
                        <input type="radio" id="card" name="payment" checked>
                        <label for="card">Bankkártya</label>
                    </div>
                    <div class="form-group">
                        <input type="radio" id="paypal" name="payment">
                        <label for="paypal">PayPal</label>
                    </div>
                    <div class="form-group">
                        <input type="radio" id="cash" name="payment">
                        <label for="cash">Utánvét</label>
                    </div>
                </form>
            </div>

            <!-- Rendelés összegzés -->
            <div class="order-summary">
                <h2>Rendelés összegzése</h2>
                <ul>
                    <li>Gaming Laptop – 299 000 Ft</li>
                    <li>RGB Egér – 9 990 Ft</li>
                </ul>
                <h3>Összesen: 308 990 Ft</h3>
                <button class="pay-btn">Fizetés</button>
            </div>
        </div>
    </div>
@endsection
