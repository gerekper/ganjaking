jQuery( function ( $ ) {
    jQuery("body").bind("fue_email_type_changed", function(evt, type) {
        points_rewards_toggle_fields( type );
    });

    $("body").bind( "updated_email_details", function() {
        $("#interval_type").change();
    } );
} );

function points_rewards_toggle_fields( type ) {
    if (type == "points_and_rewards") {

        var show = ['.var_points_and_rewards'];
        var hide = ['.always_send_tr', '.signup_description', '.product_description_tr', '.product_tr', '.category_tr', '.use_custom_field_tr', '.custom_field_tr', '.var_item_name', '.var_item_category', '.var_item_names', '.var_item_categories', '.var_item_name', '.var_item_category', '.interval_type_after_last_purchase', '.interval_duration_date', '.var_customer'];

        jQuery("option.interval_duration_date").attr("disabled", true);

        for (x = 0; x < hide.length; x++) {
            jQuery(hide[x]).hide();
        }

        for (x = 0; x < show.length; x++) {
            jQuery(show[x]).show();
        }

        jQuery("option.interval_duration_date").attr("disabled", true);

        jQuery(".interval_duration_date").hide();

        jQuery("#interval_type")
            .val("points_earned")
            .change();
    } else {
        var hide = ['.interval_type_points_earned', '.interval_type_points_greater_than', '.var_points_and_rewards'];

        for (x = 0; x < hide.length; x++) {
            jQuery(hide[x]).hide();
        }
    }
}

jQuery(document).ready(function($) {
    $("#fue-email-details").on("change", "#interval_type", function() {
        $(".points-greater-than-meta").hide();
        $(".points-total-greater-than-meta").hide();

        if ($(this).val() == "points_greater_than") {
            $(".points-greater-than-meta").show();
        }

        if ($(this).val() == "points_total_greater_than") {
            $(".points-total-greater-than-meta").show();
        }
    });
});