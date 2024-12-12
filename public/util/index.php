<?php

session_start();
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

// Limpiar los mensajes de la sesión
unset($_SESSION['success']);
unset($_SESSION['error']);
$last_user = $_COOKIE['last_user'] ?? '';
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pastelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card w-100 shadow-lg" style="max-width: 500px;">
            <div class="card-body">
                <h1 class="card-title text-center text-primary mb-4">Iniciar Sesión</h1>

                <!-- Formulario de login -->
                <form action="login.php" method="POST">

                    <!-- Usuario -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Tu usuario"
                            required>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Tu contraseña" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>


                    <!-- Recordarme -->
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label for="remember" class="form-check-label">Recordarme</label>
                    </div>

                    <!-- Botón de inicio de sesión -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                    </div>

                </form>

                <!-- Enlace de registro -->
                <div class="text-center mt-3">
                    <a href="registro.php" class="btn btn-secondary">
                        <i class="bi bi-person-plus"></i> Registrarse
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de éxito -->
    <?php if ($success): ?>
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="successModalLabel"><i class="bi bi-check-circle"></i> Éxito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= htmlspecialchars($success) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <!-- Modal de error -->
    <?php if ($error): ?>
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="errorModalLabel"><i class="bi bi-exclamation-circle"></i> Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= htmlspecialchars($error) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar el modal automáticamente si hay un mensaje
        document.addEventListener('DOMContentLoaded', () => {
            <?php if ($success): ?>
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            <?php endif; ?>

            <?php if ($error): ?>
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            <?php endif; ?>
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const passwordInput = document.getElementById("password");
            const togglePasswordButton = document.getElementById("togglePassword");

            togglePasswordButton.addEventListener("click", () => {
                const isPassword = passwordInput.type === "password";
                passwordInput.type = isPassword ? "text" : "password";
                togglePasswordButton.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            });
        });
    </script>


</body>

</html>