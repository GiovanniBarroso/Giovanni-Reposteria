<?php
session_start();
require_once '../src/Pedido.php';
require_once '../db/Database.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: main.php?error=El carrito está vacío.");
    exit;
}

$clienteId = $_SESSION['user_id'];
$carrito = $_SESSION['cart'];

try {
    $db = Database::getConnection();
    $db->beginTransaction(); // Iniciar una transacción para garantizar integridad

    // Crear un nuevo pedido
    $query = "INSERT INTO pedidos (cliente_id, fecha) VALUES (:cliente_id, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->execute();

    $pedidoId = $db->lastInsertId(); // Obtener el ID del nuevo pedido

    // Añadir detalles del pedido
    foreach ($carrito as $productId => $info) {
        $query = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario)
                  VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':cantidad', $info['quantity'], PDO::PARAM_INT);
        $stmt->bindValue(':precio_unitario', $info['price'], PDO::PARAM_STR);
        $stmt->execute();
    }

    $db->commit(); // Confirmar la transacción

    // Vaciar el carrito
    unset($_SESSION['cart']);

    header("Location: main.php?success=Pedido confirmado correctamente.");
    exit;

} catch (PDOException $e) {
    $db->rollBack(); // Revertir la transacción en caso de error
    header("Location: main.php?error=Error al confirmar el pedido: " . $e->getMessage());
    exit;
}
