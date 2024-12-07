document.addEventListener("DOMContentLoaded", () => {
    const cart = document.getElementById("cart");
    const clearCartButton = document.getElementById("clear-cart");
    const confirmButton = document.getElementById("confirm-button");

    // Función para mostrar un mensaje de confirmación o error
    const showMessage = (message, type = "success") => {
        const existingAlert = document.querySelector(".alert");
        if (existingAlert) existingAlert.remove(); // Elimina mensajes existentes

        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type} text-center fixed-top`;
        alertDiv.style.zIndex = "1050"; // Asegura que esté visible
        alertDiv.textContent = message;
        document.body.prepend(alertDiv);

        setTimeout(() => alertDiv.remove(), 2000); // Remueve el mensaje después de 2 segundos
    };

    // Función para actualizar el carrito en el DOM
    function updateCart(cartData) {
        cart.innerHTML = ""; // Limpia el carrito
        let total = 0;

        for (const [id, info] of Object.entries(cartData)) {
            const item = document.createElement("div");
            item.classList.add("cart-item", "d-flex", "justify-content-between", "align-items-center", "mb-2");
            item.innerHTML = `
                <span>${id} - ${info.quantity} x ${parseFloat(info.price).toFixed(2)}€</span>
                <div>
                    <button class="btn btn-sm btn-success add-quantity" data-id="${id}">+</button>
                    <button class="btn btn-sm btn-warning remove-quantity" data-id="${id}">-</button>
                    <button class="btn btn-sm btn-danger remove-item" data-id="${id}">x</button>
                </div>
            `;
            cart.appendChild(item);
            total += info.quantity * info.price;
        }

        if (total > 0) {
            const totalElement = document.createElement("div");
            totalElement.classList.add("text-end", "fw-bold", "mt-3");
            totalElement.textContent = `Total: ${total.toFixed(2)}€`;
            cart.appendChild(totalElement);
        } else {
            cart.textContent = "El carrito está vacío.";
        }

        // Añadir eventos a los botones para manejar cantidad y eliminación
        addEventListeners();
        validateCart(cartData);
    }

    // Función para añadir eventos a los botones del carrito
    function addEventListeners() {
        document.querySelectorAll(".add-quantity").forEach((button) => {
            button.addEventListener("click", () => {
                const productId = button.getAttribute("data-id");
                fetch("../img/carrito.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=add&id=${productId}`,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        updateCart(data);
                        showMessage("Producto añadido correctamente.");
                    });
            });
        });

        document.querySelectorAll(".remove-quantity").forEach((button) => {
            button.addEventListener("click", () => {
                const productId = button.getAttribute("data-id");
                fetch("../img/carrito.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=remove&id=${productId}`,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        updateCart(data);
                        showMessage("Unidad eliminada correctamente.", "warning");
                    });
            });
        });

        document.querySelectorAll(".remove-item").forEach((button) => {
            button.addEventListener("click", () => {
                const productId = button.getAttribute("data-id");
                fetch("../img/carrito.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=remove_all&id=${productId}`,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        updateCart(data);
                        showMessage("Producto eliminado del carrito.", "danger");
                    });
            });
        });
    }

    // Validar si el carrito está vacío y habilitar/deshabilitar el botón "Confirmar Pedido"
    function validateCart(cartData) {
        const isCartEmpty = Object.keys(cartData).length === 0; // Verifica si el carrito está vacío
        confirmButton.disabled = isCartEmpty; // Deshabilita si está vacío
    }

    // Manejar el clic en "Agregar al carrito" desde los productos disponibles
    document.querySelectorAll(".add-to-cart").forEach((button) => {
        button.addEventListener("click", () => {
            const productId = button.getAttribute("data-id");
            const productPrice = button.getAttribute("data-price");

            fetch("../img/carrito.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=add&id=${productId}&price=${productPrice}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    updateCart(data);
                    showMessage("Producto añadido al carrito.");
                });
        });
    });

    // Manejar el clic en "Vaciar carrito"
    clearCartButton.addEventListener("click", () => {
        fetch("../img/carrito.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "action=clear",
        })
            .then((response) => response.json())
            .then((data) => {
                updateCart(data);
                showMessage("Carrito vaciado correctamente.", "danger");
            });
    });
});
