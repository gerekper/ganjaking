'use strict';
jQuery(document).ready(function () {
    jQuery(document).on('click', '#wbs-content-discount-bar .wbs-overlay', function () {
        jQuery('#wbs-content-discount-bar').fadeOut(200);
    });
    jQuery('body').on('click', '.wbs-button-continue-stay', function (e) {
        e.preventDefault();
        jQuery(this).closest('.woocommerce-boost-sales').find('.wbs-close').click();
    });
    if (typeof woocommerce_boost_sales_params !== 'undefined') {
        var woocommerce_boost_sales_cross_sells = jQuery('.wbs-crosssells');
        if (woocommerce_boost_sales_cross_sells.length > 0) {
            var bundle_selects = woocommerce_boost_sales_cross_sells.find('select');
            if (bundle_selects.length > 0) {
                woocommerce_boost_sales_cross_sells.find('.wbs-single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
                bundle_selects.on('change', function () {
                    var enable_add_to_cart = true;
                    for (var i = 0; i < bundle_selects.length; i++) {
                        if (bundle_selects.eq(i).val() == '') {
                            enable_add_to_cart = false;
                            break;
                        }
                    }
                    if (enable_add_to_cart) {
                        woocommerce_boost_sales_cross_sells.find('.wbs-single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
                    } else {
                        woocommerce_boost_sales_cross_sells.find('.wbs-single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
                    }
                })
            }
            if (woocommerce_boost_sales_params.wc_hide_out_of_stock === 'yes') {
                var selectedAttributes = 0;
                woocommerce_boost_sales_cross_sells.find('.wbs-variations_form').map(function () {
                    var current_form = jQuery(this);
                    var $current_product = current_form.closest('.wbs-product');
                    var $current_product_image = $current_product.find('.product-image').find('img');
                    var current_product_image = $current_product_image.attr('src');
                    var current_form_selects = current_form.find('select');
                    var current_form_data = current_form.data('product_variations');
                    if (current_form_data.length > 0) {
                        var selected = {};
                        current_form_selects.map(function () {
                            var attribute_name = jQuery(this).data('attribute_name');
                            var selectedOption = jQuery(this).find('option[selected="selected"]').val();
                            if (selectedOption !== undefined) {
                                selected[attribute_name] = selectedOption
                            }
                            for (var i = 0; i < current_form_data.length; i++) {
                                var attributes = current_form_data[i]['attributes'];
                                jQuery(this).find('option[value="' + attributes[attribute_name] + '"]').prop('disabled', false).removeClass('wbs-disabled-option');
                            }
                        });
                        if (!jQuery.isEmptyObject(selected)) {
                            if (Object.keys(selected).length === current_form_selects.length) {
                                var sorted_variation_attributes = wbs_sort_object(selected);
                                var variation_attributes_json = JSON.stringify(sorted_variation_attributes);
                                for (var i = 0; i < current_form_data.length; i++) {
                                    var attributes = wbs_sort_object(current_form_data[i]['attributes']);
                                    if (JSON.stringify(attributes) === variation_attributes_json) {
                                        var variation_image = current_form_data[i]['image'];
                                        if (variation_image.hasOwnProperty('variation_image') && variation_image.thumb_src) {
                                            $current_product_image.attr('src', variation_image.thumb_src);
                                        } else if (variation_image.hasOwnProperty('url') && variation_image.url) {
                                            $current_product_image.attr('src', variation_image.url);
                                        }
                                        break;
                                    }
                                }
                            }

                            selectedAttributes += current_form_selects.length;
                            var clearSelected = true;
                            for (var i in current_form_data) {
                                if (current_form_data.hasOwnProperty(i)) {
                                    if (JSON.stringify(current_form_data[i]['attributes']) === JSON.stringify(selected)) {
                                        clearSelected = false;
                                        break;
                                    }
                                }
                            }
                            if (clearSelected) {
                                selectedAttributes -= current_form_selects.length;
                                current_form_selects.map(function () {
                                    jQuery(this).val('');
                                })
                            }
                        }
                    }

                    current_form_selects.on('change', function (val) {
                        var selected_value = jQuery(this).val();
                        var selected_attribute_name = jQuery(this).data('attribute_name');
                        if (selected_value) {
                            var variation_attributes = {};
                            variation_attributes[selected_attribute_name] = selected_value;
                            current_form_selects.not(jQuery(this)).map(function () {
                                var attribute_name = jQuery(this).data('attribute_name');
                                var current_attribute = jQuery(this).val();
                                if (current_attribute) {
                                    variation_attributes[attribute_name] = current_attribute;
                                }
                                jQuery(this).find('option').not('option[value=""]').prop('disabled', true).addClass('wbs-disabled-option');
                                for (var i = 0; i < current_form_data.length; i++) {
                                    var attributes = current_form_data[i]['attributes'];
                                    if (attributes[selected_attribute_name] === selected_value) {
                                        jQuery(this).find('option[value="' + attributes[attribute_name] + '"]').prop('disabled', false).removeClass('wbs-disabled-option');
                                    }
                                }
                            });
                            if (Object.keys(variation_attributes).length === current_form_selects.length) {
                                var sorted_variation_attributes = wbs_sort_object(variation_attributes);
                                var variation_attributes_json = JSON.stringify(sorted_variation_attributes);
                                for (var i = 0; i < current_form_data.length; i++) {
                                    var attributes = wbs_sort_object(current_form_data[i]['attributes']);
                                    if (JSON.stringify(attributes) === variation_attributes_json) {
                                        var variation_image = current_form_data[i]['image'];
                                        if (variation_image.hasOwnProperty('variation_image') && variation_image.thumb_src) {
                                            $current_product_image.attr('src', variation_image.thumb_src);
                                        } else if (variation_image.hasOwnProperty('url') && variation_image.url) {
                                            $current_product_image.attr('src', variation_image.url);
                                        }
                                        break;
                                    }
                                }
                            }
                        } else {
                            current_form_selects.not(jQuery(this)).map(function () {
                                var attribute_name = jQuery(this).data('attribute_name');
                                jQuery(this).find('option').not('option[value=""]').prop('disabled', true).addClass('wbs-disabled-option');
                                for (var i = 0; i < current_form_data.length; i++) {
                                    var attributes = current_form_data[i]['attributes'];
                                    jQuery(this).find('option[value="' + attributes[attribute_name] + '"]').prop('disabled', false).removeClass('wbs-disabled-option');
                                }
                            });
                            if (current_product_image) {
                                $current_product_image.attr('src', current_product_image);
                            }
                        }
                    })
                });
                if (selectedAttributes === bundle_selects.length) {
                    woocommerce_boost_sales_cross_sells.find('.wbs-single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
                } else {
                    woocommerce_boost_sales_cross_sells.find('.wbs-single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
                }
            }
        }

        var side_cart_auto_open = woocommerce_boost_sales_params.side_cart_auto_open;
        if (woocommerce_boost_sales_params.ajax_add_to_cart_for_upsells === 'yes') {
            submit_form_upsell(side_cart_auto_open);
        }
        if (woocommerce_boost_sales_params.ajax_add_to_cart_for_crosssells === 'yes') {
            submit_form_crosssell(side_cart_auto_open);
        }
    }
    woo_boost_sale.init();
    woo_boost_sale.add_to_cart();
    var time_redirect = jQuery('.woocommerce-boost-sales').attr('data-time_rdt');
    if (time_redirect) {
        woo_boost_sale.counter(jQuery('#wbs_time_rdt'), time_redirect);
    }
});

function wbs_sort_object(object) {
    return Object.keys(object).sort().reduce((a, c) => (a[c] = object[c], a), {});
}

function submit_form_upsell(side_cart_auto_open) {
    jQuery('#wbs-content-upsells').unbind().on('submit', '.cart,.variations_form cart,.woocommerce-boost-sales-cart-form', function (e) {
        e.preventDefault();
        var data = jQuery(this).serializeArray();
        var data1 = jQuery(this).data();
        var button = jQuery(this).find('button[type="submit"]');
        var product_id = button.val() ? button.val() : data1['product_id'];
        var container = jQuery(this).parent().parent().parent();
        var container_mobile = jQuery(this).parent().parent().parent().parent().parent();
        var item_height = container_mobile.find('.wbs-upsells-item-main').css('height');
        container_mobile.find('.wbs-upsells-item-main').css({'max-height': item_height});
        button.attr('disabled', 'disabled').addClass('wbs-loading');
        data.push({name: button.attr('name'), value: button.val()});
        jQuery.ajax({
            url: jQuery(this).attr('action'),
            type: jQuery(this).attr('method'),
            data: data,
            success: function (response) {
                container.find('.wbs-upsells-add-items').html('<span class="wbs-icon-added"></span> ' + woocommerce_boost_sales_params.i18n_added_to_cart);
                container_mobile.addClass('wbs-upsells-item-added');
                button.removeAttr('disabled').removeClass('wbs-loading');
                jQuery('body').trigger("wc_fragment_refresh");
                if (1 == side_cart_auto_open && !jQuery('.xoo-wsc-modal').hasClass('xoo-wsc-active')) {
                    jQuery('.xoo-wsc-basket').click();
                }
            },
            error: function (err) {
                button.removeAttr('disabled');
            }
        });
    });
}

function submit_form_crosssell(side_cart_auto_open) {
    jQuery('#wbs-content-cross-sells').unbind().on('submit', '.woocommerce-boost-sales-cart-form', function (e) {
        e.preventDefault();
        var data = jQuery(this).serializeArray();
        var button = jQuery(this).find('button[type="submit"]');
        var product_id = button.parent().find('input[name="add-to-cart"]').val();
        button.attr('disabled', 'disabled');
        data.push({name: button.attr('name'), value: button.val()});
        jQuery('.wbs-content-crossell').addClass('wbs-adding-to-cart');
        jQuery.ajax({
            url: jQuery(this).attr('action'),
            type: jQuery(this).attr('method'),
            data: data,
            success: function (response) {
                button.removeAttr('disabled');
                jQuery('body').trigger("wc_fragment_refresh");
                jQuery('.wbs-content-crossell').addClass('wbs-added-to-cart');
                if (1 == side_cart_auto_open && !jQuery('.xoo-wsc-modal').hasClass('xoo-wsc-active')) {
                    jQuery('.xoo-wsc-basket').click();
                }
                setTimeout(function () {
                    jQuery('#wbs-content-cross-sells').fadeOut(200);
                    jQuery('.gift-button').fadeOut(200);
                    jQuery('html').removeClass('wbs-html-overflow');
                    jQuery('.wbs-content-crossell').removeClass('wbs-adding-to-cart').removeClass('wbs-added-to-cart');
                }, 2000);
            },
            error: function (err) {
                button.removeAttr('disabled');
            }
        });
    });


    jQuery('#wbs-content-cross-sells-product-single').css({'max-height': jQuery('#wbs-content-cross-sells-product-single').css('height')}).unbind().on('submit', '.woocommerce-boost-sales-cart-form', function (e) {
        e.preventDefault();
        var data = jQuery(this).serializeArray();
        var button = jQuery(this).find('button[type="submit"]');
        button.attr('disabled', 'disabled');
        data.push({name: button.attr('name'), value: button.val()});
        jQuery.ajax({
            url: jQuery(this).attr('action'),
            type: jQuery(this).attr('method'),
            data: data,
            success: function (response) {
                button.removeAttr('disabled');
                jQuery('body').trigger("wc_fragment_refresh");
                jQuery('.wbs-content-cross-sells-product-single-container').addClass('wbs-added-to-cart');
                if (1 == side_cart_auto_open && !jQuery('.xoo-wsc-modal').hasClass('xoo-wsc-active')) {
                    jQuery('.xoo-wsc-basket').click();
                }
            },
            error: function (err) {
                button.removeAttr('disabled');
            }
        });
    });
}

var time_redirect;
var cross_sell_init;
var woo_boost_sale = {
    hide_crosssell_init: 0,
    check_quantity: 0,
    init: function () {
        if (typeof wbs_add_to_cart_params == 'undefined' || parseInt(wbs_add_to_cart_params.ajax_button) != 1) {
            this.slider();
        } else if (wbs_add_to_cart_params.submit == 1) {
            this.slider();
        }

        this.product_variation();


        woo_boost_sale.hide();

        if (!this.hide_crosssell_init) {
            this.initial_delay_icon();
        }
        jQuery('.gift-button').on('click', function () {

            // jQuery(document).scrollTop(0);
            //woo_boost_sale.hide_upsell();
            woo_boost_sale.show_cross_sell();
            //woo_boost_sale.slider_cross_sell();
            jQuery('.vi-wbs-headline').removeClass('wbs-crosssell-message').addClass('wbs-crosssell-message');

        });

        /*Cross sells below add to cart button*/
        if (jQuery('#wbs-content-cross-sells-product-single .wbs-crosssells').length > 0) {
            this.cross_slider();
        }
        jQuery('.woocommerce-boost-sales.wbs-content-up-sell .single_add_to_cart_button').unbind();

        if (jQuery('.wbs-msg-congrats').length > 0) {

            var time = jQuery('.wbs-msg-congrats').attr('data-time');
            if (time) {
                woo_boost_sale.counter(jQuery('.auto-redirect span'), time);
            }

        }


        jQuery('#wbs-gift-button-cat').on('click', function () {
            woo_boost_sale.hide_upsell();
            woo_boost_sale.show_cross_sell_archive();
        });

        if (jQuery('.vi-wbs-topbar').hasClass('wbs_top_bar')) {
            var windowsize = jQuery(window).width();
            jQuery('.vi-wbs-headline').css('top', '50px');
            if (windowsize >= 1366) {
                jQuery('.wbs-archive-upsells .wbs-content').css('margin-top', '45px');
            } else {
                jQuery('.wbs-archive-upsells .wbs-content').css('margin-top', '85px');
            }
        }

        if (jQuery('.vi-wbs-topbar').hasClass('wbs_bottom_bar')) {
        } else {
            var windowsize = jQuery(window).width();
            if (windowsize < 1366) {
                jQuery('.wbs-archive-upsells .wbs-content').css('margin-top', '70px');
            }
            if (windowsize < 640) {
                jQuery('.wbs-archive-upsells .wbs-content').css('margin-top', '0px');
            }
        }
        if (jQuery('.wbs-message-success').length < 1) {
            jQuery('.wbs-content-up-sell').css('height', '100%');
        }
        if (jQuery('.wbs-content').hasClass('wbs-msg-congrats')) {
            setTimeout(function () {
                jQuery('.vi-wbs-headline').show();
            }, 0);
        }

        jQuery(document).on('click', '.vi-wbs_progress_close', function () {
            jQuery('.vi-wbs-topbar').fadeOut('slow');
        });

        if (!jQuery('#flexslider-cross-sell .vi-flex-prev').hasClass('vi-flex-disabled')) {
            jQuery('#flexslider-cross-sell').hover(function () {
                jQuery('#flexslider-cross-sell .vi-flex-prev').css("opacity", "1");
            }, function () {
                jQuery('#flexslider-cross-sell .vi-flex-prev').css("opacity", "0");
            });
        }

        if (!jQuery('#flexslider-cross-sell .vi-flex-next').hasClass('vi-flex-disabled')) {
            jQuery('#flexslider-cross-sell').hover(function () {
                jQuery('#flexslider-cross-sell .vi-flex-next').css("opacity", "1");
            }, function () {
                jQuery('#flexslider-cross-sell .vi-flex-next').css("opacity", "0");
            });
        }

        /*Smooth Archive page*/
        jQuery('.wbs-wrapper').animate({
            opacity: 1
        }, 200);


        woo_boost_sale.chosen_variable_upsell();
        jQuery('.wbs-upsells > .wbs-').find('div.vi-wbs-chosen:first').removeClass('wbs-hidden-variable').addClass('wbs-show-variable');

    },
    product_variation: function () {
        var form_variation = jQuery('#wbs-content-upsells').find('.wbs-variations_form');
        form_variation.each(function () {
            // jQuery(this).addClass('variations_form');
            jQuery(this).wc_variation_form();
        });
        jQuery('#wbs-content-upsells').on('check_variations', function () {
            jQuery(this).find('.variations_button').each(function () {
                if (jQuery(this).hasClass('woocommerce-variation-add-to-cart-disabled')) {
                    jQuery(this).find('.wbs-single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
                } else {
                    jQuery(this).find('.wbs-single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
                }
            });
        });

        jQuery('#wbs-content-upsells').on('show_variation', function () {
            jQuery(this).find('.variations_button').each(function () {
                if (jQuery(this).hasClass('woocommerce-variation-add-to-cart-disabled')) {
                    jQuery(this).find('.wbs-single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
                } else {
                    jQuery(this).find('.wbs-single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
                }
            })
        });
        jQuery('.wbs-single_add_to_cart_button').on('click', function (e) {
            if (jQuery(this).is('.disabled')) {
                e.preventDefault();

                if (jQuery(this).hasClass('wc-variation-is-unavailable')) {
                    window.alert(wc_add_to_cart_variation_params.i18n_unavailable_text);
                } else if (jQuery(this).hasClass('wc-variation-selection-needed')) {
                    window.alert(wc_add_to_cart_variation_params.i18n_make_a_selection_text);
                }
            }
        })
    },
    add_to_cart: function () {
        var check_quantity = 0;
        if (jQuery('.wbs-content-up-sell').length > 0) {
            jQuery(document).ajaxSend(function (event, jqxhr, settings) {
                var ajax_link = settings.url;
                var data_post = settings.data;
                var product_id = 0;
                var quantity = 0;
                var variation_id = 0;
                var check_variation = 0;

                if (data_post == '' || data_post == null || jQuery.isEmptyObject(data_post)) {
                    return;
                }
                var data_process = data_post.split('&');

                for (var i = 0; i < data_process.length; i++) {
                    if (data_process[i].search(/product_id/i) >= 0) {
                        product_id = data_process[i];
                    }
                    if (data_process[i].search(/variation_id/i) >= 0) {
                        variation_id = data_process[i];
                        check_variation = 1;
                    }
                    if (data_process[i].search(/quantity/i) >= 0) {
                        quantity = data_process[i];
                    }
                }
                /*Reformat Product ID*/
                if (check_variation) {
                    if (variation_id) {
                        product_id = variation_id.replace(/^\D+/g, '');
                        product_id = parseInt(product_id);
                    } else {
                        return;
                    }
                } else {
                    if (product_id) {
                        product_id = product_id.replace(/^\D+/g, '');
                        product_id = parseInt(product_id);
                    } else {
                        return;
                    }
                }
                if (ajax_link.search(/wc-ajax=add_to_cart/i) >= 0 || data_post.search(/action=wbs_ajax_add_to_cart/i) >= 0) {
                    if (typeof wbs_add_to_cart_params == 'undefined' || parseInt(wbs_add_to_cart_params.ajax_button) != 1) {
                        jQuery('.wbs-content-up-sell').html('<div class="wbs-overlay"><div class="wbs-loading"></div>').fadeIn(200);
                    } else {
                        /*Reformat Quantity*/
                        if (quantity) {
                            quantity = quantity.replace(/^\D+/g, '');
                            quantity = parseInt(quantity);
                        } else {
                            quantity = 1;
                        }
                        var min_qty, max_qty, p_index;

                        if (wbs_add_to_cart_params.product_type == 'variable') {
                            for (var k_id in wbs_add_to_cart_params.products) {
                                var variation_id = parseInt(wbs_add_to_cart_params.products[k_id].variation_id);
                                if (product_id == variation_id) {
                                    min_qty = parseInt(wbs_add_to_cart_params.products[k_id].min_qty);
                                    max_qty = parseInt(wbs_add_to_cart_params.products[k_id].max_qty);
                                    p_index = k_id;
                                }
                            }
                        } else {
                            min_qty = parseInt(wbs_add_to_cart_params.products[0].min_qty);
                            max_qty = parseInt(wbs_add_to_cart_params.products[0].max_qty);
                            p_index = 0;
                        }

                        /*Check quantity*/

                        if (min_qty && min_qty > 0) {

                            if (quantity < min_qty) {
                                check_quantity = 1;
                            }
                        }
                        if (max_qty && max_qty > 0) {

                            if (quantity > max_qty) {
                                check_quantity = 1;
                            }
                        }
                        if (check_quantity == 0) {
                            var p_price = wbs_add_to_cart_params.products[p_index].display_price;
                            var title_ext, img;


                            if (wbs_add_to_cart_params.products[p_index].attributes && wbs_add_to_cart_params.products[p_index].attributes != 'undefined') {
                                var attributes = Object.keys(wbs_add_to_cart_params.products[p_index].attributes).map(function (key) {
                                    return jQuery('select[name="' + key + '"]').length > 0 ? jQuery('select[name="' + key + '"]').val() : wbs_add_to_cart_params.products[p_index].attributes[key];
                                });
                                attributes = attributes.map(function (str) {
                                    return str.charAt(0).toUpperCase() + str.slice(1);

                                });
                                title_ext = attributes.join(", ");

                            }
                            if (title_ext) {
                                jQuery('.wbs-p-title').find('.wbs-p-url').html(jQuery('.wbs-p-title').find('.wbs-p-url').html() + ' - ' + title_ext);
                            }
                            /*Override quantity*/
                            jQuery('.wbs-p-quantity').find('.wbs-p-quantity-number').text(quantity);
                            /*Override Total*/

                            var total_html = jQuery('.wbs-price-total').find('.woocommerce-Price-amount').contents();


                            if (wbs_add_to_cart_params.products[p_index].display_price > 0) {
                                var total = quantity * wbs_add_to_cart_params.products[p_index].display_price;
                                total_html.filter(function (index) {

                                    return this.nodeType == 3;
                                }).each(function () {
                                    var text = encodeURI(this.textContent);
                                    var trim_text = this.textContent.trim();
                                    text = text.replace(trim_text, total);
                                    this.textContent = decodeURI(text);
                                });
                            }
                            jQuery('.vi-wbs-headline').css({'visibility': 'hidden', 'opacity': 0});
                            jQuery('.woocommerce-boost-sales.wbs-content-up-sell').css({'opacity': 0});
                            jQuery('.woocommerce-boost-sales.wbs-content-up-sell').css({
                                'display': 'flex',
                                'visibility': 'visible'
                            }).animate({'opacity': 1}, 300);
                            if (wbs_add_to_cart_params.submit == 0) {
                                woo_boost_sale.slider();
                            }
                            if (jQuery('.wbs-archive-upsells').length > 0) {
                                jQuery('html').addClass('wbs-html-overflow');
                            }
                            clearTimeout(cross_sell_init);

                            woo_boost_sale.hide_cross_sell();
                        }
                    }
                }
            });
            jQuery(document).ajaxComplete(function (event, jqxhr, settings) {
                var ajax_link = settings.url;
                var data_post = settings.data;
                var product_id = 0;
                var quantity = 0;
                var variation_id = 0;
                var check_variation = 0;

                if (data_post == '' || data_post == null || jQuery.isEmptyObject(data_post)) {
                    return;
                }
                var data_process = data_post.split('&');

                /*Process get Product ID - Require product_id*/
                for (var i = 0; i < data_process.length; i++) {
                    if (data_process[i].search(/product_id/i) >= 0) {
                        product_id = data_process[i];
                    } else if (data_process[i].search(/add-to-cart/i) >= 0) {
                        product_id = data_process[i];
                    }
                    if (data_process[i].search(/variation_id/i) >= 0) {
                        variation_id = data_process[i];
                        check_variation = 1;
                    }
                    if (data_process[i].search(/quantity/i) >= 0) {
                        quantity = data_process[i];
                    }
                }

                /*Reformat Product ID*/
                if (check_variation) {
                    if (variation_id) {
                        product_id = variation_id.replace(/^\D+/g, '');
                        product_id = parseInt(product_id);
                    } else {
                        return;
                    }
                } else {
                    if (product_id) {
                        product_id = product_id.replace(/^\D+/g, '');
                        product_id = parseInt(product_id);
                    } else {
                        return;
                    }
                }
                /*Reformat Quantity*/
                if (quantity) {
                    quantity = quantity.replace(/^\D+/g, '');
                    quantity = parseInt(quantity);
                } else {
                    quantity = 1;
                }
                if (ajax_link.search(/wc-ajax=add_to_cart/i) >= 0 || data_post.search(/action=wbs_ajax_add_to_cart/i) >= 0 || data_post.search(/action=wacv_ajax_add_to_cart/i) >= 0 || data_post.search(/action=woofc_update_cart/i) >= 0) {
                    if (typeof wbs_add_to_cart_params == 'undefined' || parseInt(wbs_add_to_cart_params.ajax_button) != 1) {
                        jQuery.ajax({
                            type: 'POST',
                            data: 'action=wbs_get_product' + '&id=' + product_id + '&quantity=' + quantity,
                            url: wboostsales_ajax_url,
                            success: function (response) {
                                if (response.upsells_html) {
                                    if (response.upsells_html.search(/wbs-overlay/i) < 1) {
                                        jQuery('html').removeClass('wbs-html-overflow');
                                        jQuery('.vi-wbs-topbar').animate({opacity: 1}, 500);
                                    }
                                    var time_redirect = jQuery('.woocommerce-boost-sales').attr('data-time_rdt');
                                    jQuery('.wbs-content-up-sell').html(response.upsells_html);
                                    jQuery('.wbs-content-up-sell').fadeIn();
                                    woo_boost_sale.hide_crosssell_init = 1;
                                    woo_boost_sale.init();
                                    woo_boost_sale.slider();
                                    setTimeout(function () {
                                        jQuery('.wbs-wrapper').animate({
                                            opacity: 1
                                        }, 200);
                                    }, 200);
                                } else {
                                    woo_boost_sale.hide();
                                    jQuery('.wbs-overlay').click();
                                }
                                let discount_bar_html = response.discount_bar_html;
                                if (discount_bar_html.hasOwnProperty('code')) {
                                    if (discount_bar_html.code == 200) {
                                        jQuery('#wbs-content-discount-bar').html(discount_bar_html.html).css({'position': 'fixed'}).fadeIn(200);
                                    } else if (discount_bar_html.code == 201) {
                                        jQuery('#wbs-content-discount-bar').html(discount_bar_html.html).css({'position': ''}).fadeIn(200);
                                    }
                                }
                            },
                            error: function (error) {
                                jQuery('html').removeClass('wbs-html-overflow');
                            }
                        });
                    } else {
                        if (check_quantity == 1) {
                            window.location.reload();
                        } else {
                            // jQuery.ajax({
                            //     type: 'POST',
                            //     data: 'action=wbs_show_bar&language=' + woocommerce_boost_sales_params.language,
                            //     url: wboostsales_ajax_url,
                            //     success: function (data) {
                            //         if (data !== null) {
                            //             if (data.code == 200) {
                            //                 jQuery('#wbs-content-discount-bar').html('');
                            //                 jQuery('#wbs-content-upsells').html(data.html).css({'visibility': 'visible'}).animate({'opacity': 1}, 300);
                            //                 jQuery('.vi-wbs-headline').css({'visibility': 'visible'}).animate({'opacity': 1}, 300);
                            //                 woo_boost_sale.hide();
                            //             } else if (data.code == 201) {
                            //                 jQuery('#wbs-content-discount-bar').html(data.html).css({
                            //                     'visibility': 'visible',
                            //                     'display': 'flex'
                            //                 }).animate({'opacity': 1}, 300);
                            //                 jQuery('.vi-wbs-headline').css({'visibility': 'visible'}).animate({'opacity': 1}, 300);
                            //                 woo_boost_sale.hide();
                            //             } else {
                            //                 if (jQuery('.wbs-archive-upsells').length < 1) {
                            //                     jQuery('html').removeClass('wbs-html-overflow');
                            //                 }
                            //             }
                            //         }
                            //     },
                            //     error: function (data) {
                            //     }
                            // });

                        }
                    }
                }
            });
        }
    },
    hide: function () {
        jQuery('.wbs-close, .woocommerce-boost-sales .wbs-overlay').unbind();
        jQuery('.wbs-close, .woocommerce-boost-sales .wbs-overlay').on('click', function () {
            jQuery('.woocommerce-boost-sales').not('.woocommerce-boost-sales-active-discount').fadeOut(200);
            jQuery('html').removeClass('wbs-html-overflow');
            clearTimeout(time_redirect);
        });
    },
    slider: function () {
        var windowsize = jQuery(window).width();
        var item_per_row = jQuery('#flexslider-up-sell').attr('data-item-per-row');
        var rtl = jQuery('#flexslider-up-sell').attr('data-rtl');
        if (parseInt(rtl)) {
            rtl = true;
        } else {
            rtl = false;
        }
        if (item_per_row == undefined) {
            item_per_row = 4;
        }
        if (windowsize < 768 && windowsize >= 600) {
            item_per_row = 2;
        }
        if (windowsize < 600) {
            item_per_row = 1;
        }
        /*Up-sells*/
        if (jQuery('#flexslider-up-sell').length > 0) {

            jQuery('#flexslider-up-sell').vi_flexslider({
                namespace: "woocommerce-boost-sales-",
                selector: '.wbs-vi-slides > .wbs-product',
                animation: "slide",
                animationLoop: false,
                itemWidth: 145,
                itemMargin: 12,
                controlNav: false,
                maxItems: item_per_row,
                reverse: false,
                slideshow: false,
                rtl: rtl
            });


            if (jQuery('#wbs-content-upsells').hasClass('wbs-form-submit') || (typeof wbs_add_to_cart_params != 'undefined' && parseInt(wbs_add_to_cart_params.ajax_button) != 1)) {
                jQuery('html').addClass('wbs-html-overflow');
            }
        }

    },
    cross_slider: function () {
        var rtl = jQuery('.wbs-cross-sells').attr('data-rtl');
        var windowsize = jQuery(window).width(),
            min_item = 3,
            max_item = 3,
            cross_sells_single_witdh = jQuery('#flexslider-cross-sells').width();
        if (windowsize < 768 && windowsize >= 600) {
            min_item = 2;
            max_item = 2;
        }
        if (windowsize < 600) {
            min_item = 1;
            max_item = 1;
        }
        if (parseInt(rtl)) {
            rtl = true;
        } else {
            rtl = false;
        }
        if (jQuery('#wbs-content-cross-sells-product-single #flexslider-cross-sells').length > 0) {
            jQuery('#flexslider-cross-sells').vi_flexslider({
                namespace: "woocommerce-boost-sales-",
                selector: '.wbs-cross-sells > .wbs-product',
                animation: "slide",
                animationLoop: false,
                itemWidth: (parseInt(cross_sells_single_witdh / max_item) - 6),
                itemMargin: 6,
                controlNav: false,
                maxItems: max_item,
                slideshow: false,
                rtl: rtl
            });
        } else if (jQuery('#flexslider-cross-sells').length > 0) {
            jQuery('#flexslider-cross-sells').vi_flexslider({
                namespace: "woocommerce-boost-sales-",
                selector: '.wbs-cross-sells > .wbs-product',
                animation: "slide",
                animationLoop: false,
                itemWidth: 145,
                itemMargin: 24,
                controlNav: false,
                maxItems: max_item,
                slideshow: false,
                rtl: rtl
            });
            jQuery('html').addClass('wbs-html-overflow');
        }
    },
    hide_upsell: function () {
        jQuery('.wbs-content').fadeOut(200);
    },
    hide_cross_sell: function () {
        jQuery('#wbs-content-cross-sells').fadeOut(200);
    },
    show_cross_sell: function () {
        jQuery('#wbs-content-cross-sells').fadeIn('slow');
        jQuery('html').addClass('wbs-html-overflow');
        this.cross_slider();
    },
    show_cross_sell_archive: function () {
        jQuery('#wbs-cross-sell-archive').fadeIn('slow');

    },
    counter: function ($el, n) {
        var checkout_url = jQuery('.vi-wbs-btn-redeem').attr('href');
        (function loop() {
            $el.html(n);
            if (n == 0) {
                if (checkout_url) {
                    window.location.href = checkout_url;
                }
            }
            if (n--) {
                time_redirect = setTimeout(loop, 1000);
            }
        })();
    },
    initial_delay_icon: function () {
        if (jQuery('#wbs-content-cross-sells').length > 0) {
            var initial_delay = jQuery('#wbs-content-cross-sells').attr('data-initial_delay');
            var open = jQuery('#wbs-content-cross-sells').attr('data-open');
            cross_sell_init = setTimeout(function () {
                jQuery('.gift-button').fadeIn('medium');
                if (open > 0) {
                    woo_boost_sale.show_cross_sell()
                }
            }, initial_delay * 1000);
        }
    },
    chosen_variable_upsell: function () {
        jQuery('select.wbs-variable').on('change', function () {
            var selected = jQuery(this).val();
            jQuery(this).closest('div.wbs-product').find('.vi-wbs-chosen').removeClass('wbs-show-variable').addClass('wbs-hidden-variable');
            jQuery(this).closest('div.wbs-product').find('.wbs-variation-' + selected).removeClass('wbs-hidden-variable').addClass('wbs-show-variable');
        });
    }
};
