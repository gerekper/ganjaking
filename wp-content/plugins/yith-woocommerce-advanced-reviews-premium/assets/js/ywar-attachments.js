jQuery(document).ready(function ($) {

    var allowed_extensions = attach.allowed_extensions.replace(' ', '').split(',');

    $('#commentform').attr('enctype', "multipart/form-data");

    $('#do_uploadFile').click(function () {
        $('#ywar-uploadFile').click();
    });

    $('#ywar-uploadFile').on('change', function () {
        $('.ywar-attachment-notice').remove();
        var input = this;

        if ((attach.limit_multiple_upload > 0) && (input.files.length > attach.limit_multiple_upload)) {
            $(this).closest('.upload_section').append('<p class="ywar-attachment-notice">' + attach.too_many_attachment_selected + '</p>');
            input.value = '';
            return;
        }

        var ul = document.getElementById("uploadFileList");
        var preview_image = function (files, index) {
            var oFReader = new FileReader();
            oFReader.readAsDataURL(files[index]);

            oFReader.onload = function (oFREvent) {
                document.getElementById("img_preview" + index).src = oFREvent.target.result;
            };
        };

        while (ul.hasChildNodes()) {
            ul.removeChild(ul.firstChild);
        }

        var errors = 0;
        for (var i = 0; i < input.files.length; i++) {

            /* Check file size and type*/
            if (!file_validation(input.files[i])) {
                errors++;
                continue;
            }

            var li = document.createElement("li");
            li.innerHTML = '<div style="display: inline;"><img id="img_preview' + i + '" style="width: 100px; height: 100px;"></div>';
            preview_image(input.files, i);
            ul.appendChild(li);
        }

        if (errors) {
            $(this).closest('.upload_section').append('<p class="ywar-attachment-notice">' + attach.attachments_failed_validation + '</p>');
        }
        else if (!ul.hasChildNodes()) {
            var li = document.createElement("li");
            li.innerHTML = attach.no_attachment_selected;
            ul.appendChild(li);
        }
    });

    var file_validation = function (file) {
        /**
         * Check if the file extension is accepted
         */
        var lastIndex = file.name.lastIndexOf(".") + 1;
        var ext = file.name.substring(lastIndex).toLowerCase();

        if ($.inArray(ext, allowed_extensions) == -1) {
            return false;
        }

        /**
         * Check if there are limits on file size
         */
        if (attach.allowed_max_size > 0) {
            var filesizeMB = file.size / 1024 / 1024;
            if (filesizeMB > attach.allowed_max_size) {
                return false;
            }
        }
        return true;
    };

    // Prevent submission if limit is exceeded.
    $('#commentform').submit(function () {
        var input = document.getElementById("ywar-uploadFile");
        if ( input != null && (attach.limit_multiple_upload > 0) && (input.files.length > attach.limit_multiple_upload))
            return false;
    });
});