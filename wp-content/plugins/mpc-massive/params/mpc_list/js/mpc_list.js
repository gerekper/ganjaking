/*----------------------------------------------------------------------------*\
	MPC_LIST PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $lists = $( '.mpc-vc-list-wrap' );

	$lists.each( function() {
		var $list  = $( this ),
			$order = $list.find( '.mpc-vc-list-order' ),
			$input  = $list.find( '.mpc-value' ),
			$options = $list.find( '.mpc-vc-list' );

		$order.sortable( {
			placeholder: 'mpc-list-item mpc-placeholder'
		} ).on( 'sortupdate', function() {
			var _order = [];

			$order.find( '.mpc-list-item' ).each( function() {
				_order.push( $( this ).attr( 'data-id' ) );
			} );

			$input.val( _order.join( ',' ) ).trigger( 'change' );
		} );

		$order.disableSelection();

		$options.on( 'change', '.mpc-list-option', function() {
			var $option = $( this ),
				_value = $option.val();

			if ( $option.prop( 'checked' ) ) {
				$order.append( '<div class="mpc-list-item mpc-list-' + _value + '" data-id="' + _value + '"><i class="dashicons dashicons-sort"></i>' + $option.attr( 'data-name' ) + '</div>' );
			} else {
				$order.find( '.mpc-list-item[data-id="' + _value + '"]' ).remove();
			}

			if ( $order.find( '.mpc-list-item' ).length == 0 ) {
				$order.addClass( 'mpc-empty' );
			} else {
				$order.removeClass( 'mpc-empty' );
			}

			$order.trigger( 'sortupdate' );
		} );

		$input.on( 'mpc.change', function() {
			var _items = $input.val();

			$options.find( '.mpc-list-option:checked' ).trigger( 'click' );

			if ( _items != '' ) {
				_items = _items.split( ',' );

				for ( var _index = 0; _index < _items.length; _index++ ) {
					$options.find( '.mpc-list-option[value="' + _items[ _index ] + '"]' ).trigger( 'click' );
				}
			}
		} );
	} );
} )( jQuery );
