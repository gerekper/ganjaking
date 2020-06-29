(function ($) {

    var steps               = new Array( 'login', 'billing', 'shipping', 'order', 'payment'),
        reset_button_id     = '#yith_wcms_remove_image_button_',
        reset_button_class  = '.yith_wcms_remove_image_button';

    for (var k in steps) {
        var reset_button = $( reset_button_id + steps[k]);
        if (reset_button.data('default') == 'yes') {
            reset_button.hide();
        }
    }

    // Uploading files
    var file_frame = new Array();

    $(document).on('click', '.yith_wcms_upload_image_button', function (event) {

        event.preventDefault();

        var t            = $(this),
            current_step = t.data('step');

        console.log(current_step);

        // If the media frame already exists, reopen it.
        if (file_frame[current_step]) {
            file_frame[current_step].open();
            return;
        }

        // Create the media frame.
        file_frame[current_step] = wp.media.frames.downloadable_file = wp.media({
            title   : 'Choose an image',
            button  : {
                text: 'Use image'
            },
            multiple: false
        });

        // When an image is selected, run a callback.
        file_frame[current_step].on('select', function () {
            attachment = file_frame[current_step].state().get('selection').first().toJSON();
            $('#yith_wcms_timeline_options_icon_' + current_step ).val(attachment.id);
            $('#yith_wcms_image_wrapper_id_' + current_step + ' img').attr('src', attachment.sizes.thumbnail.url);
            $(reset_button_id + current_step ).show();
        });

        // Finally, open the modal.
        file_frame[current_step].open();
    });

    $(document).on('click', reset_button_class, function (event) {
        var step = $(this).data('step');
        $('#yith_wcms_image_wrapper_id_' + step + ' img').attr('src', yith_wcms[step]);
        $('#yith_wcms_timeline_options_icon_' +step).val('');
        $(reset_button_id + step).hide();
        return false;
    });
})(jQuery);