jQuery(document).ready(function($){

    var mediaUploader;

    $('#ywgc-media-upload-button').click(function(e) {
        e.preventDefault();

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: ywgc_data.upload_file_frame_title,
            button: {
                text:  ywgc_data.upload_file_frame_button
            }, multiple: false });


        // Open the uploader dialog
        mediaUploader.open();
    });

});