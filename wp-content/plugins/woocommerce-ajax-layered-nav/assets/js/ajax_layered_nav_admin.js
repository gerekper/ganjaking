/* Ajax-Layered Nav Widgets
 * Shopping Cart: WooCommerce
 * File: Admin JS
 * License: GPL
 * Copyright: SixtyOneDesigns
 */

/*	Event: document.ready
 *  	Add Live Handlers to:
 * 			1. Attribute Values dropdown
 * 			2. Layered Nav Type dropdown
 * 			3. Colorpickers
 */
jQuery( document ).ready( function () {
	/* Populate attribute values on attribute change*/
	jQuery( document ).on( 'change', '.layered_nav_attributes', function () {
		const $type = jQuery( this )
			.parent()
			.parent()
			.find( '.layered_nav_type' );
		const attrName = jQuery( this )
			.parent()
			.parent()
			.find( '.layered_nav_attributes' )
			.val();
		const id = jQuery( this )
			.parent()
			.parent()
			.parent()
			.find( '.widget-id' )
			.val();
		const target = '#widget-' + id + '-labels';
		if ( jQuery( ' option:selected' ).length ) {
			jQuery( target ).empty().addClass( 'spinner' );
			jQuery( target ).load(
				ajax_layered_nav.ajaxurl,
				{
					action: 'ajax_layered_nav_set_type',
					attr_name: attrName,
					type: $type.val(),
					id,
					ajax_layered_nav_nonce: ajax_layered_nav.nonce,
				},
				function () {
					jQuery( target ).removeClass( 'spinner' );
				}
			);
		}
	} );

	// Sets layered nav display type.
	jQuery( document ).on( 'change', '.layered_nav_type', function () {
		const $this = jQuery( this );
		const attrName = jQuery( this )
			.parent()
			.parent()
			.find( '.layered_nav_attributes' )
			.val();
		const id = jQuery( this )
			.parent()
			.parent()
			.parent()
			.find( '.widget-id' )
			.val();
		const target = '#widget-' + id + '-labels';
		jQuery( target ).empty().addClass( 'spinner' );
		jQuery( target ).load(
			ajax_layered_nav.ajaxurl,
			{
				action: 'ajax_layered_nav_set_type',
				attr_name: attrName,
				type: $this.val(),
				id,
				ajax_layered_nav_nonce: ajax_layered_nav.nonce,
			},
			function () {
				jQuery( target ).removeClass( 'spinner' );
			}
		);
	} );
	/* Show color picker on focusin*/
	jQuery( document ).on( 'focusin', '.color_input', function () {
		jQuery( this ).showColorPicker();
	} );
} );

/*	Function: showColorPicker()
 *	Shows jquery UI color picker and updates adjacent input box with picker hex value
 */
jQuery.fn.showColorPicker = function () {
	const $this = jQuery( this[ 0 ] ); //cache a copy of the this variable for use inside nested function
	const initialColor = jQuery( $this ).attr( 'value' );
	jQuery( this ).ColorPicker( {
		color: initialColor,
		onShow( colpkr ) {
			jQuery( colpkr ).fadeIn( 500 ).css( 'zIndex', 999999 );
			return false;
		},
		onHide( colpkr ) {
			jQuery( colpkr ).fadeOut( 500 );
			return false;
		},
		onChange( hsb, hex ) {
			jQuery( $this )
				.parent()
				.find( '.colorSelector div' )
				.css( 'backgroundColor', '#' + hex );
			jQuery( $this )
				.attr( 'value', '#' + hex )
				.change();
		},
	} );
};
/**
 *
 * Zoomimage
 * Author: Stefan Petre www.eyecon.ro
 * required for colorpicker to show-up
 */
( function ( $ ) {
	const EYE = ( window.EYE = ( function () {
		const _registered = {
			init: [],
		};
		return {
			init() {
				$.each( _registered.init, function ( nr, fn ) {
					fn.call();
				} );
			},
			extend( prop ) {
				for ( const i in prop ) {
					if ( prop[ i ] !== undefined ) {
						this[ i ] = prop[ i ];
					}
				}
			},
			register( fn, type ) {
				if ( ! _registered[ type ] ) {
					_registered[ type ] = [];
				}
				_registered[ type ].push( fn );
			},
		};
	} )() );
	$( EYE.init );
} )( jQuery );
