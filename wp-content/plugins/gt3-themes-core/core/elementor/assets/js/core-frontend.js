;'use strict';

(function (factory) {
	window.gt3Elementor = window.gt3Elementor || {};
	window.gt3Elementor.CoreFrontend = window.gt3Elementor.CoreFrontend || factory(window.jQuery);
})(function ($) {


	function CoreFrontend() {
		if (!this || this.widgets !== CoreFrontend.prototype.widgets) {
			return new CoreFrontend()
		}

		this.initialize();
	}

	$.extend(CoreFrontend.prototype, {
		widgets: {
					'gt3-core-gallerypackery': 'GalleryPackery',
		},
		body: $('body'),
		html: $('html'),
		window: $(window),
		footer: $('footer'),
		editMode: false,
		array_chunk: function (input, size) {
			if (Array.isArray(input)) {
				for (var x, i = 0, c = -1, l = input.length, n = []; i < l; i++) {
					(x = i % size) ? n[c][x] = input[i] : n[++c] = [input[i]];
				}
				return n;
			}
			return input;
		},
		initialize: function () {

			var that = this;
			jQuery('.project_wrapper .img img').each(function(){
				if (!jQuery(jQuery(this).parents('.elementor-column')[0]).hasClass('elementor-col-100')) {

					var image_width = jQuery(this).width();
					var lazyImage = jQuery(this)[0];
					var srcset = lazyImage.dataset.srcset

					var array_srcset = srcset.split(',');
					var srcset_by_size = [];
					array_srcset.forEach(function(item, i, arr) {
						var item_array = item.trim().split(' ');
						array_srcset[i] = item_array;

						var srcset_width = parseInt(item_array[1],10);

						if ((image_width < srcset_width) && (srcset_width >= 600)) {
							srcset_by_size[parseInt(item_array[1],10)] = item_array[0];
						}
					})
					var first_key = Object.keys(srcset_by_size)[0];
					var smallest_image = srcset_by_size[first_key];

					jQuery(lazyImage).attr('src',smallest_image)
					jQuery(lazyImage).removeAttr('data-src').removeAttr('data-srcset').removeAttr('data-sizes')

					jQuery(lazyImage).removeClass("gt3_lazyload");
	        		jQuery(lazyImage).addClass("gt3_lazyload_loaded");
	        		jQuery( lazyImage ).parents('.isotope_item.lazy_loading').removeClass('lazy_loading').addClass('lazy_loaded')

				}
			})

			if (typeof window.elementorFrontend !== 'undefined') {
				$.each(this.widgets, function (name, callback) {
					window.elementorFrontend.hooks.addAction('frontend/element_ready/' + name + '.default', that[callback].bind(that));
				})
			}
			if (typeof elementorFrontend !== 'undefined') {
				this.editMode = elementorFrontend.config.isEditMode || (elementorFrontend.isEditMode && elementorFrontend.isEditMode());
			}

			$( 'body:not(.elementor-editor-active) .gt3_column_link-elementor' ).each(function(){
				var element = jQuery(this);
				var element_url = element.attr('data-column-clickable-url');
				var element_link_blank = element.attr('data-column-clickable-blank')

				element.find('.elementor-column-wrap').on('click',function(e){
					if (e.target.nodeName != 'A') {
						if (element_link_blank == 'yes') {
							window.open(element_url, '_blank');
						}else{
							window.location.href = element_url;
						}
					}
				})
			})


			if (this.editMode) {
				jQuery(function ($) {
					jQuery(':input');
				});

				window.elementor.on("preview:loaded", function () {
					if (window.elementor.elements === undefined) {
						var timer = setInterval(function () {
							if (window.elementor.elements !== undefined) {
								clearInterval(timer);
								var elementorItemsModels = window.elementor.elements.models;
								elementorItemsModels.forEach(function(item, i, arr){
									that.find_any_element(item.attributes);
								})
							}
						}, 100);
					}
				});
			}
		},
		find_any_element: function (item){
			var that = this;
			if (item.elType == 'column') {

				if (item.settings.attributes.gt3_carousel_back_end === 'yes') {

					var attributes = item.settings.attributes;
					var settings = {};

					settings.items_per_line = attributes.gt3_carousel_items_per_line;
					settings.item_per_line_mobile = attributes.gt3_carousel_items_per_line_mobile;
					settings.item_per_line_tablet = attributes.gt3_carousel_items_per_line_tablet;
					settings.autoplay = attributes.gt3_carousel_autoplay == 'yes' ? true : false;
					settings.autoplaySpeed = attributes.gt3_carousel_autoplay_time;
					settings.dots = attributes.gt3_carousel_nav == 'dots' ? true : false;
					settings.arrows = attributes.gt3_carousel_nav == 'arrows' ? true : false;
					settings.centerMode = attributes.gt3_carousel_center_mode == 'yes' ? true : false;
					settings.l10n = {};
					settings.l10n.prev = attributes.gt3_carousel_nav_prev ? attributes.gt3_carousel_nav_prev : '';
					settings.l10n.next = attributes.gt3_carousel_nav_next ? attributes.gt3_carousel_nav_next : '';

					if (item.settings.attributes.gt3_carousel === 'yes') {
						if (item.id) {
							jQuery('.elementor-element-'+item.id).addClass('gt3_carousel-elementor');
							jQuery('.elementor-element-'+item.id).attr('data-settings',JSON.stringify(settings));
							jQuery('.elementor-element-'+item.id).addClass('gt3_carousel_items_per_line-'+settings.items_per_line);
							jQuery('.elementor-element-'+item.id).addClass('gt3_carousel_items_per_line_tablet-'+settings.item_per_line_tablet);
							jQuery('.elementor-element-'+item.id).addClass('gt3_carousel_items_per_line_mobile-'+settings.item_per_line_mobile);
						}
					}
					jQuery('.elementor-element-'+item.id).removeClass('gt3_carousel_destroy-elementor');
				}else{
					jQuery('.elementor-element-'+item.id).addClass('gt3_carousel_destroy-elementor');
				}

			}

			if (item.widgetType == 'toggle') {
				if (item.settings.attributes.add_question_marker === 'yes') {
					jQuery('.elementor-element-'+item.id).find('.elementor-tab-title').addClass('add_question_marker').attr('data-question_marker', item.settings.attributes.question_marker);
				}else{
					jQuery('.elementor-element-'+item.id).find('.elementor-tab-title').removeClass('add_question_marker')
				}
			}

			if (item.elements.length) {
				if (item.elements.models.length) {
					item.elements.models.forEach(function(item, i, arr){
						if (item.attributes) {
							that.find_any_element(item.attributes);
						}
					})
				}
			}
		},
		GalleryPackery: function ($scope) {
			var that = this;
			var wrapper = $scope.hasClass('packery_wrapper') ? $scope : jQuery('.packery_wrapper', $scope);
			if (!wrapper.length) {
				console.warn('Packery wrapper not found');
				return;
			}

			var isotope = jQuery('.isotope_wrapper', $scope);

			var query = wrapper.data('settings');
			query.action = 'gt3_core_packery_load_images';


			var	images = this.array_chunk(wrapper.data('images'), query.load_items),
				packery = query.packery,
				wrap_width_origin, index, wrap_width, wrap_height,
				wrap_ratio, img_ratio;

			var paged = 0,
				max_page = images.length,
				lightbox = query.lightbox,
				lightbox_array,
				lightbox_obj,
				gap;

			if (lightbox) {
				lightbox_array = window['images' + query.uid];
				if (!that.editMode) {
					wrapper.on('click', '.lightbox', function (event) {
						event.preventDefault();
						event.stopPropagation();
						var options = {
							index: $(this).closest('.isotope_item').index(),
							container: '#popup_gt3_elementor_gallery',
							event: event,
							instance: query.uid
						};

						lightbox_obj = blueimp.ElementorGallery(lightbox_array, options);
					});
				}
			}

			query.packery = null;

			function resize() {
				if (query.gap_unit === '%') {
					gap = (wrapper.width() * parseFloat(query.gap_value) / 100).toFixed(2);
					isotope.find('.isotope_item').css('padding-right', gap + 'px').css('padding-bottom', gap + 'px');
				}

				var grid = packery.grid;
				var lap = packery.lap;

				if ($(window).outerWidth() < 600) {
					grid = 1;
				} else if ($(window).outerWidth() < 900 && (grid % 2 === 0)) {
					lap = lap / 2;
					grid /= 2;
				}

				wrap_width_origin = Math.floor(isotope.width() / grid);

				var local_key = 0;
				wrapper.find('img').each(function (key, value) {
					var img = $(this);
					var parent = img.closest('.isotope_item');
					if ($(window).outerWidth() < 600) {
						parent
							.css('height', 'auto')
							.css('width', 'auto')
							.attr('data-ratio', '');

						img.attr('data-ratio', '')
							.closest('.img').css('height', 'auto').css('width', 'auto')
					} else {
						wrap_height = wrap_width = wrap_width_origin;

						index = local_key % lap + 1;
						if (index in packery.elem) {
							if ('w' in packery.elem[index] && packery.elem[index].w > 1) {
								wrap_width = wrap_width_origin * packery.elem[index].w;
							}
							if ('h' in packery.elem[index] && packery.elem[index].h > 1) {
								wrap_height = wrap_width_origin * packery.elem[index].h;
							}
						}

						local_key++;

						wrap_ratio = (wrap_width / wrap_height);
						img_ratio = ((img.attr('width') || 1) / (img.attr('height') || 1));
						if (wrap_ratio > img_ratio) img_ratio = 0.5;

						var wrap_data_ratio = wrap_ratio >= 1 ? 'landscape' : 'portrait';
						var img_data_ratio = img_ratio >= 1 ? 'landscape' : 'portrait';

						if (wrap_data_ratio === 'portrait' && img_data_ratio === 'portrait' && wrap_ratio >= img_ratio) {
							wrap_data_ratio = 'landscape';
						} else if (wrap_data_ratio === 'landscape' && img_data_ratio === 'landscape' && img_ratio <= wrap_ratio) {
							img_data_ratio = 'portrait';
						}

						parent
							.css('height', Math.floor(wrap_height))
							.css('width', Math.floor(wrap_width))
							.attr('data-ratio-n', wrap_ratio)

							.attr('data-ratio', wrap_data_ratio);

						img.attr('data-ratio', img_data_ratio)
							.attr('data-ratio-n', img_ratio)

							.closest('.img_wrap').css('height', parent.height()).css('width', parent.width())

					}
				});

				isotope.isotope({
					layoutMode: 'masonry',
					itemSelector: '.isotope_item',
					masonry: {
						columnWidth: wrap_width_origin
					},
					originLeft: !jQuery('body').hasClass('rtl')
				}).isotope('layout');
			}

			resize();
			isotope.imagesLoaded(function () {
				resize();
				showImages();
			});

			if (!that.editMode) {
				$scope.on("click", ".isotope-filter a", function (e) {
					e.preventDefault();
					var data_filter = this.getAttribute("data-filter");
					jQuery(this).siblings().removeClass("active");
					jQuery(this).addClass("active");
					isotope.isotope({filter: data_filter});
				});

				$('.view_more_link', $scope).on('click', function (e) {
					e.preventDefault();
					query.images = images[paged++];

					jQuery.ajax({
						type: "POST",
						data: query,
						url: gt3_themes_core.ajaxurl,
						success: function (data) {
							if ('post_count' in data) {
								if (data.post_count > 0) {
									var add = $(data.respond);
									isotope.append(add).isotope('appended', add);
									if (lightbox && 'gallery_items' in data) {
										lightbox_array = lightbox_array.concat(data.gallery_items);
									}
									setTimeout(function () {
										isotope.isotope({sortby: 'original-order'});
										resize();
									}, 50);
									setTimeout(function () {
										showImages();
									}, 800);
								}
							}
						},
						error: function (e) {
							console.error('Error request');
						}
					});
					if (paged >= max_page) {
						jQuery(this).addClass('hidden');
					}
				});
			}

			function showImages() {
				if (jQuery('.loading:first', $scope).length) {
					jQuery('.loading:first', $scope).removeClass('loading');
					setTimeout(showImages, 240);
				} else {
					resize();
				}
			}


			$(window).on('resize', function () {
				resize();
			});

			if (paged >= max_page) {
				jQuery('.view_more_link', $scope).remove();
			}
		},
	});

	return CoreFrontend;
});

jQuery(window).on('elementor/frontend/init', function () {
	if ('function' === typeof window.gt3Elementor.CoreFrontend) {
		window.gt3Elementor.CoreFrontend = window.gt3Elementor.CoreFrontend();
	}
});
