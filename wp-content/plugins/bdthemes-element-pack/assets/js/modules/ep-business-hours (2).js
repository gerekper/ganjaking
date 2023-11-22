/**
 * Start advanced divider widget script
 */

(function($, elementor) {
    'use strict'; 
    var widgetBusinessHours = function($scope, $) {
        var $businessHoursContainer = $scope.find('.bdt-ep-business-hours'),
        $businessHours = $businessHoursContainer.find('.bdt-ep-business-hours-current-time');
        if (!$businessHoursContainer.length) {
            return;
        }
        var $settings = $businessHoursContainer.data('settings');
        var dynamic_timezone = $settings.dynamic_timezone;
        var timeNotation = $settings.timeNotation;
        var business_hour_style = $settings.business_hour_style;

        if (business_hour_style != 'dynamic') return;

        $(document).ready(function() {
            var offset_val;
            var timeFormat = '%H:%M:%S', timeZoneFormat; 
            var dynamic_timezone = $settings.dynamic_timezone;
            
            if(business_hour_style == 'static'){
                offset_val = $settings.dynamic_timezone_default;
            }else{
                offset_val = dynamic_timezone;
            }

            // console.log(offset_val);
            if(timeNotation == '12h'){
                timeFormat = '%I:%M:%S %p';
            } 
            if (offset_val == '') return;
            var options = {
                // format:'<span class=\"dt\">%A, %d %B %I:%M:%S %P</span>',
                //    format:'<span class=\"dt\">  %I:%M:%S </span>',
                format: timeFormat,
                timeNotation: timeNotation, //'24h',
                am_pm: true,
                utc: true,
                utc_offset: offset_val
            }
            $($businessHoursContainer).find('.bdt-ep-business-hours-current-time').jclock(options);

        });

    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-business-hours.default', widgetBusinessHours);
    });
}(jQuery, window.elementorFrontend));

/**
 * End business hours widget script
 */

