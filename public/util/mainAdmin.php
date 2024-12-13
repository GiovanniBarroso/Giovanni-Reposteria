<?php

session_start();
require_once '../src/Pasteleria.php';

if ($_SESSION['user'] !== 'admin') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}


$pasteleria = new Pasteleria();
$productos = $pasteleria->obtenerProductos();
$clientes = $pasteleria->obtenerClientes();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pastelería</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>

<body class="bg-light">

    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="mainAdmin.php">
                <i class="bi bi-shop fs-3 me-2"></i>
                <span class="fw-bold">Panel Admin</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="btn btn-danger btn-sm d-flex align-items-center" href="logout.php">
                            <i class="bi bi-box-arrow-left me-1"></i> Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Sección de bienvenida -->
        <div class="bg-primary text-white rounded shadow-lg p-4 text-center mb-5">
            <h1 class="display-6 fw-bold"><i class="bi bi-shield-lock"></i> Bienvenido, Administrador</h1>
            <p class="lead">Gestione productos y clientes desde un solo lugar.</p>
        </div>

        <!-- Botones de acciones -->
        <div class="d-flex justify-content-between flex-wrap mb-4">
            <a href="agregarProducto.php" class="btn btn-success btn-lg shadow-sm">
                <i class="bi bi-plus-circle"></i> Añadir Producto
            </a>
            <a href="eliminarValoraciones.php" class="btn btn-danger btn-lg shadow-sm"
                onclick="return confirm('¿Estás seguro de que deseas eliminar todas las valoraciones? Esta acción no se puede deshacer.');">
                <i class="bi bi-trash"></i> Eliminar Valoraciones
            </a>
        </div>

        <!-- Productos -->
        <h2 class="text-primary mb-3 text-center"><i class="bi bi-box-seam"></i> Productos</h2>
        <div class="row g-4">
            <?php foreach ($productos as $producto): ?>
                <?php
                $imagen = "../img/{$producto->getId()}.jpg";
                if (!file_exists($imagen)) {
                    $imagen = "foto1.jpg";
                }
                ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <img src="<?= $imagen ?>" class="card-img-top" alt="<?= htmlspecialchars($producto->getNombre()) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($producto->getNombre()) ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars($producto->muestraResumen()) ?></p>
                            <div class="d-flex justify-content-between">
                                <a href="editarProducto.php?id=<?= $producto->getId() ?>" class="btn btn-info btn-sm">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="eliminarProducto.php?id=<?= $producto->getId() ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                    <i class="bi bi-trash"></i> Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Clientes -->
        <h2 class="text-primary mt-5 mb-3 text-center"><i class="bi bi-people"></i> Clientes</h2>
        <div class="table-responsive shadow-sm">
            <table class="table table-hover table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= htmlspecialchars($cliente['id']) ?></td>
                            <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                            <td><?= htmlspecialchars($cliente['usuario']) ?></td>
                            <td><?= htmlspecialchars($cliente['rol']) ?></td>
                            <td>
                                <a href="editarCliente.php?id=<?= $cliente['id'] ?>" class="btn btn-info btn-sm">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="eliminarCliente.php?id=<?= $cliente['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?');">
                                    <i class="bi bi-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
