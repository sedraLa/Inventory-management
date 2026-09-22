<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Services\InventoryService;
use Throwable;

class InventoryController
{
    public function __construct(private InventoryService $inventoryService) {}

    public function stockIn(): void
    {
        $body = $this->parseBody();

        if (empty($body['productId']) || empty($body['quantity'])) {
            $this->json(['message' => 'productId and quantity are required.'], 400);
            return;
        }

        try {
            $movement = $this->inventoryService->addStock(
                (int) $body['productId'],
                (int) $body['quantity'],
            );

            $this->json(['data' => $this->serialize($movement)], 201);
        } catch (Throwable) {
            $this->json(['message' => 'Could not record stock movement.'], 500);
        }
    }

    public function stockOut(): void
    {
        $body = $this->parseBody();

        if (empty($body['productId']) || empty($body['quantity'])) {
            $this->json(['message' => 'productId and quantity are required.'], 400);
            return;
        }

        try {
            $movement = $this->inventoryService->removeStock(
                (int) $body['productId'],
                (int) $body['quantity'],
            );

            $this->json(['data' => $this->serialize($movement)], 201);
        } catch (InsufficientStockException $e) {
            $this->json(['message' => $e->getMessage()], 409);
        } catch (Throwable) {
            $this->json(['message' => 'Could not record stock movement.'], 500);
        }
    }

    private function serialize(object $movement): array
    {
        return [
            'id'         => $movement->getId(),
            'product_id' => $movement->getProductId(),
            'type'       => $movement->getType()->value,
            'quantity'   => $movement->getQuantity(),
            'created_at' => $movement->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    private function parseBody(): array
    {
        return (array) json_decode(file_get_contents('php://input'), true);
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
