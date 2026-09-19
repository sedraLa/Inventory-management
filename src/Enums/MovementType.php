<?php

declare(strict_types=1);

namespace App\Enums;

enum MovementType: string
{
    case IN  = 'IN';
    case OUT = 'OUT';
}
