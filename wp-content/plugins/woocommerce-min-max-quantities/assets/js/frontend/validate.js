( function( $, document, window ) {

	$( function() {

		var ValidationController = {

			/**
			 * Initializes Validation Controller.
			 * When a new variation is selected, validate and conditionally adjust its quantity.
			 *
			 * @param {jQuery object} $cart_form
			 */
			init: function ( $cart_form ) {
				if ( ! $cart_form.hasClass( 'variations_form' ) ) {
					return;
				}

				$cart_form.on( 'show_variation', function( event, variation ) {
					var $qty_input     = $cart_form.find( 'input.qty' ),
						$quantity_wrap = $cart_form.find( '.quantity' ),
						step           = 'undefined' !== typeof variation.step && ! variation.step.length ? parseInt( variation.step, 10 ) : 1,
						min_qty        = parseInt( variation.min_qty, 10 ),
						max_qty        = parseInt( variation.max_qty, 10 );

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

					// If the parent's Minimum Quantity is equal to the Maximum Quantity, then the qty selector is hidden for all variations.
					// To fix this, the following code shows the qty input if users should be able to configure the variation's qty.
					if ( $quantity_wrap.hasClass( 'hidden' ) && min_qty + step <= max_qty ) {
						$qty_input.prop( 'type', 'number' );
						$quantity_wrap.removeClass( 'hidden' );
					} else if ( ! $quantity_wrap.hasClass( 'hidden' ) && min_qty + step > max_qty ) {
						$qty_input.prop( 'type', 'hidden' );
						$quantity_wrap.addClass( 'hidden' );
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
