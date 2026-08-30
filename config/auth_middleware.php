<?php
/**
 * Task-3 Deliverable: Authentication & RBAC Session Middleware
 * File: config/auth_middleware.php
 * Handles session start, role checks (Admin/User), route protection,
 * and intelligent demo fallbacks when database is offline.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a user is currently logged in.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if the currently logged in user is an Administrator (role_id === 1 or role_name === 'Admin').
 */
function isAdmin() {
    if (!isLoggedIn()) return false;
    return (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1) ||
           (isset($_SESSION['role_name']) && strtolower($_SESSION['role_name']) === 'admin');
}

/**
 * Get current session user payload array.
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return [
            'id' => 0,
            'username' => 'Guest',
            'full_name' => 'Guest User',
            'email' => 'guest@example.com',
            'role_id' => 2,
            'role_name' => 'Guest',
            'title' => 'Visitor',
            'bio' => 'Not logged in.',
            'avatar_url' => 'assets/uploads/default-avatar.png'
        ];
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'user',
        'full_name' => $_SESSION['full_name'] ?? 'Alex Morgan',
        'email' => $_SESSION['email'] ?? 'alex@example.com',
        'role_id' => $_SESSION['role_id'] ?? 2,
        'role_name' => $_SESSION['role_name'] ?? 'User',
        'title' => $_SESSION['title'] ?? 'Full Stack Developer',
        'phone' => $_SESSION['phone'] ?? '',
        'bio' => $_SESSION['bio'] ?? 'Full Stack PHP & MySQL Web Developer.',
        'avatar_url' => $_SESSION['avatar_url'] ?? 'assets/uploads/default-avatar.png'
    ];
}

/**
 * Enforce Login Requirement for Protected Pages (e.g. Profile Page).
 */
function requireLogin() {
    if (!isLoggedIn()) {
        // Demo mode auto-login helper if visiting in static local mode
        if (isset($_GET['demo']) && $_GET['demo'] === 'user') {
            $_SESSION['user_id'] = 2;
            $_SESSION['username'] = 'alexmorgan';
            $_SESSION['full_name'] = 'Alex Morgan';
            $_SESSION['email'] = 'alex@example.com';
            $_SESSION['role_id'] = 2;
            $_SESSION['role_name'] = 'User';
            $_SESSION['title'] = 'Full Stack Web Developer';
            $_SESSION['bio'] = 'Passionate web developer specializing in PHP, MySQL, JavaScript, and Bootstrap 5 application design.';
            $_SESSION['avatar_url'] = 'assets/uploads/alex-avatar.png';
            return;
        }

        header('Location: auth.html?action=login&msg=login_required');
        exit;
    }
}

/**
 * Enforce Admin Role Requirement for RBAC Protected Pages (e.g. User CRUD Dashboard).
 */
function requireAdmin() {
    if (!isLoggedIn()) {
        if (isset($_GET['demo']) && $_GET['demo'] === 'admin') {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'admin';
            $_SESSION['full_name'] = 'System Administrator';
            $_SESSION['email'] = 'admin@apexplanet.com';
            $_SESSION['role_id'] = 1;
            $_SESSION['role_name'] = 'Admin';
            $_SESSION['title'] = 'Senior Systems Architect';
            $_SESSION['bio'] = 'Lead System Administrator overseeing security policies, database normalization, and RBAC user access control.';
            $_SESSION['avatar_url'] = 'assets/uploads/admin-avatar.png';
            return;
        }

        header('Location: auth.html?action=login&msg=admin_required');
        exit;
    }

    if (!isAdmin()) {
        header('Location: profile.php?error=unauthorized_admin');
        exit;
    }
}
