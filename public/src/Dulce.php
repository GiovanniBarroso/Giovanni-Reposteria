<?php
require_once 'Resumible.php';

abstract class Dulce implements Resumible
{
    protected string $nombre;
    protected float $precio;
    protected string $descripcion;
    protected string $categoria;
    private static float $IVA = 21.0;

    public function __construct(string $nombre, float $precio, string $descripcion, string $categoria)
    {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->categoria = $categoria;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public static function getIVA(): float
    {
        return self::$IVA;
    }

    // Método abstracto que las subclases deben implementar
    abstract public function muestraResumen(): string;
}
?>