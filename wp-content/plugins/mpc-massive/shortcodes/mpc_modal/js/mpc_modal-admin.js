/*----------------------------------------------------------------------------*\
 MODAL BOX SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	// vc.shortcodes.findWhere( { id: $popup.find( '[name="button_link"]' ).val() } )

	// If we find a way to save shortcode...
	// var $popup = $( '#vc_ui-panel-edit-element' );
	//
	// $popup.on( 'mpc.render', function() {
	// 	if ( ! $popup.is( '[data-vc-shortcode="mpc_modal"]' ) ) {
	// 		return;
	// 	}
	//
	// 	var _modal = vc.active_panel.model,
	// 		_modals = vc.shortcodes.where( { shortcode: 'mpc_modal' } ),
	// 		_id = _modal.get( 'params' )[ 'onclick_id' ];
	//
	// 	if ( _modal.get( 'cloned' ) ) {
	// 		console.log('cloned');
	// 		_modals.forEach( function( modal ) {
	// 			console.log(modal.get( 'params' )[ 'onclick_id' ], _id);
	// 			if ( modal.get( 'params' )[ 'onclick_id' ] == _id ) {
	// 				$( '.wpb_vc_param_value.onclick_id' ).val( 'modal_id_' + Date.now().toString(16) );
	// 			}
	// 		} );
	// 	}
	// } );

	//var shortcode = vc.shortcodes.findWhere( { id: 'e1d25581-9699' } );
	//shortcode.save()
	//function retrieve_buttons() {
	//	var $button_linker = $popup.find( '[name="button_link"]' ),
	//		_buttons = [],
	//		_options = '';
	//
	//	if( $button_linker.html() != '' ) {
	//		return;
	//	}
	//
	//	vc.shortcodes.models.forEach( function( _shortcode ) {
	//		if( _shortcode.attributes.shortcode == 'mpc_button' ) {
	//			var _title = '';
	//			_title += typeof _shortcode.attributes.params.title !== 'undefined' ? _shortcode.attributes.params.title + ' ' : '';
	//			_title += typeof _shortcode.attributes.params.url !== 'undefined' ? _shortcode.attributes.params.url : '';
	//			_title = _title == '' ? _shortcode.attributes.id : urldecode( _title );
	//			_buttons.push( { 'value' : _shortcode.attributes.id, 'title' : _title } );
	//		}
	//	});
	//
	//	_buttons.forEach( function( _button ) {
	//		_options += '<option value="' + _button.value + '">' + _button.title + '</option>';
	//	});
	//	$button_linker.html( _options );
	//}
	//
	//function link_button( $popup, _modal_id ) {
	//	var $frequency = $popup.find( '[name="frequency"]' );
	//
	//	if( $frequency.val() != 'onclick' ) {
	//		return false;
	//	}
	//
	//	var _button = vc.shortcodes.findWhere( { id: $popup.find( '[name="button_link"]' ).val() } );
	//
	//	_button.attributes.params.modal_id = _modal_id; console.log( _modal_id );
	//	_button.save();
	//}
	//
	//var $popup = $( '#vc_ui-panel-edit-element' );
	//
	//$popup.on( 'mpc.render', function() {
	//	if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_modal' ) {
	//		return '';
	//	}
	//
	//	var $frequency = $popup.find( '[name="frequency"]' ),
	//		$modal_id  =$popup.find( '[name="modal_id"]' ),
	//		_modal_id = Math.random().toString( 36 ).substr( 2, 5 );
	//
	//	if( $frequency.val() == 'onclick' ) {
	//		retrieve_buttons();
	//		$modal_id.val( _modal_id );
	//	} else {
	//		$frequency.on( 'change', function() {
	//			if( $frequency.val() == 'onclick' ) {
	//				retrieve_buttons();
	//				$modal_id.val( _modal_id );
	//			} else {
	//				$modal_id.val( '' );
	//			}
	//		});
	//	}
	//
	//	vc.edit_element_block_view.on( 'save', function() { link_button( $popup, _modal_id ); } );
	//} );
})( jQuery );
