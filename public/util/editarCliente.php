<?php

require_once '../src/Pasteleria.php';

$pasteleria = new Pasteleria();


// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];

    // Actualizar cliente
    $exito = $pasteleria->actualizarCliente($id, $nombre, $usuario, $rol);

    if ($exito) {
        header("Location: mainAdmin.php?success=Cliente actualizado correctamente");
    } else {
        header("Location: mainAdmin.php?error=No se pudo actualizar el cliente");
    }
    exit;
} else {

    // Obtener los datos del cliente para mostrar en el formulario
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $cliente = $pasteleria->buscarClientePorId($id);

        if (!$cliente) {
            header("Location: mainAdmin.php?error=Cliente no encontrado");
            exit;
        }
    } else {
        header("Location: mainAdmin.php?error=ID de cliente no especificado");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center">Editar Cliente</h1>
        <form method="post" action="editarCliente.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($cliente['id']) ?>">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario"
                    value="<?= htmlspecialchars($cliente['usuario']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-control" id="rol" name="rol" required>
                    <option value="cliente" <?= $cliente['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                    <option value="admin" <?= $cliente['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="mainAdmin.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>