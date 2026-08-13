-- ==========================================================
-- Task 1, 2, 3 & 4: Database Schema & Seed Data (portfolio_db)
-- Full Stack PHP & MySQL Development
-- Database Normalization: 1NF, 2NF, 3NF Enforced
-- ER Diagram Topology:
--   [roles] (1) <---> (N) [users] (Foreign Key: users.role_id -> roles.id)
--   [categories] (1) <---> (N) [products] (Foreign Key: products.category_id -> categories.id)
--   [users] (1) <---> (N) [orders] (Foreign Key: orders.user_id -> users.id)
--   [orders] (1) <---> (N) [order_items] (Foreign Key: order_items.order_id -> orders.id)
--   [products] (1) <---> (N) [order_items] (Foreign Key: order_items.product_id -> products.id)
-- ==========================================================

-- 1. Create Database if not exists
CREATE DATABASE IF NOT EXISTS `portfolio_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;

-- 2. Drop existing tables in reverse foreign-key order
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `projects`;
DROP TABLE IF EXISTS `skills`;

-- 3. Create 'roles' table (3NF Normalization for RBAC)
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_name` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) DEFAULT '',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create 'users' table (Task 3: RBAC, Profile Avatar, Bio, Prepared Statements)
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL DEFAULT 2,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) DEFAULT '',
    `title` VARCHAR(100) DEFAULT 'Full Stack Developer',
    `phone` VARCHAR(30) DEFAULT '',
    `bio` TEXT,
    `avatar_url` VARCHAR(255) DEFAULT 'assets/uploads/default-avatar.png',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Create 'categories' table (Task 4: E-Commerce Product Categories)
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(50) NOT NULL UNIQUE,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `icon_class` VARCHAR(50) DEFAULT 'fa-box',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Create 'products' table (Task 4: Product Catalog with Indexes)
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `stock_qty` INT NOT NULL DEFAULT 10,
    `image_url` VARCHAR(255) DEFAULT 'assets/images/product-placeholder.jpg',
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_products_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_products_cat_price` (`category_id`, `price`),
    INDEX `idx_products_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Create 'orders' table (Task 4: Customer Order Transactions with Indexes)
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('Pending', 'Processing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    `shipping_address` TEXT NOT NULL,
    `payment_method` VARCHAR(50) DEFAULT 'Credit Card / Stripe Demo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_orders_user` (`user_id`),
    INDEX `idx_orders_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Create 'order_items' table (Task 4: Order Line Items Breakdown)
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10,2) NOT NULL,
    CONSTRAINT `fk_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_items_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_items_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Create 'skills' table
CREATE TABLE `skills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `skill_name` VARCHAR(50) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `proficiency_percent` INT NOT NULL CHECK (`proficiency_percent` BETWEEN 0 AND 100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Create 'projects' table
CREATE TABLE `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `tech_stack` VARCHAR(255) NOT NULL,
    `image_url` VARCHAR(255) DEFAULT 'assets/images/project-placeholder.jpg',
    `github_url` VARCHAR(255),
    `demo_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Create 'contacts' table
CREATE TABLE `contacts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(150) NOT NULL,
    `service_type` VARCHAR(50) DEFAULT 'General Inquiry',
    `message` TEXT NOT NULL,
    `newsletter` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- SEED DATA INSERTIONS
-- ==========================================================

-- Insert Roles
INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Administrator with full system privileges, CRUD access, and Analytics.'),
(2, 'User', 'Standard user with shopping, checkout, and profile access.');

-- Insert Users
INSERT INTO `users` (`role_id`, `username`, `email`, `password_hash`, `full_name`, `title`, `phone`, `bio`, `avatar_url`) VALUES
(1, 'admin', 'admin@apexplanet.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'System Administrator', 'Senior Systems Architect', '+1 (555) 019-2834', 'Lead System Administrator overseeing security policies, database normalization, and RBAC user access control.', 'assets/uploads/admin-avatar.png'),
(2, 'alexmorgan', 'alex@example.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'Alex Morgan', 'Full Stack Web Developer', '+1 (555) 014-9821', 'Passionate web developer specializing in PHP, MySQL, JavaScript, and Bootstrap 5 responsive application design.', 'assets/uploads/alex-avatar.png'),
(2, 'johndoe', 'john@example.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'John Doe', 'Frontend Engineer', '+1 (555) 017-3342', 'Frontend specialist crafting intuitive interfaces and AJAX integrations.', 'assets/uploads/default-avatar.png');

-- Insert Product Categories
INSERT INTO `categories` (`id`, `category_name`, `slug`, `icon_class`) VALUES
(1, 'Laptops & Workstations', 'laptops', 'fa-laptop'),
(2, 'Mechanical Keyboards', 'keyboards', 'fa-keyboard'),
(3, 'UltraWide Displays', 'displays', 'fa-desktop'),
(4, 'Audio & Accessories', 'accessories', 'fa-headphones');

-- Insert Tech Products
INSERT INTO `products` (`id`, `category_id`, `title`, `description`, `price`, `stock_qty`, `image_url`, `is_featured`) VALUES
(1, 1, 'DevPro M3 Max Workstation Laptop', '16-inch liquid retina display, 36GB unified memory, 1TB NVMe SSD. Ultra performance for docker and compiled builds.', 2499.99, 15, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600', 1),
(2, 2, 'Keychron Q1 Pro Wireless Mechanical Keyboard', '75% layout custom mechanical keyboard with hot-swappable tactile switches, RGB lighting, and CNC aluminum shell.', 199.99, 45, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600', 1),
(3, 3, 'Dell UltraSharp 38" Curved 4K Monitor', 'WQHD+ IPS curved monitor with Thunderbolt 4 connectivity, USB-C 90W power delivery, and sRGB 100% color accuracy.', 1199.00, 8, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600', 1),
(4, 4, 'Sony WH-1000XM5 Wireless Noise Canceling Headphones', 'Industry-leading ANC Bluetooth headphones with 30-hour battery life and crisp developer focus sound.', 398.00, 30, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600', 0),
(5, 2, 'Ergonomic Split Mechanical Keyboard', 'Ortholinear split mechanical keyboard with OLED screen status indicators and programmable QMK/VIA firmware.', 249.50, 12, 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=600', 0),
(6, 4, 'Logitech MX Master 3S Ergonomic Mouse', '8K DPI optical sensor with silent click switches and MagSpeed electromagnetic scrolling.', 99.99, 60, 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=600', 1);

-- Insert Sample Orders
INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `shipping_address`, `payment_method`, `created_at`) VALUES
(1, 2, 'ORD-2026-9001', 2699.98, 'Completed', '742 Evergreen Terrace, Springfield, OR', 'Credit Card (Stripe)', '2026-09-01 14:20:00'),
(2, 2, 'ORD-2026-9002', 398.00, 'Processing', '742 Evergreen Terrace, Springfield, OR', 'PayPal Demo', '2026-09-06 09:15:00'),
(3, 3, 'ORD-2026-9003', 199.99, 'Pending', '100 Tech Blvd, Silicon Valley, CA', 'Credit Card', '2026-09-08 11:30:00');

-- Insert Sample Order Items
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 2499.99),
(1, 2, 1, 199.99),
(2, 4, 1, 398.00),
(3, 2, 1, 199.99);

-- Insert sample skills
INSERT INTO `skills` (`skill_name`, `category`, `proficiency_percent`) VALUES
('HTML5 & Semantic Web', 'Frontend', 95),
('CSS3, Flexbox & Grid', 'Frontend', 90),
('JavaScript (ES6+)', 'Frontend', 85),
('Bootstrap 5 Framework', 'Frontend', 92),
('PHP Core & OOP', 'Backend', 90),
('MySQL & Relational Databases', 'Database', 88),
('AJAX & Async Fetch API', 'Frontend', 90),
('E-Commerce Architecture', 'Full Stack', 92),
('Git & Version Control', 'Tools', 88);

-- Insert sample projects
INSERT INTO `projects` (`title`, `category`, `description`, `tech_stack`, `github_url`, `demo_url`) VALUES
('Personal Portfolio Website', 'frontend', 'Responsive single-page portfolio built with HTML5, CSS3, and JavaScript featuring dark mode and interactive filter.', 'HTML5, CSS3, JavaScript', 'https://github.com/example/portfolio', 'https://example.github.io/portfolio'),
('Interactive Login & Registration UI', 'fullstack', 'Task 2 deliverable: Responsive Auth UI built with Bootstrap 5, custom CSS glassmorphism, JS form validation, and real-time AJAX PHP endpoints.', 'Bootstrap 5, JS, PHP, AJAX, MySQL', 'https://github.com/example/auth-ui', 'auth.html'),
('User Management & Profile CRUD Suite', 'fullstack', 'Task 3 deliverable: Complete User CRUD dashboard, session authentication, role-based login (User/Admin), and profile picture upload.', 'PHP, MySQL, Session Auth, Bootstrap 5', 'https://github.com/example/user-crud', 'admin_users.php'),
('DevGear Full Stack E-Commerce & Analytics', 'fullstack', 'Task 4 deliverable: Real-world Tech store with cart/checkout, customer order tracking, admin product CRUD & business intelligence dashboard.', 'PHP, MySQL, AJAX, Bootstrap 5, Indexes', 'https://github.com/example/devgear-store', 'store.php');
