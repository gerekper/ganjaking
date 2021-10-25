/*!
 * Gallery Packery JS
 *
 * Copyright 2017, GT3 Theme
 */
jQuery(function ($) {
	var gt3_gallery = $('.gt3pg_photo_gallery');
	var array_type = new Array('packery');

	function gt3pg_resize_image_packery() {

		if (gt3_gallery.length) {
			gt3_gallery.each(function () {

				var t = $(this);

				if ($.inArray($(this).data('type'), array_type) === -1) {
					return true;
				}
				var pad = t.data('margin');
				var data = t.data('packery_grid');
				var wrap_width_origin, index, parent, width, height, wrap_width, wrap_height;

				wrap_width_origin = Math.floor(t.innerWidth() / data.grid);

				t.find('.gt3pg_img_wrap').each(function (key, value) {
					height = width = wrap_width_origin - pad;
					wrap_height = wrap_width = wrap_width_origin;
					parent = $(this).closest('.gt3pg_element');
					index = key % data.lap + 1;
					if (index in data.elem) {
						if ('w' in data.elem[index]) {
							width = wrap_width_origin * data.elem[index].w - pad;
							wrap_width = wrap_width_origin * data.elem[index].w;
						}
						if ('h' in data.elem[index]) {
							height = wrap_width_origin * data.elem[index].h - pad;
							wrap_height = wrap_width_origin * data.elem[index].h;
						}
					}

					$(this).css('height', height).css('width', width);
					parent.css('height', wrap_height).css('width', wrap_width);

					var img = $(this).find('img');
					if (img.width() < $(this).innerWidth()) {
						$(this).removeClass('landscape')
					} else if (img.height() < $(this).innerHeight()) {
						$(this).addClass('landscape')
					}

				});

				jQuery(this).packery({
					itemSelector: '.gt3pg_element'
				});
			});


		}


	}

	gt3pg_resize_image_packery();

	$(window).on('resize', function () {
		gt3pg_resize_image_packery();
	})
});

