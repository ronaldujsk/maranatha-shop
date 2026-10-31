
-- ==========================================
-- MARANATHA SHOP
-- ESTRUCTURA INICIAL DE LA BASE DE DATOS
-- ==========================================

CREATE DATABASE IF NOT EXISTS maranatha_shop
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE maranatha_shop;

-- ==========================================
-- 1. USUARIOS
-- ==========================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30),
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 2. DIRECCIONES
-- ==========================================

CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    additional_info VARCHAR(255),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 3. CATEGORIAS
-- ==========================================

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (parent_id)
    REFERENCES categories(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 4. PROVEEDORES
-- ==========================================

CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(120),
    email VARCHAR(150),
    phone VARCHAR(30),
    address VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- 5. PRODUCTOS
-- ==========================================

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    sku VARCHAR(80) UNIQUE,
    description TEXT,
    price DECIMAL(12,2) NOT NULL,
    cost_price DECIMAL(12,2) DEFAULT NULL,
    stock_control ENUM('tracked', 'untracked')
    DEFAULT 'tracked',
    status ENUM('active', 'inactive', 'draft')
    DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id)
    REFERENCES categories(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 6. RELACION PRODUCTOS Y PROVEEDORES
-- ==========================================

CREATE TABLE IF NOT EXISTS product_suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    supplier_id INT NOT NULL,
    supplier_price DECIMAL(12,2),
    supplier_product_code VARCHAR(100),
    is_primary BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (product_id)
    REFERENCES products(id)
    ON DELETE CASCADE,

    FOREIGN KEY (supplier_id)
    REFERENCES suppliers(id)
    ON DELETE CASCADE,

    UNIQUE KEY unique_product_supplier
    (product_id, supplier_id)
) ENGINE=InnoDB;

-- ==========================================
-- 7. INVENTARIO
-- ==========================================

CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL UNIQUE,
    quantity INT NOT NULL DEFAULT 0,
    minimum_quantity INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id)
    REFERENCES products(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 8. MOVIMIENTOS DE INVENTARIO
-- ==========================================

CREATE TABLE IF NOT EXISTS inventory_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    movement_type ENUM(
        'entry',
        'exit',
        'adjustment'
    ) NOT NULL,
    quantity INT NOT NULL,
    reason VARCHAR(255),
    reference VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id)
    REFERENCES products(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ==========================================
-- 9. IMAGENES DE PRODUCTOS
-- ==========================================

CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id)
    REFERENCES products(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 10. CARRITOS
-- ==========================================

CREATE TABLE IF NOT EXISTS carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_token VARCHAR(150) NULL,
    status ENUM('active', 'converted', 'abandoned')
    DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 11. PRODUCTOS DEL CARRITO
-- ==========================================

CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,

    FOREIGN KEY (cart_id)
    REFERENCES carts(id)
    ON DELETE CASCADE,

    FOREIGN KEY (product_id)
    REFERENCES products(id)
    ON DELETE CASCADE,

    UNIQUE KEY unique_cart_product
    (cart_id, product_id)
) ENGINE=InnoDB;

-- ==========================================
-- 12. PEDIDOS
-- ==========================================

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(40) NOT NULL UNIQUE,
    user_id INT NULL,

    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150),
    customer_phone VARCHAR(30) NOT NULL,

    delivery_address VARCHAR(255),
    delivery_city VARCHAR(100),
    delivery_department VARCHAR(100),
    delivery_notes TEXT,

    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    shipping_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    discount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,

    order_status ENUM(
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled'
    ) DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 13. PRODUCTOS DE CADA PEDIDO
-- ==========================================

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NULL,

    product_name VARCHAR(180) NOT NULL,
    product_sku VARCHAR(80),
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,

    FOREIGN KEY (order_id)
    REFERENCES orders(id)
    ON DELETE CASCADE,

    FOREIGN KEY (product_id)
    REFERENCES products(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==========================================
-- 14. PAGOS
-- ==========================================

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,

    payment_method ENUM(
        'pse',
        'nequi',
        'credit_card',
        'bank_transfer',
        'cash_on_delivery',
        'other'
    ) NOT NULL,

    payment_provider VARCHAR(100),
    transaction_id VARCHAR(150),

    amount DECIMAL(12,2) NOT NULL,

    payment_status ENUM(
        'pending',
        'processing',
        'approved',
        'rejected',
        'refunded'
    ) DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id)
    REFERENCES orders(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================
-- 15. CUPONES
-- ==========================================

CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,

    discount_type ENUM('percentage', 'fixed')
    NOT NULL,

    discount_value DECIMAL(12,2) NOT NULL,
    minimum_purchase DECIMAL(12,2) DEFAULT 0,

    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,

    start_date DATETIME NULL,
    expiration_date DATETIME NULL,

    status ENUM('active', 'inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- INDICES PARA CONSULTAS FRECUENTES
-- ==========================================

CREATE INDEX idx_products_category
ON products(category_id);

CREATE INDEX idx_products_status
ON products(status);

CREATE INDEX idx_orders_user
ON orders(user_id);

CREATE INDEX idx_orders_status
ON orders(order_status);

CREATE INDEX idx_payments_status
ON payments(payment_status);

CREATE INDEX idx_movements_product
ON inventory_movements(product_id);

-- ==========================================
-- FIN DE LA ESTRUCTURA INICIAL
-- ==========================================
