# フロントエンド課題 「TODOリストを作ろう」

## ⚒️ 環境構築
前提として PC に Node.js がインストールされている必要があります。公式サイトの手順を参考にインストールを行なってください。

https://nodejs.org/ja/download

以下のコマンドを実行して Node.js の バージョンが表示されれば Node.js が有効になっており、課題を進めるための環境が整っている状態です。

```bash
node -v

v20.9.0 <- nodeのバージョンが表示されればOK
```

この課題ではpnpmを使いますが、npmやyarnなど他のパッケージマネージャーを使用していただいてもOKです。
Node.js インストール後に自動的に npm が有効になります。pnpm をインストールする場合は公式サイトを参考にインストールを行なってください。

https://pnpm.io/ja/installation

## 🚀 プロジェクトを起動する
以下のコマンドを実行してプロジェクトを起動してください。

```bash
pnpm i
pnpm dev
```

プロジェクトを起動後に表示されるURLにアクセスしてアプリケーションが動いていることを確認してください。

http://localhost:5173/


## 📚 ソースコードを眺める
### プロジェクト構成
このプロジェクトは vite というビルドツールを使用して作成されました。
自身で新規のプロジェクトを作る場合は以下のコマンドを実行して作成できます。

```bash
pnpm create vite
```

<details>

<summary>今回のプロジェクトの設定</summary>

```bash
? Project name: <- 任意のプロジェクト名を入力

? Select a framework: › - Use arrow-keys. Return to submit.

    Vanilla
    Vue
❯   React  <- 矢印キーで移動してReactを選択してEnter
    Preact
    Lit
    Svelte
    Solid
    Qwik
    Angular
    Others

? Select a variant: › - Use arrow-keys. Return to submit.
    TypeScript
❯   TypeScript + SWC  <- 矢印キーで移動して TypeScript + SWC を選択してEnter
    JavaScript
    JavaScript + SWC
    React Router v7 ↗
```

</details>

pnpm 以外のパッケージマネージャーで作成する場合は[公式ドキュメント](https://ja.vite.dev/guide/#%E6%9C%80%E5%88%9D%E3%81%AE-vite-%E3%83%95%E3%82%9A%E3%83%AD%E3%82%B7%E3%82%99%E3%82%A7%E3%82%AF%E3%83%88%E3%82%92%E7%94%9F%E6%88%90%E3%81%99%E3%82%8B)をご覧ください。

### srcディレクトリ
今回課題を進めていくために frontend_training/src ディレクトリで作業を行います。主に触るファイルは App.tsx と App.css です。

App.tsxでReactのコンポーネントを作り、コンポーネントのスタイリングをCSSによりApp.cssで行います。

具体的なReactのコンポーネントの作成方法は[公式のドキュメント](https://ja.react.dev/learn#components)をご覧ください。

## ✍️ 課題を始めよう 

このTODOリストを作成するチュートリアルは以下のステップで進めていきます。

1. TODOを追加するための入力フォームとTODO追加ボタンを追加
2. Stateを用意して追加したTODOをリストで表示する
3. リストで表示しているTODOを削除できるようにする
4. リストで表示しているTODOを編集できるようにする
5. TODOリストの内容をバックエンドに保存する

### 1️⃣ TODOを追加するための入力フォームとTODO追加ボタンを追加する

**タスク:**

TODOを追加するための入力欄と追加ボタンを作成してください

![スクリーンショット 0007-02-04 18 38 16](https://github.com/user-attachments/assets/dac516ff-2a9b-4b33-b1b5-573f28b76810)

**ヒント:**

- App.tsx に input と button を配置します
- App.css でスタイルを調整します

### 2️⃣ Stateを用意してTODOを追加し表示できるようにする

**タスク:**

追加ボタンを押した際に入力欄の下に追加したTODOがリストで表示されるようにしてください

https://github.com/user-attachments/assets/425a3106-7522-411b-b383-e136b9f6ec9b

**ヒント:**

- 入力欄で入力された文字を保持するためにReactのStateを使います
- 入力欄で入力された文字をStateにセットします
- 追加されたTODOを保持し一覧で表示するためにStateを使います
- 追加ボタンを押した際に入力欄で入力されているTODOをStateにセットします

**>>>>>>>>>>>TODO<<<<<<<< 解説:**

### 3️⃣ リストで表示しているTODOを完了できるようにする

**タスク:**

入力欄の下にリストで表示しているそれぞれのTODOに完了ボタンを付けて、完了ボタンを押すとそのTODOが消えるようにしてください

https://github.com/user-attachments/assets/64012c8c-a905-48c8-8db7-2b5c5f0e8ed4

**ヒント:**

- idを生成するパッケージである[uuid](https://github.com/uuidjs/uuid)をプロジェクトに追加し、TODOリストの各TODOに一意のidを付与します
- TODOリストのデータを保存しているStateにTODOが完了しているかどうかを示すisCompleteというフラグを追加します
- 完了ボタンが押された際に、完了ボタンを押したTODOのisCompleteの値をtrueにします
- Javascriptのfilter関数を使って完了していないTODOのみ表示するようにします

### 4️⃣ リストで表示しているTODOを編集できるようにする

**タスク:**

TODOリストのTODOをクリックすると表示されているTODOが入力欄に変わり、TODOの内容を更新できるようにしてください。入力欄の横には更新ボタンとキャンセルボタンを用意し、更新ボタンを押すと編集用の入力欄で入力された値でTODOが更新されるようにし、キャンセルボタンを押すと更新せずに元々の値を表示するようにしてください。

https://github.com/user-attachments/assets/be9bd46f-7731-4007-94a1-aa906a474ff4

**ヒント:**

- TODOリストのデータを保存しているStateに対して編集する必要があるTODOを特定するためにisEditというフラグを追加します
- isEditがtrueのTODOの時にTODO編集用のコンポーネントを表示するようにします
- 可読性のために編集用のコンポーネントを別のコンポーネントに切り出します
- isEditで編集用の入力欄で入力値を保持するために新規で編集用のStateを作成します


### TODOリストの内容をバックエンドに保存する

