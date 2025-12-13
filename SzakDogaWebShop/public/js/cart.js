document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cart-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.innerText = data.count;
                }

                const notify = document.getElementById('cart-notification');
                if (notify) {
                    notify.classList.add('show');
                    setTimeout(() => notify.classList.remove('show'), 2500);
                }

                console.log("Kosár válasz:", data);
            })
            .catch(error => {
                console.error("Hiba a kosárhoz adáskor:", error);
            });
        });
    });
});


document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cart-action').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`/cart/${action}/${productId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // itt HTML-t várunk vissza
            .then(html => {
                // Kosár tartalom frissítése
                const cartContainer = document.querySelector('#cart-container');
                if (cartContainer) {
                    cartContainer.innerHTML = html;
                }
            })
            .catch(error => {
                console.error("Hiba a kosár műveletnél:", error);
            });
        });
    });
});

function bindCartButtons() {
    document.querySelectorAll('.cart-action').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`/cart/${action}/${productId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                const cartContainer = document.querySelector('#cart-container');
                if (cartContainer) {
                    cartContainer.innerHTML = html;
                    bindCartButtons(); // újra rá kell kötni az eseményeket
                }
            })
            .catch(error => {
                console.error("Hiba a kosár műveletnél:", error);
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    bindCartButtons();
});


