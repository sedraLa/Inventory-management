<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Sale;
use App\Entities\SaleItem;
use App\Repositories\SaleRepository;
use PDO;
use Throwable;

class SaleService
{
    public function __construct(
        private PDO              $pdo,
        private SaleRepository   $saleRepository,
        private InventoryService $inventoryService,
    ) {}

    public function createSale(array $items): Sale
    {
        $this->pdo->beginTransaction();

        try {
            $sale = $this->saleRepository->createSale();

            foreach ($items as $item) {
                $saleItem = new SaleItem(
                    null,
                    $sale->getId(),
                    $item['productId'],
                    $item['quantity'],
                    $item['unitPrice'],
                );

                $this->saleRepository->createSaleItem($saleItem);
                $this->inventoryService->removeStock($item['productId'], $item['quantity']);
            }

            $this->pdo->commit();

            return $sale;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
