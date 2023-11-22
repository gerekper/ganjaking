/**
 * Start image compare widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetImageCompare = function( $scope, $ ) {
        var $image_compare_main = $scope.find('.bdt-image-compare');
        var $image_compare      = $scope.find('.image-compare');
        if ( !$image_compare.length ) {
            return;
        }

        var $settings        = $image_compare.data('settings');
        
        var 
        default_offset_pct   = $settings.default_offset_pct,
        orientation          = $settings.orientation,
        before_label         = $settings.before_label,
        after_label          = $settings.after_label,
        no_overlay           = $settings.no_overlay,
        on_hover             = $settings.on_hover,
        add_circle_blur      = $settings.add_circle_blur,
        add_circle_shadow    = $settings.add_circle_shadow,
        add_circle           = $settings.add_circle,
        smoothing            = $settings.smoothing,
        smoothing_amount     = $settings.smoothing_amount,
        bar_color            = $settings.bar_color,
        move_slider_on_hover = $settings.move_slider_on_hover;
      
        var viewers = document.querySelectorAll('#' + $settings.id);
  
        var options = {

            // UI Theme Defaults
            controlColor : bar_color,
            controlShadow: add_circle_shadow,
            addCircle    : add_circle,
            addCircleBlur: add_circle_blur,
          
            // Label Defaults
            showLabels   : no_overlay,
            labelOptions : {
              before       : before_label,
              after        : after_label,
              onHover      : on_hover
            },
          
            // Smoothing
            smoothing      : smoothing,
            smoothingAmount: smoothing_amount,
          
            // Other options
            hoverStart     : move_slider_on_hover,
            verticalMode   : orientation,
            startingPoint  : default_offset_pct,
            fluidMode      : false
          };

          viewers.forEach(function (element){
            var view = new ImageCompare(element, options).mount();
          });

	};

	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-image-compare.default', widgetImageCompare );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End image compare widget script
 */

