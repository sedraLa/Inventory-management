<?php

declare(strict_types=1);

namespace App\Entities;

use App\Traits\HasTimestamps;

class Product
{
    use HasTimestamps;

    public function __construct(
        private ?int    $id,
        private string  $name,
        private string  $sku,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }
}
