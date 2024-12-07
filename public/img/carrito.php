<?php
session_start();

// Inicializar el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Obtener la acción y los datos del producto
$action = $_POST['action'] ?? '';
$productId = intval($_POST['id'] ?? 0);
$productPrice = isset($_POST['price']) ? floatval($_POST['price']) : null;

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
            // Reducir cantidad del producto
            $_SESSION['cart'][$productId]['quantity']--;
            if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                unset($_SESSION['cart'][$productId]);
            }
        }
        break;

    case 'remove_all':
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            // Eliminar el producto completamente del carrito
            unset($_SESSION['cart'][$productId]);
        }
        break;

    case 'clear':
        // Vaciar el carrito por completo
        $_SESSION['cart'] = [];
        break;

    default:
        // Acción no reconocida
        break;
}

// Responder con el estado actualizado del carrito
echo json_encode($_SESSION['cart']);
exit;
