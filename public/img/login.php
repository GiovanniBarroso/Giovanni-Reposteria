<?php
require_once __DIR__ . '/../db/Database.php';

session_start();

$db = Database::getConnection();

// Obtener datos del formulario
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Buscar usuario en la base de datos
$query = "SELECT * FROM clientes WHERE usuario = :username";
$stmt = $db->prepare($query);
$stmt->bindValue(':username', $username);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Comparar la contraseña usando MD5
if ($user && md5($password) === $user['password']) {
    // Iniciar sesión
    $_SESSION['user'] = $username;

    // Configurar cookie si se marca "Recordarme"
    if ($remember) {
        setcookie('last_user', $username, time() + (86400 * 30), "/");
    } else {
        setcookie('last_user', '', time() - 3600, "/");
    }

    // Redirigir según el usuario
    if ($username === 'admin') {
        header("Location: mainAdmin.php");
    } else {
        header("Location: main.php");
    }
    exit;
} else {
    // Error de login
    $_SESSION['error'] = "Credenciales incorrectas. Por favor, inténtelo de nuevo.";
    header("Location: index.php");
    exit;
}
?>