/**
 * Start price table widget script
 */

 ( function( $, elementor ) {

	'use strict';

	var widgetImageStack = function( $scope, $ ) {

		var $imageStack = $scope.find( '.bdt-image-stack' );

        if ( ! $imageStack.length ) {
            return;
        }

        var $tooltip = $imageStack.find('.bdt-tippy-tooltip'),
        	widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

    };
    
	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-image-stack.default', widgetImageStack );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End price table widget script
 */