document.addEventListener("DOMContentLoaded", () => {
    const cartDropdown = document.getElementById("cartDropdown");
    const clearCartButton = document.getElementById("clear-cart");

    const showMessage = (message, type = "success") => {
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type} text-center fixed-top`;
        alertDiv.textContent = message;
        document.body.prepend(alertDiv);

        setTimeout(() => alertDiv.remove(), 2000);
    };

    const updateCart = (cartData) => {
        cartDropdown.innerHTML = "";
        let total = 0;

        for (const [id, item] of Object.entries(cartData)) {
            const cartItem = document.createElement("li");
            cartItem.className = "dropdown-item d-flex justify-content-between align-items-center";
            cartItem.innerHTML = `
                <span>${item.name} (${item.quantity} x ${item.price.toFixed(2)}€)</span>
                <div>
                    <button class="btn btn-sm btn-success add-item" data-id="${id}">+</button>
                    <button class="btn btn-sm btn-danger remove-item" data-id="${id}">-</button>
                </div>
            `;
            cartDropdown.appendChild(cartItem);
            total += item.quantity * item.price;
        }

        if (total > 0) {
            const totalItem = document.createElement("li");
            totalItem.className = "dropdown-item text-end fw-bold";
            totalItem.textContent = `Total: ${total.toFixed(2)}€`;
            cartDropdown.appendChild(totalItem);
        } else {
            cartDropdown.innerHTML = "<p class='text-muted'>El carrito está vacío.</p>";
        }

        addCartEventListeners();
    };

    const addCartEventListeners = () => {
        document.querySelectorAll(".add-item").forEach((button) => {
            button.addEventListener("click", () => {
                const productId = button.getAttribute("data-id");
                fetch("../util/carrito.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=add&id=${productId}`
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            updateCart(data.cart);
                            showMessage("Producto añadido al carrito.");
                        } else {
                            showMessage(data.error, "danger");
                        }
                    });
            });
        });

        document.querySelectorAll(".remove-item").forEach((button) => {
            button.addEventListener("click", () => {
                const productId = button.getAttribute("data-id");
                fetch("../util/carrito.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=remove&id=${productId}`
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            updateCart(data.cart);
                            showMessage("Producto eliminado del carrito.", "warning");
                        } else {
                            showMessage(data.error, "danger");
                        }
                    });
            });
        });
    };

    clearCartButton.addEventListener("click", () => {
        fetch("../util/carrito.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "action=clear"
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    updateCart(data.cart);
                    showMessage("Carrito vaciado correctamente.", "danger");
                }
            });
    });

    document.querySelectorAll(".add-to-cart").forEach((button) => {
        button.addEventListener("click", () => {
            const productId = button.getAttribute("data-id");
            fetch("../util/carrito.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=add&id=${productId}`
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        updateCart(data.cart);
                        showMessage("Producto añadido al carrito.");
                    } else {
                        showMessage(data.error, "danger");
                    }
                });
        });
    });
});
