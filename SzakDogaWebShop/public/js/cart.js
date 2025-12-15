document.addEventListener('DOMContentLoaded', function () {
    const cartContainer = document.querySelector('#cart-container');

    if (cartContainer) {
        cartContainer.addEventListener('click', function (e) {
            const button = e.target.closest('.cart-action');
            if (!button) return;

            e.preventDefault();
            const id = button.getAttribute('data-id');
            const action = button.getAttribute('data-action');

            // Ne engedje 1 alá csökkenteni
            if (action === 'decrease') {
                const qtyElement = document.querySelector(`#qty-${id}`);
                if (qtyElement) {
                    const currentQty = parseInt(qtyElement.innerText, 10);
                    if (currentQty <= 1) {
                        console.log("Nem lehet 1 alá csökkenteni!");
                        return;
                    }
                }
            }

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`/cart/${action}/${id}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Frissítjük a kosár tartalmat
                renderCart(data.cart, data.total);
            })
            .catch(error => {
                console.error("Hiba a kosár műveletnél:", error);
            });
        });
    }

    function renderCart(cart, total) {
        let html = '';
        if (Object.keys(cart).length > 0) {
            html += `
                <table class="cart-table" id="cart-container">
                    <thead>
                        <tr>
                            <th>Kép</th>
                            <th>Név</th>
                            <th>Mennyiség</th>
                            <th>Ár</th>
                            <th>Művelet</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            for (const id in cart) {
                const item = cart[id];
                const price = item.price * (1 - (item.discount ?? 0) / 100) * item.quantity;
                html += `
                    <tr data-id="${id}">
                        <td><img src="/${item.image}" width="80" alt="${item.name}"></td>
                        <td>${item.name}</td>
                        <td>
                            <button class="cart-action" data-id="${id}" data-action="decrease">−</button>
                            <span id="qty-${id}">${item.quantity}</span>
                            <button class="cart-action" data-id="${id}" data-action="increase">+</button>
                        </td>
                        <td>${price.toLocaleString('hu-HU')} Ft</td>
                        <td>
                            <button class="cart-action" data-id="${id}" data-action="remove">❌ Törlés</button>
                        </td>
                    </tr>
                `;
            }
            html += `
                    </tbody>
                </table>
                <div class="cart-total">
                    <h3>Összesen: <span id="cart-total">${total.toLocaleString('hu-HU')}</span> Ft</h3>
                </div>
            `;
        } else {
            html = `<p class="empty-cart">A kosár üres.</p>`;
        }

        document.querySelector('.cart-table').parentElement.innerHTML = html;
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // Globális delegáció: bármely oldal, bármely gomb
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.cart-btn');
        if (!button) return;

        e.preventDefault();
        const productId = button.getAttribute('data-id');
        if (!productId) {
            console.warn('cart-btn: hiányzik a data-id');
            return;
        }

        const formData = new FormData();
        const csrf = document.querySelector('meta[name="csrf-token"]');
        if (!csrf) {
            console.error('Hiányzik a CSRF meta tag a layout head-ben.');
            return;
        }
        formData.append('_token', csrf.getAttribute('content'));

        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            // Kosár jelző frissítése (ha van)
            const cartCount = document.querySelector('.cart-count');
            if (cartCount && data && typeof data === 'object' && 'cart' in data) {
                const totalQty = Object.values(data.cart)
                    .reduce((sum, item) => sum + (item.quantity || 0), 0);
                cartCount.innerText = totalQty;
            } else if (cartCount && data && 'count' in data) {
                cartCount.innerText = data.count;
            }

            // Opcionális notification
            const notify = document.getElementById('cart-notification');
            if (notify) {
                notify.classList.add('show');
                setTimeout(() => notify.classList.remove('show'), 2500);
            }
        })
        .catch(err => {
            console.error('Hiba a kosárhoz adáskor:', err);
        });
    });
});
