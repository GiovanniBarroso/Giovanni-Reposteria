<?php
require_once 'PasteleriaException.php';

class DulceNoCompradoException extends PasteleriaException
{
    public function __construct(string $message = "El dulce no ha sido comprado.")
    {
        parent::__construct($message);
    }
}
?>