/**
 * Start advanced progress bar widget script
 */

(function($, elementor) {
    'use strict';
    // AdvancedProgressBar
    var widgetAdvancedProgressBar = function($scope, $) {
        var $advancedProgressBar = $scope.find('.bdt-ep-advanced-progress-bar-item');
        if (!$advancedProgressBar.length) {
            return;
        }
                    
 
        elementorFrontend.waypoint($advancedProgressBar, function() {
            var $this = $(this);
 
            //.bdt-progress-item .bdt-progress-fill
            var bar = $(this).find(" .bdt-ep-advanced-progress-bar-fill"),
                barPos,
                windowBtm = $(window).scrollTop() + $(window).height();
            bar.each(function() {
                barPos = $(this).offset().top;

                // if (barPos <= windowBtm) {
                    $(this).css("width", function() {
                         var thisMaxVal = $(this).attr("data-max-value");
                         var thisFillVal = $(this).attr("data-width").slice(0, -1); 
                         var formula = (thisFillVal*100) / thisMaxVal;
                         // console.log(formula);
                        // return $(this).attr("data-width");
                        return formula+'%';
                    });
                    $(this).children(".bdt-ep-advanced-progress-bar-parcentage").css({
                        '-webkit-transform': 'scale(1)',
                        '-moz-transform': 'scale(1)',
                        '-ms-transform': 'scale(1)',
                        '-o-transform': 'scale(1)',
                        'transform': 'scale(1)'
                    });
                // }
            });
        }, {
            offset: '90%'
        });
 
    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-progress-bar.default', widgetAdvancedProgressBar);
    });
}(jQuery, window.elementorFrontend)); 

/**
 * End advanced progress bar widget script
 */

