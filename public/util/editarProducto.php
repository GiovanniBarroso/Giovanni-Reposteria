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
        <form action="actualizarProducto.php" method="POST" enctype="multipart/form-data"
            class="shadow p-4 bg-white rounded">
            <input type="hidden" name="id" value="<?= $id ?>">

            <!-- Nombre -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    value="<?= htmlspecialchars($producto->getNombre()) ?>" required>
            </div>

            <!-- Precio -->
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01"
                    value="<?= htmlspecialchars($producto->getPrecio()) ?>" required>
            </div>

            <!-- Categoría -->
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <input type="text" class="form-control" id="categoria" name="categoria"
                    value="<?= htmlspecialchars($producto->getCategoria()) ?>" required>
            </div>

            <!-- Descripción -->
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion"
                    name="descripcion"><?= htmlspecialchars($producto->getDescripcion()) ?></textarea>
            </div>

            <!-- Imagen -->
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen del Producto</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                <?php
                $rutaImagen = "../img/{$producto->getId()}.jpg";
                if (file_exists($rutaImagen)): ?>
                    <p class="mt-2">Imagen actual:</p>
                    <img src="<?= $rutaImagen ?>" alt="Imagen del producto" style="max-width: 150px;">
                <?php endif; ?>
            </div>

            <!-- Tipo específico -->
            <?php if ($producto instanceof Bollo): ?>
                <div id="tipo-bollo">
                    <div class="mb-3">
                        <label for="relleno" class="form-label">Relleno</label>
                        <input type="text" class="form-control" id="relleno" name="relleno"
                            value="<?= htmlspecialchars($producto->getRelleno()) ?>">
                    </div>
                </div>
            <?php elseif ($producto instanceof Chocolate): ?>
                <div id="tipo-chocolate">
                    <div class="mb-3">
                        <label for="porcentajeCacao" class="form-label">Porcentaje de Cacao</label>
                        <input type="number" class="form-control" id="porcentajeCacao" name="porcentajeCacao" step="0.1"
                            value="<?= htmlspecialchars($producto->getPorcentajeCacao()) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="peso" class="form-label">Peso (g)</label>
                        <input type="number" class="form-control" id="peso" name="peso"
                            value="<?= htmlspecialchars($producto->getPeso()) ?>">
                    </div>
                </div>
            <?php elseif ($producto instanceof Tarta): ?>
                <div id="tipo-tarta">
                    <div class="mb-3">
                        <label for="rellenos" class="form-label">Rellenos (separados por comas)</label>
                        <input type="text" class="form-control" id="rellenos" name="rellenos"
                            value="<?= htmlspecialchars(implode(',', $producto->getRellenos())) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="numPisos" class="form-label">Número de Pisos</label>
                        <input type="number" class="form-control" id="numPisos" name="numPisos" min="1"
                            value="<?= htmlspecialchars($producto->getNumPisos()) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="minComensales" class="form-label">Mínimo de Comensales</label>
                        <input type="number" class="form-control" id="minComensales" name="minComensales" min="1"
                            value="<?= htmlspecialchars($producto->getMinNumComensales()) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="maxComensales" class="form-label">Máximo de Comensales</label>
                        <input type="number" class="form-control" id="maxComensales" name="maxComensales" min="1"
                            value="<?= htmlspecialchars($producto->getMaxNumComensales()) ?>">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary w-100 me-2">Guardar Cambios</button>
                <a href="mainAdmin.php" class="btn btn-secondary w-100">Volver</a>
            </div>
        </form>
    </div>
</body>

</html>