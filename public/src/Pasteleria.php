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

}
?>