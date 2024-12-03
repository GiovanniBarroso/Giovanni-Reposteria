<?php
require_once 'Dulce.php';

class Bollo extends Dulce
{
    private string $relleno;

    public function __construct(string $nombre, float $precio, string $descripcion, string $categoria, string $relleno)
    {
        parent::__construct($nombre, $precio, $descripcion, $categoria);
        $this->relleno = $relleno;
    }

    public function getRelleno(): string
    {
        return $this->relleno;
    }

    public function muestraResumen(): string
    {
        return "Bollo: {$this->nombre}, Precio: {$this->precio}€, Categoría: {$this->categoria}, Relleno: {$this->relleno}, Descripción: {$this->descripcion}";
    }
}
?>