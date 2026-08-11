<?php
/**
 * Task-1: PHP Basics & Syntax Demonstration Script
 * File: hello.php
 * Demonstrates:
 *  1. PHP Syntax & Variables
 *  2. Indexed, Associative, and Multidimensional Arrays
 *  3. Functions & Control Structures (if/else, switch, loops)
 *  4. Form Handling with $_GET and $_POST
 *  5. Reusable file includes (include/require)
 */

// Basic Variables & Data Types
$pageTitle = "PHP Basics Showcase - Task 1";
$developerName = "Full Stack Web Developer";
$courseDurationDays = 12;
$isEnvironmentConfigured = true;

// 1. Indexed Array
$techStack = ["HTML5", "CSS3", "JavaScript", "PHP", "MySQL", "Git"];

// 2. Associative Array
$developerProfile = [
    "name" => "Alex Morgan",
    "role" => "Junior Full Stack Developer",
    "email" => "alex@example.com",
    "experience_months" => 6
];

// 3. Multidimensional Array
$modules = [
    ["id" => 1, "name" => "Foundation & Environment Setup", "duration" => "Days 1-12", "status" => "Completed"],
    ["id" => 2, "name" => "Interactive UI & Frontend Development", "duration" => "Days 13-24", "status" => "In Progress"],
    ["id" => 3, "name" => "Backend Development & Database Integration", "duration" => "Days 25-36", "status" => "Upcoming"]
];

// 4. Custom Function
function calculateProgress($completedDays, $totalDays = 36) {
    if ($totalDays <= 0) return 0;
    $percentage = ($completedDays / $totalDays) * 100;
    return round($percentage, 1);
}

// 5. Handling $_GET and $_POST
$submittedMessage = "";
$submissionType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userKeyword = isset($_POST["keyword"]) ? htmlspecialchars($_POST["keyword"]) : "";
    $submittedMessage = "POST Received! You searched for: <strong>" . $userKeyword . "</strong>";
    $submissionType = "POST";
} elseif (isset($_GET["demo_param"])) {
    $paramVal = htmlspecialchars($_GET["demo_param"]);
    $submittedMessage = "GET Received! Parameter value: <strong>" . $paramVal . "</strong>";
    $submissionType = "GET";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --accent-color: #38bdf8;
            --text-color: #f8fafc;
            --subtext: #94a3b8;
            --border-color: #334155;
            --success: #22c55e;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 2rem;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        h1, h2, h3 {
            color: var(--accent-color);
            margin-top: 0;
        }
        .badge {
            display: inline-block;
            background: rgba(56, 189, 248, 0.15);
            color: var(--accent-color);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid var(--success);
            color: #4ade80;
            margin-bottom: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid var(--border-color);
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background: rgba(255,255,255,0.05);
            color: var(--accent-color);
        }
        form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        input[type="text"] {
            flex: 1;
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: #090d16;
            color: white;
        }
        button, a.btn {
            background: var(--accent-color);
            color: #0f172a;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        button:hover, a.btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>👋 Hello World from PHP!</h1>
            <p>PHP installation and environment setup verified successfully.</p>
            <div class="alert">
                ✓ PHP Version: <strong><?php echo phpversion(); ?></strong> | Server API: <strong><?php echo php_sapi_name(); ?></strong>
            </div>
            <p>Current Server Time: <strong><?php echo date('Y-m-d H:i:s'); ?></strong></p>
        </div>

        <!-- 1. Variables & Control Structures -->
        <div class="card">
            <h2>1. Variables & Control Structures</h2>
            <p>Developer: <strong><?php echo $developerProfile["name"]; ?></strong> (<?php echo $developerProfile["role"]; ?>)</p>
            <p>
                Status: 
                <?php if ($isEnvironmentConfigured): ?>
                    <span style="color: var(--success);">Environment Active & Fully Functional</span>
                <?php else: ?>
                    <span style="color: #ef4444;">Environment Pending Setup</span>
                <?php endif; ?>
            </p>
            <p>Overall Roadmap Progress: <strong><?php echo calculateProgress($courseDurationDays, 36); ?>%</strong></p>
        </div>

        <!-- 2. Arrays Showcase -->
        <div class="card">
            <h2>2. PHP Arrays (Indexed, Associative & Multidimensional)</h2>
            
            <h3>Indexed Array (Tech Stack):</h3>
            <div>
                <?php foreach ($techStack as $tech): ?>
                    <span class="badge"><?php echo $tech; ?></span>
                <?php endforeach; ?>
            </div>

            <h3>Multidimensional Array (Course Roadmap):</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Module Name</th>
                        <th>Timeline</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $mod): ?>
                        <tr>
                            <td><?php echo $mod["id"]; ?></td>
                            <td><?php echo $mod["name"]; ?></td>
                            <td><?php echo $mod["duration"]; ?></td>
                            <td><?php echo $mod["status"]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 3. Handling $_GET and $_POST -->
        <div class="card">
            <h2>3. Form Handling with $_GET and $_POST</h2>
            
            <?php if (!empty($submittedMessage)): ?>
                <div class="alert">
                    <?php echo $submittedMessage; ?>
                </div>
            <?php endif; ?>

            <h3>Test $_POST Submission:</h3>
            <form method="POST" action="hello.php">
                <input type="text" name="keyword" placeholder="Enter keyword to search..." required>
                <button type="submit">Send POST Request</button>
            </form>

            <h3 style="margin-top: 1.5rem;">Test $_GET Submission:</h3>
            <p>Click link to test $_GET URL parameter handling:</p>
            <a href="hello.php?demo_param=PHP_MySQL_Task1_Success" class="btn">Send GET Request (?demo_param=...)</a>
        </div>

        <p style="text-align: center; color: var(--subtext);">PHP & MySQL Full Stack Web Development - Task 1 Deliverable</p>
    </div>
</body>
</html>
