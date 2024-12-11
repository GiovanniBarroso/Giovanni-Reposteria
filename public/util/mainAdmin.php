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
    <title>Administrador - Pastelería</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body class="bg-light">

    <!-- Navegación fija -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="mainAdmin.php"><i class="bi bi-shop"></i> Pastelería Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container mt-4">
        <!-- Sección de bienvenida -->
        <div class="welcome-admin bg-primary text-white rounded shadow-lg p-4 text-center mb-5">
            <h1 class="display-6 fw-bold"><i class="bi bi-shield-lock"></i> Bienvenido, Administrador</h1>
            <p class="lead">Gestione productos y clientes desde un solo lugar.</p>
        </div>


        <!-- Botón para agregar productos -->
        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <a href="agregarProducto.php" class="btn btn-success btn-lg shadow-sm">
                    <i class="bi bi-plus-circle"></i> Añadir Producto
                </a>
            </div>
        </div>


        <!-- Listado de Productos -->
        <div class="row">
            <h2 class="text-primary text-center mb-3"><i class="bi bi-box-seam"></i> Productos</h2>

            <?php foreach ($productos as $producto): ?>
                <?php
                $imagen = "../img/{$producto->getId()}.jpg";
                if (!file_exists($imagen)) {
                    $imagen = "foto1.jpg"; // Imagen por defecto
                }
                ?>

                <div class="col-md-4 col-sm-6 mb-4 d-flex align-items-stretch">
                    <div class="card2 h-100 shadow-sm border-0 rounded">
                        <img src="<?= $imagen ?>" class="card-img-top product-image2 rounded-top"
                            alt="<?= htmlspecialchars($producto->getNombre()) ?>">
                        <div class="card-body2 d-flex flex-column p-4">
                            <h5 class="card-title text-primary fw-bold"><?= htmlspecialchars($producto->getNombre()) ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars($producto->muestraResumen()) ?></p>
                            <div class="mt-auto">
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


        <!-- Listado de Clientes -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h2 class="text-primary text-center mb-3"><i class="bi bi-people"></i> Clientes</h2>
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-hover table-bordered align-middle">
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
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>