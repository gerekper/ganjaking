/**
 * Add pin button to woocommerce product gallery
 */
jQuery(function($){

	if (typeof wooPinterestConfig === 'undefined') {
		return;
	}

	$('.woocommerce-product-gallery').each(function () {
		var gallery = $(this);

		var fx = gallery.data('flexslider') || {};

		if (typeof fx.vars === 'undefined' || typeof fx.vars.after === 'undefined') {
			return;
		}

		var afterCallback = fx.vars.after;

		fx.vars.after = function( slider ){

			if (typeof afterCallback === "function") {
				afterCallback(slider);
			}

			var currentSlide = slider.slides[slider.currentSlide];
			var buttonUrl    = 'https://www.pinterest.com/pin/create/button/';
			var pinterestBtn = $('.product-gallery-pin-btn').find('[class*="_button_pin"]');

			var media       = $(currentSlide).find('a').attr('href');
			var description = $('title').text();
			var url         = wooPinterestConfig.productUrl;

			var pinUrl = buttonUrl + '?media=' + media + '&url=' + url + '&description=' + description;

			pinterestBtn.attr('href', pinUrl);
			pinterestBtn.attr('data-pin-href', pinUrl);
		};
	});

	var gallery = $('.woocommerce-product-gallery');

	gallery.on('mouseover', function () {

		var pinBtn = $('.product-gallery-pin-btn [class*="_button_pin"]');
		var offset = gallery.offset();
		var indent = 10;

		pinBtn.show();
		pinBtn.css({
			left: offset.left + indent,
			top: offset.top + indent
		})


	});

	gallery.on('mouseout', function (event) {

		if (!$(event.relatedTarget).is('[class*="_button_pin"]')) {
			var pinBtn = $('.product-gallery-pin-btn [class*="_button_pin"]');
			pinBtn.hide();
		}

	});
});
