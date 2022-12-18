var portoVCCarouselTimer = null
var portoVCCarouselElement = null
function vcCarousel($el, preview = false, owlItem = 0) {
	var $el = $el;
	var owlItem = owlItem;
	var preview = preview;
	if (portoVCCarouselTimer && portoVCCarouselElement == $el) {
		window.cancelAnimationFrame(portoVCCarouselTimer)
		portoVCCarouselTimer = null
	}
	portoVCCarouselElement = $el
	portoVCCarouselTimer = window.requestAnimationFrame(function () {
		let options = JSON.parse($el.attr("data-plugin-options"))
		if (preview == true) {
			options.loop = false
		}
		$el.themeCarousel(options)
		$el.trigger('to.owl.carousel', [owlItem, 0])
	});
}
(function ($) {
	window.vcv.on('ready', function (action, id) {
		if (action !== 'merge') {
			var $el = id ? $('#el-' + id).closest('.vc-porto-carousel-wrapper').find('.owl-carousel') : undefined;
			if ($el && $el.length > 0) {
				if ($el.find('.owl-item:not(.cloned)').length != $el.attr('data-haschildren')) return;
				if ($el.children('owl-stage-outer')) {
					$el.find('.owl-stage').css({ 'transform': '', 'width': '', 'height': '', 'max-height': '' }).off('.owl.core')
					$(document).off('.owl.core');
					$el.off('.owl.core');
					$el.find('.owl-nav, .owl-dots, .owl-item.cloned').remove();
					$el.siblings('.owl-nav, .owl-dots').remove();
					$el.removeClass('owl-drag owl-grab');
					$el.removeData('__carousel');
					$el.removeData('owl.carousel');
				}
				let $itemDiv = $el.find('.owl-item.active:not(.cloned)')
				let owlItem =  $itemDiv.length > 0 ? $itemDiv.eq(0).index() : 0
				vcCarousel($el, true, owlItem && owlItem > -1 ? owlItem : 0);
			}
		}
	});
})(window.jQuery);