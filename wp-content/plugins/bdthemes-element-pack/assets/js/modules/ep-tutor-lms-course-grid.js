/**
 * Start tutor lms grid widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetTutorLMSGrid = function ($scope, $) {

		var $tutorLMS = $scope.find('.bdt-tutor-lms-course-grid'),
			$settings = $tutorLMS.data('settings');

		if (!$tutorLMS.length) {
			return;
		}

		if ($settings.tiltShow == true) {
			var elements = document.querySelectorAll($settings.id + " .bdt-tutor-course-item");
			VanillaTilt.init(elements);
		}

	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tutor-lms-course-grid.default', widgetTutorLMSGrid);
	});

}(jQuery, window.elementorFrontend));

/**
 * End tutor lms grid widget script
 */
