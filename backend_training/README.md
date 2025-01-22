# Todoリストアプリチュートリアル チェックリスト

このチェックリストを使って、Todoリストアプリの構築中の進捗状況を追跡してください。

## 1. 環境設定

-    **Dockerのインストール:** Dockerがインストールされ、実行されていること。([https://www.docker.com/get-started/](https://www.docker.com/get-started/))
-    **Docker Composeのインストール:** Docker Composeがインストールされていること。([https://docs.docker.com/compose/install/](https://docs.docker.com/compose/install/))
-    **コードエディタ/IDEのインストール:** テキストエディタまたはIDE（例: VS Code）がインストールされていること。
-    **プロジェクトファイル:** `docker-compose.yml`、`php/Dockerfile`、`php/src/`、および`nginx/default.conf`ファイルがあることを確認してください。
-    **プロジェクトへの移動:** ターミナルを開き、`cd backend_tranning/`を使ってプロジェクトのルートディレクトリに移動してください。

## 2. Docker Composeの設定

-    **Dockerコンテナの起動:** `docker-compose up -d`コマンドを実行してコンテナを起動します。
    -    `docker ps`またはDocker Desktopを使って、コンテナが実行中であることを確認します。

## 3. データベースの設定

-    **データベースクライアント:** PostgreSQLデータベースクライアント（`pgAdmin`、`DBeaver`など）がインストールされていること。
-    **データベースへの接続:** `docker-compose.yml`ファイルにある以下の接続情報を使って、PostgreSQLデータベースに正常に接続します。
    -   ホスト: `localhost`
    -   ポート: `5432`
    -   データベース: `mydatabase`
    -   ユーザー名: `myuser`
    -   パスワード: `mypassword`
-    **`todos`テーブルの作成:** データベースクライアントで次のSQLクエリを実行して、`todos`テーブルを作成します。
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
