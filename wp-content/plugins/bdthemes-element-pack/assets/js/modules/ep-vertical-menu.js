/**
 * Start vertical menu widget script
 */

(function ($, elementor) {
    'use strict';
    // Vertical Menu
    var widgetVerticalMenu = function ($scope, $) {
        var $vrMenu = $scope.find('.bdt-vertical-menu');
        var $settings = $vrMenu.data('settings');
        if (!$vrMenu.length) {
            return;
        }

        //.attr("onclick", "event.stopPropagation();");
        $('#' + $settings.id).metisMenu();


        if ('yes' == $settings.removeParentLink) {
            $($vrMenu).find('.has-arrow').attr('href', 'javascript:void(0);')
            // $($vrMenu).find('.has-arrow').on('click', function () {
            //     return false;
            // });
        }
    }
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-vertical-menu.default', widgetVerticalMenu);
    });


}(jQuery, window.elementorFrontend));

/**
 * End vertical menu widget script
 */