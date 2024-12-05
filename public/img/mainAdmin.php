<?php
session_start();

if ($_SESSION['user'] !== 'admin') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

// Mostrar listado de clientes y productos
require_once '../src/Pasteleria.php';

$pasteleria = new Pasteleria();
$productos = $pasteleria->obtenerProductos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Pastelería</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h1 class="text-center mb-4"><i class="bi bi-shield-lock"></i> Bienvenido, Administrador</h1>

        <!-- Mensajes de error o éxito -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <h2 class="text-primary"><i class="bi bi-box-seam"></i> Listado de Productos</h2>
                <ul class="list-group">
                    <?php foreach ($productos as $producto): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($producto->muestraResumen()) ?>
                            <div>
                                <a href="editarProducto.php?id=<?= $producto->getId() ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="eliminarProducto.php?id=<?= $producto->getId() ?>" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Eliminar
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>
        </div>

        <div class="text-center mt-4">
            <a href="logout.php" class="btn btn-secondary">
                <i class="bi bi-box-arrow-left"></i> Cerrar sesión
            </a>
        </div>
    </div>
</body>

</html>