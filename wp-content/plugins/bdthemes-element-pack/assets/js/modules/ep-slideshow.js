/**
 * Start slideshow widget script
 */

(function($, elementor) {

    'use strict';

    var widgetSlideshow = function($scope, $) {

        var $slideshow = $scope.find( '.bdt-slideshow' ),
            $thumbNav  = $($slideshow).find('.bdt-thumbnav-wrapper > .bdt-thumbnav-scroller');

        if ( ! $slideshow.length ) {
            return;
        }

        $($thumbNav).mThumbnailScroller({
            axis: 'yx',
            type: 'hover-precise'
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-slideshow.default', widgetSlideshow);
    });

}(jQuery, window.elementorFrontend));

/**
 * End slideshow widget script
 */

