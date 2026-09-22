<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Supplier;
use App\Repositories\SupplierRepository;
use Throwable;

class SupplierController
{
    public function __construct(private SupplierRepository $supplierRepository) {}

    public function index(): void
    {
        $suppliers = $this->supplierRepository->findAll();

        $data = array_map(fn($s) => $this->serialize($s), $suppliers);

        $this->json(['data' => $data]);
    }

    public function store(): void
    {
        $body = $this->parseBody();

        if (empty($body['name'])) {
            $this->json(['message' => 'name is required.'], 400);
            return;
        }

        try {
            $supplier = $this->supplierRepository->create(
                new Supplier(null, $body['name'], $body['phone'] ?? null)
            );

            $this->json(['data' => $this->serialize($supplier)], 201);
        } catch (Throwable) {
            $this->json(['message' => 'Could not create supplier.'], 500);
        }
    }

    private function serialize(Supplier $s): array
    {
        return [
            'id'         => $s->getId(),
            'name'       => $s->getName(),
            'phone'      => $s->getPhone(),
            'created_at' => $s->getCreatedAt()?->format('Y-m-d H:i:s'),
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
