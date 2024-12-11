<?php
require_once __DIR__ . '/../src/Pasteleria.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "El nombre de usuario y la contraseña son obligatorios.";
        header("Location: index.php");
        exit;
    }

    try {
        $pasteleria = new Pasteleria();
        $user = $pasteleria->validarCredenciales($username, $password);

        if ($user) {
            $_SESSION['user'] = $user['usuario'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];

            if ($remember) {
                setcookie('last_user', $username, time() + (86400 * 30), "/");
            }

            header("Location: " . ($user['rol'] === 'admin' ? 'mainAdmin.php' : 'main.php'));
        } else {
            $_SESSION['error'] = "Credenciales inválidas.";
            header("Location: index.php");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al iniciar sesión: " . $e->getMessage();
        header("Location: index.php");
    }
    exit;
}
?>