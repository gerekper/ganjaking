/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.initQtip = function() {
		var classes;

		// Shadow.
		var shadow    = '';
		var tipShadow = welaunch.optName.args.hints.tip_style.shadow;

		// Color.
		var color    = '';
		var tipColor = welaunch.optName.args.hints.tip_style.color;

		// Rounded.
		var rounded    = '';
		var tipRounded = welaunch.optName.args.hints.tip_style.rounded;

		// Tip style.
		var style    = '';
		var tipStyle = welaunch.optName.args.hints.tip_style.style;

		// Get position data.
		var myPos = welaunch.optName.args.hints.tip_position.my;
		var atPos = welaunch.optName.args.hints.tip_position.at;

		// Tooltip trigger action.
		var showEvent = welaunch.optName.args.hints.tip_effect.show.event;
		var hideEvent = welaunch.optName.args.hints.tip_effect.hide.event;

		// Tip show effect.
		var tipShowEffect   = welaunch.optName.args.hints.tip_effect.show.effect;
		var tipShowDuration = welaunch.optName.args.hints.tip_effect.show.duration;

		// Tip hide effect.
		var tipHideEffect   = welaunch.optName.args.hints.tip_effect.hide.effect;
		var tipHideDuration = welaunch.optName.args.hints.tip_effect.hide.duration;

		if ( $().qtip ) {
			if ( true === tipShadow ) {
				shadow = 'qtip-shadow';
			}

			if ( '' !== tipColor ) {
				color = 'qtip-' + tipColor;
			}

			if ( true === tipRounded ) {
				rounded = 'qtip-rounded';
			}

			if ( '' !== tipStyle ) {
				style = 'qtip-' + tipStyle;
			}

			classes = shadow + ',' + color + ',' + rounded + ',' + style + ',welaunch-qtip';
			classes = classes.replace( /,/g, ' ' );

			// Gotta be lowercase, and in proper format.
			myPos = $.welaunch.verifyPos( myPos.toLowerCase(), true );
			atPos = $.welaunch.verifyPos( atPos.toLowerCase(), false );

			$( 'div.welaunch-dev-qtip' ).each(
				function() {
					$( this ).qtip(
						{
							content: {
								text: $( this ).attr( 'qtip-content' ),
								title: $( this ).attr( 'qtip-title' )
							}, show: {
								effect: function() {
									$( this ).slideDown( 500 );
								},
								event: 'mouseover'
							}, hide: {
								effect: function() {
									$( this ).slideUp( 500 );
								},
								event: 'mouseleave'
							}, style: {
								classes: 'qtip-shadow qtip-light'
							}, position: {
								my: 'top center',
								at: 'bottom center'
							}
						}
					);
				}
			);

			$( 'div.welaunch-hint-qtip' ).each(
				function() {
					$( this ).qtip(
						{
							content: {
								text: $( this ).attr( 'qtip-content' ),
								title: $( this ).attr( 'qtip-title' )
							}, show: {
								effect: function() {
									switch ( tipShowEffect ) {
										case 'slide':
											$( this ).slideDown( tipShowDuration );
											break;
										case 'fade':
											$( this ).fadeIn( tipShowDuration );
											break;
										default:
											$( this ).show();
											break;
									}
								},
								event: showEvent
							}, hide: {
								effect: function() {
									switch ( tipHideEffect ) {
										case 'slide':
											$( this ).slideUp( tipHideDuration );
											break;
										case 'fade':
											$( this ).fadeOut( tipHideDuration );
											break;
										default:
											$( this ).hide( tipHideDuration );
											break;
									}
								},
								event: hideEvent
							}, style: {
								classes: classes
							}, position: {
								my: myPos,
								at: atPos
							}
						}
					);
				}
			);

			$( 'input[qtip-content]' ).each(
				function() {
					$( this ).qtip(
						{
							content: {
								text: $( this ).attr( 'qtip-content' ),
								title: $( this ).attr( 'qtip-title' )
							},
							show: 'focus',
							hide: 'blur',
							style: classes,
							position: {
								my: myPos,
								at: atPos
							}
						}
					);
				}
			);
		}
	};

	$.welaunch.verifyPos = function( s, b ) {
		var split;
		var paramOne;
		var paramTwo;

		// Trim off spaces.
		s = s.replace( /^\s+|\s+$/gm, '' );

		// Position value is blank, set the default.
		if ( '' === s || - 1 === s.search( ' ' ) ) {
			if ( true === b ) {
				return 'top left';
			} else {
				return 'bottom right';
			}
		}

		// Split string into array.
		split = s.split( ' ' );

		// Evaluate first string.  Must be top, center, or bottom.
		paramOne = b ? 'top' : 'bottom';

		if ( 'top' === split[0] || 'center' === split[0] || 'bottom' === split[0] ) {
			paramOne = split[0];
		}

		// Evaluate second string.  Must be left, center, or right.
		paramTwo = b ? 'left' : 'right';

		if ( 'left' === split[1] || 'center' === split[1] || 'right' === split[1] ) {
			paramTwo = split[1];
		}

		return paramOne + ' ' + paramTwo;
	};
})( jQuery );
