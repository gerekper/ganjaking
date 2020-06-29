(function ( $ ) {

    // we create a copy of the WP inline edit post function
    var $wp_inline_edit = inlineEditPost.edit;

    // and then we overwrite the function with our own code
    inlineEditPost.edit = function ( id ) {

        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        $wp_inline_edit.apply( this, arguments );

        // now we take care of our business

        // get the post ID
        var $post_id = 0;
        if ( typeof( id ) == 'object' ) {
            $post_id = parseInt( this.getId( id ) );
        }

        if ( $post_id > 0 ) {
            // define the edit row
            var $edit_row = $( '#edit-' + $post_id );
            var $post_row = $( '#post-' + $post_id );

            // get the data
            var $charts_td = $( '.column-yith_wcpsc_product_size_charts', $post_row );
            var charts_ids = [];

            try {
                charts_ids = JSON.parse( $( 'input.yith-wcpsc-hidden', $charts_td ).val() );
            } catch ( e ) {
                console.log( e );
            }

            // populate the data
            for ( var i in charts_ids ) {
                var selector = 'input#in-chart-' + charts_ids[ i ];
                $( selector, $edit_row ).prop( 'checked', true );
            }
            //$( ':input[name="inprint"]', $edit_row ).prop( 'checked', $inprint );
        }
    };

    $( '#bulk_edit' ).live( 'click', function() {
        // define the bulk edit row
        var $bulk_row = $( '#bulk-edit' );

        // get the selected post ids that are being edited
        var $post_ids = [];
        $bulk_row.find( '#bulk-titles' ).children().each( function() {
            $post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
        });

        // get the data
        var $charts_checkboxes = $( '.charts-checklist', $bulk_row ).find('input:checked' );
        var charts_ids = [];
        $charts_checkboxes.each(function(){
            charts_ids.push($(this ).val());
        });

        // save the data
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            async: false,
            cache: false,
            data: {
                action: 'save_bulk_edit_product',
                post_ids: $post_ids,
                yith_wcpsc_product_charts: charts_ids
            }
        });
    });

})( jQuery );