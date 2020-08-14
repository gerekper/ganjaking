jQuery( document).ready(function( $ ) {

    var redeem_funds = $('form.yith_redeem_funds_form');

    redeem_funds.on('submit', function () {

        if( $(this).is('.processing')){
            return false;
        }
        $(this).addClass('processing');
        $(this).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.ajax({
            type: 'POST',
            url: ywf_redeem_funds_args.ajax_url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function (result) {
                try {

                    if (  result.result === 'success' ) {

                        window.location = decodeURI( ywf_redeem_funds_args.redirect_url );

                    } else if ( 'failure' === result.result ) {
                        throw 'Result failure';
                    } else {
                        throw 'Invalid response';
                    }
                } catch( err ) {
                    // Reload page
                    submit_error( result.message);
                }
            }
        });
        return false;
    });

   var submit_error = function( error_message ) {
        $( '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message' ).remove();
           redeem_funds.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + error_message + '</div>' ); // eslint-disable-line max-len
           redeem_funds.removeClass( 'processing' ).unblock();
           scroll_to_notices();

    },
    scroll_to_notices = function() {
        var scrollElement           = $( '.woocommerce-NoticeGroup-updateOrderReview, .woocommerce-NoticeGroup-checkout' );

        if ( ! scrollElement.length ) {
            scrollElement = $( '.form.yith_redeem_funds_form' );
        }
        $.scroll_to_notices( scrollElement );
    }
});