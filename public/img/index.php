<?php
session_start();

// Verificar si existe una cookie para recordar al usuario
$last_user = $_COOKIE['last_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastelería - Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <h1>Bienvenido a la Pastelería</h1>
    <form action="login.php" method="POST">
        <label for="username">Usuario:</label>
        <!-- Prellenar con el último usuario guardado en la cookie -->
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($last_user) ?>" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="remember">Recordarme:</label>
        <input type="checkbox" id="remember" name="remember" <?= $last_user ? 'checked' : '' ?>>
        <br>
        <button type="submit">Iniciar sesión</button>
        <?php
        // Mostrar mensaje de error si existe
        if (isset($_SESSION['error'])) {
            echo "<p style='color:red;'>{$_SESSION['error']}</p>";
            unset($_SESSION['error']);
        }
        ?>
    </form>
</body>

</html>