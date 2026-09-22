<?php

declare(strict_types=1);

namespace App\Router;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['pattern'], $path);

            if ($params !== null) {
                ($route['handler'])($params);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Route not found.']);
    }

    // Converts /api/products/{id} into a regex and extracts named params.
    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        // Return only named captures.
        return array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
    }
}
