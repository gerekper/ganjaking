/**
 * Start switcher widget script
 */

(function ($, elementor) {

	'use strict';

	var sectionSwitcher = function ($scope, $) {
		var $switcher = $scope.find('.bdt-switchers'),
			$settings = $switcher.data('settings'),
			$activatorSettings = $switcher.data('activator'),
			$settingsLinkWidget = $switcher.data('bdt-link-widget'),
			editMode = Boolean(elementorFrontend.isEditMode());


		if ($activatorSettings !== undefined) {
			// for A
			bdtUIkit.util.on($activatorSettings.switchA, "click", function () {
				bdtUIkit.switcher('#bdt-switcher-activator-' + $activatorSettings.id).show(0);
				bdtUIkit.switcher('#bdt-switcher-' + $activatorSettings.id).show(0);
			});
			// for B
			bdtUIkit.util.on($activatorSettings.switchB, "click", function () {
				bdtUIkit.switcher('#bdt-switcher-activator-' + $activatorSettings.id).show(1);
				bdtUIkit.switcher('#bdt-switcher-' + $activatorSettings.id).show(1);
			});

		}


		// if ( $settings === undefined || editMode ) {
		// 	return;
		// }

		if ($settings !== undefined && editMode === false) {
			var $switchAContainer = $switcher.find('.bdt-switcher > div > div > .bdt-switcher-item-a'),
				$switchBContainer = $switcher.find('.bdt-switcher > div > div > .bdt-switcher-item-b'),
				$switcherContentA = $('.elementor').find('.elementor-section' + '#' + $settings['switch-a-content']),
				$switcherContentB = $('.elementor').find('.elementor-section' + '#' + $settings['switch-b-content']);

			if ($settings.positionUnchanged !== true) {
				if ($switchAContainer.length && $switcherContentA.length) {
					$($switcherContentA).appendTo($switchAContainer);
				}

				if ($switchBContainer.length && $switcherContentB.length) {
					$($switcherContentB).appendTo($switchBContainer);
				}
			}

			if ($settings.positionUnchanged == true) {
				$('#bdt-tabs-' + $settings.id).find('.bdt-switcher').remove();

				var $switcherContentAAA = $('#' + $settings['switch-a-content']);
				var $switcherContentBBB = $('#' + $settings['switch-b-content']);

				$('#' + $settings['switch-a-content']).parent().append(`<div id="bdt-switcher-${$settings.id}" class="bdt-switcher bdt-switcher-item-content" style="width:100%;"></div>`);

				$($switcherContentAAA).appendTo($('#bdt-switcher-' + $settings.id));
				$($switcherContentBBB).appendTo($('#bdt-switcher-' + $settings.id));
				
				var $activeA, $activeB = '';
				if ($settings.defaultActive == 'a'){
						$activeA, $activeA = 'bdt-active';
				}else{
					$activeB = 'bdt-active';
				}

				$('#' + $settings['switch-a-content']).wrapAll('<div class="bdt-switcher-item-content-inner ' + $activeA + '"></div>');
				$('#' + $settings['switch-b-content']).wrapAll('<div class="bdt-switcher-item-content-inner ' + $activeB + '"></div>');
			}
		}
 

		if ($settingsLinkWidget !== undefined && editMode === false) {
			var $targetA = $($settingsLinkWidget.linkWidgetTargetA),
				$targetB = $($settingsLinkWidget.linkWidgetTargetB),
				$switcher = '#bdt-switcher-' + $settingsLinkWidget.id;

			$targetA.css({
				'opacity': 1,
				'display': 'block',
				'grid-row-start': 1,
				'grid-column-start': 1
			});

			$targetA.parent().css({
				'display': 'grid'
			});

			$targetB.css({
				'opacity': 0,
				'display': 'none',
				'grid-row-start': 1,
				'grid-column-start': 1
			});

			bdtUIkit.util.on($switcher, 'shown', function (e) {
				var index = bdtUIkit.util.index(e.target)
				if (index == 0) {
					$targetA.css({
						'opacity': 1,
						'display': 'block',
					});
					$targetB.css({
						'opacity': 0,
						'display': 'none',
					});
				} else {
					$targetB.css({
						'opacity': 1,
						'display': 'block',
					});
					$targetA.css({
						'opacity': 0,
						'display': 'none',
					});
				}

			})
		}


	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-switcher.default', sectionSwitcher);
	});

}(jQuery, window.elementorFrontend));

/**
 * End switcher widget script
 */