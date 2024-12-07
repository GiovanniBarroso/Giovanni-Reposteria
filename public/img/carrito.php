<?php
session_start();
require_once '../db/Database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Obtener la acción y los datos del producto
$action = $_POST['action'] ?? '';
$productId = intval($_POST['id'] ?? 0);
$productPrice = floatval($_POST['price'] ?? 0);
$response = ['status' => 'error', 'message' => '', 'cart' => []];

try {
    $db = Database::getConnection();

    switch ($action) {
        case 'add':
            // Validar producto
            $query = "SELECT precio FROM productos WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Agregar producto al carrito
                if (!isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] = ['quantity' => 0, 'price' => $product['precio']];
                }
                $_SESSION['cart'][$productId]['quantity']++;
                $response['status'] = 'success';
                $response['message'] = 'Producto añadido al carrito.';
            } else {
                $response['message'] = 'Producto no encontrado.';
            }
            break;

        case 'remove':
            if (isset($_SESSION['cart'][$productId])) {
                // Reducir cantidad o eliminar producto
                $_SESSION['cart'][$productId]['quantity']--;
                if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$productId]);
                }
                $response['status'] = 'success';
                $response['message'] = 'Producto eliminado del carrito.';
            } else {
                $response['message'] = 'Producto no encontrado en el carrito.';
            }
            break;

        case 'clear':
            // Vaciar el carrito
            $_SESSION['cart'] = [];
            $response['status'] = 'success';
            $response['message'] = 'Carrito vaciado.';
            break;

        default:
            $response['message'] = 'Acción no válida.';
            break;
    }

    // Incluir el estado del carrito
    $response['cart'] = $_SESSION['cart'];
} catch (PDOException $e) {
    $response['message'] = 'Error al procesar el carrito: ' . $e->getMessage();
}

echo json_encode($response);
exit;
