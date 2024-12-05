<?php
session_start();

// Inicializar el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Obtener la acción y el producto enviado por el cliente
$action = $_POST['action'] ?? '';
$productId = intval($_POST['id'] ?? 0);
$productPrice = floatval($_POST['price'] ?? 0);

switch ($action) {
    case 'add':
        if ($productId > 0) {
            // Agregar producto al carrito
            if (!isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = ['quantity' => 0, 'price' => $productPrice];
            }
            $_SESSION['cart'][$productId]['quantity']++;
        }
        break;

    case 'remove':
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            // Reducir la cantidad o eliminar el producto si llega a 0
            $_SESSION['cart'][$productId]['quantity']--;
            if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                unset($_SESSION['cart'][$productId]);
            }
        }
        break;

    case 'clear':
        // Vaciar el carrito
        $_SESSION['cart'] = [];
        break;
}

// Devolver el contenido actualizado del carrito
echo json_encode($_SESSION['cart']);
?>