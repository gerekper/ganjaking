jQuery( function ( $ ) {
    /* Protected Links Table */
    var row_number = $( '.yith-wcmbs-admin-settings-table-row:not(.yith-wcmbs-admin-settings-table-default-row)' ).length - 1;
    $( document ).on( 'click', '.yith-wcmbs-admin-settings-table-add-row', function ( event ) {
        var target      = $( event.target ),
            container   = target.closest( '.yith-wcmbs-admin-settings-table-wrapper' ),
            table       = container.find( '.yith-wcmbs-admin-settings-table' ),
            default_row = table.find( '.yith-wcmbs-admin-settings-table-default-row' ).first(),
            new_row     = default_row.clone().removeClass( 'yith-wcmbs-admin-settings-table-default-row' );

        row_number++;
        new_row.html(new_row.html().replace( /YITH_WCMBS_ID/g, row_number ));
        new_row.find( '.select2' ).remove();
        new_row.find( '.yith-wcmbs-select2' ).select2();
        table.append( new_row );
    } );

    $( document ).on( 'click', '.yith-wcmbs-delete', function ( event ) {
        var target = $( event.target ),
            row    = target.closest( '.yith-wcmbs-admin-settings-table-row' );
        row.remove();
    } );
} );