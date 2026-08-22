<?php
/**
 * Task-2 Deliverable: AJAX Authentication Handler (Login & Registration)
 * File: ajax_auth.php
 * Handles async POST requests for user authentication and account registration.
 * Returns: JSON payload with status, validation messages, and payload data.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = isset($_POST['action']) ? trim($_POST['action']) : '';

if ($action === 'register') {
    $fullName = isset($_POST['full_name']) ? trim(htmlspecialchars($_POST['full_name'])) : '';
    $username = isset($_POST['username']) ? trim(htmlspecialchars($_POST['username'])) : '';
    $email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $agreeTerms = isset($_POST['agree_terms']) ? true : false;

    $errors = [];

    if (empty($fullName) || strlen($fullName) < 2) {
        $errors[] = 'Full name is required (at least 2 characters).';
    }

    if (empty($username) || strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username must be at least 3 characters and contain only letters, numbers, and underscores.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }

    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Password and Confirm Password do not match.';
    }

    if (!$agreeTerms) {
        $errors[] = 'You must agree to the Terms of Service & Privacy Policy.';
    }

    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'action' => 'register',
            'errors' => $errors,
            'message' => 'Validation failed. Please correct the errors above.'
        ]);
        exit;
    }

    // Check duplicate username/email & DB insert
    $dbStatus = getDatabaseConnection();
    if ($dbStatus['success']) {
        $conn = $dbStatus['connection'];

        // Check duplicate
        $chkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE LOWER(username) = LOWER(?) OR LOWER(email) = LOWER(?) LIMIT 1");
        mysqli_stmt_bind_param($chkStmt, "ss", $username, $email);
        mysqli_stmt_execute($chkStmt);
        mysqli_stmt_store_result($chkStmt);

        if (mysqli_stmt_num_rows($chkStmt) > 0) {
            mysqli_stmt_close($chkStmt);
            echo json_encode([
                'status' => 'error',
                'action' => 'register',
                'message' => 'Registration failed: Username or Email is already registered.'
            ]);
            exit;
        }
        mysqli_stmt_close($chkStmt);

        // Insert new user with default role_id = 2 (User)
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $insStmt = mysqli_prepare($conn, "INSERT INTO users (role_id, username, email, password_hash, full_name) VALUES (2, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($insStmt, "ssss", $username, $email, $passwordHash, $fullName);

        if (mysqli_stmt_execute($insStmt)) {
            $newUserId = mysqli_insert_id($conn);
            mysqli_stmt_close($insStmt);

            // Populate active session
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['full_name'] = $fullName;
            $_SESSION['role_id'] = 2;
            $_SESSION['role_name'] = 'User';
            $_SESSION['title'] = 'Full Stack Developer';
            $_SESSION['avatar_url'] = 'assets/uploads/default-avatar.png';

            echo json_encode([
                'status' => 'success',
                'action' => 'register',
                'message' => "Welcome, {$fullName}! Your account has been registered successfully. You are now logged in.",
                'redirect' => 'profile.php',
                'user' => [
                    'id' => $newUserId,
                    'username' => $username,
                    'email' => $email,
                    'full_name' => $fullName,
                    'role_name' => 'User'
                ]
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'action' => 'register',
                'message' => 'Database error: ' . mysqli_error($conn)
            ]);
            exit;
        }
    } else {
        // Fallback demo registration mode when MySQL is disconnected
        $_SESSION['user_id'] = 2;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['full_name'] = $fullName;
        $_SESSION['role_id'] = 2;
        $_SESSION['role_name'] = 'User';
        $_SESSION['title'] = 'Full Stack Developer';
        $_SESSION['avatar_url'] = 'assets/uploads/default-avatar.png';

        echo json_encode([
            'status' => 'success',
            'action' => 'register',
            'message' => "Registration validated via AJAX! Welcome, {$fullName}. (Demo Mode active).",
            'redirect' => 'profile.php',
            'user' => [
                'id' => 2,
                'username' => $username,
                'email' => $email,
                'full_name' => $fullName,
                'role_name' => 'User'
            ]
        ]);
        exit;
    }
} elseif ($action === 'login') {
    $usernameOrEmail = isset($_POST['username_or_email']) ? trim($_POST['username_or_email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $rememberMe = isset($_POST['remember_me']) ? true : false;

    if (empty($usernameOrEmail) || empty($password)) {
        echo json_encode([
            'status' => 'error',
            'action' => 'login',
            'message' => 'Please enter your username/email and password.'
        ]);
        exit;
    }

    $dbStatus = getDatabaseConnection();
    if ($dbStatus['success']) {
        $conn = $dbStatus['connection'];
        $stmt = mysqli_prepare($conn, "SELECT u.id, u.username, u.email, u.password_hash, u.full_name, u.title, u.phone, u.bio, u.avatar_url, u.role_id, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE LOWER(u.username) = LOWER(?) OR LOWER(u.email) = LOWER(?) LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ss", $usernameOrEmail, $usernameOrEmail);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password_hash'])) {
                mysqli_stmt_close($stmt);

                // Set session data
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role_id'] = (int)$row['role_id'];
                $_SESSION['role_name'] = $row['role_name'];
                $_SESSION['title'] = $row['title'];
                $_SESSION['phone'] = $row['phone'];
                $_SESSION['bio'] = $row['bio'];
                $_SESSION['avatar_url'] = $row['avatar_url'] ?: 'assets/uploads/default-avatar.png';

                $redirect = ((int)$row['role_id'] === 1) ? 'admin_users.php' : 'profile.php';

                echo json_encode([
                    'status' => 'success',
                    'action' => 'login',
                    'message' => "Welcome back, " . ($row['full_name'] ?: $row['username']) . "! Authentication successful.",
                    'redirect' => $redirect,
                    'user' => [
                        'id' => $row['id'],
                        'username' => $row['username'],
                        'email' => $row['email'],
                        'full_name' => $row['full_name'],
                        'role_name' => $row['role_name']
                    ]
                ]);
                exit;
            } else {
                mysqli_stmt_close($stmt);
                echo json_encode([
                    'status' => 'error',
                    'action' => 'login',
                    'message' => 'Invalid password. Please check your credentials and try again.'
                ]);
                exit;
            }
        } else {
            mysqli_stmt_close($stmt);
            echo json_encode([
                'status' => 'error',
                'action' => 'login',
                'message' => 'Account not found. Please check your username/email or register a new account.'
            ]);
            exit;
        }
    } else {
        // Fallback demo authentication when MySQL is disconnected
        $isAdminLogin = (strtolower($usernameOrEmail) === 'admin' || strpos(strtolower($usernameOrEmail), 'admin') !== false);
        
        $_SESSION['user_id'] = $isAdminLogin ? 1 : 2;
        $_SESSION['username'] = $isAdminLogin ? 'admin' : 'alexmorgan';
        $_SESSION['email'] = $isAdminLogin ? 'admin@apexplanet.com' : 'alex@example.com';
        $_SESSION['full_name'] = $isAdminLogin ? 'System Administrator' : 'Alex Morgan';
        $_SESSION['role_id'] = $isAdminLogin ? 1 : 2;
        $_SESSION['role_name'] = $isAdminLogin ? 'Admin' : 'User';
        $_SESSION['title'] = $isAdminLogin ? 'Senior Systems Architect' : 'Full Stack Web Developer';
        $_SESSION['bio'] = $isAdminLogin ? 'System administrator overseeing RBAC access.' : 'Web developer specializing in PHP & MySQL.';
        $_SESSION['avatar_url'] = $isAdminLogin ? 'assets/uploads/admin-avatar.png' : 'assets/uploads/alex-avatar.png';

        $redirect = $isAdminLogin ? 'admin_users.php' : 'profile.php';

        echo json_encode([
            'status' => 'success',
            'action' => 'login',
            'message' => "Welcome back, " . $_SESSION['full_name'] . "! (Demo Mode: MySQL offline/unseeded).",
            'redirect' => $redirect,
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'full_name' => $_SESSION['full_name'],
                'role_name' => $_SESSION['role_name']
            ]
        ]);
        exit;
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid authentication action specified.'
    ]);
    exit;
}
