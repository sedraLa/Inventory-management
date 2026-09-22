<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\InventoryController;
use App\Controllers\ProductController;
use App\Controllers\SaleController;
use App\Controllers\SupplierController;
use App\Database\Connection;
use App\Repositories\InventoryMovementRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SaleRepository;
use App\Repositories\SupplierRepository;
use App\Router\Router;
use App\Services\InventoryService;
use App\Services\SaleService;


$pdo = Connection::get();

$productRepository   = new ProductRepository($pdo);
$supplierRepository  = new SupplierRepository($pdo);
$movementRepository  = new InventoryMovementRepository($pdo);
$saleRepository      = new SaleRepository($pdo);

$inventoryService = new InventoryService($movementRepository);
$saleService      = new SaleService($pdo, $saleRepository, $inventoryService);

$productController   = new ProductController($productRepository);
$supplierController  = new SupplierController($supplierRepository);
$inventoryController = new InventoryController($inventoryService);
$saleController      = new SaleController($saleService, $saleRepository);

// --- Routes ---

$router = new Router();

$router->add('GET', '/api/products', [$productController, 'index']);
$router->add('GET', '/api/products/{id}', [$productController, 'show']);
$router->add('POST', '/api/products', [$productController, 'store']);

$router->add('GET', '/api/suppliers', [$supplierController, 'index']);
$router->add('POST', '/api/suppliers', [$supplierController, 'store']);

$router->add('POST', '/api/inventory/in', [$inventoryController, 'stockIn']);
$router->add('POST', '/api/inventory/out', [$inventoryController, 'stockOut']);

$router->add('POST', '/api/sales', [$saleController, 'store']);
$router->add('GET', '/api/sales/{id}', [$saleController, 'show']);


$router->dispatch();
