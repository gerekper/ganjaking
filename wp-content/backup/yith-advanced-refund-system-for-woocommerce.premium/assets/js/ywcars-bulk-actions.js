jQuery( function ( $ ) {
    $( document ).ready( function ( $ ) {
        var bulk_action_1 = $( '#bulk-action-selector-top' ),
            bulk_action_2 = $( '#bulk-action-selector-bottom' ),
            set_approved = ywcars_bk_data.set_approved,
            set_rejected = ywcars_bk_data.set_rejected;
        bulk_action_1.add( bulk_action_2 ).append( set_approved, set_rejected );
    } );
} );