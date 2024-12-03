<?php
require_once 'Dulce.php';

class Tarta extends Dulce
{
    private array $rellenos;
    private int $numPisos;
    private int $minNumComensales;
    private int $maxNumComensales;

    public function __construct(string $nombre, float $precio, string $descripcion, string $categoria, array $rellenos, int $numPisos, int $minNumComensales = 2, int $maxNumComensales)
    {
        parent::__construct($nombre, $precio, $descripcion, $categoria);
        $this->rellenos = $rellenos;
        $this->numPisos = $numPisos;
        $this->minNumComensales = $minNumComensales;
        $this->maxNumComensales = $maxNumComensales;
    }

    public function muestraComensalesPosibles(): string
    {
        if ($this->minNumComensales === $this->maxNumComensales) {
            return "Para {$this->minNumComensales} comensales";
        }
        return "De {$this->minNumComensales} a {$this->maxNumComensales} comensales";
    }

    public function muestraResumen(): string
    {
        $rellenos = implode(", ", $this->rellenos);
        return "Tarta: {$this->nombre}, Precio: {$this->precio}€, Categoría: {$this->categoria}, Rellenos: {$rellenos}, Número de Pisos: {$this->numPisos}, Descripción: {$this->descripcion}, Comensales Posibles: {$this->muestraComensalesPosibles()}";
    }
}
?>