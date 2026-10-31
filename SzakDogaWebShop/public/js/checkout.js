document.addEventListener('DOMContentLoaded', () => {
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
