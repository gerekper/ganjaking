/**
 * Start panel slider widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetPanelSlider = function ($scope, $) {

		var $slider = $scope.find('.bdt-panel-slider');

		if (!$slider.length) {
			return;
		}

		var $sliderContainer = $slider.find('.swiper-carousel'),
			$settings = $slider.data('settings'),
			$widgetSettings = $slider.data('widget-settings');

			const Swiper = elementorFrontend.utils.swiper;
			initSwiper();
			async function initSwiper() {
				var swiper = await new Swiper($sliderContainer, $settings);

				if ($settings.pauseOnHover) {
					$($sliderContainer).hover(function () {
						(this).swiper.autoplay.stop();
					}, function () {
						(this).swiper.autoplay.start();
					});
				}
			};

		if ($widgetSettings.mouseInteractivity == true) {
			var data = $($widgetSettings.id).find('.bdt-panel-slide-item');
			$(data).each((index, element) => {
				var scene = $(element).get(0);
				var parallaxInstance = new Parallax(scene, {
					selector: '.bdt-panel-slide-thumb',
					hoverOnly: true,
					pointerEvents: true
				});
			});
		}

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.default', widgetPanelSlider);
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.bdt-middle', widgetPanelSlider);
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.always-visible', widgetPanelSlider);
	});

}(jQuery, window.elementorFrontend));

/**
 * End panel slider widget script
 */