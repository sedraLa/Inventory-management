<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Product;
use DateTimeImmutable;
use PDO;

class ProductRepository
{
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?Product
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM products');
        $products = [];

        foreach ($stmt->fetchAll() as $row) {
            $products[] = $this->hydrate($row);
        }

        return $products;
    }

    public function create(Product $product): Product
    {
        $stmt = $this->pdo->prepare('INSERT INTO products (name, sku) VALUES (?, ?)');
        $stmt->execute([$product->getName(), $product->getSku()]);

        return $this->findById((int) $this->pdo->lastInsertId());
    }

    private function hydrate(array $row): Product
    {
        $product = new Product((int) $row['id'], $row['name'], $row['sku']);
        $product->setCreatedAt(new DateTimeImmutable($row['created_at']));
        return $product;
    }
}
