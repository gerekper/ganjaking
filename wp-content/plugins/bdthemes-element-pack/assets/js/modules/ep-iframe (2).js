/**
 * Start iframe widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetIframe = function ($scope, $) {

        var $iframe = $scope.find('.bdt-iframe > iframe'),
            $autoHeight = $iframe.data('auto_height');

        if (!$iframe.length) {
            return;
        }

        // Auto height only works when cross origin properly set

        $($iframe).recliner({
            throttle: $iframe.data('throttle'),
            threshold: $iframe.data('threshold'),
            live: $iframe.data('live')
        });

        if ($autoHeight) {
            $(document).on('lazyshow', $iframe, function () {
                var height = jQuery($iframe).contents().find('html').height();
                jQuery($iframe).height(height);
            });
        }
    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-iframe.default', widgetIframe);
    });

}(jQuery, window.elementorFrontend));

/**
 * End iframe widget script
 */
