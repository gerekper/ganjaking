jQuery( function ( $ ) {

	if ( ywmmq.variations ) {

		$( document ).on( 'found_variation', function () {

			var product_id   = parseInt( $( '.single_variation_wrap .product_id, .single_variation_wrap input[name="product_id"]' ).val() ),
				variation_id = parseInt( $( '.single_variation_wrap .variation_id, .single_variation_wrap input[name="variation_id"]' ).val() );

			if ( ! isNaN( product_id ) && ! isNaN( variation_id ) ) {

				get_variation_rules( product_id, variation_id );

			}

		} );

	}

	function get_variation_rules( product_id, variation_id ) {

		var container       = $( '.ywmmq-rules-wrapper' ),
			variations_form = $( '.single_variation_wrap' ),
			raq_button      = $( '.add-request-quote-button' );

		if ( variations_form.is( '.processing' ) ) {
			return false;
		}

		variations_form.addClass( 'processing' );
		raq_button.addClass( 'disabled' );
		variations_form.block( {
			message   : null,
			overlayCSS: {
				background: '#fff',
				opacity   : 0.6
			}
		} );

		$.ajax( {
			type    : 'POST',
			url     : ywmmq.ajax_url,
			data    : {
				action      : 'ywmmq_get_rules',
				product_id  : product_id,
				variation_id: variation_id
			},
			success : function ( response ) {

				if ( response.status === 'success' ) {

					container.html( response.rules );

					if ( parseInt( response.limits.max ) !== 0 ) {

						$( '.single_variation_wrap .quantity input[name="quantity"]' ).attr( 'max', response.limits.max );

					} else {

						$( '.single_variation_wrap .quantity input[name="quantity"]' ).removeAttr( 'max' );

					}

					if ( parseInt( response.limits.min ) !== 0 ) {

						$( '.single_variation_wrap .quantity input[name="quantity"]' ).attr( 'min', response.limits.min ).val( response.limits.min );

					} else {

						$( '.single_variation_wrap .quantity input[name="quantity"]' ).attr( 'min', 1 ).val( 1 );

					}

					if ( parseInt( response.limits.step ) !== 0 ) {

						$( '.single_variation_wrap .quantity input[name="quantity"]' ).attr( 'step', response.limits.step );

					} else {

						$( '.single_variation_wrap .quantity input[name="quantity"]' ).attr( 'step', 1 ).val( 1 );

					}


					$( document ).trigger( 'ywmmq_additional_operations', [ response.limits.min ] );


				} else {

					container.html();

				}

				variations_form.removeClass( 'processing' ).unblock();
				raq_button.removeClass( 'disabled' );

			},
			dataType: 'json'
		} );

		return false;

	}

	$( document ).on( 'yith_wcpb_found_variation_after', function ( event, form, variation ) {

		if ( form.is( '.processing' ) ) {
			return false;
		}

		form.addClass( 'processing' );
		form.block( {
			message   : null,
			overlayCSS: {
				background: '#fff',
				opacity   : 0.6
			}
		} );

		$.ajax( {
			type    : 'POST',
			url     : ywmmq.ajax_url,
			data    : {
				action      : 'ywmmq_get_rules',
				product_id  : form.data( 'product_id' ),
				variation_id: form.find( '.variation_id' ).val()
			},
			success : function ( response ) {

				if ( response.status === 'success' ) {

					if ( parseInt( response.limits.max ) !== 0 ) {

						form.find( '.yith-wcpb-bundled-quantity' ).attr( 'max', response.limits.max );

					} else {

						form.find( '.yith-wcpb-bundled-quantity' ).removeAttr( 'max' );

					}

					if ( parseInt( response.limits.min ) !== 0 ) {

						form.find( '.yith-wcpb-bundled-quantity' ).attr( 'min', response.limits.min ).val( response.limits.min );

					} else {

						form.find( '.yith-wcpb-bundled-quantity' ).attr( 'min', 1 ).val( 1 );

					}

					if ( parseInt( response.limits.step ) !== 0 ) {

						form.find( '.yith-wcpb-bundled-quantity' ).attr( 'step', response.limits.step );

					} else {

						form.find( '.yith-wcpb-bundled-quantity' ).attr( 'step', 1 ).val( 1 );

					}


				}

				form.removeClass( 'processing' ).unblock();

			},
			dataType: 'json'
		} );

	} )

} );