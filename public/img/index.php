<?php
session_start();

// Capturar el mensaje de error y eliminarlo después de usarlo
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['error']); // Limpia el mensaje de error

// Verificar si existe una cookie para recordar al usuario
$last_user = $_COOKIE['last_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pastelería</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h2><i class="bi bi-person-circle"></i> Iniciar Sesión</h2>
                    </div>
                    <div class="card-body">
                        <!-- Mostrar mensaje de error si existe -->
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger text-center">
                                <?= htmlspecialchars($error_message) ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= htmlspecialchars($last_user) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-50">
                                    <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                                </button>
                                <a href="registro.php" class="btn btn-secondary w-50">
                                    <i class="bi bi-person-plus"></i> Registrarse
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>