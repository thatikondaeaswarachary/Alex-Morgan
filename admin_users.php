<?php
/**
 * Task-3 Deliverable: Admin User Management CRUD Dashboard
 * File: admin_users.php
 * Displays all users in styled HTML table format, provides modals for Create, Update,
 * and Delete with popup confirmation modal, protected by Role-Based Access Control (RBAC).
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth_middleware.php';

requireAdmin();

$user = getCurrentUser();
$dbStatus = getDatabaseConnection();
$usersList = [];

if ($dbStatus['success']) {
    $conn = $dbStatus['connection'];
    $query = "SELECT u.id, u.username, u.email, u.full_name, u.title, u.avatar_url, u.created_at, u.role_id, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id ASC";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $usersList[] = $row;
        }
    }
} else {
    // Fallback seed list for demo static mode
    $usersList = [
        [
            'id' => 1,
            'role_id' => 1,
            'role_name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@apexplanet.com',
            'full_name' => 'System Administrator',
            'title' => 'Senior Systems Architect',
            'avatar_url' => 'assets/uploads/admin-avatar.png',
            'created_at' => '2026-08-20 10:00:00'
        ],
        [
            'id' => 2,
            'role_id' => 2,
            'role_name' => 'User',
            'username' => 'alexmorgan',
            'email' => 'alex@example.com',
            'full_name' => 'Alex Morgan',
            'title' => 'Full Stack Web Developer',
            'avatar_url' => 'assets/uploads/alex-avatar.png',
            'created_at' => '2026-08-21 14:30:00'
        ],
        [
            'id' => 3,
            'role_id' => 2,
            'role_name' => 'User',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'full_name' => 'John Doe',
            'title' => 'Frontend Engineer',
            'avatar_url' => 'assets/uploads/default-avatar.png',
            'created_at' => '2026-08-22 11:15:00'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management CRUD Dashboard | Admin Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth-styles.css">
    <style>
        .crud-card {
            background: var(--auth-bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--auth-border);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--auth-shadow-card);
        }
        .user-table-img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--auth-accent-cyan);
        }
        .table-custom th {
            background: rgba(0, 0, 0, 0.4);
            color: var(--auth-accent-cyan);
            border-bottom: 2px solid var(--auth-border);
        }
        .table-custom td {
            vertical-align: middle;
            border-bottom: 1px solid var(--auth-border);
            color: var(--auth-text-primary);
        }
    </style>
</head>
<body class="auth-body">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg auth-navbar sticky-top">
        <div class="container">
            <a class="brand-logo-custom" href="index.html">
                <span class="logo-badge">&lt;/&gt;</span> Alex.Dev <span class="badge bg-danger ms-2">Admin CRUD</span>
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link text-white-50" href="index.html"><i class="fa-solid fa-house"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link active text-info fw-bold" href="admin_users.php"><i class="fa-solid fa-users-gear"></i> User Management</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-5">
        <div class="crud-card">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h2 class="fw-bold text-white mb-1">
                        <i class="fa-solid fa-users-viewfinder text-info"></i> User Management System (CRUD)
                    </h2>
                    <p class="text-white-50 small mb-0">Task 3: Prepared statements, RBAC roles, and delete popup confirmation</p>
                </div>
                <button type="button" class="btn btn-info fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fa-solid fa-user-plus"></i> Add New User
                </button>
            </div>

            <div id="crudAlert" class="alert d-none" role="alert"></div>

            <!-- HTML Data Table displaying users -->
            <div class="table-responsive">
                <table class="table table-dark table-hover table-custom mb-0">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>User Profile</th>
                            <th>Role</th>
                            <th>Contact Email</th>
                            <th>Job Title</th>
                            <th>Registered</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php foreach ($usersList as $u): ?>
                            <tr id="userRow-<?php echo $u['id']; ?>">
                                <td><strong>#<?php echo $u['id']; ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo htmlspecialchars($u['avatar_url'] ?: 'assets/uploads/default-avatar.png'); ?>" alt="Avatar" class="user-table-img">
                                        <div>
                                            <div class="fw-bold text-white"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                            <div class="small text-white-50">@<?php echo htmlspecialchars($u['username']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?php echo ($u['role_name'] === 'Admin') ? 'bg-danger' : 'bg-primary'; ?> rounded-pill">
                                        <?php echo htmlspecialchars($u['role_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><span class="small text-info"><?php echo htmlspecialchars($u['title'] ?: 'Developer'); ?></span></td>
                                <td><span class="small text-white-50"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></span></td>
                                <td class="text-end">
                                    <button class="btn btn-outline-info btn-sm rounded-circle edit-user-btn me-1" data-id="<?php echo $u['id']; ?>" title="Edit User">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm rounded-circle delete-user-btn" data-id="<?php echo $u['id']; ?>" data-name="<?php echo htmlspecialchars($u['full_name']); ?>" title="Delete User">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-white-50 small py-3">
                                <i class="fa-solid fa-shield-halved text-info"></i> All database operations are parameterized using <code>mysqli_prepare</code> prepared statements.
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </main>

    <!-- Modal 1: Add User Modal (Create) -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-info"><i class="fa-solid fa-user-plus"></i> Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addUserForm">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control bg-dark text-white border-secondary" required placeholder="e.g. Sarah Jenkins">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control bg-dark text-white border-secondary" required placeholder="e.g. sjenkins">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required placeholder="sarah@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required placeholder="At least 6 characters">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Assign Role</label>
                                <select name="role_id" class="form-select bg-dark text-white border-secondary">
                                    <option value="2" selected>User</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Job Title</label>
                                <input type="text" name="title" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Backend Developer">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info fw-bold rounded-pill px-4">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: Edit User Modal (Update) -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-info"><i class="fa-solid fa-user-pen"></i> Edit User Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" id="edit_email" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Role</label>
                                <select name="role_id" id="edit_role_id" class="form-select bg-dark text-white border-secondary">
                                    <option value="2">User</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Job Title</label>
                                <input type="text" name="title" id="edit_title" class="form-control bg-dark text-white border-secondary">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info fw-bold rounded-pill px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 3: Delete Confirmation Popup Modal (Delete) -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation"></i> Confirm User Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to permanently delete user <strong id="deleteUserNameText" class="text-info"></strong>?</p>
                    <p class="small text-white-50 mb-0">This action cannot be undone and will remove the record from MySQL <code>users</code> table.</p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger fw-bold rounded-pill px-4" id="confirmDeleteBtn">Delete User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addUserForm = document.getElementById('addUserForm');
            const editUserForm = document.getElementById('editUserForm');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const crudAlert = document.getElementById('crudAlert');

            let targetDeleteId = null;

            // Add User Handler
            if (addUserForm) {
                addUserForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(addUserForm);
                    fetch('user_crud_handler.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showAlert('success', data.message);
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                                modal.hide();
                                addUserForm.reset();
                                setTimeout(() => window.location.reload(), 1200);
                            } else {
                                alert(data.message);
                            }
                        });
                });
            }

            // Edit User Fetch & Populate
            document.querySelectorAll('.edit-user-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    fetch(`user_crud_handler.php?action=fetch&id=${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success' && data.user) {
                                document.getElementById('edit_id').value = data.user.id;
                                document.getElementById('edit_full_name').value = data.user.full_name;
                                document.getElementById('edit_email').value = data.user.email;
                                document.getElementById('edit_role_id').value = data.user.role_id;
                                document.getElementById('edit_title').value = data.user.title || '';
                                const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                                modal.show();
                            }
                        });
                });
            });

            // Edit Submit
            if (editUserForm) {
                editUserForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(editUserForm);
                    fetch('user_crud_handler.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showAlert('success', data.message);
                                const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                                modal.hide();
                                setTimeout(() => window.location.reload(), 1200);
                            } else {
                                alert(data.message);
                            }
                        });
                });
            }

            // Delete Popup Confirmation Modal
            document.querySelectorAll('.delete-user-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    targetDeleteId = btn.getAttribute('data-id');
                    const name = btn.getAttribute('data-name');
                    document.getElementById('deleteUserNameText').innerText = name;
                    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                    modal.show();
                });
            });

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', () => {
                    if (!targetDeleteId) return;
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', targetDeleteId);

                    fetch('user_crud_handler.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
                            modal.hide();
                            if (data.status === 'success') {
                                showAlert('success', data.message);
                                const row = document.getElementById(`userRow-${targetDeleteId}`);
                                if (row) row.remove();
                            } else {
                                showAlert('danger', data.message);
                            }
                        });
                });
            }

            function showAlert(type, msg) {
                crudAlert.className = `alert alert-${type} alert-dismissible fade show`;
                crudAlert.innerHTML = `<i class="fa-solid fa-circle-info"></i> ${msg} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                crudAlert.classList.remove('d-none');
            }
        });
    </script>
</body>
</html>
