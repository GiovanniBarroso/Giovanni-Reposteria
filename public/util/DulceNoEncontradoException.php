<?php
require_once 'PasteleriaException.php';

class DulceNoEncontradoException extends PasteleriaException
{
    public function __construct(string $message = "El dulce no se encuentra en los productos disponibles.")
    {
        parent::__construct($message);
    }
}
?>