dce = {
    addCssForBackground: function( element ) {
		// Background Color
		backgroundColor = jQuery(element).data( "dce-background-color" );
		this.addCssForBackgroundColor( element, backgroundColor );

		backgroundColor = jQuery(element).data( "dce-background-hover-color" );
		this.addCssForBackgroundColor( element, backgroundColor );

		backgroundColor = jQuery(element).data( "dce-background-overlay-color" );
		this.addCssForBackgroundColor( element, backgroundColor );

		backgroundColor = jQuery(element).data( "dce-background-overlay-hover-color" );
		this.addCssForBackgroundColor( element, backgroundColor );

		// Background Image URL
		backgroundUrl = jQuery(element).data( "dce-background-image-url" );
		this.addCssForBackgroundImage( element, backgroundUrl );

		backgroundUrl = jQuery(element).data( "dce-background-hover-image-url" );
		this.addCssForBackgroundImage( element, backgroundUrl );

		backgroundUrl = jQuery(element).data( "dce-background-overlay-image-url" );
		this.addCssForBackgroundImage( element, backgroundUrl );

		backgroundUrl = jQuery(element).data( "dce-background-overlay-hover-image-url" );
		this.addCssForBackgroundImage( element, backgroundUrl );

		// Background Color on Advanced
		backgroundColor = jQuery(element).data( "dce-advanced-background-color" );
		this.addCssForBackgroundColor( element, backgroundColor );

		backgroundColor = jQuery(element).data( "dce-advanced-background-hover-color" );
		this.addCssForBackgroundColor( element, backgroundColor );

		backgroundColor = jQuery(element).data( "dce-advanced-background-overlay-color" );
		this.addCssForBackgroundColor( element, backgroundColor );

		backgroundColor = jQuery(element).data( "dce-advanced-background-overlay-hover-color" );
		this.addCssForBackgroundColor( element, backgroundColor );

		// Background Image URL on Advanced
		backgroundUrl = jQuery(element).data( "dce-advanced-background-image-url" );
		this.addCssForBackgroundImage( element, backgroundUrl );

		backgroundUrl = jQuery(element).data( "dce-advanced-background-hover-image-url" );
		this.addCssForBackgroundImage( element, backgroundUrl );

		backgroundUrl = jQuery(element).data( "dce-advanced-background-overlay-image-url" );
		this.addCssForBackgroundImage( element, backgroundUrl );

		backgroundUrl = jQuery(element).data( "dce-advanced-background-overlay-hover-image-url" );
		this.addCssForBackgroundImage( element, backgroundUrl );
    },
    addCssForBackgroundImage: function( element, value ) {
		if( value ) {
			if( jQuery(element).hasClass( "elementor-section") ) {
				// Section
				jQuery(element).css('background-image', 'url(' + value + ')');
			} else if ( jQuery(element).hasClass( "elementor-column") ) {
				// Column
				if( jQuery(element).find('.elementor-column-wrap').length ) {
					jQuery(element).find('.elementor-column-wrap').first().css('background-image', 'url(' + value + ')');
				} else {
					jQuery(element).find('.elementor-widget-wrap').first().css('background-image', 'url(' + value + ')');
				}
			} else if( jQuery(element).hasClass( "e-container") || jQuery(element).hasClass( "e-con") ) {
				// Flex Container
				jQuery(element).css('background-image', 'url(' + value + ')');
			}
		}
    },
    addCssForBackgroundColor: function( element, value ) {
		if( value ) {
			if( jQuery(element).hasClass( "elementor-section") ) {
				// section
				jQuery(element).css('background-color', value );
			} else if ( jQuery(element).hasClass( "elementor-column") ) {
				// Column
				if( jQuery(element).find('.elementor-column-wrap').length ) {
					jQuery(element).find('.elementor-column-wrap').first().css('background-color', value );
				} else {
					// Widget
					jQuery(element).find('.elementor-widget-wrap').first().css('background-color', value );
				}
			} else if( jQuery(element).hasClass( "elementor-widget-container") || jQuery(element).hasClass( "elementor-widget") ) {
				// Widget
				jQuery(element).find('.elementor-widget-container').first().css('background-color', value );
			} else if( jQuery(element).hasClass( "e-container") || jQuery(element).hasClass( "e-con") ) {
				// Flex Container
				jQuery(element).css('background-color', value );
			}
		}
    }
};
