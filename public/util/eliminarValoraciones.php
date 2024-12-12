<?php
session_start();
require_once '../src/Pasteleria.php';

// Verificar si el usuario es admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

$pasteleria = new Pasteleria();

// Eliminar todas las valoraciones
if ($pasteleria->eliminarTodasValoraciones()) {
    header("Location: mainAdmin.php?success=Todas las valoraciones han sido eliminadas correctamente");
} else {
    header("Location: mainAdmin.php?error=No se pudieron eliminar las valoraciones");
}
exit;
?>