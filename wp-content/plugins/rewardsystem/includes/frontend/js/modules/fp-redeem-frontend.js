/*
 * Redeem - Module
 */
jQuery(function ($) {
    'use strict';

    var display_redeem_field = false;

    var RSRedeemFrontend = {
        init: function () {
            RSRedeemFrontend.trigger_on_page_load();
            $(document).on('click', '#mainsubmi', this.validation_in_cart_and_checkout);
            $(document).on('click', '.woocommerce-info a.redeemit', this.toggle_redeem_field);
            $('.checkout_redeeming').css("display", "none");

            //Removed coupon action
            $(document).on('removed_coupon', this.removed_coupon_action);
            $(document).on('updated_wc_div', this.toggle_redeeming_coupon_field_in_cart);
            $(document).on('change', '.rs-predefined-button-wrapper select', this.predefined_dropdown_redeeming);
            $('.rs-predefined-button-wrapper select').change();

            $(document).on('updated_cart_totals', function (e) {
                RSRedeemFrontend.srp_apply_coupon();
            });
        },
        trigger_on_page_load: function () {
            // Hide Current Available points message.            
            $('.woocommerce').find('.rs_hide_available_points_info').hide();
            RSRedeemFrontend.srp_apply_coupon();
        },
        srp_apply_coupon() {
            var data = ({
                action: 'rp_apply_coupon',
                sumo_security: fp_redeem_frontend_params.apply_coupon
            });
            $.post(frontendscripts_params.ajaxurl, data, function (res) {
                if ('hide' === res['coupon_field']) {
                    $('#coupon_code').closest('div').find('.woocommerce-form-coupon-toggle').hide();
                } else {
                    $('#coupon_code').closest('div').find('.woocommerce-form-coupon-toggle').show();
                }

                if ('hide' === res['cart_redeem']) {
                    $('.fp_apply_reward').hide();
                } else {
                    $('.fp_apply_reward').show();
                }

                if ('hide' === res['checkout_redeem']) {
                    $('.fp_apply_reward').hide();
                } else {
                    $('.fp_apply_reward').show();
                }

            });
        },
        removed_coupon_action: function (event, coupon) {
            event.preventDefault();
            if (coupon == fp_redeem_frontend_params.redeeming_coupon || coupon == fp_redeem_frontend_params.auto_redeeming_coupon) {
                display_redeem_field = true;
            } else {
                display_redeem_field = false;
            }
        },
        toggle_redeeming_coupon_field_in_cart: function () {
            if (display_redeem_field) {
                $('.fp_apply_reward').show();
                $('.rs_button_redeem_cart').show();
            }
        },
        toggle_redeem_field: function () {
            $('.checkout_redeeming').toggle();
        },
        validation_in_cart_and_checkout: function () {
            var availablepoints = parseFloat(fp_redeem_frontend_params.available_points);
            availablepoints = Math.round(availablepoints * 100) / 100;
            var minredeempoint = parseFloat(fp_redeem_frontend_params.minredeempoint);
            minredeempoint = Math.round(minredeempoint * 100) / 100;
            var maxredeempoint = parseFloat(fp_redeem_frontend_params.maxredeempoint);
            maxredeempoint = Math.round(maxredeempoint * 100) / 100;
            var getvalue = jQuery('#rs_apply_coupon_code_field').val();

            if ('2' === fp_redeem_frontend_params.redeemingfieldtype) {
                return true;
            }

            if (getvalue === '') {
                jQuery('.rs_warning_message').html(fp_redeem_frontend_params.emptyerr);
                return false;
            } else if (getvalue > availablepoints) {
                jQuery('.rs_warning_message').html(fp_redeem_frontend_params.maxredeemederr);
                return false;
            } else if (jQuery.isNumeric(getvalue) == true) {
                if (getvalue < 0) {
                    jQuery('.rs_warning_message').html(fp_redeem_frontend_params.numericerr);
                    return false;
                }
            }

            if (fp_redeem_frontend_params.minredeempoint == fp_redeem_frontend_params.maxredeempoint) {
                if (getvalue < minredeempoint) {
                    jQuery('.rs_warning_message').html(fp_redeem_frontend_params.minmaxerr);
                    return false;
                } else if (getvalue > maxredeempoint) {
                    jQuery('.rs_warning_message').html(fp_redeem_frontend_params.minmaxerr);
                    return false;
                }
            }

            if (fp_redeem_frontend_params.minredeempoint != '') {
                if (getvalue < minredeempoint) {
                    jQuery('.rs_warning_message').html(fp_redeem_frontend_params.minerr);
                    return false;
                }
            }

            if (fp_redeem_frontend_params.maxredeempoint != '') {
                if (getvalue > maxredeempoint) {
                    jQuery('.rs_warning_message').html(fp_redeem_frontend_params.maxerr);
                    return false;
                }
            }

        },
        predefined_dropdown_redeeming: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if ('0' != $this.val()) {
                $this.closest('.fp_apply_reward').find('#rs_apply_coupon_code_field').val($this.val());
            } else {
                $this.closest('.fp_apply_reward').find('#rs_apply_coupon_code_field').val('');
            }
        },
    };
    RSRedeemFrontend.init();
});
