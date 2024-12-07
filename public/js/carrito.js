document.addEventListener("DOMContentLoaded", () => {
    const cart = document.getElementById("cart");
    const clearCartButton = document.getElementById("clear-cart");
    const confirmButton = document.getElementById("confirm-button");

    // Función para mostrar un mensaje de confirmación o error
    const showMessage = (message, type = "success") => {
        // Eliminar cualquier mensaje existente
        const existingAlert = document.querySelector(".alert");
        if (existingAlert) existingAlert.remove();

        // Crear y mostrar el nuevo mensaje
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type} text-center fixed-top`;
        alertDiv.style.zIndex = "1050"; // Asegurar que esté sobre otros elementos
        alertDiv.textContent = message;
        document.body.prepend(alertDiv);

        // Remover el mensaje después de 3 segundos
        setTimeout(() => alertDiv.remove(), 2000);
    };

    const updateCart = (data) => {
        if (data.status === "success") {
            cart.innerHTML = "";
            let total = 0;

            for (const [id, info] of Object.entries(data.cart)) {
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

            confirmButton.disabled = Object.keys(data.cart).length === 0;
        }

        showMessage(data.message, data.status === "success" ? "success" : "danger");
    };

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
                .then(updateCart);
        });
    });

    clearCartButton.addEventListener("click", () => {
        fetch("../img/carrito.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "action=clear",
        })
            .then((response) => response.json())
            .then(updateCart);
    });
});
