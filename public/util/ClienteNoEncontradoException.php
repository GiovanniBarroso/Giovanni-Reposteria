<?php
require_once 'PasteleriaException.php';

class ClienteNoEncontradoException extends PasteleriaException
{
    public function __construct(string $message = "El cliente no se encuentra en el sistema.")
    {
        parent::__construct($message);
    }
}
?>