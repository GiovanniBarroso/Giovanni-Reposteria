document.addEventListener("DOMContentLoaded", () => {
    const cart = document.getElementById("cart");
    const clearCartButton = document.getElementById("clear-cart");

    // Manejar clics en "Agregar al carrito"
    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", () => {
            const productId = button.getAttribute("data-id");
            const productPrice = button.getAttribute("data-price");

            fetch("../img/carrito.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `action=add&id=${productId}&price=${productPrice}`,
            })
                .then(response => response.json())
                .then(data => updateCart(data));
        });
    });

    // Manejar clic en "Vaciar carrito"
    clearCartButton.addEventListener("click", () => {
        fetch("carrito.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "action=clear",
        })
            .then(response => response.json())
            .then(data => updateCart(data));
    });

    // Actualizar el carrito en el DOM
    function updateCart(cartData) {
        cart.innerHTML = ""; // Limpiar contenido del carrito
        let total = 0;

        for (const [id, info] of Object.entries(cartData)) {
            const item = document.createElement("div");
            item.textContent = `${id} - ${info.quantity} x ${parseFloat(info.price).toFixed(2)}€`;
            cart.appendChild(item);
            total += info.quantity * info.price;
        }

        if (total > 0) {
            const totalElement = document.createElement("div");
            totalElement.textContent = `Total: ${total.toFixed(2)}€`;
            cart.appendChild(totalElement);
        } else {
            cart.textContent = "El carrito está vacío.";
        }
    }
});
