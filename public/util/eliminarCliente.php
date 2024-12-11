<?php

require_once '../src/Pasteleria.php';
$pasteleria = new Pasteleria();


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $exito = $pasteleria->eliminarCliente($id);

    if ($exito) {
        header("Location: mainAdmin.php?success=Cliente eliminado correctamente");
    } else {
        $error = $_SESSION['error'] ?? "No se pudo eliminar el cliente";
        header("Location: mainAdmin.php?error=" . urlencode($error));
    }
    exit;
} else {
    header("Location: mainAdmin.php?error=ID de cliente no especificado");
    exit;
}


?>