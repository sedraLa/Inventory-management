<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Product;
use App\Repositories\ProductRepository;
use Throwable;

class ProductController
{
    public function __construct(private ProductRepository $productRepository) {}

    public function index(): void
    {
        $products = $this->productRepository->findAll();

        $data = array_map(fn($p) => $this->serialize($p), $products);

        $this->json(['data' => $data]);
    }

    public function show(array $params): void
    {
        $product = $this->productRepository->findById((int) $params['id']);

        if ($product === null) {
            $this->json(['message' => 'Product not found.'], 404);
            return;
        }

        $this->json(['data' => $this->serialize($product)]);
    }

    public function store(): void
    {
        $body = $this->parseBody();

        if (empty($body['name']) || empty($body['sku'])) {
            $this->json(['message' => 'name and sku are required.'], 400);
            return;
        }

        try {
            $product = $this->productRepository->create(
                new Product(null, $body['name'], $body['sku'])
            );

            $this->json(['data' => $this->serialize($product)], 201);
        } catch (Throwable) {
            $this->json(['message' => 'Could not create product.'], 500);
        }
    }

    private function serialize(Product $p): array
    {
        return [
            'id'         => $p->getId(),
            'name'       => $p->getName(),
            'sku'        => $p->getSku(),
            'created_at' => $p->getCreatedAt()?->format('Y-m-d H:i:s'),
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
