/**
 * Start marker widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetIconMobileMenu = function( $scope, $ ) {

		var $marker = $scope.find( '.bdt-icon-mobile-menu-wrap' );

        if ( ! $marker.length ) {
            return;
        }

		var $tooltip = $marker.find('ul > li > .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-icon-mobile-menu.default', widgetIconMobileMenu );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End marker widget script
 */

