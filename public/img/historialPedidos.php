<?php
session_start();
require_once '../src/Pedido.php';
require_once '../src/Bollo.php';
require_once '../src/Chocolate.php';
require_once '../src/Tarta.php';
require_once '../db/Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$clienteId = $_SESSION['user_id'];

try {
    $db = Database::getConnection();

    // Obtener pedidos del cliente
    $query = "SELECT p.id AS pedido_id, p.fecha, dp.producto_id, dp.cantidad, dp.precio_unitario, prod.nombre, prod.tipo
              FROM pedidos p
              JOIN detalle_pedidos dp ON p.id = dp.pedido_id
              JOIN productos prod ON dp.producto_id = prod.id
              WHERE p.cliente_id = :cliente_id
              ORDER BY p.fecha DESC";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar por pedido
    $pedidos = [];
    foreach ($result as $row) {
        $pedidoId = $row['pedido_id'];
        if (!isset($pedidos[$pedidoId])) {
            $pedidos[$pedidoId] = new Pedido(
                $pedidoId,
                $clienteId,
                [],
                new DateTime($row['fecha'])
            );
        }

        // Crear el objeto Dulce según el tipo
        $dulce = null;
        switch ($row['tipo']) {
            case 'Bollo':
                $dulce = new Bollo(
                    $row['producto_id'],
                    $row['nombre'],
                    $row['precio_unitario'],
                    '',
                    'Bollo',
                    '' // Puedes usar el relleno si está disponible en la base de datos
                );
                break;

            case 'Chocolate':
                $dulce = new Chocolate(
                    $row['producto_id'],
                    $row['nombre'],
                    $row['precio_unitario'],
                    '',
                    'Chocolate',
                    0, // porcentajeCacao
                    0  // peso
                );
                break;

            case 'Tarta':
                $dulce = new Tarta(
                    $row['producto_id'],
                    $row['nombre'],
                    $row['precio_unitario'],
                    '',
                    'Tarta',
                    [],  // rellenos
                    1,   // numPisos
                    2,   // minNumComensales
                    2    // maxNumComensales
                );
                break;

            default:
                // Manejar productos con tipos desconocidos
                throw new Exception("Tipo de producto desconocido: {$row['tipo']}");
        }

        if ($dulce) {
            $pedidos[$pedidoId]->agregarDulce($dulce, $row['cantidad']);
        }
    }

} catch (PDOException $e) {
    header("Location: main.php?error=Error al cargar el historial: " . $e->getMessage());
    exit;
} catch (Exception $e) {
    header("Location: main.php?error=Error: " . $e->getMessage());
    exit;
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
        <h1 class="text-center mb-4">Historial de Pedidos</h1>
        <?php if (!empty($pedidos)): ?>
            <?php foreach ($pedidos as $pedido): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        Pedido #<?= $pedido->getId() ?> - Fecha: <?= $pedido->getFecha() ?> - Total:
                        <?= number_format($pedido->getTotal(), 2) ?>€
                    </div>
                    <div class="card-body">
                        <ul>
                            <?php foreach ($pedido->getDulces() as $dulceId => $dulceData): ?>
                                <li>
                                    <?= $dulceData['dulce']->getNombre() ?> -
                                    Cantidad: <?= $dulceData['cantidad'] ?> -
                                    Precio Unitario: <?= number_format($dulceData['dulce']->getPrecio(), 2) ?>€
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No se han realizado pedidos aún.</p>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="main.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</body>

</html>