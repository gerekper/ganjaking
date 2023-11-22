/**
 * Start section sticky widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetSectionSticky = function( $scope, $ ) {

        var $section   = $scope;

        //sticky fixes for inner section.
        jQuery($section).each(function( index ) {
            var $sticky      = jQuery(this),
                $stickyFound = $sticky.find('.elementor-inner-section.bdt-sticky');

            if ($stickyFound.length) {
                jQuery($stickyFound).wrap('<div class="bdt-sticky-wrapper"></div>');
            }
        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/section', widgetSectionSticky );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End section sticky widget script
 */

