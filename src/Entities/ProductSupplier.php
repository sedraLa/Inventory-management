<?php

declare(strict_types=1);

namespace App\Entities;

class ProductSupplier
{
    public function __construct(
        private ?int   $id,
        private int    $productId,
        private int    $supplierId,
        private float  $price,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
