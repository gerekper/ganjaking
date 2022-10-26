jQuery(
    function( $ ) {
    $( '.enable_variation' ).addClass( 'show_if_variable-mix-and-match' );
    $( 'select#product-type' ).trigger( 'change' );
    }
);

jQuery(
    function( $ ) {
    $( '.variations_tab' ).addClass( 'show_if_variable-mix-and-match' );

    $( document.body ).on(
        'woocommerce_added_attribute',
        function() {
        $( '.enable_variation' ).addClass( 'show_if_variable-mix-and-match' );

        if ( 'variable-mix-and-match' === $( 'select#product-type' ).val() ) {
            $( '.enable_variation' ).show();
        }
        }
    );
    } 
);
