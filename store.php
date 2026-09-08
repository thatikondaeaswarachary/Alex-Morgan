<?php
/**
 * Task-4 Deliverable: DevGear Storefront Catalog & Shopping Cart
 * File: store.php
 * Real-world full stack E-Commerce store with product search, category filtering,
 * price sorting, AJAX shopping cart modal, and checkout workflow.
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth_middleware.php';

$user = getCurrentUser();
$dbStatus = getDatabaseConnection();

$categories = [];
$products = [];

if ($dbStatus['success']) {
    $conn = $dbStatus['connection'];
    
    // Fetch Categories
    $catRes = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
    if ($catRes) {
        while ($c = mysqli_fetch_assoc($catRes)) {
            $categories[] = $c;
        }
    }

    // Fetch Products with Category Name
    $prodQuery = "SELECT p.*, c.category_name, c.icon_class FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC";
    $prodRes = mysqli_query($conn, $prodQuery);
    if ($prodRes) {
        while ($p = mysqli_fetch_assoc($prodRes)) {
            $products[] = $p;
        }
    }
} else {
    // Fallback seed data for static preview
    $categories = [
        ['id' => 1, 'category_name' => 'Laptops & Workstations', 'slug' => 'laptops', 'icon_class' => 'fa-laptop'],
        ['id' => 2, 'category_name' => 'Mechanical Keyboards', 'slug' => 'keyboards', 'icon_class' => 'fa-keyboard'],
        ['id' => 3, 'category_name' => 'UltraWide Displays', 'slug' => 'displays', 'icon_class' => 'fa-desktop'],
        ['id' => 4, 'category_name' => 'Audio & Accessories', 'slug' => 'accessories', 'icon_class' => 'fa-headphones']
    ];

    $products = [
        [
            'id' => 1, 'category_id' => 1, 'category_name' => 'Laptops & Workstations',
            'title' => 'DevPro M3 Max Workstation Laptop',
            'description' => '16-inch liquid retina display, 36GB unified memory, 1TB NVMe SSD. Ultra performance for docker and compiled builds.',
            'price' => 2499.99, 'stock_qty' => 15,
            'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600',
            'is_featured' => 1
        ],
        [
            'id' => 2, 'category_id' => 2, 'category_name' => 'Mechanical Keyboards',
            'title' => 'Keychron Q1 Pro Wireless Mechanical Keyboard',
            'description' => '75% layout custom mechanical keyboard with hot-swappable tactile switches, RGB lighting, and CNC aluminum shell.',
            'price' => 199.99, 'stock_qty' => 45,
            'image_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600',
            'is_featured' => 1
        ],
        [
            'id' => 3, 'category_id' => 3, 'category_name' => 'UltraWide Displays',
            'title' => 'Dell UltraSharp 38" Curved 4K Monitor',
            'description' => 'WQHD+ IPS curved monitor with Thunderbolt 4 connectivity, USB-C 90W power delivery, and sRGB 100% color accuracy.',
            'price' => 1199.00, 'stock_qty' => 8,
            'image_url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600',
            'is_featured' => 1
        ],
        [
            'id' => 4, 'category_id' => 4, 'category_name' => 'Audio & Accessories',
            'title' => 'Sony WH-1000XM5 Wireless Headphones',
            'description' => 'Industry-leading ANC Bluetooth headphones with 30-hour battery life and crisp developer focus sound.',
            'price' => 398.00, 'stock_qty' => 30,
            'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600',
            'is_featured' => 0
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevGear Store | Real-World Full Stack E-Commerce</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth-styles.css">
    <style>
        .store-hero {
            background: linear-gradient(135deg, rgba(11, 15, 25, 0.95) 0%, rgba(17, 24, 39, 0.9) 100%), url('https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200') center/cover;
            border-bottom: 1px solid var(--auth-border);
            padding: 4rem 0 3rem 0;
        }
        .product-card-store {
            background: var(--auth-bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--auth-border);
            border-radius: 16px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: var(--auth-transition);
        }
        .product-card-store:hover {
            transform: translateY(-6px);
            border-color: var(--auth-accent-cyan);
            box-shadow: 0 15px 30px rgba(56, 189, 248, 0.2);
        }
        .product-img-wrapper {
            height: 220px;
            overflow: hidden;
            position: relative;
        }
        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .product-card-store:hover .product-img-wrapper img {
            transform: scale(1.08);
        }
        .badge-price {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(8px);
            color: var(--auth-accent-cyan);
            font-weight: 800;
            font-size: 1.1rem;
            padding: 0.4rem 0.8rem;
            border-radius: 12px;
            border: 1px solid var(--auth-border-glow);
        }
        .cart-badge-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f43f5e;
            color: white;
            font-size: 0.75rem;
            font-weight: 800;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="auth-body">

    <!-- Header & Navigation -->
    <nav class="navbar navbar-expand-lg auth-navbar sticky-top">
        <div class="container">
            <a class="brand-logo-custom" href="index.html">
                <span class="logo-badge">&lt;/&gt;</span> DevGear <span class="badge bg-primary ms-2">Task 4 Store</span>
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#storeNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="storeNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link text-white-50" href="index.html"><i class="fa-solid fa-house"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link active text-white" href="store.php"><i class="fa-solid fa-store"></i> Store</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="user_orders.php"><i class="fa-solid fa-receipt"></i> My Orders</a></li>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link text-warning fw-bold" href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Admin Panel</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link text-white-50" href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                    
                    <!-- Cart Trigger Button -->
                    <li class="nav-item">
                        <button class="btn btn-outline-info rounded-pill px-3 position-relative" data-bs-toggle="modal" data-bs-target="#cartModal" id="cartTriggerBtn">
                            <i class="fa-solid fa-cart-shopping"></i> Cart
                            <span class="cart-badge-count" id="cartCountBadge">0</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Store Hero Banner -->
    <section class="store-hero mb-4">
        <div class="container text-center">
            <span class="badge bg-info text-dark font-monospace mb-2 px-3 py-2">DEVGEAR TECH ECOSYSTEM</span>
            <h1 class="display-5 fw-bold text-white mb-2">High Performance Hardware for Developers</h1>
            <p class="text-white-50 max-w-600 mx-auto">Explore curated developer laptops, mechanical keyboards, ultrawide monitors, and productivity gear with instant checkout.</p>
        </div>
    </section>

    <!-- Main Storefront Catalog -->
    <main class="container pb-5">
        
        <!-- Filter Controls & Live Search Bar -->
        <div class="row g-3 align-items-center mb-4">
            <div class="col-12 col-md-6">
                <div class="input-group-custom">
                    <span class="input-icon-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control-custom" id="searchProductInput" placeholder="Search laptops, keyboards, displays...">
                </div>
            </div>
            <div class="col-12 col-md-6 text-md-end">
                <div class="btn-group flex-wrap" role="group" id="categoryFilterGroup">
                    <button type="button" class="btn btn-outline-info active filter-cat-btn" data-category="all">All Products</button>
                    <?php foreach ($categories as $cat): ?>
                        <button type="button" class="btn btn-outline-info filter-cat-btn" data-category="<?php echo $cat['id']; ?>">
                            <i class="fa-solid <?php echo $cat['icon_class']; ?>"></i> <?php echo htmlspecialchars($cat['category_name']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div id="storeAlert" class="alert d-none" role="alert"></div>

        <!-- Product Cards Grid -->
        <div class="row g-4" id="productsGrid">
            <?php foreach ($products as $p): ?>
                <div class="col-12 col-sm-6 col-lg-4 product-item-card" data-category="<?php echo $p['category_id']; ?>" data-title="<?php echo strtolower(htmlspecialchars($p['title'])); ?>">
                    <div class="product-card-store">
                        <div class="product-img-wrapper">
                            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy">
                            <span class="badge-price">$<?php echo number_format($p['price'], 2); ?></span>
                            <?php if (!empty($p['is_featured'])): ?>
                                <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3 fw-bold">FEATURED</span>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <span class="badge bg-dark text-info border border-secondary align-self-start mb-2 small">
                                <?php echo htmlspecialchars($p['category_name'] ?? 'Hardware'); ?>
                            </span>
                            <h5 class="fw-bold text-white mb-2"><?php echo htmlspecialchars($p['title']); ?></h5>
                            <p class="text-white-50 small flex-grow-1"><?php echo htmlspecialchars($p['description']); ?></p>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary">
                                <span class="small text-success fw-bold"><i class="fa-solid fa-circle-check"></i> <?php echo $p['stock_qty']; ?> In Stock</span>
                                <button type="button" class="btn btn-info btn-sm rounded-pill px-3 add-to-cart-btn fw-bold" 
                                        data-id="<?php echo $p['id']; ?>" 
                                        data-title="<?php echo htmlspecialchars($p['title']); ?>" 
                                        data-price="<?php echo $p['price']; ?>"
                                        data-image="<?php echo htmlspecialchars($p['image_url']); ?>">
                                    <i class="fa-solid fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </main>

    <!-- Shopping Cart Modal Component -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-info"><i class="fa-solid fa-cart-shopping"></i> Shopping Cart & Checkout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="cartEmptyMsg" class="text-center py-4 text-white-50">
                        <i class="fa-solid fa-basket-shopping fs-1 text-secondary mb-2 d-block"></i>
                        <p class="mb-0">Your cart is currently empty. Browse products above and click 'Add to Cart'.</p>
                    </div>

                    <div id="cartItemsContainer" class="d-none">
                        <div class="table-responsive mb-3">
                            <table class="table table-dark align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody"></tbody>
                            </table>
                        </div>

                        <!-- Checkout Details Form -->
                        <form id="checkoutForm" class="p-3 border border-secondary rounded bg-black bg-opacity-40">
                            <h6 class="text-info fw-bold mb-3"><i class="fa-solid fa-truck-fast"></i> Shipping & Payment Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Shipping Address</label>
                                    <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary" id="shippingAddress" required placeholder="123 Tech Lane, City, State ZIP">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Payment Method</label>
                                    <select class="form-select form-select-sm bg-dark text-white border-secondary" id="paymentMethod">
                                        <option value="Credit Card / Stripe Demo">Credit Card (Stripe Demo)</option>
                                        <option value="PayPal Express">PayPal Express</option>
                                        <option value="Crypto (BTC/ETH)">Crypto Payment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-secondary">
                                <div>
                                    <span class="text-white-50 small">Total Order Value:</span>
                                    <div class="fs-4 fw-bold text-info" id="cartGrandTotal">$0.00</div>
                                </div>
                                <button type="submit" class="btn btn-success fw-bold rounded-pill px-4" id="checkoutBtn">
                                    <i class="fa-solid fa-lock"></i> Complete Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Cart state in memory / localStorage
            let cart = JSON.parse(localStorage.getItem('devgear_cart') || '[]');

            const cartCountBadge = document.getElementById('cartCountBadge');
            const cartEmptyMsg = document.getElementById('cartEmptyMsg');
            const cartItemsContainer = document.getElementById('cartItemsContainer');
            const cartTableBody = document.getElementById('cartTableBody');
            const cartGrandTotal = document.getElementById('cartGrandTotal');
            const storeAlert = document.getElementById('storeAlert');

            updateCartUI();

            // Add to Cart Event Listeners
            document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const title = btn.getAttribute('data-title');
                    const price = parseFloat(btn.getAttribute('data-price'));
                    const image = btn.getAttribute('data-image');

                    const existing = cart.find(item => item.id === id);
                    if (existing) {
                        existing.qty += 1;
                    } else {
                        cart.push({ id, title, price, image, qty: 1 });
                    }

                    localStorage.setItem('devgear_cart', JSON.stringify(cart));
                    updateCartUI();

                    showStoreAlert('success', `Added '${title}' to shopping cart!`);
                });
            });

            function updateCartUI() {
                const totalItems = cart.reduce((sum, i) => sum + i.qty, 0);
                cartCountBadge.innerText = totalItems;

                if (cart.length === 0) {
                    cartEmptyMsg.classList.remove('d-none');
                    cartItemsContainer.classList.add('d-none');
                } else {
                    cartEmptyMsg.classList.add('d-none');
                    cartItemsContainer.classList.remove('d-none');

                    cartTableBody.innerHTML = '';
                    let grandTotal = 0;

                    cart.forEach(item => {
                        const subtotal = item.price * item.qty;
                        grandTotal += subtotal;

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="${item.image}" width="36" height="36" class="rounded object-fit-cover">
                                    <span class="small fw-bold text-white">${item.title}</span>
                                </div>
                            </td>
                            <td class="small">$${item.price.toFixed(2)}</td>
                            <td>
                                <div class="input-group input-group-sm" style="width: 90px;">
                                    <button class="btn btn-outline-secondary btn-decrease" data-id="${item.id}">-</button>
                                    <span class="form-control text-center bg-dark text-white px-1">${item.qty}</span>
                                    <button class="btn btn-outline-secondary btn-increase" data-id="${item.id}">+</button>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-cyan">$${subtotal.toFixed(2)}</td>
                            <td class="text-end">
                                <button class="btn btn-link text-danger p-0 btn-remove" data-id="${item.id}"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        `;
                        cartTableBody.appendChild(tr);
                    });

                    cartGrandTotal.innerText = `$${grandTotal.toFixed(2)}`;

                    // Cart item quantity buttons
                    cartTableBody.querySelectorAll('.btn-increase').forEach(b => {
                        b.addEventListener('click', () => {
                            const id = b.getAttribute('data-id');
                            const item = cart.find(i => i.id === id);
                            if (item) item.qty += 1;
                            localStorage.setItem('devgear_cart', JSON.stringify(cart));
                            updateCartUI();
                        });
                    });

                    cartTableBody.querySelectorAll('.btn-decrease').forEach(b => {
                        b.addEventListener('click', () => {
                            const id = b.getAttribute('data-id');
                            const item = cart.find(i => i.id === id);
                            if (item && item.qty > 1) {
                                item.qty -= 1;
                            } else {
                                cart = cart.filter(i => i.id !== id);
                            }
                            localStorage.setItem('devgear_cart', JSON.stringify(cart));
                            updateCartUI();
                        });
                    });

                    cartTableBody.querySelectorAll('.btn-remove').forEach(b => {
                        b.addEventListener('click', () => {
                            const id = b.getAttribute('data-id');
                            cart = cart.filter(i => i.id !== id);
                            localStorage.setItem('devgear_cart', JSON.stringify(cart));
                            updateCartUI();
                        });
                    });
                }
            }

            // Checkout Form Submission (AJAX to cart_handler.php)
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (cart.length === 0) return;

                    const shippingAddress = document.getElementById('shippingAddress').value;
                    const paymentMethod = document.getElementById('paymentMethod').value;

                    const payload = {
                        action: 'checkout',
                        cart: cart,
                        shipping_address: shippingAddress,
                        payment_method: paymentMethod
                    };

                    fetch('cart_handler.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            cart = [];
                            localStorage.removeItem('devgear_cart');
                            updateCartUI();
                            const modal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
                            modal.hide();
                            showStoreAlert('success', `<i class="fa-solid fa-circle-check"></i> ${data.message}`);
                            setTimeout(() => {
                                window.location.href = 'user_orders.php';
                            }, 1500);
                        } else {
                            alert(data.message);
                        }
                    });
                });
            }

            // Category Filtering & Live Search
            const searchInput = document.getElementById('searchProductInput');
            const filterBtns = document.querySelectorAll('.filter-cat-btn');
            const items = document.querySelectorAll('.product-item-card');

            let activeCategory = 'all';

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    activeCategory = btn.getAttribute('data-category');
                    filterProducts();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', filterProducts);
            }

            function filterProducts() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

                items.forEach(card => {
                    const cat = card.getAttribute('data-category');
                    const title = card.getAttribute('data-title');

                    const matchCat = (activeCategory === 'all' || activeCategory === cat);
                    const matchQuery = (query === '' || title.includes(query));

                    if (matchCat && matchQuery) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            function showStoreAlert(type, msg) {
                storeAlert.className = `alert alert-${type} alert-dismissible fade show`;
                storeAlert.innerHTML = `${msg} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                storeAlert.classList.remove('d-none');
            }
        });
    </script>
</body>
</html>
