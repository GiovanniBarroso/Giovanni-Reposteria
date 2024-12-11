<?php

session_start();
require_once '../src/Pasteleria.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$clienteId = $_SESSION['user_id'];
$productoId = intval($_POST['producto_id']);
$valoracion = trim($_POST['valoracion']);
$puntuacion = intval($_POST['puntuacion']);

$pasteleria = new Pasteleria();


if ($pasteleria->puedeValorar($clienteId, $productoId)) {
    if ($pasteleria->guardarValoracion($productoId, $clienteId, $valoracion, $puntuacion)) {
        header("Location: main.php?success=Valoración enviada correctamente");
        exit;
    } else {
        header("Location: main.php?error=Error al enviar la valoración");
        exit;
    }
} else {
    header("Location: main.php?error=No puedes valorar este producto");
    exit;
}