<?php
require_once 'config.php'; // 設定ファイルを読み込み

// レスポンスのヘッダーを設定
// JSON形式で返すためのヘッダー
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

// Strip out query parameters by getting only the path
$requestUri = $_SERVER['REQUEST_URI']; // This strips out the query string

// Now $requestUri will only have the path, and no query parameters
global $pdo;

$routes = [
    'GET' => [
        '#^/health$#' => 'handleHealthCheck',
        '#^/todos$#' => 'handleGetTodos',  // `/todos` → 全てのTODOを取得
        '#^/todos/(\d+)$#' => 'handleGetTodoById',  // `/todos/{id}` → 特定のTODOを取得
    ],
    'POST' => [
        '#^/todos$#' => 'handlePostTodos',  // `/todos` → 新しいTodoを作成
    ],
    'PUT' => [
        '#^/todos(?:\?id=(\d+))?$#' => 'handlePutTodoById',  // `/todos?id={id}` → 特定のTODOを更新
    ],
    'DELETE' => [
        '#^/todos(?:\?id=(\d+))?$#' => 'handleDeleteTodoById', // `/todos?id={id}` → 特定のTODOを削除
    ]
];

if (isset($routes[$method])) {
    foreach ($routes[$method] as $pattern => $handler) {
        if (preg_match($pattern, $requestUri, $matches)) {
            array_shift($matches);
            call_user_func_array($handler, array_merge([$pdo], $matches));
            exit;
        }
    }
}

