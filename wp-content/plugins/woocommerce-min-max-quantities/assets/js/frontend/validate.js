( function( $, document, window ) {

	$( function() {

		var ValidationController = {

			/**
			 * Initializes Validation Controller.
			 * When a new variation is selected, validate and conditionally adjust its quantity.
			 * When the Min Qty = Max Qty = 1, hide the quantity selector.
			 *
			 * @param {jQuery object} $cart_form
			 */
			init: function ( $cart_form ) {

				// Simple Products.
				var $quantity_wrap = $cart_form.find( '.quantity' );

				$quantity_wrap.each( function() {
					var $qty_input = $( this ).find( 'input.qty' );

					if ( $qty_input.length ) {
						var min_qty    = parseInt( $qty_input.prop( 'min' ), 10 ),
							max_qty    = '' !== $qty_input.prop( 'max' ) ? parseInt( $qty_input.prop( 'max' ), 10 ) : Infinity;

						// Hide variation quantity, only if Min Qty = Max Qty = 1, and display it otherwise.
						if ( 1 === min_qty && min_qty === max_qty ) {
							$qty_input.prop( 'type', 'hidden' );
							$( this ).addClass( 'hidden' );
						} else if ( $( this ).hasClass( 'hidden' ) ) {
							$qty_input.prop( 'type', 'number' );
							$( this ).removeClass( 'hidden' );
						}
					}
				} );

				// Variations.
				if ( ! $cart_form.hasClass( 'variations_form' ) ) {
					return;
				}

				$cart_form.on( 'show_variation', function( event, variation ) {
					var $qty_input     = $cart_form.find( 'input.qty' ),
						$quantity_wrap = $cart_form.find( '.quantity' ),
						step           = 'undefined' !== typeof variation.step && ! variation.step.length ? parseInt( variation.step, 10 ) : 1,
						min_qty        = parseInt( variation.min_qty, 10 ),
						max_qty        = '' !== variation.max_qty ? parseInt( variation.max_qty, 10 ) : Infinity;

					if ( step > 1 ) {

						// Update Minimum Quantity if it is not a multiple of the step.
						if ( min_qty > step ) {
							remain  = min_qty / step;
							min_qty = remain ? step * Math.ceil( remain ) : min_qty;
						} else if ( min_qty > 0 ) {
							min_qty = Math.max( min_qty, step );
						}

						// Update Maximum Quantity if it is not a multiple of the step.
						if ( max_qty > step ) {
							remain  = max_qty / step;
							max_qty = remain ? step * Math.floor( remain ) : max_qty;
						} else if ( max_qty < step ) {
							max_qty = step;
						}
					}

					$qty_input.prop( 'step', step ).val( min_qty );
					$qty_input.prop( 'min', min_qty );
					$qty_input.prop( 'max', max_qty );

					// Hide variation quantity, only if Min Qty = Max Qty = 1, and display it otherwise.
					if ( 1 === min_qty && min_qty === max_qty ) {
						$qty_input.prop( 'type', 'hidden' );
						$quantity_wrap.addClass( 'hidden' );
					} else if ( $quantity_wrap.hasClass( 'hidden' ) ) {
						$qty_input.prop( 'type', 'number' );
						$quantity_wrap.removeClass( 'hidden' );
					}
				} );

			},
			/**
			 * Listens to any scripts that trigger the Min/Max Quantities validation manually.
			 */
			watch: function() {
				$( 'body' ).on( 'wc-mmq-init-validation', function( event, $cart_form ) {
					ValidationController.init( $cart_form );
				});
			}
		}

		// Initialize.
		var $cart_forms = $( 'body' ).find( '.cart:not( .cart_group )' );

		if ( ! $cart_forms.length ) {
			return
		}

		$cart_forms.each( function () {
			ValidationController.init( $(this) );
		});

		ValidationController.watch();

	} );

} )( jQuery, document, window );
