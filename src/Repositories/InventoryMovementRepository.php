<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\InventoryMovement;
use App\Enums\MovementType;
use DateTimeImmutable;
use PDO;

class InventoryMovementRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(InventoryMovement $movement): InventoryMovement
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO inventory_movements (product_id, type, quantity) VALUES (?, ?, ?)'
        );

        $stmt->execute([
            $movement->getProductId(),
            $movement->getType()->value,
            $movement->getQuantity(),
        ]);

        $id = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare('SELECT * FROM inventory_movements WHERE id = ?');
        $stmt->execute([$id]);

        return $this->hydrate($stmt->fetch());
    }

    public function findByProductId(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM inventory_movements WHERE product_id = ? ORDER BY created_at ASC'
        );
        $stmt->execute([$productId]);

        $movements = [];
        foreach ($stmt->fetchAll() as $row) {
            $movements[] = $this->hydrate($row);
        }

        return $movements;
    }

    public function getCurrentStock(int $productId): int
    {
        $stmt = $this->pdo->prepare('
            SELECT
                SUM(CASE WHEN type = "IN"  THEN quantity ELSE 0 END) -
                SUM(CASE WHEN type = "OUT" THEN quantity ELSE 0 END) AS stock
            FROM inventory_movements
            WHERE product_id = ?
        ');

        $stmt->execute([$productId]);
        $row = $stmt->fetch();

        return (int) ($row['stock'] ?? 0);
    }

    private function hydrate(array $row): InventoryMovement
    {
        $movement = new InventoryMovement(
            (int) $row['id'],
            (int) $row['product_id'],
            MovementType::from($row['type']),
            (int) $row['quantity'],
        );
        $movement->setCreatedAt(new DateTimeImmutable($row['created_at']));
        return $movement;
    }
}
