-- ==========================================================
-- Task 1: Database Schema & Seed Data (portfolio_db)
-- Full Stack PHP & MySQL Development
-- ==========================================================

-- 1. Create Database if not exists
CREATE DATABASE IF NOT EXISTS `portfolio_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;

-- 2. Drop existing tables to ensure clean setup
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `projects`;
DROP TABLE IF EXISTS `skills`;

-- 3. Create 'users' table (Task 2: AJAX Authentication & User Management)
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) DEFAULT '',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create 'skills' table
CREATE TABLE `skills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `skill_name` VARCHAR(50) NOT NULL,
    `category` VARCHAR(50) NOT NULL, -- e.g., Frontend, Backend, Database, Tools
    `proficiency_percent` INT NOT NULL CHECK (`proficiency_percent` BETWEEN 0 AND 100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Create 'projects' table
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

-- 6. Create 'contacts' table (Handles contact form submissions)
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

-- Insert sample users (Task 2 AJAX check test data)
-- Default password for seeded users is: Password123!
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`) VALUES
('alexmorgan', 'alex@example.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'Alex Morgan'),
('johndoe', 'john@example.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'John Doe'),
('admin', 'admin@apexplanet.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8Q0gR/W4S7gK5YhP3V3C8n2m9W5W.', 'System Admin');

-- Insert sample skills
INSERT INTO `skills` (`skill_name`, `category`, `proficiency_percent`) VALUES
('HTML5 & Semantic Web', 'Frontend', 95),
('CSS3, Flexbox & Grid', 'Frontend', 90),
('JavaScript (ES6+)', 'Frontend', 85),
('Bootstrap 5 Framework', 'Frontend', 92),
('PHP Core & OOP', 'Backend', 80),
('MySQL & Relational Databases', 'Database', 82),
('AJAX & Async Fetch API', 'Frontend', 88),
('Git & Version Control', 'Tools', 88);

-- Insert sample projects
INSERT INTO `projects` (`title`, `category`, `description`, `tech_stack`, `github_url`, `demo_url`) VALUES
('Personal Portfolio Website', 'frontend', 'Responsive single-page portfolio built with HTML5, CSS3, and JavaScript featuring dark mode and interactive filter.', 'HTML5, CSS3, JavaScript', 'https://github.com/example/portfolio', 'https://example.github.io/portfolio'),
('Interactive Login & Registration UI', 'fullstack', 'Task 2 deliverable: Responsive Auth UI built with Bootstrap 5, custom CSS glassmorphism, JS form validation, and real-time AJAX PHP endpoints.', 'Bootstrap 5, JS, PHP, AJAX, MySQL', 'https://github.com/example/auth-ui', 'auth.html'),
('PHP Contact Management Module', 'backend', 'Backend form handler and validation script connected to MySQL database using PHP mysqli prepared statements.', 'PHP, MySQL, HTML5', 'https://github.com/example/php-contact', 'http://localhost/portfolio/contact.php'),
('Dynamic Task Manager App', 'fullstack', 'Full-stack task tracker application featuring AJAX form submissions, user sessions, and database CRUD actions.', 'PHP, MySQL, JavaScript, Bootstrap', 'https://github.com/example/task-manager', 'http://localhost/portfolio/tasks');
