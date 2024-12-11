<?php

session_start();
require_once '../src/Pasteleria.php';


if ($_SESSION['user'] !== 'admin') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: mainAdmin.php?error=Falta el ID del producto");
    exit;
}


$id = intval($_GET['id']);
$pasteleria = new Pasteleria();



if ($pasteleria->eliminarProducto($id) === true) {
    header("Location: mainAdmin.php?success=Producto eliminado correctamente");
} else {
    header("Location: mainAdmin.php?error=No se pudo eliminar el producto porque está relacionado con otros registros.");
}
exit;