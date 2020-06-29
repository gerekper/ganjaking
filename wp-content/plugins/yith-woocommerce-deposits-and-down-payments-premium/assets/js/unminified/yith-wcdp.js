jQuery(document).ready(function($){
    var form_cart = $( 'form.cart' ),
        payment_type_radio = $('input[name="payment_type"]'),
        deposit_shipping = $('.yith-wcdp-deposit-shipping'),
        deposit_shipping_form = deposit_shipping.find('.yith-wcdp-shipping-form'),
        xhr = null,
        deposit_shipping_toggle = function(){
            if( payment_type_radio.length ) {
                payment_type_radio.on('change', function () {
                    var t = $(this),
                        val = t.val();

                    if( ! t.is(':checked') ){
                        return;
                    }

                    if (val === 'deposit') {
                        deposit_shipping.slideDown();
                    }
                    else {
                        deposit_shipping.slideUp();
                    }
                }).change();
            }
            else if( deposit_shipping.length ){
                deposit_shipping.slideDown();
            }
        },
        deposit_shipping_handling = function(){
            // hide shipping form by default
            $( '.shipping-calculator-form' ).hide();

            // choose whether to show or hide shipping form
            deposit_shipping_toggle();

            // init "Calculate Shipping" Button
            $( document ).on( 'click', '.shipping-calculator-button', function() {
                $( '.shipping-calculator-form' ).slideToggle( 'slow', function(){
                    $( 'body' ).trigger( 'country_to_state_changed' );
                } );
                return false;
            });
        },
        deposit_shipping_reinit = function( variation ){
            // update variables
            deposit_shipping = form_cart.find('.yith-wcdp-deposit-shipping');
            payment_type_radio = $('input[name="payment_type"]');

            if (deposit_shipping.length) {

                deposit_shipping_toggle();

                $.ajax({
                    beforeSend: function () {
                        deposit_shipping.block({
                            message   : null,
                            overlayCSS: {
                                background: '#fff',
                                opacity   : 0.6
                            }
                        });
                    },
                    complete  : function () {
                        deposit_shipping.unblock();
                    },
                    data      : {
                        product_id: variation.variation_id,
                        qty       : form_cart.find('input[name="quantity"]').val(),
                        action    : yith_wcdp.actions.calculate_shipping
                    },
                    dataType  : 'json',
                    method    : 'post',
                    success   : function (data) {
                        deposit_shipping_form.find('table').html(data.template);
                    },
                    url       : yith_wcdp.ajax_url
                })
            }
        };

    // change shipping methods when ever a variation is selected
    form_cart
        .on( 'found_variation', function(ev, variation){
            var t = $(this),
                woocommerce_variation_add_to_cart = t.find('.woocommerce-variation-add-to-cart'),
                deposit_options = t.find( '.yith-wcdp-single-add-to-cart-fields'),
                deposit_full_price_placeholder = deposit_options.find('.full-price'),
                deposit_price_placeholder = deposit_options.find('.deposit-price'),
                deposit_type = deposit_options.data('deposit-type'),
                deposit_amount = deposit_options.data('deposit-amount'),
                deposit_rate = deposit_options.data('deposit-rate'),
                full_price = 0,
                full_price_html = '',
                deposit_price = 0,
                deposit_price_html = '';

            if( yith_wcdp.ajax_variations ){
                xhr = $.ajax({
                    beforeSend: function(){
                        if( xhr != null ) {
                            xhr.abort();
                        }

                        t.block({
                            message   : null,
                            overlayCSS: {
                                background: '#fff',
                                opacity   : 0.6
                            }
                        });
                    },
                    complete: function(){
                        t.unblock();
                    },
                    data: {
                        variation_id: variation.variation_id,
                        action: 'get_deposit_template'
                    },
                    dataType: 'html',
                    success: function( data ){
                        if( deposit_options.length ){
                            deposit_options.parent().remove();
                        }
                        woocommerce_variation_add_to_cart.before( data );
                        deposit_options = t.find( '.yith-wcdp-single-add-to-cart-fields');
                        deposit_shipping_reinit( variation );

                        $(document).trigger( 'yith_wcdp_updated_deposit_form', deposit_options );
                    },
                    url: yith_wcdp.ajax_url
                });
            }
            else if( typeof( variation.add_deposit_to_cart ) !== 'undefined' ){
                if( deposit_options.length ){
                    deposit_options.parent().remove();
                }
                woocommerce_variation_add_to_cart.before( variation.add_deposit_to_cart );

                deposit_shipping_reinit( variation );

                $(document).trigger( 'yith_wcdp_updated_deposit_form', deposit_options );
            }
            else if( deposit_options.length ) {
                full_price = variation.display_price;
                full_price_html = '( ' + accounting.formatMoney( full_price, {
                    symbol:    yith_wcdp.currency_format.symbol,
                    decimal:   yith_wcdp.currency_format.decimal,
                    thousand:  yith_wcdp.currency_format.thousand,
                    precision: yith_wcdp.currency_format.precision,
                    format:    yith_wcdp.currency_format.format
                } ) + ' )';

                if (deposit_type === 'amount') {
                    deposit_price = Math.min( full_price, deposit_amount );
                }
                else {
                    deposit_price = full_price * deposit_rate / 100;
                    deposit_price = Math.min( full_price, deposit_price );
                }

                deposit_price_html = '( ' + accounting.formatMoney( deposit_price, {
                    symbol:    yith_wcdp.currency_format.symbol,
                    decimal:   yith_wcdp.currency_format.decimal,
                    thousand:  yith_wcdp.currency_format.thousand,
                    precision: yith_wcdp.currency_format.precision,
                    format:    yith_wcdp.currency_format.format
                } ) + ' )';

                deposit_full_price_placeholder.html(full_price_html);
                deposit_price_placeholder.html(deposit_price_html);

                deposit_shipping_reinit( variation );

                $(document).trigger( 'yith_wcdp_updated_deposit_form', deposit_options );
            }
        })

        .on( 'reset_data', function(){
            var add_deposit_to_cart = $('#yith-wcdp-add-deposit-to-cart');

            if( add_deposit_to_cart.parents('.variations_form').length && yith_wcdp.variations_handling ){
                add_deposit_to_cart.remove();
            }
        })

        .on( 'click','.reset_variations',function(){
            $("#yith-wcdp-add-deposit-to-cart").hide();
        } )

        .on( 'click', '#yith-wcdp-add-deposit-to-cart button[name="calc_shipping"]', function(ev){
            ev.preventDefault();

            var button = $(this),
                form_cart = $('form.cart'),
                product_id = form_cart.find( 'button[name="add-to-cart"]' ).val(),
                variation_id = form_cart.find( 'input[name="variation_id"]' ).val(),
                calc_shipping_form = button.closest('.shipping-calculator-form'),
                calc_shipping_country = calc_shipping_form.find('#calc_shipping_country').val(),
                calc_shipping_city = calc_shipping_form.find('#calc_shipping_city').val(),
                calc_shipping_state = calc_shipping_form.find('#calc_shipping_state').val(),
                calc_shipping_postcode = calc_shipping_form.find('#calc_shipping_postcode').val();

            if( ! product_id && ! variation_id ){
                product_id = form_cart.find( 'input[name="add-to-cart"]' ).val();
            }

            if( ! product_id && ! variation_id ){
                return;
            }

            $.ajax({
                beforeSend: function () {
                    deposit_shipping.block({
                        message   : null,
                        overlayCSS: {
                            background: '#fff',
                            opacity   : 0.6
                        }
                    });
                },
                complete  : function () {
                    deposit_shipping.unblock();
                },
                data      : {
                    action: yith_wcdp.actions.change_location,
                    product_id: variation_id ? variation_id : product_id,
                    qty : form_cart.find('input[name="quantity"]').val(),
                    calc_shipping_country: calc_shipping_country,
                    calc_shipping_city: calc_shipping_city,
                    calc_shipping_state: calc_shipping_state,
                    calc_shipping_postcode: calc_shipping_postcode
                },
                dataType  : 'json',
                method    : 'post',
                success   : function (data) {
                    // close shipping calculator div
                    $( '.shipping-calculator-form' ).slideUp( 'slow' );

                    // fill table with calculated shipping methods
                    deposit_shipping_form.find( 'table' ).html( data.template );

                    // shows notices, if any
                    if( typeof data.notices != 'undefined' ){
                        deposit_shipping.prepend( $('<div>').addClass( 'yith-wcdp-messages' ).html( data.notices ) ).slideDown();

                        window.setTimeout( function(){
                            $('.yith-wcdp-messages').slideUp( 'slow', function(){
                                $(this).remove();
                            } );
                        }, 3000 );
                    }
                },
                url : yith_wcdp.ajax_url
            });
        } )

        .find('select:eq(0)').change();

    $(document).on( 'yith_wcpb_ajax_update_price_request', function( ev, response ){
        var deposit_options = $( '.yith-wcdp-single-add-to-cart-fields'),
            deposit_full_price_placeholder = deposit_options.find('.full-price'),
            deposit_price_placeholder = deposit_options.find('.deposit-price');

        if( ! deposit_options.length || typeof response == 'undefined' || typeof response.deposit_html == 'undefined' ){
            return;
        }


        deposit_full_price_placeholder.html( '( ' + response.price_html + ' )' );
        deposit_price_placeholder.html( '( ' + response.deposit_html + ' )' );
    } );

    $(document).on( 'yith_wcevti_price_refreshed yith_wapo_product_price_updated yith_wcp_price_updated', function( ev, total ){
        var deposit_options = $( '.yith-wcdp-single-add-to-cart-fields'),
            deposit_full_price_placeholder = deposit_options.find('.full-price'),
            deposit_price_placeholder = deposit_options.find('.deposit-price'),
            deposit_type = deposit_options.data('deposit-type'),
            deposit_amount = deposit_options.data('deposit-amount'),
            deposit_rate = deposit_options.data('deposit-rate'),
            full_price = total,
            full_price_html,
            deposit_price = 0,
            deposit_price_html,
            ywcp_wcp_group_total = $('.ywcp_wcp_group_total');

        if( ! deposit_options.length ){
            return;
        }

        // skip yith_wapo_product_price_updated processing is product is composite (the correct price will come from yith_wcp_price_updated event)
        if( ywcp_wcp_group_total.length === 1 && ev.type === 'yith_wapo_product_price_updated' ){
            return;
        }

        full_price_html = '( ' + accounting.formatMoney( full_price, {
            symbol:    yith_wcdp.currency_format.symbol,
            decimal:   yith_wcdp.currency_format.decimal,
            thousand:  yith_wcdp.currency_format.thousand,
            precision: yith_wcdp.currency_format.precision,
            format:    yith_wcdp.currency_format.format
        } ) + ' )';

        if (deposit_type === 'amount') {
            deposit_price = Math.min( full_price, deposit_amount );
        }
        else {
            deposit_price = full_price * deposit_rate / 100;
            deposit_price = Math.min( full_price, deposit_price );
        }

        deposit_price_html = '( ' + accounting.formatMoney( deposit_price, {
            symbol:    yith_wcdp.currency_format.symbol,
            decimal:   yith_wcdp.currency_format.decimal,
            thousand:  yith_wcdp.currency_format.thousand,
            precision: yith_wcdp.currency_format.precision,
            format:    yith_wcdp.currency_format.format
        } ) + ' )';

        deposit_full_price_placeholder.html(full_price_html);
        deposit_price_placeholder.html(deposit_price_html);
    } );


    deposit_shipping_handling();
});