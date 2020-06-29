jQuery( function ( $ ) {
    var statuses = [
        'booking_status_unpaid', 'booking_status_pending-confirmation', 'booking_status_confirmed',
        'booking_status_paid', 'booking_status_cancelled', 'booking_status_complete'
    ];

    $("body").bind("fue_email_type_changed", function(evt, type) {
        wc_bookings_toggle_fields(type);
    });

    $("body").bind("updated_email_details", function() {
        $(".show-if-booking-status").hide();
        $(".wc-bookings-selector").show();

        $("#fue-email-details").on("change", "#interval_type", function() {
            var val = $(this).val();

            if ( $.inArray( val, statuses ) !== -1 ) {
                $(".show-if-booking-status").show();
            } else {
                $(".show-if-booking-status").hide();
                $("#meta_bookings_last_status").val('');
            }

        });

        $("#interval_type").change();
    } );


    function wc_bookings_toggle_fields( type ) {

        if (type == "wc_bookings") {
            var show = ['.interval_type_wc_bookings', '.wc_bookings', '.wc-products-selector'];
            var hide = [
                '.interval_type_option', '.interval_duration_date', '.always_send_tr', '.signup_description',
                '.product_description_tr', '.product_tr', '.category_tr', '.use_custom_field_tr',
                '.custom_field_tr', '.var_item_names', '.var_item_categories',
                '.interval_type_after_last_purchase', '.var_customer', 'show-if-booking-status'
            ];

            $("option.interval_duration_date").attr("disabled", true);

            for (x = 0; x < hide.length; x++) {
                $(hide[x]).hide();
            }

            for (x = 0; x < show.length; x++) {
                $(show[x]).show();
            }

            $("#interval_type").change();
        } else {
            var hide = ['.interval_type_before_booking_event', '.interval_type_after_booking', '.interval_type_after_booking_approved', '.var_wc_bookings', '.wc_bookings'];

            for (x = 0; x < hide.length; x++) {
                $(hide[x]).hide();
            }
        }
    }
} );