document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('cart-container');
    const notification = document.getElementById('cart-notification');
    let pending = false;
    let notificationTimer;
    const money = value => Number(value).toLocaleString('hu-HU', { maximumFractionDigits: 2 });
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[char]);

    function notify(message) {
        if (!notification) return;
        clearTimeout(notificationTimer);
        notification.textContent = message;
        notification.classList.add('show');
        notificationTimer = setTimeout(() => notification.classList.remove('show'), 4000);
    }

    function renderCart(cart, total) {
        const items = Object.values(cart);
        if (container) {
            if (!items.length) {
                container.innerHTML = '<p class="empty-cart">A kosár üres.</p>';
            } else {
                const rows = items.map(item => {
                    const id = Number(item.product_id);
                    const price = Math.round(Number(item.price) * (1 - Number(item.discount ?? 0) / 100) * 100) / 100;
                    return `<tr>
                        <td><img src="/${escapeHtml(item.image || 'images/products/default.png')}" width="80" alt="${escapeHtml(item.name)}"></td>
                        <td>${escapeHtml(item.name)}</td>
                        <td>
                            <button class="cart-action" data-id="${id}" data-action="decrease" aria-label="Mennyiség csökkentése" ${item.quantity <= 1 ? 'disabled' : ''}>−</button>
                            <span>${Number(item.quantity)}</span>
                            <button class="cart-action" data-id="${id}" data-action="increase" aria-label="Mennyiség növelése">+</button>
                        </td>
                        <td>${money(price * item.quantity)} Ft</td>
                        <td><button class="cart-action" data-id="${id}" data-action="remove">Törlés</button></td>
                    </tr>`;
                }).join('');
                container.innerHTML = `<table class="cart-table">
                    <thead><tr><th>Kép</th><th>Név</th><th>Mennyiség</th><th>Ár</th><th>Művelet</th></tr></thead>
                    <tbody>${rows}</tbody></table>
                    <div class="cart-total"><h3>Összesen: ${money(total)} Ft</h3></div>`;
            }
        }
        const checkout = document.querySelector('.checkout-button-container');
        if (checkout) checkout.hidden = items.length === 0;
        document.querySelectorAll('.cart-count').forEach(badge => {
            const quantity = items.reduce((sum, item) => sum + Number(item.quantity), 0);
            badge.textContent = quantity;
            badge.hidden = quantity === 0;
        });
    }

    document.body.addEventListener('click', async event => {
        const button = event.target.closest('.cart-action, .cart-btn');
        if (!button) return;
        event.preventDefault();
        if (pending) return;
        const action = button.classList.contains('cart-btn') ? 'add' : button.dataset.action;
        const id = Number(button.dataset.id);
        if (!Number.isInteger(id) || id < 1 || !['add', 'increase', 'decrease', 'remove'].includes(action)) return;
        pending = true;
        button.disabled = true;
        try {
            const response = await fetch(`/cart/${action}/${id}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (!response.ok) throw new Error('A kosár módosítása nem sikerült. Frissítsd az oldalt, és próbáld újra.');
            const data = await response.json();
            renderCart(data.cart, data.total);
            if (action === 'add') notify('Sikeresen hozzáadva a kosárhoz.');
        } catch (error) {
            notify(error.message || 'Hálózati hiba történt. Próbáld újra.');
        } finally {
            pending = false;
            button.disabled = false;
        }
    });
});