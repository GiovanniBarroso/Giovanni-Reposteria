<?php
require_once '../src/Pasteleria.php';
require_once '../src/Tarta.php';
require_once '../src/Bollo.php';
require_once '../src/Chocolate.php';

$pasteleria = new Pasteleria();

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = (float) $_POST['precio'];
    $categoria = $_POST['categoria'];
    $tipo = $_POST['tipo'];
    $descripcion = $_POST['descripcion'] ?? '';


    // Crear un producto basado en el tipo
    $producto = null;


    // Crear un producto basado en el tipo
    switch ($tipo) {
        case 'Bollo':
            $relleno = $_POST['relleno'] ?? '';
            $producto = new Bollo(null, $nombre, $precio, $descripcion, $categoria, $relleno);
            break;

        case 'Chocolate':
            $porcentajeCacao = (float) ($_POST['porcentajeCacao'] ?? 0);
            $peso = (float) ($_POST['peso'] ?? 0);
            $producto = new Chocolate(null, $nombre, $precio, $descripcion, $categoria, $porcentajeCacao, $peso);
            break;

        case 'Tarta':
            $rellenos = explode(',', $_POST['rellenos'] ?? '');
            $numPisos = (int) ($_POST['numPisos'] ?? 1);
            $minComensales = (int) ($_POST['minComensales'] ?? 2);
            $maxComensales = (int) ($_POST['maxComensales'] ?? 2);
            $producto = new Tarta(null, $nombre, $precio, $descripcion, $categoria, $rellenos, $numPisos, $minComensales, $maxComensales);
            break;

        default:
            header("Location: mainAdmin.php?error=Tipo de producto no válido");
            exit;
    }



    // Guardar el producto en la base de datos
    $exito = $pasteleria->guardarProducto($producto);

    if ($exito) {
        header("Location: mainAdmin.php?success=Producto añadido correctamente");
    } else {
        header("Location: mainAdmin.php?error=No se pudo añadir el producto");
    }
    exit;

}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../img/favicon.ico" type="image/x-icon">
</head>


<body class="bg-light">
    <div class="container py-5">
        <div class="card mx-auto shadow-lg" style="max-width: 600px;">
            <div class="card-body">
                <h1 class="card-title text-center text-primary mb-4">Agregar Producto</h1>
                <form method="post" action="agregarProducto.php">


                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            placeholder="Introduce el nombre del producto" required>
                    </div>


                    <!-- Precio -->
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="precio" name="precio" step="0.01"
                            placeholder="Ejemplo: 19.99" required>
                    </div>


                    <!-- Categoría -->
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="categoria" name="categoria"
                            placeholder="Introduce la categoría" required>
                    </div>


                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                            placeholder="Introduce una descripción"></textarea>
                    </div>


                    <!-- Tipo -->
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo" required onchange="updateForm()">
                            <option value="" disabled selected>Selecciona un tipo</option>
                            <option value="Bollo">Bollo</option>
                            <option value="Chocolate">Chocolate</option>
                            <option value="Tarta">Tarta</option>
                        </select>
                    </div>


                    <!-- Opciones específicas por tipo -->
                    <div id="tipo-bollo" class="tipo-opciones d-none">
                        <div class="mb-3">
                            <label for="relleno" class="form-label">Relleno</label>
                            <input type="text" class="form-control" id="relleno" name="relleno"
                                placeholder="Ejemplo: Crema, Chocolate">
                        </div>
                    </div>


                    <div id="tipo-chocolate" class="tipo-opciones d-none">
                        <div class="mb-3">
                            <label for="porcentajeCacao" class="form-label">Porcentaje de Cacao</label>
                            <input type="number" class="form-control" id="porcentajeCacao" name="porcentajeCacao"
                                step="0.1" placeholder="Ejemplo: 70">
                        </div>
                        <div class="mb-3">
                            <label for="peso" class="form-label">Peso (g)</label>
                            <input type="number" class="form-control" id="peso" name="peso" placeholder="Ejemplo: 100">
                        </div>
                    </div>


                    <div id="tipo-tarta" class="tipo-opciones d-none">
                        <div class="mb-3">
                            <label for="rellenos" class="form-label">Rellenos (separados por comas)</label>
                            <input type="text" class="form-control" id="rellenos" name="rellenos"
                                placeholder="Ejemplo: Fresa, Chocolate">
                        </div>
                        <div class="mb-3">
                            <label for="numPisos" class="form-label">Número de Pisos</label>
                            <input type="number" class="form-control" id="numPisos" name="numPisos" min="1"
                                placeholder="Ejemplo: 2">
                        </div>
                        <div class="mb-3">
                            <label for="minComensales" class="form-label">Mínimo de Comensales</label>
                            <input type="number" class="form-control" id="minComensales" name="minComensales" min="1"
                                placeholder="Ejemplo: 4">
                        </div>
                        <div class="mb-3">
                            <label for="maxComensales" class="form-label">Máximo de Comensales</label>
                            <input type="number" class="form-control" id="maxComensales" name="maxComensales" min="1"
                                placeholder="Ejemplo: 8">
                        </div>
                    </div>


                    <!-- Botones de acción -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Añadir Producto</button>
                        <a href="mainAdmin.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function updateForm() {
            const tipo = document.getElementById('tipo').value;
            document.querySelectorAll('.tipo-opciones').forEach(div => div.classList.add('d-none'));
            const target = document.getElementById('tipo-' + tipo.toLowerCase());
            if (target) target.classList.remove('d-none');
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>