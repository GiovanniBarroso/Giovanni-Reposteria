<?php
require_once 'Dulce.php';
require_once 'Cliente.php';
require_once 'Bollo.php';
require_once 'Chocolate.php';
require_once 'Tarta.php';
require_once '../util/DulceNoEncontradoException.php';
require_once '../util/ClienteNoEncontradoException.php';
require_once __DIR__ . '/../db/Database.php';

class Pasteleria
{
    private array $productos;
    private array $clientes;

    public function __construct()
    {
        $this->productos = [];
        $this->clientes = [];
    }

    public function incluirProducto(Dulce $d): void
    {
        $this->productos[] = $d;
        echo "Producto añadido: {$d->getNombre()}.\n";
    }

    public function listarProductos(): void
    {
        echo "Productos disponibles en la pastelería:\n";
        foreach ($this->productos as $producto) {
            echo "- {$producto->muestraResumen()}\n";
        }
    }

    public function buscarProducto(string $nombre): ?Dulce
    {
        try {
            foreach ($this->productos as $producto) {
                if ($producto->getNombre() === $nombre) {
                    return $producto;
                }
            }
            throw new DulceNoEncontradoException("Producto no encontrado: {$nombre}");
        } catch (DulceNoEncontradoException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function buscarCliente(string $nombre): ?Cliente
    {
        try {
            foreach ($this->clientes as $cliente) {
                if ($cliente->getNombre() === $nombre) {
                    return $cliente;
                }
            }
            throw new ClienteNoEncontradoException("Cliente no encontrado: {$nombre}");
        } catch (ClienteNoEncontradoException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function guardarProducto(Dulce $d): bool
    {
        $db = Database::getConnection();

        $query = "INSERT INTO productos 
                  (nombre, precio, categoria, tipo, descripcion, porcentajeCacao, peso, rellenos, numPisos, minComensales, maxComensales) 
                  VALUES (:nombre, :precio, :categoria, :tipo, :descripcion, :porcentajeCacao, :peso, :rellenos, :numPisos, :minComensales, :maxComensales)";

        $stmt = $db->prepare($query);

        // Valores comunes
        $stmt->bindValue(':nombre', $d->getNombre());
        $stmt->bindValue(':precio', $d->getPrecio());
        $stmt->bindValue(':categoria', $d->getCategoria());
        $stmt->bindValue(':tipo', get_class($d));
        $stmt->bindValue(':descripcion', $d->getDescripcion());

        // Valores específicos según el tipo
        if ($d instanceof Chocolate) {
            $stmt->bindValue(':porcentajeCacao', $d->getPorcentajeCacao());
            $stmt->bindValue(':peso', $d->getPeso());
            $stmt->bindValue(':rellenos', null);
            $stmt->bindValue(':numPisos', null);
            $stmt->bindValue(':minComensales', null);
            $stmt->bindValue(':maxComensales', null);
        } elseif ($d instanceof Tarta) {
            $stmt->bindValue(':porcentajeCacao', null);
            $stmt->bindValue(':peso', null);
            $stmt->bindValue(':rellenos', implode(',', $d->getRellenos()));
            $stmt->bindValue(':numPisos', $d->getNumPisos());
            $stmt->bindValue(':minComensales', $d->getMinNumComensales());
            $stmt->bindValue(':maxComensales', $d->getMaxNumComensales());
        } else {
            // Otros tipos (Bollo)
            $stmt->bindValue(':porcentajeCacao', null);
            $stmt->bindValue(':peso', null);
            $stmt->bindValue(':rellenos', null);
            $stmt->bindValue(':numPisos', null);
            $stmt->bindValue(':minComensales', null);
            $stmt->bindValue(':maxComensales', null);
        }

        return $stmt->execute();
    }







    public function obtenerProductos(): array
    {
        $db = Database::getConnection();
        $query = "SELECT * FROM productos";
        $stmt = $db->query($query);
        $productos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            switch ($row['tipo']) {
                case 'Bollo':
                    $productos[] = new Bollo(
                        $row['id'],
                        $row['nombre'],
                        $row['precio'],
                        $row['descripcion'] ?? '',
                        $row['categoria'],
                        $row['relleno'] ?? ''
                    );
                    break;

                case 'Chocolate':
                    $productos[] = new Chocolate(
                        $row['id'],
                        $row['nombre'],
                        $row['precio'],
                        $row['descripcion'] ?? '',
                        $row['categoria'],
                        $row['porcentajeCacao'] ?? 0,
                        $row['peso'] ?? 0
                    );
                    break;

                case 'Tarta':
                    $productos[] = new Tarta(
                        $row['id'],
                        $row['nombre'],
                        $row['precio'],
                        $row['descripcion'] ?? '',
                        $row['categoria'],
                        explode(',', $row['rellenos'] ?? ''),
                        $row['numPisos'] ?? 1,
                        $row['minComensales'] ?? 2,
                        $row['maxComensales'] ?? 2
                    );
                    break;

                default:
                    echo "Tipo desconocido: {$row['tipo']}";
            }
        }

        return $productos;
    }


    public function obtenerClientes(): array
    {
        $db = Database::getConnection();
        $query = "SELECT id, nombre, usuario, rol FROM clientes";
        $stmt = $db->query($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function actualizarCliente(int $id, string $nombre, string $usuario, string $rol): bool
    {
        $db = Database::getConnection();
        $query = "UPDATE clientes SET nombre = :nombre, usuario = :usuario, rol = :rol WHERE id = :id";
        $stmt = $db->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->bindValue(':rol', $rol);

        return $stmt->execute();
    }



    public function eliminarCliente(int $id): bool
    {
        $db = Database::getConnection();
        $query = "DELETE FROM clientes WHERE id = :id";
        $stmt = $db->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function buscarClientePorId(int $id): ?array
    {
        $db = Database::getConnection();
        $query = "SELECT id, nombre, usuario, rol FROM clientes WHERE id = :id";
        $stmt = $db->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cliente ? $cliente : null;
    }




    public function actualizarProducto(int $id, Dulce $d): bool
    {
        $db = Database::getConnection();

        // Crear la consulta SQL con todas las columnas
        $query = "UPDATE productos 
              SET nombre = :nombre, 
                  precio = :precio, 
                  categoria = :categoria, 
                  descripcion = :descripcion, 
                  porcentajeCacao = :porcentajeCacao, 
                  peso = :peso, 
                  rellenos = :rellenos, 
                  numPisos = :numPisos, 
                  minComensales = :minComensales, 
                  maxComensales = :maxComensales 
              WHERE id = :id";

        $stmt = $db->prepare($query);

        // Valores comunes
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $d->getNombre());
        $stmt->bindValue(':precio', $d->getPrecio());
        $stmt->bindValue(':categoria', $d->getCategoria());
        $stmt->bindValue(':descripcion', $d->getDescripcion());

        // Valores específicos según el tipo
        if ($d instanceof Chocolate) {
            $stmt->bindValue(':porcentajeCacao', $d->getPorcentajeCacao());
            $stmt->bindValue(':peso', $d->getPeso());
            $stmt->bindValue(':rellenos', null);
            $stmt->bindValue(':numPisos', null);
            $stmt->bindValue(':minComensales', null);
            $stmt->bindValue(':maxComensales', null);
        } elseif ($d instanceof Tarta) {
            $stmt->bindValue(':porcentajeCacao', null);
            $stmt->bindValue(':peso', null);
            $stmt->bindValue(':rellenos', implode(',', $d->getRellenos()));
            $stmt->bindValue(':numPisos', $d->getNumPisos());
            $stmt->bindValue(':minComensales', $d->getMinNumComensales());
            $stmt->bindValue(':maxComensales', $d->getMaxNumComensales());
        } else {
            // Otros tipos (Bollo)
            $stmt->bindValue(':porcentajeCacao', null);
            $stmt->bindValue(':peso', null);
            $stmt->bindValue(':rellenos', null);
            $stmt->bindValue(':numPisos', null);
            $stmt->bindValue(':minComensales', null);
            $stmt->bindValue(':maxComensales', null);
        }

        return $stmt->execute();
    }




    public function eliminarProducto(int $id): bool
    {
        $db = Database::getConnection();
        $query = "DELETE FROM productos WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function buscarProductoPorId(int $id): ?Dulce
    {
        $db = Database::getConnection();
        $query = "SELECT * FROM productos WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null; // Retorna null si no se encuentra el producto
        }

        // Crear la instancia del producto basado en el tipo
        switch ($row['tipo']) {
            case 'Bollo':
                return new Bollo(
                    $row['id'], // Incluye el id
                    $row['nombre'],
                    $row['precio'],
                    $row['descripcion'] ?? '',
                    $row['categoria'],
                    $row['relleno'] ?? ''
                );

            case 'Chocolate':
                return new Chocolate(
                    $row['id'], // Incluye el id
                    $row['nombre'],
                    $row['precio'],
                    $row['descripcion'] ?? '',
                    $row['categoria'],
                    $row['porcentajeCacao'] ?? 0,
                    $row['peso'] ?? 0
                );

            case 'Tarta':
                return new Tarta(
                    $row['id'], // Incluye el id
                    $row['nombre'],
                    $row['precio'],
                    $row['descripcion'] ?? '',
                    $row['categoria'],
                    explode(',', $row['rellenos'] ?? ''), // Convierte a array
                    $row['numPisos'] ?? 1,
                    $row['minNumComensales'] ?? 2,
                    $row['maxNumComensales'] ?? 2
                );

            default:
                return null; // Retorna null si el tipo no coincide
        }
    }


}
?>