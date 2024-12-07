<?php
session_start();
require_once __DIR__ . '/../src/Pasteleria.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'usuario') {
    header("Location: index.php");
    exit;
}

$clienteId = $_SESSION['user_id']; // Obtén el ID del usuario logueado

try {
    $db = Database::getConnection();
    $query = "SELECT p.id AS pedido_id, p.fecha, SUM(dp.cantidad * dp.precio_unitario) AS total
              FROM pedidos p
              JOIN detalle_pedidos dp ON p.id = dp.pedido_id
              WHERE p.cliente_id = :cliente_id
              GROUP BY p.id, p.fecha
              ORDER BY p.fecha DESC
              LIMIT 3"; // Limitar a los últimos 3 pedidos
    $stmt = $db->prepare($query);
    $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->execute();
    $pedidosRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pedidosRecientes = [];
}

$pasteleria = new Pasteleria();
$productos = $pasteleria->obtenerProductos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h1 class="text-center mb-4"><i class="bi bi-cart4"></i> Bienvenido, <?= htmlspecialchars($_SESSION['user']) ?>
        </h1>

        <div class="row">
            <!-- Productos Disponibles -->
            <div class="col-md-4">
                <h2 class="text-primary"><i class="bi bi-bag"></i> Productos Disponibles</h2>
                <ul class="list-group">
                    <?php foreach ($productos as $producto): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($producto->getNombre()) ?> -
                            <?= number_format($producto->getPrecio(), 2) ?>€
                            <button class="btn btn-success btn-sm add-to-cart" data-id="<?= $producto->getId() ?>"
                                data-price="<?= $producto->getPrecio() ?>">
                                <i class="bi bi-cart-plus"></i> Agregar
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Carrito de Compras -->
            <div class="col-md-4">
                <h2 class="text-primary"><i class="bi bi-cart"></i> Carrito de Compras</h2>
                <div id="cart" class="bg-white p-3 rounded shadow">
                    <p>El carrito está vacío.</p>
                </div>
                <div class="text-center mt-4">
                    <form action="confirmarPedido.php" method="POST">
                        <button id="confirm-button" type="submit" class="btn btn-primary w-100" disabled>
                            <i class="bi bi-check-circle"></i> Confirmar Pedido
                        </button>
                    </form>
                </div>
                <button id="clear-cart" class="btn btn-danger mt-3"><i class="bi bi-trash"></i> Vaciar carrito</button>
            </div>

            <!-- Historial de Pedidos -->
            <div class="col-md-4">
                <h2 class="text-primary"><i class="bi bi-clock-history"></i> Historial de Pedidos</h2>
                <div class="bg-white p-3 rounded shadow">
                    <?php if (!empty($pedidosRecientes)): ?>
                        <ul class="list-group mb-3">
                            <?php foreach ($pedidosRecientes as $pedido): ?>
                                <li class="list-group-item">
                                    Pedido #<?= $pedido['pedido_id'] ?> - Fecha: <?= $pedido['fecha'] ?> - Total:
                                    <?= number_format($pedido['total'], 2) ?>€
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay pedidos recientes.</p>
                    <?php endif; ?>
                    <a href="historialPedidos.php" class="btn btn-info w-100"><i class="bi bi-eye"></i> Ver Todo</a>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="logout.php" class="btn btn-secondary"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</a>
        </div>
    </div>

    <script src="../js/carrito.js" defer></script>
</body>

</html>