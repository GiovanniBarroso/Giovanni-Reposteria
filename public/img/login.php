<?php
require_once __DIR__ . '/../db/Database.php';

session_start();

try {
    $db = Database::getConnection();

    // Obtener datos del formulario
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    // Buscar usuario en la base de datos
    $query = "SELECT * FROM clientes WHERE usuario = :username";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Iniciar sesión
            $_SESSION['user'] = $username;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];

            // Configurar cookie si se marca "Recordarme"
            if ($remember) {
                setcookie('last_user', $username, time() + (86400 * 30), "/");
            } else {
                setcookie('last_user', '', time() - 3600, "/");
            }

            // Redirigir según el rol del usuario
            if ($user['rol'] === 'admin') {
                header("Location: mainAdmin.php");
            } else {
                header("Location: main.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Contraseña incorrecta.";
        }
    } else {
        $_SESSION['error'] = "Usuario no encontrado.";
    }

    header("Location: index.php");
    exit;

} catch (PDOException $e) {
    // Manejo de errores en la base de datos
    $_SESSION['error'] = "Error al iniciar sesión: " . $e->getMessage();
    header("Location: index.php");
    exit;
}
?>