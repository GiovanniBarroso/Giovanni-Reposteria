<?php
session_start();
require_once '../db/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $rol = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin' && isset($_POST['rol'])) ? $_POST['rol'] : 'cliente';

    // Validar que no haya campos vacíos
    if (empty($nombre) || empty($usuario) || empty($password)) {
        header('Location: registro.php?error=Todos los campos son obligatorios');
        exit;
    }

    try {
        $db = Database::getConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO clientes (nombre, usuario, password, rol) VALUES (:nombre, :usuario, :password, :rol)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':rol', $rol);

        $stmt->execute();

        header('Location: login.php?success=Usuario registrado correctamente');
        exit;
    } catch (PDOException $e) {
        // Redirigir con un mensaje de error específico
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
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card w-100 shadow-lg" style="max-width: 500px;">
            <div class="card-body">
                <h1 class="card-title text-center text-primary mb-4">Registro de Usuario</h1>

                <!-- Mensajes de error o éxito -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario de registro -->
                <form action="registro.php" method="POST">
                    <!-- Nombre Completo -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            placeholder="Introduce tu nombre completo" required>
                    </div>

                    <!-- Nombre de Usuario -->
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario"
                            placeholder="Crea un nombre de usuario" required>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Introduce una contraseña segura" required>
                    </div>

                    <!-- Botón de registro -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>

                <!-- Enlace de volver -->
                <div class="text-center mt-3">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>