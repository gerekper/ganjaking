/**
 * Start video gallery widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetVideoGallery = function ($scope, $) {

		var $video_gallery = $scope.find('.rvs-container');

		if (!$video_gallery.length) {
			return;
		}

		$($video_gallery).rvslider();

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-video-gallery.default', widgetVideoGallery);
	});

}(jQuery, window.elementorFrontend));

/**
 * End video gallery widget script
 */