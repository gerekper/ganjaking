jQuery( function ( $ ) {
    $( '.yith-wcmbs-with-menu' ).each( function () {
        var menu    = $( this ).find( '.yith-wcmbs-menu' ),
            objects = [];

        menu.children( 'li' ).each( function ( idx, obj ) {
            if ( idx == 0 ) {
                $( this ).addClass( 'yith-wcmbs-menu-active' );
            }
            objects.push( $( $( this ).data( 'show' ) ) );
        } );

        var hide_all = function () {
            menu.children( 'li.yith-wcmbs-menu-active' ).removeClass( 'yith-wcmbs-menu-active' );
            $.each( objects, function () {
                $( this ).hide();
            } );
        };

        menu.children( 'li' ).on( 'click', function ( e ) {
            hide_all();

            $( this ).addClass( 'yith-wcmbs-menu-active' );
            $( $( this ).data( 'show' ) ).show();

        } );

        menu.children( 'li.yith-wcmbs-menu-active' ).trigger( 'click' );

    } );
} );
