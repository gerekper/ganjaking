/**
 * Settings Scripts
 */

jQuery(document).ready(function() {

    /**
     * Display hints in settings
     */
    jQuery('.rp_wcdpd_settings_container .rp_wcdpd_setting').each(function() {

        // Get hint
        var hint = jQuery(this).data('rp-wcdpd-hint');

        // Check if hint is set
        if (hint) {

            // Append hint element
            jQuery(this).parent().append('<div class="rp_wcdpd_settings_hint">' + hint + '</div>');
        }
    });

    /**
     * Toggle fields
     */
    jQuery('#rp_wcdpd_cart_discounts_if_multiple_applicable').change(function() {
        var display = jQuery(this).val() === 'combined';
        jQuery('#rp_wcdpd_cart_discounts_combined_title').prop('disabled', !display).closest('tr').css('display', (display ? 'table-row' : 'none'));
    }).change();
    jQuery('#rp_wcdpd_checkout_fees_if_multiple_applicable').change(function() {
        var display = jQuery(this).val() === 'combined';
        jQuery('#rp_wcdpd_checkout_fees_combined_title').prop('disabled', !display).closest('tr').css('display', (display ? 'table-row' : 'none'));
    }).change();

    /**
     * Toggle promotion fields
     */
    jQuery(['rp_wcdpd_promo_your_price', 'rp_wcdpd_promo_total_saved', 'rp_wcdpd_promo_countdown_timer', 'rp_wcdpd_promo_volume_pricing_table']).each(function(index, value) {
        jQuery('#' + value).change(function() {
            if ((jQuery(this).is(':checkbox') && jQuery(this).is(':checked')) || (jQuery(this).is('select') && jQuery(this).val() !== '0')) {
                jQuery('[id^="' + value + '_"]').closest('tr').show();
            }
            else {
                jQuery('[id^="' + value + '_"]').closest('tr').hide();
            }
        }).change();
    });
    jQuery(['rp_wcdpd_promo_rule_notifications_product_pricing', 'rp_wcdpd_promo_rule_notifications_cart_discounts', 'rp_wcdpd_promo_rule_notifications_checkout_fees']).each(function(index, value) {
        jQuery('#' + value).change(function() {
            if ((jQuery(this).is(':checkbox') && jQuery(this).is(':checked'))) {
                jQuery('.if_' + value).closest('tr').show();
            }
            else {
                jQuery('.if_' + value).closest('tr').hide();
            }
        }).change();
    });

    /**
     * Turn all multiselects to Select2
     */
    jQuery('select[multiple].rp_wcdpd_field_select').each(function() {

        var config = {
            placeholder: jQuery(this).prop('id') === 'rp_wcdpd_conditions_custom_taxonomies' ? rp_wcdpd.labels.select2_placeholder_custom_product_taxonomies : rp_wcdpd.labels.select2_placeholder,
            language: {
                noResults: function (params) {
                    return rp_wcdpd.labels.select2_no_results;
                }
            },
        };

        // Initialize Select2
        if (typeof RP_Select2 !== 'undefined') {
            RP_Select2.call(jQuery(this), config);
        }
        // Initialize Select2
        else if (typeof element.selectWoo !== 'undefined') {
            jQuery(this).selectWoo(config);
        }
    });




    /**
     * We are done by now, remove preloader
     */
    jQuery('#rp_wcdpd_preloader').remove();

});
