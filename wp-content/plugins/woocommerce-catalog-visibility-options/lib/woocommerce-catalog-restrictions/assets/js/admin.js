jQuery(document).ready(function ($) {

    $('#_wc_restrictions').change(function () {
        if ($(this).val() == 'restricted') {
            $('#wc_catalog_restrictions_roles_container').show();
        } else {
            $('#wc_catalog_restrictions_roles_container').hide();
        }
    });

    $('#_wc_restrictions_purchase').change(function () {
        if ($(this).val() == 'restricted') {
            $('#wc_catalog_restrictions_purchase_roles_container').show();
            $('#wc_catalog_restrictions_purchase_locations_container').hide();
        } else if ($(this).val() == 'locations_allowed' || $(this).val() == 'locations_restricted'){
            $('#wc_catalog_restrictions_purchase_roles_container').hide();
            $('#wc_catalog_restrictions_purchase_locations_container').show();
        } else {
            $('#wc_catalog_restrictions_purchase_roles_container').hide();
            $('#wc_catalog_restrictions_purchase_locations_container').hide();
        }
    });


    $('#_wc_restrictions_price').change(function () {
        if ($(this).val() == 'restricted') {
            $('#wc_catalog_restrictions_prices_locations_container').hide();
            $('#wc_catalog_restrictions_prices_roles_container').show();
        } else if ($(this).val() == 'locations_allowed' || $(this).val() == 'locations_restricted'){
            $('#wc_catalog_restrictions_prices_roles_container').hide();
            $('#wc_catalog_restrictions_prices_locations_container').show();
        } else {
            $('#wc_catalog_restrictions_prices_roles_container').hide();
            $('#wc_catalog_restrictions_prices_locations_container').hide();
        }
    });

    $('#_wc_restrictions_location').change(function () {
        if ($(this).val() == 'restricted') {
            $('#wc_catalog_restrictions_locations_container').show();
        } else {
            $('#wc_catalog_restrictions_locations_container').hide();
        }
    });

});