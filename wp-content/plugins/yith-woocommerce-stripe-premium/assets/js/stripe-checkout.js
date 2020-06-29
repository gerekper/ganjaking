/* global Stripe, yith_stripe_info, woocommerce_params */

(function ( $ ) {

    var stripe = Stripe( yith_stripe_info.public_key ),

        // init error handling
        handle_elements_error = function( response, args ) {
            var defaults = {
                    form: $('.woocommerce-notices-wrapper'),
                    unblock: $( '.woocommerce' )
                },
                args = $.extend( defaults, args );

            args.unblock.unblock();

            $( '.woocommerce-error', args.form ).remove();

            if ( response.error ) {
                // Remove token, if any
                $( '.stripe-intent', args.form ).remove();

                // Show the errors on the form
                if ( response.error.message ) {
                    args.form.prepend( '<ul class="woocommerce-error"><li>' + response.error.message + '</li></ul>' );
                }
            }
        },

        // init form submit
        handle_form_submit = function( event ){
            var session_id = $( this ).data( 'session_id' );

            if( ! session_id ){
                return false;
            }

            $( '.woocommerce' ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            stripe.redirectToCheckout({
                sessionId: session_id
            }).then(function (result) {
                if( typeof result.error != 'undefined' ) {
                    handle_elements_error(result);
                }
            });
        };

    $( document ).on( 'ready ywsbs-auto-renew-opened', function(){
        // handle form submit: checkout form
        $( '#yith_wcstripe_open_checkout' ).on( 'click', handle_form_submit ).click();
    } );

})(jQuery);