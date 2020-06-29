( function ( $ ) {
    $( document ).ready(function ( $ ) {
        $( document ).on( 'yith_infs_added_elem', function () {// YITH Infinite Scrolling Compatibility
            $( 'div.pre_order_loop' ).each( function () {
                var unix_time = parseInt( $( this ).data( 'time' ) );
                var date = new Date(0);
                date.setUTCSeconds( unix_time );
                var time = date.toLocaleTimeString();
                time = time.slice(0, -3);
                $( this ).find( '.availability_date' ).text( date.toLocaleDateString() );
                $( this ).find( '.availability_time' ).text( time );
            });
            $( 'div.pre_order_on_cart' ).each( function () {
                var unix_time = parseInt( $( this ).data( 'time' ) );
                var date = new Date(0);
                date.setUTCSeconds( unix_time );
                var time = date.toLocaleTimeString();
                time = time.slice(0, -3);
                $( this ).find( '.availability_date' ).text( date.toLocaleDateString() );
                $( this ).find( '.availability_time' ).text( time );
            });
        } );
        $( document ).trigger( 'yith_infs_added_elem' );
    } );
} )
( jQuery );