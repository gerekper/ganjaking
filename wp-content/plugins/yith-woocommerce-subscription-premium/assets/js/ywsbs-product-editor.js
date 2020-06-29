/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */

jQuery(document).ready(function ($) {
    'use strict';

    function toggle_product_editor_single_product() {
        if ($('#_ywsbs_subscription').prop('checked')) {
            $('.ywsbs-general-section').show();
        } else {
            $('.ywsbs-general-section').hide();
        }
    }

    $('#_ywsbs_subscription').on('change', function () {
        toggle_product_editor_single_product();
    });
    $(document).on( 'change','#_virtual,#_downloadable', function(e){

        if(!$('#_ywsbs_subscription').prop('checked')){
            $('.ywsbs-general-section').hide();
        }
    });

    toggle_product_editor_single_product();



    $('#_ywsbs_price_time_option').on('change', function () {

        var selected = $(this).find(':selected'),
            max_value = selected.data('max');
        $('.ywsbs_max_length .description span').text(selected.data('text'));
        $('.ywsbs_max_length .description .max-l').text(max_value);
    }).change();


    function toggle_product_editor_variable_product() {
        var $subscription_variable = $(document).find('.variable_ywsbs_subscription');

        $subscription_variable.each(function () {
            var $t = $(this),
                $price_is_per = $t.closest('.woocommerce_variable_attributes').find('.ywsbs_subscription_variation_products'),
                $time_option = $t.closest('.woocommerce_variable_attributes').find('#_ywsbs_price_time_option');


            $time_option.on('change', function () {
                $(this).closest('.ywsbs_subscription_variation_products').find('.variable_ywsbs_max_length .description span').text($(this).val());
                var selected = $(this).find(':selected'),
                    max_value = selected.data('max');

                $(this).closest('.ywsbs_subscription_variation_products').find('.variable_ywsbs_max_length .description .max-l').text(max_value);
            });


            if ($t.prop('checked')) {
                $price_is_per.show();

            } else {
                $price_is_per.hide();
            }

            /*
            * variable_ywsbs_max_length
            * */

            $t.on('change', function () {
                if ($t.prop('checked')) {
                    $price_is_per.show();
                } else {
                    $price_is_per.hide();
                }
            });
        });
    }

    $('#variable_product_options').on('woocommerce_variations_added', function () {
        toggle_product_editor_variable_product();
    });
    $('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {
        toggle_product_editor_variable_product();
    });


});
