/**
 * Start Flip Box widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetFlipBox = function ($scope, $) {
        var $flipBox = $scope.find('.bdt-flip-box'),
            $settings = $flipBox.data('settings');
        if (!$flipBox.length) {
            return;
        }

        if ('click' === $settings.flipTrigger) {
            $($flipBox).on('click', function () {
                $(this).toggleClass('bdt-active');
            });
        }
        if ('hover' === $settings.flipTrigger) {
            $($flipBox).on('mouseenter', function () {
                $(this).addClass('bdt-active');
            });
            $($flipBox).on('mouseleave', function () {
                $(this).removeClass('bdt-active');
            });
        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-flip-box.default', widgetFlipBox);
    });
}(jQuery, window.elementorFrontend));

/**
 * End Flip Box widget script
 */