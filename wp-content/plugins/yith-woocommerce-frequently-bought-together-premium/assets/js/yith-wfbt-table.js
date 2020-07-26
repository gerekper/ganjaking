/**
 * yith-wfbt-table.js This js handle admin table actions
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.0.0
 */

( function( $, document ){

    var deleteSpan = $('<span/>', { 'class': 'delete-linked' }).html( yith_wfbt.deleteLabel );

    $( '.linked-product' ).hover(
        function( ev ) {
            $(this).append( deleteSpan );
        },
        function( ev ) {
            $(this).find( '.delete-linked' ).remove();
        }
    );

    $( document ).on( 'click', '.delete-linked', function( ev ) {
        ev.stopPropagation();

        var wrap        = $( this ).closest( '.linked-product' ),
            table       = wrap.closest( 'table' ),
            product_id  = wrap.data( 'product_id' ),
            linked_id   = wrap.data( 'linked_id' );

        if( product_id && linked_id ) {

            $.ajax({
                type: "POST",
                url: yith_wfbt.ajaxurl,
                dataType: 'json',
                data: {
                    action: yith_wfbt.action,
                    security: yith_wfbt.security,
                    product_id: product_id,
                    linked_id: linked_id,
                },
                beforeSend: function() {
                    table.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }});
                },
                complete: function( result ) {
                    if( result.success ) {
                        wrap.remove();
                    }

                    table.unblock();
                }
            });
        }
    });


})( jQuery, document );