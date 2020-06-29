jQuery( function ( $ ) {
    /**=============================
     *  Toggle
     * =============================
     */
    $( '.yith-wcbep-toggle' ).each( function () {
        var $toggleAnchor = $( this ),
            $target       = $( $toggleAnchor.data( 'target' ) ),
            animate       = $toggleAnchor.data( 'animate' ) || false;

        $toggleAnchor.on( 'click', function () {
            if ( $( this ).is( '.closed' ) ) {
                $( this ).removeClass( 'closed' );
                if ( animate ) {
                    $target.slideUp();
                } else {
                    $target.show();
                }
            } else {
                $( this ).addClass( 'closed' );
                if ( animate ) {
                    $target.slideDown();
                } else {
                    $target.hide();
                }
            }
        } );
    } );


} );