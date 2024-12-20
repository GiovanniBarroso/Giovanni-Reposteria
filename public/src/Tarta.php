<?php
require_once 'Dulce.php';

class Tarta extends Dulce
{
    private array $rellenos;
    private int $numPisos;
    private int $minNumComensales;
    private int $maxNumComensales;

    public function __construct(?int $id, string $nombre, float $precio, string $descripcion, string $categoria, array $rellenos, int $numPisos, int $minNumComensales = 2, int $maxNumComensales)
    {
        parent::__construct($id, $nombre, $precio, $descripcion, $categoria);
        $this->id = $id;
        $this->rellenos = $rellenos;
        $this->numPisos = $numPisos;
        $this->minNumComensales = $minNumComensales;
        $this->maxNumComensales = $maxNumComensales;
    }


    public function getRellenos(): array
    {
        return $this->rellenos;
    }


    public function getNumPisos(): int
    {
        return $this->numPisos;
    }


    public function getMinNumComensales(): int
    {
        return $this->minNumComensales;
    }


    public function getMaxNumComensales(): int
    {
        return $this->maxNumComensales;
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


    public function setOpcionesPersonalizadas(int $numPisos, array $rellenos): void
    {
        $this->numPisos = $numPisos;
        $this->rellenos = $rellenos;
    }


    public function getOpcionesPersonalizadas(): string
    {
        return "Número de pisos: {$this->numPisos}, Rellenos: " . implode(", ", $this->rellenos);
    }
}
?>