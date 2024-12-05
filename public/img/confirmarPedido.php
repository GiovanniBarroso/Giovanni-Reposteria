<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php?error=Debes iniciar sesión para confirmar el pedido");
    exit;
}

require_once '../src/Pasteleria.php';

$pasteleria = new Pasteleria();
$clienteId = $_SESSION['user_id'] ?? null;
$carrito = $_SESSION['cart'] ?? [];

if (!$clienteId) {
    header("Location: main.php?error=No se ha identificado al usuario");
    exit;
}

if (empty($carrito)) {
    header("Location: main.php?error=El carrito está vacío");
    exit;
}

try {
    $db = Database::getConnection();

    // Guardar cada producto del carrito en la tabla `pedidos`
    foreach ($carrito as $productId => $detalle) {
        $cantidad = $detalle['quantity'];
        $query = "INSERT INTO pedidos (cliente_id, producto_id, cantidad) VALUES (:cliente_id, :producto_id, :cantidad)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Vaciar el carrito después de confirmar el pedido
    unset($_SESSION['cart']);

    header("Location: main.php?success=Pedido confirmado correctamente");
} catch (PDOException $e) {
    header("Location: main.php?error=Error al confirmar el pedido: " . $e->getMessage());
    exit;
}
?>