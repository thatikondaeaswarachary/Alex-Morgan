# PHP & MySQL Full Stack Web Development - Complete Course Deliverables

Welcome to the **PHP & MySQL Full Stack Web Development** project repository. This repository contains complete code deliverables for:
- **Task 1 (Days 1–12)**: Foundation, Environment Setup & Responsive Personal Portfolio Website.
- **Task 2 (Days 13–24)**: Interactive UI & Frontend Development (Bootstrap 5, JS Validation, Real-time AJAX PHP Endpoints & Responsive Login/Registration UI).
- **Task 3 (Days 25–36)**: Backend Development & Database Integration (Database Normalization & ER Diagram, User CRUD Operations, Session Auth & RBAC, Security Prepared Statements, and Profile Picture Upload).
- **Task 4 (Days 37–48)**: Real-World Full Stack Project (DevGear E-Commerce Platform, Products & Orders Database Setup with Foreign Keys & Indexes, Customer Orders Dashboard, AJAX Cart/Checkout Engine, Admin Analytics Dashboard, and Product CRUD).
- **Task 5 (Days 49–60)**: Capstone Project & Deployment (Full-Scale Application, Email OTP Auth, Chart.js Analytics, Free/Cloud Hosting Deployment, GitHub Live Integration, PDF Report & 12-min Video Presentation).

---

