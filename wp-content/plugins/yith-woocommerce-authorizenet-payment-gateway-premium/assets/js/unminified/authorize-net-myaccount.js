jQuery(document).ready(function($){
    "use strict";

    // set default card
    $('.woocommerce table.my_account_authorize_payment_methods')

        .on( 'click', '[data-table-action]', function(e){
            e.preventDefault();

            var table = $(this).closest('table'),
                actionurl = $(this).attr('href'),
                action = $(this).data('table-action');

            // Block widgets and fragments
            table.fadeTo( '400', '0.6' ).block({
                message: null,
                overlayCSS: {
                    backgroundColor: '#fff',
                    opacity: 0.6
                }
            });

            // remove messages
            table.siblings('[class^="woocommerce-"]').remove();

            // move default label
            if ( action == 'default ' ) {
                $(this).replaceWith( table.find('span.default') );
            }

            $.get( actionurl, function( html ) {
                var message = $( html ).find('[class^="woocommerce-"]').clone(),
                    tableHTML = $( html ).find('.woocommerce table.my_account_authorize_payment_methods').html();

                // remove loading
                table.stop( true ).css( 'opacity', '1' ).unblock();

                // add message
                message.insertBefore( table );

                // replace html of table
                table.html( tableHTML );

            }, 'html' );
        });
});