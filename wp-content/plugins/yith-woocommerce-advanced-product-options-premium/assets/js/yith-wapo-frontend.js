/**
 * Frontend
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */
jQuery(document).ready(function ($) {
    "use strict";

    // Fix: textarea freeze after press enter key
    $('textarea.ywapo_input.ywapo_input_textarea').keydown(function (event) {
        if (event.keyCode == 13) {
            var val = this.value;
            if (typeof this.selectionStart == "number" && typeof this.selectionEnd == "number") {
                var start = this.selectionStart;
                this.value = val.slice(0, start) + "\n" + val.slice(this.selectionEnd);
                this.selectionStart = this.selectionEnd = start + 1;
            } else if (document.selection && document.selection.createRange) {
                this.focus();
                var range = document.selection.createRange();
                range.text = "\r\n";
                range.collapse(false);
                range.select();
            }
            return false;
        }
    });

    if (typeof yith_wapo_general === 'undefined') {
        return false;
    }

    var global_select_variations = false;
    var global_product_featured_image = '';
    var global_selected_group_container_image = null;

    /**
     * @author Andrea Frascaspata
     */
    $.fn.init_yith_wapo_totals = function () {

        $('body').on('change', '.yith_wapo_groups_container input, .yith_wapo_groups_container select, .yith_wapo_groups_container textarea, div.quantity > input.qty', function (e) {

            /* CHECK QTY */
            wapo_qty_dependencies();
            /* CHECK QTY */

            var current_selected_element = $(this);
            var group_container = current_selected_element.closest('.ywapo_group_container');

            //------------------------------------------------------------------

            if (typeof current_selected_element.data('pricetype') != 'undefined'
                && (current_selected_element.data('pricetype') == 'calculated_multiplication' || current_selected_element.data('pricetype') == 'calculated_character_count')
                && current_selected_element.val() != '') {

                var $current_cart = current_selected_element.find('form.cart');
                var current_container = current_selected_element.closest('.ywapo_input_container');
                setCalculatedPrice($current_cart, 1, current_selected_element, group_container, current_container, false, true);
            }

            //--------------------------------------------------------------

            $(this).trigger('yith-wapo-product-option-conditional', current_selected_element);
            $(this).trigger('yith-wapo-product-option-update');
            changeFeaturedImage(group_container, $(this).find(':selected'));

        });

        $(this).on('change mouseleave', '.yith_wapo_calculate_quantity input[type="number"]', function (e) {
            'use strict';

            var $total_quantity = 0;
            //var $items = $(this).closest('.ywapo_group_container').find('input[type="number"]');
            var $items = $('body').find('.yith_wapo_calculate_quantity input[type="number"]');

            $items.each(function () {
                var val = $(this).val();
                if (val != '') {
                    $total_quantity += parseInt(val);
                }
            });

            var $form_quantity_input = $(this).closest('form').find('div.quantity > input[name="quantity"]');

            if ($form_quantity_input.closest('.ywcp_component_options_selection_container').length == 0) {
                if ($total_quantity > 0) {
                    $form_quantity_input.val($total_quantity);
                    $form_quantity_input.attr('readonly', 'readonly');
                } else {
                    $form_quantity_input.val($form_quantity_input.attr('min'));
                    $form_quantity_input.removeAttr('readonly');
                }
            }

            updateTotal($(this).closest('form.cart'));

        });

        $(this).on('found_variation', function (event, variation) {

            if ($(event.target).hasClass('ywcp_inner_selected_container') || $(event.target).hasClass('bundled_item_cart_content')) {
                return;
            }

            var yith_wapo_group_total = $('.yith_wapo_group_total');
            var new_product_price = 0;

            global_select_variations = variation;

            if (typeof(variation.display_price) !== 'undefined') {
                new_product_price = variation.display_price;
            } else if ($(variation.price_html).find('.amount:last').length) {
                var $cart = $('.cart');
                new_product_price = $(variation.price_html).find('.amount:last').text();
                new_product_price = getFormattedPrice(new_product_price);
            }

            yith_wapo_group_total.data('product-price', new_product_price);
            yith_wapo_group_total.attr('data-product-price', new_product_price);
            yith_wapo_update_variation_price(variation);
            doConditionalVariationsLoop($(this));
            $(this).trigger('yith-wapo-product-option-update');

        });

        $(this).on('hide_variation', function (event) {
            'use strict';

            if ($(event.target).hasClass('ywcp_inner_selected_container')) {
                return;
            }

            global_select_variations = false;
            doConditionalVariationsLoop($(this));
            $(this).trigger('yith-wapo-product-option-update');

        });

        /**
         *
         * @param variation
         */
        function yith_wapo_update_variation_price(variation) {
            'use strict';

            var master_group_container = $('.yith_wapo_groups_container');

            if (typeof master_group_container != 'undefined') {
                var group_container_list = master_group_container.find('input.ywapo_input.ywapo_price_percentage, select.ywapo_input option.ywapo_price_percentage, textarea.ywapo_input.ywapo_price_percentage');
                var $i = 0;

                group_container_list.each(function () {

                    var current_option = $(this);

                    if (typeof current_option.data('pricetype') != 'undefined' && current_option.data('pricetype') != 'fixed') {

                        var current_container = current_option.closest('.ywapo_input_container');
                        var option_value = current_option.val();

                        $.ajax({
                            url       : yith_wapo_general.wc_ajax_url.toString().replace('%%endpoint%%', 'yith_wapo_update_variation_price'),
                            type      : 'POST',
                            data      : {
                                //action: 'yith_wccl_add_to_cart',
                                variation_id   : variation.variation_id,
                                variation_price: variation.display_price,
                                type_id        : current_option.data('typeid'),
                                option_index   : current_option.data('index'),
                                option_value   : option_value
                            },
                            beforeSend: function () {
                                if ($i == 0) {
                                    showLoader(master_group_container);
                                }

                            },
                            success   : function (res) {

                                // redirect to product page if some error occurred
                                if (res.error || res == '') {
                                    //   hideLoader( current_option , current_container );
                                    return;
                                } else {

                                    current_option.data('price', res);
                                    current_option.attr('data-price', res);

                                    var formatted_price = getFormattedPrice(parseFloat(res));

                                    current_container.find('span.amount').html(formatted_price);
                                    // select option fix
                                    if (current_option.text() != '') {
                                        var temp_text = current_option.text().split('+');
                                        if (temp_text.length > 0) {
                                            temp_text = temp_text[0] + ' + ' + formatted_price;
                                            current_option.addClass('ywapo_option_price_changed');

                                            /* select2 fix */
                                            var current_group_container = current_option.closest('.ywapo_group_container');
                                            var select_element = current_group_container.find('select');
                                            var sb_attribute = select_element.attr('sb');
                                            if (typeof sb_attribute != 'undefined') {
                                                var index = current_option.data('index');
                                                var select2_element = $($('#sbOptions_' + sb_attribute).find('li').get(parseInt(index) + 1)).find('a');
                                                select2_element.html(temp_text);
                                            }

                                        }

                                        current_option.html(temp_text);

                                    }

                                    $i++;

                                    if ($i == group_container_list.length) {
                                        var $cart = group_container_list.closest('form.cart');
                                        $cart.trigger('yith-wapo-product-option-update');
                                        hideLoader(master_group_container);
                                    }

                                }

                            }
                        });

                    }

                });

            }

        }

        function yith_wapo_update_bundle_price($cart) {
            'use strict';

            var $bundle_price = getProductBundlePrice();

            if ($bundle_price > 0) {

                var master_group_container = $('.yith_wapo_groups_container');

                if (typeof master_group_container != 'undefined') {

                    var group_container_list = master_group_container.find('input.ywapo_input.ywapo_price_percentage, select.ywapo_input option.ywapo_price_percentage, textarea.ywapo_input.ywapo_price_percentage');

                    group_container_list.each(function () {

                        var current_option = $(this);

                        if (typeof current_option.data('pricetype') != 'undefined' && current_option.data('pricetype') != 'fixed') {

                            var current_container = current_option.closest('.ywapo_input_container');

                            setCalculatedPrice($cart, $bundle_price, current_option, master_group_container, current_container, true, false);

                        }

                    });

                }

            }

        }

        $('body').on('yith-wapo-product-option-update yith-wapo-product-price-changed', function () {
            'use strict';

            var $cart = $(this);
            updateTotal($cart);

        });

        function updateTotal($cart) {
            'use strict';

            var yith_wapo_group_total = $('.yith_wapo_group_total');

            //--- quantity -----------------------------------------------

            if ( $cart.find('form > div.quantity > input.qty').length ) {
                var qty = parseFloat( $cart.find('form > div.quantity > input.qty' ).val());
            } else if ( $cart.find('form.cart input.qty').length ) {
                var qty = parseFloat( $cart.find('form.cart input.qty' ).val());
            } else {
                var qty = 1;
            }

            //---------------------------------------------------------

            var yith_wapo_option_total_price = getOptionsTotal( $cart, qty );

            var yith_wapo_group_total = $('.yith_wapo_group_total');
            var yith_wapo_product_price = parseFloat(yith_wapo_group_total.data('product-price'));

            var is_product_bundle = $('.yith-wcpb-product-bundled-items');

            if (is_product_bundle.length > 0 && getProductBundlePrice() > 0) {
                yith_wapo_product_price = getProductBundlePrice();
            }

            // Product price
            var yith_wapo_group_product_price_total = yith_wapo_group_total.find('.yith_wapo_group_product_price_total span.price');
            if (yith_wapo_product_price > 0) {
                var yith_wapo_product_price_formatted = getFormattedPrice(yith_wapo_product_price * qty);
                yith_wapo_group_product_price_total.html(yith_wapo_product_price_formatted);
                yith_wapo_group_product_price_total.show();
            } else {
                yith_wapo_group_product_price_total.html('');
                yith_wapo_group_product_price_total.hide();
            }

            // Additional option
            var yith_wapo_group_option_total = yith_wapo_group_total.find('.yith_wapo_group_option_total span.price');
            var yith_wapo_option_total_price_formatted = getFormattedPrice(yith_wapo_option_total_price);
            yith_wapo_group_option_total.html(yith_wapo_option_total_price_formatted);

            // Final Total
            var yith_wapo_final_total_price = 0.0;
            var yith_wapo_group_final_total = yith_wapo_group_total.find('.yith_wapo_group_final_total span.price');
            yith_wapo_final_total_price = (yith_wapo_product_price * qty) + yith_wapo_option_total_price;
            var yith_wapo_total_price_formatted = getFormattedPrice(yith_wapo_final_total_price);

            var tax_rate = jQuery('.yith_wapo_group_total').data('tax-rate');
            var tax_string = jQuery('.yith_wapo_group_total').data('tax-string');
            if (tax_rate > 0) {
                var yith_wapo_total_price_formatted_tax = getFormattedPrice(yith_wapo_final_total_price * (tax_rate / 100 + 1));
                yith_wapo_group_final_total.html(yith_wapo_total_price_formatted + ' / ' + yith_wapo_total_price_formatted_tax + tax_string + tax_rate + '%');
            } else {
                yith_wapo_group_final_total.html(yith_wapo_total_price_formatted);
            }

            if (!yith_wapo_general.keep_price_shown && yith_wapo_option_total_price == 0) {
                yith_wapo_group_total.fadeOut();
            } else {
                yith_wapo_group_total.fadeIn();
            }

            loadCompositeTotal(yith_wapo_option_total_price, yith_wapo_product_price);

            $(document).trigger('yith_wapo_product_price_updated', [yith_wapo_product_price + yith_wapo_option_total_price]);

        }

        function getProductBundlePrice() {
            'use strict';
            return parseFloat($('.yith-wcpb-wapo-bundle-product-price').val());
        }

        function setCalculatedPrice($cart, $product_price, $current_option, $master_group_container, $current_container, $do_update, $do_option_change) {
            'use strict';

            var $product_id = $('.yith_wapo_group_total').data('product-id');

            if ($current_option.length > 0) {

                showLoader($master_group_container);

                $.ajax({
                    url    : yith_wapo_general.wc_ajax_url.toString().replace('%%endpoint%%', 'yith_wapo_get_calculated_display_price'),
                    type   : 'POST',
                    data   : {
                        product_id   : $product_id,
                        product_price: $product_price,
                        type_id      : $current_option.data('typeid'),
                        option_index : $current_option.data('index'),
                        option_value : $current_option.val()
                    },
                    success: function (res) {

                        // redirect to product page if some error occurred
                        if (res.error || res == '') {
                            //   hideLoader( current_option , current_container );
                            return 0;
                        } else {

                            $current_option.data('price', res);
                            $current_option.attr('data-price', res);

                            var formatted_price = getFormattedPrice(parseFloat(res));

                            $current_container.find('span.amount').html(formatted_price);
                            // select option fix
                            if ($current_option.text() != '') {
                                var temp_text = $current_option.text().split('+');
                                if (temp_text.length > 0) {
                                    temp_text = temp_text[0] + ' + ' + formatted_price;
                                    $current_option.addClass('ywapo_option_price_changed');

                                    /* select2 fix */
                                    var current_group_container = $current_option.closest('.ywapo_group_container');
                                    var select_element = current_group_container.find('select');
                                    var sb_attribute = select_element.attr('sb');
                                    if (typeof sb_attribute != 'undefined') {
                                        var index = $current_option.data('index');
                                        var select2_element = $($('#sbOptions_' + sb_attribute).find('li').get(parseInt(index) + 1)).find('a');
                                        select2_element.html(temp_text);
                                    }

                                }

                                $current_option.html(temp_text);

                                if ($do_update) {
                                    $cart.trigger('yith-wapo-product-option-update');
                                }

                            }

                            if ($do_option_change) {
                                $current_option.trigger('yith-wapo-product-price-changed');
                            }

                        }

                        hideLoader($master_group_container);

                    }
                });

            }

            return 0;

        }

        function loadCompositeTotal(yith_wapo_option_total_price, yith_wapo_product_price) {
            'use strict';

            var ywcp_wcp_group_total = $('.ywcp_wcp_group_total');
            if (ywcp_wcp_group_total.length == 1) {

                var ywcp_wcp_tr_wapo_option_total = $('#ywcp_wcp_tr_wapo_option_total');
                var yith_wcp_wapo_add_ons_total = $('.yith_wcp_wapo_add_ons_total span.amount');
                if (yith_wapo_option_total_price > 0) {

                    var yith_wapo_order_total = yith_wapo_option_total_price + yith_wapo_product_price;
                    ywcp_wcp_group_total.data('product-price', yith_wapo_order_total);
                    ywcp_wcp_group_total.attr('data-product-price', yith_wapo_order_total);

                    ywcp_wcp_tr_wapo_option_total.removeClass('ywcp_wapo_total_hided');
                    yith_wcp_wapo_add_ons_total.html(getFormattedPrice(yith_wapo_option_total_price));

                } else {
                    ywcp_wcp_group_total.data('product-price', yith_wapo_product_price);
                    ywcp_wcp_group_total.attr('data-product-price', yith_wapo_product_price);
                    ywcp_wcp_tr_wapo_option_total.addClass('ywcp_wapo_total_hided');
                    yith_wcp_wapo_add_ons_total.html(getFormattedPrice(0));
                }

                $(document).trigger('ywcp_calculate_total');
            }

        }

        $(this).on('click', '.ywapo_input_container.ywapo_input_container_labels', function (e) {
            'use strict';

            var current_selected_element = $(this).find('input[type="hidden"]');

            if (current_selected_element.val() != '') {
                current_selected_element.val('');
                $(this).removeClass('ywapo_selected');
            } else {
                var all_labels = $(this).closest('.ywapo_group_container_labels').find('.ywapo_input_container.ywapo_input_container_labels');

                all_labels.removeClass('ywapo_selected');
                all_labels.find('input[type="hidden"]').val('');

                $(this).addClass('ywapo_selected');

                current_selected_element.val(current_selected_element.data('index'));

            }

            $(this).trigger('yith-wapo-product-option-conditional', current_selected_element);

            $(this).trigger('yith-wapo-product-option-update');

        });

        /* dependencies */

        $(this).on('yith-wapo-product-option-conditional', function (e, data) {
            'use strict';

            var current_group_container = $(data).closest('.ywapo_group_container');
            doConditionaLoop($(this), data, current_group_container);

        });

        /* end dependencies*/

        /* thumb feature image overwrite */

        function changeFeaturedImage( current_group_container, data ) {
            'use strict';

            var allow_to_change = current_group_container.data('change-featured-image');
            var replaceImageMethod = 'standard';
            if ( yith_wapo_general.alternative_replace_image ) {
                replaceImageMethod = yith_wapo_general.alternative_replace_image;
            }

            if ( allow_to_change ) {

                if ( replaceImageMethod == 'alternative' || replaceImageMethod == 'yes' ) {

                    var current_item = $(data);

                    // Default WooCommerce teplate
                    var $product_image = $('.single-product .woocommerce-product-gallery .wp-post-image, .yith-quick-view-content .woocommerce-main-image img');
                    var $zoom_image = $('.single-product .woocommerce-product-gallery .zoomImg');
                    var $original_image_url = $product_image.attr('src');
                    var $original_zoom_url = $zoom_image.attr('src');

                    var is_field_selected = isFieldSelected(current_item, current_group_container);
                    var $input_container = current_item.closest('.ywapo_input_container');

                    // Labels exception
                    if (is_field_selected && current_group_container.data('type') == 'labels' && !$input_container.hasClass('ywapo_selected')) {
                        return;
                    }

                    // Image url of the related add-on
                    var $new_image = getImageByCurrentContainerData(current_group_container, current_item, $input_container);

                    // Check if replace the image or set the original
                    var $replace = is_field_selected &&
                        $new_image != '' &&
                        $new_image != global_product_featured_image &&
                        !current_group_container.hasClass('ywapo_conditional_hidden') &&
                        !current_group_container.hasClass('ywapo_conditional_variation_hidden');

                    if ($replace) {

                        $product_image.removeAttr('srcset');
                        $product_image.removeAttr('srcsize');
                        $product_image.attr('src', $new_image);
                        $product_image.attr('data-src', $new_image);
                        $product_image.attr('data-large_image', $new_image);
                        $zoom_image.attr('src', $new_image);
                        $zoom_image.siblings('a').attr('href', $new_image);

                        global_selected_group_container_image = current_group_container;
                        yith_zoom_mgnifier_update_img($new_image);

                    } else if (global_selected_group_container_image != null) {

                        $product_image.removeAttr('srcset');
                        $product_image.removeAttr('srcsize');
                        $product_image.attr('src', $original_image_url);
                        $zoom_image.attr('src', $original_zoom_url);

                        yith_zoom_mgnifier_update_img($original_image_url);

                    }

                } else if (replaceImageMethod == 'paul') {

                    var current_item = $(data);

                    var $gallery_wrapper = $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper');
                    var $gallery_images_number = $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:not(.clone)').length;
                    var $gallery_image = $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:first-child');
                    var $gallery_image_size = $gallery_image.width();

                    var is_field_selected = isFieldSelected(current_item, current_group_container);
                    var $input_container = current_item.closest('.ywapo_input_container');

                    var $new_image = getImageByCurrentContainerData(current_group_container, current_item, $input_container);

                    // Labels exception
                    if (is_field_selected && current_group_container.data('type') == 'labels' && !$input_container.hasClass('ywapo_selected')) {
                        return;
                    }

                    // Check if replace the image or set the original
                    var $replace = is_field_selected &&
                        $new_image != '' &&
                        $gallery_images_number > 0 &&
                        !current_group_container.hasClass('ywapo_conditional_hidden') &&
                        !current_group_container.hasClass('ywapo_conditional_variation_hidden');

                    if ($replace) {
                        $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper a').attr({'href': $new_image}).find('img').removeAttr('srcset').removeAttr('srcsize').attr('src', $new_image);
                    }

                } else if (replaceImageMethod == 'divi') {

                    var current_item = $(data);

                    // Default WooCommerce teplate
                    var $product_image = $('.single-product .sb_woo_product_image > img, img.wp-post-image');
                    var $zoom_image = $('.single-product .zoomImg');
                    var $original_image_url = $product_image.attr('src');
                    var $original_zoom_url = $zoom_image.attr('src');

                    var is_field_selected = isFieldSelected(current_item, current_group_container);
                    var $input_container = current_item.closest('.ywapo_input_container');

                    // Labels exception
                    if (is_field_selected && current_group_container.data('type') == 'labels' && !$input_container.hasClass('ywapo_selected')) {
                        return;
                    }

                    // Image url of the related add-on
                    var $new_image = getImageByCurrentContainerData(current_group_container, current_item, $input_container);

                    // Check if replace the image or set the original
                    var $replace = is_field_selected &&
                        $new_image != '' &&
                        $new_image != global_product_featured_image &&
                        !current_group_container.hasClass('ywapo_conditional_hidden') &&
                        !current_group_container.hasClass('ywapo_conditional_variation_hidden');

                    if ($replace) {

                        $product_image.removeAttr('srcset');
                        $product_image.removeAttr('srcsize');
                        $product_image.attr('src', $new_image);
                        $product_image.attr('data-src', $new_image);
                        $product_image.attr('data-large_image', $new_image);
                        $zoom_image.attr('src', $new_image);
                        $zoom_image.siblings('a').attr('href', $new_image);

                        global_selected_group_container_image = current_group_container;
                        yith_zoom_mgnifier_update_img($new_image);

                    } else if (global_selected_group_container_image != null) {

                        $product_image.removeAttr('srcset');
                        $product_image.removeAttr('srcsize');
                        $product_image.attr('src', $original_image_url);
                        $zoom_image.attr('src', $original_zoom_url);

                        yith_zoom_mgnifier_update_img($original_image_url);

                    }

                } else {

                    var current_item = $(data);

                    var $gallery_wrapper = $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper');
                    var $gallery_images_number = $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:not(.clone)').length;
                    var $gallery_image = $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:first-child');
                    var $gallery_image_size = $gallery_image.width();

                    var is_field_selected = isFieldSelected(current_item, current_group_container);
                    var $input_container = current_item.closest('.ywapo_input_container');

                    var $new_image = getImageByCurrentContainerData(current_group_container, current_item, $input_container);

                    // Labels exception
                    if (is_field_selected && current_group_container.data('type') == 'labels' && !$input_container.hasClass('ywapo_selected')) {
                        return;
                    }

                    // Check if replace the image or set the original
                    var $replace = is_field_selected &&
                        $new_image != '' &&
                        $gallery_images_number > 0 &&
                        !current_group_container.hasClass('ywapo_conditional_hidden') &&
                        !current_group_container.hasClass('ywapo_conditional_variation_hidden');

                    if ($replace) {

                        $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper .clone').remove();
                        $gallery_image.clone().addClass('clone').appendTo('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper');

                        if ($gallery_images_number < 2) {
                            $gallery_wrapper.attr('style', 'width: 200%; transition-duration: 0s;');
                            $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper > div')
                                .attr('style', 'width: ' + $gallery_image_size + 'px; margin-right: 0px; float: left; display: block; position: relative; overflow: hidden;');
                        }
                        $gallery_wrapper.css('transform', 'translate3d(-' + ($gallery_images_number * $gallery_image_size) + 'px, 0px, 0px)');

                        if (!$gallery_wrapper.parent().hasClass('flex-viewport')) {
                            $gallery_wrapper.wrap('<div class="flex-viewport" style="overflow: hidden; position: relative; height: ' + $gallery_image_size + 'px;"></div>');
                        }

                        $('.single-product .woocommerce-product-gallery .woocommerce-product-gallery__wrapper .clone img').removeAttr('srcset').removeAttr('srcsize').attr('src', $new_image);

                    }

                }

            }

        }

        function getImageByCurrentContainerData(current_group_container, $data, $input_container) {
            'use strict';

            var $new_image = '';

            var option_selected = $data.parents('select').find('option:selected');

            // select controls
            if ((typeof option_selected != 'undefined') && option_selected.length == 1 && (typeof option_selected.data('image-url') != 'undefined' && option_selected.data('image-url').length > 0)) {

                $new_image = option_selected.data('image-url');

            } else { // others controls

                var replaceImageMethod = 'standard';
                if (yith_wapo_general.alternative_replace_image) {
                    replaceImageMethod = yith_wapo_general.alternative_replace_image;
                }

                var $current_image = $input_container.find('.ywapo_single_option_image');

                //single option image
                if ($current_image.length == 1) {

                    if ( replaceImageMethod == 'paul' || replaceImageMethod == 'divi' ) {
                        $new_image = $current_image.attr('fullsize');
                    } else {
                        $new_image = $current_image.attr('src');
                    }

                } else { // Add-on image
                    $current_image = current_group_container.find('.ywapo_product_option_image > img');

                    if ($current_image.length == 1) {

                        if ( replaceImageMethod == 'paul' || replaceImageMethod == 'divi' ) {
                            $new_image = $current_image.attr('fullsize');
                        } else {
                            $new_image = $current_image.attr('src');
                        }

                    } else {
                        $new_image = global_product_featured_image;
                    }
                }
            }

            return $new_image;
        }

        function undoFeaturedImage( $product_image, current_group_container ) {
            'use strict';

            if ( global_selected_group_container_image != null && current_group_container.data('id') == global_selected_group_container_image.data('id') ) {
                resetFeaturedImage( $product_image );
                global_selected_group_container_image = null;
            }

        }

        function resetFeaturedImage( $product_image ) {
            'use strict';

            $product_image.removeAttr('srcset');
            $product_image.removeAttr('srcsize');
            $product_image.attr('src', global_product_featured_image);
            yith_zoom_mgnifier_update_img(global_product_featured_image);

        }

        function yith_zoom_mgnifier_update_img($changed_image) {
            'use strict';

            if ( typeof yith_magnifier_options == 'undefined' ) {
                return;
            }

            var yith_wcmg_zoom = $('.yith_magnifier_zoom');
            var yith_wcmg_image = $('.yith_magnifier_zoom img');

            yith_wcmg_zoom.attr('href', $changed_image);
            yith_wcmg_image.attr('src', $changed_image);
            yith_wcmg_image.attr('srcset', $changed_image);
            yith_wcmg_image.attr('src-orig', $changed_image);

            var yith_wcmg = $('.images');

            if ( yith_wcmg.data('yith_magnifier') ) {
                yith_wcmg.yith_magnifier('destroy');
            }

            yith_wcmg.yith_magnifier(yith_magnifier_options);

        }

        /* Required */
        $(this).on('click', '.single_add_to_cart_button', function (e) {
            'use strict';

            var $cart = $(this).closest('form.cart');
            yith_wapo_general.do_submit = checkRequiredFields($cart);
            if ( ! yith_wapo_general.do_submit ) {
                var $add_to_cart = $cart.find('button.single_add_to_cart_button');
                if ( $add_to_cart.length > 0 ) {
                    if ( $add_to_cart.hasClass('loading') ) {
                        $add_to_cart.removeClass('loading');
                    }
                }
            }
            return yith_wapo_general.do_submit;

        });

        /* Request a Quote */
        $(document).on('yith_ywraq_action_before', function () {
            'use strict';

            $cart = $('form.cart');
            yith_wapo_general.do_submit = checkRequiredFields($cart);
            return yith_wapo_general.do_submit;

        });

        $(document).on('yith_wcpb_ajax_update_price_request', function () {
            $cart = $('form.cart');
            yith_wapo_update_bundle_price( $cart );
            updateTotal( $cart );
        });

        function checkRequiredFields($cart) {
            'use strict';

            var do_submit = true;

            $cart.find('.ywapo_group_container').each(function () {

                var group_container = $(this);

                if (typeof group_container != 'undefined' && !group_container.hasClass('ywapo_conditional_hidden') && !group_container.hasClass('ywapo_conditional_variation_hidden')) {

                    var type = group_container.data('type');
                    var required = group_container.data('requested') == '1';
                    var required_all_options = group_container.data('requested-all-options') == '1';
                    var selected = required_all_options;
                    var max_item_selected = group_container.data('max-item-selected');

                    switch (type) {
                        case 'text' :
                        case 'textarea' :
                        case 'number' :
                        case 'file' :
                        case 'date' :
                        case 'range' :
                        case 'color' :

                            // work just for number
                            if (group_container.data('max-input-values-required') == '1' || group_container.data('min-input-values-required') == '1') {
                                required = true;
                                selected = false;
                            } else {
                                var $all_elements = group_container.find('input.ywapo_input, textarea.ywapo_input, input.ywapo_input_color');
                                var $selected_elements = 0;

                                $all_elements.each(function () {
                                    var value = $(this).val();
                                    if (value != '') {
                                        $selected_elements++;
                                    }
                                });

                                if (max_item_selected > 0 && max_item_selected == $selected_elements) {
                                    selected = true;
                                } else {
                                    $all_elements.each(function () {
                                        if (required_all_options) {
                                            if ($(this).val() == '' && $(this).attr('required') == 'required') {
                                                required = true;
                                                selected = false;
                                                return;
                                            } else if ($(this).val() == '') {
                                                selected = false;
                                                return;
                                            }
                                        } else {
                                            if ($(this).val() != '') {
                                                selected = true;
                                                return;
                                            }
                                        }
                                    });
                                }
                            }

                            break;

                        case 'checkbox' :

                            if (required) {

                                var num_elements = group_container.find('.ywapo_input').length;
                                var num_elements_selected = group_container.find('.ywapo_input:checked').length;

                                if (required_all_options) {
                                    selected = num_elements > 0 && (num_elements == num_elements_selected || (max_item_selected > 0 && num_elements == max_item_selected));
                                } else {
                                    selected = num_elements > 0 && (num_elements_selected > 0);
                                }

                            } else {

                                group_container.find('.ywapo_input').each(function () {

                                    if (!$(this).is(':checked') && $(this).attr('required') == 'required') {
                                        required = true;
                                        selected = false;
                                        return;
                                    }

                                });

                            }

                            break;


                        case 'select' :

                            selected = group_container.find('select.ywapo_input').val() != '';

                            break;

                        case 'labels' :
                        case 'multiple_labels' :
                            selected = group_container.find('.ywapo_input_container_labels.ywapo_selected').length > 0;

                            break;

                        case 'radio' :

                            selected = false;

                            group_container.find('input.ywapo_input').each(function () {

                                if ($(this).is(':checked')) {
                                    selected = true;
                                    return;
                                }

                            });

                            break;

                        default :

                    }

                    if (required && !selected) {
                        do_submit = false;
                        group_container.addClass('ywapo_miss_required');

                        $([document.documentElement, document.body]).animate({
                            scrollTop: $("#yith_wapo_groups_container").offset().top
                        }, 1000);

                        return;
                    } else {
                        group_container.removeClass('ywapo_miss_required');
                    }

                }

            });

            return do_submit;

        }

        /* end required */

        /* max item selected */

        $(this).on('yith-wapo-product-option-conditional', function (e, data) {
            'use strict';

            checkMaxItemSelected(data);

        });

        function checkMaxItemSelected(data) {
            'use strict';

            var current_group_container = $(data).closest('.ywapo_group_container');

            var max_item_selected = current_group_container.data('max-item-selected');

            if (max_item_selected > 0) {

                var current_group_container_type = current_group_container.data('type');

                switch (current_group_container_type) {

                    case 'checkbox':

                        var $all_elements = current_group_container.find('input[type="checkbox"].ywapo_input');
                        var $selected_elements = current_group_container.find('input[type="checkbox"]:checked.ywapo_input');

                        if ($selected_elements.length == max_item_selected) {

                            $all_elements.each(function () {

                                if (!$(this).is(':checked')) {
                                    $(this).attr('disabled', 'disabled');
                                }

                            });

                        } else {

                            $all_elements.each(function () {

                                $(this).removeAttr('disabled');

                            });

                        }

                        break;

                    case 'multiple_labels':

                        var $all_elements = current_group_container.find('div.ywapo_input_container_labels');
                        var $selected_elements = current_group_container.find('div.ywapo_input_container_labels.ywapo_selected');

                        if ($selected_elements.length == max_item_selected) {

                            $all_elements.each(function () {

                                if (!$(this).hasClass('ywapo_selected')) {
                                    $(this).attr('disabled', 'disabled');
                                }

                            });

                        } else {

                            $all_elements.each(function () {

                                $(this).removeAttr('disabled');

                            });

                        }

                        break;

                    case 'number':

                        var $all_elements = current_group_container.find('input[type="number"].ywapo_input');
                        var $selected_elements = 0;

                        $all_elements.each(function () {
                            var value = $(this).val();
                            if (value != '') $selected_elements++;
                        });


                        if ($selected_elements == max_item_selected) {

                            $all_elements.each(function () {
                                var value = $(this).val();
                                if (value == '') {
                                    $(this).attr('disabled', 'disabled');
                                }

                            });

                        } else {

                            $all_elements.each(function () {

                                $(this).removeAttr('disabled');

                            });

                        }

                        break;

                }

            }

        }

        $(this).on('yith-wapo-product-option-conditional', function (e, data) {
            'use strict';

            cechkMinMaxInputValuesAmount(data);

        });

        function cechkMinMaxInputValuesAmount(data) {
            'use strict';

            var current_group_container = $(data).closest('.ywapo_group_container');

            var current_group_container_type = current_group_container.data('type');

            if (current_group_container_type == 'number') {

                var $all_elements = current_group_container.find('input[type="number"].ywapo_input');
                var $selected_values_amount = 0.0;

                $all_elements.each(function () {
                    var value = $(this).val();
                    if (value != '') $selected_values_amount += parseFloat(value);
                });

                var max_input_values_amount = current_group_container.data('max-input-values-amount');

                if (max_input_values_amount > 0) {

                    if ($selected_values_amount > max_input_values_amount) {
                        current_group_container.data('max-input-values-required', '1');
                        current_group_container.attr('data-max-input-values-required', '1');
                    } else {
                        current_group_container.data('max-input-values-required', '0');
                        current_group_container.attr('data-max-input-values-required', '0');
                    }

                }

                var min_input_values_amount = current_group_container.data('min-input-values-amount');

                if (min_input_values_amount > 0) {

                    if ($selected_values_amount < min_input_values_amount) {
                        current_group_container.data('min-input-values-required', '1');
                        current_group_container.attr('data-min-input-values-required', '1');
                    } else {
                        current_group_container.data('min-input-values-required', '0');
                        current_group_container.attr('data-min-input-values-required', '0');
                    }

                }

            }

        }

        /**
         *
         * @param $disable_element
         * @param $load_element
         */
        function showLoader($load_element) {
            'use strcit';

            $load_element.block({
                message: '', overlayCSS: {
                    backgroundColor: '#fff',
                    opacity        : 0.6,
                    cursor         : 'wait'
                }
            });

        }

        /**
         *
         * @param $disable_element
         * @param $load_element
         */
        function hideLoader($load_element) {
            'use strcit';

            $load_element.unblock();

        }

        /**
         *
         * @param data
         * @param current_group_container
         * @returns {boolean}
         */
        function isFieldSelected(data, current_group_container) {
            'use strict';

            var current_group_container_type = current_group_container.data('type');

            var selected = false;

            if (current_group_container_type == 'select') {
                if (data.val() != '') {
                    selected = true;
                } else {
                    selected = false;
                }
            }
            else if (current_group_container_type == 'radio') {
                selected = data.is(':checked');
            }
            else {

                switch (current_group_container_type) {
                    case 'checkbox':

                        current_group_container.find('input[type="checkbox"].ywapo_input').each(function () {

                            if ($(this).is(':checked')) {
                                selected = true;
                                return;
                            }

                        });

                        break;

                    case 'labels':
                        var count_val = 0;
                        current_group_container.find('input[type="hidden"].ywapo_input').each(function () {

                            if ($(this).val() != '') {
                                count_val++;
                                return true;
                            }

                        });

                        selected = (count_val > 0);

                        break;

                    case 'text' :
                    case 'number' :
                    case 'file' :
                    case 'color' :
                    case 'date' :
                    case 'range' :

                        current_group_container.find('input.ywapo_input').each(function () {

                            if ($(this).val() != '') {
                                selected = true;
                                return;
                            }

                        });

                        break;
                    case 'textarea' :

                        current_group_container.find('textarea.ywapo_input').each(function () {

                            if ($(this).val() != '') {
                                selected = true;
                                return;
                            }
                        });

                        break;
                }
            }

            if (selected) {
                current_group_container.removeClass('ywapo_miss_required');
            }

            return selected;

        }

        /**
         *
         * @param data
         * @param current_group_container
         * @returns {Array}
         */
        function getSelectedValues(data, current_group_container) {
            'use strict';

            var current_group_container_type = current_group_container.data('type');

            var values = [];

            switch (current_group_container_type) {

                case 'radio':

                    if (data.is(':checked')) {
                        values[0] = data.val();
                    }

                    break;

                case 'select':

                    values[0] = data.val();

                    break;

                case 'checkbox':

                    var i = 0;
                    current_group_container.find('input[type="checkbox"].ywapo_input').each(function () {

                        if ($(this).is(':checked')) {
                            values.push(i.toString())
                        }

                        i++;

                    });

                    break;

                case 'labels':

                    current_group_container.find('input[type="hidden"].ywapo_input').each(function () {

                        if ($(this).val() != '') {
                            values.push($(this).val());
                        }

                    });

                    break;

                case 'text' :
                case 'number' :
                case 'file' :
                case 'color' :
                case 'date' :
                case 'range' :

                    var i = 0;
                    current_group_container.find('input.ywapo_input').each(function () {

                        if ($(this).val() != '') {
                            values.push(i.toString());
                        }

                        i++;

                    });

                    break;

                case 'textarea' :

                    var i = 0;
                    current_group_container.find('textarea.ywapo_input').each(function () {

                        if ($(this).val() != '') {
                            values.push(i.toString());
                        }

                        i++;

                    });

                    break;

            }

            return values;

        }

        /**
         *
         * @param $cart
         * @param data
         * @param current_group_container
         */
        function doConditionaLoop($cart, data, current_group_container) {
            'use strcit';

            var current_group_container_id = current_group_container.data('id');

            if (typeof current_group_container_id != 'undefined') {

                var current_values = getSelectedValues($(data), current_group_container);
                var operatorArray = new Array();

                // verify dependence condition
                $cart.find('.ywapo_group_container').each(function () {

                    var group_container = $(this).closest('.ywapo_group_container');

                    if ( typeof group_container != 'undefined' ) {

                        var group_container_id = group_container.data('id');

                        if ( current_group_container_id != group_container_id ) {

                            var group_container_operator = group_container.data('operator');
                            var group_container_dependecies = getDependenciesListByGroup(group_container);

                            if ( group_container_dependecies.length > 0 ) {

                                var has_hided_dependecies = checkDependeciesList(group_container_dependecies);

                                if ( false ) {
                                // if ( has_hided_dependecies ) {

                                    if ( ! group_container.hasClass('ywapo_conditional_hidden') ) {
                                        group_container.addClass('ywapo_conditional_hidden');
                                        doFieldDisabled( group_container );
                                        return true;
                                    }

                                } else {

                                    if ( typeof current_group_container_id != 'undefined' && current_group_container_id != null ) {

                                        var is_dependent = isGroupDependent( current_group_container_id.toString(), group_container_dependecies );

                                        if ( is_dependent >= 0 || is_dependent == 'option' ) {

                                            var $is_match = isDependentMatch( current_group_container_id.toString(), current_values, group_container_dependecies, is_dependent, group_container_operator );

                                            var addonId = group_container.attr('id');
                                            var addonDep = current_group_container_id.toString();

                                            /* AND FIX FUNCTION */

                                            if ( group_container_operator == 'and' ) {
                                                var AND = true;
                                                var AND_dependent_element = '';
                                                var AND_add_on_id = '';
                                                var AND_ctrl_value = '';
                                                var AND_check_value = '';
                                                var AND_element_id = '';
                                                var AND_select_id = '';
                                                var AND_is_checkchecked = false;
                                                var AND_is_selectchecked = false;
                                                var AND_is_textchecked = false;

                                                for ( var y = 0; y < group_container_dependecies.length; y++ ) {
                                                    if ( AND == false ) { break; }
                                                    AND_dependent_element = group_container_dependecies[y];
                                                    AND_add_on_id = 'ywapo_value_' + AND_dependent_element.split(',')[0];
                                                    AND_ctrl_value = AND_dependent_element.split(',')[0];
                                                    AND_check_value = AND_dependent_element.split(',')[1];
                                                    AND_element_id = 'ywapo_ctrl_id_' + AND_ctrl_value + '_' + AND_check_value;
                                                    AND_select_id = 'ywapo_select_' + AND_ctrl_value;

                                                    AND_is_checkchecked = false;
                                                    AND_is_selectchecked = false;
                                                    AND_is_textchecked = false;

                                                    if ( document.getElementById( AND_element_id ) != 'undefined' || document.getElementById( AND_select_id ) != 'undefined' ) {
                                                        if ( document.getElementById( AND_element_id ).type == 'checkbox' ) {
                                                            AND_is_checkchecked = document.getElementById( AND_element_id ).checked;
                                                        } else if ( document.getElementById( AND_element_id ).type == 'radio' ) {
                                                            AND_is_checkchecked = document.getElementById( AND_element_id ).checked;
                                                        } else if ( document.getElementById( AND_element_id ).type == 'hidden' ) {
                                                            AND_is_checkchecked = jQuery('#'+AND_element_id).parent().hasClass( 'ywapo_selected' );
                                                        } else if ( document.getElementById( AND_element_id ).type == 'text' ) {
                                                            AND_is_textchecked = document.getElementById( AND_element_id ).value != '';
                                                        } else if ( document.getElementById( AND_select_id ).type == 'select-one' ) {
                                                            AND_is_selectchecked = jQuery( '#' + AND_select_id + ' option:selected' ).attr('id') == AND_element_id;
                                                        }
                                                    }

                                                    if ( AND_is_checkchecked || AND_is_textchecked || AND_is_selectchecked ) {
                                                        AND = true;
                                                    } else {
                                                        AND = false;
                                                    }
                                                    
                                                }
                                                $is_match = AND;
                                            }

                                            /* AND FIX FUNCTION */

                                            if ($is_match) {
                                                group_container.removeClass('ywapo_conditional_hidden');
                                                group_container.addClass('ywapo_conditional_matched');
                                                group_container.addClass('ywapo_conditional_matched_' + current_group_container_id);
                                                if (!group_container.hasClass('ywapo_conditional_variation_hidden')) {
                                                    doFieldChange(group_container);
                                                }
                                            } else {
                                                group_container.addClass('ywapo_conditional_hidden');
                                                group_container.removeClass('ywapo_conditional_matched');
                                                group_container.removeClass('ywapo_conditional_matched_' + current_group_container_id);
                                                doFieldDisabled(group_container);
                                            }

                                        }
                                    }

                                }
                            }

                        }

                    }

                });

            }

        }

        /**
         *
         * @param group_container
         * @returns {*}
         */
        function getDependenciesListByGroup(group_container) {
            'use strict';

            var group_container_dependecies = group_container.data('condition');

            if (group_container_dependecies != '') {

                group_container_dependecies = group_container_dependecies.toString().split(',');

                for (var i = 0; i < group_container_dependecies.length; i++) {

                    if (group_container_dependecies[i].indexOf('option_') == 0) {

                        group_container_dependecies[i] = group_container_dependecies[i].replace('option_', '');

                        group_container_dependecies[i] = group_container_dependecies[i].replace('_', ',');

                    }

                }

            }

            return group_container_dependecies;

        }

        function isGroupDependent($current_id, group_container_dependecies) {
            'use strict';

            var is_dipendent = $.inArray($current_id, group_container_dependecies);

            if (is_dipendent == -1) {

                for (var i = 0; i < group_container_dependecies.length; i++) {

                    if (group_container_dependecies[i].indexOf(',') >= 0) {

                        var add_on_id = group_container_dependecies[i].split(',')[0];

                        if ($current_id == add_on_id.toString()) {
                            is_dipendent = 'option';
                            break;
                        }

                    }

                }

            }

            return is_dipendent;

        }

        function isDependentMatch($current_id, $current_values, group_container_dependecies, dependent_type, group_container_operator) {
            'use strict';

            // check for options

            if (dependent_type == 'option') {

                var match_and_operator = true;

                var matchResult = false;

                for (var i = 0; i < group_container_dependecies.length; i++) {

                    var $dependent_element = group_container_dependecies[i];

                    if ($dependent_element.indexOf(',') >= 0) {

                        var add_on_id = $dependent_element.split(',')[0];
                        var option_value = $dependent_element.split(',')[1].toString();

                        if ($current_id == add_on_id.toString() && option_value != '') {
                            var $is_match = $.inArray(option_value, $current_values);

                            if (group_container_operator == 'and') {

                                if (!($is_match >= 0)) {
                                    match_and_operator = false;
                                }

                            } else {

                                match_and_operator = false;
                                if ($is_match >= 0) {
                                    return true;
                                }

                            }
                        }

                    }

                }

                return match_and_operator;

            } else {

                // check simple dependencies

                return $current_values.length > 0 ? $current_values[0] >= 0 : false;

            }

        }

        /**
         *
         * @param dependencies_list
         * @returns {*}
         */
        function checkDependeciesList(dependencies_list) {
            'use strict';

            var has_hided_dependecies = false;
            $('.yith_wapo_groups_container').find('.ywapo_group_container').each(function () {

                var id = $(this).data('id');
                var is_dependent = isGroupDependent(id, dependencies_list);

                if ((is_dependent >= 0 || is_dependent == 'option') && ($(this).hasClass('ywapo_conditional_hidden'))) {
                    has_hided_dependecies = true;
                    return;
                }

            });

            return has_hided_dependecies;

        }

        function doFieldChange(group_container) {
            'use strcit';

            group_container.find('input, select, textarea').each(function () {
                $(this).removeAttr('disabled')
                $(this).change();
            });
        }

        function doFieldDisabled(group_container) {
            'use strcit';

            group_container.find('input, select, textarea').each(function () {
                $(this).attr('disabled', 'disabled')
            });

            // cascade disabled
            var group_container_id = group_container.data('id');
            var $group_dependent_childs = $('.ywapo_conditional_matched_' + group_container_id);
            $group_dependent_childs.each(function () {
                var $group_child = $(this);
                $group_child.addClass('ywapo_conditional_hidden');
                doFieldDisabled($group_child);
            });
        }

        /* variations dependencies */

        function doConditionalVariationsLoop($cart) {
            'use strcit';

            var variation_id = global_select_variations ? global_select_variations.variation_id.toString() : 0;

            // verify dependence condition
            $cart.find('.ywapo_group_container').each(function () {

                var group_container = $(this).closest('.ywapo_group_container');

                if (typeof group_container != 'undefined') {

                    var group_container_id = group_container.data('id');

                    var group_container_depencies_variations = getDependentVariationsByGroup(group_container);

                    if (group_container_depencies_variations.length > 0) {

                        var variation_matched = ($.inArray(variation_id, group_container_depencies_variations)) >= 0;

                        if (variation_matched) {
                            group_container.removeClass('ywapo_conditional_variation_hidden');
                            group_container.addClass('ywapo_conditional_variation_matched');
                            if (!group_container.hasClass('ywapo_conditional_hidden')) {
                                doFieldChange(group_container);
                            }

                        } else {
                            group_container.addClass('ywapo_conditional_variation_hidden');
                            group_container.removeClass('ywapo_conditional_variation_matched');
                            doFieldDisabled(group_container);

                            var $group_dependent_childs = $('.ywapo_conditional_matched_' + group_container_id);

                            $group_dependent_childs.each(function () {
                                var $group_child = $(this);
                                $group_child.addClass('ywapo_conditional_hidden');
                                doFieldDisabled($group_child);
                            });
                        }

                    }

                }

            });

        }

        function getDependentVariationsByGroup(group_container) {
            'use strict';

            var group_container_dependecie_variations = group_container.data('condition-variations');

            if (group_container_dependecie_variations != '') {

                group_container_dependecie_variations = group_container_dependecie_variations.toString().split(',');

            }

            return group_container_dependecie_variations;
        }

        /* end variations dependencies */

        /* minimum maximum quantity */

        $(document).on('ywmmq_additional_operations', function () {
            'use strict';

            var $cart = $('form.cart');

            updateTotal($cart);

        });

        /* end minimum maximum quantity */

        /**
         *
         * @param $cart
         *
         * @author Andrea Frascaspata
         * @returns {number}
         */
        function getOptionsTotal( $cart, $qty ) {
            'use strict';

            var yith_wapo_final_total_price = 0.0;

            var first_options_free_container;

            $cart.find('.ywapo_input:checked, select.ywapo_input option:selected, input[type="text"].ywapo_input, input[type="number"].ywapo_input, input[type="file"].ywapo_input, input[type="color"].ywapo_input, input[type="date"].ywapo_input,input[type="hidden"].ywapo_input ,textarea.ywapo_input').each(function () {

                var current_option = $(this);
                var $current_cart = current_option.closest('form.cart');
                var single_quantity = $qty;
                var group_container = current_option.closest('.ywapo_group_container');

                var first_options_free = group_container.data('first-options-free-temp');
                first_options_free_container = group_container;

                if ( typeof group_container != 'undefined' && !group_container.hasClass('ywapo_conditional_hidden') && !group_container.hasClass('ywapo_conditional_variation_hidden') ) {

                    var type = group_container.data('type');
                    var sold_individually = group_container.data('sold-individually');
                    var add_price = false;

                    if ( sold_individually == '1' ) { single_quantity = 1; }

                    switch ( type ) {

                        case 'number' :

                            if ( current_option.val().trim() != '' && current_option.val().trim() != 0 ) { add_price = true; }
                            break;

                        case 'text' :
                        case 'textarea' :
                        case 'file' :
                        case 'color' :
                        case 'date' :
                        case 'labels' :
                        case 'multiple_labels' :

                            if ( current_option.val().trim() != '' ) { add_price = true; }
                            break;

                        default : add_price = true;
                    }

                    if ( add_price ) {

                        if ( first_options_free == 0 ) {
                            var price_attribute = current_option.data('price');
                        } else {
                            var price_attribute = 0;
                            first_options_free--;
                            group_container.data('first-options-free-temp', first_options_free);

                        }

                        if ( typeof price_attribute != 'undefined' && price_attribute != 0 ) {
                            yith_wapo_final_total_price += parseFloat( price_attribute ) * parseFloat( single_quantity );
                        }

                    }

                }

                var replaceImageMethod = 'standard';
                if ( yith_wapo_general.alternative_replace_image ) {
                    replaceImageMethod = yith_wapo_general.alternative_replace_image;
                }

                if ( replaceImageMethod != 'paul' || current_option.closest('.ywapo_input_container').hasClass('ywapo_selected') ) {
                    changeFeaturedImage( group_container, this );
                }

            });
            if ( first_options_free_container ) {
                first_options_free_container.data('first-options-free-temp', first_options_free_container.data('first-options-free'));
            }

            return yith_wapo_final_total_price;
        }

        /**
         *
         * @param price
         * @author Andrea Frascaspata
         * @returns {*}
         */
        function getFormattedPrice(price) {
            'use strict';

            var formatted_price = accounting.formatMoney(price, {
                symbol   : yith_wapo_general.currency_format_symbol,
                decimal  : yith_wapo_general.currency_format_decimal_sep,
                thousand : yith_wapo_general.currency_format_thousand_sep,
                precision: yith_wapo_general.currency_format_num_decimals,
                format   : yith_wapo_general.currency_format
            });

            return formatted_price;
        }

        var $cart = $(this);

        /* trigger change event (default value fix) */
        $cart.find('.yith_wapo_groups_container input, .yith_wapo_groups_container select, .yith_wapo_groups_container textarea').each(function () {

            $cart.trigger('yith-wapo-product-option-conditional', $(this));

        });

        $cart.trigger('yith-wapo-product-option-update');

        updateTotal($cart);

        // utility

        function yit_wapo_remove_array_element($array, val) {
            var i = $array.indexOf(val);
            return i > -1 ? $array.splice(i, 1) : [];
        }

    }


    function ywapo_initialize() {
        'use strcit';

        var $product_featured_image = $('.woocommerce-main-image > img.wp-post-image, div.product div.images > img');
        if ($product_featured_image.length > 0) {
            global_product_featured_image = $product_featured_image.attr('src');
        }

        // Initialize
        $('body').find('form:not(.in_loop).cart').each(function () {
            $(this).init_yith_wapo_totals();
            $(this).find('.variations select').change();
        });

        $('body').find('.wapo_option_tooltip').each(function () {
            var tooltip = $(this).data('tooltip');
            if (tooltip) {
                yith_wapo_tooltip($(this), tooltip);
            }
        });

        wapo_collapse_feature();

        /* external */

        $('.ywapo_input_container_color .wp-color-picker').wpColorPicker({
            change: function (event, ui) {

                var container = $(this).closest('.ywapo_input_container_color');
                var element = container.find('input.ywapo_input_color');
                element.val(ui.color.toString());
                element.change();
            },
            clear : function () {
                var container = $(this).closest('.ywapo_input_container_color');
                var element = container.find('input.ywapo_input_color');
                element.val('');
                element.change();
            }
        });

        $('.ywapo_datepicker').each(function () {
            $(this).datepicker({
                dateFormat: yith_wapo_general.date_format
            });
        });


        var $avada_select = $('.fusion-body select.ywapo_input');
        if ($avada_select.length > 0) {
            $avada_select.addClass('avada-select');
            $avada_select.wrap('<div class="avada-select-parent"></div>').after('<div class="select-arrow">&#xe61f;</div>');
        }

        wapo_qty_dependencies();

    }

    ywapo_initialize();

    function yith_wapo_tooltip(opt, tooltip) {
        'use strict';

        var tooltip_wrapper = $('<span class="yith_wccl_tooltip"></span>'),
            classes = yith_wapo_general.tooltip_pos + ' ' + yith_wapo_general.tooltip_ani;

        tooltip_wrapper.addClass(classes);

        opt.append(tooltip_wrapper.html('<span>' + tooltip + '</span>'));
    };

    /* YITH QUICK VIEW */

    $(document).on('qv_loader_stop yit_quick_view_loaded flatsome_quickview unero_quick_view_request_success', function () {
        'use strict';
        ywapo_initialize();
    });

    /* DISABLE PRESS ENTER */

    $( '.single-product .product form.cart input' ).keypress( function(e) { 
        if ( e.which == 13 ) {
            // ywapo_initialize();
            // return false;
        } 
    });

    $( '.single-product .product form.cart textarea' ).keypress( function(e) { 
        if ( e.which == 13 ) {
            ywapo_initialize();
        } 
    });

    /* TOGGLE GROUP */
    function wapo_collapse_feature() {
        var enableCollapseFeature = $('#yith_wapo_groups_container').hasClass('enable-collapse-feature');
        var enableAlternateCollapse = $('#yith_wapo_groups_container').hasClass('enable-alternate-collapse');
        var showAddonsCollapsed = $('#yith_wapo_groups_container').hasClass('show-addons-collapsed');
        var titleHeight = $('#yith_wapo_groups_container .ywapo_group_container h3').css('height');
        if ( enableCollapseFeature || enableAlternateCollapse ) {
            if ( ! showAddonsCollapsed ) {
                $('#yith_wapo_groups_container .ywapo_group_container h3').css('cursor', 'pointer').prepend('<span class="dashicons dashicons-arrow-down"></span> ');
                $('#yith_wapo_groups_container .ywapo_group_container h3 .dashicons').css('height', titleHeight).css('line-height', titleHeight);
                if ( $('#yith_wapo_groups_container .ywapo_group_container h3').hasClass('toggle-closed') ) {
                    $('.dashicons', this).removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
                }
                $('#yith_wapo_groups_container .ywapo_group_container h3').click(function () {
                    if ($(this).hasClass('toggle-closed')) {
                        $(this).removeClass('toggle-closed').addClass('toggle-open');
                        $('.dashicons', this).removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
                    } else {
                        $(this).removeClass('toggle-open').addClass('toggle-closed');
                        $('.dashicons', this).removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
                    }
                    $(this).parent().find('div').toggle('fast');
                });
                // Collapsed by default option
                $('#yith_wapo_groups_container .ywapo_group_container.collapsed h3' ).removeClass('toggle-open').addClass('toggle-closed');
                $('#yith_wapo_groups_container .ywapo_group_container.collapsed h3 .dashicons' ).removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
                $('#yith_wapo_groups_container .ywapo_group_container.collapsed div' ).toggle('fast');
            } else {
                $('#yith_wapo_groups_container .ywapo_group_container h3').parent().find('div').hide();
                $('#yith_wapo_groups_container .ywapo_group_container h3').css('cursor', 'pointer').prepend('<span class="dashicons dashicons-arrow-right"></span> ');
                $('#yith_wapo_groups_container .ywapo_group_container h3 .dashicons').css('height', titleHeight).css('line-height', titleHeight);
                if ($('#yith_wapo_groups_container .ywapo_group_container h3').hasClass('toggle-open')) {
                    $('.dashicons', this).removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
                }
                $('#yith_wapo_groups_container .ywapo_group_container h3').click(function () {
                    if ($(this).hasClass('toggle-open')) {
                        $(this).removeClass('toggle-open').addClass('toggle-closed');
                        $('.dashicons', this).removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
                    } else {
                        $(this).removeClass('toggle-closed').addClass('toggle-open');
                        $('.dashicons', this).removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
                    }
                    $(this).parent().find('div').toggle('fast');
                });

                // Collapsed by default
                $('#yith_wapo_groups_container .ywapo_group_container h3').removeClass('toggle-open').addClass('toggle-closed').parent().find('div').hide();
                $('#yith_wapo_groups_container .ywapo_group_container h3 .dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
            }

            if ( enableAlternateCollapse ) {

                // First item open
                $('#yith_wapo_groups_container .ywapo_group_container:first-child h3').removeClass('toggle-closed').addClass('toggle-open').parent().find('div').show();
                $('#yith_wapo_groups_container .ywapo_group_container:first-child h3 .dashicons').removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');

            }

        }
    }

    $('#yith_wapo_groups_container.enable-alternate-collapse .ywapo_group_container h3').click( function() {
        var clickedAddon = $(this);
        $('#yith_wapo_groups_container .ywapo_group_container h3').removeClass('toggle-open').addClass('toggle-closed').parent().find('div').hide();
        $('#yith_wapo_groups_container .ywapo_group_container h3 .dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
        clickedAddon.removeClass('toggle-closed').addClass('toggle-open').parent().find('div').show();
        $('.dashicons', clickedAddon).removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
    });

    /**
     * Reset add-ons type file
     */

    $('#yith_wapo_groups_container input[type=file]').change(function () {

        var has_reset = $(this).parent().find('a.yith_wapo_reset_file').length;

        if ($(this).val() != '' && has_reset < 1) {
            $(this).after('<a class="yith_wapo_reset_file" href="#">Reset</a>');
        }
    });

    $('#yith_wapo_groups_container').on('click', '.yith_wapo_reset_file', function (e) {

        var field = $(this).parent().find('input[type=file]').attr('name');
        field = field.replace('[', '_');
        field = 'span.' + field.replace(']', '');
        $(this).parent().find('input[type=file]').val('');
        $(this).parent().find('.yith_wapo_reset_file').remove();
        $('.yith_ywraq_add_item_response_message').find(field).remove();

        return false;
    });

    $(document).on('yith_wwraq_removed_successfully', function () {
        'use strict';
        $('#yith_wapo_groups_container input[type=file]').val('');
        $('#yith_wapo_groups_container .yith_wapo_reset_file').remove();
    });

    /**
     * New input type number format
     */
    jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up">+</div><div class="quantity-button quantity-down">-</div></div>').insertAfter('.ywapo_input_container_number input');
    jQuery('.ywapo_input_container_number').each(function () {
        var spinner = jQuery(this),
            input = spinner.find('input[type="number"]'),
            btnUp = spinner.find('.quantity-up'),
            btnDown = spinner.find('.quantity-down'),
            min = input.attr('min'),
            max = input.attr('max');

        btnUp.click( function () {
            var oldValue = parseFloat( input.val() );
            if ( isNaN( oldValue ) ) {
                if ( min > 0 ) { var newVal = min; }
                else { newVal = 1; }
            } else {
                if ( oldValue >= max ) { var newVal = oldValue; }
                else { var newVal = ++oldValue; }
            }
            spinner.find('input').val( newVal );
            spinner.find('input').trigger('change');
        });

        btnDown.click(function () {
            var oldValue = parseFloat(input.val());
            if (isNaN(oldValue)) {
                if (min > 0) {
                    oldValue = min;
                }
                else {
                    oldValue = 0;
                }
            }
            if (oldValue <= min) {
                var newVal = oldValue;
            }
            else {
                var newVal = oldValue - 1;
            }
            spinner.find('input').val(newVal);
            spinner.find('input').trigger("change");
        });
    });

    function wapo_qty_dependencies() {
        var qty = $('.quantity input.qty').val();
        if ( ! qty > 0 ) { qty = 0; }
        $('.ywapo_group_container' ).not( '.ywapo_conditional_hidden, .ywapo_conditional_matched, .ywapo_conditional_variation_hidden, .ywapo_conditional_variation_matched' ).hide();
        for ( var i = 0; i <= qty; i++ ) {
            $('.ywapo_group_container.min_qty_' + i ).not( '.ywapo_conditional_hidden, .ywapo_conditional_matched, .ywapo_conditional_variation_hidden, .ywapo_conditional_variation_matched' ).show();
        }
    }

    $('select.ywapo_input').change(function(){
        var imageUrl = $(this).find(':selected').data('image');
        if ( typeof imageUrl !== 'undefined' ) {
            console.log('inside');
            var image = '<img src="' + imageUrl + '" style="max-width: 100%">';
            var description = $(this).find(':selected').data('description');
            $(this).prev('div.wapo_option_image').html( image );
            $(this).next('p.wapo_option_description').html( description );
        }
    });

    setTimeout(
        function() {
            var current_selected_element = $( '.yith_wapo_groups_container input, .yith_wapo_groups_container select, .yith_wapo_groups_container textarea, div.quantity > input.qty' );
            $( '.yith_wapo_groups_container input, .yith_wapo_groups_container select, .yith_wapo_groups_container textarea, div.quantity > input.qty' ).trigger('yith-wapo-product-option-conditional', current_selected_element);
            $( '.yith_wapo_groups_container input, .yith_wapo_groups_container select, .yith_wapo_groups_container textarea, div.quantity > input.qty' ).trigger('yith-wapo-product-option-update');
        }
    , 500);

    // WooFood plugin compatibility
    $('body').on( 'click', '.woofood-quickview-button', function() {
        setTimeout( function() { ywapo_initialize(); }, 5000 );
    });
    // Popups compatibility
    $('body').on( 'click', '.wcpt-button, .wcpt-button-cart_refresh', function() {
        setTimeout( function() { ywapo_initialize(); }, 2000 );
    });

    // Test replace image
    $('body').on( 'click', '.ywapo_input_container_labels, .ywapo_input_container_checkbox', function() {
        var replaceImage = $(this).closest('.ywapo_group_container').data('change-featured-image');
        var urlSelected = $(this).find('.ywapo_single_option_image').attr('src');
        var urlSelectedFull = $(this).find('.ywapo_single_option_image').attr('fullsize');
        var urlReplace = urlSelected;
        if ( urlSelectedFull ) {
            urlReplace = urlSelectedFull;
        }
        if ( replaceImage == 1 ) {
            $('.wp-post-image').attr('src', urlReplace);
        }
    });

});
