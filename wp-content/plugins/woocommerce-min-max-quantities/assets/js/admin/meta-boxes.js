/* global woocommerce_admin_meta_boxes */
( function( $, document, window ) {

	$( function() {

		var allow_combination_field = $( '.allow_combination_field' ),
			max_qty_prev            = $( 'input#maximum_allowed_quantity' ).val();

		if ( 'variable' === $( '#product-type' ).val() ) {
			allow_combination_field.show();
		} else {
			allow_combination_field.hide();
		}

		$( document.body ).on( 'woocommerce-product-type-change', function( evt, value, variation ) {
			if ( 'variable' === value ) {
				allow_combination_field.show();
			} else {
				allow_combination_field.hide();
			}
		} );

		$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function() {
			var min_max_rules_options = $( '.checkbox.min_max_rules' ).parents( '.woocommerce_variable_attributes' ).find( '.min_max_rules_options' ),
				$min_quantities       = $( 'input.variation_minimum_allowed_quantity' ),
				$max_quantities       = $( 'input.variation_maximum_allowed_quantity' );

			if ( $( '.checkbox.min_max_rules' ).is( ':checked' ) ) {
				min_max_rules_options.show();
			} else {
				min_max_rules_options.hide();
			}

			// If the Minimum Quantity changes, validate its new value.
			$min_quantities.each( function() {

				$( this ).on( 'keyup change focusout focus click', function() {

					var $input     = $( this ),
						qty        = $input.val(),
						$container = $input.closest( '.min_max_rules_options' ),
						$max       = $container.find( 'input.variation_maximum_allowed_quantity' ),
						$step      = $container.find( 'input.variation_group_of_quantity' );

					is_valid = validate_min_qty( $input, qty, $step.val(), $max );
				} );
			} );

			// If the Maximum quantity changes, validate its new value.
			$max_quantities.each( function() {

				$( this ).data( "prev-value", $( this ).val() );

				$( this ).on( 'keyup change focusout focus click', function() {

					var $input       = $( this ),
						qty          = $input.val(),
						$container   = $input.closest( '.min_max_rules_options' ),
						$min         = $container.find( 'input.variation_minimum_allowed_quantity' ),
						$step        = $container.find( 'input.variation_group_of_quantity' ),
						max_qty_prev = $input.data( "prev-value" );

					is_valid = validate_max_qty( $input, qty, $step.val(), $min.val(), max_qty_prev );

					if ( is_valid ) {
						$input.data( "prev-value", $input.val() );
					}
				} );
			} );
		} );

		$( '.woocommerce_variations' ).on( 'change', '.checkbox.min_max_rules', function() {
			var min_max_rules_options = $( this ).parents( '.woocommerce_variable_attributes' ).find( '.min_max_rules_options' );

			if ( $( this ).is( ':checked' ) ) {
				min_max_rules_options.show();
			} else {
				min_max_rules_options.hide();
			}
		} );

		// Add error tip when an invalid quantity is used.
		var add_error_tip = function( target, error ) {

			var offset        = target.position(),
				$targetParent = target.parent();

			$targetParent.find( '.wc_error_tip' ).remove();

			target.after( '<div class=\"wc_error_tip\">' + error + '</div>' );
			$targetParent.find( '.wc_error_tip' )
				.css( 'left', offset.left + target.width() - ( target.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
				.css( 'top', offset.top + target.height() + 4 );

		};

		// Remove error tip when a valid quantity is used.
		var remove_error_tip = function( target ) {

			target.parent().find( '.wc_error_tip' ).fadeOut( '100', function() {
				$( this ).remove();
			} );
		};

		// If the Minimum Quantity changes, validate its new value.
		$( 'input#minimum_allowed_quantity' ).on( 'keyup change focusout focus click', function() {
			var $input     = $( this ),
				qty        = $input.val(),
				$container = $input.closest( '.options_group' ),
				$max       = $container.find( 'input#maximum_allowed_quantity' ),
				$step      = $container.find( 'input#group_of_quantity' );

			is_valid = validate_min_qty( $input, qty, $step.val(), $max );
		} );

		// If the Maximum quantity changes, validate its new value.
		$( 'input#maximum_allowed_quantity' ).on( 'keyup change focusout focus click', function() {
			var $input     = $( this ),
				qty        = $input.val(),
				$container = $input.closest( '.options_group' ),
				$min       = $container.find( 'input#minimum_allowed_quantity' ),
				$step      = $container.find( 'input#group_of_quantity' );

			is_valid = validate_max_qty( $input, qty, $step.val(), $min.val() );
		} );

		// Validate that the Minimum Quantity is a multiple of the Group Of quantity.
		// If it isn't, then display an error an update its value.
		// Moreover, if the Minimum Quantity is increased to a value higher than the Maximum Quantity, increase the latter as well.
		var validate_min_qty = function( input, qty, step, max ) {

			var is_valid = true;

			if ( qty !== '' ) {

				qty  = parseInt( qty, 10 );
				step = parseInt( step, 10 );

				if ( step > 0 && qty > 0 ) {

					if ( qty % step ) {

						is_valid = false;
						qty      = step * Math.ceil( qty / step );

						if ( ( 'keyup' === event.type || 'change' === event.type || 'click' === event.type || 'focus' === event.type ) && input.is( ':focus' ) ) {

							// Add error.
							setTimeout( function() {
								add_error_tip( input, 'Please enter an integer that is a multiple of ' + step + '.' );
							}, 5 );
						} else {
							// Valid value?
							input.val( qty ).change();
							is_valid = true;
						}
					}
				}

				// If the Minimum Quantity is greater than the Maximum Quantity, increase the Maximum Quantity.
				if ( qty > max.val() && '' !== max.val() ) {
					max.val( qty );
					max_qty_prev = qty;
				}

				max.prop( 'min', qty );

				if ( is_valid ) {
					remove_error_tip( input );
				}
			}

			return is_valid;
		};

		// Validate that the Maximum quantity is a multiple of the Group Of quantity.
		// If it isn't, then display an error an update its value.
		// Moreover, ensure that the Maximum Quantity is greater than the Minimum Quantity.
		// If it isn't, then display an error an update its value.
		var validate_max_qty = function( input, qty, step, min, previous_value = max_qty_prev ) {

			var is_valid = true;

			if ( qty !== '' ) {

				qty  = parseInt( qty, 10 );
				step = parseInt( step, 10 );
				min  = parseInt( min, 10 );

				if ( step > 0 && qty > 0 ) {

					if ( qty % step ) {
						is_valid = false;
						qty      = step * Math.floor( qty / step );

						if ( ( 'keyup' === event.type || 'change' === event.type || 'click' === event.type || 'focus' === event.type ) && input.is( ':focus' ) ) {
							// Add error.
							setTimeout( function() {
								add_error_tip( input, 'Please enter an integer that is a multiple of ' + step + '.' );
							}, 5 );

						} else {
							// Valid value?

							if ( 0 === qty ) {
								qty = '';
							}
							input.val( qty ).change();
							is_valid = true;
						}
					}
				}

				if ( qty < min && is_valid ) {

					if ( ( 'keyup' === event.type || 'change' === event.type ) && input.is( ':focus' ) ) {

						// Add error.
						setTimeout( function() {
							add_error_tip( input, 'Please enter an integer higher than ' + ( min - 1 ) + '.' );
						}, 5 );

					} else {
						// Valid value?
						if ( previous_value < min ) {
							previous_value = min;
						}
						input.val( previous_value ).change();
						qty      = previous_value;
						is_valid = true;
					}
				}

				if ( is_valid ) {
					max_qty_prev = qty;
					remove_error_tip( input );
				}
			} else {
				max_qty_prev = qty;
				remove_error_tip( input );
			}

			return is_valid;
		}

	} );

} )( jQuery, document, window );
