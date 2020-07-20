var init_fue_product_search,
    init_fue_customer_search,
    init_fue_select,
    init_fue_coupon_search;

(function( $ ) {
    init_fue_product_search = function() {

		jQuery( ':input.wc-product-search' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
				placeholder: jQuery( this ).data( 'placeholder' ),
				width:       '100%',
				minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url:         ajaxurl,
					dataType:    'json',
					quietMillis: 250,
					data: function( params ) {
						return {
							term:     params.term,
							action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
							security: FUE.nonce,
						};
					},
					processResults: function( data ) {
						var terms = [];
						if ( data ) {
							jQuery.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							} );
						}
						return { results: terms };
					},
					cache: true
				},
			};

			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
		} );
    }

    init_fue_customer_search = function() {
		jQuery( ':input.fue-customer-search' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
				placeholder: jQuery( this ).data( 'placeholder' ),
				width:       '100%',
				minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url:         ajaxurl,
					dataType:    'json',
					quietMillis: 250,
					data: function( params ) {
						return {
							term:    params.term,
							action:  jQuery( this ).data( 'action' ) || 'fue_json_search_customers',
							nonce:   jQuery( this ).data( 'nonce' ) || FUE.nonce,
                            exclude: jQuery( this ).data( 'exclude' ),
						};
					},
					processResults: function( data ) {
						var terms = [];
						if ( data ) {
							jQuery.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							} );
						}
						return { results: terms };
					},
					cache: true
				},
			};

			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
		} );

		jQuery( ':input.email-search-select' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
				placeholder: jQuery( this ).data( 'placeholder' ),
				width:       '100%',
				minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url:         ajaxurl,
					dataType:    'json',
					quietMillis: 250,
					data: function( params ) {
						return {
							term:    params.term,
							action:  jQuery( this ).data( 'action' ) || 'fue_search_for_email',
							nonce:   jQuery( this ).data( 'nonce' ) || FUE.nonce,
						};
					},
					processResults: function( data ) {
						var terms = [];
						if ( data ) {
							jQuery.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							} );
						}
						return { results: terms };
					},
					cache: true
				},
			};

			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
		} );
    }

    init_fue_select = function() {
        $(":input.select2").filter(":not(.enhanced)").each( function() {
            $(this).select2();
        } );
    }

    init_fue_coupon_search = function() {

		jQuery( ':input.wc-coupon-search' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
				placeholder: jQuery( this ).data( 'placeholder' ),
				width:       '100%',
				minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url:         ajaxurl,
					dataType:    'json',
					quietMillis: 250,
					data: function( params ) {
						return {
							term:     params.term,
							action:   jQuery( this ).data( 'action' ) || 'fue_wc_json_search_coupons',
							security: jQuery( this ).data( 'nonce' ) || FUE.nonce,
						};
					},
					processResults: function( data ) {
						var terms = [];
						if ( data ) {
							jQuery.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							} );
						}
						return { results: terms };
					},
					cache: true
				},
			};

			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
		} );
    }

    init_fue_product_search();
    init_fue_customer_search();
    init_fue_select();
    init_fue_coupon_search();
}(jQuery));
