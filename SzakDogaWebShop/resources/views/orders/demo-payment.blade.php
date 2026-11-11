@if($order->payment_method === 'demo_card')
    <div class="demo-payment-summary">
        <p><strong>Demófizetés:</strong> {{ $order->payment_status === 'demo_approved' ? 'Sikeres szimuláció' : 'Nincs rögzített eredmény' }}. Valódi pénzlevonás nem történt.</p>
        <p>Tesztkártya: {{ $order->card_brand }} •••• {{ $order->card_last_four }}</p>
        <p>Demóazonosító: <span style="overflow-wrap: anywhere;">{{ $order->payment_reference }}</span></p>
    </div>
@endif
