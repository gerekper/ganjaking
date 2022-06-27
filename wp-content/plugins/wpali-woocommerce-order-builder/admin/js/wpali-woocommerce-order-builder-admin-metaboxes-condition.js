jQuery( document ).ready( function( $ ) {
	'use strict';

	/**
	 * Add 'show' and 'hide' event to JQuery event detection.
	 * @see http://viralpatel.net/blogs/jquery-trigger-custom-event-show-hide-element/
	 * 
	 * WWOB change on line : 38 | 10
	 */
	$('.cmb2-row').each(['show', 'hide'], function (i, ev) {
		var el = $.fn[ev];
		$.fn[ev] = function() {
			this.trigger( ev );
			return el.apply( this, arguments );
		};
	});


	/**
	 * Set up the functionality for CMB2 conditionals.
	 */
	function CMB2ConditionalsInit( changeContext, conditionContext ) {
		var loopI, requiredElms, uniqueFormElms, formElms;

		if ( 'undefined' === typeof changeContext ) {
			changeContext = '#wwob_metabox';
		}
		changeContext = $( changeContext );

		if ( 'undefined' === typeof conditionContext ) {
			conditionContext = '#wwob_metabox';
		}
		conditionContext = $( conditionContext );

		/**
		 * Set up event listener for any changes in the form values, including on new elements.
		 */
		changeContext.on( 'change', 'input, textarea, select', function( evt ) {
			
			if ($(this).parents('.cmb-repeatable-grouping').length) {
				return false;
			}
			
			var elm       = $( this ),
				fieldName = $( this ).attr( 'name' ),
				dependants,
				dependantsSeen = [],
				checkedValues,
				elmValue;
			// Is there an element which is conditional on this element ?
			dependants = CMB2ConditionalsFindDependants( fieldName, elm, conditionContext );

			// Only continue if we actually have dependants.
			if ( dependants.length > 0 ) {

				// Figure out the value for the current element.
				if ( 'checkbox' === elm.attr( 'type' ) ) {
					checkedValues = $( '[name="' + fieldName + '"]:checked' ).map( function() {
						return this.value;
					}).get();
				} else if ( 'radio' === elm.attr( 'type' ) ) {
					if ( $( '[name="' + fieldName + '"]' ).is( ':checked' ) ) {
						elmValue = elm.val();
					}
				} else {
					elmValue = CMB2ConditionalsStringToUnicode( evt.currentTarget.value );
				}

				dependants.each( function( i, e ) {
					var loopIndex        = 0,
						current          = $( e ),
						currentFieldName = current.attr( 'name' ),
						requiredValue    = current.data( 'conditional-value' ),
						currentParent    = current.parents( '.cmb-row:first' ),
						shouldShow       = false;

					// Only check this dependant if we haven't done so before for this parent.
					// We don't need to check ten times for one radio field with ten options,
					// the conditionals are for the field, not the option.
					if ( 'undefined' !== typeof currentFieldName && '' !== currentFieldName && $.inArray( currentFieldName, dependantsSeen ) < 0 ) {
						dependantsSeen.push = currentFieldName;

						if ( 'checkbox' === elm.attr( 'type' ) ) {
							if ( 'undefined' === typeof requiredValue ) {
								shouldShow = ( checkedValues.length > 0 );
							} else if ( 'off' === requiredValue ) {
								shouldShow = ( 0 === checkedValues.length );
							} else if ( checkedValues.length > 0 ) {
								if ( 'string' === typeof requiredValue ) {
									shouldShow = ( $.inArray( requiredValue, checkedValues ) > -1 );
								} else if ( Array.isArray( requiredValue ) ) {
									for ( loopIndex = 0; loopIndex < requiredValue.length; loopIndex++ ) {
										if ( $.inArray( requiredValue[loopIndex], checkedValues ) > -1 ) {
											shouldShow = true;
											break;
										}
									}
								}
							}
						} else if ( 'undefined' === typeof requiredValue ) {
							shouldShow = ( elm.val() ? true : false );
						} else {
							if ( 'string' === typeof requiredValue ) {
								shouldShow = ( elmValue === requiredValue );
							} else if ( Array.isArray( requiredValue ) ) {
								shouldShow = ( $.inArray( elmValue, requiredValue ) > -1 );
							}
						}

						// Handle any actions necessary.
						currentParent.toggle( shouldShow );
						if ( current.data( 'conditional-required' ) ) {
							current.prop( 'required', shouldShow );
						}

						// If we're hiding the row, hide all dependants (and their dependants).
						if ( false === shouldShow ) {
							CMB2ConditionalsRecursivelyHideDependants( currentFieldName, current, conditionContext );
						}

						// If we're showing the row, check if any dependants need to become visible.
						else {
							if ( 1 === current.length ) {
								current.trigger( 'change' );
							} else {
								current.filter( ':checked' ).trigger( 'change' );
							}
						}
					}
				});
			}
		});


		/**
		 * Make sure it also works when the select/deselect all button is clicked for a multi-checkbox.
		 */
		conditionContext.on( 'click', '.cmb-multicheck-toggle', function( evt ) {
			var button, multiCheck;
			evt.preventDefault();
			button     = $( this );
			multiCheck = button.closest( '.cmb-td' ).find( 'input[type=checkbox]:not([disabled])' );
			multiCheck.trigger( 'change' );
		});


		/**
		 * Deal with (un)setting the required property on (un)hiding of form elements.
		 */

		// Remove the required property from form elements within rows being hidden.
		conditionContext.on( 'hide', '.cmb-row', function() {
			$( this ).children( '[data-conditional-required="required"]' ).each( function( i, e ) {
				$( e ).prop( 'required', false );
			});
		});

		// Add the required property to form elements within rows being unhidden.
		conditionContext.on( 'show', '.cmb-row', function() {
			$( this ).children( '[data-conditional-required="required"]' ).each( function( i, e ) {
				$( e ).prop( 'required', true );
			});
		});


		/**
		 * Set the initial state for elements on page load.
		 */

		// Unset required attributes
		requiredElms = $( 'div#wwob_metabox [data-conditional-id][required]', conditionContext );
		requiredElms.data( 'conditional-required', requiredElms.prop( 'required' ) ).prop( 'required', false );

		// Hide all conditional elements
		$( 'div#wwob_metabox [data-conditional-id]', conditionContext ).parents( '.cmb-row:first' ).hide();

		// Selectively trigger the change event.
		uniqueFormElms = [];
		$( 'div#wwob_metabox input', changeContext ).each( function( i, e ) {
			var elmName = $( e ).attr( 'name' );
			if ( 'undefined' !== typeof elmName && '' !== elmName && -1 === $.inArray( elmName, uniqueFormElms ) ) {
				uniqueFormElms.push( elmName );
			}
		});
		for ( loopI = 0; loopI < uniqueFormElms.length; loopI++ ) {
			formElms = $( '[name="' + uniqueFormElms[loopI] + '"]' );
			if ( 1 === formElms.length || ! formElms.is( ':checked' ) ) {
				formElms.trigger( 'change' );
			} else {
				formElms.filter( ':checked' ).trigger( 'change' );
			}
		}
		
		/**
		 * Set the initial state of new elements which are added to the page dynamically (i.e. group elms).
		 */
		$( '.cmb2-wrap > .cmb2-metabox' ).on( 'cmb2_add_row', function( evt, row ) {
			var rowFormElms,
				rowRequiredElms = $( '[data-conditional-id][required]', row );

			rowRequiredElms.data( 'conditional-required', rowRequiredElms.prop( 'required' ) ).prop( 'required', false );

			// Hide all conditional elements
			$( '[data-conditional-id]', row ).parents( '.cmb-row:first' ).hide();

			rowFormElms = $( ':input', row );
			if ( 1 === rowFormElms.length || ! rowFormElms.is( ':checked' ) ) {
				rowFormElms.trigger( 'change' );
			} else {
				rowFormElms.filter( ':checked' ).trigger( 'change' );
			}
		});
	}


	/**
	 * Find all fields which are directly conditional on the current field.
	 *
	 * Allows for within group dependencies and multi-check dependencies.
	 */
	function CMB2ConditionalsFindDependants( fieldName, elm, context ) {
		var inGroup, iterator, dependants;
		

		if (typeof fieldName === 'undefined') {
			return false;
		}

		if ($(elm).parents('#wwob_metabox').length == 0) {
			return false;
		}
		
		// Remove the empty [] at the end of a multi-check field.
		fieldName = fieldName.replace( /\[\]$/, '' );

		// Is there an element which is conditional on this element ?
		// If a group element, within the group.
		inGroup = elm.closest( '.cmb-repeatable-grouping' );
		if ( 1 === inGroup.length ) {
			iterator = elm.closest( '[data-iterator]' ).data( 'iterator' );
			dependants = $( '[data-conditional-id]', inGroup ).filter( function() {
				var conditionalId = $( this ).data( 'conditional-id' );
				return ( Array.isArray( conditionalId ) && ( fieldName === conditionalId[0] + '[' + iterator + '][' + conditionalId[1] + ']' ) );
			});
		}

		// Else within the whole form.
		else {
			dependants = $( '[data-conditional-id="' + fieldName + '"]', context );
		}
		return dependants;
	}

	/**
	 * Recursively hide all fields which have a dependency on a certain field.
	 */
	function CMB2ConditionalsRecursivelyHideDependants( fieldName, elm, context ) {
		
		var dependants = CMB2ConditionalsFindDependants( fieldName, elm, context );
		dependants = dependants.filter( ':visible' );

		if ( dependants.length > 0 ) {
			dependants.each( function( i, e ) {
				var dependant     = $( e ),
					dependantName = dependant.attr( 'name' );

				// Hide it.
				dependant.parents( '.cmb-row:first' ).hide();
				if ( dependant.data( 'conditional-required' ) ) {
					dependant.prop( 'required', false );
				}

				// And do the same for dependants.
				CMB2ConditionalsRecursivelyHideDependants( dependantName, dependant, context );
			});
		}
	}


	function CMB2ConditionalsStringToUnicode( string ) {
		var i, result = '',
			map = ['Á', 'A', '?', '?', '?', '?', '?', 'A', 'Â', '?', '?', '?', '?', '?', 'Ä', 'A', '?', '?', '?', '?', 'À', '?', '?', 'A', 'A', 'Å', '?', '?', '?', 'Ã', '?', 'Æ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'C', 'C', 'Ç', '?', 'C', 'C', '?', '?', 'D', '?', '?', '?', '?', '?', '?', '?', '?', 'Ð', '?', '?', '?', 'É', 'E', 'E', '?', '?', 'Ê', '?', '?', '?', '?', '?', '?', 'Ë', 'E', '?', '?', 'È', '?', '?', 'E', '?', '?', 'E', '?', '?', '?', '?', '?', 'ƒ', '?', 'G', 'G', 'G', 'G', 'G', '?', '?', 'G', '?', '?', '?', 'H', '?', '?', '?', '?', 'H', 'Í', 'I', 'I', 'Î', 'Ï', '?', 'I', '?', '?', 'Ì', '?', '?', 'I', 'I', 'I', 'I', '?', '?', '?', '?', '?', '?', '?', '?', 'J', '?', '?', 'K', 'K', '?', '?', '?', '?', '?', '?', '?', 'L', '?', 'L', 'L', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'L', '?', '?', '?', '?', '?', 'N', 'N', 'N', '?', '?', '?', '?', '?', '?', '?', '?', 'Ñ', '?', 'Ó', 'O', 'O', 'Ô', '?', '?', '?', '?', '?', 'Ö', '?', '?', '?', '?', 'O', '?', 'Ò', '?', 'O', '?', '?', '?', '?', '?', '?', '?', '?', 'O', '?', '?', 'O', 'O', 'O', 'Ø', '?', 'Õ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'R', 'R', 'R', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'S', '?', 'Š', '?', 'S', 'S', '?', '?', '?', '?', 'T', 'T', '?', '?', '?', '?', '?', '?', '?', 'T', 'T', '?', '?', '?', '?', '?', 'Ú', 'U', 'U', 'Û', '?', 'Ü', 'U', 'U', 'U', 'U', '?', '?', 'U', '?', 'Ù', '?', 'U', '?', '?', '?', '?', '?', '?', 'U', '?', 'U', 'U', 'U', '?', '?', '?', '?', '?', '?', '?', '?', 'W', '?', '?', '?', '?', '?', '?', '?', 'Ý', 'Y', 'Ÿ', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Z', 'Ž', '?', '?', 'Z', '?', '?', '?', '?', '?', 'Œ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'á', 'a', '?', '?', '?', '?', '?', 'a', 'â', '?', '?', '?', '?', '?', 'ä', 'a', '?', '?', '?', '?', 'à', '?', '?', 'a', 'a', '?', '?', 'å', '?', '?', '?', 'ã', '?', 'æ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'b', '?', '?', 'c', 'c', 'ç', '?', 'c', '?', 'c', '?', '?', 'd', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'd', '?', '?', 'i', '?', '?', '?', '?', '?', 'é', 'e', 'e', '?', '?', 'ê', '?', '?', '?', '?', '?', '?', 'ë', 'e', '?', '?', 'è', '?', '?', 'e', '?', '?', '?', 'e', '?', '?', '?', '?', '?', '?', 'ƒ', '?', '?', '?', 'g', 'g', 'g', 'g', 'g', '?', '?', '?', 'g', '?', '?', '?', 'h', '?', '?', '?', '?', '?', '?', 'h', '?', 'í', 'i', 'i', 'î', 'ï', '?', '?', '?', 'ì', '?', '?', 'i', 'i', '?', '?', 'i', '?', '?', '?', '?', '?', '?', '?', '?', 'j', 'j', '?', '?', '?', 'k', 'k', '?', '?', '?', '?', '?', '?', '?', '?', 'l', 'l', '?', 'l', 'l', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'l', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'n', 'n', 'n', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'ñ', '?', 'ó', 'o', 'o', 'ô', '?', '?', '?', '?', '?', 'ö', '?', '?', '?', '?', 'o', '?', 'ò', '?', 'o', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'o', '?', '?', 'o', 'o', 'ø', '?', 'õ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'r', 'r', 'r', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 's', '?', 'š', '?', 's', 's', '?', '?', '?', '?', '?', '?', '?', '?', 'g', '?', '?', '?', 't', 't', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 't', '?', 't', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'ú', 'u', 'u', 'û', '?', 'ü', 'u', 'u', 'u', 'u', '?', '?', 'u', '?', 'ù', '?', 'u', '?', '?', '?', '?', '?', '?', 'u', '?', 'u', '?', 'u', 'u', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'w', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'ý', 'y', 'ÿ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'z', 'ž', '?', '?', '?', 'z', '?', '?', '?', '?', '?', '?', 'z', '?', '?', '?', '?', '?', '?', '?', 'œ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?'];

		for ( i = 0; i < string.length; i++ ) {
			if ( $.inArray( string[i], map ) === -1 ) {
				result += string[i];
			} else {
				result += '\\u' + ( '000' + string[i].charCodeAt( 0 ).toString( 16 ) ).substr( -4 );
			}
		}

		return result;
	}

	CMB2ConditionalsInit( '#post', '#post .cmb2-wrap' );
});