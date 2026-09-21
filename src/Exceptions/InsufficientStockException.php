<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Insufficient stock for this product.');
    }
}
