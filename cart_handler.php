<?php
/**
 * Task-4 Deliverable: Cart & Checkout AJAX Endpoint
 * File: cart_handler.php
 * Accepts JSON order payloads, creates orders and order_items using prepared statements.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth_middleware.php';

$rawInput = file_get_contents('php_input') ?: file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON payload received.']);
    exit;
}

$action = $data['action'] ?? '';

if ($action === 'checkout') {
    $cart = $data['cart'] ?? [];
    $shippingAddress = trim(htmlspecialchars($data['shipping_address'] ?? '742 Evergreen Terrace, Springfield, OR'));
    $paymentMethod = trim(htmlspecialchars($data['payment_method'] ?? 'Credit Card (Stripe Demo)'));

    if (empty($cart)) {
        echo json_encode(['status' => 'error', 'message' => 'Shopping cart is empty.']);
        exit;
    }

    $user = getCurrentUser();
    $userId = $user['id'] ?: 2; // Default to Alex Morgan #2 if guest checkout in demo mode

    // Calculate Grand Total
    $totalAmount = 0;
    foreach ($cart as $item) {
        $totalAmount += ((float)$item['price'] * (int)$item['qty']);
    }

    $orderNumber = 'ORD-' . date('Y') . '-' . rand(1000, 9999);

    $dbStatus = getDatabaseConnection();
    if ($dbStatus['success']) {
        $conn = $dbStatus['connection'];

        // Prepared Statement 1: Create Order
        $stmtOrder = mysqli_prepare($conn, "INSERT INTO orders (user_id, order_number, total_amount, status, shipping_address, payment_method) VALUES (?, ?, ?, 'Pending', ?, ?)");
        mysqli_stmt_bind_param($stmtOrder, "isdss", $userId, $orderNumber, $totalAmount, $shippingAddress, $paymentMethod);

        if (mysqli_stmt_execute($stmtOrder)) {
            $orderId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmtOrder);

            // Prepared Statement 2: Insert Order Items
            $stmtItem = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            foreach ($cart as $item) {
                $pId = (int)$item['id'];
                $qty = (int)$item['qty'];
                $price = (float)$item['price'];
                mysqli_stmt_bind_param($stmtItem, "iiid", $orderId, $pId, $qty, $price);
                mysqli_stmt_execute($stmtItem);
            }
            mysqli_stmt_close($stmtItem);

            echo json_encode([
                'status' => 'success',
                'message' => "Order #{$orderNumber} placed successfully! Total: $" . number_format($totalAmount, 2),
                'order_number' => $orderNumber
            ]);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database order creation error: ' . mysqli_error($conn)]);
            exit;
        }
    } else {
        // Fallback checkout success for static demo mode
        echo json_encode([
            'status' => 'success',
            'message' => "Order #{$orderNumber} placed successfully in Demo Mode! Total: $" . number_format($totalAmount, 2),
            'order_number' => $orderNumber
        ]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action parameter.']);
    exit;
}
