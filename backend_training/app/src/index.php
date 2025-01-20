<?php
header('Content-Type: application/json');

// Environment variables for database connection
$db_host = getenv('DB_HOST') ?: 'db';
$db_port = getenv('DB_PORT') ?: '5432';
$db_name = getenv('DB_DATABASE') ?: 'prtimes';
$db_user = getenv('DB_USERNAME') ?: 'prtimes';
$db_pass = getenv('DB_PASSWORD') ?: 'prtimes';

// PDO connection
try {
    $pdo = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed', 'error' => $e->getMessage()]);
    exit;
}

// Handle health check route
if ($_SERVER['REQUEST_URI'] === '/health') {
    try {
        $stmt = $pdo->query("SELECT 1");
        $result = $stmt->fetchColumn();
        if ($result == 1) {
            echo json_encode(['status' => 'ok', 'database' => 'connected']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database not responding']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Query failed', 'error' => $e->getMessage()]);
    }
    exit;
}
