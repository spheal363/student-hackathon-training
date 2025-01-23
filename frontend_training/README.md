# フロントエンド課題 TODOリストを作ろう

## 環境構築
前提として PC に Node.js がインストールされている必要があります。公式サイトの手順を参考にインストールを行なってください。

https://nodejs.org/ja/download

以下のコマンドを実行して Node.js の バージョンが表示されれば Node.js が有効になっており、課題を進めるための環境が整っている状態です。

```bash
node -v

v20.9.0 <- nodeのバージョンが表示されればOK
```

この課題ではpnpmを使いますが、npmやyarnなど他のパッケージマネージャーを使用していただいてもOKです。

## プロジェクトを起動する
以下のコマンドを実行してプロジェクトを起動してください。

```bash
pnpm i
pnpm dev
```

## ソースコードを眺める
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

pnpm 以外のパッケージマネージャーで作成する場合は以下の公式ドキュメントをご覧ください。

https://ja.vite.dev/guide/#%E6%9C%80%E5%88%9D%E3%81%AE-vite-%E3%83%95%E3%82%9A%E3%83%AD%E3%82%B7%E3%82%99%E3%82%A7%E3%82%AF%E3%83%88%E3%82%92%E7%94%9F%E6%88%90%E3%81%99%E3%82%8B


