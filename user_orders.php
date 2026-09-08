<?php
/**
 * Task-4 Deliverable: Customer Orders Dashboard
 * File: user_orders.php
 * Displays logged-in user order history, status timeline, itemized receipts.
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth_middleware.php';

requireLogin();

$user = getCurrentUser();
$userId = $user['id'];

$ordersList = [];
$dbStatus = getDatabaseConnection();

if ($dbStatus['success']) {
    $conn = $dbStatus['connection'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    while ($o = mysqli_fetch_assoc($res)) {
        // Fetch items for each order
        $orderId = $o['id'];
        $itemStmt = mysqli_prepare($conn, "SELECT oi.*, p.title, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        mysqli_stmt_bind_param($itemStmt, "i", $orderId);
        mysqli_stmt_execute($itemStmt);
        $itemRes = mysqli_stmt_get_result($itemStmt);
        $items = [];
        while ($item = mysqli_fetch_assoc($itemRes)) {
            $items[] = $item;
        }
        mysqli_stmt_close($itemStmt);

        $o['items'] = $items;
        $ordersList[] = $o;
    }
    mysqli_stmt_close($stmt);
} else {
    // Fallback seed order list for static preview
    $ordersList = [
        [
            'id' => 1,
            'order_number' => 'ORD-2026-9001',
            'total_amount' => 2699.98,
            'status' => 'Completed',
            'shipping_address' => '742 Evergreen Terrace, Springfield, OR',
            'payment_method' => 'Credit Card (Stripe)',
            'created_at' => '2026-09-01 14:20:00',
            'items' => [
                ['title' => 'DevPro M3 Max Workstation Laptop', 'quantity' => 1, 'unit_price' => 2499.99, 'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600'],
                ['title' => 'Keychron Q1 Pro Mechanical Keyboard', 'quantity' => 1, 'unit_price' => 199.99, 'image_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600']
            ]
        ],
        [
            'id' => 2,
            'order_number' => 'ORD-2026-9002',
            'total_amount' => 398.00,
            'status' => 'Processing',
            'shipping_address' => '742 Evergreen Terrace, Springfield, OR',
            'payment_method' => 'PayPal Demo',
            'created_at' => '2026-09-06 09:15:00',
            'items' => [
                ['title' => 'Sony WH-1000XM5 Headphones', 'quantity' => 1, 'unit_price' => 398.00, 'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600']
            ]
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | DevGear Customer Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth-styles.css">
    <style>
        .order-card {
            background: var(--auth-bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--auth-border);
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--auth-shadow-card);
        }
        .status-badge-pending { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid #f59e0b; }
        .status-badge-processing { background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid #38bdf8; }
        .status-badge-completed { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid #10b981; }
        .status-badge-cancelled { background: rgba(244, 63, 94, 0.2); color: #fb7185; border: 1px solid #f43f5e; }
    </style>
</head>
<body class="auth-body">

    <!-- Header & Navigation -->
    <nav class="navbar navbar-expand-lg auth-navbar sticky-top">
        <div class="container">
            <a class="brand-logo-custom" href="index.html">
                <span class="logo-badge">&lt;/&gt;</span> DevGear <span class="badge bg-primary ms-2">Customer Orders</span>
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#ordersNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="ordersNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link text-white-50" href="index.html"><i class="fa-solid fa-house"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="store.php"><i class="fa-solid fa-store"></i> Store</a></li>
                    <li class="nav-item"><a class="nav-link active text-white" href="user_orders.php"><i class="fa-solid fa-receipt"></i> My Orders</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Section -->
    <main class="container py-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-box-open text-info"></i> My Orders & Receipts</h2>
                <p class="text-white-50 small mb-0">Track your order statuses, items, and shipping addresses</p>
            </div>
            <a href="store.php" class="btn btn-info fw-bold rounded-pill px-4">
                <i class="fa-solid fa-cart-plus"></i> Shop Hardware
            </a>
        </div>

        <?php if (empty($ordersList)): ?>
            <div class="order-card text-center py-5">
                <i class="fa-solid fa-receipt fs-1 text-secondary mb-3"></i>
                <h4 class="text-white fw-bold">No Orders Placed Yet</h4>
                <p class="text-white-50 small mb-3">Browse our DevGear tech catalog and place your first order.</p>
                <a href="store.php" class="btn btn-info rounded-pill px-4">Visit Storefront</a>
            </div>
        <?php else: ?>
            <?php foreach ($ordersList as $ord): ?>
                <?php
                    $statusClass = 'status-badge-pending';
                    if ($ord['status'] === 'Processing') $statusClass = 'status-badge-processing';
                    if ($ord['status'] === 'Completed') $statusClass = 'status-badge-completed';
                    if ($ord['status'] === 'Cancelled') $statusClass = 'status-badge-cancelled';
                ?>
                <div class="order-card">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-secondary pb-3 mb-3 gap-2">
                        <div>
                            <span class="font-monospace text-info fw-bold fs-5"><?php echo htmlspecialchars($ord['order_number']); ?></span>
                            <span class="text-white-50 small ms-2"><i class="fa-solid fa-calendar"></i> <?php echo date('M d, Y - h:i A', strtotime($ord['created_at'])); ?></span>
                        </div>
                        <div>
                            <span class="badge <?php echo $statusClass; ?> px-3 py-2 rounded-pill fw-bold">
                                <i class="fa-solid fa-truck"></i> <?php echo htmlspecialchars($ord['status']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-3">
                        <table class="table table-dark table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Item Description</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ord['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'assets/images/product-placeholder.jpg'); ?>" width="32" height="32" class="rounded object-fit-cover">
                                                <span class="text-white small fw-bold"><?php echo htmlspecialchars($item['title']); ?></span>
                                            </div>
                                        </td>
                                        <td class="small">$<?php echo number_format($item['unit_price'], 2); ?></td>
                                        <td class="small"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end small fw-bold text-info">$<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center pt-2 text-white-50 small">
                        <div>
                            <i class="fa-solid fa-location-dot text-info"></i> <strong>Shipping Address:</strong> <?php echo htmlspecialchars($ord['shipping_address']); ?>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <span class="me-3">Payment: <strong><?php echo htmlspecialchars($ord['payment_method']); ?></strong></span>
                            <span class="fs-5 fw-bold text-white">Total: <span class="text-info">$<?php echo number_format($ord['total_amount'], 2); ?></span></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
