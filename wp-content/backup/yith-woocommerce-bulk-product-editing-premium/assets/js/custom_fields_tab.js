jQuery( function ( $ ) {
    var wrapper  = $( '#yith-wcbep-custom-fields-tab-wrapper' ),
        save_btn = $( '#yith-wcbep-custom-fields-tab-actions-save' ),
        ajax_request;

    wrapper.on( 'click', '.yith-wcbep-custom-field-add', function ( e ) {
            var current_target = $( e.target ),
                parent         = current_target.closest( '.yith-wcbep-custom-field-wrap' ),
                parent_clone   = parent.clone();

            parent_clone.find( 'input' ).val( '' );
            parent.after( parent_clone );
        } )

        .on( 'click', '.yith-wcbep-custom-field-delete', function ( e ) {
            var number_of_custom_fields = wrapper.find( '.yith-wcbep-custom-field-wrap' ).length,
                current_target          = $( e.target ),
                parent                  = current_target.closest( '.yith-wcbep-custom-field-wrap' );

            if ( number_of_custom_fields > 1 ) {
                parent.remove();
            }else{
                parent.find( 'input' ).val( '' );
            }
        } );
} );