/**
 * Start circle menu widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetCircleMenu = function( $scope, $ ) {

		var $circleMenu = $scope.find('.bdt-circle-menu'),
            $settings = $circleMenu.data('settings');

        if ( ! $circleMenu.length ) {
            return;
        }

        $($circleMenu[0]).circleMenu({
            direction           : $settings.direction,
            item_diameter       : $settings.item_diameter,
            circle_radius       : $settings.circle_radius,
            speed               : $settings.speed,
            delay               : $settings.delay,
            step_out            : $settings.step_out,
            step_in             : $settings.step_in,
            trigger             : $settings.trigger,
            transition_function : $settings.transition_function
        });

        var $tooltip = $circleMenu.find('.bdt-tippy-tooltip'),
            widgetID = $scope.data('id');

        $tooltip.each(function (index) {
            tippy(this, {
                //appendTo: $scope[0]
                //arrow: false,
                allowHTML: true,
                theme: 'bdt-tippy-' + widgetID
            });
        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-circle-menu.default', widgetCircleMenu );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End circle menu widget script
 */

