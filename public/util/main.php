<?php
session_start();
require_once __DIR__ . '/../src/Pasteleria.php';

if (!isset($_SESSION['user']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: index.php");
    exit;
}



$clienteId = $_SESSION['user_id']; // Obtén el ID del usuario logueado

try {
    $db = Database::getConnection();
    $query = "SELECT p.id AS pedido_id, p.fecha, SUM(dp.cantidad * dp.precio_unitario) AS total
              FROM pedidos p
              JOIN detalle_pedidos dp ON p.id = dp.pedido_id
              WHERE p.cliente_id = :cliente_id
              GROUP BY p.id, p.fecha
              ORDER BY p.fecha DESC
              LIMIT 3"; // Limitar a los últimos 3 pedidos
    $stmt = $db->prepare($query);
    $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
    $stmt->execute();
    $pedidosRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pedidosRecientes = [];
}

if (isset($_GET['success'])): ?>
    <div class="alert alert-success text-center">
        <?= htmlspecialchars($_GET['success']) ?>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger text-center">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif;

$pasteleria = new Pasteleria();
$productos = $pasteleria->obtenerProductos();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-cupcake"></i> Pastelería</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="historialPedidos.php">Historial</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Carrito -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <button id="toggle-dark-mode" class="btn btn-sm btn-outline-light">Modo Oscuro</button>
                        </li>
                    </ul>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarCart" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-cart"></i> Carrito
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3" id="cartDropdown">
                            <p class="text-muted">El carrito está vacío.</p>
                        </ul>
                    </li>
                    <!-- Usuario -->
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container mt-4">
        <h1 class="text-center mb-4"><i class="bi bi-person-circle"></i> Bienvenido,
            <?= htmlspecialchars($_SESSION['user']) ?>
        </h1>

        <!-- Productos -->
        <div class="row">
            <h2 class="text-primary mb-4"><i class="bi bi-bag"></i> Productos Disponibles</h2>
            <?php foreach ($productos as $producto): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <!-- Imagen de Producto -->
                        <img src="../img/productos/<?= $producto->getId() ?>.jpg" class="card-img-top"
                            alt="<?= htmlspecialchars($producto->getNombre()) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($producto->getNombre()) ?></h5>
                            <p class="card-text">Precio: <?= number_format($producto->getPrecio(), 2) ?>€</p>
                            <p class="card-text">Categoría: <?= htmlspecialchars($producto->getCategoria()) ?></p>
                            <button class="btn btn-success w-100 add-to-cart" data-id="<?= $producto->getId() ?>"
                                data-price="<?= $producto->getPrecio() ?>">

                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/carrito.js" defer></script>


    <script>
        document.getElementById("toggle-dark-mode").addEventListener("click", () => {
            document.body.classList.toggle("bg-dark");
            document.body.classList.toggle("text-light");

            const navbar = document.querySelector(".navbar");
            navbar.classList.toggle("navbar-dark");
            navbar.classList.toggle("navbar-light");
            navbar.classList.toggle("bg-light");
            navbar.classList.toggle("bg-dark");

            const button = document.getElementById("toggle-dark-mode");
            button.textContent = document.body.classList.contains("bg-dark") ? "Modo Claro" : "Modo Oscuro";
        });
    </script>

</body>

</html>