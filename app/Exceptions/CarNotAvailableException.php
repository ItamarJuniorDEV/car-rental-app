<?php

namespace App\Exceptions;

use Exception;

class CarNotAvailableException extends Exception
{
    public function __construct()
    {
        parent::__construct('O veículo selecionado não está disponível para locação.', 422);
    }
}
