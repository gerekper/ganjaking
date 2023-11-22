(function ($) {
    $(document).ready(function () {
        $(document).on('click', "input.element-pack-josn-file-upload-file", function (e) {
            e.preventDefault();

            var callbackSelector = $(this).parent().find('.element-pack-json-file-upload-control-hidden-field');

            var media_file = wp.media({
                title: 'Upload JSON',
                // mutiple: true if you want to upload multiple files at once
                multiple: false,
                library: {
                    order: 'DESC',
                    orderby: 'date',
                    type: 'application/json',
                    search: null,
                    uploadedTo: null // wp.media.view.settings.post.id (for current post ID)
                },
            }).open()
                .on('select', function (e) {
                    // This will return the selected image from the Media Uploader, the result is an object
                    var uploaded_json = media_file.state().get('selection').first();
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    // Output to the console uploaded_image
                    var file_url = uploaded_json.toJSON().url;
                    if (file_url) {
                        callbackSelector.val(file_url);
                        callbackSelector.trigger('input')
                    }
                });
        });
    });
})(jQuery);