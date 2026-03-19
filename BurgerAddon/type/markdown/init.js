/// <reference path="../../@types/BgE.d.ts" />
'use strict';

// BurgerEditor の ioFilter は "<" 始まりの値を HTML としてパースするため
// Markdown ソースを base64 エンコードして保存し、読み出し時にデコードする。
function encodeMd(str) {
    if (typeof str !== 'string') { return str; }
    try {
        return btoa(unescape(encodeURIComponent(str)));
    } catch (e) {
        return str;
    }
}

function decodeMd(str) {
    if (typeof str !== 'string' || !str) { return str; }
    try {
        return decodeURIComponent(escape(atob(str)));
    } catch (e) {
        // 旧形式データ（Base64 でない場合）のフォールバック
        return str;
    }
}

function createMarkdownRenderer() {
    var md = window.markdownit({
        html: false,
        linkify: true,
        typographer: true,
        highlight: function (str, lang) {
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
    return md;
}

BgE.registerTypeModule('Markdown', {

    open: function (editorDialog, type) {
        var savedData = type.export();
        var rawMarkdown = decodeMd(savedData['md-source'] || '');

        // textarea に Markdown ソースをセット（jQuery.val() は ioFilter を通らない）
        editorDialog.$el.find('[name="bge-md-source"]').val(rawMarkdown);

        // markdown-it インスタンスを生成（open のたびに新規作成してオプションを固定）
        var md = createMarkdownRenderer();

        var $previewBody = editorDialog.$el.find('.bge-md-preview-body');

        var updatePreview = function () {
            var source = editorDialog.$el.find('[name="bge-md-source"]').val() || '';
            $previewBody.html(md.render(source));
        };

        editorDialog.$el.find('[name="bge-md-source"]').off('input.bgemd').on('input.bgemd', updatePreview);

        // 初回プレビュー描画
        updatePreview();
    },

    beforeChange: function (newValues) {
        // BurgerEditor が DOM に書き込む前に base64 エンコード
        if (typeof newValues['md-source'] === 'string') {
            newValues['md-source'] = encodeMd(newValues['md-source']);
        }
    },

    change: function (value, type) {
        var rawMarkdown = decodeMd(value['md-source'] || '');
        var $wrapper = $(type.el).find('.bge-md-wrapper');

        var md = createMarkdownRenderer();
        $wrapper.html(md.render(rawMarkdown));
    },

});
