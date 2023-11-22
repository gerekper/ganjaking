/**
 * Start progress pie widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetProgressPie = function( $scope, $ ) {

		var $progressPie = $scope.find( '.bdt-progress-pie' );

        if ( ! $progressPie.length ) {
            return;
        }

        elementorFrontend.waypoint( $progressPie, function() {
            var $this = $( this );
            
                $this.asPieProgress({
                    namespace: 'pieProgress',
                    classes: {
                        svg     : 'bdt-progress-pie-svg',
                        number  : 'bdt-progress-pie-number',
                        content : 'bdt-progress-pie-content'
                    }
                });
                
                $this.asPieProgress('start');

        }, {
            offset: 'bottom-in-view'
        } );

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-progress-pie.default', widgetProgressPie );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End progress pie widget script
 */

