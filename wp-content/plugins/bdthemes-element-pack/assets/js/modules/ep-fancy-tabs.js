/**
 * Start fancy tabs widget script
 */

(function($, elementor) {

    'use strict';

    var widgetFancyTabs = function($scope, $) {


        var $fancyTabs = $scope.find('.bdt-ep-fancy-tabs'),
            $settings = $fancyTabs.data('settings');

        var iconBx = document.querySelectorAll('#' + $settings.tabs_id + ' .bdt-ep-fancy-tabs-item');
        var contentBx = document.querySelectorAll('#' + $settings.tabs_id + ' .bdt-ep-fancy-tabs-content');

        for (var i = 0; i < iconBx.length; i++) {
            iconBx[i].addEventListener($settings.mouse_event, function() {
                for (var i = 0; i < contentBx.length; i++) {
                    contentBx[i].className = 'bdt-ep-fancy-tabs-content';
                }
                document.getElementById(this.dataset.id).className = 'bdt-ep-fancy-tabs-content active';

                for (var i = 0; i < iconBx.length; i++) {
                    iconBx[i].className = 'bdt-ep-fancy-tabs-item';
                }
                this.className = 'bdt-ep-fancy-tabs-item active';

            });
        }

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-fancy-tabs.default', widgetFancyTabs);
    });

}(jQuery, window.elementorFrontend));

/**
 * End fancy tabs widget script
 */

