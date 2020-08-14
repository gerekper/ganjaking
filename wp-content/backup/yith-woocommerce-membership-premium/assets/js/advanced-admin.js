jQuery( function ( $ ) {
    var edit_btn             = $( '.yith-wcmbs-advanced-edit' ),
        undo_btn             = $( '.yith-wcmbs-advanced-undo' ),
        set_unlimited_button = $( '.yith-wcmbs-advanced-set-unlimited' ),
        show_if_editable     = $( '.yith-wcmbs-advanced-show-if-editable' );

    show_if_editable.hide();

    edit_btn.on( 'click', function ( e ) {
        var parent           = $( this ).closest( 'p.yith-wcmbs-advanced-admin-section' ),
            field            = parent.children( '.yith-wcmbs-advanced-admin-section-field' ).first(),
            show_if_editable = parent.children( '.yith-wcmbs-advanced-show-if-editable' ),
            hide_if_editable = parent.children( '.yith-wcmbs-advanced-hide-if-editable' );

        field.attr( 'disabled', false );
        show_if_editable.show();
        hide_if_editable.hide();
    } );

    undo_btn.on( 'click', function ( e ) {
        var parent           = $( this ).closest( 'p.yith-wcmbs-advanced-admin-section' ),
            field            = parent.children( '.yith-wcmbs-advanced-admin-section-field' ).first(),
            value            = field.data( 'value' ),
            show_if_editable = parent.children( '.yith-wcmbs-advanced-show-if-editable' ),
            hide_if_editable = parent.children( '.yith-wcmbs-advanced-hide-if-editable' );

        field.val( value );
        field.attr( 'disabled', true );
        show_if_editable.hide();
        hide_if_editable.show();
    } );

    set_unlimited_button.on( 'click', function ( e ) {
        var parent = $( this ).closest( 'p.yith-wcmbs-advanced-admin-section' ),
            field  = parent.children( '.yith-wcmbs-advanced-admin-section-field' ).first(),
            value  = 'unlimited';

        if ( !field.prop( 'disabled' ) ) {
            field.val( value );
        }
    } );
} );
