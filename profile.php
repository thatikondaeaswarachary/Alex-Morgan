<?php
/**
 * Task-3 Deliverable: Profile Management & Avatar Upload Page
 * File: profile.php
 * Displays dynamic profile info, handles profile details edit,
 * and processes profile picture upload with size & file type validation.
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth_middleware.php';

requireLogin();

$user = getCurrentUser();
$userId = $user['id'];
$successMsg = '';
$errorMsg = '';

// Handle Profile Text Updates & Avatar Uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Text Fields Update
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $fullName = trim(htmlspecialchars($_POST['full_name'] ?? ''));
        $title = trim(htmlspecialchars($_POST['title'] ?? ''));
        $phone = trim(htmlspecialchars($_POST['phone'] ?? ''));
        $bio = trim(htmlspecialchars($_POST['bio'] ?? ''));

        if (empty($fullName)) {
            $errorMsg = 'Full Name cannot be empty.';
        } else {
            $dbStatus = getDatabaseConnection();
            if ($dbStatus['success']) {
                $conn = $dbStatus['connection'];
                $stmt = mysqli_prepare($conn, "UPDATE users SET full_name = ?, title = ?, phone = ?, bio = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ssssi", $fullName, $title, $phone, $bio, $userId);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['full_name'] = $fullName;
                    $_SESSION['title'] = $title;
                    $_SESSION['phone'] = $phone;
                    $_SESSION['bio'] = $bio;
                    $successMsg = 'Profile details updated successfully!';
                } else {
                    $errorMsg = 'Database update failed: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['full_name'] = $fullName;
                $_SESSION['title'] = $title;
                $_SESSION['phone'] = $phone;
                $_SESSION['bio'] = $bio;
                $successMsg = 'Profile details updated in demo session!';
            }
            $user = getCurrentUser();
        }
    }

    // 2. Avatar File Upload Processing
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['avatar_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = 'File upload error code: ' . $file['error'];
        } else {
            // Validation 1: Size Limit (Max 2MB = 2097152 bytes)
            $maxSizeBytes = 2 * 1024 * 1024;
            if ($file['size'] > $maxSizeBytes) {
                $errorMsg = 'Profile image size exceeds 2MB limit. Please upload a smaller image.';
            } else {
                // Validation 2: Allowed File Extensions & MIME Types
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

                $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($fileExt, $allowedExtensions) || !in_array($mimeType, $allowedMimeTypes)) {
                    $errorMsg = 'Invalid image format. Only JPG, PNG, and WEBP files are allowed.';
                } else {
                    // Safe File Saving into assets/uploads/
                    $targetDir = __DIR__ . '/assets/uploads/';
                    if (!file_exists($targetDir)) {
                        @mkdir($targetDir, 0755, true);
                    }

                    $newFilename = 'avatar_' . $userId . '_' . time() . '.' . $fileExt;
                    $targetFilePath = $targetDir . $newFilename;
                    $relAvatarUrl = 'assets/uploads/' . $newFilename;

                    if (move_uploaded_filename_safe($file['tmp_name'], $targetFilePath)) {
                        $dbStatus = getDatabaseConnection();
                        if ($dbStatus['success']) {
                            $conn = $dbStatus['connection'];
                            $stmt = mysqli_prepare($conn, "UPDATE users SET avatar_url = ? WHERE id = ?");
                            mysqli_stmt_bind_param($stmt, "si", $relAvatarUrl, $userId);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_close($stmt);
                        }
                        $_SESSION['avatar_url'] = $relAvatarUrl;
                        $user['avatar_url'] = $relAvatarUrl;
                        $successMsg = 'Profile picture updated successfully!';
                    } else {
                        $errorMsg = 'Failed to save uploaded file on server.';
                    }
                }
            }
        }
    }
}

function move_uploaded_filename_safe($tmpName, $targetPath) {
    if (is_uploaded_file($tmpName)) {
        return move_uploaded_file($tmpName, $targetPath);
    }
    return @copy($tmpName, $targetPath);
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management | Alex Morgan Developer Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/auth-styles.css">
    <style>
        .profile-avatar-lg {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--auth-accent-cyan);
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
        }
        .profile-card {
            background: var(--auth-bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--auth-border);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--auth-shadow-card);
        }
    </style>
</head>
<body class="auth-body">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg auth-navbar sticky-top">
        <div class="container">
            <a class="brand-logo-custom" href="index.html">
                <span class="logo-badge">&lt;/&gt;</span> Alex.Dev <span class="badge bg-success ms-2">Task 3</span>
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#profileNav">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="profileNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link text-white-50" href="index.html"><i class="fa-solid fa-house"></i> Home</a></li>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link text-info fw-bold" href="admin_users.php"><i class="fa-solid fa-users-gear"></i> Admin CRUD Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link active text-white" href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-5">
        <div class="row g-4">
            
            <!-- Left Column: User Summary Card -->
            <div class="col-12 col-lg-4">
                <div class="profile-card text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Profile Avatar" class="profile-avatar-lg" id="avatarPreview">
                        <span class="position-absolute bottom-0 end-0 bg-info text-dark p-2 rounded-circle" style="line-height: 1;">
                            <i class="fa-solid fa-camera"></i>
                        </span>
                    </div>

                    <h3 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p class="text-info small mb-2"><i class="fa-solid fa-briefcase"></i> <?php echo htmlspecialchars($user['title']); ?></p>

                    <div class="mb-3">
                        <span class="badge <?php echo ($user['role_name'] === 'Admin') ? 'bg-danger' : 'bg-primary'; ?> px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-shield-halved"></i> <?php echo htmlspecialchars($user['role_name']); ?> Role
                        </span>
                    </div>

                    <hr class="border-secondary my-3">

                    <div class="text-start small text-white-50">
                        <p class="mb-2"><i class="fa-solid fa-at text-info"></i> <strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p class="mb-2"><i class="fa-solid fa-envelope text-info"></i> <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p class="mb-2"><i class="fa-solid fa-phone text-info"></i> <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?: 'Not specified'); ?></p>
                    </div>

                    <!-- Profile Picture Upload Form -->
                    <form method="POST" enctype="multipart/form-data" class="mt-4 pt-3 border-top border-secondary">
                        <label for="avatar_file" class="form-label text-white small fw-bold text-start d-block">
                            <i class="fa-solid fa-upload"></i> Upload Profile Picture
                        </label>
                        <input type="file" class="form-control form-control-sm bg-dark text-white border-secondary mb-2" id="avatar_file" name="avatar_file" accept="image/png, image/jpeg, image/webp" required>
                        <span class="small text-white-50 d-block text-start mb-2" style="font-size: 0.78rem;">Max size: 2MB. Formats: JPG, PNG, WEBP</span>
                        <button type="submit" class="btn btn-info btn-sm w-100 fw-bold rounded-pill">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Update Avatar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Edit Profile Details Form -->
            <div class="col-12 col-lg-8">
                <div class="profile-card">
                    
                    <h3 class="fw-bold text-white mb-3">
                        <i class="fa-solid fa-user-pen text-info"></i> Edit Profile Information
                    </h3>
                    <p class="text-white-50 small mb-4">Task 3 Profile Management with server-side validation & database sync</p>

                    <?php if (!empty($successMsg)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check"></i> <?php echo $successMsg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $errorMsg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="profile.php">
                        <input type="hidden" name="action" value="update_profile">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label text-white small fw-bold">Full Name</label>
                                <div class="input-group-custom">
                                    <span class="input-icon-text"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" class="form-control-custom" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="title" class="form-label text-white small fw-bold">Job Title</label>
                                <div class="input-group-custom">
                                    <span class="input-icon-text"><i class="fa-solid fa-briefcase"></i></span>
                                    <input type="text" class="form-control-custom" id="title" name="title" value="<?php echo htmlspecialchars($user['title']); ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="email_readonly" class="form-label text-white small fw-bold">Email Address (Read-only)</label>
                                <div class="input-group-custom opacity-75">
                                    <span class="input-icon-text"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" class="form-control-custom" id="email_readonly" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label text-white small fw-bold">Phone Number</label>
                                <div class="input-group-custom">
                                    <span class="input-icon-text"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" class="form-control-custom" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="+1 (555) 000-0000">
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="bio" class="form-label text-white small fw-bold">Professional Bio</label>
                                <textarea class="form-control bg-dark text-white border-secondary rounded-3" id="bio" name="bio" rows="4" placeholder="Tell us about your background and technical skills..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-3 align-items-center">
                            <button type="submit" class="btn btn-info fw-bold rounded-pill px-4">
                                <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
                            </button>
                            <?php if (isAdmin()): ?>
                                <a href="admin_users.php" class="btn btn-outline-light rounded-pill px-4">
                                    <i class="fa-solid fa-users-gear"></i> Open Admin CRUD
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
