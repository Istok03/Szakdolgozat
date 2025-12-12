document.querySelectorAll('.cart-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        let productId = this.getAttribute('data-id');

        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {

            document.querySelector('.cart-count').innerText = data.count;
        });
    });
});

console.log("Cart.js betöltve!");
