<?php
/**
 * Task-1: Contact Form Backend Handler
 * File: contact.php
 * Demonstrates: Handling forms with $_POST, input sanitization, server-side validation,
 *               and saving data into MySQL using mysqli_connect().
 */

require_once __DIR__ . '/config/db.php';

$errors = [];
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Extract and sanitize $_POST inputs
    $name = isset($_POST["name"]) ? trim(htmlspecialchars($_POST["name"])) : "";
    $email = isset($_POST["email"]) ? trim(filter_var($_POST["email"], FILTER_SANITIZE_EMAIL)) : "";
    $serviceType = isset($_POST["service_type"]) ? trim(htmlspecialchars($_POST["service_type"])) : "General Inquiry";
    $subject = isset($_POST["subject"]) ? trim(htmlspecialchars($_POST["subject"])) : "";
    $message = isset($_POST["message"]) ? trim(htmlspecialchars($_POST["message"])) : "";
    $newsletter = isset($_POST["newsletter"]) ? 1 : 0;

    // 2. Server-side validation
    if (empty($name) || strlen($name) < 2) {
        $errors[] = "Please provide your full name (at least 2 characters).";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (empty($subject)) {
        $errors[] = "Subject line is required.";
    }

    if (empty($message) || strlen($message) < 10) {
        $errors[] = "Message details must be at least 10 characters long.";
    }

    // 3. Database Insertion if no errors
    if (empty($errors)) {
        $dbStatus = getDatabaseConnection();
        if ($dbStatus["success"]) {
            $conn = $dbStatus["connection"];

            // Prepare SQL Statement (Prevents SQL Injection)
            $stmt = mysqli_prepare($conn, "INSERT INTO contacts (name, email, service_type, subject, message, newsletter) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $serviceType, $subject, $message, $newsletter);
                if (mysqli_stmt_execute($stmt)) {
                    $successMessage = "Thank you, " . $name . "! Your message has been saved to the database successfully.";
                } else {
                    $errors[] = "Database execution error: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } else {
                $errors[] = "Database preparation error: " . mysqli_error($conn);
            }
        } else {
            // Fallback message if database connection isn't online
            $successMessage = "Form validated successfully on server! (Note: MySQL server is currently offline or portfolio_db is unseeded. Connect MySQL in XAMPP to record live submissions).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Processing - Task 1 PHP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .response-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 2.5rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
        h1 { color: #38bdf8; margin-top: 0; }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #4ade80; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table th, .data-table td { border: 1px solid #334155; padding: 0.75rem; text-align: left; }
        .data-table th { background: #090d16; color: #38bdf8; }
        .btn {
            display: inline-block;
            background: #38bdf8;
            color: #0f172a;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="response-card">
        <h1>📩 PHP Contact Form Processing</h1>

        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <strong>✓ Success:</strong> <?php echo $successMessage; ?>
            </div>

            <h3>Submitted Data Payload ($_POST):</h3>
            <table class="data-table">
                <tr><th>Full Name</th><td><?php echo $name; ?></td></tr>
                <tr><th>Email Address</th><td><?php echo $email; ?></td></tr>
                <tr><th>Service Requested</th><td><?php echo $serviceType; ?></td></tr>
                <tr><th>Subject</th><td><?php echo $subject; ?></td></tr>
                <tr><th>Message</th><td><?php echo nl2br($message); ?></td></tr>
                <tr><th>Newsletter Opt-in</th><td><?php echo $newsletter ? "Yes" : "No"; ?></td></tr>
            </table>

            <a href="index.html" class="btn">← Return to Portfolio</a>
            <a href="db_test.php" class="btn" style="background: #a855f7; color: white;">View MySQL Records</a>
        <?php else: ?>
            <div class="alert alert-danger">
                <strong>✗ Form Processing Errors:</strong>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo $err; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="index.html#contact" class="btn">← Back to Contact Form</a>
        <?php endif; ?>
    </div>
</body>
</html>
