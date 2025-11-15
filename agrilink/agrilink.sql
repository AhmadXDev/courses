-- AgriLink Database Schema
-- Drop database if exists to start fresh
DROP DATABASE IF EXISTS `agrilink`;
CREATE DATABASE agrilink;
USE agrilink;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'vendor', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    short_description VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) NOT NULL DEFAULT 'default.jpg',
    vendor_id INT NOT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    pickup_date DATE NOT NULL,
    status ENUM('pending', 'confirmed', 'ready', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert test data

-- Users (passwords are hashed version of 'password123')
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@agrilink.com', '$2y$10$TtMDD6G.J1dVUnf4cA3i4OJXd/MucRF6L8ewPdsS8MUdjJba9PcOq', 'admin'),
('Salem Alotaibi', 'salem@agrilink.com', '$2y$10$TtMDD6G.J1dVUnf4cA3i4OJXd/MucRF6L8ewPdsS8MUdjJba9PcOq', 'customer'),
('Fahad Alqahtani', 'fahad@agrilink.com', '$2y$10$TtMDD6G.J1dVUnf4cA3i4OJXd/MucRF6L8ewPdsS8MUdjJba9PcOq', 'customer'),
('Nasser Alharbi', 'nasser@agrilink.com', '$2y$10$TtMDD6G.J1dVUnf4cA3i4OJXd/MucRF6L8ewPdsS8MUdjJba9PcOq', 'vendor'),
('Abdullah Alghamdi', 'abdullah@agrilink.com', '$2y$10$TtMDD6G.J1dVUnf4cA3i4OJXd/MucRF6L8ewPdsS8MUdjJba9PcOq', 'vendor');


-- Products
INSERT INTO products (name, description, short_description, price, category, stock, image, vendor_id, is_featured) VALUES
('Organic Carrots', 'Fresh organic carrots grown without pesticides. Perfect for salads, cooking, or juicing.', 'Fresh organic carrots grown without pesticides.', 2.99, 'Vegetables', 50, 'carrots.jpg', 4, 1),
('Farm Fresh Eggs', 'Free-range eggs from pasture-raised chickens. Rich in flavor and nutrition.', 'Free-range eggs from pasture-raised chickens.', 4.50, 'Dairy & Eggs', 30, 'eggs.jpg', 4, 1),
('Artisan Sourdough Bread', 'Handcrafted sourdough bread made with organic flour and traditional fermentation techniques.', 'Handcrafted sourdough bread made with organic flour.', 6.99, 'Bakery', 15, 'sourdough.jpg', 4, 0),
('Raw Honey', 'Pure, unfiltered honey from local beehives. Great for sweetening tea or as a natural remedy.', 'Pure, unfiltered honey from local beehives.', 8.99, 'Honey & Preserves', 25, 'honey.jpg', 5, 1),
('Organic Whole Milk', 'Creamy, nutritious whole milk from grass-fed cows. Pasteurized but not homogenized.', 'Creamy, nutritious whole milk from grass-fed cows.', 3.99, 'Dairy & Eggs', 20, 'milk.jpg', 5, 0),
('Fresh Strawberries', 'Sweet, juicy strawberries picked at peak ripeness. Perfect for snacking or desserts.', 'Sweet, juicy strawberries picked at peak ripeness.', 4.99, 'Fruits', 40, 'strawberries.jpg', 4, 0);

-- Orders
INSERT INTO orders (user_id, total_amount, pickup_date, status, created_at) VALUES
(2, 15.97, '2023-06-15', 'completed', '2023-06-14 10:25:36'),
(3, 12.99, '2023-06-18', 'confirmed', '2023-06-17 15:42:10'),
(2, 21.98, '2023-06-20', 'pending', NOW());

-- Order Items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 2.99),
(1, 2, 1, 4.50),
(1, 4, 1, 8.99),
(2, 3, 1, 6.99),
(2, 5, 1, 3.99),
(2, 1, 1, 2.99),
(3, 6, 2, 4.99),
(3, 4, 1, 8.99),
(3, 3, 1, 6.99);

-- Add indexes for performance
CREATE INDEX idx_products_vendor ON products(vendor_id);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id); 