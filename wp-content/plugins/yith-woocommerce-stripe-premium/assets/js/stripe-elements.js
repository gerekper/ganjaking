/* global Stripe, yith_stripe_info, woocommerce_params */

(function ( $ ) {

    var $body = $( 'body' ),
        style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '16px',
                color: '#333'
            }
        },
        stripe = Stripe( yith_stripe_info.public_key ),
        elements = stripe.elements(),
        card,
        cardExpiry,
        cardCvc,

        // init Stripe Elements fields
        init_elements = function() {
            // Add an instance of the card Element into the `card-element` <div>.
            if( $( yith_stripe_info.elements_container_id ).length ) {

                if( typeof card != 'undefined' ){
                    card.destroy();
                }

                card = elements.create( 'card', { style: style, hidePostalCode: ! yith_stripe_info.show_zip } );
                card.mount(yith_stripe_info.elements_container_id);
            }
            else{
                var number = $( '#yith-stripe-card-number' ),
                    expiry = $( '#yith-stripe-card-expiry' ),
                    cvc    = $( '#yith-stripe-card-cvc' );

                if( number.length ){
                    var placeholder = number.attr('placeholder');

                    if( typeof card != 'undefined' ){
                        card.destroy();
                    }

                    card = elements.create( 'cardNumber', { style: style, placeholder: placeholder } );

                    number.replaceWith( '<div id="yith-stripe-card-number" class="yith-stripe-elements-field">' );
                    card.mount( '#yith-stripe-card-number' );
                }

                if( expiry.length ) {
                    var placeholder = expiry.attr('placeholder');

                    if( typeof cardExpiry != 'undefined' ){
                        cardExpiry.destroy();
                    }

                    cardExpiry = elements.create( 'cardExpiry', { style: style, placeholder: placeholder } );

                    expiry.replaceWith( '<div id="yith-stripe-card-expiry" class="yith-stripe-elements-field">' );
                    cardExpiry.mount( '#yith-stripe-card-expiry' );
                }

                if( cvc.length ) {
                    var placeholder = cvc.attr('placeholder');

                    if( typeof cardCvc != 'undefined' ){
                        cardCvc.destroy();
                    }

                    cardCvc = elements.create( 'cardCvc', { style: style, placeholder: placeholder } );

                    cvc.replaceWith( '<div id="yith-stripe-card-cvc" class="yith-stripe-elements-field">' );
                    cardCvc.mount( '#yith-stripe-card-cvc' );
                }
            }
        },

        // init error handling
        handle_elements_error = function( response, args ) {
            var defaults = {
                    form: $( '#wc-yith-stripe-cc-form, #yith-stripe-cc-form' ).closest('.payment_method_yith-stripe'),
                    unblock: $( '.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table, #add_payment_method, #order_review' )
                },
                args = $.extend( defaults, args );

            args.unblock.removeClass('processing').unblock();

            $( '.woocommerce-error', args.form ).remove();

            if ( response.error ) {
                // Remove token, if any
                $( '.stripe-intent', args.form ).remove();

                // Show the errors on the form
                if ( response.error.message ) {
                    var error = $( '<ul>', { class: 'woocommerce-error' } ).append( $( '<li>', { text: response.error.message } ) );

                    args.form.prepend( error );
                    $('html, body').animate( { scrollTop: error.offset().top } );
                }
            }
        },

        // init form submit
        handle_form_submit = function( event ){
            if ( $( 'input#payment_method_yith-stripe' ).is( ':checked' ) && 0 === $( 'input.stripe-intent' ).length ) {
                var ccForm = $( '#wc-yith-stripe-cc-form, #yith-stripe-cc-form' ),
                    $form =  $( 'form.checkout, form#order_review, form#add_payment_method' ),
                    toBlockForms = $( '.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table, #add_payment_method' ),
                    nameInput = $( '#yith-stripe-card-name' ),
                    billing_email = $('#billing_email'),
                    billing_country_input = $('#billing_country'),
                    billing_city_input = $('#billing_city:visible'),
                    billing_address_1_input = $('#billing_address_1:visible'),
                    billing_address_2_input = $('#billing_address_2:visible'),
                    billing_state_input = $('select#billing_state:visible, input#billing_state:visible'),
                    cardData = filter_empty_attributes( {
                        billing_details: {
                            name: nameInput.length ? nameInput.val() : $('#billing_first_name' ).val() + ' ' + $('#billing_last_name' ).val(),
                            address: {
                                line1  : billing_address_1_input.length ? billing_address_1_input.val() : '',
                                line2  : billing_address_2_input.length ? billing_address_2_input.val() : '',
                                city   : billing_city_input.length ? billing_city_input.val() : '',
                                state  : billing_state_input.length ? billing_state_input.val() : '',
                                country: billing_country_input.length ? billing_country_input.val() : ''
                            },
                            email: billing_email.length ? billing_email.val() : ''
                        }
                    } ),
                    selectedCard = $( 'input[name="wc-yith-stripe-payment-token"]:checked');

                // update PaymentIntent
                selectedCard = selectedCard.length && 'new' !== selectedCard.val() ? selectedCard.val() : false;

                toBlockForms.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                if( ! selectedCard ){
                    stripe.createPaymentMethod( 'card', card, cardData ).then( function( result ){
                        if (result.error) {
                            handle_elements_error(result);
                        } else {
                            ccForm.append('<input type="hidden" class="stripe-intent" name="stripe_intent" value=""/>');
                            ccForm.append('<input type="hidden" class="stripe-payment-method" name="stripe_payment_method" value="' + result.paymentMethod.id + '"/>');

                            toBlockForms.unblock();
                            $form.submit();
                        }
                    } )
                }
                else{
                    ccForm.append('<input type="hidden" class="stripe-intent" name="stripe_intent" value=""/>');
                    toBlockForms.unblock();
                    $form.submit();
                }

                return false;
            }

            return event;
        },

        // init add payment method
        handle_method_add = function( event ){
            if ( $( 'input#payment_method_yith-stripe' ).is( ':checked' ) && 0 === $( 'input.stripe-intent' ).length ) {
                var ccForm = $('#wc-yith-stripe-cc-form, #yith-stripe-cc-form'),
                    $form = $('form#add_payment_method'),
                    toBlockForms = $('#add_payment_method'),
                    nameInput = $('#yith-stripe-card-name'),
                    billing_email = $('#billing_email'),
                    billing_country_input = $('#billing_country'),
                    billing_city_input = $('#billing_city:visible'),
                    billing_address_1_input = $('#billing_address_1:visible'),
                    billing_address_2_input = $('#billing_address_2:visible'),
                    billing_state_input = $('select#billing_state:visible, input#billing_state:visible'),
                    cardData = filter_empty_attributes({
                        payment_method_data: {
                            billing_details: {
                                name   : nameInput.length ? nameInput.val() : $('#billing_first_name').val() + ' ' + $('#billing_last_name').val(),
                                address: {
                                    line1  : billing_address_1_input.length ? billing_address_1_input.val() : '',
                                    line2  : billing_address_2_input.length ? billing_address_2_input.val() : '',
                                    city   : billing_city_input.length ? billing_city_input.val() : '',
                                    state  : billing_state_input.length ? billing_state_input.val() : '',
                                    country: billing_country_input.length ? billing_country_input.val() : ''
                                },
                                email  : billing_email.length ? billing_email.val() : ''
                            }
                        },
                        save_payment_method: true
                    }),
                    selectedCard = $( 'input[name="wc-yith-stripe-payment-token"]:checked'),
                    intent_id,
                    intent_secret;

                // update PaymentIntent
                selectedCard = selectedCard.length && 'new' !== selectedCard.val() ? selectedCard.val() : false;

                toBlockForms.block({
                    message   : null,
                    overlayCSS: {
                        background: '#fff',
                        opacity   : 0.6
                    }
                });

                update_intent( selectedCard ).then(function (data) {
                    if (typeof data.res != 'undefined') {
                        if (!data.res && typeof data.error != 'undefined') {
                            handle_elements_error(data);
                            return false;
                        }
                    }

                    if (typeof data.refresh != 'undefined' && data.refresh){
                        window.location.reload();
                        return false;
                    }

                    intent_id = data.intent_id;
                    intent_secret = data.intent_secret;

                    if( ! selectedCard ) {
                        stripe.handleCardSetup(intent_secret, card, cardData).then(function (result) {
                            if (result.error) {
                                handle_elements_error(result);
                            } else {
                                intent_id = typeof result.paymentIntent != 'undefined' ? result.paymentIntent.id : result.setupIntent.id;

                                ccForm.append('<input type="hidden" class="stripe-intent" name="stripe_intent" value="' + intent_id + '"/>');
                                toBlockForms.unblock();
                                $form.submit();
                            }
                        });
                    }
                    else{
                        ccForm.append('<input type="hidden" class="stripe-intent" name="stripe_intent" value="' + intent_id + '"/>');
                        toBlockForms.unblock();
                        $form.submit();
                    }
                });

                return false;
            }

            return event;
        },

        // handle hash change
        on_hash_change = function() {
            var partials = window.location.hash.match( /^#?yith-confirm-pi-([^:]+):(.+)$/ );

            if ( ! partials || 3 > partials.length ) {
                return;
            }

            var intentClientSecret = partials[1];
            var redirectURL        = decodeURIComponent( partials[2] );

            // Cleanup the URL
            window.location.hash = '';

            open_intent_modal( intentClientSecret, redirectURL );
        },

        // manual confirmation for payment intent
        open_intent_modal = function( secret, redirectURL ){
            var $form =  $( 'form.checkout, form#order_review' ),
                handler = secret.indexOf( 'seti' ) < 0 ? 'handleCardAction' : 'handleCardSetup';

            stripe[handler]( secret ).then( function( result ){
                if ( result.error ) {
                    handle_elements_error( result, {
                        unblock: $form
                    } )
                }
                else {
                    window.location = redirectURL;
                }
            } ).catch( function( error ) {
                error.log( error );
            } );
        },

        // remove token from DOM
        remove_token = function(){
            $( '.stripe-intent' ).remove();
            $( '.stripe-payment-method' ).remove();
        },

        // handle card selection
        handle_card_selection = function(){
            var $cards = $( '#payment').find( 'div.cards');

            if ( $cards.length ) {
                $cards.siblings( 'fieldset#wc-yith-stripe-cc-form, fieldset#yith-stripe-cc-form').hide();

                $( 'body' ).on( 'updated_checkout', function() {
                    $( '#payment').find( 'div.cards').siblings( 'fieldset#wc-yith-stripe-cc-form, fieldset#yith-stripe-cc-form').hide();
                });

                $('form.checkout, form#order_review').on( 'change', '#payment input[name="wc-yith-stripe-payment-token"]', function(){
                    var input = $(this),
                        $cards = $( '#payment').find( 'div.cards');

                    // change selected
                    $cards.find('div.card').removeClass('selected');
                    $cards.find('input[name="wc-yith-stripe-payment-token"]:checked').closest('div.card').addClass('selected');

                    if ( input.val() === 'new' ) {
                        $cards.siblings( 'fieldset#wc-yith-stripe-cc-form, fieldset#yith-stripe-cc-form').show();
                    } else {
                        $cards.siblings( 'fieldset#wc-yith-stripe-cc-form, fieldset#yith-stripe-cc-form').hide();
                    }
                });
            }
        },

        // update paymentIntent
        update_intent = function( token ){
            var data = [];

            if( yith_stripe_info.is_checkout && ! yith_stripe_info.order ){
                data = $( 'form.checkout' ).serializeArray();
            }

            return $.ajax( {
                data: pushRecursive( data, {
                    action: 'yith_stripe_refresh_intent',
                    yith_stripe_refresh_intent: yith_stripe_info.refresh_intent,
                    selected_token: token,
                    is_checkout: yith_stripe_info.is_checkout,
                    order: yith_stripe_info.order
                } ),
                method: 'POST',
                url: yith_stripe_info.ajaxurl
            } );
        },

        // confirm card
        confirm_card = function(ev){
            ev.preventDefault();

            var t = $(this),
                h = t.attr('href'),
                r = /.*\/([0-9]*)\//ig,
                selectedCard = r.exec( h )[1],
                intent_secret,
                intent_id;

            $( 'table.account-payment-methods-table' ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            update_intent(selectedCard).then(function (data) {
                if (typeof data.res != 'undefined') {
                    if (!data.res && typeof data.error != 'undefined') {
                        handle_elements_error(data);
                        return false;
                    }
                }

                if (typeof data.refresh != 'undefined' && data.refresh){
                    window.location.reload();
                    return false;
                }

                intent_secret = data.intent_secret;

                stripe.handleCardSetup( intent_secret ).then(function (result) {
                    if (result.error) {
                        handle_elements_error(result, {
                            form: $( '.woocommerce-notices-wrapper' ),
                            unblock: $( '.account-payment-methods-table' )
                        });
                    } else {
                        intent_id = result.setupIntent.id;

                        window.location = h + '&stripe_intent=' + intent_id;
                    }
                });
            });
        },

        // init cvc popup
        cvv_lightbox = function(){
            if ( typeof $.fn.prettyPhoto == 'undefined' ) {
                return;
            }

            $('.woocommerce #payment ul.payment_methods li, form#add_payment_method').find( 'a.cvv2-help' ).prettyPhoto({
                hook: 'data-rel',
                social_tools: false,
                theme: 'pp_woocommerce',
                horizontal_padding: 20,
                opacity: 0.8,
                deeplinking: false
            });
        },

        // utility: removes empty attributes from objects
        filter_empty_attributes = function( object ){
            var result = {},
                key,
                value;

            if( typeof object != 'object' ){
                return object;
            }

            for( key in object ){
                if ( ! object.hasOwnProperty( key ) ) {
                    continue;
                }

                value = typeof object[ key ] == 'object' ? filter_empty_attributes( object[ key ] ) : object[ key ];

                if( value && ! $.isEmptyObject( value ) ){
                    result[ key ] = value;
                }
            }

            return result
        },

        // utility: add data to array that comes from $.serializeArray()
        pushRecursive = function( arr, data ){
            var key;

            for( key in data ){
                if ( ! data.hasOwnProperty( key ) ) {
                    continue;
                }

                arr.push( {
                    name: key,
                    value: data[ key ]
                } );
            }

            return arr;
        };

    $( document ).on( 'ready ywsbs-auto-renew-opened', function(){

        $( 'table.account-payment-methods-table' ).on( 'click', '.confirm', confirm_card );

        // init elements handling, if container was found
        if( $( yith_stripe_info.elements_container_id ).length || $( '#yith-stripe-card-number' ).length ){

            init_elements();
            cvv_lightbox();
            handle_card_selection();
            on_hash_change();

            // handles errors messages
            card.addEventListener( 'change', handle_elements_error );

            // handles hash change
            window.addEventListener( 'hashchange', on_hash_change );

            // init elements and updates it when checkout is updated
            $body.on( 'updated_checkout', init_elements );

            // init cc popup when checkout form is updated
            $body.on( 'updated_checkout', cvv_lightbox );

            // handle checkout error
            $body.on( 'checkout_error', remove_token );

            // handle form submit: checkout form
            $( 'form.checkout' ).on( 'checkout_place_order_yith-stripe', handle_form_submit );

            // handle form submit: pay form
            $( 'form#order_review' ).on( 'submit', handle_form_submit );

            // handle form submit: add card form
            $( 'form#add_payment_method' ).on( 'submit', handle_method_add );

            // handle change of payment method
            $( 'form.checkout, form#order_review, form#add_payment_method' ).on( 'change', '#wc-yith-stripe-cc-form input, #yith-stripe-cc-form input', remove_token);

        }
    } );

})(jQuery);