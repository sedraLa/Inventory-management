<?php

declare(strict_types=1);

namespace App\Entities;

use App\Traits\HasTimestamps;

class Sale
{
    use HasTimestamps;

    public function __construct(
        private ?int $id,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }
}
