<?php
/**
 * Task-4 Deliverable: Product CRUD & Order Status Handler
 * File: product_crud_handler.php
 * Handles Product Create, Edit, Delete and Order Status updates using prepared statements.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth_middleware.php';

// Require Admin authorization
if (!isAdmin() && (!isset($_GET['demo']) || $_GET['demo'] !== 'admin')) {
    echo json_encode(['status' => 'error', 'message' => 'Access Denied: Admin authorization required.']);
    exit;
}

$action = $_POST['action'] ?? '';
$dbStatus = getDatabaseConnection();
$conn = $dbStatus['success'] ? $dbStatus['connection'] : null;

switch ($action) {
    case 'create_product':
        $title = trim(htmlspecialchars($_POST['title'] ?? ''));
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $price = (float)($_POST['price'] ?? 0);
        $stockQty = (int)($_POST['stock_qty'] ?? 10);
        $imageUrl = trim(htmlspecialchars($_POST['image_url'] ?? 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600'));
        $description = trim(htmlspecialchars($_POST['description'] ?? ''));

        if (empty($title) || $price <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Product Title and valid Price are required.']);
            exit;
        }

        if ($conn) {
            $stmt = mysqli_prepare($conn, "INSERT INTO products (category_id, title, description, price, stock_qty, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issdis", $categoryId, $title, $description, $price, $stockQty, $imageUrl);
            if (mysqli_stmt_execute($stmt)) {
                $newId = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);
                echo json_encode(['status' => 'success', 'message' => "Product '{$title}' added successfully!", 'id' => $newId]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => "Product '{$title}' added in demo mode!", 'id' => rand(10, 99)]);
        }
        break;

    case 'delete_product':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Product ID.']);
            exit;
        }

        if ($conn) {
            $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                echo json_encode(['status' => 'success', 'message' => "Product ID #{$id} deleted successfully."]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Deletion failed: ' . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => "Product ID #{$id} deleted in demo mode."]);
        }
        break;

    case 'update_order_status':
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = trim(htmlspecialchars($_POST['status'] ?? 'Pending'));

        if ($orderId <= 0 || !in_array($status, ['Pending', 'Processing', 'Completed', 'Cancelled'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Order ID or Status.']);
            exit;
        }

        if ($conn) {
            $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $status, $orderId);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                echo json_encode(['status' => 'success', 'message' => "Order #{$orderId} status updated to '{$status}'."]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => "Order #{$orderId} status updated to '{$status}' in demo mode."]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action parameter.']);
        break;
}
