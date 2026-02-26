<?php

namespace App\Exceptions;

use Exception;

class ActiveRentalsException extends Exception
{
    public function __construct(string $message = 'Não é possível remover este registro pois ele possui locações ativas.')
    {
        parent::__construct($message, 422);
    }
}
