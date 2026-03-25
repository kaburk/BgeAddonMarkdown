# BgeAddonMarkdown

BurgerEditor の Addon プラグイン。Markdown 形式でコンテンツを記述し、フロントエンドで GitHub スタイルの美しい HTML としてレンダリングします。

## 機能

- Markdown 形式で記述したコンテンツをブロックとして挿入
- 管理画面での **左右分割エディタ**（左：Markdown 入力 / 右：リアルタイムプレビュー）
- GitHub スタイルの CSS（github-markdown-css）によるきれいな表示
- コードブロック（` ``` `）への **シンタックスハイライト**（highlight.js）
- XSS 安全（markdown-it のデフォルト `html: false`）

## 必要要件

- baserCMS 5.x
- BurgerEditor プラグイン

## インストール

```bash
bin/cake plugin load BgeAddonMarkdown
```

## 使い方

1. BurgerEditor のブロック選択パネルから「Markdown」カテゴリを選択
2. Markdown ブロックを挿入
3. 左ペインに Markdown テキストを入力 → 右ペインでリアルタイムプレビューを確認
4. 保存すると、フロントエンドで HTML としてレンダリングされます

## 対応 Markdown 記法

- 見出し（H1〜H6）
- 太字・斜体・打ち消し線
- 順序付き・順序なしリスト
- テーブル
- コードブロック（言語指定によるシンタックスハイライト）
- インラインコード
- リンク
- 画像
- 引用
- 水平線

## 構成

plugins/BgeAddonMarkdown/
├── config.php / config/setting.php     ← プラグイン登録
├── src/
│   ├── BgeAddonMarkdownPlugin.php      ← BcPlugin 継承
│   └── Event/BgeAddonMarkdownViewEventListener.php  ← フロント JS/CSS 自動注入
├── BurgerAddon/
│   ├── block/category.php              ← 「Markdown」カテゴリ
│   └── block/markdown/                 ← ブロック定義 + panel.svg
│   └── type/markdown/
│       ├── init.js                     ← BgE.registerTypeModule('Markdown', ...)
│       ├── init.php / load.php         ← JS 初期化 + 管理画面アセット
│       ├── input.php                   ← 左右分割エディタ UI
│       └── value.php                   ← data-bge="md-source:data-md-source"
└── webroot/
    ├── css/bge-addon-markdown.css      ← 分割エディタ + フロントスタイル
    ├── css/vendor/github-markdown.min.css  (19KB)
    └── js/vendor/markdown-it.min.js (121KB) + highlight.min.js (125KB)

## ライセンス

MIT License

## 作者

kaburk
