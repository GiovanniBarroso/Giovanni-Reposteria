<?php
require_once '../src/Pasteleria.php';

$pasteleria = new Pasteleria();

// Verificar si se proporcionó el ID del cliente
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Eliminar cliente
    $exito = $pasteleria->eliminarCliente($id);

    if ($exito) {
        header("Location: mainAdmin.php?success=Cliente eliminado correctamente");
    } else {
        header("Location: mainAdmin.php?error=No se pudo eliminar el cliente");
    }
    exit;
} else {
    header("Location: mainAdmin.php?error=ID de cliente no especificado");
    exit;
}
?>