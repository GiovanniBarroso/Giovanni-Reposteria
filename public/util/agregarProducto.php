<?php
require_once '../src/Pasteleria.php';
require_once '../src/Tarta.php';
require_once '../src/Bollo.php';
require_once '../src/Chocolate.php';

$pasteleria = new Pasteleria();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = max(1, (float) $_POST['precio']); // Mínimo 1
    $categoria = trim($_POST['categoria']);
    $tipo = $_POST['tipo'];
    $descripcion = trim($_POST['descripcion'] ?? '');

    try {
        $producto = null;

        switch ($tipo) {
            case 'Bollo':
                $relleno = trim($_POST['relleno'] ?? '');
                $producto = new Bollo(null, $nombre, $precio, $descripcion, $categoria, $relleno);
                break;

            case 'Chocolate':
                $porcentajeCacao = max(1, min(100, (float) ($_POST['porcentajeCacao'] ?? 0))); // Entre 1 y 100
                $peso = max(1, (float) ($_POST['peso'] ?? 0)); // Mínimo 1
                $producto = new Chocolate(null, $nombre, $precio, $descripcion, $categoria, $porcentajeCacao, $peso);
                break;

            case 'Tarta':
                $rellenos = array_map('trim', explode(',', $_POST['rellenos'] ?? ''));
                $numPisos = max(1, (int) ($_POST['numPisos'] ?? 1)); // Mínimo 1
                $minComensales = max(1, (int) ($_POST['minComensales'] ?? 2));
                $maxComensales = max($minComensales, (int) ($_POST['maxComensales'] ?? $minComensales));
                $producto = new Tarta(null, $nombre, $precio, $descripcion, $categoria, $rellenos, $numPisos, $minComensales, $maxComensales);
                break;

            default:
                throw new Exception("Tipo de producto no válido.");
        }

        if ($pasteleria->guardarProducto($producto)) {
            header("Location: mainAdmin.php?success=Producto añadido correctamente");
        } else {
            throw new Exception("Error al guardar el producto en la base de datos.");
        }
    } catch (Exception $e) {
        header("Location: agregarProducto.php?error=" . urlencode($e->getMessage()));
    }
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
                        <label for="precio" class="form-label">Precio (€)</label>
                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0.01"
                            placeholder="Ejemplo: 19.99" required>
                        <div class="invalid-feedback">El precio debe ser mayor que 0.</div>
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


                    <!-- Porcentaje de Cacao -->
                    <div id="tipo-chocolate" class="tipo-opciones d-none">
                        <div id="tipo-chocolate" class="tipo-opciones d-none">
                            <div class="mb-3">
                                <label for="porcentajeCacao" class="form-label">Porcentaje de Cacao (%)</label>
                                <input type="number" class="form-control" id="porcentajeCacao" name="porcentajeCacao"
                                    step="0.1" min="1" max="100" placeholder="Ejemplo: 70">
                                <div class="invalid-feedback">El porcentaje de cacao debe estar entre 1 y 100.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="peso" class="form-label">Peso (g)</label>
                            <input type="number" class="form-control" id="peso" name="peso" min="1"
                                placeholder="Ejemplo: 100">
                        </div>
                    </div>


                    <div id="tipo-tarta" class="tipo-opciones d-none">
                        <div class="mb-3">
                            <label for="rellenos" class="form-label">Rellenos (separados por comas)</label>
                            <input type="text" class="form-control" id="rellenos" name="rellenos"
                                placeholder="Ejemplo: Fresa, Chocolate">
                        </div>
                        <div id="tipo-tarta" class="tipo-opciones d-none">
                            <div class="mb-3">
                                <label for="numPisos" class="form-label">Número de Pisos</label>
                                <input type="number" class="form-control" id="numPisos" name="numPisos" min="1"
                                    placeholder="Ejemplo: 2">
                                <div class="invalid-feedback">El número de pisos debe ser al menos 1.</div>
                            </div>
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

    <script>
        document.getElementById('productoForm').addEventListener('submit', function (event) {
            let valid = true;

            // Validar precio
            const precio = document.getElementById('precio');
            if (precio.value <= 0) {
                precio.value = 1; // Valor mínimo
                precio.classList.add('is-invalid');
                valid = false;
            } else {
                precio.classList.remove('is-invalid');
            }

            // Validar porcentaje de cacao
            const porcentajeCacao = document.getElementById('porcentajeCacao');
            if (porcentajeCacao && (porcentajeCacao.value < 1 || porcentajeCacao.value > 100)) {
                porcentajeCacao.value = Math.max(1, Math.min(100, porcentajeCacao.value));
                porcentajeCacao.classList.add('is-invalid');
                valid = false;
            } else if (porcentajeCacao) {
                porcentajeCacao.classList.remove('is-invalid');
            }

            // Validar número de pisos
            const numPisos = document.getElementById('numPisos');
            if (numPisos && numPisos.value < 1) {
                numPisos.value = 1; // Valor mínimo
                numPisos.classList.add('is-invalid');
                valid = false;
            } else if (numPisos) {
                numPisos.classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault(); // Evitar el envío si hay errores
            }
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>