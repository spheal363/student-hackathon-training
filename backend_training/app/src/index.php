<?php
require_once 'config.php'; // 設定ファイルを読み込み

/**
 * `/health` エンドポイントを処理します。
 *
 * @param PDO $pdo データベース接続のためのPDOインスタンス
 * @return void
 */
function handleHealthCheck(PDO $pdo): void
{
    try {
        // データベース接続を確認
        $stmt = $pdo->query("SELECT 1");
        $result = $stmt->fetchColumn();

        if ($result == 1) {
            // データベース接続が正常の場合のレスポンス
            echo json_encode(['status' => 'ok', 'database' => 'connected']);
        } else {
            // データベース応答なしの場合のエラーレスポンス
            throw new RuntimeException('データベースが応答していません');
        }
    } catch (Exception $e) {
        // クエリエラー時のレスポンス
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'ヘルスチェックに失敗しました',
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// ルーティングロジック
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// グローバル変数 $pdo を使用
global $pdo;

switch ($requestUri) {
    case '/health':
        handleHealthCheck($pdo); // config.php の $pdo を関数に渡します
        break;

    default:
        // 不明なエンドポイントの場合の404レスポンス
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'エンドポイントが見つかりません']);
        break;
}
