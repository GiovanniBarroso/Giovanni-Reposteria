<?php
session_start();

if ($_SESSION['user'] !== 'admin') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

require_once '../src/Pasteleria.php';

if (!isset($_POST['id'], $_POST['nombre'], $_POST['precio'])) {
    header("Location: mainAdmin.php?error=Faltan datos para actualizar el producto");
    exit;
}

$id = intval($_POST['id']);
$nombre = $_POST['nombre'];
$precio = floatval($_POST['precio']);

$pasteleria = new Pasteleria();
$producto = $pasteleria->buscarProductoPorId($id);

if (!$producto) {
    header("Location: mainAdmin.php?error=Producto no encontrado");
    exit;
}

$producto->setNombre($nombre);
$producto->setPrecio($precio);

if ($pasteleria->actualizarProducto($id, $producto)) {
    header("Location: mainAdmin.php?success=Producto actualizado correctamente");
} else {
    header("Location: mainAdmin.php?error=No se pudo actualizar el producto");
}
?>