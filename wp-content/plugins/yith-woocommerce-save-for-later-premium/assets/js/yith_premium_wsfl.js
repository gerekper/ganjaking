jQuery( document ).ready( function( $ ){

    var call_ajax_add_to_cart_variable  =   function ( button, product_id, variation_id, variations ) {

        var var_items = {};


        variations.each( function () {

            var t = $(this),
                name = t.attr('name'),
                value = t.val();

            var_items[name] = value;
        });

        var data = {

            product_id: product_id,
            variation_id: variation_id,
            variation: var_items,
            action: yith_wsfl_premium_l10n.actions.add_to_cart_variable
        }

        button.removeClass( 'added' );
        button.addClass( 'loading' );
        $('body').trigger('adding_to_cart', [button, data]);

        $.ajax({

            type: 'POST',
            url: yith_wsfl_premium_l10n.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {

                var this_page = window.location.toString();

                this_page = this_page.replace('add-to-cart', 'added-to-cart');

               button.removeClass('loading');

                if (response.error && response.product_url) {
                    window.location = response.product_url;
                    return;
                }

                fragments = response.fragments;
                cart_hash = response.cart_hash;

                // Block fragments class
                if (fragments) {
                    $.each(fragments, function (key, value) {
                        $(key).addClass('updating');
                    });
                }

                // Block widgets and fragments
                $('.shop_table.cart, .updating, .cart_totals,.widget_shopping_cart_top').fadeTo('400', '0.6').block({
                    message: null,
                    overlayCSS: {
                        background: 'transparent url(' + woocommerce_params.ajax_loader_url + ') no-repeat center',
                        backgroundSize: '16px 16px',
                        opacity: 0.6
                    }
                });

                // Changes button classes
                button.addClass('added');


                // View cart text
                if (!wc_add_to_cart_params.is_cart && button.parent().find('.added_to_cart').size() === 0) {
                    button.after(' <a href="' + wc_add_to_cart_params.cart_url + '" class="added_to_cart wc-forward" title="' +
                    wc_add_to_cart_params.i18n_view_cart + '">' + wc_add_to_cart_params.i18n_view_cart + '</a>');
                }


                // Replace fragments
                if (fragments) {
                    $.each(fragments, function (key, value) {
                        $(key).replaceWith($($.trim(value)));
                    });
                }

                // Unblock
                $('.widget_shopping_cart, .updating, .widget_shopping_cart_top').stop(true).css('opacity', '1').unblock();

                // Cart page elements
                $('.widget_shopping_cart_top').load(this_page + ' .widget_shopping_cart_top:eq(0) > *', function () {

                 $('.widget_shopping_cart_top').stop(true).css('opacity', '1').unblock();

                    $('body').trigger('cart_page_refreshed');
                });

                // Cart page elements
                $('.shop_table.cart').load(this_page + ' .shop_table.cart:eq(0) > *', function () {

                    $('.shop_table.cart').stop(true).css('opacity', '1').unblock();

                    $('body').trigger('cart_page_refreshed');
                });

                $('.cart_totals').load(this_page + ' .cart_totals:eq(0) > *', function () {
                    $('.cart_totals').stop(true).css('opacity', '1').unblock();
                });

                $('body').trigger('added_to_cart', [fragments, cart_hash, button]);
            }

        });
        // Trigger event so themes can refresh other areas

    }

    $(document).on('click', '#ywsfl_general_content .product_type_variation', function( ev ){

        var product_id      = $(this).data('product_id'),
            variation_id    = $(document).find( 'input[name=variation_id]').val(),
            variations      = $(document).find('input[name^=attribute]' );

        ev.preventDefault();

        call_ajax_add_to_cart_variable( $(this), product_id, variation_id, variations );


    });

});