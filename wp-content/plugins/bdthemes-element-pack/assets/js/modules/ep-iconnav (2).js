/**
 * Start iconnav widget script
 */

( function( $, elementor ) {

	'use strict'; 

	var widgetIconNav = function( $scope, $ ) {

		var $iconnav        = $scope.find( 'div.bdt-icon-nav' ),
            $iconnavTooltip = $iconnav.find( '.bdt-icon-nav' );

        if ( ! $iconnav.length ) {
            return;
        }

		var $tooltip = $iconnavTooltip.find('> .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-iconnav.default', widgetIconNav );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End iconnav widget script
 */

