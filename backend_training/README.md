## 0. 課題説明
TODO リストアプリを作成するためのバックエンド開発トレーニングを開始します。このトレーニングでは、PHPとPostgreSQLを使用して、RESTful APIを作成し、データベースとのやり取りを行います。このトレーニングを完了すると、PHPを使用してデータベースと通信する方法を学び、APIエンドポイントを作成する方法を理解することができます。
backend trainingの目標は以下の通りです。
- PHPを使用してRESTful APIを作成する。
- PostgreSQLデータベースとの通信を設定する。
- APIエンドポイントを作成し、データベースとのやり取りを行う。 
- APIエンドポイントをテストし、データベースとの通信を確認する。

TODOリストアプリのバックエンドは以下の仕様に従います：
- `todos`テーブルを作成し、タスクのID、タイトル、ステータスを格納します。
- タスクのステータスは`active`、`completed`、`pending`のいずれかです。
- APIエンドポイントを作成し、タスクのリストを取得、作成、更新、削除できるようにします。

## 1. 環境設定
構成図：
```mermaid
graph LR
    N[Nginx] -- リクエスト転送 --> A[PHP]
    A <-- データベース操作 --> B[PostgreSQL]
    A -- 応答 --> N
```
-    **Dockerのインストール:** Dockerがインストールされ、実行されていること。([https://www.docker.com/get-started/](https://www.docker.com/get-started/))
-    **Docker Composeのインストール:** Docker Composeがインストールされていること。([https://docs.docker.com/compose/install/](https://docs.docker.com/compose/install/))
-    **コードエディタ/IDEのインストール:** テキストエディタまたはIDE（例: VS Code）がインストールされていること。
-    **プロジェクトへの移動:** ターミナルを開き、`cd backend_tranning/`を使ってプロジェクトのルートディレクトリに移動してください。
-    **プロジェクトファイル:** `compose.yml`、`app/Dockerfile`、`app/src/`、および`nginx/default.conf`ファイルがあることを確認してください。

## 2. Docker Composeの設定

-    **Dockerコンテナの起動:** `docker-compose up -d`コマンドを実行してコンテナを起動します。 
- `docker ps`またはDocker Desktopを使って、コンテナが実行中であることを確認します。

## 3. データベースの設定
### 3.1.A. データベースクライアントで接続する場合
-    **データベースクライアント:** PostgreSQLデータベースクライアント（`pgAdmin`、`DBeaver`など）がインストールされていること。
-    **データベースへの接続:** `compose.yml`ファイルにある以下の接続情報を使って、PostgreSQLデータベースに正常に接続します。
      ```sql
        DATABASE: 2025winterhackathon
        PORT: 5432
        USERNAME: prtimes
        PASSWORD: 2025winter
      ```
### 3.1.B. dockerコンテナ内で接続する場合
-    **`app`コンテナに接続:** `docker exec -it app bash`を使って`app`コンテナに接続します。
-   **`psql`コマンド:** `psql -U prtimes -d 2025winterhackathon`を使って、PostgreSQLデータベースに接続します。

### 3.2 テーブルの作成
TODOリストアプリのデータベーススキーマを設計し、必要なテーブルを作成します。 以下のデータベース設計をもとに、todos テーブルと statuses テーブルを作成してください。

#### ER図 (データベース設計)
以下の ER図は、todos テーブルと statuses テーブルの関係を表しています。各 todo 項目には、status (状態) が関連付けられます。
<br />PostgreSQLに作成するテーブルの構造を以下の ER図を参考に作成してください。
```mermaid
erDiagram
    todos {
        int id
        varchar title
        int status_id
    }
    statuses {
        int id
        varchar name
    }
    todos }o--|| statuses : "has status"
```

- todos テーブル: TODOリストの項目を管理します。
- statuses テーブル: 各TODO項目の状態（例: "未完了", "進行中", "完了"）を管理します。
- リレーション: todos の status_id は、statuses の id を参照します (外部キー制約)。

<details>
  <summary>CREATE SQL</summary>

以下のSQLクエリを使用して、statuses と todos の2つのテーブルを作成してください。
<br/>`statuses`テーブルの作成：
```sql
CREATE TABLE statuses (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);
```

<br />`todos`テーブルの作成：
```sql
CREATE TABLE todos (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    status_id INT NOT NULL REFERENCES statuses(id)
);
```

</details>

### 3.3 データの挿入
-    **ステータスの挿入:** 以下のSQLクエリを実行して、`statuses`テーブルにデータを挿入します。
```sql
INSERT INTO statuses (name) VALUES
    ('pending'),
    ('completed'),
    ('active');
```
-    **事前タスクの挿入:** 以下のSQLクエリを実行して、`todos`テーブルにデータを挿入します。
```sql
INSERT INTO todos (title, status_id) VALUES
    ('Todo 1', 1),
    ('Todo 2', 2),
    ('Todo 3', 1),
    ('Todo 4', 3);
```

### 3.4 データの確認
**SQLクエリの実行:** 以下のSQLクエリを実行して、`todos`テーブルのデータを確認します。ステップ3.3で挿入したデータが表示されることを確認してください。
```sql
SELECT * FROM todos;
```
以下のような結果が表示されると、OKです。
```bash
  id | title  | status
-----+--------+---------
   1 | Todo 1 | pending
   2 | Todo 2 | completed
   3 | Todo 3 | pending
   4 | Todo 4 | active
(4 rows)
```

## 4. PHP API の開発

1. **`config.php`の作成:**  
   `php/src`ディレクトリに、データベース接続設定を含む`config.php`ファイルを作成します。

2. **`index.php`の作成:**  
   同じディレクトリに`index.php`ファイルを作成し、`/todos`エンドポイントを実装します。 
   <br>全てのコードを`index.php`に書く必要はありません。任意のファイルを作成して、コードを分割することができます。
   <br><br>以下の機能を追加してください：

    - **GET `/todos`（全てのTodoを取得）:**
        - 成功時: HTTPステータスコード`200`で全てのTodoをJSON形式で返します。Todoが存在しない場合、空の配列`[]`を返します。
          - 例：<br> 
            ```json
            {
                "status": "ok", 
                "data": [
                    {"id": 1, "title": "Todo 1", "status": "pending"},
                    {"id": 2, "title": "Todo 2", "status": "completed"},
                    {"id": 3, "title": "Todo 3", "status": "pending"},
                    {"id": 4, "title": "Todo 4", "status": "active"}
                ]        
            }
            
            ``` 
        - エラー時: HTTPステータスコード`500`でエラーメッセージを`{"error": "エラーメッセージ"}`の形式で返します。

    - **GET `/todos?id={id}`（IDでTodoを取得）:**
        - 成功時: HTTPステータスコード`200`で指定されたIDのTodoをJSON形式で返します。Todoが存在しない場合、空のオブジェクト`{}`を返します。
        - IDが見つからない場合: HTTPステータスコード`404`でエラーメッセージ`{"error": "Todoが見つかりません"}`を返します。
        - エラー時: HTTPステータスコード`500`でエラーメッセージを返します。

    - **POST `/todos`（新しいTodoを作成）:**
        - リクエストボディとして`{"title": "Todoのタイトル"}`のJSONを受け付けます。
        - 成功時: HTTPステータスコード`201`で作成されたTodoをJSON形式で返します。
        - エラー時: HTTPステータスコード`500`でエラーメッセージを返します。

    - **PUT `/todos?id={id}`（IDでTodoを更新）:**
        - リクエストボディで`title`または`completed`ステータスを更新します。
        - 成功時: HTTPステータスコード`200`で更新されたTodoをJSON形式で返します。
        - IDが見つからない場合: HTTPステータスコード`404`でエラーメッセージを返します。
        - エラー時: HTTPステータスコード`500`でエラーメッセージを返します。

    - **DELETE `/todos?id={id}`（IDでTodoを削除）:**
        - 成功時: HTTPステータスコード`200`で削除されたTodoをJSON形式で返します。
        - IDが見つからない場合: HTTPステータスコード`404`でエラーメッセージを返します。
        - エラー時: HTTPステータスコード`500`でエラーメッセージを返します。

3. **アプリコンテナの再起動:**  
   エンドポイントを実装した後、以下のコマンドを実行してアプリコンテナを再起動します：
   ```bash
   docker compose restart app
    ```


## 5. APIのテスト

-    **APIクライアント:** APIクライアント（Postman、Insomniaなど）がインストールされていること。
-    **GET /todos (リスト):** `GET`メソッドを使用して`http://localhost/todos`をテストし、空の配列`[]`が返されることを確認します。
-    **POST /todos (作成):** `POST`メソッドを使用して`http://localhost/todos`をテストし、`{"title": "テストTodo"}`のようなJSONボディを提供します。
    -    新しいアイテムが正しいタイトルでデータベースに作成されたことを確認します。
-    **GET /todos?id={id} (取得):** `GET`メソッドを使用して`http://localhost/todos?id={id}`をテストし、`{id}`を作成済みの`id`に置き換えます。
    -    正しいレコードが返されることを確認します。
-    **PUT /todos?id={id} (更新):** `PUT`メソッドを使用して`http://localhost/todos?id={id}`をテストし、`{id}`を作成済みのidに置き換えます。`title`または`completed`ステータスを更新します。
    -    レコードが正しく更新されることを確認します。
-    **DELETE /todos?id={id} (削除):** `DELETE`メソッドを使用して`http://localhost/todos?id={id}`をテストし、`{id}`を作成済みの`id`に置き換えます。
    -    レコードが正しく削除されることを確認します。

## 6. 開発と反復

-    **PHPコードの変更:** `php/src`ディレクトリ内のファイルを編集します。
-    **コンテナの再起動:** `docker-compose restart app`を使用して、PHPコードを変更した後にコンテナを再起動します。
-    **APIの再テスト:** 変更ごとにAPIクライアントを使用してエンドポイントを再テストします。

## 7. クリーンアップ
-    **コンテナの停止:** `docker-compose stop`コマンドを実行してコンテナを停止します。

## おめでとうございます！

Todoリストアプリのチュートリアルを正常に完了しました！ メインのチュートリアルページにある「さらに探索」セクションを自由に探索して、アプリをさらに改善してください。
