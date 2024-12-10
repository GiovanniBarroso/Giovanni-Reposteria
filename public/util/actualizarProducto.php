<?php
session_start();

if ($_SESSION['user'] !== 'admin') {
    header("Location: index.php?error=Acceso no autorizado");
    exit;
}

require_once '../src/Pasteleria.php';
require_once '../src/Bollo.php';
require_once '../src/Chocolate.php';
require_once '../src/Tarta.php';

if (!isset($_POST['id'], $_POST['nombre'], $_POST['precio'], $_POST['categoria'], $_POST['descripcion'])) {
    header("Location: mainAdmin.php?error=Faltan datos para actualizar el producto");
    exit;
}

$id = intval($_POST['id']);
$nombre = $_POST['nombre'];
$precio = floatval($_POST['precio']);
$categoria = $_POST['categoria'];
$descripcion = $_POST['descripcion'] ?? '';

$pasteleria = new Pasteleria();
$producto = $pasteleria->buscarProductoPorId($id);

if (!$producto) {
    header("Location: mainAdmin.php?error=Producto no encontrado");
    exit;
}

// Actualizar los valores comunes
$producto->setNombre($nombre);
$producto->setPrecio($precio);
$producto->setCategoria($categoria);
$producto->setDescripcion($descripcion);


// Manejo de la imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $nombreArchivo = "{$producto->getId()}.jpg"; // Usamos el ID del producto como nombre
    $rutaDestino = "../img/" . $nombreArchivo;

    // Mover la imagen subida a la carpeta img
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
        header("Location: mainAdmin.php?error=Error al subir la imagen");
        exit;
    }
}

// Actualizar campos específicos según el tipo de producto
if ($producto instanceof Bollo) {
    $relleno = $_POST['relleno'] ?? '';
    $producto = new Bollo($id, $nombre, $precio, $descripcion, $categoria, $relleno);
} elseif ($producto instanceof Chocolate) {
    $porcentajeCacao = floatval($_POST['porcentajeCacao'] ?? 0);
    $peso = floatval($_POST['peso'] ?? 0);
    $producto = new Chocolate($id, $nombre, $precio, $descripcion, $categoria, $porcentajeCacao, $peso);
} elseif ($producto instanceof Tarta) {
    $rellenos = explode(',', $_POST['rellenos'] ?? '');
    $numPisos = intval($_POST['numPisos'] ?? 1);
    $minComensales = intval($_POST['minComensales'] ?? 2);
    $maxComensales = intval($_POST['maxComensales'] ?? 2);
    $producto = new Tarta($id, $nombre, $precio, $descripcion, $categoria, $rellenos, $numPisos, $minComensales, $maxComensales);
}

// Guardar los cambios en la base de datos
if ($pasteleria->actualizarProducto($id, $producto)) {
    header("Location: mainAdmin.php?success=Producto actualizado correctamente");
} else {
    header("Location: mainAdmin.php?error=No se pudo actualizar el producto");
}
?>