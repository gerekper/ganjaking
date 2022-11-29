! ( function ( $ ) {
	$( document ).ready( function () {
		$( '.wpb_el_type_ultimate_hotspot_param' ).each( function ( i, p ) {
			const hlink = ULT_H_img_link;
			$img = $( p ).find( '.ult-hotspot-image' );
			$img.attr( 'src', hlink );
			if ( ULT_H_Size == 'main_img_custom' ) {
				$img.css( { width: ULT_H_custom_size + 'px' } );
			}
		} );
		if ( typeof $.fn.draggable !== 'undefined' ) {
			$( '.ult-hotspot-draggable' ).draggable( {
				containment: 'parent',
				create( event, ui ) {
					const current_position = $( this )
						.next( '.ult-hotspot-positions' )
						.val();
					const positions = current_position.split( ',' );
					$( this ).css( { top: positions[ 0 ] + '%' } );
					if ( typeof positions[ 1 ] !== 'undefined' )
						$( this ).css( { left: positions[ 1 ] + '%' } );
				},
				stop( event, ui ) {
					let current_position = '';
					const $img = $( this ).prev( '.ult-hotspot-image' );
					const img_width = $img.width();
					const img_height = $img.height();
					const top = ( ui.position.top / img_height ) * 100;
					const left = ( ui.position.left / img_width ) * 100;
					current_position = top + ',' + left;
					$( this )
						.next( '.ult-hotspot-positions' )
						.val( current_position );
				},
			} );
		}
	} );
} )( jQuery );
