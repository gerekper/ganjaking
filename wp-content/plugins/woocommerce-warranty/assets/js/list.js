jQuery(document).ready(function($) {
    $(".update-status").click(function() {
        var id      = $(this).data('request_id');
        var value   = $("#status_"+ id).val();

        if ( value ) {
            var data = {"action": "warranty_update_request_fragment", "type": "change_status", "status": value, "request_id": id};
            $.ajax({
                type:"POST",
                url: ajaxurl,
                data : data,
                success : function(response){
                    if ( response ) {
                        window.location.href = response;
                    }
                }
            });
        }
    });

    $("a.inline-edit").click(function(e) {
        e.preventDefault();

        var req_id = $(this).data("request_id");
        var tr = $(this).closest("tr");
        var cloned = $("#inline-edit-"+ req_id).clone();

        $("#the-list tr#inline-edit-"+ req_id).find(".close_tr").click();

        cloned
            .insertAfter(tr)
            .show();

        $("<tr class='hidden'></tr>").insertBefore(cloned);

        $("#the-list .tip").tipTip({
            maxWidth: "400px"
        });
    });

    $("#the-list").on("click", ".close-form", function(e) {
        e.preventDefault();

        $(this).parents("div.closeable").hide();
    });

    $("#the-list").on("click", ".close_tr", function() {
        $(this).parents("tr").remove();
        $("#the-list").find("tr.hidden").remove();
    });

    // RMA Update
    $("#the-list").on("click", ".rma-update", function() {
        var request = $("#the-list")
        var inputs  = request.find("input,select,textarea");
        var data    = $(inputs).serializeArray();

        data.push({
            name: "action",
            value: "warranty_update_inline"
        });
        data.push({
            name: "id",
            value: $(this).data("id")
        });
        data.push({
            name: "_wpnonce",
            value: $(this).data("security")
        });

        request.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post(
            ajaxurl, data, function(resp) {
                if ( resp.status == 'OK' ) {
                    var status_block = $(request).find(".warranty-update-message");
                    status_block.find("p").html( resp.message );
                    status_block.show();
                } else {
                    alert( resp.message );
                }
                request.unblock();
            }
        );

    });

    // Uploading files
    var file_frame;

    $("#the-list").on("click", ".rma-upload-button", function( event ) {
        event.preventDefault();

        var btn = $(this);

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $( this ).data( 'uploader_title' ),
            button: {
                text: $( this ).data( 'uploader_button_text' ),
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();

            var request_id = btn.data("id");
            $("#shipping_label_"+ request_id).val( attachment.url );
            $("#shipping_label_id_"+ request_id).val( attachment.id );
        });

        // Finally, open the modal
        file_frame.open();
    });

    $("#the-list").on("click", "input.request-tracking", function() {
        var btn = this;
        var tr = $(this).closest("tr");
        var td = $(tr).find("td");
        $( td ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post(
            ajaxurl,
            {
                action: "warranty_request_tracking",
                id: $(this).data("request")
            },
            function(resp) {
                $(".wc-tracking-requested").show();
                $("#the-list .request-tracking-div").remove();
                $(td).unblock();
            }
        );
    });

    $("#the-list").on("click", ".set-tracking", function() {
        var btn = this;
        var tr = $(this).closest("tr");
        var td = $(tr).find("td");
        $( td ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
        var provider = '';

        if ( $("#the-list select.return_tracking_provider").length > 0 ) {
            provider = $("#the-list select.return_tracking_provider option:selected").val();
        }

        $.post(
            ajaxurl,
            {
                action: "warranty_set_tracking",
                tracking: $("#the-list").find(".tracking_code").val(),
                id: $(this).data("request"),
                provider: provider
            },
            function(resp) {
                $(".wc-tracking-saved").show();
                $(td).unblock();
            }
        );
    });

    $("body").on("click", ".warranty-process-refund", function() {
        var id          = $(this).data("id");
        var security    = $(this).data("security");
        var table       = $("table.toplevel_page_warranties");
        var tb_window   = $(this).parents("#TB_window");
        var amount      = tb_window.find("input.amount").val();

        tb_remove();

        table.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post(
            ajaxurl,
            {
                action: "warranty_refund_item",
                ajax: true,
                id: $(this).data("id"),
                amount: amount,
                _wpnonce: security
            },
            function(resp) {
                if ( resp.status == 'OK' ) {
                    window.location.reload();
                } else {
                    alert( resp.message );
                    table.unblock();
                }

            }
        )
    });

    $("body").on("click", ".warranty-process-coupon", function() {
        var id          = $(this).data("id");
        var security    = $(this).data("security");
        var table       = $("table.toplevel_page_warranties");
        var tb_window   = $(this).parents("#TB_window");
        var amount      = tb_window.find("input.amount").val();

        tb_remove();

        table.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post(
            ajaxurl,
            {
                action: "warranty_send_coupon",
                ajax: true,
                id: $(this).data("id"),
                amount: amount,
                _wpnonce: security
            },
            function(resp) {
                if ( resp.status == 'OK' ) {
                    window.location.reload();
                } else {
                    alert( resp.message );
                    table.unblock();
                }

            }
        )
    });

    $("body").on("click", ".add_note", function(e) {
        e.preventDefault();
        var container   = $(this).parents(".inline-edit-col");
        var request     = $(this).data("request");
        var notes_list  = container.find( "ul.admin-notes" );
        var note        = $("#admin_note_"+ request).val()

        if ( note.length == 0 ) {
            return;
        }

        container.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var data = {
            action: 'warranty_add_note',
            request: request,
            note: note
        };

        $.post(ajaxurl, data, function(resp) {
            $(notes_list).html(resp);
            container.unblock();
        });
    });

    $("body").on("click", ".delete_note", function(e) {
        e.preventDefault();
        var container   = $(this).parents(".inline-edit-col");
        var note        = $(this).data("note_id");
        var request     = $(this).data("request");
        var notes_list  = container.find( "ul.admin-notes" );

        container.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var data = {
            action: 'warranty_delete_note',
            request: request,
            note_id: note
        };

        $.post(ajaxurl, data, function(resp) {
            $(notes_list).html(resp);
            container.unblock();
        });
    });

    $(".tip").tipTip({
        maxWidth: "400px"
    });
});