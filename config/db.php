<?php
/**
 * Task-1: MySQL Database Connection Configuration
 * File: config/db.php
 * Demonstrates: Connecting PHP with MySQL using mysqli_connect()
 */

$dbHost = "localhost";
$dbUser = "root";
$dbPass = ""; // Default XAMPP/WAMP MySQL password is empty
$dbName = "portfolio_db";
$dbPort = 3306;

// Create connection
$conn = @mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

// Function to get connection instance or error message
function getDatabaseConnection() {
    global $conn, $dbHost, $dbUser, $dbPass, $dbName, $dbPort;
    
    if (!$conn) {
        return [
            "success" => false,
            "error" => mysqli_connect_error(),
            "errno" => mysqli_connect_errno(),
            "connection" => null
        ];
    }
    
    // Set charset to utf8mb4 for full unicode support
    mysqli_set_charset($conn, "utf8mb4");
    
    return [
        "success" => true,
        "error" => null,
        "connection" => $conn
    ];
}
?>
