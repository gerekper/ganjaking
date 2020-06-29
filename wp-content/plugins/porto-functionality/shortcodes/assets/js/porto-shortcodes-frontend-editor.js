/**
 * Fix shortcodes styles for Visual Composer Front-end Editor
 * @since 1.5
 */
jQuery(document).ready(function($) {
	'use strict';

	$(document.body).on('porto_init_start', function() {
		// Porto Masonry Container
		$('.porto-grid-container [data-plugin-masonry]').each(function() {
			// Grid Container
			if (!$(this).hasClass('fe-init') && $(this).data('item-grid')) {
				var item_classes = $(this).data('item-grid').split(',');
				$(this).addClass('fe-init').children('.vc_porto_grid_item').each(function(index) {
					if (typeof item_classes[index % item_classes.length] != 'undefined') {
						var current_classes = $(this).attr('class').split(' '), new_classes = item_classes[index % item_classes.length];
						for (var i = 0; i < current_classes.length; i++) {
							var c = $.trim(current_classes[i]);
							if (c && c.indexOf('grid-') === -1) {
								new_classes += ' ' + c;
							}
						}
						$(this).attr('class', new_classes);
					}
				});
			}

			// Grid Item
			if ($(this).find('.vc_porto_grid_item:not(.fe-init)').length) {
				var $item_to_init = $(this).find('.vc_porto_grid_item:not(.fe-init)');
				$item_to_init.each(function() {
					if (!($(this).get(0) instanceof HTMLElement)) {
						Object.setPrototypeOf($(this).get(0), HTMLDivElement.prototype);
					}
					var classes = $(this).children('.porto-grid-item').attr('class'),
						widthAttr = $(this).children('.porto-grid-item').attr('style');
					classes = $.trim(classes.replace('porto-grid-item', '').replace('vc_element-container', '').replace('ui-sortable', ''));
					if (classes) {
						$(this).addClass(classes).addClass('fe-init').children('.porto-grid-item').removeClass(classes);
					}
					if (widthAttr) {
						$(this).attr('style', widthAttr).addClass('fe-init').children('.porto-grid-item').css('width', '');
					}
				});
				if ($(this).data('isotope')) {
					$(this).removeData('__masonry');
					$(this).isotope('destroy');
				}
			}
		});

		// Porto Interactive Banner Layer
		$('.porto-ibanner-layer:not(.fe-init)').each(function() {
			if (!$(this).parent().hasClass('vc_porto_interactive_banner_layer')) {
				$(this).addClass('fe-init');
				return;
			}
			$(this).parent().attr('style', $(this).attr('style')).css('position', 'absolute');
			$(this).removeAttr('style').css('position', 'relative').addClass('fe-init');
		});
	});

	$(document.body).on('mouseup touchend', '.vc_porto_grid_item .vc_control-btn-delete', function(e) {
		$(window).trigger('resize');
	});
});
