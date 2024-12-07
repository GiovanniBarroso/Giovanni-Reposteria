<?php
session_start();
require_once '../db/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $rol = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin' && isset($_POST['rol'])) ? $_POST['rol'] : 'cliente';

    try {
        $db = Database::getConnection();

        $query = "INSERT INTO clientes (nombre, usuario, password, rol) VALUES (:nombre, :usuario, :password, :rol)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':rol', $rol);

        $stmt->execute();

        header('Location: login.php?success=Usuario registrado correctamente');
        exit;
    } catch (PDOException $e) {
        header('Location: registro.php?error=Error al registrar usuario: ' . $e->getMessage());
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h1 class="text-center">Registro de Usuario</h1>
        <form action="registro.php" method="POST" class="p-4 bg-white shadow rounded">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select class="form-select" id="rol" name="rol">
                        <option value="cliente" selected>Cliente</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100">Registrar</button>
        </form>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</body>

</html>