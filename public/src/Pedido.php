<?php
require_once 'Dulce.php';

class Pedido
{
    private int $id;
    private int $clienteId;
    private array $dulces;
    private float $total;
    private DateTime $fecha;

    public function __construct(int $id, int $clienteId, array $dulces, DateTime $fecha = null)
    {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->dulces = $dulces;
        $this->total = $this->calcularTotal();
        $this->fecha = $fecha ?? new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClienteId(): int
    {
        return $this->clienteId;
    }

    public function getDulces(): array
    {
        return $this->dulces;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getFecha(): string
    {
        return $this->fecha->format('Y-m-d H:i:s');
    }

    public function agregarDulce(Dulce $dulce, int $cantidad): void
    {
        $dulceId = $dulce->getId();
        if (isset($this->dulces[$dulceId])) {
            $this->dulces[$dulceId]['cantidad'] += $cantidad;
        } else {
            $this->dulces[$dulceId] = ['dulce' => $dulce, 'cantidad' => $cantidad];
        }
        $this->total = $this->calcularTotal();
    }

    private function calcularTotal(): float
    {
        $total = 0;
        foreach ($this->dulces as $dulceData) {
            $total += $dulceData['dulce']->getPrecio() * $dulceData['cantidad'];
        }
        return $total;
    }

    public function muestraResumen(): string
    {
        $resumen = "Pedido #{$this->id} - Fecha: {$this->getFecha()} - Total: {$this->total}€\n";
        foreach ($this->dulces as $dulceData) {
            $dulce = $dulceData['dulce'];
            $resumen .= "- {$dulce->getNombre()} (Cantidad: {$dulceData['cantidad']}, Precio Unitario: {$dulce->getPrecio()}€)\n";
        }
        return $resumen;
    }
}
