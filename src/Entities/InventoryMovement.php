<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enums\MovementType;
use App\Traits\HasTimestamps;

class InventoryMovement
{
    use HasTimestamps;

    public function __construct(
        private ?int          $id,
        private int           $productId,
        private MovementType  $type,
        private int           $quantity,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getType(): MovementType
    {
        return $this->type;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
