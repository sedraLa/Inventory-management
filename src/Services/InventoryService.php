<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\InventoryMovement;
use App\Enums\MovementType;
use App\Exceptions\InsufficientStockException;
use App\Repositories\InventoryMovementRepository;

class InventoryService
{
    public function __construct(private InventoryMovementRepository $movementRepository) {}

    public function addStock(int $productId, int $quantity): InventoryMovement
    {
        $movement = new InventoryMovement(null, $productId, MovementType::IN, $quantity);
        return $this->movementRepository->create($movement);
    }

    public function removeStock(int $productId, int $quantity): InventoryMovement
    {
        $currentStock = $this->movementRepository->getCurrentStock($productId);

        if ($quantity > $currentStock) {
            throw new InsufficientStockException();
        }

        $movement = new InventoryMovement(null, $productId, MovementType::OUT, $quantity);
        return $this->movementRepository->create($movement);
    }
}
