/**
 * Start helpdesk widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetHelpDesk = function( $scope, $ ) {

		var $helpdesk = $scope.find( '.bdt-helpdesk' ),
            $helpdeskTooltip = $helpdesk.find('.bdt-helpdesk-icons');

        if ( ! $helpdesk.length ) {
            return;
        }
		
		var $tooltip = $helpdeskTooltip.find('> .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-helpdesk.default', widgetHelpDesk );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End helpdesk widget script
 */
 