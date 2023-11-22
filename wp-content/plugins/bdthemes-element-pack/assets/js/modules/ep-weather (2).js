/**
 * Start weather widget script
 */
 
(function($, elementor) {
    'use strict';
    var widgetWeather = function($scope, $) {
        var $weatherContainer = $scope.find('.bdt-weather');
        if (!$weatherContainer.length) {
            return;
        }
        var $settings = $weatherContainer.data('settings');
        var $weather = $($settings.id).closest('.elementor-widget-container');

        if ($settings.dynamicBG !== false){
            $($weather).css('background-image', 'url(' + $settings.url + ')');
            $($weather).css({
                'background-size': 'cover',
                'background-position': 'center center',
                'background-repeat': 'no-repeat'
            });
            
        }
        

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-weather.default', widgetWeather);
    });

}(jQuery, window.elementorFrontend));

/**
 * End weather widget script
 */