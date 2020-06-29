(function ($) {

    $(window).on('load', function () {

        if (typeof tinymce === 'undefined') return;

        var editor = tinymce.get('mo_newsletter_editor');

        if (editor) {
            editor.on('keyup change undo redo SetContent Paste', function () {
                editor.save();
                parent.wp.customize.value(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][newsletter_editor_content]').set(
                    editor.getContent()
                );
            });
        }

        $(document).on('change keyup', '#mo_newsletter_editor', _.debounce(function () {
                parent.wp.customize.value(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][newsletter_editor_content]').set(
                    $(this).val()
                );
            }, 300)
        );
    });

})(jQuery);