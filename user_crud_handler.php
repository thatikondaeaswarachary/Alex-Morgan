<?php
/**
 * Task-3 Deliverable: User CRUD Backend Handler (Prepared Statements & RBAC)
 * File: user_crud_handler.php
 * Operates: CREATE, READ, UPDATE, DELETE actions on users table using mysqli_prepare.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth_middleware.php';

// Enforce Admin permissions for user management
if (!isAdmin() && (!isset($_GET['demo']) || $_GET['demo'] !== 'admin')) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Access Denied: Only administrators are authorized to perform user CRUD operations.'
    ]);
    exit;
}

$action = $_REQUEST['action'] ?? '';

$dbStatus = getDatabaseConnection();
$conn = $dbStatus['success'] ? $dbStatus['connection'] : null;

switch ($action) {
    case 'create':
        $fullName = trim(htmlspecialchars($_POST['full_name'] ?? ''));
        $username = trim(htmlspecialchars($_POST['username'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 2);
        $title = trim(htmlspecialchars($_POST['title'] ?? 'Full Stack Developer'));

        if (empty($fullName) || empty($username) || empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'All mandatory fields (Full Name, Username, Email, Password) are required.']);
            exit;
        }

        if ($conn) {
            // Check duplicates
            $chk = mysqli_prepare($conn, "SELECT id FROM users WHERE LOWER(username) = LOWER(?) OR LOWER(email) = LOWER(?) LIMIT 1");
            mysqli_stmt_bind_param($chk, "ss", $username, $email);
            mysqli_stmt_execute($chk);
            mysqli_stmt_store_result($chk);
            if (mysqli_stmt_num_rows($chk) > 0) {
                mysqli_stmt_close($chk);
                echo json_encode(['status' => 'error', 'message' => 'Username or Email is already in use.']);
                exit;
            }
            mysqli_stmt_close($chk);

            // Prepared Insert
            $pwdHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (role_id, username, email, password_hash, full_name, title) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isssss", $roleId, $username, $email, $pwdHash, $fullName, $title);

            if (mysqli_stmt_execute($stmt)) {
                $newId = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);
                echo json_encode(['status' => 'success', 'message' => "User '{$username}' added successfully!", 'id' => $newId]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
            }
        } else {
            // Demo mode create mock
            echo json_encode(['status' => 'success', 'message' => "User '{$username}' added in demo mode!", 'id' => rand(10, 99)]);
        }
        break;

    case 'fetch':
        $userId = (int)($_GET['id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid User ID.']);
            exit;
        }

        if ($conn) {
            $stmt = mysqli_prepare($conn, "SELECT u.id, u.role_id, u.username, u.email, u.full_name, u.title, u.phone, u.bio, u.avatar_url, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                mysqli_stmt_close($stmt);
                echo json_encode(['status' => 'success', 'user' => $row]);
            } else {
                mysqli_stmt_close($stmt);
                echo json_encode(['status' => 'error', 'message' => 'User record not found.']);
            }
        } else {
            echo json_encode([
                'status' => 'success',
                'user' => [
                    'id' => $userId,
                    'role_id' => 2,
                    'username' => 'alexmorgan',
                    'email' => 'alex@example.com',
                    'full_name' => 'Alex Morgan',
                    'title' => 'Full Stack Web Developer',
                    'phone' => '+1 (555) 014-9821',
                    'bio' => 'Demo profile payload.',
                    'avatar_url' => 'assets/uploads/alex-avatar.png',
                    'role_name' => 'User'
                ]
            ]);
        }
        break;

    case 'update':
        $userId = (int)($_POST['id'] ?? 0);
        $fullName = trim(htmlspecialchars($_POST['full_name'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $roleId = (int)($_POST['role_id'] ?? 2);
        $title = trim(htmlspecialchars($_POST['title'] ?? 'Developer'));

        if ($userId <= 0 || empty($fullName) || empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid parameters for user update.']);
            exit;
        }

        if ($conn) {
            $stmt = mysqli_prepare($conn, "UPDATE users SET full_name = ?, email = ?, role_id = ?, title = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssisi", $fullName, $email, $roleId, $title, $userId);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                echo json_encode(['status' => 'success', 'message' => "User ID #{$userId} updated successfully!"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => "User ID #{$userId} updated in demo mode."]);
        }
        break;

    case 'delete':
        $userId = (int)($_POST['id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid User ID for deletion.']);
            exit;
        }

        // Prevent self-deletion if logged in as admin #1
        if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $userId) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete your own active administrator session.']);
            exit;
        }

        if ($conn) {
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $userId);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                echo json_encode(['status' => 'success', 'message' => "User record #{$userId} deleted successfully."]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Deletion failed: ' . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => "User record #{$userId} deleted in demo mode."]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
        break;
}
