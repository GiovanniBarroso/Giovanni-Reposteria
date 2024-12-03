<?php
require_once 'Dulce.php';
require_once 'Cliente.php';

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
        foreach ($this->productos as $producto) {
            if ($producto->getNombre() === $nombre) {
                return $producto;
            }
        }
        echo "Producto no encontrado: {$nombre}\n";
        return null;
    }
}
?>