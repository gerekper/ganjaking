/* global admin_i18n */
( function ( $ ) {

    var disable = function () {
        var restockCheckbox = $( '#restock_refunded_items' ),
            restockRow      = restockCheckbox.closest( 'tr' );
        restockCheckbox.prop( 'checked', false );
        restockRow.addClass( 'yith-pos-disabled' );

        restockRow.after( $( '<tr><td colspan="2"><div class="yith-pos-admin-notice yith-pos-admin-notice--warning">' + admin_i18n.restock_not_allowed + '</div></td>' ) );
    };

    disable();

} )( jQuery );