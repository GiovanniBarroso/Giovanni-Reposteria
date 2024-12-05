<?php
session_start();

if ($_SESSION['user'] !== 'admin') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

require_once '../src/Pasteleria.php';

if (!isset($_GET['id'])) {
    header("Location: mainAdmin.php?error=Falta el ID del producto");
    exit;
}

$id = intval($_GET['id']);
$pasteleria = new Pasteleria();
$producto = $pasteleria->buscarProductoPorId($id);

if (!$producto) {
    header("Location: mainAdmin.php?error=Producto no encontrado");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Editar Producto</h1>
        <form action="actualizarProducto.php" method="POST" class="shadow p-4 bg-white rounded">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $producto->getNombre() ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01"
                    value="<?= $producto->getPrecio() ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
        </form>
    </div>
</body>

</html>