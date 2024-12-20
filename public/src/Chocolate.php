<?php
require_once 'Dulce.php';

class Chocolate extends Dulce
{
    private float $porcentajeCacao;
    private float $peso;

    public function __construct(?int $id, string $nombre, float $precio, string $descripcion, string $categoria, float $porcentajeCacao, float $peso)
    {
        parent::__construct($id, $nombre, $precio, $descripcion, $categoria);
        $this->porcentajeCacao = $porcentajeCacao;
        $this->peso = $peso;
    }
    public function getPorcentajeCacao(): float
    {
        return $this->porcentajeCacao;
    }

    public function getPeso(): float
    {
        return $this->peso;
    }

    public function muestraResumen(): string
    {
        return "Chocolate: {$this->nombre}, Precio: {$this->precio}€, Categoría: {$this->categoria}, Cacao: {$this->porcentajeCacao}%, Peso: {$this->peso}g";
    }
}
?>