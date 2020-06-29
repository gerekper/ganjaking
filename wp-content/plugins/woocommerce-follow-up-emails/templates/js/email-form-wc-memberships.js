jQuery( function( $ ) {

    $("body").on("change", "#interval_type", function() {
        wc_memberships_toggle_fields($("#email_type").val());
    });
    $("body").bind("updated_email_type updated_variables_list", function () {
        wc_memberships_toggle_fields($("#email_type").val());
    });

    $("body").bind("updated_email_details", function () {
        wc_memberships_toggle_fields($("#email_type").val());

        $("#storewide_type").trigger("change");
    });

});

function wc_memberships_toggle_fields( type ) {
    var show = [],
        hide = [];

    if ( type === "wc_memberships" ) {
        show = ['.var_wc_memberships'];
        hide = ['.var_order'];
    } else {
        show = ['.var_order'];
        hide = ['.var_wc_memberships'];
    }

    fue_show_elements( show );
    fue_hide_elements( hide );
}