jQuery( function ( $ ) {
    "use strict";

    $( document ).on( 'click', '.yith-wcbk-confirm-button', function ( event ) {
        var confirm_text  = $( this ).data( 'confirm-text' ),
            confirm_class = $( this ).data( 'confirm-class' ),
            undo_html     = '<span class="yith-wcbk-confirm-button-done dashicons dashicons-undo"></span>';

        if ( !$( this ).is( '.' + confirm_class ) ) {
            event.preventDefault();
        }

        $( this ).data( 'confirm-old-text', $( this ).html() );
        $( this ).html( confirm_text + undo_html );
        $( this ).addClass( confirm_class );

    } );

    $( document ).on( 'click', '.yith-wcbk-confirm-button-done', function ( event ) {
        event.preventDefault();
        event.stopPropagation();
        var button        = $( this ).closest( '.yith-wcbk-confirm-button' ),
            confirm_class = button.data( 'confirm-class' ),
            text          = button.data( 'confirm-old-text' );

        button.data( 'confirm-old-text', '' );
        button.html( text );
        button.removeClass( confirm_class );
        
    } );
} );
