/**
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */

/* global yith_ywsbs_frontend */
jQuery(document).ready( function($) {
    'use strict';

    var $body = $('body');


    $.fn.yith_ywsbs_variations = function() {
        var $form = $('.variations_form'),
            $button = $form.find('.single_add_to_cart_button');
           // default_label = $button.text();

        $form.on( 'found_variation', function( event, variation){
            if( variation.is_subscription == true ){
                $button.text(yith_ywsbs_frontend.add_to_cart_label);
            }else{
                $button.text(yith_ywsbs_frontend.default_cart_label);
            }
        });

    };

    if( $body.hasClass('single-product') ){
        $.fn.yith_ywsbs_variations();
    }


    $.fn.yith_ywsbs_switch_variations = function() {

        var selected = $(this).find(':selected'),
            show = selected.data('show'),
            price = selected.data('price'),
            simpleprice = selected.data('simpleprice'),
            $upgrade_option =  $('.upgrade-option');
        if( show == 'yes'){
            $upgrade_option.slideDown('slow');
            $upgrade_option.find('.price').html(price);
            $('#pay-gap-price').val(simpleprice);
        }else{
            $upgrade_option.slideUp();
            $('#pay-gap-no').attr('checked', 'checked');
            $('#pay-gap-price').val('');
        }
    };

    $('#switch-variation').yith_ywsbs_switch_variations();

    $(document).on('change', '#switch-variation', function(e){
        $(this).yith_ywsbs_switch_variations();
    });

    if (typeof $.fn.prettyPhoto != 'undefined') {
        var cancel_buttons = $('.cancel-subscription-button');

        cancel_buttons.each(function () {
            var id = $(this).data('id');
            var url = $(this).data('url');
            $(this).prettyPhoto({
                hook: 'data-ywsbs-rel',
                social_tools: false,
                theme: 'pp_woocommerce',
                horizontal_padding: 20,
                opacity: 0.8,
                deeplinking: false,
                changepicturecallback: function () {
                    $('.my-account-cancel-quote-modal-button').on('click', function (e) {
                        e.preventDefault();
                        window.location.href = url;
                    });
                }

            });
        });


        var pause_buttons = $('.pause-subscription-button');

        pause_buttons.prettyPhoto({
            hook: 'data-ywsbs-rel',
            social_tools: false,
            theme: 'pp_woocommerce',
            horizontal_padding: 20,
            opacity: 0.8,
            deeplinking: false
        });

        var resume_buttons = $('.resume-subscription-button');

        resume_buttons.prettyPhoto({
            hook: 'data-ywsbs-rel',
            social_tools: false,
            theme: 'pp_woocommerce',
            horizontal_padding: 20,
            opacity: 0.8,
            deeplinking: false
        });

        var renew_buttons = $('.renew-subscription-button');

        renew_buttons.prettyPhoto({
            hook: 'data-ywsbs-rel',
            social_tools: false,
            theme: 'pp_woocommerce',
            horizontal_padding: 20,
            opacity: 0.8,
            deeplinking: false
        });

        var switch_buttons = $('.switch-subscription-button');

        switch_buttons.prettyPhoto({
            hook: 'data-ywsbs-rel',
            social_tools: false,
            theme: 'pp_woocommerce',
            horizontal_padding: 20,
            opacity: 0.8,
            deeplinking: false
        });

        $(document).on('click', '.close-subscription-modal-button', function (e) {
            e.preventDefault();
            $.prettyPhoto.close();
        });

    }





});


