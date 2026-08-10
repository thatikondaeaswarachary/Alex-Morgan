# PHP & MySQL Full Stack Web Development - Task 1 & Task 2

Welcome to the **PHP & MySQL Full Stack Web Development** project repository. This repository contains complete code deliverables for:
- **Task 1 (Days 1–12)**: Foundation, Environment Setup & Responsive Personal Portfolio Website.
- **Task 2 (Days 13–24)**: Interactive UI & Frontend Development (Bootstrap 5, JS Form Handling, Real-time AJAX PHP Endpoints & Responsive Login/Registration UI).

---

## 📋 Table of Contents
1. [Task 2 Specification Overview](#task-2-specification-overview)
2. [Environment Setup (XAMPP / WAMP / Apache / MySQL)](#1-environment-setup)
3. [phpMyAdmin & MySQL Database Management](#2-phpmyadmin--mysql-database-management)
4. [Git & GitHub Setup & Workflow](#3-git--github-setup--workflow)
5. [Course Topics Covered](#4-course-topics-covered)
   - [Bootstrap 5 Mastery & Custom Styling](#bootstrap-5-mastery--custom-styling)
   - [JavaScript Form Handling & Validation](#javascript-form-handling--validation)
   - [AJAX & PHP Async Data Processing](#ajax--php-async-data-processing)
6. [Project Structure](#5-project-structure)
7. [Running the Project Locally](#6-running-the-project-locally)
8. [GitHub Pages Deployment Guide](#7-github-pages-deployment-guide)

---

## Task 2 Specification Overview (Days 13–24)

**Objective**: Create responsive, user-friendly, and interactive interfaces using HTML, CSS, JavaScript, and Bootstrap 5.

### Key Steps Completed:
1. **Bootstrap 5 Mastery**:
   - Grid System: `row`, `col-12`, `col-lg-6`, breakpoint responsiveness.
   - Core Components: Responsive Navbar, Feature Showcase Carousel, Glassmorphism Cards, Quick Spec & Terms Modals, Animated Action Buttons.
   - Display Utilities: `d-none`, `d-md-block`, `d-flex`, `flex-column flex-md-row`.
2. **Custom Styling**:
   - Palette: Cyan/Emerald (`#38bdf8`, `#10b981`), Obsidian Navy (`#0b0f19`), Glassmorphism.
   - Micro-interactions: `@keyframes pulseGlow`, `@keyframes statusFadeIn`, smooth scrolling, hover scaling.
   - Fonts & Icons: Google Fonts (`Inter`, `Fira Code`) + FontAwesome v6.4.0.
3. **Form Handling with JS**:
   - Live client-side validation for Login & Registration forms.
   - Live **Password Match check** (`confirm_password === password`).
   - Password strength calculation meter bar.
   - **Show/Hide Password toggle** with icon state switching.
4. **AJAX Basics**:
   - Real-time debounced AJAX availability checker (`ajax_check_user.php`).
   - Asynchronous form submission (`ajax_auth.php`) using native `fetch()` API without page reloads.
5. **Deliverables**:
   - Responsive Login & Registration UI page (`auth.html`).
   - PHP AJAX backend scripts & updated MySQL database schema (`users` table).

---

## 1. Environment Setup

### A. Installing XAMPP (Recommended for Windows)
1. Download XAMPP for Windows from the official website: [Apache Friends](https://www.apachefriends.org/).
2. Run the installer and select Apache, MySQL, PHP, and phpMyAdmin.
3. Install XAMPP into `C:\xampp`.
4. Open the **XAMPP Control Panel** and click **Start** next to **Apache** and **MySQL**.

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