http_response_code(500);
echo json_encode(['error' => 'Internal Server Error']);
exit;

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
            throw new RuntimeException('Database connection failed');
        }
    } catch (Exception $e) {
        // クエリエラー時のレスポンス
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

/**
 * `/todos` エンドポイントを処理します。
 *
 * @param PDO $pdo データベース接続のためのPDOインスタンス
 * @return void
 */
function handleGetTodos(PDO $pdo): void
{
    try {
        // データベースからTodoリストを取得
        $stmt = $pdo->query("SELECT todos.id, todos.title, statuses.name FROM todos JOIN statuses ON todos.status_id = statuses.id;");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // レスポンスを返却
        echo json_encode(['status' => 'ok', 'todos' => $result]);
    } catch (Exception $e) {
        // クエリエラー時のレスポンス
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to get todos',
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

/**
 * `/todos/{id}` エンドポイントを処理します。
 *
 * @param PDO $pdo データベース接続のためのPDOインスタンス
 * @param int $id 取得するTODOのID
 * @return void
 */
function handleGetTodoById(PDO $pdo, int $id): void
{
    try {
        // IDを指定してTodoを取得
        $stmt = $pdo->prepare("SELECT todos.id, todos.title, statuses.name FROM todos JOIN statuses ON todos.status_id = statuses.id WHERE todos.id = :id;");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $todo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($todo) {
            // Todoが見つかった場合
            http_response_code(200);
            echo json_encode(['status' => 'ok', 'todos' => $todo]);
        } else {
            // Todoが見つからない場合
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Todoが見つかりません'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        // クエリエラー時のレスポンス
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to get todo',
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

/**
 * `/todos` エンドポイントを処理します。（新しいTodoを作成）
 *
 * @param PDO $pdo データベース接続のためのPDOインスタンス
 * @return void
 */
function handlePostTodos(PDO $pdo): void
{
    try {
        // リクエストボディを取得
        $input = json_decode(file_get_contents("php://input"), true);

        // JSONのバリデーション（titleがあるか）
        if (!isset($input['title']) || empty(trim($input['title']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            exit;
        }

        // `status_id` のデフォルト値を設定（例: 1 = "pending"）
        $defaultStatusId = 1;

        // データベースに新しいTodoを挿入
        $stmt = $pdo->prepare("INSERT INTO todos (title, status_id) VALUES (:title, :status_id)");
        $stmt->bindParam(':title', $input['title'], PDO::PARAM_STR);
        $stmt->bindParam(':status_id', $defaultStatusId, PDO::PARAM_INT);
        $stmt->execute();

        // 挿入されたIDを取得
        $id = $pdo->lastInsertId();

        // 挿入されたデータを返す
        http_response_code(201);
        echo json_encode([
            'status' => 'ok',
            'todos' => [
                'id' => $id,
                'title' => $input['title'],
                'status_id' => $defaultStatusId
            ]
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        // クエリエラー時のレスポンス
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create todo',
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

/**
 * `/todos/{id}` エンドポイントを処理します。（特定のTodoを更新）
 *
 * @param PDO $pdo データベース接続のためのPDOインスタンス
 * @param int $id 更新するTODOのID
 * @return void
 */
function handlePutTodoById(PDO $pdo, int $id): void
{
    try {
        // トランザクション開始
        $pdo->beginTransaction();

        // リクエストボディを取得
        $input = json_decode(file_get_contents("php://input"), true);

        // JSONのバリデーション（title または status のどちらかが必要）
        if (!is_array($input) || (!isset($input['title']) && !isset($input['status']))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Either title or status must be provided'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // `title` がある場合は trim() して空白のみを防ぐ
        if (isset($input['title']) && trim($input['title']) === '') {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Title cannot be empty'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 指定されたIDのTodoが存在するか確認
        $stmt = $pdo->prepare("SELECT id FROM todos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Todoが見つかりません'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 更新用のSQLを動的に組み立て
        $updateFields = [];
        if (isset($input['title'])) {
            $updateFields[] = "title = :title";
        }

        if (isset($input['status'])) {
            // `status` の値に応じて `status_id` を取得
            $stmt = $pdo->prepare("SELECT id FROM statuses WHERE name = :status");
            $stmt->bindParam(':status', $input['status'], PDO::PARAM_STR);
            $stmt->execute();
            $statusId = $stmt->fetchColumn();

            if (!$statusId) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid status value'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $updateFields[] = "status_id = :status_id";
        }

        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'No valid fields provided for update'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $updateSQL = "UPDATE todos SET " . implode(", ", $updateFields) . " WHERE id = :id";
        $stmt = $pdo->prepare($updateSQL);

        if (isset($input['title'])) {
            $stmt->bindParam(':title', $input['title'], PDO::PARAM_STR);
        }
        if (isset($statusId)) {
            $stmt->bindParam(':status_id', $statusId, PDO::PARAM_INT);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // コミット
        $pdo->commit();

        // 更新されたデータを取得
        $stmt = $pdo->prepare("SELECT todos.id, todos.title, statuses.name AS status FROM todos JOIN statuses ON todos.status_id = statuses.id WHERE todos.id = :id;");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $updatedTodo = $stmt->fetch(PDO::FETCH_ASSOC);

        // 更新結果を返す
        http_response_code(200);
        echo json_encode([
            'status' => 'ok',
            'todo' => $updatedTodo
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $pdo->rollBack(); // 失敗時にロールバック
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update todo',
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}


/**
 * `/todos/{id}` エンドポイントを処理します。（特定のTodoを削除）
 *
 * @param PDO $pdo データベース接続のためのPDOインスタンス
 * @param int $id 削除するTODOのID
 * @return void
 */
function handleDeleteTodoById(PDO $pdo, int $id): void
{
    try {
        // 削除前に対象のTodoを取得
        $stmt = $pdo->prepare("SELECT * FROM todos WHERE id = :id;");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $todo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$todo) {
            // Todoが見つからない場合
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Todoが見つかりません'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Todoを削除
        $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id;");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // 削除成功時
            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'deleted_todo' => $todo // 削除前のデータを返す
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // 予期しないエラー（通常は発生しないが念のため）
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => '削除に失敗しました'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        // クエリエラー時のレスポンス
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'エラーが発生しました',
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}
