jQuery( function ( $ ) {
    var delete_input = $( '.yith-wcbm-delete-input' );

    // Datepicker
    $( '.yith-wcbm-datepicker' ).datepicker( {
        dateFormat: 'yy-mm-dd'
    } );

    $( document ).on( 'click', '.yith-wcbm-delete-input', function ( e ) {
        var button = $( e.target );

        button.prev( 'input' ).val( '' );
    } );


} );