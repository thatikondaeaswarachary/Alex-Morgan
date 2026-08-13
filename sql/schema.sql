-- ==========================================================
-- Task 1, 2 & 3: Database Schema & Seed Data (portfolio_db)
-- Full Stack PHP & MySQL Development
-- Database Normalization: 1NF, 2NF, 3NF Enforced
-- ER Diagram Topology:
--   [roles] (1) <---> (N) [users] (Foreign Key: users.role_id -> roles.id)
-- ==========================================================

-- 1. Create Database if not exists
CREATE DATABASE IF NOT EXISTS `portfolio_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;

-- 2. Drop existing tables in reverse foreign-key order
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

-- 5. Create 'skills' table
CREATE TABLE `skills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `skill_name` VARCHAR(50) NOT NULL,
    `category` VARCHAR(50) NOT NULL, -- e.g., Frontend, Backend, Database, Tools
    `proficiency_percent` INT NOT NULL CHECK (`proficiency_percent` BETWEEN 0 AND 100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Create 'projects' table
CREATE TABLE `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL, -- e.g., frontend, backend, fullstack
    `description` TEXT NOT NULL,
    `tech_stack` VARCHAR(255) NOT NULL,
    `image_url` VARCHAR(255) DEFAULT 'assets/images/project-placeholder.jpg',
    `github_url` VARCHAR(255),
    `demo_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Create 'contacts' table (Handles contact form submissions)
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

-- Insert Roles (1 = Admin, 2 = User)
INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Administrator with full system privileges, CRUD user management access, and system settings.'),
(2, 'User', 'Standard user with profile edit privileges, avatar upload access, and personal dashboard access.');

-- Insert sample users (Task 3 RBAC & Profile Test Data)
-- Default password for seeded users is: Password123!
INSERT INTO `users` (`role_id`, `username`, `email`, `password_hash`, `full_name`, `title`, `phone`, `bio`, `avatar_url`) VALUES
(1, 'admin', 'admin@apexplanet.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'System Administrator', 'Senior Systems Architect', '+1 (555) 019-2834', 'Lead System Administrator overseeing security policies, database normalization, and RBAC user access control.', 'assets/uploads/admin-avatar.png'),
(2, 'alexmorgan', 'alex@example.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'Alex Morgan', 'Full Stack Web Developer', '+1 (555) 014-9821', 'Passionate web developer specializing in PHP, MySQL, JavaScript, and Bootstrap 5 responsive application design.', 'assets/uploads/alex-avatar.png'),
(2, 'johndoe', 'john@example.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'John Doe', 'Frontend Engineer', '+1 (555) 017-3342', 'Frontend specialist crafting intuitive interfaces and AJAX integrations.', 'assets/uploads/default-avatar.png');

-- Insert sample skills
INSERT INTO `skills` (`skill_name`, `category`, `proficiency_percent`) VALUES
('HTML5 & Semantic Web', 'Frontend', 95),
('CSS3, Flexbox & Grid', 'Frontend', 90),
('JavaScript (ES6+)', 'Frontend', 85),
('Bootstrap 5 Framework', 'Frontend', 92),
('PHP Core & OOP', 'Backend', 88),
('MySQL & Relational Databases', 'Database', 85),
('AJAX & Async Fetch API', 'Frontend', 88),
('Git & Version Control', 'Tools', 88);

-- Insert sample projects
INSERT INTO `projects` (`title`, `category`, `description`, `tech_stack`, `github_url`, `demo_url`) VALUES
('Personal Portfolio Website', 'frontend', 'Responsive single-page portfolio built with HTML5, CSS3, and JavaScript featuring dark mode and interactive filter.', 'HTML5, CSS3, JavaScript', 'https://github.com/example/portfolio', 'https://example.github.io/portfolio'),
('Interactive Login & Registration UI', 'fullstack', 'Task 2 deliverable: Responsive Auth UI built with Bootstrap 5, custom CSS glassmorphism, JS form validation, and real-time AJAX PHP endpoints.', 'Bootstrap 5, JS, PHP, AJAX, MySQL', 'https://github.com/example/auth-ui', 'auth.html'),
('User Management & Profile CRUD Suite', 'fullstack', 'Task 3 deliverable: Complete User CRUD dashboard, session authentication, role-based login (User/Admin), and profile picture upload.', 'PHP, MySQL, Session Auth, Bootstrap 5', 'https://github.com/example/user-crud', 'admin_users.php'),
('PHP Contact Management Module', 'backend', 'Backend form handler and validation script connected to MySQL database using PHP mysqli prepared statements.', 'PHP, MySQL, HTML5', 'https://github.com/example/php-contact', 'http://localhost/portfolio/contact.php');
