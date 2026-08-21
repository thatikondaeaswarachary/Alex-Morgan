<?php
/**
 * Task-2 Deliverable: Real-Time AJAX User & Email Checker Endpoint
 * File: ajax_check_user.php
 * Accepts: GET or POST request with 'field' ('username' or 'email') and 'value'.
 * Returns: JSON payload indicating availability for live client-side validation.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/db.php';

// Demo fallback seed list when MySQL is offline
$fallbackExistingUsers = [
    'usernames' => ['alexmorgan', 'johndoe', 'admin', 'apexuser'],
    'emails' => ['alex@example.com', 'john@example.com', 'admin@apexplanet.com', 'alexmorgan@dev.com']
];

$field = isset($_REQUEST['field']) ? strtolower(trim($_REQUEST['field'])) : '';
$value = isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '';

if (empty($field) || empty($value)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing field or value parameter.'
    ]);
    exit;
}

if (!in_array($field, ['username', 'email'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid field specified. Must be username or email.'
    ]);
    exit;
}

$exists = false;
$dbStatus = getDatabaseConnection();

if ($dbStatus['success']) {
    $conn = $dbStatus['connection'];
    $sql = ($field === 'username') 
        ? "SELECT id FROM users WHERE LOWER(username) = LOWER(?) LIMIT 1"
        : "SELECT id FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $value);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $exists = true;
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Database offline fallback check
    if ($field === 'username') {
        $exists = in_array(strtolower($value), array_map('strtolower', $fallbackExistingUsers['usernames']));
    } else {
        $exists = in_array(strtolower($value), array_map('strtolower', $fallbackExistingUsers['emails']));
    }
}

$label = ($field === 'username') ? 'Username' : 'Email address';
$message = $exists 
    ? "{$label} '{$value}' is already taken."
    : "{$label} '{$value}' is available!";

echo json_encode([
    'status' => 'success',
    'exists' => $exists,
    'field' => $field,
    'value' => htmlspecialchars($value),
    'message' => $message,
    'db_connected' => $dbStatus['success']
]);
