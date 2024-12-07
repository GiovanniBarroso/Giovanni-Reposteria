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
            let custom = "";

            // Mostrar personalización solo si el producto es una tarta
            if (info.custom && info.custom.numPisos && info.custom.rellenos.length > 0) {
                custom = `(Pisos: ${info.custom.numPisos}, Rellenos: ${info.custom.rellenos.join(", ")})`;
            }

            const item = document.createElement("div");
            item.classList.add("cart-item", "d-flex", "justify-content-between", "align-items-center", "mb-2");
            item.innerHTML = `
                <span>${id} - ${info.quantity} x ${parseFloat(info.price).toFixed(2)}€ ${custom}</span>
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


    document.querySelectorAll(".customize-tarta").forEach((button) => {
        button.addEventListener("click", () => {
            const productId = button.getAttribute("data-id");
            const productPrice = button.getAttribute("data-price");

            // Mostrar formulario de personalización
            const formHtml = `
                <form id="customize-form" class="p-3 border rounded bg-light">
                    <h5>Personalizar Tarta</h5>
                    <label for="numPisos" class="form-label">Número de pisos:</label>
                    <select id="numPisos" class="form-select mb-2" name="numPisos">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <label for="rellenos" class="form-label">Rellenos:</label>
                    <select id="rellenos" class="form-select mb-2" name="rellenos" multiple>
                        <option value="chocolate">Chocolate</option>
                        <option value="vainilla">Vainilla</option>
                        <option value="nata">Nata</option>
                        <option value="frutas">Frutas</option>
                    </select>
                    <button type="button" id="add-custom-tarta" class="btn btn-primary w-100">Agregar al carrito</button>
                </form>
            `;

            const modalContainer = document.createElement("div");
            modalContainer.innerHTML = formHtml;
            document.body.appendChild(modalContainer);

            // Registrar evento para el botón dentro del formulario dinámico
            document.getElementById("add-custom-tarta").addEventListener("click", () => {
                const numPisos = document.getElementById("numPisos").value;
                const rellenos = Array.from(
                    document.getElementById("rellenos").selectedOptions
                ).map((option) => option.value);

                // Enviar la información al servidor
                fetch("../img/carrito.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=add&id=${productId}&price=${productPrice}&numPisos=${numPisos}&rellenos=${JSON.stringify(
                        rellenos
                    )}`,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        updateCart(data); // Actualizar el carrito
                        showMessage("Tarta personalizada añadida al carrito.");
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        showMessage("Error al agregar la tarta personalizada.", "danger");
                    })
                    .finally(() => {
                        // Eliminar el formulario después de agregar la tarta
                        modalContainer.remove();
                    });
            });
        });
    });







});




