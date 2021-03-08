(function ($) {
    $(document).on('tinymce-editor-setup', function (event, editor) {

        if (void 0 === wyt_shortcodes) {
            console.log('Unknown error (wyt).');
            return;
        }

        if ($.isEmptyObject(wyt_shortcodes)) {
            return;
        }

        editor.settings.toolbar1 += ',wyt_insert_button';

        var menu = [];

        $.each(wyt_shortcodes, function (index, item) {
            menu.push({
                text: item.title,
                value: item.id,
                onclick: function () {
                    var selected_content = editor.selection.getContent();

                    if ('' === selected_content) {
                        editor.selection.setContent('[cm_youtube_feed id="' + item.id + '"]');
                    } else {
                        editor.selection.setContent('[cm_youtube_feed id="' + item.id + '"]');
                    }
                }
            });
        });

        editor.addButton('wyt_insert_button', {
            title: 'Youtube Feed',
            type: 'menubutton',
            icon: 'icon wyt-shortcode-icon',
            menu: menu
        });

    });
})(jQuery);
