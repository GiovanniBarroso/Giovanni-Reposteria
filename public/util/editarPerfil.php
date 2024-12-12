<?php

session_start();
require_once '../src/Pasteleria.php';


if (!isset($_SESSION['user'])) {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

$userId = $_SESSION['user_id'];
$pasteleria = new Pasteleria();
$cliente = $pasteleria->buscarClientePorId($userId);


if (!$cliente) {
    header("Location: main.php?error=Cliente no encontrado");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars($_POST['nombre']);
    $usuario = htmlspecialchars($_POST['usuario']);
    $passwordActual = $_POST['passwordActual'] ?? "";
    $passwordNueva = $_POST['passwordNueva'] ?? "";

    if (!empty($passwordActual) && !empty($passwordNueva)) {
        if (password_verify($passwordActual, $cliente['password'])) {
            $passwordHashed = password_hash($passwordNueva, PASSWORD_DEFAULT);
            $updated = $pasteleria->actualizarClienteConPassword($userId, $nombre, $usuario, $passwordHashed);
        } else {
            $error = "La contraseña actual no es correcta.";
            $updated = false;
        }
    } else {
        $updated = $pasteleria->actualizarCliente($userId, $nombre, $usuario, $cliente['rol']);
    }

    if ($updated) {
        header("Location: main.php?success=Perfil actualizado correctamente");
        exit;
    } else {
        $error = $error ?: "Error al actualizar el perfil. Por favor, verifica los datos.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles3.css">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>


<body>
    <div class="container mt-5">
        <div class="form-container">
            <h1 class="form-title">Editar Perfil</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger text-center"> <?= $error ?> </div>
            <?php endif; ?>


            <form method="POST">
                <!-- Nombre -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $cliente['nombre'] ?>"
                        required>
                </div>


                <!-- Usuario -->
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario"
                        value="<?= $cliente['usuario'] ?>" required>
                </div>


                <!-- Contraseña actual -->
                <div class="mb-3">
                    <label for="passwordActual" class="form-label">Contraseña Actual</label>
                    <input type="password" class="form-control" id="passwordActual" name="passwordActual"
                        placeholder="Introduce tu contraseña actual">
                </div>


                <!-- Nueva contraseña -->
                <div class="mb-3">
                    <label for="passwordNueva" class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control" id="passwordNueva" name="passwordNueva"
                        placeholder="Introduce una nueva contraseña (opcional)">
                </div>


                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary w-100 me-2">Guardar Cambios</button>
                    <a href="main.php" class="btn btn-secondary w-100">Cancelar</a>
                </div>


            </form>
            <!-- Botón de eliminar cuenta -->
            <div class="mt-4">
                <button class="btn btn-danger w-100" onclick="confirmarEliminacion()">Eliminar cuenta</button>
            </div>
        </div>
    </div>
</body>
<script>
    function confirmarEliminacion() {
        if (confirm("¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.")) {
            window.location.href = "eliminarClienteUser.php";
        }
    }
</script>

</html>