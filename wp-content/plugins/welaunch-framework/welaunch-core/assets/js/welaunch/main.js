/* global welaunch, document */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$( document ).ready(
		function() {
			var opt_name;
			var li;

			var tempArr = [];

			$.fn.isOnScreen = function() {
				var win;
				var viewport;
				var bounds;

				if ( ! window ) {
					return;
				}

				win = $( window );
				viewport = {
					top: win.scrollTop()
				};

				viewport.right = viewport.left + win.width();
				viewport.bottom = viewport.top + win.height();

				bounds = this.offset();

				bounds.right = bounds.left + this.outerWidth();
				bounds.bottom = bounds.top + this.outerHeight();

				return ( ! ( viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom ) );
			};

			$( 'fieldset.welaunch-container-divide' ).css( 'display', 'none' );

			// Weed out multiple instances of duplicate weLaunch instance.
			if ( welaunch.customizer ) {
				$( '.wp-full-overlay-sidebar' ).addClass( 'welaunch-container' );
			}

			$( '.welaunch-container' ).each(
				function() {
					opt_name = $.welaunch.getOptName( this );

					if ( $.inArray( opt_name, tempArr ) === -1 ) {
						tempArr.push( opt_name );
						$.welaunch.checkRequired( $( this ) );
						$.welaunch.initEvents( $( this ) );
					}
				}
			);

			$( '.welaunch-container' ).on(
				'click',
				function() {
					opt_name = $.welaunch.getOptName( this );
				}
			);

			if ( undefined !== welaunch.optName ) {
				$.welaunch.disableFields();
				$.welaunch.hideFields();
				$.welaunch.disableSections();
				$.welaunch.initQtip();
				$.welaunch.tabCheck();
				$.welaunch.notices();
			}
		}
	);

	$.welaunch.disableSections = function() {
		$( '.welaunch-group-tab' ).each(
			function() {
				if ( $( this ).hasClass( 'disabled' ) ) {
					$( this ).find( 'input, select, textarea' ).attr( 'name', '' );
				}
			}
		);
	};

	$.welaunch.disableFields = function() {
		$( 'label[for="welaunch_disable_field"]' ).each(
			function() {
				$( this ).parents( 'tr' ).find( 'fieldset:first' ).find( 'input, select, textarea' ).attr( 'name', '' );
			}
		);
	};

	$.welaunch.hideFields = function() {
		$( 'label[for="welaunch_hide_field"]' ).each(
			function() {
				var tr = $( this ).parent().parent();

				$( tr ).addClass( 'hidden' );
			}
		);
	};

	$.welaunch.getOptName = function( el ) {
		var metabox;
		var li;
		var optName;
		var item = $( el );

		if ( welaunch.customizer ) {
			optName = item.find( '.welaunch-customizer-opt-name' ).data( 'opt-name' );
		} else {
			optName = $( el ).parents( '.welaunch-wrap-div' ).data( 'opt-name' );
		}

		// Compatibility for metaboxes
		if ( undefined === optName ) {
			metabox = $( el ).parents( '.postbox' );
			if ( 0 === metabox.length ) {
				metabox = $( el ).parents( '.welaunch-metabox' );
			}
			if ( 0 !== metabox.length ) {
				optName = metabox.attr( 'id' ).replace( 'welaunch-', '' ).split( '-metabox-' )[0];
				if ( undefined === optName ) {
					optName = metabox.attr( 'class' )
					.replace( 'welaunch-metabox', '' )
					.replace( 'postbox', '' )
					.replace( 'welaunch-', '' )
					.replace( 'hide', '' )
					.replace( 'closed', '' )
					.trim();
				}
			} else {
				optName = $( '.welaunch-ajax-security' ).data( 'opt-name' );
			}
		}
		if ( undefined === optName ) {
			optName = $( el ).find( '.welaunch-form-wrapper' ).data( 'opt-name' );
		}

		// Shim, let's just get an opt_name shall we?!
		if ( undefined === optName ) {
			optName = welaunch.opt_names[0];
		}

		if ( undefined !== optName ) {
			welaunch.optName = window['welaunch_' + optName.replace( /\-/g, '_' )];
		}

		return optName;
	};

	$.welaunch.getSelector = function( selector, fieldType ) {
		if ( ! selector ) {
			selector = '.welaunch-container-' + fieldType + ':visible';
			if ( welaunch.customizer ) {
				selector = $( document ).find( '.control-section-welaunch.open' ).find( selector );
			} else {
				selector = $( document ).find( '.welaunch-group-tab:visible' ).find( selector );
			}
		}
		return selector;
	};
})( jQuery );
