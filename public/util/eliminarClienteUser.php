<?php
session_start();
require_once '../src/Pasteleria.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$pasteleria = new Pasteleria();
$userId = $_SESSION['user_id'];

// Eliminar la cuenta
if ($pasteleria->eliminarCliente($userId)) {
    session_destroy();
    header("Location: index.php?success=Cuenta eliminada correctamente.");
    exit;
} else {
    header("Location: editarPerfil.php?error=No se pudo eliminar la cuenta.");
    exit;
}
?>