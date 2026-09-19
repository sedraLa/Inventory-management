<?php

declare(strict_types=1);

namespace App\Entities;

class SaleItem
{
    public function __construct(
        private ?int   $id,
        private int    $saleId,
        private int    $productId,
        private int    $quantity,
        private float  $unitPrice,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaleId(): int
    {
        return $this->saleId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }
}
