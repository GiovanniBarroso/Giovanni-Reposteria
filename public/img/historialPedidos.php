<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=Debes iniciar sesión para ver tu historial");
    exit;
}

require_once '../db/Database.php';

$clienteId = $_SESSION['user_id'];

try {
    $db = Database::getConnection();

    // Consulta para obtener los pedidos y sus detalles
    $query = "SELECT p.id AS pedido_id, p.fecha, dp.cantidad, dp.precio_unitario, pr.nombre AS producto
              FROM pedidos p
              JOIN detalle_pedidos dp ON p.id = dp.pedido_id
              JOIN productos pr ON dp.producto_id = pr.id
              WHERE p.cliente_id = :cliente_id
              ORDER BY p.fecha DESC, dp.pedido_id";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar los productos por pedido
    $pedidosAgrupados = [];
    foreach ($pedidos as $pedido) {
        $pedidoId = $pedido['pedido_id'];
        if (!isset($pedidosAgrupados[$pedidoId])) {
            $pedidosAgrupados[$pedidoId] = [
                'fecha' => $pedido['fecha'],
                'productos' => [],
                'total' => 0, // Inicializa el total del pedido
            ];
        }
        $pedidosAgrupados[$pedidoId]['productos'][] = [
            'producto' => $pedido['producto'],
            'cantidad' => $pedido['cantidad'],
            'precio_unitario' => $pedido['precio_unitario'],
        ];
        // Sumar al total
        $pedidosAgrupados[$pedidoId]['total'] += $pedido['cantidad'] * $pedido['precio_unitario'];
    }
} catch (PDOException $e) {
    $pedidosAgrupados = [];
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h1 class="text-center mb-4"><i class="bi bi-clock-history"></i> Historial de Pedidos</h1>
        <?php if (!empty($pedidosAgrupados)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Productos</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidosAgrupados as $pedidoId => $pedido): ?>
                        <tr>
                            <td><?= htmlspecialchars($pedidoId) ?></td>
                            <td><?= htmlspecialchars($pedido['fecha']) ?></td>
                            <td>
                                <ul>
                                    <?php foreach ($pedido['productos'] as $producto): ?>
                                        <li>
                                            <?= htmlspecialchars($producto['producto']) ?>
                                            (Cantidad: <?= $producto['cantidad'] ?>,
                                            Precio unitario: <?= number_format($producto['precio_unitario'], 2) ?>€)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td><?= number_format($pedido['total'], 2) ?>€</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted">No hay pedidos recientes.</p>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="main.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
    </div>
</body>

</html>