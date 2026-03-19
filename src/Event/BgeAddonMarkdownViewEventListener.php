<?php
declare(strict_types=1);
/**
 * BgeAddonMarkdown
 *
 * @copyright     Copyright (c) kaburk
 * @link          https://github.com/kaburk/BgeAddonMarkdown
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace BgeAddonMarkdown\Event;

use BaserCore\Event\BcViewEventListener;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use Cake\View\View;

/**
 * BgeAddonMarkdown View Event Listener
 *
 * フロントエンドに markdown-it.js と github-markdown-css を注入する
 */
class BgeAddonMarkdownViewEventListener extends BcViewEventListener
{

    /**
     * 登録イベント
     *
     * @var array
     */
    public $events = [
        'beforeLayout',
    ];

    /**
     * beforeLayout
     *
     * フロントエンドのページに Markdown レンダリング関連ファイルを読み込む
     *
     * @param EventInterface $event
     * @return void
     */
    public function beforeLayout(EventInterface $event)
    {
        /** @var View $View */
        $View = $event->getSubject();

        // 管理画面は除外（管理画面向けは load.php が担当）
        if (BcUtil::isAdminSystem()) {
            return;
        }

        // メール・RSS等のレイアウトは除外
        $excludeViewPath = [
            'email/text',
            'Blog/rss',
            'Feed',
        ];
        if (in_array($View->getTemplatePath(), $excludeViewPath)) {
            return;
        }

        $View->BcBaser->css(
            'BgeAddonMarkdown.vendor/github-markdown.min',
            false
        );
        $View->BcBaser->css(
            'BgeAddonMarkdown.bge-addon-markdown',
            false
        );

        // markdown-it は常にロード
        $View->BcBaser->js(
            'BgeAddonMarkdown.vendor/markdown-it.min',
            false,
            ['defer' => 'defer']
        );

        // highlight.js は BgeAddonSyntaxHighlight が読み込んでいない場合のみロード
        // フロントエンド初期化スクリプト内で window.hljs の存在を確認して重複を防ぐ
        $View->BcBaser->js(
            'BgeAddonMarkdown.vendor/highlight.min',
            false,
            ['defer' => 'defer', 'id' => 'bge-addon-markdown-hljs']
        );

        // フロントエンド初期化インラインスクリプト
        $initScript = <<<'JS'
document.addEventListener('DOMContentLoaded', function() {
    if (typeof markdownit === 'undefined') return;

    var md = markdownit({
        html: false,
        linkify: true,
        typographer: true,
        highlight: function(str, lang) {
            if (lang && typeof hljs !== 'undefined' && hljs.getLanguage(lang)) {
                try {
                    return '<pre class="hljs"><code class="language-' + lang + '">' +
                        hljs.highlight(str, { language: lang, ignoreIllegals: true }).value +
                        '</code></pre>';
                } catch (e) {}
            }
            return '<pre class="hljs"><code>' +
                md.utils.escapeHtml(str) +
                '</code></pre>';
        }
    });

    document.querySelectorAll('[data-bgb="markdown"] .bge-md-wrapper[data-md-source]').forEach(function(el) {
        var encoded = el.getAttribute('data-md-source') || '';
        if (!encoded) return;
        var decoded;
        try {
            decoded = decodeURIComponent(escape(atob(encoded)));
        } catch (e) {
            decoded = encoded;
        }
        el.innerHTML = md.render(decoded);
    });
});
JS;
        $View->append('script', '<script>' . $initScript . '</script>');
    }

}
