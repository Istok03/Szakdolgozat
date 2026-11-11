document.addEventListener('DOMContentLoaded', () => {
    const cardPanel = document.getElementById('demo-card-fields');
    const updatePayment = () => {
        if (!cardPanel) return;
        const active = document.querySelector('input[name="payment_method"]:checked')?.value === 'demo_card';
        cardPanel.hidden = !active;
        cardPanel.disabled = !active;
        cardPanel.querySelectorAll('input').forEach(input => {
            input.required = active;
            if (!active) input.value = '';
        });
    };
    document.querySelectorAll('input[name="payment_method"]').forEach(option => option.addEventListener('change', updatePayment));
    updatePayment();
    window.addEventListener('pageshow', updatePayment);
    const address = document.getElementById('address');
    const updateAddress = () => {
        if (!address) return;
        const pickup = document.querySelector('input[name="shipping_method"]:checked')?.value === 'pickup';
        address.required = !pickup;
        address.disabled = pickup;
        address.closest('.form-group').hidden = pickup;
    };
    document.querySelectorAll('input[name="shipping_method"]').forEach(option => option.addEventListener('change', updateAddress));
    updateAddress();
    window.addEventListener('pageshow', updateAddress);
    const total = document.getElementById('checkout-grand-total');
    const fee = document.getElementById('shipping-fee');
    if (!total || !fee) return;
    const money = value => Number(value).toLocaleString('hu-HU', { maximumFractionDigits: 0 });
    document.querySelectorAll('input[name="shipping_method"]').forEach(option => {
        option.addEventListener('change', () => {
            if (!option.checked) return;
            fee.textContent = money(option.dataset.fee);
            total.textContent = money(Number(total.dataset.subtotal) + Number(option.dataset.fee));
        });
    });
});
