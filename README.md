# PHP & MySQL Full Stack Web Development - Task 1: Foundation & Environment Setup

Welcome to **Task 1 (Days 1–12)** of the **PHP & MySQL Full Stack Web Development** course. This repository contains the complete codebase, environment setup guides, PHP/MySQL test scripts, and the primary deliverable: a **Responsive Personal Portfolio Website**.

---

## 📋 Table of Contents
1. [Environment Setup (XAMPP / WAMP / Apache / MySQL)](#1-environment-setup)
2. [phpMyAdmin & MySQL Database Management](#2-phpmyadmin--mysql-database-management)
3. [Git & GitHub Setup & Workflow](#3-git--github-setup--workflow)
4. [Course Topics Covered](#4-course-topics-covered)
   - [HTML5 Fundamentals](#html5-fundamentals)
   - [CSS3 Styling & Layouts](#css3-styling--layouts)
   - [JavaScript Basics](#javascript-basics)
   - [PHP Basics](#php-basics)
   - [MySQL Basics](#mysql-basics)
5. [Project Structure](#5-project-structure)
6. [Running the Project Locally](#6-running-the-project-locally)
7. [GitHub Pages Deployment Guide](#7-github-pages-deployment-guide)

---

## 1. Environment Setup

### A. Installing XAMPP (Recommended for Windows)
1. Download XAMPP for Windows from the official website: [Apache Friends](https://www.apachefriends.org/).
2. Run the installer and select the following components:
   - **Apache** (Web Server)
   - **MySQL** (Database Server)
   - **PHP** (Server-side Scripting Language)
   - **phpMyAdmin** (Database Management Interface)
3. Install XAMPP into `C:\xampp`.
4. Open the **XAMPP Control Panel**.
5. Click **Start** next to **Apache** and **MySQL**.
6. Verify Apache is running by opening `http://localhost/` in your browser.

### B. Configuring Environment PATH (Optional)
To use `php` and `mysql` directly from your command line / terminal:
1. Open Windows Search, type **Environment Variables**, and select **Edit the system environment variables**.
2. Click **Environment Variables...**.
3. Under System Variables, select **Path** and click **Edit...**.
4. Click **New** and add: `C:\xampp\php` and `C:\xampp\mysql\bin`.
5. Click **OK** and restart your terminal.

---

## 2. phpMyAdmin & MySQL Database Management

1. Open your browser and navigate to `http://localhost/phpmyadmin/`.
2. Click on the **Databases** tab.
3. Create a new database named `portfolio_db` with Collation `utf8mb4_unicode_ci`.
4. Select `portfolio_db`, navigate to the **SQL** tab.
5. Paste the contents of `sql/schema.sql` located in this project and click **Go**.
6. This will automatically set up the `contacts`, `projects`, and `skills` tables with primary keys, auto-increments, and sample seed data.

---

## 3. Git & GitHub Setup & Workflow

### Essential Git Commands
- **Initialize Repository**: `git init`
- **Check Status**: `git status`
- **Stage Files**: `git add .` (or `git add <filename>`)
- **Commit Changes**: `git commit -m "feat: descriptive message"`
- **Connect Remote Repository**: `git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git`
- **Push to GitHub**: `git push -u origin main`
- **Pull Latest Changes**: `git pull origin main`

### Auto-Generating Commit History
Run `setup_git_history.bat` in the terminal to initialize Git and create at least 10 structured commits reflecting the 12-day setup timeline.

---

## 4. Course Topics Covered

### HTML5 Fundamentals
- **Semantic Structure**: `<header>`, `<nav>`, `<section>`, `<article>`, `<footer>`, `<main>`, `<aside>`.
- **Forms**: Text inputs, email validation, textareas, dropdowns (`<select>`), radio buttons, checkboxes, required attributes.
- **Multimedia**: Native `<video>`, `<audio>` players, responsive `<iframe>` embeds.
- **Tables**: Styled data grids using `<thead>`, `<tbody>`, and `<tfoot>`.

### CSS3 Styling & Layouts
- **CSS Styling**: External stylesheets, CSS Custom Properties (`--bg-primary`, `--accent-color`).
- **Flexbox & Grid**: CSS Grid for 2D project cards, Flexbox for navigation and card items.
- **Visuals**: Glassmorphism (`backdrop-filter`), CSS linear gradients, soft box-shadows, rounded borders.
- **Animations**: CSS transitions (`hover`), `@keyframes` gradient shifts, smooth scrolling (`scroll-behavior: smooth`).
- **Media Queries**: Mobile-first responsive layouts for desktop, tablet, and mobile devices (`@media (max-width: 768px)`).

### JavaScript Basics
- **Syntax**: `let`, `const`, arrow functions, array methods (`filter`, `forEach`).
- **DOM Manipulation**: `document.querySelector`, `element.classList.toggle`, updating innerText/HTML.
- **Event Handling**: `click`, `keyup`, `change`, `submit` event listeners.
- **Interactive Features**: Dark/Light mode switcher with `localStorage`, interactive portfolio project filter, animated skill bars, mobile nav drawer.
- **Form Validation**: Client-side validation for contact forms (email regex, mandatory fields, password match toggle demo).

### PHP Basics
- **Syntax & Output**: `echo`, `print`, short tags.
- **Data Structures**: Indexed arrays, associative arrays, multidimensional arrays.
- **Functions & Logic**: Custom functions, `if / else`, `switch`, `for`, `foreach` loops.
- **Form Handling**: `$_GET` and `$_POST` superglobals, server-side sanitization with `htmlspecialchars()`.
- **File Inclusion**: Reusable components using `include()` and `require()`.
- **Environment Test**: See `hello.php` for live code examples.

### MySQL Basics
- **Database Operations**: `CREATE DATABASE`, `CREATE TABLE`, `ALTER TABLE`.
- **CRUD Queries**: `INSERT INTO`, `SELECT`, `UPDATE`, `DELETE`.
- **Constraints**: `PRIMARY KEY`, `FOREIGN KEY`, `AUTO_INCREMENT`, `DEFAULT CURRENT_TIMESTAMP`.
- **PHP Integration**: `mysqli_connect()`, checking connection errors, executing queries, fetching associative rows (`mysqli_fetch_assoc()`).

---

## 5. Project Structure

```
.
├── README.md                      # Comprehensive course & environment guide
├── .gitignore                     # Git ignore rules for web projects
├── hello.php                      # Comprehensive PHP syntax & features demo
├── db_test.php                    # MySQL connection & query testing script
├── contact.php                    # PHP backend handler for contact form submissions
├── setup_git_history.bat          # Automated script to generate 10+ git commits
├── config/
│   └── db.php                     # Reusable mysqli connection script
├── sql/
│   └── schema.sql                 # SQL schema, tables & sample data
├── assets/
│   ├── css/
│   │   └── styles.css             # Main CSS stylesheet with animations & themes
│   └── js/
│       └── script.js              # Interactivity, themes & form validation
└── index.html                     # Deliverable 1: Personal Portfolio Website
```

---

## 6. Running the Project Locally

1. Move or clone this project folder into `C:\xampp\htdocs\portfolio` (or your WAMP `www` folder).
2. Start **Apache** and **MySQL** in XAMPP Control Panel.
3. Import `sql/schema.sql` into phpMyAdmin under `portfolio_db`.
4. Access the web applications in your browser:
   - **Portfolio Website**: `http://localhost/portfolio/index.html`
   - **PHP Basics Demo**: `http://localhost/portfolio/hello.php`
   - **MySQL Connection Test**: `http://localhost/portfolio/db_test.php`

---

## 7. GitHub Pages Deployment Guide

1. Push your repository to GitHub:
   ```bash
   git init
   git add .
   git commit -m "feat: complete Task 1 portfolio project"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/portfolio-website.git
   git push -u origin main
   ```
2. Go to your repository on **GitHub.com**.
3. Navigate to **Settings** > **Pages**.
4. Under **Build and deployment** > **Source**, select **Deploy from a branch**.
5. Select **main** branch and `/ (root)` folder, then click **Save**.
6. GitHub Pages will publish your site at `https://YOUR_USERNAME.github.io/portfolio-website/`.
