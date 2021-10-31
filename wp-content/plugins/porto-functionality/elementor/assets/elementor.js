jQuery(document).ready(function ($) {
	'use strict';
	if (typeof elementorFrontend != 'undefined') {

		var porto_elementor_init = function () {
			if (typeof elementor == 'undefined') {
				return;
			}
			function porto_gcd(a, b) {
				if (typeof a == 'undefined') {
					return false;
				}
				if (Array.isArray(a)) {
					var len = a.length;
					if (1 === len) {
						return a[0];
					}
					if (2 === len) {
						return porto_gcd(a[0], a[1]);
					} else if (len > 2) {
						return porto_gcd(a.pop(), porto_gcd(a));
					}
				} else {
					var max = Math.max(a, b),
						min = Math.min(a, b),
						rem = max % min;
					max = min;
					min = rem;
					if (0 === rem) {
						return max;
					} else {
						return porto_gcd(max, min);
					}
				}
			}

			function porto_lcm(a, b) {
				if (Array.isArray(a)) {
					var len = a.length;
					if (1 === len) {
						return a[0];
					}
					if (2 === len) {
						return porto_lcm(a[0], a[1]);
					} else {
						return porto_lcm(a.pop(), porto_lcm(a));
					}
				} else {
					return (a * b) / porto_gcd(a, b);
				}
			}

			function initSlider($el) {
				if (!$.fn.themeCarousel) {
					return;
				}
				$el.removeData('__carousel');
				$el.data('owl.carousel') && $el.owlCarousel('destroy');
				$el.children('.owl-item').remove();
				$el.themeCarousel($el.data('plugin-options'));
			}

			// init variables
			var refresh_timer = null,
				refresh_timer1 = null;

			elementor.channels.data.on('element:after:add', function (item) {
				var $this = $('.elementor-element-' + item.id),
					$row = $this.closest('.elementor-row, .elementor-container'),
					$column = 'widget' == item.elType ? $this.closest('.elementor-widget-wrap') : false;
				if ('widget' == item.elType && $column.hasClass('owl-carousel')) {
					initSlider($column);
				} else if ('column' == item.elType && $row.hasClass('owl-carousel')) { // carousel
					$row.trigger('add.owl.carousel', $this);
					$row.trigger('refresh.owl.carousel', $this);
				} else if ('column' == item.elType && typeof $row.attr('data-plugin-masonry') != 'undefined') { // isotope
					porto_init_creative_layout($row);
					if (!($this.get(0) instanceof HTMLElement)) {
						Object.setPrototypeOf($this.get(0), HTMLElement.prototype);
					}
					$this.addClass('porto-grid-item');
					$row.removeData('__masonry');
					if ($row.data('isotope')) {
						$row.isotope('destroy');
					}
					$row.themeMasonry($row.data('plugin-options'));
				}
			});

			function porto_init_creative_layout($obj) {
				var index = $obj.data('layout');
				$obj.children('.elementor-column').addClass('porto-grid-item');
				if (index) { // preset layout
					if (typeof porto_elementor_vars.creative_layouts[parseInt(index, 10)] == 'undefined') {
						return;
					}
					var item_classes = porto_elementor_vars.creative_layouts[Number(index)];
					$obj.children('.elementor-column').each(function (i) {
						if (typeof item_classes[i % item_classes.length] != 'undefined') {
							var current_classes = $(this).attr('class').split(' '),
								new_classes = item_classes[i % item_classes.length];
							for (var j = 0; j < current_classes.length; j++) {
								var c = $.trim(current_classes[j]);
								if (c && c.indexOf('grid-') === -1) {
									new_classes += ' ' + c;
								}
							}
							new_classes = new_classes.replace(' porto-grid-item', '');
							$(this).attr('class', new_classes + ' porto-grid-item');
						}
					});
					if ($obj.prev('style[data-id="' + escape(index) + '"]').length < 1) {
						var st = '.elementor-element.elementor-element-' + $obj.closest('.elementor-section').data('id');
						$.ajax({
							url: theme.ajax_url,
							data: {
								action: 'porto_load_creative_layout_style',
								nonce: js_porto_vars.porto_nonce,
								layout: index,
								grid_height: $obj.data('grid-height'),
								spacing: $obj.data('spacing'),
								selector: st
							},
							type: 'post',
							success: function (res) {
								$obj.prev('style').remove();
								$(res).insertBefore($obj);
								if ($obj.hasClass('elementor-container') && $obj.closest('.elementor-section').hasClass('elementor-section-boxed')) {
									var css = st + ' .grid-creative{max-width:' + (Number(porto_elementor_vars.container_width) - Number(porto_elementor_vars.grid_spacing) + Number($obj.data('spacing'))) + 'px}';
									css += '@media (min-width: 992px) and (max-width: ' + (Number(porto_elementor_vars.container_width) + Number(porto_elementor_vars.grid_spacing) - 1) + 'px){';
									css += st + ' .grid-creative{max-width:' + (960 - Number(porto_elementor_vars.grid_spacing) + Number($obj.data('spacing'))) + 'px}';
									css += '}';
									$obj.prev('style').prepend(css);
								}
								$obj.isotope('layout');
							}
						});
					}
				} else if (!$obj.hasClass('porto-preset-layout')) { // normal
					var fractions = [],
						denominators = [],
						numerators = [];
					$obj.children().each(function () {
						if ($(this).hasClass('grid-col-sizer')) {
							return;
						}
						var percent_w = $(this).children('.elementor-column-wrap, .elementor-widget-wrap').data('width');
						if (percent_w && percent_w.size) {
							var arr;
							percent_w = percent_w.size;
							if (parseFloat(parseInt(percent_w, 10)) === parseFloat(percent_w)) { // integer
								arr = [percent_w, 1];
							} else {
								for (var index = 2; index <= 100; index++) {
									var r_w = (percent_w * index).toFixed(1);
									if (parseFloat(parseInt(r_w, 10)) === r_w) { //integer
										var gcd = porto_gcd(r_w, index);
										arr = [r_w / gcd, index / gcd];
									}
								}

								if (typeof arr == 'undefined') {
									percent_w = Math.floor(percent_w * 10);
									var gcd = porto_gcd(percent_w, 10);
									arr = [percent_w / gcd, 10 / gcd];
								}
							}
							if (typeof arr != 'undefined' && -1 === fractions.indexOf(arr)) {
								fractions.push(arr);
								numerators.push(arr[0]);
								denominators.push(arr[1]);
							}
						}
					});

					if (fractions.length) {
						var deno_lcm = porto_lcm(denominators),
							num_gcd = porto_gcd(numerators),
							unit_num = (num_gcd / deno_lcm).toFixed(4);
						if (unit_num >= 0.1) {
							$obj.children('.grid-col-sizer').css({ width: unit_num + '%', flex: '0 0 ' + unit_num + '%' });
						}
					}

					if ($obj.prev('style').length < 1) {
						$('<style></style>').insertBefore($obj)
					}
					var st = '.elementor-element.elementor-element-' + $obj.closest('.elementor-section').data('id'),
						css = '@media (min-width: 992px) and (max-width: ' + (Number(porto_elementor_vars.container_width) + Number(porto_elementor_vars.grid_spacing) - 1) + 'px){';
					css += st + ' > .elementor-container{max-width:' + (960 - Number(porto_elementor_vars.grid_spacing) + Number($obj.data('spacing'))) + 'px}';
					css += '}';
					$obj.prev('style').html(css);

					if ($obj.data('isotope')) {
						$obj.isotope('layout');
					}
				}
			}

			$('.elementor-row[data-plugin-masonry], .elementor-container[data-plugin-masonry]').children('.elementor-column').each(function () {
				if (!(this instanceof HTMLElement)) {
					Object.setPrototypeOf(this, HTMLElement.prototype);
				}
			});
			/*$('.elementor-row[data-plugin-masonry]').each(function() {
				porto_init_creative_layout($(this));
				$(this).addClass('porto-init');
			});*/

			if (typeof porto_init == 'function') {
				elementor.channels.data.on('element:before:remove', function (item) {
					var $this = $('.elementor-element-' + item.id),
						$row = $this.closest('.elementor-row, .elementor-container'),
						$column = 'widget' == item.attributes.elType ? $this.closest('.elementor-widget-wrap') : false;
					if ('widget' == item.attributes.elType && $column.hasClass('owl-carousel')) {
						theme.requestFrame(function () {
							initSlider($column);
						});
					} else if ('column' == item.attributes.elType && $row.hasClass('owl-carousel')) { // carousel
						var index = $this.parent('.owl-item:not(.cloned)').index() - ($row.find('.owl-item.cloned').length / 2);
						$row.trigger('remove.owl.carousel', index);
						$row.trigger('refresh.owl.carousel', $this);
					} else if ('column' == item.attributes.elType && typeof $row.attr('data-plugin-masonry') != 'undefined' && $row.data('isotope')) { // isotope
						porto_init_creative_layout($row);
						$row.isotope('remove', $this).isotope('layout');
					}
				});

				var porto_widgets = ['porto_blog.default', 'wp-widget-recent_posts-widget.default', 'wp-widget-recent_portfolios-widget.default', 'porto_products.default', 'porto_sb_products.default', 'porto_product_categories.default', 'porto_recent_posts.default', 'shortcode.default', 'porto_portfolios.default', 'porto_button.default', 'porto_ultimate_heading.default', 'porto_recent_members.default', 'porto_recent_portfolios.default', 'porto_circular_bar.default', 'porto_cp_related.default', 'porto_cp_upsell.default', 'porto_image_gallery.default'];
				$.each(porto_widgets, function (key, element_name) {
					elementorFrontend.hooks.addAction('frontend/element_ready/' + element_name, function ($obj) {
						var $iso_obj = $obj.find('[data-plugin-masonry]').length ? $obj.find('[data-plugin-masonry]') : $obj.find('.posts-masonry .posts-container:not(.manual)');
						if (!$iso_obj.length) {
							$iso_obj = $obj.find('.page-members .member-row:not(.manual)');
						}
						if (!$iso_obj.length) {
							$iso_obj = $obj.find('.page-portfolios .portfolio-row:not(.manual)');
						}
						if ($iso_obj.length) {
							$iso_obj.children().each(function () {
								if (!(this instanceof HTMLElement)) {
									if ('shortcode.default' == element_name && $iso_obj.data('isotope')) {
										$iso_obj.isotope('destroy');
									}
									Object.setPrototypeOf(this, HTMLElement.prototype);
								}
							});
						}
						porto_init($obj);
					});
				});

				elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($obj) {
					var $row = $obj.find('> .elementor-container > .elementor-row');
					if (!$row.length) {
						$row = $obj.children('.elementor-container')
					}
					if ($row.hasClass('porto-carousel')) {
						var $carousel = $obj.find('> .elementor-container > .porto-carousel, > .porto-carousel');
						if ($carousel.data('owl.carousel')) {
							$carousel.trigger('refresh.owl.carousel');
						} else {
							$carousel.themeCarousel($carousel.data('plugin-options'));
						}
						setTimeout(function () {
							$carousel.trigger('refresh.owl.carousel');
						}, 150);
					} else if (typeof $row.attr('data-plugin-masonry') != 'undefined') {
						var $iso_obj = $row;
						$iso_obj.children().each(function () {
							if (!(this instanceof HTMLElement)) {
								Object.setPrototypeOf(this, HTMLElement.prototype);
							}
						});
						if (0 === $iso_obj.children('.grid-col-sizer').index()) {
							$iso_obj.children('.grid-col-sizer').appendTo($iso_obj);
						}
						if (!$iso_obj.hasClass('porto-init')) {
							porto_init_creative_layout($iso_obj);
							$iso_obj.themeMasonry($iso_obj.data('plugin-options'));
							$iso_obj.addClass('porto-init');
						} else if ($iso_obj.data('isotope')) {
							$iso_obj.isotope('layout');
						}
					}

					if ($row.data('add_container')) {
						$row.children('.elementor-column').filter(function () {
							if ($(this).children('.porto-ibanner-layer').length) {
								return true;
							}
							return false;
						}).addClass('container');
					}

					if ($row.hasClass('porto-parallax')) {
						var speed = $row.data('parallax-speed');
						if ($obj.data('__parallax') && $obj.data('__parallax').options) {
							var old_speed = $obj.data('__parallax').options.speed;
							if (parseFloat(old_speed) !== parseFloat(speed)) {
								$obj.removeData('__parallax');
							}
						}
						$obj.themeParallax({ speed: speed });
					}

					initWidgetAddon($obj);
				});

				elementorFrontend.hooks.addAction('frontend/element_ready/column', function ($obj) {
					var $row = $obj.closest('.elementor-row, .elementor-container');
					if ($obj.find('> .elementor-column-wrap > .porto-carousel, > .porto-carousel').length) {
						var $carousel = $obj.find('> .elementor-column-wrap > .porto-carousel, > .porto-carousel');
						if ($carousel.data('owl.carousel')) {
							$carousel.trigger('refresh.owl.carousel');
						} else {
							$carousel.themeCarousel($carousel.data('plugin-options'));
						}
					}

					var $column_wrap = $obj.children('.elementor-column-wrap, .elementor-widget-wrap');
					if ($column_wrap.data('cont_cls')) {
						$obj.addClass($column_wrap.data('cont_cls'));
					}
					if ($row.hasClass('owl-carousel')) {
						if (refresh_timer) {
							clearTimeout(refresh_timer);
						}
						refresh_timer = setTimeout(function () {
							$row.removeData('__carousel');
							$row.trigger('destroy.owl.carousel');
							$row.themeCarousel($row.data('plugin-options'));
						}, 100);
					} else if (typeof $row.attr('data-plugin-masonry') != 'undefined') {
						if (refresh_timer) {
							clearTimeout(refresh_timer);
						}
						refresh_timer = setTimeout(function () {
							porto_init_creative_layout($row);
							$row.children().each(function () {
								if (!(this instanceof HTMLElement)) {
									Object.setPrototypeOf(this, HTMLElement.prototype);
								}
							});
							$row.removeData('__masonry');
							if ($row.data('isotope')) {
								$row.isotope('destroy');
							}
							$row.themeMasonry($row.data('plugin-options'));
						}, 100);
					}

					if ($obj.find('> .porto-parallax').length) {
						var speed = $obj.find('> .porto-parallax').data('parallax-speed');
						if ($obj.data('__parallax') && $obj.data('__parallax').options) {
							var old_speed = $obj.data('__parallax').options.speed;
							if (parseFloat(old_speed) !== parseFloat(speed)) {
								$obj.removeData('__parallax');
							}
						}
						$obj.themeParallax({ speed: speed });
					}

					if ($column_wrap.hasClass('porto-ibanner-layer')) {
						if ($column_wrap.attr('data-wrap_cls')) {
							var classList = $obj[0].classList;
							classList.forEach(function (item) {
								if (-1 != item.indexOf('porto-ibe-')) {
									$obj.removeClass(item);
								}
							});


							$obj.addClass($column_wrap.attr('data-wrap_cls'));
							$column_wrap.removeAttr('data-wrap_cls');
						}
						if ($column_wrap.attr('data-add_container')) {
							$column_wrap.removeAttr('data-add_container').wrap('<div class="container"></div>');
						}
					}
					if (typeof $column_wrap.attr('data-appear-animation') != 'undefined') {
						$column_wrap.themeAnimate();
					}

					if ($row.data('add_container')) {
						if ($column_wrap.hasClass('porto-ibanner-layer')) {
							$obj.addClass('container');
						} else {
							$obj.removeClass('container');
						}
					}

					if ( $column_wrap.hasClass('porto-sticky') ) {
						if ( $column_wrap.is( ':visible' ) ) {
							var pluginOptions = $column_wrap.attr( 'data-plugin-options' );
							if (typeof pluginOptions == 'string') {
								try {
									pluginOptions = JSON.parse(pluginOptions.replace(/'/g, '"').replace(';', ''));
								} catch (e) { }
							}
							$column_wrap.themeSticky( pluginOptions );
						}
					}

					var $widget_wrap = $obj.find('> .elementor-column-wrap > .elementor-widget-wrap, > .elementor-widget-wrap');
					if (typeof $widget_wrap.attr('data-plugin-float-element') != 'undefined') {
						var opts = $widget_wrap.data('plugin-options');
						if (typeof opts == 'string') {
							try {
								opts = JSON.parse(opts.replace(/'/g, '"').replace(';', ''));
							} catch (e) { }
						}
						$widget_wrap.themePluginFloatElement(opts);
					}

					initWidgetAddon($obj);
				});

				elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($obj) {
					initWidgetAddon($obj);
				});
			}

			function initWidgetAddon($obj) {
				var widget_settings,
					editorElements = null,
					widgetData = {};

				if (!window.elementor.hasOwnProperty('elements')) {
					widget_settings = false;
				}

				editorElements = window.elementor.elements;

				if (!editorElements.models) {
					widget_settings = false;
				}

				$.each(editorElements.models, function (index, obj) {
					if ($obj.data('id') == obj.id) {
						widgetData = obj.attributes.settings.attributes;
						return false;
					}

					$.each(obj.attributes.elements.models, function (index, obj) {
						if ($obj.data('id') == obj.id) {
							widgetData = obj.attributes.settings.attributes;
							return false;
						}

						$.each(obj.attributes.elements.models, function (index, obj) {
							if ($obj.data('id') == obj.id) {
								widgetData = obj.attributes.settings.attributes;
								return false;
							}

							$.each(obj.attributes.elements.models, function (index, obj) {
								if ($obj.data('id') == obj.id) {
									widgetData = obj.attributes.settings.attributes;
									return false;
								}

								$.each(obj.attributes.elements.models, function (index, obj) {
									if ($obj.data('id') == obj.id) {
										widgetData = obj.attributes.settings.attributes;
										return false;
									}
								});

							});
						});

					});
				});

				var widget_settings = {
					mpx: widgetData['mouse_parallax'],
					mpx_inverse: widgetData['mouse_parallax_inverse'],
					mpx_speed: 'object' == typeof widgetData['mouse_parallax_speed'] && widgetData['mouse_parallax_speed']['size'] ? widgetData['mouse_parallax_speed']['size'] : 0.5,
				};

				if ($obj.data('parallax')) {
					$obj.parallax('disable');
					$obj.removeData('parallax');
					$obj.removeData('options');
				}

				if ('object' == typeof widget_settings && widget_settings.mpx) {
					$obj.attr('data-plugin', 'mouse-parallax');

					var settings = {},
						opts;

					if ('yes' == widget_settings['mpx_inverse']) {
						settings['invertX'] = true;
						settings['invertY'] = true;
					} else {
						settings['invertX'] = false;
						settings['invertY'] = false;
					}

					$obj.attr('data-options', JSON.stringify(settings));
					$obj.attr('data-floating-depth', widget_settings['mpx_speed']);

					if ($obj.hasClass('elementor-element')) {
						$obj.children('.elementor-widget-container, .elementor-container, .elementor-widget-wrap, .elementor-column-wrap').addClass('layer').attr('data-depth', $obj.attr('data-floating-depth'));
					} else {
						$obj.children('.layer').attr('data-depth', $obj.attr('data-floating-depth'));
					}

					var pluginOptions = $obj.data('options');
					if (pluginOptions)
						opts = pluginOptions;

					if ($.fn.parallax) {
						new theme.Mouseparallax($obj, opts);
					} else {
						if (porto_elementor_vars.js_assets_url) {
							$(document.createElement('script')).attr('id', 'jquery-parallax').appendTo('body').attr('src', porto_elementor_vars.js_assets_url + '/libs/jquery.parallax.min.js').on('load', function () {
								new theme.Mouseparallax($obj, opts);
							});
						}
					}
					return;
				}
			}

			elementorFrontend.hooks.addAction('frontend/element_ready/porto_fancytext.default', function ($obj) {
				$(document.body).trigger('porto_init_fancytext', [$obj]);
			});
			elementorFrontend.hooks.addAction('frontend/element_ready/porto_countdown.default', function ($obj) {
				if ($obj.find('.porto_countdown-div').length) {
					let cdate = new Date(), sdate = cdate.getTime() + parseFloat(porto_elementor_vars.gmt_offset) * 3600 * 1000;
					sdate = new Date(sdate).toISOString().replace(/(.*)(20[0-9]{2}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})(.*)/, '$2 $3');
					$obj.find('.porto_countdown-div').data('time-now', sdate.replace(/-/g, '/'));
				}
				$(document.body).trigger('porto_init_countdown', [$obj]);
			});

			if (typeof porto_woocommerce_init == 'function') {
				var porto_woocommerce_widgets = ['porto_products.default', 'porto_sb_products.default', 'porto_product_categories.default', 'porto_cp_related.default'];
				$.each(porto_woocommerce_widgets, function (key, element_name) {
					elementorFrontend.hooks.addAction('frontend/element_ready/' + element_name, function ($obj) {
						porto_woocommerce_init();
					});
				});

				elementorFrontend.hooks.addAction('frontend/element_ready/porto_cp_image.default', function ($obj) {
					theme.WooProductImageSlider.initialize();
				});

				porto_woocommerce_widgets = ['porto_cp_actions.default', 'porto_cp_add_to_cart.default'];
				$.each(porto_woocommerce_widgets, function (key, element_name) {
					elementorFrontend.hooks.addAction('frontend/element_ready/' + element_name, function ($obj) {
						theme.WooQtyField.initialize()
					});
				});
			}

			elementorFrontend.hooks.addAction('masonry_refresh', function (cls, w) {
				if (refresh_timer) {
					clearTimeout(refresh_timer);
				}
				refresh_timer = setTimeout(function () {
					var $obj;
					if (cls) {
						$obj = $('.elementor-column[class="' + cls + '"]').parent();
					} else {
						$obj = $('.elementor-element-editable.porto-grid-item').parent();
					}
					if ($obj.length && $obj.data('isotope')) {
						if (w) {
							$('.elementor-element-editable.porto-grid-item').children('.elementor-column-wrap, .elementor-widget-wrap').data('width').size = Number(w);
						}
						porto_init_creative_layout($obj);
					}
				}, 100);
			});

			elementorFrontend.hooks.addAction('refresh_dynamic_css', function (css, block_id) {
				var $obj = $('style#porto_elementor_custom_css');
				if (!$obj.length) {
					$obj = $('<style></style>').attr('id', 'porto_elementor_custom_css').appendTo('head');
				}
				css = css.replace('/<script.*?\/script>/s', '');
				if (typeof block_id == 'undefined') {
					$obj.html(css);
				} else if (-1 === $obj.html().indexOf(css)) {
					$obj.html($obj.html() + css);
				}
			});

			elementorFrontend.hooks.addAction('refresh_popup_options', function (option, value) {
				if ('popup_width' == option) {
					$('.elementor.elementor-edit-area').css('max-width', value + 'px');
				}
				else {
					var _$ = value;
					var horizontal = parseInt(_$('input[data-setting="popup_pos_horizontal"]').val(), 10),
						vertical = parseInt(_$('input[data-setting="popup_pos_vertical"]').val(), 10),
						editor = $('.elementor.elementor-edit-area');
					if (option == 'popup_pos_first') {
						horizontal = elementor.settings.page.model.get('popup_pos_horizontal');
						vertical = elementor.settings.page.model.get('popup_pos_vertical');
					}
					editor.css({ left: '', top: '', right: '', bottom: '', transform: '' });
					if (50 === horizontal) {
						if (50 === vertical) {
							editor.css({ left: '50%', top: '50%', transform: 'translate(-50%, -50%)' });
						} else {
							editor.css({ left: '50%', transform: 'translateX(-50%)' });
						}
					}
					else if (50 > horizontal) {
						editor.css({ left: horizontal + '%' });
					}
					else {
						editor.css({ right: (100 - horizontal) + '%' });
					}
					if (50 === vertical) {
						if (50 !== horizontal) {
							editor.css({ top: '50%', transform: 'translateY(-50%)' });
						}
					}
					else if (50 > vertical) {
						editor.css({ top: vertical + '%' });
					}
					else {
						editor.css({ bottom: (100 - vertical) + '%' });
					}
				}
			});

			elementorFrontend.hooks.addAction('refresh_edit_area', function (width) {
				var $style = $('style#porto-edit-area-style');
				if (!$style.length) {
					$('.elementor-edit-area').before('<style id="porto-edit-area-style"></style');
					$style = $('style#porto-edit-area-style');
				}
				if ('' == width) {
					$style.html('');
				} else {
					$style.html('.elementor-edit-area > .elementor-section-wrap { max-width: ' + parseFloat(js_porto_vars.container_width) + 'px; margin: 0 auto; }.elementor-section-wrap > .elementor-section { max-width: ' + width + '; }');
				}
			});


			$('.porto-block[data-el_cls]').each(function () {
				$(this).addClass($(this).data('el_cls')).removeAttr('data-el_cls');
			});

			if (typeof elementorFrontend.elementsHandler.elementsHandlers.section[4] == 'function' && elementorFrontend.elementsHandler.elementsHandlers.section[4].prototype.buildSVG) {
				elementorFrontend.elementsHandler.elementsHandlers.section[4].prototype.onElementChange = function (propertyName) {
					if (propertyName.match(/^shape_divider_(top|bottom)_custom$/)) {
						this.buildSVG(propertyName.match(/^shape_divider_(top|bottom)_custom$/)[1]);
						return;
					}
					var shapeChange = propertyName.match(/^shape_divider_(top|bottom)$/);
					if (shapeChange) {
						this.buildSVG(shapeChange[1]);
						return;
					}
					var negativeChange = propertyName.match(/^shape_divider_(top|bottom)_negative$/);
					if (negativeChange) {
						this.buildSVG(negativeChange[1]);
						this.setNegative(negativeChange[1]);
					}
				}
				elementorFrontend.elementsHandler.elementsHandlers.section[4].prototype.buildSVG = function buildSVG(side) {
					var baseSettingKey = 'shape_divider_' + side,
						shapeType = this.getElementSettings(baseSettingKey),
						$svgContainer = this.elements['$' + side + 'Container'];
					$svgContainer.attr('data-shape', shapeType);

					if (!shapeType) {
						$svgContainer.empty(); // Shape-divider set to 'none'

						return;
					}

					var fileName = shapeType;

					if (this.getElementSettings(baseSettingKey + '_negative')) {
						fileName += '-negative';
					}

					var svgURL = this.getSvgURL(shapeType, fileName);
					if (shapeType != 'custom') {
						jQuery.get(svgURL, function (data) {
							$svgContainer.empty().append(data.childNodes[0]);
						});
					} else {
						this.elements['$' + side + 'Container'].attr('data-negative', 'false');
						var data = this.getElementSettings(baseSettingKey + '_custom');
						var svgManager = elementor.helpers;
						data = data.value;
						if (!data.id) {
							$svgContainer.empty();
							return;
						}

						if (svgManager._inlineSvg.hasOwnProperty(data.id)) {
							data && $svgContainer.empty().html(svgManager._inlineSvg[data.id]);
							return;
						}
						svgManager.fetchInlineSvg(data.url, function (svgData) {
							if (svgData) {
								svgManager._inlineSvg[data.id] = svgData; //$( data ).find( 'svg' )[ 0 ].outerHTML;
								svgData && $svgContainer.empty().html(svgData);
								elementor.channels.editor.trigger('svg:insertion', svgData, data.id);
							}
						});
					}
					this.setNegative(side);
				}
			}

			// Header and Footer Type preset
			if (window.top.porto_builder_condition && window.top.porto_builder_condition.builder_type && (window.top.porto_builder_condition.builder_type == 'header' || window.top.porto_builder_condition.builder_type == 'footer')) {
				window.top.elementor.presetsFactory.getPresetSVG = function getPresetSVG(preset, svgWidth, svgHeight, separatorWidth) {
					var _ = window.top._;
					if (_.isEqual(preset, ['flex-1', 'flex-auto'])) {
						var svg = document.createElement('svg');
						var protocol = 'http';
						svg.setAttribute('viewBox', '0 0 88.3 44.2');
						svg.setAttributeNS(protocol + '://www.w3.org/2000/xmlns/', 'xmlns:xlink', protocol + '://www.w3.org/1999/xlink');
						svg.innerHTML = '<rect fill="#D5DADF" width="73.8" height="44.2"></rect> <rect x="75.5" fill="#D5DADF" width="12.8" height="44.2"></rect> <text transform="matrix(1 0 0 1 8.5 25.9167)" fill="#A7A9AC" font-family="Segoe Script" font-size="12">For ' + window.top.porto_builder_condition.builder_type + '</text>'
						return svg;
					}
					else if (_.isEqual(preset, ['flex-1', 'flex-auto', 'flex-1'])) {
						var svg = document.createElement('svg');
						var protocol = 'http';
						svg.setAttribute('viewBox', '0 0 88.3 44.2');
						svg.setAttributeNS(protocol + '://www.w3.org/2000/xmlns/', 'xmlns:xlink', protocol + '://www.w3.org/1999/xlink');
						svg.innerHTML = '<rect fill="#D5DADF" width="35" height="44.2"></rect><rect x="53.4" fill="#D5DADF" width="35" height="44.2" ></rect><rect x="36.9" fill="#D5DADF" width="14.5" height="44.2"></rect><text transform="matrix(1 0 0 1 8.5 25.9167)" fill="#A7A9AC" font-family="Segoe Script" font-size="12">For ' + window.top.porto_builder_condition.builder_type + '</text>';
						return svg;
					}
					else if (_.isEqual(preset, ['flex-auto', 'flex-1', 'flex-auto'])) {
						var svg = document.createElement('svg');
						var protocol = 'http';
						svg.setAttribute('viewBox', '0 0 88.3 44.2');
						svg.setAttributeNS(protocol + '://www.w3.org/2000/xmlns/', 'xmlns:xlink', protocol + '://www.w3.org/1999/xlink');
						svg.innerHTML = '<rect fill="#D5DADF" width="11.5" height="44.2"></rect><rect x="59.2" fill="#D5DADF" width="29.2" height="44.2"></rect><rect x="13.7" fill="#D5DADF" width="43.5" height="44.2"></rect> <text transform="matrix(1 0 0 1 8.5 25.9167)" fill="#A7A9AC" font-family="Segoe Script" font-size="12">For ' + window.top.porto_builder_condition.builder_type + '</text></svg>'
						return svg;
					}
					svgWidth = svgWidth || 100;
					svgHeight = svgHeight || 50;
					separatorWidth = separatorWidth || 2;

					var absolutePresetValues = this.getAbsolutePresetValues(preset),
						presetSVGPath = this._generatePresetSVGPath(absolutePresetValues, svgWidth, svgHeight, separatorWidth);

					return this._createSVGPreset(presetSVGPath, svgWidth, svgHeight);
				}
			}

			// image comparison widget
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_image_comparison.default', function( $obj ) {
				if ( $.fn.portoImageCompare ) {
					$obj.find( '.porto-image-comparison' ).portoImageCompare();
				}
			} );

		}

		if (elementorFrontend.hooks && typeof elementor != 'undefined') {
			porto_elementor_init();
		} else {
			elementorFrontend.on('components:init', porto_elementor_init);
		}
	}
});