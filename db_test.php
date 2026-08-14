<?php
/**
 * Task-1: Database Diagnostic Test Script
 * File: db_test.php
 * Verifies MySQL connection, phpMyAdmin database setup, and runs sample queries.
 */

require_once __DIR__ . '/config/db.php';

$dbStatus = getDatabaseConnection();
$tableCount = 0;
$contacts = [];
$projects = [];

if ($dbStatus["success"]) {
    $conn = $dbStatus["connection"];
    
    // Fetch count of tables
    $tablesResult = mysqli_query($conn, "SHOW TABLES");
    if ($tablesResult) {
        $tableCount = mysqli_num_rows($tablesResult);
    }
    
    // Fetch sample projects
    $projQuery = "SELECT * FROM projects ORDER BY id DESC LIMIT 5";
    $projRes = mysqli_query($conn, $projQuery);
    if ($projRes) {
        while ($row = mysqli_fetch_assoc($projRes)) {
            $projects[] = $row;
        }
    }
    
    // Fetch sample contact submissions
    $contactQuery = "SELECT * FROM contacts ORDER BY id DESC LIMIT 5";
    $contactRes = mysqli_query($conn, $contactQuery);
    if ($contactRes) {
        while ($row = mysqli_fetch_assoc($contactRes)) {
            $contacts[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Connection Test - Task 1</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            padding: 2rem;
            line-height: 1.6;
        }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 1.5rem;
            max-width: 850px;
            margin: 0 auto 1.5rem auto;
        }
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
        }
        .online { background: #166534; color: #4ade80; border: 1px solid #22c55e; }
        .offline { background: #991b1b; color: #fca5a5; border: 1px solid #ef4444; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #334155; padding: 0.75rem; text-align: left; }
        th { background: #090d16; color: #38bdf8; }
        code { background: #090d16; padding: 0.2rem 0.4rem; border-radius: 4px; color: #38bdf8; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🗄️ MySQL & PHP Connection Status</h1>
        
        <?php if ($dbStatus["success"]): ?>
            <div class="status-badge online">✓ Connected to MySQL database `portfolio_db` successfully!</div>
            <p style="margin-top: 1rem;">
                Host: <code>localhost</code> | User: <code>root</code> | Port: <code>3306</code><br>
                Tables Found: <strong><?php echo $tableCount; ?></strong> (`skills`, `projects`, `contacts`)
            </p>
        <?php else: ?>
            <div class="status-badge offline">✗ Database Connection Failed</div>
            <p style="margin-top: 1rem; color: #fca5a5;">
                Error: <code><?php echo htmlspecialchars($dbStatus["error"]); ?></code>
            </p>
            <p><strong>Troubleshooting Steps:</strong></p>
            <ol>
                <li>Ensure XAMPP or WAMP MySQL service is started.</li>
                <li>Open phpMyAdmin (http://localhost/phpmyadmin) and import <code>sql/schema.sql</code>.</li>
            </ol>
        <?php endif; ?>
    </div>

    <?php if ($dbStatus["success"]): ?>
        <div class="card">
            <h2>📦 Seeded Projects Table (`projects`)</h2>
            <?php if (count($projects) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Tech Stack</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td><?php echo $p["id"]; ?></td>
                                <td><strong><?php echo htmlspecialchars($p["title"]); ?></strong></td>
                                <td><?php echo htmlspecialchars($p["category"]); ?></td>
                                <td><code><?php echo htmlspecialchars($p["tech_stack"]); ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No project records found. Please run <code>sql/schema.sql</code> in phpMyAdmin.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>📩 Contact Form Submissions (`contacts`)</h2>
            <?php if (count($contacts) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $c): ?>
                            <tr>
                                <td><?php echo $c["id"]; ?></td>
                                <td><?php echo htmlspecialchars($c["name"]); ?></td>
                                <td><?php echo htmlspecialchars($c["email"]); ?></td>
                                <td><?php echo htmlspecialchars($c["subject"]); ?></td>
                                <td><?php echo $c["created_at"]; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No contact messages yet. Submit a message on <a href="index.html" style="color: #38bdf8;">Personal Portfolio</a>!</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