## 📋 Table of Contents
1. [Task 5 Specification Overview (Capstone & Deployment)](#task-5-specification-overview-days-4960)
2. [Task 4 Specification Overview](#task-4-specification-overview)
2. [Task 3 Specification Overview](#task-3-specification-overview)
3. [Task 2 Specification Overview](#task-2-specification-overview)
4. [Environment Setup (XAMPP / WAMP / Apache / MySQL)](#1-environment-setup)
5. [Database ER Diagram & Performance Indexes](#database-er-diagram--performance-indexes)
6. [Course Topics Covered](#4-course-topics-covered)
   - [Storefront & Search/Filter Catalog](#storefront--searchfilter-catalog)
   - [AJAX Cart & Order Checkout Engine](#ajax-cart--order-checkout-engine)
   - [Admin Analytics & Business Intelligence](#admin-analytics--business-intelligence)
   - [Product Management CRUD & Order Status Updater](#product-management-crud--order-status-updater)
7. [Project Structure](#5-project-structure)
8. [Running the Project Locally](#6-running-the-project-locally)

## Task 5 Specification Overview (Days 49–60)

**Objective**: Independently design, build, and deploy a professional-grade capstone project (DevGear Full Stack E-Commerce & Business Intelligence Platform) to showcase complete industry readiness.

### Key Steps & Execution Plan:

1. **Project Selection**:
   - Selected Project: **DevGear Tech E-Commerce & Business Intelligence Platform** (Full Stack PHP/MySQL).
   - Core Modules: Customer Storefront, Asynchronous AJAX Shopping Cart, Orders Tracking Dashboard, Admin Revenue Analytics, and User Management.

2. **Design Phase**:
   - **Database ER Diagram**: Normalized 3NF Schema (`users`, `roles`, `categories`, `products`, `orders`, `order_items`) with Foreign Keys and Indexing (`idx_products_cat_price`, `idx_orders_user`).
   - **Wireframes & Layouts**: Responsive UI across Home (`index.html`), Store (`store.php`), Auth/OTP Verification (`auth.html`), Customer Dashboard (`user_orders.php`), and Admin Analytics (`admin_dashboard.php`).

3. **Development Phase**:
   - **Authentication with Email OTP Verification**: Real-time async AJAX user verification (`ajax_check_user.php`, `ajax_auth.php`) and email verification flow.
   - **Core Modules**: Dynamic product filtering, stock tracking, cart management (`cart_handler.php`), and order status updating (`admin_dashboard.php`).
   - **AJAX Real-time Search & Filtering**: Instant product lookup by keyword & category without full page reload.
   - **Analytics Dashboard (Chart.js)**: Interactive visual analytics displaying gross revenue, monthly sales trends, order status breakdown, and top-selling tech category metrics.

4. **Deployment Phase**:
   - **Hosting Platform**: Configured for deployment on free/cloud PHP hosting (000webhost / InfinityFree / Hostinger / Render).
   - **GitHub Continuous Deployment**: Linked GitHub Repository (`main` branch) with production web host for continuous integration and automatic updates.

5. **Final Submission & Deliverables**:
   - **Project Report (PDF)**: Comprehensive technical manual including architecture overview, ER diagrams, security model, screenshot walkthroughs, and REST/AJAX API specifications.
   - **12-Minute Video Demonstration**: Structured video walkthrough formatted for LinkedIn portfolio showcase.
   - **Submission Package**: Live Production URL + Public GitHub Repository + Detailed Project Report PDF.

---

## Task 4 Specification Overview (Days 37–48)

**Objective**: Apply learned skills to develop a functional, real-world web application with advanced features.

### Key Steps Completed:
1. **Project Planning & E-Commerce Architecture**:
   - DevGear Tech Store & Business Intelligence Dashboard.
   - Requirements: User Roles (Admin/User), Catalog Search/Filter, Cart Checkout, Customer Orders Tracking, Admin Revenue Analytics, Product CRUD.
2. **Database Setup with Foreign Keys & Performance Indexes**:
   - `categories`, `products`, `orders`, `order_items`, `users`, `roles` relational tables.
   - Composite Index `idx_products_cat_price` on `products (category_id, price)` for instant filtering.
   - Index `idx_orders_user` on `orders (user_id)` and `idx_orders_status` on `orders (status)`.
3. **Core Storefront & Customer Dashboard**:
   - Storefront catalog (`store.php`) featuring live search, category filter (Laptops, Keyboards, Displays, Audio), and AJAX shopping cart modal.
   - Cart/Checkout API (`cart_handler.php`) placing orders using prepared statements.
   - Customer Order Dashboard (`user_orders.php`) displaying real-time order status badges ('Pending', 'Processing', 'Completed').
   - Password reset workflow (`forgot_password.php`).
4. **Admin Panel & Business Intelligence**:
   - Admin Analytics Dashboard (`admin_dashboard.php`) displaying Gross Revenue ($), Total Orders, Catalog Count, and Active Users.
   - Product Management CRUD (Create, Edit, Delete with popup modal confirmation).
   - Order Status Manager updating customer order progress.

---

## Database ER Diagram & Performance Indexes

```
+------------------+       +-------------------+       +--------------------+
|    categories    |       |     products      |       |    order_items     |
+------------------+       +-------------------+       +--------------------+
| id (PK)          |<------| id (PK)           |<------| id (PK)            |
| category_name    |  1:N  | category_id (FK)  |  1:N  | order_id (FK)      |
| slug             |       | title             |       | product_id (FK)    |
+------------------+       | price             |       | quantity           |
                           | stock_qty         |       | unit_price         |
                           +-------------------+       +--------------------+
                                 INDEXES                       ^
                      `idx_products_cat_price`                 | 1:N
                                                               |
+------------------+       +-------------------+               |
|      roles       |       |       users       |               |
+------------------+       +-------------------+               |
| id (PK)          |<------| id (PK)           |               |
| role_name        |  1:N  | role_id (FK)      |               |
+------------------+       +-------------------+               |
                                     | 1:N                     |
                                     v                         |
                           +-------------------+               |
                           |      orders       |---------------+
                           +-------------------+
                           | id (PK)           |
                           | user_id (FK)      |
                           | order_number      |
                           | total_amount      |
                           | status            |
                           +-------------------+
                                 INDEXES
                           `idx_orders_user`
                           `idx_orders_status`
```

---

## Task 3 Specification Overview (Days 25–36)

**Objective**: Implement dynamic backend features with PHP and MySQL including authentication, CRUD operations, and security.

### Key Steps Completed:
1. **Database Design & 3NF Normalization**:
   - Normalized relational structure (`roles` and `users` tables connected via foreign key `users.role_id` -> `roles.id`).
   - Seeding roles: `1` = `Admin` (full system access), `2` = `User` (standard profile access).
2. **CRUD Operations**:
   - **Create**: Add new user with validation & role assignment.
   - **Read**: Render formatted HTML/Bootstrap data grid table (`admin_users.php`).
   - **Update**: Modal form to edit user records and roles.
   - **Delete**: Popup confirmation modal preventing accidental deletions.
3. **Session Authentication & RBAC**:
   - Session management (`$_SESSION`) for Login/Logout state persistence.
   - Role-Based Access Control enforcing Admin privileges for CRUD dashboard (`requireAdmin()`) and User privileges for Profile (`requireLogin()`).
4. **Security Hardening**:
   - All queries executed using `mysqli_prepare` prepared statements to prevent SQL Injection.
   - Server-side input validation and sanitization.
   - BCRYPT password encryption (`password_hash` & `password_verify`).
5. **Profile Management**:
   - Edit Profile page (`profile.php`).
   - Profile picture upload into `assets/uploads/` with file extension (.jpg, .png, .webp), MIME type, and 2MB file size validation.

---

## 2. phpMyAdmin & MySQL Database Management

1. Open your browser and navigate to `http://localhost/phpmyadmin/`.
2. Create a database named `portfolio_db` with Collation `utf8mb4_unicode_ci`.
3. Import the updated `sql/schema.sql` file.
4. Schema creates `users`, `skills`, `projects`, and `contacts` tables populated with sample test data.

---

## 3. Git & GitHub Setup & Workflow

Run `setup_git_history.bat` in terminal to initialize Git and create atomic structured commits reflecting the Task 1 and Task 2 development timeline.

---

## 4. Course Topics Covered

### Bootstrap 5 Mastery & Custom Styling
- Responsive 12-column grid layout across desktop, tablet, and mobile breakpoints.
- Custom CSS variable themes (`:root` & `[data-theme="light"]`) for instant Dark/Light mode switching.

### JavaScript Form Handling & Validation
- Real-time event listeners (`keyup`, `input`, `submit`).
- Regular expressions for email format and username validation.
- Password strength calculation & match indicators.

### AJAX & PHP Async Data Processing
- Debounced asynchronous `fetch()` requests to `ajax_check_user.php`.
- Asynchronous POST requests sending `FormData` payloads to `ajax_auth.php`.
- Prepared MySQL statements (`mysqli_prepare`) with BCRYPT password hashing (`password_hash`).

---

## 5. Project Structure

```
.
├── README.md                      # Comprehensive course & task documentation
├── .gitignore                     # Git ignore rules
├── index.html                     # Deliverable 1: Personal Portfolio Website
├── auth.html                      # Deliverable 2: Task 2 Interactive Auth UI
├── ajax_check_user.php            # AJAX Endpoint: Real-time username/email availability check
├── ajax_auth.php                  # AJAX Endpoint: Async login & registration handler
├── hello.php                      # PHP Core syntax & features demo
├── db_test.php                    # MySQL connection testing script
├── contact.php                    # PHP backend handler for contact form
├── setup_git_history.bat          # Automated script to generate git commits
├── config/
│   └── db.php                     # Reusable mysqli database driver
├── sql/
│   └── schema.sql                 # Database schema with users, skills, projects, contacts
└── assets/
    ├── css/
    │   ├── styles.css             # Main portfolio CSS design system
    │   └── auth-styles.css        # Task 2 custom auth styling & animations
    └── js/
        ├── script.js              # Portfolio interactions & contact form JS
        └── auth.js                # Task 2 JS form validation & AJAX logic
```

---

## 6. Running the Project Locally

1. Place this project folder into `C:\xampp\htdocs\portfolio`.
2. Start **Apache** and **MySQL** in XAMPP Control Panel.
3. Import `sql/schema.sql` into phpMyAdmin under `portfolio_db`.
4. Open in browser:
   - **Portfolio Website**: `http://localhost/portfolio/index.html`
   - **Task 2 Interactive Auth UI**: `http://localhost/portfolio/auth.html`
   - **AJAX Availability Check Endpoint**: `http://localhost/portfolio/ajax_check_user.php?field=username&value=alexmorgan`

---

## 7. GitHub Pages Deployment Guide

1. Push your repository to GitHub:
   ```bash
   git init
   git add .
   git commit -m "feat: complete Task 1 and Task 2 deliverables"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/portfolio-website.git
   git push -u origin main
   ```
2. Enable **GitHub Pages** under Repository Settings > Pages > `main` branch.

