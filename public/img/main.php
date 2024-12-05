<?php
session_start();

if ($_SESSION['user'] !== 'usuario') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

// Mostrar dulces comprados por el cliente
require_once '../src/Cliente.php';

echo "<h1>Bienvenido, {$_SESSION['user']}</h1>";
echo "<p>Aquí están tus pedidos:</p>";

// Simulación de pedidos
echo "<ul><li>Donut Glaseado</li><li>Chocolate con leche</li></ul>";
?>
<a href="logout.php">Cerrar sesión</a>