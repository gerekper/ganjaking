/**
 * Start vertical menu widget script
 */

(function ($, elementor) {
    'use strict';
    // Horizontal Menu
    var widgetSlinkyVerticalMenu = function ($scope, $) {
        var $vrMenu = $scope.find('.bdt-slinky-vertical-menu');
        var $settings = $vrMenu.attr('id');
        if (!$vrMenu.length) {
            return;
        }
        const slinky = $('#'+$settings).slinky();
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-slinky-vertical-menu.default', widgetSlinkyVerticalMenu);
    });

}(jQuery, window.elementorFrontend));

/**
 * End vertical menu widget script
 */

