/**
 * Start offcanvas widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetOffcanvas = function( $scope, $ ) {

		var $offcanvas = $scope.find( '.bdt-offcanvas' );
			
        if ( ! $offcanvas.length ) {
            return;
        }


        $.each($offcanvas, function(index, val) {
            
            var $this   	= $(this),
                $settings   = $this.data('settings'),
                offcanvasID = $settings.id;
            
            if ( $(offcanvasID).length ) {
                // global custom link for a tag
                $(offcanvasID).on('click', function(event){
                    event.preventDefault();       
                    bdtUIkit.offcanvas( $this ).show();
                });
            }

        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-offcanvas.default', widgetOffcanvas );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End offcanvas widget script
 */

