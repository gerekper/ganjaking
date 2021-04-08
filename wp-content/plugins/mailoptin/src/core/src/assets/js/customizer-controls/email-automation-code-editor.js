(function ($) {

    /**
     * @var {object} moEmailCodeEditor_strings
     */
    var add_toolbar = function () {
        var title = $('.panel-title.site-title').text();
        var markup = [
            '<div class="mo-automation-code-toolbar">',
            '<div class="mo-automation-code-toolbar-left">',
            '<a href="#" onclick="parent.wp.customize.section(\'mailoptin_campaign_view_tags\').focus()" class="mo-automation-code-view-tags">' + moEmailCodeEditor_strings.viewTags + ' <span class="dashicons dashicons-editor-help"></span></a>',
            '</div>',
            '<div class="mo-automation-code-toolbar-center"><span class="mo-automation-code-title">' + title + '</span></div>',
            '<div class="mo-automation-code-toolbar-right">',
            '<a href="#" class="mo-automation-code-toolbar-btn mo-preview">',
            '<span class="dashicons dashicons-visibility"></span>',
            '<span class="text">' + moEmailCodeEditor_strings.previewBtn + '</span>',
            '</a>',
            '<a href="#" class="mo-automation-code-toolbar-btn mo-code-editor btn-active">',
            '<span class="dashicons dashicons-editor-code"></span>',
            moEmailCodeEditor_strings.codeEditorBtn,
            '</a>',
            '</div>',
            '</div>'
        ].join('');

        $('#customize-preview').append(markup);
    };

    var add_ace_editor = function () {
        var markup = [
            '<div class="mo-email-automation-editor-wrap">',
            '<div id="mo-email-automation-editor"></div>',
            '</div>'
        ].join('');

        $('#customize-preview').append(markup);

        var editor = ace.edit('mo-email-automation-editor');
        editor.setTheme("ace/theme/monokai");
        var session = editor.getSession();
        // disable syntax checker https://stackoverflow.com/a/13016089/2648410
        session.setUseWorker(false);
        session.setMode("ace/mode/html");
        editor.$blockScrolling = Infinity;
        var textarea_val = $("input[data-customize-setting-link*='[code_your_own]']").val();
        if (textarea_val) {
            session.setValue(textarea_val);
        }
        editor.on('change', function () {
            $("input[data-customize-setting-link*='[code_your_own]']").val(session.getValue()).change();
        });

        // remove the temporary iframe hide css.
        $('#customize-preview-iframe-hide').remove();
    };

    var preview_screen = function () {
        return [
            '<div class="mo-code-editor-preview-loader">',
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" role="img" aria-hidden="true" focusable="false"><path class="outer" d="M48 12c19.9 0 36 16.1 36 36S67.9 84 48 84 12 67.9 12 48s16.1-36 36-36" fill="none"></path><path class="inner" d="M69.5 46.4c0-3.9-1.4-6.7-2.6-8.8-1.6-2.6-3.1-4.9-3.1-7.5 0-2.9 2.2-5.7 5.4-5.7h.4C63.9 19.2 56.4 16 48 16c-11.2 0-21 5.7-26.7 14.4h2.1c3.3 0 8.5-.4 8.5-.4 1.7-.1 1.9 2.4.2 2.6 0 0-1.7.2-3.7.3L40 67.5l7-20.9L42 33c-1.7-.1-3.3-.3-3.3-.3-1.7-.1-1.5-2.7.2-2.6 0 0 5.3.4 8.4.4 3.3 0 8.5-.4 8.5-.4 1.7-.1 1.9 2.4.2 2.6 0 0-1.7.2-3.7.3l11.5 34.3 3.3-10.4c1.6-4.5 2.4-7.8 2.4-10.5zM16.1 48c0 12.6 7.3 23.5 18 28.7L18.8 35c-1.7 4-2.7 8.4-2.7 13zm32.5 2.8L39 78.6c2.9.8 5.9 1.3 9 1.3 3.7 0 7.3-.6 10.6-1.8-.1-.1-.2-.3-.2-.4l-9.8-26.9zM76.2 36c0 3.2-.6 6.9-2.4 11.4L64 75.6c9.5-5.5 15.9-15.8 15.9-27.6 0-5.5-1.4-10.8-3.9-15.3.1 1 .2 2.1.2 3.3z" fill="none"></path></svg>',
            '</div>',
            '<style>.mo-code-editor-preview-loader{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;width:100vw}@-webkit-keyframes paint{0%{stroke-dashoffset:0}}@-moz-keyframes paint{0%{stroke-dashoffset:0}}@-o-keyframes paint{0%{stroke-dashoffset:0}}@keyframes paint{0%{stroke-dashoffset:0}}.mo-code-editor-preview-loader svg{width:192px;height:192px;stroke:#555d66;stroke-width:.75}.mo-code-editor-preview-loader svg .inner,.mo-code-editor-preview-loader svg .outer{stroke-dasharray:280;stroke-dashoffset:280;-webkit-animation:paint 1.5s ease infinite alternate;-moz-animation:paint 1.5s ease infinite alternate;-o-animation:paint 1.5s ease infinite alternate;animation:paint 1.5s ease infinite alternate}p{text-align:center;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif}</style>'
        ].join('');
    };

    var update_email_title = function () {
        $('input[id*="email_campaign_title"]').on('change keyup', function () {
            $('.mo-automation-code-title').text(this.value);
        });
    };

    var switch_view = function () {
        $(document).on('click', '.mo-automation-code-toolbar-btn', function (e) {
            e.preventDefault();
            $('.mo-automation-code-toolbar-btn').removeClass('btn-active');
            $(this).addClass('btn-active');
            var cache = $('#customize-preview iframe');
            if ($(this).hasClass('mo-preview')) {
                wp.customize.previewer.refresh();
                var iframe = cache[0];
                iframe.contentWindow.document.open();
                iframe.contentWindow.document.write(preview_screen());
                iframe.contentWindow.document.close();
                $('.mo-email-automation-editor-wrap').hide();
                cache.show();
            } else {
                cache.hide();
                $('.mo-email-automation-editor-wrap').show();
            }
        });
    };

    $(function () {
        var css = '<style id="customize-preview-iframe-hide" type="text/css">#customize-preview iframe {display:none;}</style>';
        $(document.head).append(css);
    });

    $(window).on('load', function () {
        setTimeout(function () {
            if (mailoptin_email_campaign_is_code_your_own === false) return;
            add_toolbar();
            add_ace_editor();
            switch_view();
            update_email_title();
        }, 1000);
    });
})(jQuery);