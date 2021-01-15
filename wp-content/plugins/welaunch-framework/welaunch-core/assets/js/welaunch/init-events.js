/* global welaunch, welaunch_change */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.initEvents = function( el ) {
		var stickyHeight;

		el.find( '.welaunch-presets-bar' ).on(
			'click',
			function() {
				window.onbeforeunload = null;
			}
		);

		// Customizer save hook.
		el.find( '#customize-save-button-wrapper #save' ).on(
			'click',
			function() {

			}
		);

		el.find( '#toplevel_page_' + welaunch.optName.args.slug + ' .wp-submenu a, #wp-admin-bar-' + welaunch.optName.args.slug + ' a.ab-item' ).click(
			function( e ) {
				var url;

				if ( ( el.find( '#toplevel_page_' + welaunch.optName.args.slug ).hasClass( 'wp-menu-open' ) ||
					$( this ).hasClass( 'ab-item' ) ) &&
					! $( this ).parents( 'ul.ab-submenu:first' ).hasClass( 'ab-sub-secondary' ) &&
					$( this ).attr( 'href' ).toLowerCase().indexOf( welaunch.optName.args.slug + '&tab=' ) >= 0 ) {

					url = $( this ).attr( 'href' ).split( '&tab=' );

					e.preventDefault();

					el.find( '#' + url[1] + '_section_group_li_a' ).click();

					$( this ).parents( 'ul:first' ).find( '.current' ).removeClass( 'current' );
					$( this ).addClass( 'current' );
					$( this ).parent().addClass( 'current' );

					return false;
				}
			}
		);

		// Save button clicked.
		el.find( '.welaunch-action_bar input, #welaunch-import-action input' ).on(
			'click',
			function( e ) {
				if ( $( this ).attr( 'name' ) === welaunch.optName.args.opt_name + '[defaults]' ) {

					// Defaults button clicked.
					if ( ! confirm( welaunch.optName.args.reset_confirm ) ) {
						return false;
					}
				} else if ( $( this ).attr( 'name' ) === welaunch.optName.args.opt_name + '[defaults-section]' ) {

					// Default section clicked.
					if ( ! confirm( welaunch.optName.args.reset_section_confirm ) ) {
						return false;
					}
				} else if ( 'import' === $( this ).attr( 'name' ) ) {
					if ( ! confirm( welaunch.optName.args.import_section_confirm ) ) {
						return false;
					}
				}

				window.onbeforeunload = null;

				if ( true === welaunch.optName.args.ajax_save ) {
					$.welaunch.ajax_save( $( this ) );
					e.preventDefault();
				} else {
					location.reload( true );
				}
			}
		);

		$( '.expand_options' ).click(
			function( e ) {
				var tab;

				var container = el;

				e.preventDefault();

				if ( $( container ).hasClass( 'fully-expanded' ) ) {
					$( container ).removeClass( 'fully-expanded' );

					tab = $.cookie( 'welaunch_current_tab_' + welaunch.optName.args.opt_name );

					el.find( '#' + tab + '_section_group' ).fadeIn(
						200,
						function() {
							if ( 0 !== el.find( '#welaunch-footer' ).length ) {
								$.welaunch.stickyInfo(); // Race condition fix.
							}

							$.welaunch.initFields();
						}
					);
				}

				$.welaunch.expandOptions( $( this ).parents( '.welaunch-container:first' ) );

				return false;
			}
		);

		if ( el.find( '.saved_notice' ).is( ':visible' ) ) {
			el.find( '.saved_notice' ).slideDown();
		}

		$( document.body ).on(
			'change',
			'.welaunch-field input, .welaunch-field textarea, .welaunch-field select',
			function() {
				if ( $( '.welaunch-container-typography select' ).hasClass( 'ignore-change' ) ) {
					return;
				}
				if ( ! $( this ).hasClass( 'noUpdate' ) && ! $( this ).hasClass( 'no-update' ) ) {
					welaunch_change( $( this ) );
				}
			}
		);

		stickyHeight = el.find( '#welaunch-footer' ).height();

		el.find( '#welaunch-sticky-padder' ).css(
			{ height: stickyHeight }
		);

		el.find( '#welaunch-footer-sticky' ).removeClass( 'hide' );

		if ( 0 !== el.find( '#welaunch-footer' ).length ) {
			$( window ).scroll(
				function() {
					$.welaunch.stickyInfo();
				}
			);

			$( window ).resize(
				function() {
					$.welaunch.stickyInfo();
				}
			);
		}

		el.find( '.saved_notice' ).delay( 4000 ).slideUp();
	};
})( jQuery );
