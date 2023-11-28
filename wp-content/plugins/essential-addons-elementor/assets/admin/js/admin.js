jQuery(document).ready(function($) {
    $(".eael-admin-settings-popup").on("click", function(e) {
        e.preventDefault();
        var settings = $(this).data("settings");
        var key = $(this).data("key");
        var title = $(this).data("title");

        swal.fire({
            title: title,
            html:
                '<input type="text" id="' +
                settings +
                '" class="swal2-input" name="' +
                settings +
                '" placeholder="' +
                title +
                '" value="' +
                eaelAdmin[key] +
                '" />',
            closeOnClickOutside: false,
            closeOnEsc: false,
            showCloseButton: true
        }).then(function(result) {
            if (!result.dismiss) {
                $("#" + settings + "-hidden").val($("#" + settings).val());
                $(".js-eael-settings-save")
                    .addClass("save-now")
                    .removeAttr("disabled")
                    .css("cursor", "pointer")
                    .trigger("click");
            }
        });
    });
});
