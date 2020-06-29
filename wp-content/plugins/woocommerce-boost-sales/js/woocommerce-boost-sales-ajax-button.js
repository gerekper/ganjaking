'use strict';
jQuery(document).ready(function () {

    jQuery('button[name="add-to-cart"]:not(.wbs-ajax-add-to-cart)').each(function () {
        if (jQuery(this).closest('.woocommerce-boost-sales').length > 0) {

        } else if (jQuery(this).hasClass('wbs-ajax-add-to-cart')) {

        } else {
            jQuery(this).remove();
        }
    });
    jQuery('button.single_add_to_cart_button:not(.wbs-ajax-add-to-cart)').each(function () {
        if (jQuery(this).closest('.woocommerce-boost-sales').length > 0) {

        } else {
            jQuery(this).remove();
        }
    });
    if (jQuery('[name="variation_id"]').length > 0) {
        jQuery('[name="variation_id"]').each(function () {
            var variation_el = jQuery(this);
            var variation_val = variation_el.val();
            if (parseInt(variation_val) > 0 && variation_val) {
                variation_el.closest('form.cart').find('.wbs-ajax-add-to-cart').removeClass('disabled');
            } else {
                variation_el.closest('form.cart').find('.wbs-ajax-add-to-cart').addClass('disabled');
            }
            variation_el.on('change', function () {
                var variation_val = jQuery(this).val();
                if (parseInt(variation_val) > 0 && variation_val) {
                    jQuery(this).closest('form.cart').find('.wbs-ajax-add-to-cart').removeClass('disabled');
                } else {
                    jQuery(this).closest('form.cart').find('.wbs-ajax-add-to-cart').addClass('disabled');
                }
            })
        })
    }

    if (typeof wbs_wacv !== 'undefined') {
        if (wbs_wacv.compatible && document.cookie.search(/wacv_get_email/i) > 0) {
            ajaxATCBtn();
        }
    } else {
        ajaxATCBtn();
    }

    function ajaxATCBtn() {
        jQuery('.wbs-ajax-add-to-cart').on('click', function (e) {
            e.preventDefault();

            var form = jQuery(this).closest('.cart');
            var variation = form.find('input[name="variation_id"]');
            if (variation.length > 0) {
                if (parseInt(variation.val()) < 1 || !variation.val()) {
                    return;
                }
                var variation_title_field = jQuery('#wbs-content-upsells').find('.upsell-title');
                var variation_title = variation_title_field.html();
                var variations_select = form.find('.variations').find('select');
                if (variations_select.length > 0) {
                    variation_title += ' - ';
                    var attribute_name = [];
                    variations_select.map(function () {
                        attribute_name.push(jQuery(this).val());
                    });
                    variation_title += attribute_name.join(', ');
                    variation_title_field.html(variation_title);
                }
            }
            jQuery(this).addClass('loading');
            var form_data = form.serialize();
            var button = jQuery(this);
            button.closest('form').find('.added_to_cart').remove();
            jQuery.ajax({
                type: 'POST',
                data: 'action=wbs_ajax_add_to_cart&' + form_data,
                url: wboostsales_ajax_url,
                success: function (response1) {
                    if (response1) {
                        refresh_cart_fragment();
                        if (response1.html) {
                            var wbs_notices = jQuery('.wbs-add-to-cart-notices-ajax').html();
                            jQuery('.wbs-add-to-cart-notices-ajax').html(wbs_notices + response1.html);
                        }
                        if (response1.hasOwnProperty('variation_image_url') && response1.variation_image_url) {
                            jQuery('#wbs-content-upsells').find('.wbs-p-image').find('img').attr('src', response1.variation_image_url);
                        }
                        if (response1.hasOwnProperty('total') && response1.total) {
                            jQuery('#wbs-content-upsells').find('.wbs-current_total_cart').html(response1.total);
                        }
                        if (wbs_add_to_cart_params.hasOwnProperty('auto_open_cart') && wbs_add_to_cart_params.auto_open_cart) {
                            if (!jQuery('.xoo-wsc-modal').hasClass('xoo-wsc-active')) {
                                jQuery('.xoo-wsc-basket').click();
                            }
                        }
                        button.after(' <a href="' + wbs_add_to_cart_params.cart_url + '" class="added_to_cart wc-forward" title="' +
                            wbs_add_to_cart_params.i18n_view_cart + '">' + wbs_add_to_cart_params.i18n_view_cart + '</a>');
                        button.removeClass('loading');
                        button.addClass('added');
                        if (response1.hasOwnProperty('discount_bar_html')) {
                            let discount_bar_html = response1.discount_bar_html;
                            if (discount_bar_html.hasOwnProperty('code')) {
                                if (discount_bar_html.code == 200) {
                                    jQuery('.vi-wbs-headline').css({'visibility': 'visible'}).animate({'opacity': 1}, 300);
                                    jQuery('#wbs-content-discount-bar').html(discount_bar_html.html).css({'position': 'fixed'}).fadeIn(200);
                                } else if (discount_bar_html.code == 201) {
                                    jQuery('.vi-wbs-headline').css({'visibility': 'visible'}).animate({'opacity': 1}, 300);
                                    jQuery('#wbs-content-discount-bar').html(discount_bar_html.html).css({'position': ''}).fadeIn(200);
                                }
                            }
                        }
                    }
                },
                error: function (html) {
                    button.removeClass('loading');
                }
            });
        });
    }

    /* Named callback for refreshing cart fragment */
    function refresh_cart_fragment() {
        jQuery(document.body).trigger('wc_fragment_refresh');
    }
});
