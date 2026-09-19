<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Sale;
use App\Entities\SaleItem;
use DateTimeImmutable;
use PDO;

class SaleRepository
{
    public function __construct(private PDO $pdo) {}

    public function createSale(): Sale
    {
        $this->pdo->exec('INSERT INTO sales () VALUES ()');

        return $this->findSaleById((int) $this->pdo->lastInsertId());
    }

    public function createSaleItem(SaleItem $item): SaleItem
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sale_items (sale_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)'
        );

        $stmt->execute([
            $item->getSaleId(),
            $item->getProductId(),
            $item->getQuantity(),
            $item->getUnitPrice(),
        ]);

        $id = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare('SELECT * FROM sale_items WHERE id = ?');
        $stmt->execute([$id]);

        return $this->hydrateSaleItem($stmt->fetch());
    }

    public function findSaleById(int $id): ?Sale
    {
        $stmt = $this->pdo->prepare('SELECT * FROM sales WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $sale = new Sale((int) $row['id']);
        $sale->setCreatedAt(new DateTimeImmutable($row['created_at']));
        return $sale;
    }

    public function findItemsBySaleId(int $saleId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM sale_items WHERE sale_id = ?');
        $stmt->execute([$saleId]);

        $items = [];
        foreach ($stmt->fetchAll() as $row) {
            $items[] = $this->hydrateSaleItem($row);
        }

        return $items;
    }

    private function hydrateSaleItem(array $row): SaleItem
    {
        return new SaleItem(
            (int) $row['id'],
            (int) $row['sale_id'],
            (int) $row['product_id'],
            (int) $row['quantity'],
            (float) $row['unit_price'],
        );
    }
}
