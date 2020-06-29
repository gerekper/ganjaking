/*----------------------------------------------------------------------------*\
 ICON LIST SHORTCODE - Panel
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_icon_list' ) {
			return '';
		}

		var $icon_type = $popup.find( '[name="mpc_icon__icon_type"]' ),
			$list_group = $popup.find( '[data-vc-shortcode-param-name="list"]' ),
			$group_toggle = $list_group.find( '.column_toggle' ),
			$group_add = $list_group.find( '.vc_param_group-add_content' ),
			$group_duplicate = $list_group.find( '.column_clone' );

		function icon_dependency( $this ) {
			var _type = $this.val();

			$list_group.find( '[name="list_icon_type"]' ).val( _type ).trigger( 'change' );
		}

		$icon_type.on( 'change', function() {
			icon_dependency( $( this ) );
		} );

		$group_add.on( 'click', function() {
			setTimeout( function(){
				icon_dependency( $icon_type );
			}, 250 );
		} );
		$group_duplicate.on( 'click', function() {
			icon_dependency( $icon_type );
		} );

		// Triggers
		setTimeout( function() {
			icon_dependency( $icon_type );
			$group_toggle.first().trigger( 'click' );
		}, 250 );
	} );
})( jQuery );
