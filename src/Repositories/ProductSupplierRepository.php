<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\ProductSupplier;
use PDO;

class ProductSupplierRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(ProductSupplier $productSupplier): ProductSupplier
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO product_suppliers (product_id, supplier_id, price) VALUES (?, ?, ?)'
        );

        $stmt->execute([
            $productSupplier->getProductId(),
            $productSupplier->getSupplierId(),
            $productSupplier->getPrice(),
        ]);

        $id = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare('SELECT * FROM product_suppliers WHERE id = ?');
        $stmt->execute([$id]);

        return $this->hydrate($stmt->fetch());
    }

    public function findByProductId(int $productId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM product_suppliers WHERE product_id = ?');
        $stmt->execute([$productId]);

        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[] = $this->hydrate($row);
        }

        return $results;
    }

    public function findBySupplierId(int $supplierId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM product_suppliers WHERE supplier_id = ?');
        $stmt->execute([$supplierId]);

        $results = [];
        foreach ($stmt->fetchAll() as $row) {
            $results[] = $this->hydrate($row);
        }

        return $results;
    }

    private function hydrate(array $row): ProductSupplier
    {
        return new ProductSupplier(
            (int) $row['id'],
            (int) $row['product_id'],
            (int) $row['supplier_id'],
            (float) $row['price'],
        );
    }
}
