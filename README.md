# Inventory Management System

A REST API built with pure PHP, PDO, and MySQL. No framework.

---

## Project Overview

This project manages products, suppliers, inventory stock movements, and sales. It exposes a JSON REST API and uses a MySQL database for storage. All database access goes through PDO.

---

## Features

- Create and list products and suppliers
- Link products to suppliers with individual pricing per supplier
- Record stock IN and OUT movements
- Calculate current stock from movement history
- Protect against selling more than what is in stock
- Create sales with multiple line items
- Roll back a sale completely if any item fails

---

## Architecture

```
HTTP Request → Router → Controller → Service → Repository → PDO → MySQL
```

- **Router** — reads the HTTP method and URL, matches a route pattern, and calls the right controller method
- **Controller** — reads the request body, calls the service or repository, and returns a JSON response
- **Service** — contains business logic (stock checks, transactions) no SQL here  
- **Repository** — contains all SQL queries returns entity objects
- **Entity** — a plain PHP class that represents a database row (no SQL, no business logic)

---

## Database Design

Six tables:

| Table | Purpose |
|---|---|
| `products` | Product catalogue |
| `suppliers` | Supplier directory |
| `product_suppliers` | Links products to suppliers stores price per supplier |
| `inventory_movements` | Append-only log of every stock IN and OUT |
| `sales` | Sale header (one row per transaction) |
| `sale_items` | Line items within a sale |

**Key design decisions:**

- `products` and `suppliers` have a many-to-many relationship through `product_suppliers`. Price is stored there because the same product can cost different amounts from different suppliers.
- Current stock is not stored anywhere. It is calculated as `SUM(IN quantities) - SUM(OUT quantities)` from `inventory_movements`. This avoids keeping two values in sync.
- `sale_items` stores `unit_price` at the time of the sale so the financial record stays accurate even if the supplier price changes later.

---

## Design Pattern

The project uses the **Repository Pattern**.

Each repository class handles all SQL for one part of the domain (`ProductRepository`, `SaleRepository`, etc.). Services call repository methods and get back PHP entity objects. This means:

- SQL stays inside repositories, not scattered across the codebase
- Services contain business rules but no queries
- Controllers stay thin — they just read the request and return a response

---

## Stock Management

- `POST /api/inventory/in` records a stock IN movement
- `POST /api/inventory/out` records a stock OUT movement
- Current stock = total IN − total OUT for a product
- If the requested OUT quantity is greater than current stock, the API returns `409 Conflict`
- When creating a sale, all writes (sale, sale items, OUT movements) happen inside a database transaction. If anything fails, everything rolls back.

---

## API Endpoints

### Products

| GET | `/api/products` | 200 | List all products |
| GET | `/api/products/{id}` | 200 | Get one product |
| POST | `/api/products` | 201 | Create a product |

Request body for POST:
```json
{
    "name": "Mechanical Keyboard",
    "sku": "KB-001"
}
```

### Suppliers

| GET | `/api/suppliers` | 200 | List all suppliers |
| POST | `/api/suppliers` | 201 | Create a supplier |

Request body for POST:
```json
{
    "name": "ABC Supplier",
    "phone": "0912345678"
}
```

### Inventory

| POST | `/api/inventory/in` | 201 | Record a stock IN movement |
| POST | `/api/inventory/out` | 201 | Record a stock OUT movement |

Request body:
```json
{
    "productId": 1,
    "quantity": 10
}
```

### Sales

| POST | `/api/sales` | 201 | Create a sale with line items |
| GET | `/api/sales/{id}` | 200 | Get a sale with its items |

Request body for POST:
```json
{
    "items": [
        {
            "productId": 1,
            "quantity": 2,
            "unitPrice": 49.99
        }
    ]
}
```

### Error responses

| 400 | Missing required fields in request body |
| 404 | Product or sale not found, or route does not exist |
| 409 | Not enough stock to complete the operation |


