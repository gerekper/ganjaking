jQuery( document ).ready( function () {
	jQuery( '.ult-team.ult-social-icon' ).hover(
		function () {
			const icon_color = jQuery( this ).data( 'iconcolor' );
			const icon_hover = jQuery( this ).data( 'iconhover' );

			if ( icon_hover != 'inherit' ) {
				jQuery( this ).css( 'color', icon_hover );
			} else {
				jQuery( this ).css( 'color', '' );
			}
		},
		function () {
			const icon_color = jQuery( this ).data( 'iconcolor' );
			const icon_hover = jQuery( this ).data( 'iconhover' );

			if ( icon_color != 'inherit' ) {
				jQuery( this ).css( 'color', icon_color );
			} else {
				jQuery( this ).css( 'color', '' );
			}
		}
	);

	jQuery( '.ult-style-2' ).hover(
		function () {
			const self = jQuery( this )
				.find( ' .ult-team-member-image' )
				.first();
			const hover_opacity = self.data( 'hover_opacity' );
			self.children( 'img' ).css( 'opacity', hover_opacity );
		},
		function () {
			const self = jQuery( this )
				.find( ' .ult-team-member-image' )
				.first();
			const opacity = self.data( 'opacity' );
			self.children( 'img' ).css( 'opacity', opacity );
		}
	);

	jQuery( '.ult-style-3' ).hover(
		function () {
			const self = jQuery( this )
				.find( ' .ult-team-member-image' )
				.first();
			const hover_opacity = self.data( 'hover_opacity' );
			self.find( 'img' ).css( 'opacity', hover_opacity );
		},
		function () {
			const self = jQuery( this )
				.find( ' .ult-team-member-image' )
				.first();
			self.find( 'img' ).css( 'opacity', 1 );
		}
	);

	function Ult_ResponsiveStyle() {
		jQuery( '.ult-team-member-wrap' ).each( function ( i ) {
			const r_width = jQuery( this ).data( 'responsive_width' );

			if ( r_width != '' ) {
				if ( jQuery( window ).width() <= r_width ) {
					jQuery( this ).removeClass( 'ult-style-2' );
					jQuery( this ).addClass( 'ult-style-1 ult-responsive' );
				} else if ( jQuery( this ).hasClass( 'ult-responsive' ) ) {
					jQuery( this ).removeClass( 'ult-style-1 ult-responsive' );
					jQuery( this ).addClass( 'ult-style-2' );
				}
			}
		} );
	}

	function set_Top_Description() {
		jQuery( '.ult-team-member-wrap' ).each( function ( i ) {
			const p_height = jQuery( this ).height();
			const child_height = jQuery( this ).find(
				'.ult-team_description_slide'
			);
			const desc_element = child_height; // jQuery(child_height[0]);

			if (
				parseFloat( p_height ) < parseFloat( desc_element.height() )
			) {
				desc_element.addClass( 'ult-desc-set-top' );
			} else if ( desc_element.hasClass( 'ult-desc-set-top' ) ) {
				desc_element.removeClass( 'ult-desc-set-top' );
			}
		} );

		jQuery( '.ult-style-3 .ult-team-member-image' ).each( function ( i ) {
			const p_height = jQuery( this ).height();
			const child_height = jQuery( this ).find(
				'.ult-team-member-description'
			);
			const desc_element = child_height; // jQuery(child_height[0]);

			if (
				parseFloat( p_height ) < parseFloat( desc_element.height() )
			) {
				desc_element.addClass( 'ult-desc-set-top' );
			} else if ( desc_element.hasClass( 'ult-desc-set-top' ) ) {
				desc_element.removeClass( 'ult-desc-set-top' );
			}
		} );
	}
	Ult_ResponsiveStyle();

	setTimeout( set_Top_Description, 500 );

	jQuery( '.ult-team-member-wrap .ult-team-member-image img' ).on(
		'load',
		function () {
			set_Top_Description();
		}
	);

	jQuery( window ).resize( function () {
		Ult_ResponsiveStyle();
		set_Top_Description();
	} );

	jQuery( '.ult-team-member-image-overlay.ult-team_img_hover' ).each(
		function () {
			const bg_color = jQuery( this ).data( 'background_clr' );
			jQuery( this ).css( { 'background-color': bg_color } );
		}
	);
} );
