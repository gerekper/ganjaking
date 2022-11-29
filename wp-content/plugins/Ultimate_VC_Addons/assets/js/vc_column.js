( function ( jQuery ) {
	jQuery.fn.ultimate_column_shift = function ( option ) {
		jQuery( this ).each( function () {
			const br_rad = jQuery( this ).data( 'border-radius' );
			const txt_color = jQuery( this ).data( 'txt-color' );
			const bg_col = jQuery( this ).data( 'bg-color' );
			const br_style = jQuery( this ).data( 'br-style' );
			const br_width = jQuery( this ).data( 'br-width' );
			const br_color = jQuery( this ).data( 'br-color' );
			const cl_pad = jQuery( this ).data( 'cl-pad' );
			const cl_margin = jQuery( this ).data( 'cl-margin' );
			const ani = jQuery( this ).data( 'animation' );
			const ani_delay = jQuery( this ).data( 'animation-delay' );
			console.log( ani + ' ' + ani_delay );
			//console.log('rad'+br_rad+'sty'+br_style+' width'+br_width+' col'+bg_col+' clpad'+cl_pad+' cl-ma'+cl_margin+' brcol'+br_color);
			jQuery( this )
				.prev()
				.css( {
					'border-radius': br_rad,
					'background-color': bg_col,
					padding: cl_pad,
					margin: cl_margin,
					'border-style': br_style,
					'border-width': br_width + 'px',
					'border-color': br_color,
					color: txt_color,
				} );
			jQuery( this ).prev().attr( 'data-animation', ani );
			jQuery( this ).prev().attr( 'data-animation-delay', ani_delay );
			jQuery( this ).remove();
		} );
		return this;
	};
} )( jQuery );
jQuery( document ).ready( function () {
	jQuery( '.ult-column-param' ).ultimate_column_shift();
} );
