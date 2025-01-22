## 0. 課題説明
TODO リストアプリを作成するためのバックエンド開発トレーニングを開始します。このトレーニングでは、PHPとPostgreSQLを使用して、RESTful APIを作成し、データベースとのやり取りを行います。このトレーニングを完了すると、PHPを使用してデータベースと通信する方法を学び、APIエンドポイントを作成する方法を理解することができます。
backend trainingの目標は以下の通りです。
- PHPを使用してRESTful APIを作成する。
- PostgreSQLデータベースとの通信を設定する。
- APIエンドポイントを作成し、データベースとのやり取りを行う。 
- APIエンドポイントをテストし、データベースとの通信を確認する。

構成図：
```mermaid
graph LR
    N[Nginx] -- リクエスト転送 --> A[PHP]
    A <-- データベース操作 --> B[PostgreSQL]
    A -- 応答 --> N
```

## 1. 環境設定

-    **Dockerのインストール:** Dockerがインストールされ、実行されていること。([https://www.docker.com/get-started/](https://www.docker.com/get-started/))
-    **Docker Composeのインストール:** Docker Composeがインストールされていること。([https://docs.docker.com/compose/install/](https://docs.docker.com/compose/install/))
-    **コードエディタ/IDEのインストール:** テキストエディタまたはIDE（例: VS Code）がインストールされていること。
-    **プロジェクトへの移動:** ターミナルを開き、`cd backend_tranning/`を使ってプロジェクトのルートディレクトリに移動してください。
-    **プロジェクトファイル:** `compose.yml`、`app/Dockerfile`、`app/src/`、および`nginx/default.conf`ファイルがあることを確認してください。

## 2. Docker Composeの設定

-    **Dockerコンテナの起動:** `docker-compose up -d`コマンドを実行してコンテナを起動します。 
- `docker ps`またはDocker Desktopを使って、コンテナが実行中であることを確認します。

## 3. データベースの設定
- 
```mermaid
erDiagram
    TODOS {
        INT id PK "Serial Primary Key"
        VARCHAR title "Title of the task (max 255 characters, not null)"
        BOOLEAN completed "Task status (default false)"
    }
```
### 3.1.A. データベースクライアントで接続する場合
-    **データベースクライアント:** PostgreSQLデータベースクライアント（`pgAdmin`、`DBeaver`など）がインストールされていること。
-    **データベースへの接続:** `compose.yml`ファイルにある以下の接続情報を使って、PostgreSQLデータベースに正常に接続します。
      ```sql
        DATABASE: prtimes
        PORT: 5432
        USERNAME: prtimes
        PASSWORD: prtimes
      ```
### 3.1.B. dockerコンテナ内で接続する場合
-    **`app`コンテナに接続:** `docker exec -it app bash`を使って`app`コンテナに接続します。
-   **`psql`コマンド:** `psql -U prtimes -d prtimes`を使って、PostgreSQLデータベースに接続します。

### 3.2 テーブルの作成
-    **SQLクエリの実行:** 以下のSQLクエリを実行して、`todos`テーブルを作成します。
```sql
CREATE TABLE todos (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    completed BOOLEAN DEFAULT FALSE
);
```

## 4. PHP APIの開発

-    **`config.php`の作成:** `php/src`ディレクトリにデータベース設定を含む`config.php`を作成します。
-    **`index.php`の作成:** `php/src`ディレクトリに`index.php`ファイルを作成します。
    -    `/todos`エンドポイントへのさまざまなリクエストタイプ（GET、POST、PUT、DELETE）を処理するコード。
    -    リクエストに応じて異なるレスポンスを処理するコード:
        -    すべてのTodoを返す`GET /todos`エンドポイントを実装。
             - データベースからすべてのTodoを取得し、JSON形式で返します。
             - データベースから取得したTodoが空の場合は、空の配列`[]`を返します。
             - データベースから取得したTodoがある場合は、JSON形式で返します。
             - すべてのTodoが正常に返されることを確認します。
        -    IDで特定のTodoを返す`GET /todos?id={id}`エンドポイントを実装。
        -    新しいTodoを作成する`POST /todos`エンドポイントを実装。
        -    Todoを更新する`PUT /todos?id={id}`エンドポイントを実装。
        -    IDでTodoを削除する`DELETE /todos?id={id}`エンドポイントを実装。
        -    APIが`Content-Type: application/json`ヘッダーを使ってJSONデータを出力することを確認します。
        -   各APIエンドポイントに適したエラー状態コードを返すエラー処理を実装します。
-    **`app`サービスの再起動:** `docker-compose restart app`を使って`app`サービスを再起動します。

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
