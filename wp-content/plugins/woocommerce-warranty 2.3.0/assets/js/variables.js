jQuery( document ).ready( function( $ ) {
	$( 'input[name=variation_id]' ).change( function() {
		var variations = $( '.variations_form' ).data( 'product_variations' );
		var variation_id = $( 'input[name=variation_id]' ).val();

		for ( var x = 0; x < variations.length; x ++ ) {

			if ( variations[x].variation_id == variation_id ) {
				var variation = variations[x];

				if ( variation._warranty ) {
					if ( 'included_warranty' === variation._warranty.type ) {
						if ( 'limited' === variation._warranty.length ) {
							var value = variation._warranty.value;
							var duration = WC_Warranty.durations[variation._warranty.duration];

							$( '.warranty_info' )
								.html( '<b>' + variation._warranty_label + ':</b> ' + value + ' ' + duration );
						} else {
							$( '.warranty_info' )
								.html( '<b>' + variation._warranty_label + ':</b> ' + WC_Warranty.lifetime );
						}
					} else if ( 'addon_warranty' === variation._warranty.type ) {
						var addons = variation._warranty.addons;

						if ( addons.length ) {
							var src = '<b>' + variation._warranty_label + '</b> <select name="warranty">';

							if ( variation._warranty.no_warranty_option && 'yes' === variation._warranty.no_warranty_option ) {
								src += '<option value="-1">' + WC_Warranty.no_warranty + '</option>';
							}

							for ( var i = 0; i < addons.length; i ++ ) {
								var amount = addons[i].amount;
								var value = addons[i].value;
								var duration = WC_Warranty.durations[addons[i].duration];

								if ( 0 === value && 0 === amount ) {
									src += '<option value="-1">' + WC_Warranty.no_warranty + '</option>';
								} else {
									if ( 0 === amount ) {
										amount = WC_Warranty.free;
									} else {
										amount = WC_Warranty.currency_symbol + '' + amount;
									}
									src += '<option value="' + i + '">' + value + ' ' + duration + ' &mdash; ' + amount + '</option>';
								}
							}

							src += '</select>';
							$( '.warranty_info' ).html( src );
						}
					}

					if ( 'no_warranty' === variation._warranty.type ) {
						$( '.warranty_info' ).hide();
					} else {
						$( '.warranty_info' ).show();
					}
				}
			}

		}
	} ).change();
} );
