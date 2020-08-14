jQuery( function ( $ ) {
    /* ======================================
     *             Bulk User Edit
     * ===================================== */
    $( document ).on( 'change', '.filter_by_membership_plan', function ( e ) {
        var target = $( e.target );
        $( '.filter_by_membership_plan' + name ).val( target.val() );
    } );

    $( document ).on( 'change', '.membership_editing_bulk_action', function ( e ) {
        var target = $( e.target );
        $( '.membership_editing_bulk_action' + name ).val( target.val() );
    } );

    $( document ).on( 'change', '.membership_editing_action_plan', function ( e ) {
        var target = $( e.target );
        $( '.membership_editing_action_plan' + name ).val( target.val() );
    } );

} );
