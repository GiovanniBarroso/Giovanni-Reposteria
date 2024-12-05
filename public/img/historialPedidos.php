<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php?error=Debes iniciar sesión para ver tu historial");
    exit;
}

require_once '../src/Pasteleria.php';

$clienteId = $_SESSION['user_id'] ?? null;

if (!$clienteId) {
    header("Location: index.php?error=No se ha identificado al usuario");
    exit;
}

try {
    $db = Database::getConnection();

    $query = "SELECT p.id, pr.nombre AS producto, p.cantidad, p.fecha 
              FROM pedidos p 
              JOIN productos pr ON p.producto_id = pr.id 
              WHERE p.cliente_id = :cliente_id 
              ORDER BY p.fecha DESC";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header("Location: main.php?error=Error al cargar el historial: " . $e->getMessage());
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
        <h1 class="text-center mb-4"><i class="bi bi-clock-history"></i> Historial de Pedidos</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= htmlspecialchars($pedido['id']) ?></td>
                        <td><?= htmlspecialchars($pedido['producto']) ?></td>
                        <td><?= htmlspecialchars($pedido['cantidad']) ?></td>
                        <td><?= htmlspecialchars($pedido['fecha']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-center mt-4">
            <a href="main.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
    </div>
</body>

</html>