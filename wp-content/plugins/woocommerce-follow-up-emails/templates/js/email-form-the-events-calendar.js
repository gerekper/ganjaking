jQuery( function ( $ ) {
    jQuery("body").on("fue_email_type_changed", function(evt, type) {
        wootickets_toggle_fields(type);
    });

    // enable visible input fields
    $('body').on('updated_email_details', function() {
        $( '#wootickets_type' ).trigger( 'change' );
    });

    $( '#fue-email-details' ).on( 'change', '#wootickets_type', function() {
        $(".product_tr, .category_tr, .excluded_category_tr").hide();

        var type = $(this).val();

        switch ( type ) {
            case 'all':
                $("#ticket_product_id").val("");
                $("#ticket_category_id").val("0");

                $(".ticket_product_tr, .ticket_category_tr, .ticket_event_category_tr").hide();
                $(".excluded_category_tr").show();

                break;

            case 'products':
                $("#ticket_category_id").val("0");

                $(".ticket_product_tr").show();
                $(".ticket_category_tr").hide();
                $(".ticket_event_category_tr").hide();
                break;

            case 'categories':
                $("#ticket_product_id").val("");

                $(".ticket_product_tr").hide();
                $(".ticket_event_category_tr").hide();
                $(".ticket_category_tr").show();
                break;

            case 'event_categories':
                $("#ticket_product_id").val("");

                $(".ticket_product_tr").hide();
                $(".ticket_category_tr").hide();
                $(".ticket_event_category_tr").show();
                break;
        }

        $( '#ticket_product_id' ).trigger( 'change' );
        $( '#ticket_category_id' ).trigger( 'change' );
    } );

    jQuery("body").on("fue_interval_type_changed", function(evt, type) {
        if (type == "before_tribe_event_starts" || type == "after_tribe_event_ends") {
            jQuery(".adjust_date_tr").show();
            $(".wootickets-selector").show();
            $(".wc-products-selector").hide();
        } else if ( 'string' === typeof type && type.substr( 0, 7 ) == "ticket_" ) {
            $(".wootickets-selector").show();
            $(".wc-products-selector").hide();
        } else {
            $(".wootickets-selector").hide();
            $(".wc-products-selector").show();
        }
    });
} );

function wootickets_toggle_fields( type ) {
    if (type == "wootickets" || type == "twitter") {
        var show = ['#fue-email-wootickets', '.adjust_date_tr', '.interval_type_before_tribe_event_starts', '.interval_type_after_tribe_event_ends', '.ticket_product_tr'];
        var hide = ['.interval_type_option', '.always_send_tr', '.signup_description', '.product_description_tr', '.product_tr', '.category_tr', '.use_custom_field_tr', '.custom_field_tr', '.var_item_name', '.var_item_category', '.var_item_names', '.var_item_categories', '.var_item_name', '.var_item_category', '.interval_type_after_last_purchase', '.var_customer'];

        for (x = 0; x < hide.length; x++) {
            jQuery(hide[x]).hide();
        }

        for (x = 0; x < show.length; x++) {
            jQuery(show[x]).show();
        }

        jQuery( '#interval_type' ).trigger( 'change' );
        jQuery( '#wootickets_type' ).trigger( 'change' );
    } else {
        var hide = ['#fue-email-wootickets', '.var_events_calendar', '.interval_type_before_tribe_event_starts', '.interval_type_after_tribe_event_ends', '.tribe_limit_tr', '.ticket_product_tr'];

        for (x = 0; x < hide.length; x++) {
            jQuery(hide[x]).hide();
        }
    }
}

jQuery(document).ready(function($) {
    wootickets_toggle_fields( jQuery("#email_type").val() );

    jQuery( '#interval_type' ).on( 'change', function() {
        var val = jQuery(this).val();
        if ( val == "before_tribe_event_starts" || val == "after_tribe_event_ends" ) {
            jQuery("option.interval_duration_date").attr("disabled", true);
            jQuery(".interval_duration_date").hide();
            jQuery(".interval_type_after_span").hide();
            jQuery(".var_events_calendar").show();
        } else {
            jQuery(".var_events_calendar").hide();
        }

        if (val == "before_tribe_event_starts") {
            jQuery(".tribe_limit_tr").show();
        } else {
            jQuery(".tribe_limit_tr").hide();
        }

    } ).trigger( 'change' );

    $( 'body' ).on( 'change', '#ticket_product_id', function() {
        if ( jQuery(this).val() ) {
            // if selected product contain variations, show option to include variations
            jQuery(".ticket_product_tr").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

            jQuery.get(ajaxurl, {action: 'fue_wc_product_has_children', product_id: jQuery(this).find("option:selected").val(), nonce: $( this ).data( 'nonce' )}, function(resp) {
                if ( resp == 1) {
                    jQuery(".product_include_variations").show();
                } else {
                    jQuery("#include_variations").prop("checked", false);
                    jQuery(".product_include_variations").hide();
                }

                jQuery(".ticket_product_tr").unblock();
            });
        } else {
            jQuery("#include_variations").prop("checked", false);
            jQuery(".product_include_variations").hide();
        }
    } );

});
