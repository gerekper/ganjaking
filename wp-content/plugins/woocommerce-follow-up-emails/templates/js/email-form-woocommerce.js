var email_data;
jQuery( function( $ ) {

    function toggle_custom_fields_tab() {
        // hide the Custom Fields tab if no product is selected
        if ($("#product_id").val() > 0) {
            $(".fue-tabs li.custom-fields").show();
        } else {
            $(".fue-tabs li.custom-fields").hide();
        }
    }

    function toggle_downloadables_div() {
        if ( email_data && email_data.product_files ) {
            var $select = $("#downloadable_file");
            $select.html("<option></option>");

            for ( key in email_data.product_files ) {
                $select.append('<option value="'+ key +'">'+ email_data.product_files[ key ] +'</option>');
            }

            $select.trigger( 'change' );
        }

        if ($("#product_id").val() > 0) {
            $(".downloadables_div").show();
        } else {
            $(".downloadables_div").hide();
        }
    }

    function toggle_variations_row() {
        if (email_data.has_variations) {
            $(".product_include_variations").show();
        } else {
            $(".product_include_variations").hide();
        }
    }

    $("body").on("updated_email_type", function () {
        woocommerce_toggle_fields($("#email_type").val());
    });

    $("body").on("updated_email_details", function () {
        woocommerce_toggle_fields($("#email_type").val());

        toggle_custom_fields_tab();
        toggle_downloadables_div();

        // trigger the conditions toggler
        $(' select.condition:visible' ).trigger( 'change' );
        $( '#storewide_type' ).trigger( 'change' );
    });

    $("body").on("updated_email", toggle_variations_row);

    init_select2_fields();

    $( '#fue-email-details' ).on( 'change', '#storewide_type', function () {
        var type = $(this).val();

        $(".product_tr, .category_tr, .excluded_category_tr").hide();

        if (type == "all") {
            $("#product_id").val("");
            $("#category_id").val("");

            $(".product_tr, .category_tr").hide();
            $(".excluded_category_tr").show();

            $( '#product_id' ).trigger( 'change' );
        } else if (type == "products") {
            $("#category_id").val("");

            $(".product_tr").show();
            $(".category_tr").hide();
            $(".excluded_category_tr").hide();
        } else if (type == "categories") {
            $("#product_id").val("");

            $(".product_tr").hide();
            $(".category_tr").show();
            $(".excluded_category_tr").hide();
        }
    } );

    $( '#fue-email-details' ).on( 'change', '#product_id, #include_variations, #category_id', function () {
        $("#fue-email-details").block({
            message: null,
            overlayCSS: {background: '#fff url(' + FUE.ajax_loader + ') no-repeat center', opacity: 0.6}
        });
        var args = {
            "action": "fue_update_email",
            "id": $("#email_id").val(),
            "product_id": $("#product_id").val(),
            "category_id": $("#category_id").val(),
            "meta[storewide_type]": $("#storewide_type").val(),
            "meta[include_variations]": $("#include_variations").is(":checked") ? 'yes' : '',
            'nonce': 'undefined' !== typeof $( this ).data( 'nonce' ) ? $( this ).data( 'nonce' ) : $( this ).closest( '#fue-email-details' ).find( '#_wpnonce' ).val()
        };
        $.post(ajaxurl, args, function (resp) {
            email_data = resp.email;

            $('body').trigger('fue_update_variables');
            toggle_custom_fields_tab();
            toggle_downloadables_div();
            toggle_variations_row();

            $("#fue-email-details").unblock();
        }, 'json');

    } );

    // conditions
    $( '#fue-email-details' ).on( 'change', 'select.condition', function () {
        var conditions_with_value = [
            'order_total_above', 'order_total_below', 'total_orders_above',
            'total_orders_below', 'total_purchases_above', 'total_purchases_below'
        ];
        var conditions_with_currency = [
            'order_total_above', 'order_total_below', 'total_purchases_above', 'total_purchases_below'
        ];
        var conditions_with_products = ['bought_products'];
        var conditions_with_categories = ['bought_categories'];
        var condition = $(this).val();

        if ($.inArray(condition, conditions_with_value) > -1) {
            $(this).parents('fieldset').find('span.value').show();
            $( this ).parents( 'fieldset' ).find( '.value :input' ).prop( 'disabled', false );
        } else {
            $(this).parents('fieldset').find('span.value').hide();
            $( this ).parents( 'fieldset' ).find( '.value :input' ).prop( 'disabled', true );
        }

        if ($.inArray(condition, conditions_with_currency) > -1) {
            $(this).parents('fieldset').find('span.value-currency').show();
        } else {
            $(this).parents('fieldset').find('span.value-currency').hide();
        }

        if ($.inArray(condition, conditions_with_products) > -1) {
            $(this).parents('fieldset').find('.value-products').show();
            $( this ).parents( 'fieldset' ).find( '.value-products :input' ).prop( 'disabled', false );

        } else {
            $(this).parents('fieldset').find('.value-products').hide();
            $( this ).parents( 'fieldset' ).find( '.value-products :input' ).prop( 'disabled', true );
        }

        if ($.inArray(condition, conditions_with_categories) > -1) {
            $(this).parents('fieldset').find('.value-categories').show();
            $( this ).parents( 'fieldset' ).find( '.value-categories :input' ).prop( 'disabled', false );
        } else {
            $(this).parents('fieldset').find('.value-categories').hide();
            $( this ).parents( 'fieldset' ).find( '.value-categories :input' ).prop( 'disabled', true );
        }

        if ( condition == "payment_method" ) {
            $(this).parents('fieldset').find('.value-payment-method').show();
            $( this ).parents( 'fieldset' ).find( '.value-payment-method :input' ).prop( 'disabled', false );
        } else {
            $(this).parents('fieldset').find('.value-payment-method').hide();
            $( this ).parents( 'fieldset' ).find( '.value-payment-method :input' ).prop( 'disabled', true );
        }

        if ( condition == "shipping_method" ) {
            $(this).parents('fieldset').find('.value-shipping-method').show();
            $( this ).parents( 'fieldset' ).find( '.value-shipping-method :input' ).prop( 'disabled', false );
        } else {
            $(this).parents('fieldset').find('.value-shipping-method').hide();
            $( this ).parents( 'fieldset' ).find( '.value-shipping-method :input' ).prop( 'disabled', true );
        }

    } );

    // Conditions
    $( '#fue-email-details' ).on( 'click', '.btn-add-condition', function( e ) {
        e.preventDefault();

        var id = fue_get_next_condition_id();
        var html = $("#conditions_tpl").html().replace(/_idx_/g, id);

        $(html).insertBefore($(this).parents('p'), null);
        $( '#condition_' + id + ' :input').prop( 'disabled', false );
        $("#condition_" + id + " .select2-init")
            .removeClass('select2-init')
            .addClass('select2');

        $("div.value-products:visible .ajax-select2-init")
            .removeClass('ajax-select2-init')
            .addClass('ajax_select2_products_and_variations');

        init_select2_fields();
    } );

    $( '#fue-email-details' ).on( 'click', '.btn-remove-condition', function( e ) {
        e.preventDefault();

        $(this).parents('fieldset').remove();
    } );

    // enable visible input fields
    $('body').on('updated_email_details', function () {
        $( '#trigger_conditions :input:visible' ).prop( 'disabled', false );

        $(".select2-init:visible")
            .addClass('select2')
            .removeClass('select2-init');

        $("div.value-products:visible .ajax-select2-init")
            .removeClass('ajax-select2-init')
            .addClass('ajax_select2_products_and_variations');

        $( 'select.condition' ).trigger( 'change' );

        $( '#send_coupon' ).trigger( 'change' );
        $( '#interval_type' ).trigger( 'change' );

        init_select2_fields();
    });

    $('body').on('updated_email_type', function (evt, type) {
        $("#fue-email-test").block({
            message: null,
            overlayCSS: {background: '#fff url(' + FUE.ajax_loader + ') no-repeat center', opacity: 0.6}
        });
        var args = {
            "action": "fue_get_email_test_html",
            "id": $("#email_id").val(),
            "type": type
        }
        $.getJSON(ajaxurl, args, function (resp) {
            $("#fue-email-test div.inside").html(resp.html);
            init_select2_fields();
            $( '#test_type' ).trigger( 'change' );
            $("#fue-email-test").unblock();
        });

    });

    $( '#fue-email-details' ).on( 'change', '#use_custom_field', function () {
        if ($(this).prop("checked")) {
            $(".show-if-custom-field").show();
        } else {
            $(".show-if-custom-field").hide();
        }
    } );

    $( '#fue-email-details' ).on( 'change', '#custom_fields', function () {
        if ($(this).val() == "Select a product first.") return;
        $(".show-if-cf-selected").show();
        $("#custom_field").val("{cf " + $("#product_id").val() + " " + $(this).val() + "}");
    } );

    $( '#fue-email-details' ).on( 'change', '#send_coupon', function () {
        if ($(this).prop("checked")) {
            $(".var_coupon").show();
            $(".coupon_tr").show();
        } else {
            $(".var_coupon").hide();
            $(".coupon_tr").hide();
        }
    } );

    $( '#fue-email-test' ).on( 'change', '#test_type', function () {
        if ($(this).val() == "order") {
            $("#test_email_order").show();
            $("#test_email_product").hide();
        } else {
            $("#test_email_product").show();
            $("#test_email_order").hide();
        }
    } );

    // Interval Type
    $( '#fue-email-details' ).on( 'change', '#interval_type', function () {

        if ( $(this).val() == "downloaded" || $(this).val() == "not_downloaded" ) {
            $("#storewide_type")
                .val("products")
                .trigger( 'change' )
                .prop( 'disabled', true );

            $("#storewide_type_hidden")
                .prop( 'disabled', false )
                .val("products");

            //$(".downloadables_div").show();
        } else {
            //$(".downloadables_div").hide();
            $( '#storewide_type_hidden' ).prop( 'disabled', true );
            $( '#storewide_type' ).prop( 'disabled', false );
        }

        if ( $(this).val() == "coupon" ) {
            $("#storewide_type")
                .val("all")
                .prop( 'disabled', true );

            $("#storewide_type_hidden")
                .prop( 'disabled', false )
                .val("all");

            $(".wc-products-selector").hide();
            $(".coupons_div").show();
            $(".var_interval_coupon").show();
        } else {
            $( '#storewide_type_hidden' ).prop( 'disabled', true );
            $( '#storewide_type' ).prop( 'disabled', false );
            $(".wc-products-selector").show();
            $(".coupons_div").hide();
            $(".var_interval_coupon").hide();
        }
    } );
});

