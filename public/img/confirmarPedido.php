<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=Debes iniciar sesión para confirmar el pedido");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: main.php?error=El carrito está vacío");
    exit;
}

require_once '../db/Database.php';

$clienteId = $_SESSION['user_id'];
$carrito = $_SESSION['cart'];

try {
    $db = Database::getConnection();
    $db->beginTransaction();

    // Crear un pedido
    $query = "INSERT INTO pedidos (cliente_id, fecha) VALUES (:cliente_id, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener el ID del pedido recién creado
    $pedidoId = $db->lastInsertId();

    // Insertar productos en detalle_pedidos con el precio actual
    $queryDetalle = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) 
                     VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario)";
    $stmtDetalle = $db->prepare($queryDetalle);

    foreach ($carrito as $productId => $info) {
        $stmtDetalle->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
        $stmtDetalle->bindValue(':producto_id', $productId, PDO::PARAM_INT);
        $stmtDetalle->bindValue(':cantidad', $info['quantity'], PDO::PARAM_INT);
        $stmtDetalle->bindValue(':precio_unitario', $info['price'], PDO::PARAM_STR); // Guardar precio actual
        $stmtDetalle->execute();
    }

    $db->commit();

    // Vaciar el carrito después de confirmar el pedido
    unset($_SESSION['cart']);

    header("Location: main.php?success=Pedido confirmado correctamente");
    exit;
} catch (PDOException $e) {
    $db->rollBack();
    header("Location: main.php?error=Error al confirmar el pedido: " . $e->getMessage());
    exit;
}
