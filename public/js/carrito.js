document.addEventListener("DOMContentLoaded", () => {
    const cartDropdown = document.getElementById("cartDropdown");
    const cartCount = document.getElementById("cartCount");

    // Actualizar el carrito en el servidor
    const updateCart = async (action, productId = null) => {
        try {
            const response = await fetch("../util/carrito.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=${action}${productId ? `&id=${productId}` : ""}`
            });
            const data = await response.json();

            if (data.success) {
                showMessage("Carrito actualizado correctamente", "success");
                renderCart(data.cart); // Renderiza el carrito con los nuevos datos
                updateCartCount(data.cart); // Actualiza el contador
            } else {
                showMessage(data.error, "danger");
            }
        } catch (error) {
            console.error("Error en la comunicación con el servidor:", error);
            showMessage("Error en la comunicación con el servidor", "danger");
        }
    };

    // Mostrar mensajes en el carrito
    const showMessage = (message, type = "success") => {
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type} text-center fixed-top`;
        alertDiv.textContent = message;
        document.body.prepend(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 1200); // Mostrar por 2 segundos
    };

    // Renderizar el carrito en el menú desplegable
    const renderCart = (cart) => {
        cartDropdown.innerHTML = ""; // Limpiar el contenido previo
        let total = 0;

        // Verificar si el carrito está vacío
        if (Object.keys(cart).length === 0) {
            cartDropdown.innerHTML = `
                <li class="dropdown-item text-center">
                    <p class="text-muted">El carrito está vacío.</p>
                </li>`;
            return;
        }

        // Recorrer los elementos del carrito
        for (const [id, item] of Object.entries(cart)) {
            const price = parseFloat(item.price);
            total += item.quantity * price;

            cartDropdown.innerHTML += `
                <li class="dropdown-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">${item.name}</h6>
                            <small class="text-muted">${item.quantity} x ${price.toFixed(2)}€</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-success add-item" data-id="${id}">
                                <i class="bi bi-plus"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning remove-item" data-id="${id}">
                                <i class="bi bi-dash"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-item" data-id="${id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <hr class="my-2">
                </li>`;
        }

        // Añadir total y botones finales
        cartDropdown.innerHTML += `
            <li class="dropdown-item">
                <div class="d-flex justify-content-between">
                    <strong>Total:</strong>
                    <span>${total.toFixed(2)}€</span>
                </div>
            </li>
            <li class="dropdown-item text-center">
                <a href="../util/confirmarPedido.php" class="btn btn-primary w-100 confirm-order">Confirmar Pedido</a>
            </li>
            <li class="dropdown-item text-center">
                <button class="btn btn-danger w-100 clear-cart">Limpiar Carrito</button>
            </li>`;

        assignCartEventListeners(); // Reasignar eventos
    };

    // Actualizar contador del carrito
    const updateCartCount = (cart) => {
        const itemCount = Object.values(cart).reduce((total, item) => total + item.quantity, 0);
        cartCount.textContent = itemCount; // Actualizar el número del contador
    };

    // Asignar eventos a los botones del carrito
    const assignCartEventListeners = () => {
        document.querySelectorAll(".add-item").forEach((button) => {
            button.addEventListener("click", (event) => {
                event.stopPropagation();
                const id = button.getAttribute("data-id");
                updateCart("add", id);
            });
        });

        document.querySelectorAll(".remove-item").forEach((button) => {
            button.addEventListener("click", (event) => {
                event.stopPropagation();
                const id = button.getAttribute("data-id");
                updateCart("remove", id);
            });
        });

        document.querySelectorAll(".delete-item").forEach((button) => {
            button.addEventListener("click", (event) => {
                event.stopPropagation();
                const id = button.getAttribute("data-id");
                updateCart("delete", id);
            });
        });

        const clearCartButton = document.querySelector(".clear-cart");
        if (clearCartButton) {
            clearCartButton.addEventListener("click", () => {
                updateCart("clear");
            });
        }
    };

    // Inicializar eventos de productos en la página principal
    document.querySelectorAll(".add-to-cart").forEach((button) => {
        button.addEventListener("click", () => {
            const productId = button.getAttribute("data-id");
            updateCart("add", productId);
        });
    });

    // Cargar el carrito al hacer clic en el botón del navbar
    document.getElementById("navbarCart").addEventListener("click", async () => {
        try {
            const response = await fetch("../util/carrito.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=get"
            });

            const data = await response.json();
            if (data.success) {
                renderCart(data.cart);
                updateCartCount(data.cart); // Actualiza el contador al abrir el carrito
            } else {
                console.error("Error al cargar el carrito:", data.error);
            }
        } catch (error) {
            console.error("Error en la comunicación con el servidor:", error);
        }
    });
});
