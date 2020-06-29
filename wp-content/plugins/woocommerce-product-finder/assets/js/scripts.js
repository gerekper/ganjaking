jQuery( document ).ready( function() {

	jQuery( '#wc_product_finder #add_row' ).css('display', 'block').click( function(e) {
		e.preventDefault();
		jQuery( '#add_row .loader' ).show();
		jQuery( '#add_row .plus' ).hide();
		var last_row = parseInt( jQuery( '#last_row' ).text() );
		var next_row = last_row + 1;
		var show_cat = jQuery( '#show_cat' ).val();
		var search_attributes = jQuery( '#search_attributes' ).val();

		jQuery.ajax({
            type: "GET",
            url: wc_product_finder_data.ajax_url,
            data: 'action=wc_product_finder_add_row&row=' + next_row + '&show_cat=' + show_cat + '&search_attributes=' + search_attributes,
            dataType: "html",
            success: function( response ) {
                if( response ) {
                    jQuery( '#last_row' ).before( response );
                    jQuery( '#last_row' ).text( next_row );
                    jQuery( '#search_row_' + next_row ).slideDown( 'fast' , function() {
                    	jQuery( '#add_row .plus' ).show();
						jQuery( '#add_row .loader' ).hide();
                    });
                    
                } else {

                }
            }
        });

	});

	var load_taxonomy_values = function( tax , row ) {

        if( tax.length > 0 ) {

        	if( tax == 'none' ) {

        		jQuery( '#val_' + row ).attr( 'disabled' , 'disabled' );
        		jQuery( '#val_' + row + ' option' ).remove();
        		jQuery( '#val_' + row ).append( '<option value="none" selected="selected">Select criteria</option>' );

        	} else {

        		jQuery( '#val_' + row ).attr( 'disabled' , 'disabled' );
	            jQuery( '#val_' + row + ' option' ).remove();
	            jQuery( '#val_' + row ).append( '<option value="none" selected="selected">Loading options...</option>' );
	            jQuery.ajax({
	                type: "GET",
	                url: wc_product_finder_data.ajax_url,
	                data: 'action=wc_product_finder_get_tax_options&row=' + row + '&tax=' + tax,
	                dataType: "html",
	                success: function( response ){
	                    if( response ) {
	                        jQuery( '#val_' + row + ' option' ).remove();
	                        jQuery( '#val_' + row ).append( response );
	                        jQuery( '#val_' + row ).removeAttr( 'disabled' );
	                    } else {
	                        jQuery( '#val_' + row + ' option' ).remove();
	                        jQuery( '#val_' + row ).append( '<option value="none" selected="selected">No options found</option>' );
	                    }
	                }
	            });

			}
        }
    };

    var remove_search_row = function( row ) {
    	jQuery( '#search_row_' + row ).slideUp( 'fast' , function() {
    		jQuery( '#search_row_' + row ).remove();
    	});
    };

	jQuery( '#wc_product_finder' ).on( 'change' , '.taxonomy' , function() {
		var tax = this.value;
        var row = this.id;
        row = row.replace( 'tax_' , '' );
		load_taxonomy_values( tax , row );
	});

	jQuery( '#wc_product_finder' ).on( 'click' , '.remove_row' , function(e) {
		e.preventDefault();
        var row = this.id;
        row = row.replace( 'remove_' , '' );
		remove_search_row( row );
	});

});