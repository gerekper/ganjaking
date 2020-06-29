jQuery(document).ready(function ($) {

    $(document).on("click", "a.remove-amount", function (e) {
        e.preventDefault();

        var data = {
            'action'    : 'remove_gift_card_amount',
            'amount'    : $(this).closest("span.variation-amount").find('input[name="gift-card-amounts[]"]').val(),
            'product_id': $("#post_ID").val()
        };

        var clicked_item = $(this).closest("span.variation-amount");
        clicked_item.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywgc_data.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywgc_data.ajax_url, data, function (response) {
            if (1 == response.code) {
                clicked_item.remove();
            }

            clicked_item.unblock();
        });

    });

    /**
     * Add a new amount to current gift card
     * @param item
     */
    function add_amount(item) {
        var data = {
            'action'    : 'add_gift_card_amount',
            'amount'    : $("#gift_card-amount").val(),
            'product_id': $("#post_ID").val()
        };

        var clicked_item = item.closest("span.add-new-amount-section");
        clicked_item.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywgc_data.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywgc_data.ajax_url, data, function (response) {
            if (1 == response.code) {
                $("p._gift_card_amount_field").replaceWith(response.value);
            }

            $('#gift_card-amount').val('');
            $('#gift_card-amount').selectionStart = 0;
            $('#gift_card-amount').selectionEnd = 0;
            $('#gift_card-amount').focus();

            clicked_item.unblock();
        });
    }

    /**
     * Add a new amount for the current gift card
     */
    $(document).on("click", "a.add-new-amount", function (e) {
        e.preventDefault();
        add_amount($(this));
    });

    /**
     * Add a new amount for the current gift card
     */
    $(document).on('keypress', 'input#gift_card-amount', function (e) {
        if (event.which === 13) {
            e.preventDefault();

            //Disable textbox to prevent multiple submit
            $(this).attr("disabled", "disabled");

            //Do Stuff, submit, etc..
            add_amount($(this));

            $(this).removeAttr("disabled");

        }
    });

    $(document).on('change', 'input[name="ywgc_physical_gift_card"]', function (e) {
        var status = $(this).prop("checked");
        $('input[name="_virtual"]').prop("checked", !status);
    });

    $(document).on('click', '.image-gallery-reset', function (e) {
        e.preventDefault();

        $('#ywgc-card-header-image img').remove();
        $("#ywgc_product_image_id").val(0);
    });

    $(document).on('click', '.image-gallery-chosen', function (e) {
        e.preventDefault();

        var t = $(this),
            custom_uploader,
            id = t.attr('name') + '_id';

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        var custom_uploader_states = [
            // Main states.
            new wp.media.controller.Library({
                library   : wp.media.query(),
                multiple  : false,
                title     : ywgc_data.choose_image_text,
                priority  : 20,
                filterable: 'uploaded'
            })
        ];

        // Create the media frame.
        custom_uploader = wp.media.frames.downloadable_file = wp.media({
            // Set the title of the modal.
            title   : ywgc_data.choose_image_text,
            library : {
                type: ''
            },
            button  : {
                text: ywgc_data.choose_image_text
            },
            multiple: false,
            states  : custom_uploader_states
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            $('#ywgc-card-header-image img').remove();
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#ywgc-card-header-image').prepend('<img width="80" src="' + attachment.url + '" />');
            $("#" + id).val(attachment.id);
            $('.plugin-option .upload_img_url').trigger('change');
        });

        // Open the uploader dialog.
        custom_uploader.open();
    });

    $( 'body .ywgc_order_sold_as_gift_card' ).each( function () {
        $( this ).parent( 'td' ).find( '.wc-order-item-name' ).hide();
    });

    //show the manage stock in the inventory tab
    $('._manage_stock_field').addClass('show_if_gift-card').show();

    /* Manage date when gift card is created manually */
    if(typeof jQuery.fn.datepicker !== "undefined"){

        $(".ywgc-expiration-date-picker").datepicker({dateFormat: ywgc_data.date_format, minDate: +1, maxDate: "+1Y"});
    }


  var default_button_text = $('button#ywgc_direct_link_button:first').text();

  $(document).on( 'click', 'button#ywgc_direct_link_button', function (e) {
    e.preventDefault();

    var button =  $(this);

    var link = button.prev('#ywgc_direct_link').text()
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(link).select();
    document.execCommand("copy");
    $temp.remove();

    var copied_text = $('#ywgc_copied_to_clipboard').text();
    button.text(copied_text);

    setTimeout(function() {
      button.text(default_button_text);
    }, 1000);


  });

  $( document ).on( 'change', '.ywgc-toggle-enabled input', function () {

    var enabled   = $( this ).val() === 'yes' ? 'yes' : 'no',
        container = $( this ).closest( '.ywgc-toggle-enabled' ),
        gift_card_ID   = container.data( 'gift-card-id' );

    var blockParams = {
      message        : null,
      overlayCSS     : { background: '#fff', opacity: 0.7 },
      ignoreIfBlocked: true
    };
    container.block( blockParams );

    $.ajax( {
      type    : 'POST',
      data    : {
        action  : 'ywgc_toggle_enabled_action',
        id      : gift_card_ID,
        enabled : enabled,
      },
      url     : ajaxurl,
      success : function ( response ) {
        if ( typeof response.error !== 'undefined' ) {
          alert( response.error );
        }
      },
      complete: function () {
        container.unblock();
      }
    } );
  } );


});
