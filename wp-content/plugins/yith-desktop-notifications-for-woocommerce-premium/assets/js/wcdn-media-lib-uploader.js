jQuery(document).ready(function($){

    var custom_uploader;
    var custom_uploader_audio;


    $('#upload_image_button').click(function(e) {
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#upload_image').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();

    });

    $('#upload_audio_button').click(function(e) {
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader_audio) {
            custom_uploader_audio.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader_audio = wp.media.frames.file_frame = wp.media({
            title: 'Choose Audio',
            button: {
                text: 'Choose Audio'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader_audio.on('select', function() {
            attachment = custom_uploader_audio.state().get('selection').first().toJSON();
            $('#upload_audio').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader_audio.open();

    });

});
