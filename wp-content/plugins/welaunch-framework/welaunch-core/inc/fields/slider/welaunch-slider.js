/*global welaunch_change, welaunch*/

(function( $ ) {
	'use strict';

	welaunch.field_objects        = welaunch.field_objects || {};
	welaunch.field_objects.slider = welaunch.field_objects.slider || {};

	welaunch.field_objects.slider.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'slider' );

		$( selector ).each(
			function() {
				var el     = $( this );
				var parent = el;

				if ( ! el.hasClass( 'welaunch-field-container' ) ) {
					parent = el.parents( '.welaunch-field-container:first' );
				}

				if ( parent.is( ':hidden' ) ) {
					return;
				}

				if ( parent.hasClass( 'welaunch-field-init' ) ) {
					parent.removeClass( 'welaunch-field-init' );
				} else {
					return;
				}

				el.find( 'div.welaunch-slider-container' ).each(
					function() {
						var start;
						var toClass;
						var defClassOne;
						var defClassTwo;
						var connectVal;
						var range;
						var startOne;
						var startTwo;
						var inputOne;
						var inputTwo;
						var classOne;
						var classTwo;
						var x;
						var y;
						var slider;
						var inpSliderVal;

						var DISPLAY_NONE   = 0;
						var DISPLAY_LABEL  = 1;
						var DISPLAY_TEXT   = 2;
						var DISPLAY_SELECT = 3;

						var mainID       = $( this ).data( 'id' );
						var minVal       = $( this ).data( 'min' );
						var maxVal       = $( this ).data( 'max' );
						var stepVal      = $( this ).data( 'step' );
						var handles      = $( this ).data( 'handles' );
						var defValOne    = $( this ).data( 'default-one' );
						var defValTwo    = $( this ).data( 'default-two' );
						var resVal       = $( this ).data( 'resolution' );
						var displayValue = parseInt( ( $( this ).data( 'display' ) ) );
						var rtlVal       = Boolean( $( this ).data( 'rtl' ) );
						var floatMark    = ( $( this ).data( 'float-mark' ) );

						var rtl;
						if ( true === rtlVal ) {
							rtl = 'rtl';
						} else {
							rtl = 'ltr';
						}

						// Range array.
						range = [minVal, maxVal];

						// Set default values for dual slides.
						startTwo = [defValOne, defValTwo];

						// Set default value for single slide.
						startOne = [defValOne];

						if ( displayValue === DISPLAY_TEXT ) {
							defClassOne = el.find( '.welaunch-slider-input-one-' + mainID );
							defClassTwo = el.find( '.welaunch-slider-input-two-' + mainID );

							inputOne = defClassOne;
							inputTwo = defClassTwo;
						} else if ( displayValue === DISPLAY_SELECT ) {
							defClassOne = el.find( '.welaunch-slider-select-one-' + mainID );
							defClassTwo = el.find( '.welaunch-slider-select-two-' + mainID );

							welaunch.field_objects.slider.loadSelect( defClassOne, minVal, maxVal, resVal, stepVal );

							if ( 2 === handles ) {
								welaunch.field_objects.slider.loadSelect( defClassTwo, minVal, maxVal, resVal, stepVal );
							}

						} else if ( displayValue === DISPLAY_LABEL ) {
							defClassOne = el.find( '#welaunch-slider-label-one-' + mainID );
							defClassTwo = el.find( '#welaunch-slider-label-two-' + mainID );
						} else if ( displayValue === DISPLAY_NONE ) {
							defClassOne = el.find( '.welaunch-slider-value-one-' + mainID );
							defClassTwo = el.find( '.welaunch-slider-value-two-' + mainID );
						}

						if ( displayValue === DISPLAY_LABEL ) {
							x = [defClassOne, 'html'];
							y = [defClassTwo, 'html'];

							classOne = [x];
							classTwo = [x, y];
						} else {
							classOne = [defClassOne];
							classTwo = [defClassOne, defClassTwo];
						}

						if ( 2 === handles ) {
							start      = startTwo;
							toClass    = classTwo;
							connectVal = true;
						} else {
							start      = startOne;
							toClass    = classOne;
							connectVal = 'lower';
						}

						slider = $( this ).welaunchNoUiSlider(
							{
								range: range,
								start: start,
								handles: handles,
								step: stepVal,
								connect: connectVal,
								behaviour: 'tap-drag',
								direction: rtl,
								serialization: {
									resolution: resVal,
									to: toClass,
									mark: floatMark
								},
								slide: function() {
									if ( displayValue === DISPLAY_LABEL ) {
										if ( 2 === handles ) {
											inpSliderVal = slider.val();
											el.find( 'input.welaunch-slider-value-one-' + mainID ).attr( 'value', inpSliderVal[0] );
											el.find( 'input.welaunch-slider-value-two-' + mainID ).attr( 'value', inpSliderVal[1] );
										} else {
											el.find( 'input.welaunch-slider-value-one-' + mainID ).attr( 'value', slider.val() );
										}
									}

									if ( displayValue === DISPLAY_SELECT ) {
										if ( 2 === handles ) {
											el.find( '.welaunch-slider-select-one' ).val( slider.val()[0] ).trigger( 'change' );
											el.find( '.welaunch-slider-select-two' ).val( slider.val()[1] ).trigger( 'change' );
										} else {
											el.find( '.welaunch-slider-select-one' ).val( slider.val() );
										}
									}

									welaunch_change( $( this ) );
								}
							}
						);

						if ( displayValue === DISPLAY_TEXT ) {
							inputOne.keydown(
								function( e ) {
									var sliderOne = slider.val();
									var value     = parseInt( sliderOne[0] );

									switch ( e.which ) {
										case 38:
											slider.val( [value + 1, null] );
											break;
										case 40:
											slider.val( [value - 1, null] );
											break;
										case 13:
											e.preventDefault();
											break;
									}
								}
							);

							if ( 2 === handles ) {
								inputTwo.keydown(
									function( e ) {
										var sliderTwo = slider.val();
										var value     = parseInt( sliderTwo[1] );

										switch ( e.which ) {
											case 38:
												slider.val( [null, value + 1] );
												break;
											case 40:
												slider.val( [null, value - 1] );
												break;
											case 13:
												e.preventDefault();
												break;
										}
									}
								);
							}
						}
					}
				);

				el.find( 'select.welaunch-slider-select-one, select.welaunch-slider-select-two' ).select2();
			}
		);
	};

	// Return true for float value, false otherwise.
	welaunch.field_objects.slider.isFloat = function( mixed_var ) {
		return + mixed_var === mixed_var && ( ! ( isFinite( mixed_var ) ) ) || Boolean( ( mixed_var % 1 ) );
	};

	// Return number of integers after the decimal point.
	welaunch.field_objects.slider.decimalCount = function( res ) {
		var q = res.toString().split( '.' );
		return q[1].length;
	};

	welaunch.field_objects.slider.loadSelect = function( myClass, min, max, res ) {
		var decCount;
		var i;
		var n;

		for ( i = min; i <= max; i = i + res ) {
			n = i;

			if ( welaunch.field_objects.slider.isFloat( res ) ) {
				decCount = welaunch.field_objects.slider.decimalCount( res );
				n        = i.toFixed( decCount );
			}

			$( myClass ).append( '<option value="' + n + '">' + n + '</option>' );
		}
	};
})( jQuery );
