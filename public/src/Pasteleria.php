<?php
require_once 'Dulce.php';
require_once 'Cliente.php';
require_once '../util/DulceNoEncontradoException.php';
require_once '../util/ClienteNoEncontradoException.php';

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
        $query = "INSERT INTO productos (nombre, precio, categoria, tipo) VALUES (:nombre, :precio, :categoria, :tipo)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':nombre', $d->getNombre());
        $stmt->bindValue(':precio', $d->getPrecio());
        $stmt->bindValue(':categoria', $d->getCategoria());
        $stmt->bindValue(':tipo', get_class($d)); // Obtiene el nombre de la subclase
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
                        $row['nombre'],
                        $row['precio'],
                        $row['descripcion'] ?? '',
                        $row['categoria'],
                        $row['relleno'] ?? ''
                    );
                    break;

                case 'Chocolate':
                    $productos[] = new Chocolate(
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
                        $row['nombre'],
                        $row['precio'],
                        $row['descripcion'] ?? '',
                        $row['categoria'],
                        explode(',', $row['rellenos'] ?? ''), // Rellenos como array
                        $row['numPisos'] ?? 1,
                        $row['minNumComensales'] ?? 2,
                        $row['maxNumComensales'] ?? 2
                    );
                    break;

                default:
                    // Si el tipo no coincide, lanza una excepción o ignóralo
                    echo "Tipo desconocido: {$row['tipo']}";
            }
        }

        return $productos;
    }


    public function actualizarProducto(int $id, Dulce $d): bool
    {
        $db = Database::getConnection();
        $query = "UPDATE productos SET nombre = :nombre, precio = :precio, categoria = :categoria WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nombre', $d->getNombre());
        $stmt->bindValue(':precio', $d->getPrecio());
        $stmt->bindValue(':categoria', $d->getCategoria());
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



}
?>