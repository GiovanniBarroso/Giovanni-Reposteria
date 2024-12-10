<?php
require_once __DIR__ . '/../db/Database.php'; // Ruta a la conexión con la base de datos

session_start();

try {
    // Conexión a la base de datos
    $db = Database::getConnection();

    // Obtener datos del formulario
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "El nombre de usuario y la contraseña son obligatorios.";
        header("Location: index.php");
        exit;
    }

    // Buscar usuario en la base de datos
    $query = "SELECT * FROM clientes WHERE usuario = :username";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Credenciales válidas: iniciar sesión
            $_SESSION['user'] = $user['usuario'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];

            // Configurar cookie si se selecciona "Recordarme"
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

    // Redirigir a la página de inicio si hay un error
    header("Location: index.php");
    exit;

} catch (PDOException $e) {
    // Manejo de errores en la base de datos
    $_SESSION['error'] = "Error al iniciar sesión: " . $e->getMessage();
    header("Location: index.php");
    exit;
}
?>