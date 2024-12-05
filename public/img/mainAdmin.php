<?php
session_start();

if ($_SESSION['user'] !== 'admin') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

// Mostrar listado de clientes y productos
require_once '../src/Pasteleria.php';

$pasteleria = new Pasteleria();
$productos = $pasteleria->obtenerProductos();

echo "<h1>Bienvenido, administrador</h1>";
echo "<h2>Listado de Productos:</h2>";
foreach ($productos as $producto) {
    echo "<p>{$producto->muestraResumen()}</p>";
}
?>
<a href="logout.php">Cerrar sesión</a>