(function ($) {

    /**
     * TIPS
     */
    var tiptip_args = {
        'attribute': 'data-tip',
        'fadeIn'   : 50,
        'fadeOut'  : 50,
        'delay'    : 200
    };

    $('.ylc-tips').tipTip(tiptip_args);

    /**
     * OPERATOR AVATAR MANAGEMENT
     */
    if (typeof wp !== 'undefined' && typeof wp.media !== 'undefined') {

        //upload
        var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;

        // preview
        $('#ylc_operator_avatar').change(function () {

            var option = $('option:selected', '#ylc_operator_avatar_type').val();

            if (option === 'image') {

                var url = $(this).val();
                var re = new RegExp("(http|ftp|https)://[a-zA-Z0-9@?^=%&amp;:/~+#-_.]*.(gif|jpg|jpeg|png|ico)");

                var preview = $('.ylc-op-avatar .preview img');
                if (re.test(url)) {
                    preview.attr('src', url)

                } else {
                    preview.attr('src', '');
                }

            }

        }).change();

        $(document).on('click', '#ylc_operator_avatar_button', function () {
            var button = $('#ylc_operator_avatar_button');
            var field = $('#ylc_operator_avatar');
            var preview = $('.ylc-op-avatar .preview img');
            _custom_media = true;

            wp.media.editor.send.attachment = function (props, attachment) {
                if (_custom_media) {

                    field.val(attachment.url);
                    preview.attr('src', attachment.url).change();

                } else {

                    return _orig_send_attachment.apply(this, [props, attachment]);

                }

            };

            wp.media.editor.open(button);
            return false;
        });

    }

    $('.ylc-op-avatar .add_media').on('click', function () {
        _custom_media = false;
    });

    $('#ylc_operator_avatar_type').change(function () {

        var option = $('option:selected', this).val(),
            img = $('.avatar .preview img'),
            uploader = $('.avatar .upload');

        switch (option) {
            case 'image':
                uploader.show();
                img.attr('src', $('#ylc_image').val());
                break;

            case 'gravatar':
                uploader.hide();
                img.attr('src', $('#ylc_gravatar').val());
                break;

            default:
                uploader.hide();
                img.attr('src', $('#ylc_default').val());

        }

    }).change();

    $(document).on('click', '#cancel-reply', function (e) {
        e.preventDefault();

        tinyMCE.activeEditor.setContent('');

    });

    $(document).on('click', '#send-reply', function (e) {
        e.preventDefault();


    });



})(jQuery);
