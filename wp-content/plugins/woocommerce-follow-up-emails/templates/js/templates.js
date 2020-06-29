jQuery(document).ready(function($) {
    $(".edit-html").click(function(e) {
        e.preventDefault();

        var parent_div = $(this).parents("div").eq(0);
        parent_div.block({ message: null, overlayCSS: { background: '#fff url('+ FUE_Templates.ajax_loader +') no-repeat center', opacity: 0.6 } });
        var that = this;

        $.get( ajaxurl, {
            action: "fue_load_template_source",
            template: $(that).data("template"),
            security: FUE_Templates.get_template_nonce
        }, function(src) {
            parent_div.unblock();

            if ( src.indexOf("Error:") == 0 ) {
                alert( src );
                return false;
            }

            $("ul.fue-templates").slideUp(function() {
                $("#template_editor").slideDown();
            });

            $("#current_template").val( $(that).data("template") );
            $("#editor").val(src);

        });

    });

    $(".edit-html-close").click(function() {
        $("#template_editor").slideUp(function() {
            $("ul.fue-templates").slideDown();
        });

        $("#current_template").val("");
        $("#editor").val("");
    });

    $(".edit-html-save").click(function() {
        var source = $("#editor").val()

        $(".edit-html-spinner").css({
            display: "inline-block",
            visibility: "visible"
        });

        $(".edit-html-status")
            .html("")
            .removeClass("updated error");

        $.post(ajaxurl, {
            action: "fue_save_template_source",
            template: $("#current_template").val(),
            security: FUE_Templates.save_template_nonce,
            source: source
        }, function(resp) {
            if ( resp.status == "ERROR" ) {
                $(".edit-html-status")
                    .addClass("error")
                    .html( resp.error );
            } else {
                $(".edit-html-status")
                    .addClass("updated")
                    .html("<span class='dashicons dashicons-yes'></span> Updated");
            }

            $(".edit-html-spinner").hide();
        }, 'json')

    });

    $(".create-template").on("click", function() {
        $(".templates-new").slideUp(function() {
            $(".template-form").slideDown(function() {
                $(".switch-tmce").click();
            });
        })
    });

    $(".cancel-new-template").click(function() {
        $("#editor").val("");
        $(".template-form").slideUp(function() {
            $(".templates-new").slideDown();
        })
    });

});