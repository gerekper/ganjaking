/**
 * Start advanced counter widget script
 */

;(function($, elementor) {
    'use strict';
    // AdvancedCounter
    var widgetAdvancedCounter = function($scope, $) {
        var $AdvancedCounter = $scope.find('.bdt-ep-advanced-counter');
        if (!$AdvancedCounter.length) {
            return;
        }

        elementorFrontend.waypoint($AdvancedCounter, function() {

            var $this = $(this);
            var $settings = $this.data('settings');
            // start null checking
            var countStart = $settings.countStart;
            if (typeof countStart === 'undefined' || countStart == null) {
                countStart = 0;
            }
            
            var countNumber = $settings.countNumber;
            if (typeof countNumber === 'undefined' || countNumber == null) {
                countNumber = 0;
            }
            var decimalPlaces = $settings.decimalPlaces;
            if (typeof decimalPlaces === 'undefined' || decimalPlaces == null) {
                decimalPlaces = 0;
            }
            var duration = $settings.duration;
            if (typeof duration === 'undefined' || duration == null) {
                duration = 0;
            }
            var useEasing = $settings.useEasing;
            useEasing = !(typeof useEasing === 'undefined' || useEasing == null);
            var useGrouping = $settings.useGrouping;

            useGrouping = !(typeof useGrouping === 'undefined' || useGrouping == null);

            var counterSeparator = $settings.counterSeparator;
            if (typeof counterSeparator === 'undefined' || counterSeparator == null) {
                counterSeparator = '';
            }
            var decimalSymbol = $settings.decimalSymbol;
            if (typeof decimalSymbol === 'undefined' || decimalSymbol == null) {
                decimalSymbol = '';
            }
            var counterPrefix = $settings.counterPrefix;
            if (typeof counterPrefix === 'undefined' || counterPrefix == null) {
                counterPrefix = '';
            }
            var counterSuffix = $settings.counterSuffix;
            if (typeof counterSuffix === 'undefined' || counterSuffix == null) {
                counterSuffix = '';
            }

            // end null checking


            var options = {  
                startVal: countStart,
                numerals: $settings.language,
                decimalPlaces: decimalPlaces,
                duration: duration,
                useEasing: useEasing,
                useGrouping: useGrouping,
                separator: counterSeparator,
                decimal: decimalSymbol,
                prefix: counterPrefix,
                suffix: counterSuffix,


            };

            var demo = new CountUp($settings.id, countNumber, options);
            if (!demo.error) {
                demo.start();
            } else {
                console.error(demo.error);
            }
            //  start  for count 

        }, {
            offset: 'bottom-in-view'
        });

    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-counter.default', widgetAdvancedCounter);
    });
}(jQuery, window.elementorFrontend));

/**
 * End advanced counter widget script
 */