function woocommerce_toggle_fields( type ) {
    var hide = [];
    var show = [];

    jQuery(".wc-products-selector").hide();

    if ( type == 'storewide' || type == 'reminder' ) {
        jQuery(".wc-products-selector").show();
    }

    if ( type == 'storewide' ) {
        show = ['.always_send_tr', '.var_item_names', '.var_item_categories', '.interval_type_option', '.interval_type_span', '.var', '.use_custom_field_tr'];
        hide.push('var_interval_downloadable_file_added', '.coupons_div', '.adjust_date_tr', '.signup_description', '.email_receipient_tr', '.btn_send_save', '.var_item_name', '.var_item_category', '.custom_field_tr', '.interval_type_order_total_above', '.interval_type_order_total_below', '.interval_type_purchase_above_one', '.interval_type_total_purchases', '.interval_type_total_orders', '.interval_type_after_last_purchase', '.var_customer');
    } else {
        hide.push('.var_item_names', '.var_item_categories', '.downloadables_div', '.coupons_div');
    }

    if ( type == 'customer' ) {
        show = ['.always_send_tr', '.interval_type_option', '.interval_type_span', '.interval_type_order_total_above', '.interval_type_order_total_below', '.interval_type_purchase_above_one', '.interval_type_total_purchases', '.interval_type_total_orders', '.interval_type_total_purchases', '.interval_type_after_last_purchase', '.interval_type_span', '.var_customer', '.use_custom_field_tr'];
        hide = ['.downloadables_div', '.adjust_date_tr', '.always_send_tr', '.signup_description', '.product_description_tr', '.product_tr', '.category_tr', '.var_item_name', '.var_item_category', '.var_item_names', '.var_item_categories', '.var_item_name', '.var_item_category', '.interval_duration_date', '.updated_variables_list', '.custom_field_tr'];
        jQuery( 'option.interval_duration_date' ).prop( 'disabled', true );
    } else {
        hide.push('.interval_type_order_total_above', '.interval_type_order_total_below', '.interval_type_purchase_above_one', '.interval_type_total_purchases', '.interval_type_total_orders', '.interval_type_total_purchases', '.interval_type_after_last_purchase', '.var_customer');
    }

    if ( type == 'reminder' ) {
        show = ['.interval_type_option', '.interval_type_span', '.use_custom_field_tr', '.var'];
        hide.push('.downloadables_div', '.always_send_tr', '.adjust_date_tr', '.var_item_names', '.email_receipient_tr', '.btn_send_save', '.var_item_categories', '.signup_description', '.interval_type_order_total_above', '.interval_type_order_total_below', '.interval_type_purchase_above_one', '.interval_type_total_purchases', '.interval_type_total_orders', '.interval_type_after_last_purchase', '.var_customer', '.interval_duration_date');
    } else {
        hide.push('.var_first_email', '.var_quantity_email', '.var_final_email');
    }

    for (var x = 0; x < show.length; x++) {
        jQuery(show[x]).show();
    }

    for (var x = 0; x < hide.length; x++) {
        jQuery(hide[x]).hide();
    }

}

function fue_get_next_condition_id() {
    var i = 0;

    while ( jQuery("#condition_"+ i).length ) {
        i++;
    }

    return i;
}
