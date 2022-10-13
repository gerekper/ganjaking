/* global woocommerce_admin_meta_boxes */
( function( $, document, window ) {

	$( function() {

		var $combine_variations_field    = $( '.allow_combination_field' ),
			$combine_variations_checkbox = $( '.options_group #allow_combination' ),
			max_qty_prev                 = $( 'input#maximum_allowed_quantity' ).val(),
			$previous_section            = $( '#min_max_settings' ).prevAll( '.options_group:visible:first' );

		if ( 'variable' === $( '#product-type' ).val() ) {
			$combine_variations_field.show();
		} else {
			$combine_variations_field.hide();
		}

		// Hide the border for the previous section to display custom border with embedded section title.
		$previous_section.addClass( 'mmq_previous_section' );

		// Select the target node.
		var target = document.querySelector( '#general_product_data' );

		// Create an observer instance.
		var observer = new MutationObserver( function( mutations) {
			$previous_section.removeClass( 'mmq_previous_section' );
			$previous_section = $( '#min_max_settings' ).prevAll( '.options_group:visible:first' );
			$previous_section.addClass( 'mmq_previous_section' );
		});

		if ( target !== null ) {
			// Pass in the target node, as well as the observer options.
			observer.observe( target, {
				attributes:    true,
				childList:     true,
				characterData: true
			});
		}

		$( document.body ).on( 'woocommerce-product-type-change', function( evt, value, variation ) {
			if ( 'variable' === value ) {
				$combine_variations_field.show();
			} else {
				$combine_variations_field.hide();
			}
		} );

		// When the Combine Variations checkbox is active, hide variation level Min/Max rules.
		$combine_variations_checkbox.on( 'change', function() {
			var $min_max_rules_options = $( '.checkbox.min_max_rules' ).parents( '.woocommerce_variable_attributes' ).find( '.min_max_rules_options' );

			$min_max_rules_options.each( function() {
				var $checkbox = $( this ).closest( '.woocommerce_variation .data' ).find( '.checkbox.min_max_rules' );

					if ( $combine_variations_checkbox.is( ':checked' ) ) {

						// Grey out Min/Max rules checkbox.
						$checkbox.prop( 'disabled', true );

						// Uncheck checked Min/Max rules checkboxes. Keep track of previously active checkboxes to revert them.
						if ( $checkbox.is( ':checked' ) ) {
							$checkbox.prop( 'checked', false );
							$checkbox.data( "prev-checked", true );
						}

						// Hide Min/Max Rules fields.
						$( this ).hide();

					} else {

						// Enable grey-out checkboxes.
						$checkbox.prop( 'disabled', false );

						// Check previously active checkboxes and display Min/Max Rules fields.
						if ( $checkbox.data( "prev-checked" ) ) {
							$checkbox.prop( 'checked', true );
							$( this ).show();
						}
					}
			} );
		} );

		$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded woocommerce_variations_added', function() {
			var $min_max_rules_options = $( '.checkbox.min_max_rules' ).parents( '.woocommerce_variable_attributes' ).find( '.min_max_rules_options' ),
				$min_quantities        = $( 'input.variation_minimum_allowed_quantity' ),
				$max_quantities        = $( 'input.variation_maximum_allowed_quantity' ),
				$group_of_quantities   = $( 'input.variation_group_of_quantity' );

			// Determine whether to show variation Min/Max rules based on the value of the Min/Max rules checkbox.
			$min_max_rules_options.each( function() {
				var $checkbox = $(this).closest(  '.woocommerce_variation .data' ).find( '.checkbox.min_max_rules' );

				// If Combine Variations is enabled, disable unchecked Min/Max Rules checkboxes.
				if ( $combine_variations_checkbox.is( ':checked' ) ) {
					$checkbox.prop( 'disabled', true );

					if ( $checkbox.is( ':checked' ) ) {
						$checkbox.prop( 'checked', false );
						$checkbox.data( "prev-checked", true );
					}

				} else {
					$checkbox.prop( 'disabled', false );

					if ( $checkbox.data( "prev-checked" ) ) {
						$checkbox.prop( 'checked', true );
					}
				}


				// Hide variation level Min/Max fields if the Min/Max Rules field is unchecked or disabled.
				if ( $checkbox.is( ':checked' ) && ! $checkbox.is( ':disabled' ) ) {
					$(this).show();
				} else {
					$(this).hide();
				}

			} );

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

			$group_of_quantities.each( function() {
				$( this ).on( 'keyup change focusout focus click', function() {
					var $input            = $( this ),
						qty               = $input.val(),
						$container        = $input.closest( '.min_max_rules_options' ),
						$category_exclude = $container.find( 'input.variation_minmax_category_group_of_exclude' );

					if ( qty ) {
						// If the user has already enabled "Exclude from Category rules" before filling in a "Group of" value, do not interfere with checkbox.
						if ( ! $category_exclude.is( ':checked' ) ) {
							$category_exclude.prop( 'disabled', true );
							$category_exclude.prop( 'checked', true );
						}
					} else {
						if ( $category_exclude.is( ':disabled' ) &&  $category_exclude.is( ':checked' ) ) {
							$category_exclude.prop( 'disabled', false );
							$category_exclude.prop( 'checked', false );
						}
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

		$( 'input#group_of_quantity' ).on( 'keyup change focusout focus click', function() {
			var $input            = $( this ),
				qty               = $input.val(),
				$container        = $input.closest( '.options_group' ),
				$category_exclude = $container.find( 'input.exclude_category_rules' );

				if ( qty ) {
					// If the user has already enabled "Exclude from Category rules" before filling in a "Group of" value, do not interfere with checkbox.
					if ( ! $category_exclude.is( ':checked' ) ) {
						$category_exclude.prop( 'disabled', true );
						$category_exclude.prop( 'checked', true );
					}
				} else {
					if ( $category_exclude.is( ':disabled' ) &&  $category_exclude.is( ':checked' ) ) {
						$category_exclude.prop( 'disabled', false );
						$category_exclude.prop( 'checked', false );
					}
				}
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
