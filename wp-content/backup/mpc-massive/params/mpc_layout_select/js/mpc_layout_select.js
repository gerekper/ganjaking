/*----------------------------------------------------------------------------*\
 MPC LAYOUT SELECT PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
    "use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		$popup.find( '.vc_wrapper-param-type-mpc_layout_select' ).each( function() {
			var $layout_select      = $( this ),
			    $layout_items       = $layout_select.find( '.mpc-layout-item' ),
			    $layout_value       = $layout_select.find( '.mpc_layout_select_field' );

			$layout_items.on( 'click', function() {
				var $active_item = $layout_select.find( '.mpc-layout-item[data-checked="true"]' );

				$active_item.removeAttr( 'data-checked' );

				$( this ).attr( 'data-checked', true );

				$layout_value
					.val( $( this ).attr( 'data-value' ) )
					.trigger( 'change' );
			} );

			$layout_value.on( 'mpc.change', function() {
				$layout_items.filter( '[data-value="' + $layout_value.val() + '"]' ).trigger( 'click' );
			} );
		});
	});

} )( jQuery );
