```sql
CREATE TABLE products (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE (sku)
);

CREATE TABLE suppliers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
);

CREATE TABLE product_suppliers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    price DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id),
    UNIQUE (product_id, supplier_id),

    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),

    CHECK (price > 0)
);

CREATE TABLE inventory_movements (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id INT UNSIGNED NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    FOREIGN KEY (product_id) REFERENCES products(id),

    CHECK (quantity > 0)
);

CREATE TABLE sales (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
);

CREATE TABLE sale_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    sale_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id),

    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id),

    CHECK (quantity > 0),
    CHECK (unit_price > 0)
);
```