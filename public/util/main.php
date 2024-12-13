<?php

session_start();
require_once __DIR__ . '/../src/Pasteleria.php';


if (!isset($_SESSION['user']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: index.php");
    exit;
}


$clienteId = $_SESSION['user_id'];
$pasteleria = new Pasteleria();


// Obtener pedidos recientes y productos
$pedidosRecientes = $pasteleria->obtenerPedidosRecientes($clienteId);
$productos = $pasteleria->obtenerProductos();


// Mostrar mensajes de éxito o error
if (isset($_GET['success'])) {
    echo "<div class='alert alert-success text-center'>" . htmlspecialchars($_GET['success']) . "</div>";
    header("Refresh: 1.2; URL=main.php");
}

if (isset($_GET['error'])) {
    echo "<div class='alert alert-danger text-center'>" . htmlspecialchars($_GET['error']) . "</div>";
    header("Refresh: 1.2; URL=main.php");
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastelería - Bienvenido</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../css/styles.css">
</head>


<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-cookie"></i> Pastelería</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">

                    <!-- Perfil -->
                    <li class="nav-item">
                        <a class="nav-link" href="editarPerfil.php"><i class="bi bi-person"></i> Perfil</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="historialPedidos.php">Historial</a>
                    </li>

                </ul>


                <ul class="navbar-nav">
                    <!-- Modo Oscuro -->
                    <li class="nav-item me-2">
                        <button id="toggle-dark-mode" class="btn btn-sm btn-outline-light">Modo Oscuro</button>
                    </li>


                    <!-- Carrito -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarCart" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-cart"></i> Carrito <span id="cartCount" class="badge bg-warning">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3" id="cartDropdown">
                            <p class="text-muted">El carrito está vacío.</p>
                        </ul>
                    </li>


                    <!-- Cerrar Sesión -->
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>




    <!-- Contenido Principal -->
    <div class="container mt-4">

        <!-- Sección de bienvenida -->
        <div class="welcome-section mb-4">
            <h1><i class="bi bi-person-circle"></i> Bienvenido, <?= htmlspecialchars($_SESSION['user']) ?></h1>
            <p>Explora nuestra selección de productos deliciosos y realiza tus pedidos al instante.</p>
        </div>


        <!-- Productos -->
        <div class="row">
            <h2 class="products-title mb-4"><i class="bi bi-bag"></i> Productos Disponibles</h2>


            <?php foreach ($productos as $producto): ?>
                <div class="col-md-4 col-sm-6 mb-4 d-flex align-items-stretch">
                    <div class="card product-card">
                        <img src="../img/<?= $producto->getId() ?>.jpg" class="card-img-top img-fluid product-image"
                            alt="<?= htmlspecialchars($producto->getNombre()) ?>">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($producto->getNombre()) ?></h5>
                            <p class="card-text">Categoría: <?= htmlspecialchars($producto->getCategoria()) ?></p>
                            <p class="card-text fw-bold">Precio: <?= number_format($producto->getPrecio(), 2) ?>€</p>
                            <button class="btn btn-success mt-auto w-100 add-to-cart" data-id="<?= $producto->getId() ?>">
                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>



                            <!-- Formulario de valoración -->
                            <?php if ($pasteleria->puedeValorar($clienteId, $producto->getId())): ?>
                                <form method="post" action="valorarProducto.php" class="mt-3">
                                    <input type="hidden" name="producto_id" value="<?= $producto->getId() ?>">

                                    <div class="mb-2">
                                        <label for="puntuacion-<?= $producto->getId() ?>" class="form-label">Puntuación:</label>
                                        <select name="puntuacion" id="puntuacion-<?= $producto->getId() ?>" class="form-select"
                                            required>
                                            <option value="1">1 ⭐</option>
                                            <option value="2">2 ⭐⭐</option>
                                            <option value="3">3 ⭐⭐⭐</option>
                                            <option value="4">4 ⭐⭐⭐⭐</option>
                                            <option value="5">5 ⭐⭐⭐⭐⭐</option>
                                        </select>
                                    </div>

                                    <textarea name="valoracion" class="form-control mb-2" rows="2"
                                        placeholder="Escribe tu valoración" required></textarea>
                                    <button type="submit" class="btn btn-secondary btn-sm w-100">Enviar valoración</button>

                                </form>
                            <?php else: ?>
                                <p class="text-muted mt-3">Compra este producto para poder valorarlo.</p>
                            <?php endif; ?>




                            <!-- Mostrar valoraciones -->
                            <hr>
                            <h6>Valoraciones:</h6>
                            <ul class="list-unstyled">
                                <?php foreach ($pasteleria->obtenerValoraciones($producto->getId()) as $valoracion): ?>
                                    <li>
                                        <strong><?= htmlspecialchars($valoracion['nombre']) ?></strong>
                                        (<?= htmlspecialchars($valoracion['puntuacion']) ?>/5) ⭐</i> :
                                        <?= htmlspecialchars($valoracion['valoracion']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- Modal de Política de Privacidad -->
    <div class="modal fade" id="privacyPolicyModal" tabindex="-1" aria-labelledby="privacyPolicyLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyPolicyLabel">Política de Privacidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        En nuestra pastelería respetamos su privacidad. Toda la información proporcionada será utilizada
                        exclusivamente para procesar sus pedidos y mejorar su experiencia. No compartimos su información
                        con terceros.
                        Puede contactarnos para más información o para ejercer sus derechos según lo dispuesto por la
                        ley.
                    </p>
                    <p>Para cualquier consulta, contáctenos a través de <strong>privacidad@pasteleria.com</strong>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <footer class="footer text-center">
        <p>© 2024 Pastelería. Todos los derechos reservados.
            <a href="#" data-bs-toggle="modal" data-bs-target="#privacyPolicyModal">Política de Privacidad</a>
        </p>
    </footer>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/carrito.js" defer></script>
    <script>
        document.getElementById("toggle-dark-mode").addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");

            const button = document.getElementById("toggle-dark-mode");
            button.textContent = document.body.classList.contains("dark-mode") ? "Modo Claro" : "Modo Oscuro";

            localStorage.setItem("dark-mode", document.body.classList.contains("dark-mode"));
        });



        // Aplicar preferencia de modo oscuro al cargar la página
        window.addEventListener("DOMContentLoaded", () => {
            if (localStorage.getItem("dark-mode") === "true") {
                document.body.classList.add("dark-mode");

                // Ajustar el texto del botón
                const button = document.getElementById("toggle-dark-mode");
                button.textContent = "Modo Claro";
            }
        });
    </script>

</body>

</html>