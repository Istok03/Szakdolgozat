@extends('layouts.app')

@section('content')
    <div class="contact-page">
        <h1>Kapcsolat</h1>
        <p>Vedd fel velünk a kapcsolatot az alábbi űrlapon vagy elérhetőségeinken keresztül.</p>

        <div class="contact-info">
            <p><strong>Email:</strong> info@istokstore.hu</p>
            <p><strong>Telefon:</strong> +36 30 123 4567</p>
            <p><strong>Cím:</strong> 3300 Eger, Példa utca 12.</p>
        </div>

        <div class="contact-form">
            <form action="#" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Név</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email cím</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="message">Üzenet</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>

                <button type="submit">Küldés</button>
            </form>
        </div>
    </div>
@endsection
