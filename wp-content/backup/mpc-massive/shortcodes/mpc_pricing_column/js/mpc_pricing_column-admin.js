/*----------------------------------------------------------------------------*\
	PRICING COLUMN - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_pricing_column' ) {
			return;
		}

		var _params         = vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).attributes.params,
			_title_disable  = _params.title_disable,
			_price_disable  = _params.price_disable,
            _button_disable = _params.button_disable;

		$popup.find( '[data-vc-shortcode-param-name="title_disable"] input' ).val( _title_disable ).trigger( 'change' );
		$popup.find( '[data-vc-shortcode-param-name="price_disable"] input' ).val( _price_disable ).trigger( 'change' );
		$popup.find( '[data-vc-shortcode-param-name="button_disable"] input' ).val( _button_disable ).trigger( 'change' );
    });
} )( jQuery );
