jQuery( function ( $ ) {
    $("body").on("fue_email_type_changed", function(evt, type) {
        subscriptions_toggle_fields( type );
    });

    function subscriptions_toggle_fields( type ) {

        if (type == "subscription") {

            var show = ['#fue-email-subscriptions', '.var_subscriptions', '.interval_type_subscription', '.var_item_name', '.var_item_category', '.var_dollar_spent_order', '.product_description_tr', '.subscription_product_tr', '.category_tr'];
            var hide = ['.interval_type_option', '.always_send_tr', '.signup', '.signup_description', '.use_custom_field_tr', '.custom_field_tr', '.var_item_name', '.var_item_category', '.var_item_names', '.var_item_categories', '.interval_type_after_last_purchase', '.interval_duration_date', '.var_customer'];

            $( 'option.interval_duration_date' ).prop( 'disabled', true );

            for (x = 0; x < hide.length; x++) {
                $(hide[x]).hide();
            }

            for (x = 0; x < show.length; x++) {
                $(show[x]).show();
            }

            $("div.product_tr, div.category_tr").remove();

            $(".interval_duration_date").hide();
            $( '#interval_type' ).trigger( 'change' );
        } else {
            var hide = ['#fue-email-subscriptions', '.var_subscriptions', '.interval_type_subs_activated', '.interval_type_subs_renewed', '.interval_type_subs_cancelled', '.interval_type_subs_expired', '.interval_type_subs_suspended', '.interval_type_subs_reactivated', '.interval_type_subs_before_renewal', '.var_item_name', '.var_item_category', '.subscription_product_tr'];

            for (x = 0; x < hide.length; x++) {
                $(hide[x]).hide();
            }
        }
    }
} );

jQuery(document).ready(function($) {

    $( '#fue-email-details' ).on( 'change', '#subscription_product_id, #include_variations', function() {
        $("#fue-email-details").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });
        var args = {
            "action":       "fue_update_email",
            "id":           $("#email_id").val(),
            "product_id":   $("#subscription_product_id").val(),
            "category_id":  '',
            "meta[storewide_type]": $("#storewide_type").val(),
            "meta[include_variations]": $("#include_variations").is(":checked") ? 'yes' : '',
            'nonce': 'undefined' !== typeof $( this ).data( 'nonce' ) ? $( this ).data( 'nonce' ) : $( this ).closest( '#fue-email-details' ).find( '#_wpnonce' ).val()
        };
        $.post( ajaxurl, args, function( resp ) {
            email_data = resp.email;

            $( 'body' ).trigger( 'updated_email' );
            $("#fue-email-details").unblock();
        }, 'json');

    } );

    $( '#fue-email-details' ).on( 'change', '#storewide_type', function () {
        var type = $(this).val();

        if (type == "all") {
            $("#subscription_product_id").val("");
        } else if (type == "categories") {
            $("#subscription_product_id").val("");
        }

    } );

});
