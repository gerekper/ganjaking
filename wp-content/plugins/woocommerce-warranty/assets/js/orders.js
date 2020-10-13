jQuery(document).ready(function($) {
    $("#the-list").on("click", "a.inline-rma", function(e) {
        e.preventDefault();

        var tr = $(this).closest("tr");
        var id = tr.attr("id");

        if ( $("#the-list #inline-edit-"+id).length > 0 ) {
            // close it
            $("#the-list #inline-edit-"+ id).find(".close_tr").click();
        } else {
            remove_inline_edit_rows();
            insert_inline_row( id );
        }
    });

    $("#the-list").on("click", ".close_tr", function() {
        $(this).parents("tr").remove();
        $("#the-list").find("tr.hidden").remove();
    });

    // RMA Update
    $("#the-list").on("click", ".rma-update", function() {
        var request = $(this).parents(".warranty-request")
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
                    request.find(".actions-block").html( resp.actions );
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

    // Handle RMA Delete requests
    $("#the-list").on("click", ".warranty-trash", function() {
        var request = $(this).closest(".warranty-request");

        request.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post(
            ajaxurl,
            {
                action: "warranty_delete_request",
                id: $(this).data("id"),
                _wpnonce: $(this).data("security")
            },
            function() {
                request
                    .unblock()
                    .remove();
            }
        )
    });

    // Send Coupon
    $("#the-list").on("click", ".warranty-item-coupon", function() {
        var id = $(this).data("id");
        tb_show( 'Coupon', '#TB_inline?width=400&height=250&inlineId=warranty-coupon-modal-'+ id );
    });

    $("body").on("click", ".warranty-process-coupon", function() {
        var btn         = $(this);
        var request     = $(this).closest(".warranty-request");
        var amount      = $(this).parents("#TB_window").find("input.amount").val();
        var id          = $(this).data("id");
        var security    = $(this).data("security");

        tb_remove();

        request.block({
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
                _wpnonce: $(this).data("security")
            },
            function(resp) {
                if ( resp.status == 'OK' ) {
                    window.location.reload();
                } else {
                    alert( resp.message );
                    request.unblock();
                }
            }
        )
    });

    // Refund
    $("#the-list").on("click", ".warranty-item-refund", function() {
        var id = $(this).data("id");
        tb_show( 'Refund', '#TB_inline?width=400&height=250&inlineId=warranty-refund-modal-'+ id );
    });

    $("body").on("click", ".warranty-process-refund", function() {
        var id          = $(this).data("id");
        var security    = $(this).data("security");
        var request     = $("#warranty_request_"+ id);
        var amount      = $(this).parents("#TB_window").find("input.amount").val();

        tb_remove();

        request.block({
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
                    request.unblock();
                }

            }
        )
    });

    // Return
    $("#the-list").on("click", ".warranty-inventory-return", function() {
        var btn         = $(this);
        var request     = $(this).closest(".warranty-request");
        var id          = $(this).data("id");
        var security    = $(this).data("security");

        request.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post(
            ajaxurl,
            {
                action: "warranty_return_inventory",
                ajax: true,
                id: $(this).data("id"),
                _wpnonce: $(this).data("security")
            },
            function(resp) {
                if ( resp.status == 'OK' ) {
                    $("#warranty_update_message p").html( resp.message );
                    $("#warranty_update_message").show();
                    btn
                        .val("Stock returned")
                        .attr("disabled", true);
                } else {
                    alert( resp.message );
                }
                request.unblock();
            }
        )
    });

    var insert_inline_row = function( source_id ) {
        var cloned = $("#inline-edit-"+ source_id).clone();
        var source = $("#"+ source_id);

        cloned
            .insertAfter(source)
            .show();
        $("<tr class='hidden'></tr>").insertBefore(cloned);
    }

    var remove_inline_edit_rows = function() {
        $("#the-list tr.hidden").remove();
        $("#the-list tr.inline-edit-row").remove();
    }
});