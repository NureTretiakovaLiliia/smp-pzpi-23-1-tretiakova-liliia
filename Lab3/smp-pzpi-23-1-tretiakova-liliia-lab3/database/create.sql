// створення бд для 3-4 лабораторних робіт
CREATE DATABASE smp_lab;
USE smp_lab;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    imageUrl VARCHAR(255),
    price DECIMAL(10,2) NOT NULL
);

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_token VARCHAR(64) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    
    UNIQUE (cart_token, product_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
