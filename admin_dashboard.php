<?php
/**
 * Task-4 Deliverable: Admin Analytics Dashboard & Products/Orders CRUD Portal
 * File: admin_dashboard.php
 * Real-world business intelligence dashboard displaying statistics, revenue analytics,
 * product management CRUD, and customer order status updates.
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth_middleware.php';

requireAdmin();

$user = getCurrentUser();
$dbStatus = getDatabaseConnection();

$totalRevenue = 0;
$totalOrders = 0;
$totalProducts = 0;
$totalUsers = 0;

$productsList = [];
$ordersList = [];
$categoriesList = [];

if ($dbStatus['success']) {
    $conn = $dbStatus['connection'];

    // 1. Analytics & Counters
    $revRes = mysqli_query($conn, "SELECT SUM(total_amount) AS revenue, COUNT(id) AS orders_count FROM orders WHERE status != 'Cancelled'");
    if ($revRes && $row = mysqli_fetch_assoc($revRes)) {
        $totalRevenue = (float)($row['revenue'] ?? 0);
        $totalOrders = (int)($row['orders_count'] ?? 0);
    }

    $prodCountRes = mysqli_query($conn, "SELECT COUNT(id) AS prod_count FROM products");
    if ($prodCountRes && $row = mysqli_fetch_assoc($prodCountRes)) {
        $totalProducts = (int)($row['prod_count'] ?? 0);
    }

    $userCountRes = mysqli_query($conn, "SELECT COUNT(id) AS user_count FROM users");
    if ($userCountRes && $row = mysqli_fetch_assoc($userCountRes)) {
        $totalUsers = (int)($row['user_count'] ?? 0);
    }

    // 2. Fetch Categories
    $catRes = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
    while ($c = mysqli_fetch_assoc($catRes)) {
        $categoriesList[] = $c;
    }

    // 3. Fetch Products
    $prodRes = mysqli_query($conn, "SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC");
    while ($p = mysqli_fetch_assoc($prodRes)) {
        $productsList[] = $p;
    }

    // 4. Fetch All Customer Orders
    $ordRes = mysqli_query($conn, "SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
    while ($o = mysqli_fetch_assoc($ordRes)) {
        $ordersList[] = $o;
    }
} else {
    // Fallback seed analytics for static demo
    $totalRevenue = 3297.97;
    $totalOrders = 3;
    $totalProducts = 6;
    $totalUsers = 3;

    $categoriesList = [
        ['id' => 1, 'category_name' => 'Laptops & Workstations'],
        ['id' => 2, 'category_name' => 'Mechanical Keyboards'],
        ['id' => 3, 'category_name' => 'UltraWide Displays'],
        ['id' => 4, 'category_name' => 'Audio & Accessories']
    ];

    $productsList = [
        ['id' => 1, 'category_id' => 1, 'category_name' => 'Laptops & Workstations', 'title' => 'DevPro M3 Max Workstation Laptop', 'price' => 2499.99, 'stock_qty' => 15, 'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600'],
        ['id' => 2, 'category_id' => 2, 'category_name' => 'Mechanical Keyboards', 'title' => 'Keychron Q1 Pro Mechanical Keyboard', 'price' => 199.99, 'stock_qty' => 45, 'image_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600'],
        ['id' => 3, 'category_id' => 3, 'category_name' => 'UltraWide Displays', 'title' => 'Dell UltraSharp 38" Curved Monitor', 'price' => 1199.00, 'stock_qty' => 8, 'image_url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600']
    ];

    $ordersList = [
        ['id' => 1, 'order_number' => 'ORD-2026-9001', 'full_name' => 'Alex Morgan', 'total_amount' => 2699.98, 'status' => 'Completed', 'created_at' => '2026-09-01 14:20:00'],
        ['id' => 2, 'order_number' => 'ORD-2026-9002', 'full_name' => 'Alex Morgan', 'total_amount' => 398.00, 'status' => 'Processing', 'created_at' => '2026-09-06 09:15:00'],
        ['id' => 3, 'order_number' => 'ORD-2026-9003', 'full_name' => 'John Doe', 'total_amount' => 199.99, 'status' => 'Pending', 'created_at' => '2026-09-08 11:30:00']
    ];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel & Business Intelligence | DevGear Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth-styles.css">
    <style>
        .stat-widget {
            background: var(--auth-bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--auth-border);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--auth-shadow-card);
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .admin-card-section {
            background: var(--auth-bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--auth-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--auth-shadow-card);
        }
    </style>
</head>
<body class="auth-body">

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg auth-navbar sticky-top">
        <div class="container">
            <a class="brand-logo-custom" href="index.html">
                <span class="logo-badge">&lt;/&gt;</span> DevGear <span class="badge bg-warning text-dark ms-2">Admin Analytics</span>
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminDashNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="adminDashNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link text-white-50" href="index.html"><i class="fa-solid fa-house"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="store.php"><i class="fa-solid fa-store"></i> Storefront</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="admin_users.php"><i class="fa-solid fa-users-gear"></i> Manage Users</a></li>
                    <li class="nav-item"><a class="nav-link active text-warning fw-bold" href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Admin Dashboard -->
    <main class="container py-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-chart-pie text-warning"></i> Business Intelligence & Analytics</h2>
                <p class="text-white-50 small mb-0">Task 4: Real-time statistics, products CRUD management, and order tracking</p>
            </div>
            <button type="button" class="btn btn-info fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa-solid fa-plus"></i> Add New Product
            </button>
        </div>

        <div id="adminAlert" class="alert d-none" role="alert"></div>

        <!-- 4 Key Analytics Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-success bg-opacity-20 text-success">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div>
                        <span class="text-white-50 small d-block">Gross Revenue</span>
                        <h3 class="fw-bold text-white mb-0">$<?php echo number_format($totalRevenue, 2); ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-info bg-opacity-20 text-info">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <div>
                        <span class="text-white-50 small d-block">Total Orders</span>
                        <h3 class="fw-bold text-white mb-0"><?php echo $totalOrders; ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-warning bg-opacity-20 text-warning">
                        <i class="fa-solid fa-cubes"></i>
                    </div>
                    <div>
                        <span class="text-white-50 small d-block">Products Catalog</span>
                        <h3 class="fw-bold text-white mb-0"><?php echo $totalProducts; ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-purple bg-opacity-20 text-purple" style="color: #a855f7;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <span class="text-white-50 small d-block">Active Users</span>
                        <h3 class="fw-bold text-white mb-0"><?php echo $totalUsers; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Product Catalog CRUD Management Table -->
        <div class="admin-card-section">
            <h4 class="fw-bold text-white mb-3"><i class="fa-solid fa-boxes-stacked text-info"></i> Product Records CRUD Management</h4>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Stock</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productsList as $p): ?>
                            <tr id="prodRow-<?php echo $p['id']; ?>">
                                <td><strong>#<?php echo $p['id']; ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" width="40" height="40" class="rounded object-fit-cover">
                                        <span class="fw-bold text-white small"><?php echo htmlspecialchars($p['title']); ?></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-dark text-info border border-secondary"><?php echo htmlspecialchars($p['category_name'] ?? 'Hardware'); ?></span></td>
                                <td class="fw-bold text-cyan">$<?php echo number_format($p['price'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo ($p['stock_qty'] < 10) ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo $p['stock_qty']; ?> Units
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-outline-danger btn-sm rounded-circle delete-prod-btn" data-id="<?php echo $p['id']; ?>" data-title="<?php echo htmlspecialchars($p['title']); ?>" title="Delete Product">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Order Management & Status Updater -->
        <div class="admin-card-section">
            <h4 class="fw-bold text-white mb-3"><i class="fa-solid fa-truck-ramp-box text-warning"></i> Customer Orders & Status Management</h4>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th class="text-end">Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordersList as $ord): ?>
                            <tr>
                                <td><strong class="font-monospace text-info"><?php echo htmlspecialchars($ord['order_number']); ?></strong></td>
                                <td>
                                    <div class="fw-bold text-white small"><?php echo htmlspecialchars($ord['full_name'] ?? 'Alex Morgan'); ?></div>
                                </td>
                                <td class="fw-bold text-white">$<?php echo number_format($ord['total_amount'], 2); ?></td>
                                <td><span class="small text-white-50"><?php echo date('M d, Y', strtotime($ord['created_at'])); ?></span></td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill" id="statusBadge-<?php echo $ord['id']; ?>">
                                        <?php echo htmlspecialchars($ord['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <select class="form-select form-select-sm bg-dark text-white border-secondary d-inline-block w-auto order-status-select" data-id="<?php echo $ord['id']; ?>">
                                        <option value="Pending" <?php echo ($ord['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo ($ord['status'] === 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Completed" <?php echo ($ord['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo ($ord['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-info"><i class="fa-solid fa-plus"></i> Add New Hardware Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addProductForm">
                    <input type="hidden" name="action" value="create_product">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Product Title</label>
                            <input type="text" name="title" class="form-control bg-dark text-white border-secondary" required placeholder="e.g. UltraSharp Monitor">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category_id" class="form-select bg-dark text-white border-secondary">
                                <?php foreach ($categoriesList as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control bg-dark text-white border-secondary" required placeholder="299.99">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Stock Quantity</label>
                                <input type="number" name="stock_qty" class="form-control bg-dark text-white border-secondary" value="20" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Image URL</label>
                            <input type="url" name="image_url" class="form-control bg-dark text-white border-secondary" placeholder="https://images.unsplash.com/...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="3" required placeholder="Product specifications..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info fw-bold rounded-pill px-4">Create Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addProductForm = document.getElementById('addProductForm');
            const adminAlert = document.getElementById('adminAlert');

            if (addProductForm) {
                addProductForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(addProductForm);
                    fetch('product_crud_handler.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showAdminAlert('success', data.message);
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                                modal.hide();
                                addProductForm.reset();
                                setTimeout(() => window.location.reload(), 1200);
                            } else {
                                alert(data.message);
                            }
                        });
                });
            }

            // Delete Product Handler
            document.querySelectorAll('.delete-prod-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const title = btn.getAttribute('data-title');
                    if (confirm(`Are you sure you want to delete product '${title}'?`)) {
                        const formData = new FormData();
                        formData.append('action', 'delete_product');
                        formData.append('id', id);

                        fetch('product_crud_handler.php', { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    showAdminAlert('success', data.message);
                                    const row = document.getElementById(`prodRow-${id}`);
                                    if (row) row.remove();
                                } else {
                                    alert(data.message);
                                }
                            });
                    }
                });
            });

            // Update Order Status Handler
            document.querySelectorAll('.order-status-select').forEach(select => {
                select.addEventListener('change', () => {
                    const orderId = select.getAttribute('data-id');
                    const newStatus = select.value;

                    const formData = new FormData();
                    formData.append('action', 'update_order_status');
                    formData.append('order_id', orderId);
                    formData.append('status', newStatus);

                    fetch('product_crud_handler.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showAdminAlert('success', data.message);
                                const badge = document.getElementById(`statusBadge-${orderId}`);
                                if (badge) badge.innerText = newStatus;
                            } else {
                                alert(data.message);
                            }
                        });
                });
            });

            function showAdminAlert(type, msg) {
                adminAlert.className = `alert alert-${type} alert-dismissible fade show`;
                adminAlert.innerHTML = `${msg} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                adminAlert.classList.remove('d-none');
            }
        });
    </script>
</body>
</html>
