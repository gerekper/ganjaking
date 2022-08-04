jQuery(
	function ($) {
        addOpenInANewTab = function () {
            if ( 'open_tab' === ywpi_frontend.open_file_option ) {
                let orders_table_row = $( '.woocommerce-orders-table tr.woocommerce-orders-table__row td.woocommerce-orders-table__cell-order-actions' );

                $.each(
                    orders_table_row,
                    function( key, element )
                    {
                        let selected_button = $( element ).find( $( "a[class*='print-']" ) );
                        selected_button.attr( 'target', '_blank' );
                    }
                )
            }
        };

        addOpenInANewTab();
    }
);