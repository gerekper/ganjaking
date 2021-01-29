(function (factory) {
    "use strict";
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    }
    else if(typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'));
    }
    else {
        factory(jQuery);
    }
}(function ($, undefined) {
    $(document).ready(function (){
        // Open file selector on div click
        $("#upload-file").click(function(){
            $("#media_file").click();
        });

        // file selected
        $("#media_file").change(function(){
            var fd = new FormData();

            var files = $('#media_file')[0].files[0];

            fd.append('media_file',files);

            uploadData(this);
        });
    });

    function uploadData(input) {
        if($("#media_file").val() != "") {

            var fileName = $("#media_file").val();
            fileName = fileName.toLowerCase();
            fileName = fileName.split(".");
            var fileExt = fileName[fileName.length - 1];
            if(fileExt == "jpg" || fileExt == "png" || fileExt == "jpeg" || fileExt == "gif") {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(".drag-and-drop-title").html("<img class='pre-image' id='pre-image' >");
                        $('#pre-image').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            } else {
                $(".drag-and-drop-title").html('<span class="dashicons dashicons-media-document"></span>');
            }

            setFileSize(input.files[0].size);

            $(".replace-message").removeClass("active");
            if(fileExt != $("#file_ext").val()) {
                $(".file-type").addClass("active");
                $("#rename-file").prop("checked", true);
            } else {
                $("#replace-file").prop("checked", true);
            }
            $(".button-primary").prop("disabled", false);
        } else {
            $(".button-primary").prop("disabled", true);
        }
    }

    function setFileSize(fileSize) {
        fileSize = parseInt(fileSize);
        if(fileSize > 1000000) {
            fileSize = parseFloat(fileSize/1000000).toFixed(2)+" MB";
        } else if(fileSize > 1000) {
            fileSize = parseFloat(fileSize/1000).toFixed(2)+" KB";
        } else {
            fileSize = fileSize+" B";
        }
        $(".new-image-box .file-size").html(fileSize);
    }

}));