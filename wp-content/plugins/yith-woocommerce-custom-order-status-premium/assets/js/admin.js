jQuery( function ( $ ) {
    // Run tipTip
    $( document ).on( 'yith-wccos-init-tiptip', function () {
        // Remove any lingering tooltips
        $( '#tiptip_holder' ).removeAttr( 'style' );
        $( '#tiptip_arrow' ).removeAttr( 'style' );
        $( '.tips' ).tipTip( {
                                 'attribute': 'data-tip',
                                 'fadeIn'   : 50,
                                 'fadeOut'  : 50,
                                 'delay'    : 200
                             } );
    } ).trigger( 'yith-wccos-init-tiptip' );
} );