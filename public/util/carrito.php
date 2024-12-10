<?php
session_start();
require_once '../src/Dulce.php';
require_once '../db/Database.php';

// Verificar si el método es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$productId = intval($_POST['id'] ?? 0);

try {
    $db = Database::getConnection();

    switch ($action) {
        case 'get':
            // Devolver el carrito actual
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
            exit;

        case 'add':
            // Agregar un producto al carrito
            if ($productId > 0) {
                $stmt = $db->prepare("SELECT nombre, precio FROM productos WHERE id = :id");
                $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
                $stmt->execute();
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    if (!isset($_SESSION['cart'][$productId])) {
                        $_SESSION['cart'][$productId] = [
                            'quantity' => 0,
                            'price' => $product['precio'],
                            'name' => $product['nombre']
                        ];
                    }
                    $_SESSION['cart'][$productId]['quantity']++;
                } else {
                    echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
                    exit;
                }
            }
            break;

        case 'remove':
            // Eliminar una unidad de un producto del carrito
            if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity']--;
                if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$productId]); // Eliminar producto si la cantidad llega a 0
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Producto no encontrado en el carrito']);
                exit;
            }
            break;

        case 'delete':
            // Eliminar completamente un producto del carrito
            if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }
            break;

        case 'clear':
            // Vaciar todo el carrito
            $_SESSION['cart'] = [];
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            exit;
    }

    // Devolver el carrito actualizado
    echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor: ' . $e->getMessage()]);
    exit;
}
?>