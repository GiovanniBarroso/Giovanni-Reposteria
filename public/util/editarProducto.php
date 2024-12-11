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
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../css/styles2.css">

</head>

<body>
    <div class="container mt-5">
        <!-- Encabezado -->
        <h1 class="text-center form-title"><i class="bi bi-pencil-square"></i> Editar Producto</h1>

        <!-- Formulario -->
        <form action="actualizarProducto.php" method="POST" enctype="multipart/form-data" class="form-section">
            <input type="hidden" name="id" value="<?= $id ?>">

            <!-- Información General -->
            <h2 class="form-title">Información General</h2>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del producto"
                    value="<?= htmlspecialchars($producto->getNombre()) ?>" required>
            </div>

            <div class="mb-3">
                <label for="precio" class="form-label">Precio (€)</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" placeholder="Precio"
                    value="<?= htmlspecialchars($producto->getPrecio()) ?>" required>
            </div>

            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <input type="text" class="form-control" id="categoria" name="categoria" placeholder="Categoría"
                    value="<?= htmlspecialchars($producto->getCategoria()) ?>" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                    placeholder="Descripción del producto"><?= htmlspecialchars($producto->getDescripcion()) ?></textarea>
            </div>

            <!-- Imagen -->
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen del Producto</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                <?php if (file_exists("../img/{$producto->getId()}.jpg")): ?>
                    <div class="mt-3">
                        <p>Imagen actual:</p>
                        <img src="../img/<?= $producto->getId() ?>.jpg" class="img-preview" alt="Imagen del producto">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Detalles específicos -->
            <h2 class="form-title">Detalles Específicos</h2>
            <div class="form-divider"></div>

            <?php if ($producto instanceof Bollo): ?>
                <div class="mb-3">
                    <label for="relleno" class="form-label">Relleno</label>
                    <input type="text" class="form-control" id="relleno" name="relleno" placeholder="Relleno"
                        value="<?= htmlspecialchars($producto->getRelleno()) ?>">
                </div>
            <?php elseif ($producto instanceof Chocolate): ?>
                <div class="mb-3">
                    <label for="porcentajeCacao" class="form-label">Porcentaje de Cacao (%)</label>
                    <input type="number" class="form-control" id="porcentajeCacao" name="porcentajeCacao" step="0.1"
                        placeholder="Porcentaje de cacao" value="<?= htmlspecialchars($producto->getPorcentajeCacao()) ?>">
                </div>
                <div class="mb-3">
                    <label for="peso" class="form-label">Peso (g)</label>
                    <input type="number" class="form-control" id="peso" name="peso" placeholder="Peso en gramos"
                        value="<?= htmlspecialchars($producto->getPeso()) ?>">
                </div>
            <?php elseif ($producto instanceof Tarta): ?>
                <div class="mb-3">
                    <label for="rellenos" class="form-label">Rellenos (separados por comas)</label>
                    <input type="text" class="form-control" id="rellenos" name="rellenos" placeholder="Rellenos"
                        value="<?= htmlspecialchars(implode(',', $producto->getRellenos())) ?>">
                </div>
                <div class="mb-3">
                    <label for="numPisos" class="form-label">Número de Pisos</label>
                    <input type="number" class="form-control" id="numPisos" name="numPisos" placeholder="Pisos"
                        value="<?= htmlspecialchars($producto->getNumPisos()) ?>">
                </div>
                <div class="mb-3">
                    <label for="minComensales" class="form-label">Mínimo de Comensales</label>
                    <input type="number" class="form-control" id="minComensales" name="minComensales"
                        placeholder="Comensales mínimos" value="<?= htmlspecialchars($producto->getMinNumComensales()) ?>">
                </div>
                <div class="mb-3">
                    <label for="maxComensales" class="form-label">Máximo de Comensales</label>
                    <input type="number" class="form-control" id="maxComensales" name="maxComensales"
                        placeholder="Comensales máximos" value="<?= htmlspecialchars($producto->getMaxNumComensales()) ?>">
                </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary w-100 me-2">Guardar Cambios</button>
                <a href="mainAdmin.php" class="btn btn-secondary w-100">Volver</a>
            </div>
        </form>
    </div>
</body>

</html>