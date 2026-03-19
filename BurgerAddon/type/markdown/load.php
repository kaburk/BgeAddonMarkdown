<?php
$this->BcBaser->css('BgeAddonMarkdown.vendor/github-markdown.min', false);
$this->BcBaser->css('BgeAddonMarkdown.bge-addon-markdown', false);
$this->BcBaser->js('BgeAddonMarkdown.vendor/markdown-it.min', false);
$this->BcBaser->js('BgeAddonMarkdown.vendor/highlight.min', false);

// PHP で CSS URL を直接生成して JS に渡す（ロード順序に依存しない）
$pluginPath = \Cake\Utility\Inflector::underscore('BgeAddonMarkdown');
$addonCssUrl = $this->BcBaser->getUrl('/' . $pluginPath . '/css/bge-addon-markdown.css');
$githubMdCssUrl = $this->BcBaser->getUrl('/' . $pluginPath . '/css/vendor/github-markdown.min.css');
?>
<script>
(function () {
    var ADDON_CSS_HREFS = [
        <?= json_encode($addonCssUrl) ?>,
        <?= json_encode($githubMdCssUrl) ?>
    ];

    function injectStylesIntoFrameDoc(doc) {
        if (!doc || !doc.head) {
            return;
        }
        ADDON_CSS_HREFS.forEach(function (href) {
            if (!href) {
                return;
            }
            var filename = href.replace(/^.*\//, '');
            if (doc.querySelector('link[href*="' + filename + '"]')) {
                return;
            }
            var link = doc.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            doc.head.appendChild(link);
        });
    }

    function tryInjectIntoFrame(frame) {
        var doc;
        try {
            doc = frame.contentWindow && frame.contentWindow.document;
        } catch (e) {
            return;
        }
        if (!doc || !doc.head) {
            return;
        }
        var hasBgeStyle = doc.querySelector(
            'link[href*="bge_style_default.css"], link[href*="bge_style.css"], link[href*="burger_editor.css"]'
        );
        if (hasBgeStyle) {
            injectStylesIntoFrameDoc(doc);
        }
    }

    function watchFrame(frame) {
        var doc;
        try {
            doc = frame.contentWindow && frame.contentWindow.document;
        } catch (e) {
            return;
        }
        var head = (doc && (doc.head || doc.documentElement)) || frame;
        var frameObserver = new MutationObserver(function () {
            try {
                tryInjectIntoFrame(frame);
            } catch (e) {}
        });
        frameObserver.observe(head, { childList: true, subtree: true });
        tryInjectIntoFrame(frame);
    }

    var outerObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType !== 1) { return; }
                if (node.tagName === 'IFRAME') {
                    watchFrame(node);
                }
                if (node.querySelectorAll) {
                    node.querySelectorAll('iframe').forEach(watchFrame);
                }
            });
        });
    });

    outerObserver.observe(document.documentElement, {
        childList: true,
        subtree: true
    });

    document.querySelectorAll('iframe').forEach(watchFrame);
})();
</script>
