<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Supplier;
use DateTimeImmutable;
use PDO;

class SupplierRepository
{
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?Supplier
    {
        $stmt = $this->pdo->prepare('SELECT * FROM suppliers WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM suppliers');
        $suppliers = [];

        foreach ($stmt->fetchAll() as $row) {
            $suppliers[] = $this->hydrate($row);
        }

        return $suppliers;
    }

    public function create(Supplier $supplier): Supplier
    {
        $stmt = $this->pdo->prepare('INSERT INTO suppliers (name, phone) VALUES (?, ?)');
        $stmt->execute([$supplier->getName(), $supplier->getPhone()]);

        return $this->findById((int) $this->pdo->lastInsertId());
    }

    private function hydrate(array $row): Supplier
    {
        $supplier = new Supplier((int) $row['id'], $row['name'], $row['phone']);
        $supplier->setCreatedAt(new DateTimeImmutable($row['created_at']));
        return $supplier;
    }
}
