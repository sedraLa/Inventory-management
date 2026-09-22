<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Repositories\SaleRepository;
use App\Services\SaleService;
use Throwable;

class SaleController
{
    public function __construct(
        private SaleService    $saleService,
        private SaleRepository $saleRepository,
    ) {}

    public function store(): void
    {
        $body = $this->parseBody();

        if (empty($body['items']) || !is_array($body['items'])) {
            $this->json(['message' => 'items array is required.'], 400);
            return;
        }

        try {
            $sale = $this->saleService->createSale($body['items']);

            $items = $this->saleRepository->findItemsBySaleId($sale->getId());

            $this->json([
                'data' => [
                    'id'         => $sale->getId(),
                    'created_at' => $sale->getCreatedAt()?->format('Y-m-d H:i:s'),
                    'items'      => array_map(fn($i) => [
                        'id'         => $i->getId(),
                        'product_id' => $i->getProductId(),
                        'quantity'   => $i->getQuantity(),
                        'unit_price' => $i->getUnitPrice(),
                    ], $items),
                ],
            ], 201);
        } catch (InsufficientStockException $e) {
            $this->json(['message' => $e->getMessage()], 409);
        } catch (Throwable) {
            $this->json(['message' => 'Could not create sale.'], 500);
        }
    }

    public function show(array $params): void
    {
        $sale = $this->saleRepository->findSaleById((int) $params['id']);

        if ($sale === null) {
            $this->json(['message' => 'Sale not found.'], 404);
            return;
        }

        $items = $this->saleRepository->findItemsBySaleId($sale->getId());

        $this->json([
            'data' => [
                'id'         => $sale->getId(),
                'created_at' => $sale->getCreatedAt()?->format('Y-m-d H:i:s'),
                'items'      => array_map(fn($i) => [
                    'id'         => $i->getId(),
                    'product_id' => $i->getProductId(),
                    'quantity'   => $i->getQuantity(),
                    'unit_price' => $i->getUnitPrice(),
                ], $items),
            ],
        ]);
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
