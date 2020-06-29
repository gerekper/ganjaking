jQuery(document).ready(function($) {
	'use strict';

	$('body').on('tabsbeforeactivate', '.wpb_tour_tabs_wrapper', function(e, ui) {
		ui.oldTab.removeClass('active');
		ui.newTab.addClass('active');
	});

	$('.compose-mode .vc_controls-bc .vc_control-btn-append').each(function() {
		$(this).insertAfter($(this).closest('.vc_controls').find('.vc_control-btn-prepend'));
	});

	if (window.parent.vc && window.parent.vc.events) {
		window.parent.vc.events.on('shortcodes:add', function(model) {
			var parent_id = model.attributes.parent_id;
			if (!parent_id) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get(parent_id);
			if (parent && 'porto_carousel' == parent.attributes.shortcode) {
				var $obj = $('[data-model-id="' + parent.attributes.id + '"]').children('.owl-carousel');
				if ($obj.length) {
					$obj.removeData('__carousel');
					$obj.trigger('destroy.owl.carousel');
				}
			}
		});

		window.parent.vc.events.on('shortcodeView:destroy', function(model) {
			var parent_id = model.attributes.parent_id;
			if (!parent_id) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get(parent_id);
			if (parent && 'porto_carousel' == parent.attributes.shortcode) {
				var $obj = $('[data-model-id="' + parent.attributes.id + '"]').children('.owl-carousel');
				if ($obj.length) {
					$obj.removeData('__carousel');
					$obj.trigger('destroy.owl.carousel');
					$obj.children('.owl-item:empty').remove();
					$obj.themeCarousel($obj.data('plugin-options'));
				}
			}
		});
	}
});