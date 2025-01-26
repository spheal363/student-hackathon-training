<?php

declare(strict_types=1);

header('Content-Type: application/json');

// データベース接続情報
$DB_CONFIG = [
    'host' => getenv('DB_HOST') ?: 'db',
    'port' => getenv('DB_PORT') ?: '5432',
    'dbname' => getenv('DB_DATABASE') ?: 'prtimes',
    'username' => getenv('DB_USERNAME') ?: 'prtimes',
    'password' => getenv('DB_PASSWORD') ?: 'prtimes',
];

/**
 * PDO インスタンスを作成する。
 *
 * @param array $config Database configuration.
 * @return PDO
 * @throws PDOException if the connection fails.
 */
function createPDO(array $config): PDO
{
    $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s", $config['host'], $config['port'], $config['dbname']);
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

// グローバル変数 $pdo に PDO インスタンスを作成して代入
try {
    $pdo = createPDO($DB_CONFIG);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed',
        'error' => $e->getMessage()
    ]);
    exit;
}
