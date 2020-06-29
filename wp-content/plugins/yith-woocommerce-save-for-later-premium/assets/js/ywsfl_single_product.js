jQuery(document).ready(function ($) {
    var get_variation = function () {
            var variations = $(document).find('select[name^="attribute"]'),
                var_items = {};

            variations.each(function () {

                var t = $(this),
                    name = t.attr('name');

                var_items[name] = t.val();
            });

            return var_items;
        },
        show_notice = function (html_element, $target) {
            if (!$target) {
                $target = $('.woocommerce-notices-wrapper:first') || $('.cart-empty').closest('.woocommerce') || $('.woocommerce-cart-form');
            }
            $target.find('.woocommerce-message').remove();
            $target.prepend(html_element);
        };

    if ($('form.variations_form.cart').length) {


        $('.variations_form').on('show_variation', function (e, variation, purchasable) {

            var variation_id = variation.variation_id,
                product_id = $('input[name="add-to-cart"]').val(),
                variations = get_variation(),
                data = {
                    'product_id': product_id,
                    'variation_id': variation_id,
                    'variation': variations,
                    'action': 'check_if_variation_is_in_list'
                },
                block_params = {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    },
                    ignoreIfBlocked: true
                },
                container = $(this).parent();


            $('.ywslf_variation_id').val(variation_id);
            $('.ywsfl_single_message').html('');
            container.block(block_params);
            $.ajax({
                type: 'POST',
                url: ywsfl_single_product_args.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    if (response.in_save_list) {
                        $('.ywsfl_single_remove').removeClass('ywsfl_hide');
                        $('.ywsfl_single_add').addClass('ywsfl_hide');
                        $('input[name="save_item_id"]').val(response.in_save_list)
                    } else {
                        $('.ywsfl_single_add').removeClass('ywsfl_hide').attr('disabled', false);
                        $('.ywsfl_single_remove').addClass('ywsfl_hide');

                        $('input[name="save_item_id"]').val('');
                    }
                    container.unblock();
                }
            });

        }).on('reset_data', function (e) {

            $('.ywsfl_single_remove').addClass('ywsfl_hide');
            $('.ywsfl_single_add').addClass('ywsfl_hide');
        });

    }

    $(document).on('click', '.ywsfl_single_add', function (e) {

        e.preventDefault();

        if ($(document).find('form.cart').length) {

            var form_cart = $(document).find('form.cart'),
                product_id = form_cart.find('.single_add_to_cart_button').val(),
                variation_id = '',
                variation = {};

            if (form_cart.hasClass('variations_form')) {
                product_id = form_cart.find('input[name="product_id"]').val();
                variation_id = form_cart.find('input[name="variation_id"]').val();
                variation = get_variation();
            }
        }
        var t = $(this),
            data = {
                'product_id': product_id,
                'variation_id': variation_id,
                'variation': variation,
                'action': ywsfl_single_product_args.actions.add_single_product_save_list
            };

        $.ajax({
            type: 'POST',
            url: ywsfl_single_product_args.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {


                show_notice(response.notice);

                if (response.last_item_id) {
                    t.parent().find('input[name="save_item_id"]').val(response.last_item_id);
                    var url = "<a href='" + ywsfl_single_product_args.view_list.url + "'>" + ywsfl_single_product_args.view_list.label + "</a>";
                    t.addClass('ywsfl_hide');
                    t.parent().find('.ywsfl_single_message').html(url).show();
                    t.parent().find('.ywsfl_single_remove ').removeClass('ywsfl_hide');

                    $('body').trigger('added_to_save_for_later_list',[product_id,variation_id,variation] );
                }

            },
            complete: function () {
                $.scroll_to_notices($('[role="alert"]'));
            }

        });

    })
        .on('click', '.ywsfl_single_remove', function (e) {
            e.preventDefault();
            var t = $(this),
                item_id = t.parent().find('input[name="save_item_id"]').val(),
                data = {
                    'remove_from_savelist': item_id,
                    'action': 'remove_from_savelist'
                };

            $.ajax({
                type: 'POST',
                url: ywsfl_single_product_args.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {


                    $('.ywsfl_single_remove').addClass('ywsfl_hide');
                    t.parent().find('.ywsfl_single_add').removeClass('ywsfl_hide').attr('disabled', false);
                    show_notice(response.notice);
                },
                complete: function () {
                    $.scroll_to_notices($('[role="alert"]'));
                }

            });

        });


    $('body').on('added_to_save_for_later_list', function (e, product_id, variation_id, variation ) {

        e.preventDefault();
        var data = {
            'product_id': product_id,
            'variation_id': variation_id,
            'variation': variation,
            'action': ywsfl_single_product_args.actions.remove_after_add_list
        };

        $.ajax({
            type: 'POST',
            url: ywsfl_single_product_args.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                var response_result = response.result,
                    cart_item_key = response.cart_item_key;


                if (response_result) {


                    refresh_mini_cart(cart_item_key);

                }
            }

        });


    }).on('refresh_save_for_later_list', function (e, message, template) {

        refresh_save_list(message, template);
    });


    function refresh_mini_cart($cart_item_key) {

        var mini_cart = $(document).find('.cart_list'),
            mini_cart_item = mini_cart.find('.mini_cart_item a:first-child');


        mini_cart_item.each(function () {
            var t = $(this);
            href = t.attr('href');


            if (href.search($cart_item_key) != -1) {
                window.location.href = href;

            }
        });


    }

    function refresh_save_list(message, template) {

        var save_container_list = $(document).find('#ywsfl_general_content');

        if (save_container_list.length) {

            save_container_list.replaceWith(template);
        }
    }

    $(document).on('added_to_cart', 'body', function (ev, fragments, cart_hash, button) {
        var content = button.closest('#ywsfl_general_content'),
            row = button.closest('div.ywsfl-row');


        if (content.length != 0) {
            $('.ywsfl_single_add').removeClass('ywsfl_hide');
            $('.ywsfl_single_remove').addClass('ywsfl_hide');
            $(document).find('.ywsfl_single_remove').trigger('click');

        }

    });

});
