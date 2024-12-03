<?php
require_once 'Dulce.php';

class Cliente
{
    private string $nombre;
    private int $numero;
    private int $numPedidosEfectuados;
    private array $dulcesComprados;

    public function __construct(string $nombre, int $numero, int $numPedidosEfectuados = 0)
    {
        $this->nombre = $nombre;
        $this->numero = $numero;
        $this->numPedidosEfectuados = $numPedidosEfectuados;
        $this->dulcesComprados = [];
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function getNumPedidosEfectuados(): int
    {
        return $this->numPedidosEfectuados;
    }

    public function muestraResumen(): string
    {
        return "Cliente: {$this->nombre}, Pedidos realizados: {$this->numPedidosEfectuados}";
    }

    public function listaDeDulces(Dulce $d): bool
    {
        foreach ($this->dulcesComprados as $dulceComprado) {
            if ($dulceComprado === $d) {
                return true;
            }
        }
        return false;
    }

    public function comprar(Dulce $d): bool
    {
        $this->dulcesComprados[] = $d;
        $this->numPedidosEfectuados++;
        echo "Has comprado el dulce: {$d->getNombre()}.\n";
        return true;
    }

    public function valorar(Dulce $d, string $comentario): void
    {
        if ($this->listaDeDulces($d)) {
            echo "Valoración del dulce {$d->getNombre()}: {$comentario}\n";
        } else {
            echo "No puedes valorar un dulce que no has comprado.\n";
        }
    }

    public function listarPedidos(): void
    {
        echo "Pedidos realizados por {$this->nombre}: {$this->numPedidosEfectuados}\n";
        foreach ($this->dulcesComprados as $dulce) {
            echo "- {$dulce->muestraResumen()}\n";
        }
    }
}
?>