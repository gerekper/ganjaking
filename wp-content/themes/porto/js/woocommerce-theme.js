(function() {
	'use strict';

	// Theme Functions
	function portoCalcSliderButtonsPosition($parent, padding) {
		var $buttons = $parent.find('.show-nav-title .owl-nav');
		if ($buttons.length) {
			if (window.theme.rtl) {
				$buttons.css('left', padding);
			} else {
				$buttons.css('right', padding);
			}
			if ($buttons.closest('.porto-products').length && $buttons.closest('.porto-products').parent().children('.products-slider-title').length) {
				var $title = $buttons.closest('.porto-products').parent().children('.products-slider-title'),newMT = $title.offset().top - $parent.offset().top - parseInt($title.css('padding-top'), 10) - parseInt($title.css('line-height'), 10) / 2 + $buttons.children().outerHeight() - parseInt($buttons.children().css('margin-top'), 10);
				$buttons.css('margin-top', newMT);
			}
		}
	}

	function portoCalcSliderTitleLine($parent_obj) {
		$parent_obj.each(function() {
			var $parent = jQuery(this);
			var $title = $parent.children('.section-title');
			if (!$title.length || !$parent.hasClass('title-border-middle')) return;

			var $l = $title.find('.line'),
				$t = $title.find('.inline-title');

			if (!$t.length || !$l.length) return;

			var offset = $t.offset().left - $title.offset().left,
				title_w = $title.width() - offset,
				t_w = $t.width();
			if (title_w > t_w + 200) {
				var offset_nav_dots = 0;
				if ($parent.find('.owl-carousel.show-dots-title-right').length || $parent.find('.owl-carousel.show-nav-title').length) {
					offset_nav_dots = 75;
				}
				if (window.theme.rtl) {
					$l.css({
						display: 'block',
						right: offset + t_w + 20,
						width: title_w - t_w - offset_nav_dots
					});
				} else {
					$l.css({
						display: 'block',
						left: offset + t_w + 20,
						width: title_w - t_w - offset_nav_dots
					});
				}
			} else {
				$l.css({
					display: 'none'
				});
			}
		});
	}

	// Woocommerce Widget Toggle
	(function(theme, $) {

		theme = theme || {};

		var instanceName = '__wooWidgetToggle';

		var WooWidgetToggle = function($el, opts) {
			return this.initialize($el, opts);
		};

		WooWidgetToggle.defaults = {

		};

		WooWidgetToggle.prototype = {
			initialize: function($el, opts) {
				if ($el.data(instanceName)) {
					return this;
				}

				this.$el = $el;

				this
					.setData()
					.setOptions(opts)
					.build();

				return this;
			},

			setData: function() {
				this.$el.data(instanceName, this);

				return this;
			},

			setOptions: function(opts) {
				this.options = $.extend(true, {}, WooWidgetToggle.defaults, opts, {
					wrapper: this.$el
				});

				return this;
			},

			build: function() {
				var $el = this.options.wrapper;

				$el.parent().removeClass('closed');
				if (!$el.find('.toggle').length) {
					$el.append('<span class="toggle"></span>');
				}
				$el.find('.toggle').click(function() {
					if ($el.next().is(":visible")){
						$el.parent().addClass('closed');
					} else {
						$el.parent().removeClass('closed');
					}
					$el.next().stop().slideToggle(200);
					theme.refreshVCContent();
				});

				return this;
			}
		};

		// expose to scope
		$.extend(theme, {
			WooWidgetToggle: WooWidgetToggle
		});

		// jquery plugin
		$.fn.themeWooWidgetToggle = function(opts) {
			return this.map(function() {
				var $this = $(this);

				if ($this.data(instanceName)) {
					return $this.data(instanceName);
				} else {
					return new theme.WooWidgetToggle($this, opts);
				}

			});
		}

	}).apply(this, [window.theme, jQuery]);


	// Woocommerce Widget Accordion
	(function(theme, $) {

		theme = theme || {};

		var instanceName = '__wooWidgetAccordion';

		var WooWidgetAccordion = function($el, opts) {
			return this.initialize($el, opts);
		};

		WooWidgetAccordion.defaults = {

		};

		WooWidgetAccordion.prototype = {
			initialize: function($el, opts) {
				if ($el.data(instanceName)) {
					return this;
				}

				this.$el = $el;

				this
					.setData()
					.setOptions(opts)
					.build();

				return this;
			},

			setData: function() {
				this.$el.data(instanceName, this);

				return this;
			},

			setOptions: function(opts) {
				this.options = $.extend(true, {}, WooWidgetAccordion.defaults, opts, {
					wrapper: this.$el
				});

				return this;
			},

			build: function() {
				var self = this,
					$el = this.options.wrapper;

				$el.find('ul.children').each(function() {
					var $this = $(this);
					if (!$this.prev().hasClass('toggle')) {
						$this.before(
							$('<span class="toggle"></span>').click(function() {
								var $that = $(this);
								if ($that.next().is(":visible")) {
									$that.parent().removeClass('open').addClass('closed');
								} else {
									$that.parent().addClass('open').removeClass('closed');
								}
								$that.next().stop().slideToggle(200);
								theme.refreshVCContent();
							})
						);
					}
				});
				$el.find('li[class*="current-"]').addClass('current');

				return this;
			}
		};

		// expose to scope
		$.extend(theme, {
			WooWidgetAccordion: WooWidgetAccordion
		});

		// jquery plugin
		$.fn.themeWooWidgetAccordion = function(opts) {
			return this.map(function() {
				var $this = $(this);

				if ($this.data(instanceName)) {
					return $this.data(instanceName);
				} else {
					return new theme.WooWidgetAccordion($this, opts);
				}

			});
		}

	}).apply(this, [window.theme, jQuery]);


	// Woocommerce Products Slider
	(function(theme, $) {

		theme = theme || {};

		var instanceName = '__wooProductsSlider';

		var WooProductsSlider = function($el, opts) {
			return this.initialize($el, opts);
		};

		WooProductsSlider.defaults = {
			rtl: theme.rtl,
			autoplay : theme.slider_autoplay == '1' ? true : false,
			autoplayTimeout: theme.slider_speed ? theme.slider_speed : 5000,
			loop: theme.slider_loop,
			nav: false,
			navText: ["", ""],
			dots: false,
			autoplayHoverPause : true,
			items : 1,
			responsive : {},
			autoHeight : true,
			lazyLoad: true
		};

		WooProductsSlider.prototype = {
			initialize: function($el, opts) {
				if ($el.data(instanceName)) {
					return this;
				}

				this.$el = $el;

				this
					.setData()
					.setOptions(opts)
					.build();

				return this;
			},

			setData: function() {
				this.$el.data(instanceName, true);

				return this;
			},

			setOptions: function(opts) {
				this.options = $.extend(true, {}, WooProductsSlider.defaults, opts, {
					wrapper: this.$el
				});

				return this;
			},

			calcOwlHeight: function($el) {
				var h = 0;
				$el.find('.owl-item.active').each(function() {
					if (h < $(this).height())
						h = $(this).height();
				});
				$el.find('.owl-stage-outer').height( h );
			},

			build: function() {
				var self = this,
					$el = this.options.wrapper,
					lg = this.options.lg,
					md = this.options.md,
					xs = this.options.xs,
					ls = this.options.ls,
					$slider_wrapper = $el.closest('.slider-wrapper'),
					single = this.options.single,
					dots = this.options.dots,
					nav = this.options.nav,
					responsive = {},
					items,
					scrollWidth = theme.getScrollbarWidth(),
					count = $el.find('> *').length,
					w_xs = 576 - scrollWidth,
					w_md = 768 - scrollWidth,
					w_lg = parseInt(js_porto_vars.screen_lg) - scrollWidth,
					w_sl = 1400 - scrollWidth;

				if ($el.find('.product-col').get(0)) {
					portoCalcSliderButtonsPosition($slider_wrapper, $el.find('.product-col').css('padding-left'));
				}

				if (single) {
					items = 1;
				} else {
					items = lg ? lg : 1;
					if (this.options.xl) {
						responsive[w_sl] = { items: this.options.xl, loop: (this.options.loop && count > this.options.xl) ? true : false };
					}
					responsive[w_lg] = { items: items, loop: (this.options.loop && count > items) ? true : false };
					if (md) responsive[w_md] = { items: md, loop: (this.options.loop && count > md) ? true : false };
					if (xs) responsive[w_xs] = { items: xs, loop: (this.options.loop && count > xs) ? true : false };
					if (ls) responsive[0] = { items: ls, loop: (this.options.loop && count > ls) ? true : false };
				}

				this.options = $.extend(true, {}, this.options, {
					loop: (this.options.loop && count > items) ? true : false,
					items: items,
					responsive: responsive,
					onRefresh: function() {
						if ($el.find('.product-col').get(0)) {
							portoCalcSliderButtonsPosition($slider_wrapper, $el.find('.product-col').css('padding-left'));
						}
						//$el.find('.porto-lazyload:not(.lazy-load-loaded)').trigger('appear');
					},
					onInitialized: function() {
						if ($el.find('.product-col').get(0)) {
							portoCalcSliderButtonsPosition($slider_wrapper, $el.find('.product-col').css('padding-left'));
						}
						//$el.find('.cloned .porto-lazyload:not(.lazy-load-loaded)').themePluginLazyLoad();
					},
					touchDrag: (count == 1) ? false : true,
					mouseDrag: (count == 1) ? false : true
				});

				// Auto Height Fixes
				if (this.options.autoHeight) {
					var thisobj = this;
					$(window).on('resize', function() {
						thisobj.calcOwlHeight($el);
					});

					$(window).on('load', function() {
						thisobj.calcOwlHeight($el);
					});
				}

				$el.owlCarousel(this.options);

				return this;
			}
		};

		// expose to scope
		$.extend(theme, {
			WooProductsSlider: WooProductsSlider
		});

		// jquery plugin
		$.fn.themeWooProductsSlider = function(opts) {
			return this.map(function() {
				var $this = $(this);

				if ($this.data(instanceName)) {
					return $this;
				} else {
					return new theme.WooProductsSlider($this, opts);
				}

			});
		}

	}).apply(this, [window.theme, jQuery]);

	// Woocommerce Add to Cart, View Cart Events
	(function(theme, $) {

		var $supports_html5_storage;
		try {
			$supports_html5_storage = ( 'sessionStorage' in window && window.sessionStorage !== null );

			window.sessionStorage.setItem( 'wc', 'test' );
			window.sessionStorage.removeItem( 'wc' );
		} catch( err ) {
			$supports_html5_storage = false;
		}

		var setCartCreationTimestamp = function() {
			if ( $supports_html5_storage ) {
				sessionStorage.setItem( 'wc_cart_created', ( new Date() ).getTime() );
			}
		};

		var setCartHash = function(cart_hash) {
			if ( $supports_html5_storage && wc_cart_fragments_params ) {
				localStorage.setItem( wc_cart_fragments_params.cart_hash_key, cart_hash );
				sessionStorage.setItem( wc_cart_fragments_params.cart_hash_key, cart_hash );
			}
		};

		var initAjaxRemoveCartItem = function() {
			$(document).off('click', '.widget_shopping_cart .remove-product, .shop_table.cart .remove-product').on('click', '.widget_shopping_cart .remove-product, .shop_table.cart .remove-product', function(e){
				e.preventDefault();
				var $this = $(this);
				var cart_id = $this.data("cart_id");
				var product_id = $this.data("product_id");
				$this.closest('li').find('.ajax-loading').show();

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: theme.ajax_url,
					data: {
						action: "porto_cart_item_remove",
						nonce: js_porto_vars.porto_nonce,
						cart_id: cart_id
					},
					success: function( response ) {
						var this_page = window.location.toString(),
							item_count = $(response.fragments['div.widget_shopping_cart_content']).find('.mini_cart_item').length;

						this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );
						updateCartFragment(response);
						$( document.body ).trigger( 'wc_fragments_refreshed' );
						$('.viewcart-' + product_id).removeClass('added');
						$('.porto_cart_item_' + cart_id).remove();

						// Block widgets and fragments
						if ( item_count == 0 && ($('body').hasClass('woocommerce-cart') || $('body').hasClass('woocommerce-checkout')) ) {
							$( '.page-content' ).fadeTo( '400', '0.8' ).block({
								message: null,
								overlayCSS: {
									opacity: 0.2
								}
							});
						} else {
							$( '.shop_table.cart, .shop_table.review-order, .updating, .cart_totals' ).fadeTo( '400', '0.8' ).block({
								message: null,
								overlayCSS: {
									opacity: 0.2
								}
							});
						}

						// Unblock
						$( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();

						// Cart page elements
						if ( item_count == 0 && ($('body').hasClass('woocommerce-cart') || $('body').hasClass('woocommerce-checkout')) ) {
							$( '.page-content' ).load( this_page + ' .page-content:eq(0) > *', function() {
								$( '.page-content' ).stop( true ).css( 'opacity', '1' ).unblock();
							});
						} else {
							$( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {
								$( '.shop_table.cart' ).stop( true ).css( 'opacity', '1' ).unblock();
							});

							$( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
								$( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
							});

							// Checkout page elements
							$( '.shop_table.review-order' ).load( this_page + ' .shop_table.review-order:eq(0) > *', function() {
								$( '.shop_table.review-order' ).stop( true ).css( 'opacity', '1' ).unblock();
							});
						}
					}
				});

				return false;
			});
		};

		var refreshCartFragment = function() {
			initAjaxRemoveCartItem();
			if ( $.cookie( 'woocommerce_items_in_cart' ) > 0 ) {
				$( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).show();
			} else {
				$( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).hide();
			}
		};

		var updateCartFragment = function(data) {
			if (data && data.fragments) {
				var fragments = data.fragments,
					cart_hash = data.cart_hash;

				$.each(fragments, function(key, value) {
					$(key).replaceWith(value);
				});
				if ( typeof wc_cart_fragments_params === 'undefined' ) {
					return;
				}
				/* Storage Handling */
				if ( $supports_html5_storage ) {
					var prev_cart_hash = sessionStorage.getItem( 'wc_cart_hash' );

					if ( prev_cart_hash === null || prev_cart_hash === undefined || prev_cart_hash === '' ) {
						setCartCreationTimestamp();
					}
					sessionStorage.setItem( wc_cart_fragments_params.fragment_name, JSON.stringify( fragments ) );
					setCartHash( cart_hash );
				}
			}
		};

		$(function() {

			refreshCartFragment();

			// add ajax cart loading
			$(document).on('click', '.add_to_cart_button', function(e) {
				var $this = $(this);
				if ( $this.is('.product_type_simple') ) {
					if ( $this.attr('data-product_id') ) {
						$this.addClass('product-adding');
					}
					//add to cart notifaction style 2
					if( $this.hasClass('viewcart-style-2') ){
						$('body').append('<div id="loading-mask"><div class="background-overlay"></div></div>');
						if (!$(this).closest('.product').find('.loader-container').length) {
							$(this).closest('.product').find('.product-image').append('<div class="loader-container"><div class="loader"><i class="porto-ajax-loader"></i></div></div>');
						}
						$(this).closest('.product').find('.loader-container').show();
					}
				}
			});

			// add to cart action
			$(document).on('click', 'span.add_to_cart_button', function(e) {
				var $this = $(this);
				if ( $this.is('.product_type_simple') ) {
					if ( !$this.attr('data-product_id') ) {
						window.location.href = $this.attr('href');
					}
				} else {
					window.location.href = $this.attr('href');
				}
			});

			$('body').bind('added_to_cart', function() {
				$('ul.products li.product .added_to_cart').remove();
				initAjaxRemoveCartItem();
			});

			$(document.body).bind('wc_fragments_refreshed wc_fragments_loaded', function() {
				refreshCartFragment();
			});

			$(document).on( 'click', '.product-image .viewcart, .after-loading-success-message .viewcart', function( e ){
				if (wc_add_to_cart_params.cart_url) {
					window.location.href = wc_add_to_cart_params.cart_url;
				}
				e.preventDefault();
			});
			var porto_product_add_cart_timer = null;
			$(document).on('added_to_cart', 'body', function(event) {
				var $mc_item = $('#mini-cart .cart-items');
				if ($mc_item.length) {
					$mc_item.addClass('count-updating');
					setTimeout(function() {
						$mc_item.removeClass('count-updating');
					}, 1000);
				}
				$('.add_to_cart_button.product-adding').each(function() {
					var $link = $(this);
					$link.removeClass('product-adding');
					if ($link.hasClass('viewcart-style-1')) {
						$link.closest('.product').find('.viewcart').addClass('added');
					} else {
						//add to cart notifaction style 2
						$('body #loading-mask').remove();
						$link.closest('.product').find('.loader-container').hide();
						if ($link.closest('li.outofstock').length) {
							return;
						}
						$('.after-loading-success-message .product-name').text($link.closest('.product').find('.woocommerce-loop-product__title').text());
						$('.after-loading-success-message .msg-box img').remove();
						if ($link.closest('.product').find('.product-image img').length) {
							$link.closest('.product').find('.product-image img').eq(0).clone().appendTo('.after-loading-success-message .msg-box');
						}
						$('.after-loading-success-message').eq(0).stop().show();
						if (porto_product_add_cart_timer) {
							clearTimeout(porto_product_add_cart_timer);
						}
						porto_product_add_cart_timer = setTimeout(function() { $('.after-loading-success-message').eq(0).hide(); }, 4000);
						$('.continue_shopping').click(function(){ $('.after-loading-success-message').eq(0).fadeOut(200); });
					}
				});
			});

			$(document).on("click", ".variations_form .variations .filter-item-list .filter-color, .variations_form .variations .filter-item-list .filter-item", function(e) {
				e.preventDefault();
				if ($(this).closest("ul").next("select").length < 1 || $(this).hasClass('disabled')) {
					return;
				}
				var value = unescape($(this).data("value")),
					selector = $(this).closest("ul").next("select");
				if ($(this).closest("li").hasClass("active")) {
					$(this).closest("li").removeClass("active");
					selector.children("option:selected").removeAttr("selected");
					selector.val('');
				} else {
					$(this).closest("ul").children("li").removeClass("active");
					$(this).closest("li").addClass("active");
					selector.children("option:selected").removeAttr("selected");
					selector.children("option[value='" + value + "']").attr("selected", "selected");
					selector.val(selector.children("option[value='" + value + "']").val());
				}
				selector.change();
			});
			$(document).on('wc_variation_form', '.variations_form', function() {
				$(this).addClass('vf_init');
				if ($(this).find('.filter-item-list').length < 1) {
					return;
				}
				$(this).find('.variations select').trigger('focusin');
			});
			$(document).on('updated_wc_div', function() {
				$('.woocommerce-cart-form .porto-lazyload').themePluginLazyLoad();
			});
			$(document).on('found_variation reset_data', '.variations_form', function(e, args) {
				// attribute description
				if ($(this).find('.product-attr-description').length) {
					if (typeof args == 'undefined') {
						$(this).find('.product-attr-description').removeClass('active');
					} else {
						$(this).find('.product-attr-description').addClass('active');
						$(this).find('.product-attr-description .attr-desc').removeClass('active');
						$(this).find('.product-attr-description .attr-desc[data-attrid="' + $(this).find('.variations select').val() + '"]').addClass('active');
					}
				}

				if ($(this).find(".filter-item-list").length < 1) {
					return;
				}
				$(this).find(".filter-item-list").each(function() {
					if ($(this).next("select").length < 1) {
						return;
					}
					var selector = $(this).next("select"),
						//html = '',
						$list = $(this);
					$list.find('li.active').removeClass('active');
					$list.find('.filter-color, .filter-item').removeClass('enabled').removeClass('disabled');
					selector.children("option").each(function() {
						/*var isColor = typeof $(this).data('color') != 'undefined' ? true : false,
							isImage = typeof $(this).data('image') != 'undefined' ? true : false,
						spanClass = isColor ? "filter-color" : ( isImage ? "filter-item filter-image" : "filter-item" );*/
						if (!$(this).val()) {
							return;
						}
						$list.find('[data-value="' + $(this).val() + '"]').addClass('enabled');
						if ($(this).val() == selector.val()) {
							$list.find('[data-value="' + $(this).val() + '"]').parent().addClass('active');
						}
						/*html += '<li';
						if ($(this).val() == selector.val()) {
							html += ' class="active"';
						}
						html += '><a href="#" data-value="'+ escape( $(this).val() ) +'" class="' + spanClass + '"';
						if (isColor) {
							html += ' style="background-color: #' + escape( $(this).data('color').replace('#','') ) + '"';
						}
						if (isImage) {
							html += ' style="background-image:url(' + $(this).data('image') + ')"';
						}
						html += '>';
						if (!isColor) {
							html += $(this).text();
						}
						html += '</a></li>';*/
					});
					$list.find('.filter-color:not(.enabled), .filter-item:not(.enabled)').addClass('disabled');
					//$(this).html(html);
				});
			});

			// daily sale
			$(document).on('found_variation reset_data', '.variations_form', function(e, obj) {
				var $timer = $(this).closest('.product').find('.sale-product-daily-deal.for-some-variations');
				if (!$timer.length) {
					return;
				}
				if (obj && obj.is_purchasable && typeof obj.porto_date_on_sale_to != 'undefined' && obj.porto_date_on_sale_to) {
					var saleTimer = $timer.find('.porto_countdown-dateAndTime');
					if (saleTimer.data('terminal-date') != obj.porto_date_on_sale_to) {
						var newDate = new Date(obj.porto_date_on_sale_to);
						saleTimer.porto_countdown('option', {until: newDate});
						saleTimer.data('terminal-date', obj.porto_date_on_sale_to);
					}
					$timer.slideDown();
				} else {
					if ($timer.is(':hidden')) {
						$timer.hide();
					} else {
						$timer.slideUp();
					}
				}
			});

			$('body').on('click', '.product-attr-description > a', function(e) {
				e.preventDefault();
				$(this).next().stop().slideToggle(400);
			});
		});

	}).apply(this, [window.theme, jQuery]);


	// Woocommerce Category Filter
	(function(theme, $) {

		/**
		 Copyright (c) 2010, All Right Reserved, Wong Shek Hei @ shekhei@gmail.com
		 License: GNU Lesser General Public License (http://www.gnu.org/licenses/lgpl.html)
		 **/
		var expr = /[.#\w].([\S]*)/g, classexpr = /(?!(\[))(\.)[^.#[]*/g, idexpr = /(#)[^.#[]*/, tagexpr = /^[\w]+/, varexpr = /(\w+?)=(['"])([^\2$]*?)\2/, simpleselector = /^[\w]+$/, parseSelector = function (d) {
			for (var c = {sel: [], val: []}, a = [], j = !1, h = "", e = [], f = 0, m = d.length; f < m; f++) {
				var g = d.charAt(f);
				if (j)if ("\\" === g && f + 1 < d.length)e.push(d.charAt(++f)); else if (h === g)h = "", e.push(g); else if (("'" === g || '"' === g) && "" === h)h = g, e.push(g); else if ("]" === g && "" === h)c.val.push(e.join("")), e = [], j = !1; else {
					if ("]" !== g || "" !== h)"" === h && "," === g ? (c.val.push(e.join("")),
						e = []) : e.push(g)
				} else"\\" === g && f + 1 < d.length ? j && e.push(d.charAt(++f)) : "[" === g && "" === h ? j = !0 : " " === g || "+" === g ? (c.sel = c.sel.join(""), a.push(c), "+" === g && a.push({sel: "+", val: ""}), c = {sel: [], val: []}) : " " !== g && "]" !== g && c.sel.push(g)
			}
			if (0 != c.sel.length || 0 != c.val.length)c.sel = c.sel.join(""), a.push(c);
			for (f = 0; f < a.length; f++) {
				c = a[f].sel;
				if ("+" === c)b.tag = c; else {
					var b = [];
					b.tag = tagexpr.exec(c);
					b.id = idexpr.exec(c);
					b.id && $.isArray(b.id) && (b.id = b.id[0].substr(1));
					b.tag || (b.tag = "div");
					b.vars = [];
					for (d = 0; d < a[f].val.length; d++)h =
						a[f].val[d].indexOf("="), j = a[f].val[d].substr(0, h), h = a[f].val[d].substr(h + 1), h = h.replace(/^[\s]*[\"\']*|[\"\']*[\s]*$/g, ""), "text" === j ? b.text = h : b.vars.push([j, h]);
					c = c.match(classexpr);
					j = [];
					if (c) {
						for (d = 0; d < c.length; d++)j.push(c[d].substr(1));
						b.className = j.join(" ")
					}
				}
				a[f] = b
			}
			return a
		}, rmFromParent = function (d) {
			var c = d.parentNode, a = d.nextSibling;
			c.removeChild(d);
			return a ? function () {
				c.insertBefore(d, a)
			} : function () {
				c.appendChild(d)
			}
		}, nonArrVer = function (d, c) {
			var a = [], a = simpleselector.test(d) ? [
					{tag: d}
				] : parseSelector(d),
				j = [];
			"undefined" === typeof c && (c = 1);
			for (var h = [], e = [], f = [], m = document.createElement("div"), g = 0, b = 0; b < a.length; b++) {
				if ("+" == a[b].tag)e = f.slice(), --g; else {
					for (var l = 0; l < c; l++) {
						var k;
						if ("input" == a[b].tag) {
							k = [];
							k.push("<" + a[b].tag);
							a[b].id && k.push("id='" + a[b].id + "'");
							a[b].className && (k.push("class='" + a[b].className), b + 1 === a.length && k.push(lastClass), k.push("'"));
							if (a[b].vars)for (var n = 0; n < a[b].vars.length; n++)k.push(a[b].vars[n][0] + "='" + a[b].vars[n][1] + "'");
							a[b].text && k.push("value='" + a[b].text + "'");
							k.push("/>");
							f[l] = e[l];
							e[l] ? (e[l].innerHTML += k.join(" "), e[l] = e[l].lastChild) : (m.innerHTML = k.join(" "), e[l] = m.removeChild(m.firstChild))
						} else {
							k = document.createElement(a[b].tag);
							if (a[b].vars)for (var n = 0; n < a[b].vars.length; n++)k.setAttribute(a[b].vars[n][0], a[b].vars[n][1]);
							a[b].id && (k.id = a[b].id);
							a[b].className && (k.className = a[b].className);
							a[b].text && k.appendChild(document.createTextNode(a[b].text));
							f[l] = e[l];
							e[l] = e[l] ? e[l].appendChild(k) : k
						}
					}
					g++ || Array.prototype.push.apply(h, e);
				}
				j = $.merge(j, e);
			}
			return $(h)
		}, arrVer = function (d, c, a) {
			for (var j = d.match(/%[^%]*%/g) || [], h = [], e = 0; e < c.length; e++) {
				for (var f = d, m = 0; m < j.length; m++)var g = j[m].substr(1, j[m].length - 2), f = f.replace(j[m], c[e][g]);
				h = $.merge(h, nonArrVer(f, a))
			}
			return $(h)
		};

		$.porto_jseldom = function (d) {
			if (2 == arguments.length && $.isPlainObject(arguments[1]))return arrVer.apply(this, [arguments[0], [arguments[1]]]);
			if (1 == arguments.length || 2 == arguments.length && !$.isArray(arguments[1]))return nonArrVer.apply(this, arguments);
			if (2 == arguments.length)return arrVer.apply(this, arguments)
		};

		var refreshPriceSlider = function() {

			var $price_slider = $('.price_slider');

			if ($price_slider.length) {
				// woocommerce_price_slider_params is required to continue, ensure the object exists
				if ( typeof woocommerce_price_slider_params === 'undefined' ) {
					return false;
				}

				// Get markup ready for slider
				$( 'input#min_price, input#max_price' ).hide();
				$( '.price_slider, .price_label' ).show();

				// Price slider uses jquery ui
				var min_price = $( '.price_slider_amount #min_price' ).data( 'min' ),
					max_price = $( '.price_slider_amount #max_price' ).data( 'max' ),
					current_min_price = parseInt( $( '.price_slider_amount #min_price').val() ? $( '.price_slider_amount #min_price').val() : min_price, 10 ),
					current_max_price = parseInt( $( '.price_slider_amount #max_price').val() ? $( '.price_slider_amount #max_price').val() : max_price, 10 );

				$( '.price_slider' ).slider({
					range: true,
					animate: true,
					min: min_price,
					max: max_price,
					values: [ current_min_price, current_max_price ],
					create: function() {

						$( '.price_slider_amount #min_price' ).val( current_min_price );
						$( '.price_slider_amount #max_price' ).val( current_max_price );

						$( document.body ).trigger( 'price_slider_create', [ current_min_price, current_max_price ] );
					},
					slide: function( event, ui ) {

						$( 'input#min_price' ).val( ui.values[0] );
						$( 'input#max_price' ).val( ui.values[1] );

						$( document.body ).trigger( 'price_slider_slide', [ ui.values[0], ui.values[1] ] );
					},
					change: function( event, ui ) {

						$( document.body ).trigger( 'price_slider_change', [ ui.values[0], ui.values[1] ] );
					}
				});
			}

			// remove filter loading
			$('.yith-woo-ajax-navigation, .yith-wcan-list-price-filter').removeClass('loading');
		};

		var categoryAjaxProcess = function(href, updateSelect2) {
			var shop_before = '.shop-loop-before',
				$shop_before = $(shop_before),
				shop_after = '.shop-loop-after:not(.is-shortcode)',
				shop_container = '.archive-products .products:not(.is-shortcode)',
				shop_info = '.archive-products .woocommerce-info',
				//$wrapper = $('#content.site-main'),
				$shop_parent = $shop_before.parent(),
				$shop_container = $(shop_container),
				$sticky_sidebar = $('.sidebar [data-plugin-sticky]'),
				show_toolbar = $shop_before.data('show'),
				horizontal_filter = '.porto-product-filters:not(.style2)';

			if (show_toolbar)
				$(shop_before + ',' + shop_after).stop(true).fadeTo('400','1').block({message: null, overlayCSS: {opacity: 0.2}});
			if (js_porto_vars.use_skeleton_screen.indexOf('shop') == -1) {
				if ($shop_container.length) {
					$shop_container.addClass('yith-wcan-loading');
					if (!$shop_container.children('.porto-loading-icon').length) {
						$shop_container.append('<i class="porto-loading-icon"></i>');
					}
				} else {
					$(shop_info).html('').addClass('yith-wcan-loading products');
					if (!$(shop_info).children('.porto-loading-icon').length) {
						$(shop_info).append('<i class="porto-loading-icon"></i>');
					}
				}
			} else {
				if ($shop_container.length) {
					$shop_container.addClass('skeleton-body');
					var lg_cols;
					for(var i = 1; i <= 8; i++) {
						if ($shop_container.hasClass('pcols-lg-' + i)) {
							lg_cols = i;
							break;
						}
					}
					if (lg_cols) {
						var skeleton_html = '',
							product_class = 'product product-col';
						$shop_container.empty();
						if ($shop_container.data('product_layout')) {
							product_class += ' ' + escape( $shop_container.data('product_layout') );
						}
						for (var i = 0; i < lg_cols * 3; i++) {
							$shop_container.append('<li class="' + product_class + '"></li>');
						}
					} else {
						$shop_container.find('.product-col').empty();
					}
				}
			}

			if ($(horizontal_filter).length) {
				$(horizontal_filter).block({message: null, overlayCSS: {opacity: 0.2}});
			}

			if ($sticky_sidebar.get(0)) {
				//$shop_parent.css('min-height', $sticky_sidebar.height());
				theme.refreshStickySidebar(false);
			}

			theme.scrolltoContainer(show_toolbar ? ($shop_before.hasClass('sticky') && $shop_before.prev('.filter-placeholder').length ? $shop_before.prev('.filter-placeholder') : $shop_before) : $shop_container);

			$('.yith-woo-ajax-navigation, .yith-wcan-list-price-filter').addClass('loading');

			var cart_content, widget_cart;

			if (widget_cart = $('.sidebar-content .widget_shopping_cart').get(0)) {
				cart_content = $(widget_cart).html();
			}

			$.ajax({
				url: href,
				data: {portoajax: true},
				type: "POST",
				success: function (response) {

					var $parent = $shop_container.parent(),
						$response = $(response);

					if ($sticky_sidebar.get(0))
						$shop_parent.css('min-height', 0);

					// products container
					if ($response.find(shop_container).length) {
						if ($shop_container.length && $shop_container.data('infinitescroll')) {
							try {
								$shop_container.data('infinitescroll').destroy();
							} catch (e) {
							}
						}
						$parent.html($response.find(shop_container));
					} else {
						$parent.html($response.find('.woocommerce-info'));
						$parent.find('.woocommerce-info').addClass('products');
					}

					if ($(shop_before + ',' + shop_after).get(0))
						$(shop_before + ',' + shop_after).stop(true).css('opacity', '1').unblock();

					// top toolbar
					if ($response.find(shop_before).length) {
						if ($(shop_before).length == 0) {
							$.porto_jseldom(shop_before).insertBefore($(shop_container));
						}

						$(shop_before)
							.html($response.find(shop_before).html())
							.show();
					} else {
						$(shop_before).empty();
					}

					// reset variations form
					porto_woocommerce_variations_init($parent);

					// horizontal filter
					if ($response.find(horizontal_filter).length) {
						$(horizontal_filter).html($response.find(horizontal_filter).html());
					}
					$(horizontal_filter).unblock();

					// bottom toolbar
					if ($response.find(shop_after).length) {
						if ($(shop_after).length == 0) {
							$.porto_jseldom(shop_after).insertAfter($(shop_container));
						}
						$(shop_after).html($response.find(shop_after).html()).show();
					} else {
						$(shop_after).empty();
					}

					// infinite scroll
					if (typeof theme.PostsInfinite !== 'undefined') {
						theme.PostsInfinite.initialize($(shop_container));
					}

					$('.sidebar-content').each(function(index) {
						var $this = $(this),
							$that = $($response.find('.sidebar-content').get(index));

						$this.html($that.html());

						if (typeof updateSelect2 != 'undefined' && updateSelect2) {
							// Use Select2 enhancement if possible
							if ( jQuery().selectWoo ) {
								var porto_wc_layered_nav_select = function() {
									$this.find( 'select.woocommerce-widget-layered-nav-dropdown' ).each(function() {
										$(this).selectWoo( {
											placeholder: $(this).find('option').eq(0).text(),
											minimumResultsForSearch: 5,
											width: '100%',
											allowClear: typeof $(this).attr('multiple') != 'undefined' && $(this).attr('multiple') == 'multiple' ? 'false' : 'true'
										} );
									});
								};
								porto_wc_layered_nav_select();
							}
							$('body').children('span.select2-container').remove();
						}
					});

					var $script = $response.filter('script:contains("var woocommerce_price_slider_params")').first();
					if ($script && $script.length && $script.text().indexOf('{') !== -1 && $script.text().indexOf('}') !== -1) {
						var arrStr = $script.text().substring($script.text().indexOf('{'), $script.text().indexOf('}') + 1);
						window.woocommerce_price_slider_params = JSON.parse(arrStr);
					} else {
						window.woocommerce_price_slider_params = undefined;
					}

					//update browser history (IE doesn't support it)
					if (!navigator.userAgent.match(/msie/i)) {
						window.history.pushState({"pageTitle": response.pageTitle}, "", href);
					}

					//trigger ready event
					$(document).trigger("yith-wcan-ajax-filtered");

					if (widget_cart = $('.sidebar-content .widget_shopping_cart').get(0)) {
						$('.sidebar-content .widget_shopping_cart').html(cart_content);
						if ( $.cookie( 'woocommerce_items_in_cart' ) > 0 ) {
							$( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).show();
						} else {
							$( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).hide();
						}
					}
				}
			});
		};

		function porto_update_url_param(uri, key, value) {
			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";
			if (uri.match(re)) {
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			} else {
				return uri + separator + key + "=" + value;
			}
		}

		var categoryAjax = function () {
			// add class in price filter widget
			$('.widget_price_filter').addClass('yith-wcan-list-price-filter');

			if (theme.category_ajax) {

				// order by ajax
				$( '.woocommerce-ordering' ).off( 'change', 'select.orderby' ).on( 'change', 'select.orderby', function(e) {
					e.preventDefault();

					var $this = $(this),
						$form = $this.closest('form'),
						href = '?' + $form.serialize();

					categoryAjaxProcess(href);
				});

				// view ajax
				$( '.woocommerce-viewing' ).off( 'change', 'select.count' ).on( 'change', 'select.count', function(e) {
					e.preventDefault();

					var $this = $(this),
						$form = $this.closest('form'),
						href = '?' + $form.serialize();

					categoryAjaxProcess(href);
				});

				// pagination ajax
				$( '.woocommerce-pagination:not(.load-more)' ).each(function() {
					if ($(this).closest('.porto-products').length) {
						return;
					}
					$(this).off( 'click', 'a.page-numbers' ).on( 'click', 'a.page-numbers', function(e) {
						e.preventDefault();
						var href = this.href;
						categoryAjaxProcess(href);
					});
				});

				// yith filter
				$(document).off('click', '.yith-wcan a').on('click', '.yith-wcan a', function (e) {
					$(this).yith_wcan_ajax_filters(e, this);
				});

				// price filter ajax
				$( '.widget_price_filter .price_slider_wrapper').off( 'click', '.button').on( 'click', '.button', function(e) {
					e.preventDefault();

					var $this = $(this),
						$form = $this.closest('form'),
						action = $form.attr('action'),
						href = action + ( -1 === action.indexOf('?') ? '?' : '&' ) + $form.serialize(),
						$count = $('.woocommerce-viewing select.count');

					if ($count.length) {
						var count = $('.woocommerce-viewing select.count').val();
						if (count != $count.find('option:not([disabled]):first').val()) {
							href += '&count=' + count;
						}
					}

					$('.widget_price_filter').removeClass('yith-wcan-list-price-filter');

					categoryAjaxProcess(href);
				});
				$( '.porto_widget_price_filter').off( 'click', '.button').on( 'click', '.button', function(e) {
					e.preventDefault();

					var $this = $(this),
						$form = $this.closest('form'),
						action = $form.attr('action'),
						$count = $('.woocommerce-viewing select.count'),
						hrefArr = $form.serializeArray(),
						href = action;
					$.each(hrefArr, function(i, field){
						if ($.trim(field.value)) {
							if (action.indexOf('?') == -1 && href == action) {
								href += '?';
							} else {
								href += '&';
							}
							href += (field.name + "=" + $.trim(field.value));
						}
					});
					if ($count.length) {
						var count = $('.woocommerce-viewing select.count').val();
						if (count != $count.find('option:not([disabled]):first').val()) {
							if (href.indexOf('?') == -1) {
								href += '?count=' + count;
							} else {
								href += '&count=' + count;
							}
						}
					}

					categoryAjaxProcess(href);
				});

				// layerd nav filter
				$('.widget_layered_nav, .widget_rating_filter, .widget_layered_nav_filters').off('click', 'a').on('click', 'a', function(e) {
					if ($(this).hasClass('yit-wcan-select-open'))
						return;

					e.preventDefault();

					var $this = $(this),
						href = $this.attr('href'),
						$count = $('.woocommerce-viewing select.count');

					if ($this.hasClass('yith-wcan-reset-navigation') && !$('.archive-products .products:not(.is-shortcode)').length) {
						window.location.href = href;
						return false;
					}

					if ($count.length) {
						var count = $('.woocommerce-viewing select.count').val();
						if (count != $count.find('option:not([disabled]):first').val()) {
							//href += '&count=' + count;
							href = porto_update_url_param(href, 'count', count);
						}
					}

					var yith_select = $this.closest('.yith-wcan-select');
					if (yith_select.get(0)) {
						yith_select.parent().css({"opacity":0, "z-index":-1});
					}

					categoryAjaxProcess(href);

					return false;
				});

				$('.widget_layered_nav select').off('change').on('change', function(e) {
					e.preventDefault();

					var $this = $(this),
						name = $this.closest('form').find('input[type=hidden]').length ? $this.closest('form').find('input[type=hidden]').attr('name').replace('filter_', '') : $this.attr('class').replace('dropdown_layered_nav_', ''),
						slug = $this.val(),
						href,
						$count = $('.woocommerce-viewing select.count');

					href = window.location.href;
					href = href.replace(/\/page\/\d+/, "").replace("&amp;", '&').replace("%2C", ',');

					href = porto_update_url_param( href, 'filtering', '1' );
					href = porto_update_url_param( href, 'filter_' + name, slug );
					if ($count.length) {
						var count = $('.woocommerce-viewing select.count').val();
						if (count != $count.find('option:not([disabled]):first').val()) {
							href = porto_update_url_param( href, 'count', count );
						}
					}

					categoryAjaxProcess(href, name);
					return false;
				});
			} else {
				$(document).on('change', '.woocommerce-viewing select.count', function() {
					$(this).closest('form').submit();
				});
			}
		};

		var ajaxFiltered = function(initLoad) {
			var shop_before = '.shop-loop-before',
				shop_after = '.shop-loop-after',
				shop_container = '.archive-products .products',
				$shop_parent = $(shop_before).parent(),
				$sticky_sidebar = $('.sidebar [data-plugin-sticky]');

			if ($sticky_sidebar.get(0)) {
				$shop_parent.css('min-height', 0);
			}

			if ($(shop_before + ',' + shop_after).get(0))
				$(shop_before + ',' + shop_after).stop(true).fadeTo('400','1').unblock();
			if ($(shop_container).find('.product').get(0) || $(shop_after).closest('.porto-products').length) {
				$(shop_before + ',' + shop_after).show().data('show', true);
			} else {
				$(shop_before + ',' + shop_after).hide().data('show', false);
				if ($(shop_before).find('.porto-product-filters.style2').length) {
					$(shop_before).show().data('show', true);
				}
			}

			if (typeof initLoad == 'undefined' || !initLoad) {
				porto_init();
				porto_woocommerce_init();
			}

			$( '.woocommerce-ordering' ).off( 'change', 'select.orderby' ).on( 'change', 'select.orderby', function() {
				$( this ).closest( 'form' ).submit();
			});

			// category ajax
			refreshPriceSlider();
			categoryAjax();
		};

		// initialize woocommerce actions after skeleton loading
		var skeletonLoadingTrigger;
		$('.skeleton-loading').on('skeleton-loaded', function() {
			var $this = $(this);
			if (skeletonLoadingTrigger) {
				theme.deleteTimeout(skeletonLoadingTrigger);
			}
			porto_woocommerce_variations_init($this);
			skeletonLoadingTrigger = theme.requestTimeout(function() {
				porto_woocommerce_init();
				refreshPriceSlider();
				if ($('body').hasClass('single-product')) {
					theme.WooVariationForm.init();
					if ($('.product-image-slider').length && $('.product-image-slider').data('owl.carousel')) {
						$('.product-image-slider').trigger('refresh.owl.carousel');
					} else {
						theme.WooProductImageSlider.initialize();
					}
					$('.wc-tabs-wrapper, .woocommerce-tabs, #rating').trigger('init');
				}

				// refresh cart content
				if ($this.find('.widget_shopping_cart_content').length) {
					$(document.body).trigger('wc_fragment_refresh');
				}
			}, 100);
		});

		$(function() {
			// yith woo ajax filter events
			if (typeof yith_wcan != 'undefined') {
				yith_wcan.container = '.archive-products .products';
				yith_wcan.pagination = '.shop-loop-before';
				yith_wcan.result_count = '.shop-loop-after';
			}

			$(document).on('click', '.yith-wcan a', function(e){
				// add price filter loading
				var shop_before = '.shop-loop-before',
					$shop_before = $(shop_before),
					shop_after = '.shop-loop-after',
					shop_container = '.archive-products .products',
					shop_info = '.archive-products .woocommerce-info',
					//$shop_parent = $shop_before.parent(),
					$sticky_sidebar = $('.sidebar [data-plugin-sticky]'),
					show_toolbar = $shop_before.data('show');

				if (show_toolbar)
					$(shop_before + ',' + shop_after).stop(true).show().fadeTo('400','0.8').block({message: null, overlayCSS: {opacity: 0.2}});
				if ($(shop_container).length) {
					$(shop_container).html('').addClass('yith-wcan-loading');
					if (!$(shop_container).children('.porto-loading-icon').length) {
						$(shop_container).append('<i class="porto-loading-icon"></i>');
					}
				} else {
					$(shop_info).html('').addClass('yith-wcan-loading products');
					if (!$(shop_info).children('.porto-loading-icon').length) {
						$(shop_info).append('<i class="porto-loading-icon"></i>');
					}
				}

				if ($sticky_sidebar.get(0)) {
					//$shop_parent.css('min-height', $sticky_sidebar.height());
					theme.refreshStickySidebar(false);
				}
				$('.yith-woo-ajax-navigation, .yith-wcan-list-price-filter').addClass('loading');
				theme.scrolltoContainer(show_toolbar ? ($shop_before.hasClass('sticky') && $shop_before.prev('.filter-placeholder').length ? $shop_before.prev('.filter-placeholder') : $shop_before) : $(shop_container));
			});

			$(document).ready(function() {
				ajaxFiltered(true);
			});

			$(document).on('yith-wcan-ajax-filtered', function() {
				ajaxFiltered();
			});

			//categoryAjax();

			// product filter ajax
			if (theme.prdctfltr_ajax) {
				// select count
				$(document).on( 'change', '.woocommerce-viewing select.count', function() {
					$( this ).closest( 'form' ).submit();
				});
				// page number
				$(document).on( 'click', '.woocommerce-pagination:not(.load-more) a.page-numbers', function(e) {
					var $shop_before = $('.shop-loop-before');
					theme.scrolltoContainer($shop_before.hasClass('sticky') && $shop_before.prev('.filter-placeholder').length ? $shop_before.prev('.filter-placeholder') : $shop_before);
				});
			}

			// woocommerce grid / list
			$(document).on('click', '.gridlist-toggle #grid, .gridlist-toggle #list', function(e) {
				e.preventDefault();
				var $this = $(this);
				if ($this.hasClass('active')) {
					return false;
				}
				$('.gridlist-toggle #grid, .gridlist-toggle #list').removeClass('active');
				$this.addClass('active');
				if ($.cookie) {
					$.cookie('gridcookie', $this.attr('id'), { path: '/' });
				}
				if (js_porto_vars.use_skeleton_screen.indexOf('shop') != -1) {
					$('.gridlist-toggle').parent().parent().find('ul.products').removeClass('grid').removeClass('list').addClass($this.attr('id'));
				}
				categoryAjaxProcess(window.location.href);
				return false;
			});
		});

	}).apply(this, [window.theme, jQuery]);


	// Woocommerce Product Image Slider
	(function(theme, $) {

		theme = theme || {};

		var duration = 300,
			flag = false;

		$.extend(theme, {

			WooProductImageSlider: {

				defaults: {
					elements: '.product-image-slider'
				},

				initialize: function($elements) {
					this.$elements = ($elements || $(this.defaults.elements));

					this.build();

					return this;
				},

				build: function() {
					var self = this,
						thumbs_count = theme.product_thumbs_count;

					if (theme.product_zoom && (!('ontouchstart' in document) || (('ontouchstart' in document) && theme.product_zoom_mobile))) {
						var zoomConfig = {
							responsive: true,
							zoomWindowFadeIn: 200,
							zoomWindowFadeOut: 100,
							zoomType: js_porto_vars.zoom_type,
							cursor: 'grab'
						};

						if (js_porto_vars.zoom_type == 'lens') {
							zoomConfig.scrollZoom = js_porto_vars.zoom_scroll;
							zoomConfig.lensSize = js_porto_vars.zoom_lens_size;
							zoomConfig.lensShape = js_porto_vars.zoom_lens_shape;
							zoomConfig.containLensZoom = js_porto_vars.zoom_contain_lens;
							zoomConfig.lensBorderSize = js_porto_vars.zoom_lens_border;
							zoomConfig.borderColour = js_porto_vars.zoom_border_color;
						}

						if (js_porto_vars.zoom_type == 'inner') {
							zoomConfig.borderSize = 0;
						} else {
							zoomConfig.borderSize = js_porto_vars.zoom_border;
						}
					}

					self.$elements.each(function() {
						var $this = $(this),
							$product = $this.closest('.product');
						if (!$product.length) {
							$product = $this.closest('.product_layout');
						}
						var $thumbs_slider = $product.find('.product-thumbs-slider'),
							$thumbs = $product.find('.product-thumbnails-inner'),
							$thumbs_vertical_slider = $product.find('.product-thumbs-vertical-slider'),
							currentSlide = 0,
							count = $this.find('> *').length;

						$this.find('> *:first-child').waitForImages(true).done(function() {

							$thumbs_slider.owlCarousel({
								rtl: theme.rtl,
								loop : false,
								autoplay : false,
								items : thumbs_count,
								nav: false,
								navText: ["", ""],
								dots: false,
								rewind: true,
								margin: 8,
								stagePadding: 1,
								lazyLoad: true,
								onInitialized: function() {
									self.selectThumb(null, $thumbs_slider, 0);
									if ($thumbs_slider.find('.owl-item').length >= thumbs_count)
										$thumbs_slider.append('<div class="thumb-nav"><div class="thumb-prev"></div><div class="thumb-next"></div></div>');
								}
							}).on('click', '.owl-item', function() {
								self.selectThumb($this, $thumbs_slider, $(this).index());
							});
							if ($thumbs_vertical_slider.length > 0) {
								$thumbs_vertical_slider.slick({
									dots: false,
									vertical: true,
									slidesToShow: thumbs_count > 2 ? thumbs_count - 1 : thumbs_count,
									slidesToScroll: 1
								}).on('click', '.img-thumbnail', function() {
									self.selectVerticalSliderThumb($this, $thumbs_vertical_slider, $(this).data('slick-index'));
								});
								self.selectVerticalSliderThumb(null, $thumbs_vertical_slider, 0);
								if ($thumbs_vertical_slider.find('.porto-lazyload').length) {
									theme.requestTimeout(function() {
										$thumbs_vertical_slider.find('.slick-cloned .porto-lazyload:not(.lazy-load-loaded)').each(function() {
											$(this).attr('src', $(this).data('oi')).removeAttr('data-oi').addClass('lazy-load-loaded');
										});
									}, 100);
								}
							}

							self.selectVerticalThumb(null, $thumbs, 0);
							$thumbs.off('click', '.img-thumbnail').on('click', '.img-thumbnail', function() {
								self.selectVerticalThumb($this, $thumbs, $(this).index());
							});

							$thumbs_slider.off('click', '.thumb-prev').on('click', '.thumb-prev', function(e) {
								var currentThumb = $thumbs_slider.data('currentThumb');
								self.selectThumb($this, $thumbs_slider, --currentThumb);
							});
							$thumbs_slider.off('click', '.thumb-next').on('click', '.thumb-next', function(e) {
								var currentThumb = $thumbs_slider.data('currentThumb');
								self.selectThumb($this, $thumbs_slider, ++currentThumb);
							});

							var links = [];
							if (theme.product_image_popup) {
								var i = 0;
								$this.find('img').each(function() {
									var slide = {};

									slide.src = $(this).attr('href');
									slide.title = $(this).attr('alt');

									links[i] = slide;
									i++;
								});
							}

							var itemsCount = typeof $this.data('items') != 'undefined' ? $this.data('items') : 1,
								itemsResponsive = typeof $this.data('responsive') != 'undefined' ? $this.data('responsive') : {},
								centerItem = typeof $this.data('centeritem') != 'undefined' ? true : false;
							for (var itemCount in itemsResponsive) {
								itemsResponsive[itemCount] = { items: itemsResponsive[itemCount] };
							}
							$this.owlCarousel({
								rtl: theme.rtl,
								loop : (count > 1) ? true : false,
								autoplay : false,
								items : itemsCount,
								responsive: itemsResponsive,
								autoHeight : true,
								nav: true,
								navText: ["", ""],
								dots: false,
								rewind: true,
								lazyLoad: true,
								center: centerItem,
								onInitialized : function() {
									//$this.find('.cloned .porto-lazyload:not(.lazy-load-loaded)').themePluginLazyLoad();
									if (theme.product_zoom && (!('ontouchstart' in document) || (('ontouchstart' in document) && theme.product_zoom_mobile))) {
										$this.find('img').each(function() {
											var $this = $(this);
											zoomConfig.zoomContainer = $this.parent();
											if ($.fn.elevateZoom) {
												$this.elevateZoom(zoomConfig);
											} else {
												setTimeout(function() {
													if ($.fn.elevateZoom) {
														$this.elevateZoom(zoomConfig);
													}
												}, 1000);
											}
										});
									}
								},
								onTranslate : function(event) {
									currentSlide = event.item.index - $this.find('.cloned').length / 2;
									currentSlide = (currentSlide + event.item.count) % event.item.count;
									self.selectThumb(null, $thumbs_slider, currentSlide);
									self.selectVerticalThumb(null, $thumbs, currentSlide);
									self.selectVerticalSliderThumb(null, $thumbs_vertical_slider, currentSlide);

									/*var $obj = event.relatedTarget.items(currentSlide).find('img.owl-lazy:not(.owl-lazy-loaded)');
									if ($obj.length) {
										var src = $obj.attr('href'),
											elevateZoom = $obj.data('elevateZoom'),
											smallImage = $obj.data('src') ? $obj.data('src') : $obj.attr('src');
										if (typeof elevateZoom != 'undefined') {
											elevateZoom.swaptheimage(smallImage, src);
										}
									}*/
								},
								onRefreshed: function() {
									if (theme.product_zoom && (!('ontouchstart' in document) || (('ontouchstart' in document) && theme.product_zoom_mobile))) {
										$this.find('img').each(function() {
											var $this = $(this),
												src = typeof $this.attr('href') != 'undefined' ? $this.attr('href') : ($this.data('oi') ? $this.data('oi') : $this.attr('src')),
												elevateZoom = $this.data('elevateZoom'),
												smallImage = $this.data('src') ? $this.data('src') : ($this.data('oi') ? $this.data('oi') : $this.attr('src'));
											if (typeof elevateZoom != 'undefined') {
												elevateZoom.startZoom();
												elevateZoom.swaptheimage(smallImage, src);
											} else if ($.fn.elevateZoom) {
												zoomConfig.zoomContainer = $this.parent();
												$this.elevateZoom(zoomConfig);
											}
										});
									}
								}
							});

							$this.data('links', links);

							if (theme.product_image_popup) {
								var $zoom_buttons = $this.next();
								$zoom_buttons.off('click').on('click', function(e) {
									e.preventDefault();
									if ($.fn.magnificPopup) {
										$.magnificPopup.close();
										$.magnificPopup.open($.extend(true, {}, theme.mfpConfig, {
											items: $this.data('links'),
											gallery: {
												enabled: true
											},
											type: 'image'
										}), currentSlide);
									}
								});
							}
						});
					});

					return self;
				},

				selectThumb: function($image_slider, $thumbs_slider, index) {
					if (flag || !$thumbs_slider.length ) return;

					flag = true;
					var len = $thumbs_slider.find('.owl-item').length,
						actives = [],
						i = 0;

					index = (index + len) % len;
					if ($image_slider) {
						$image_slider.trigger('to.owl.carousel', [index, duration, true]);
					}
					$thumbs_slider.find('.owl-item').removeClass('selected');
					$thumbs_slider.find('.owl-item:eq(' + index + ')').addClass('selected');
					$thumbs_slider.data('currentThumb', index);
					$thumbs_slider.find('.owl-item.active').each(function() {
						actives[i++] = $(this).index();
					});
					if ($.inArray(index, actives) == -1) {
						if (Math.abs(index - actives[0]) > Math.abs(index - actives[actives.length - 1])) {
							$thumbs_slider.trigger('to.owl.carousel', [(index - actives.length + 1) % len, duration, true]);
						} else {
							$thumbs_slider.trigger('to.owl.carousel', [index % len, duration, true]);
						}
					}
					flag = false;
				},

				selectVerticalSliderThumb: function($image_slider, $thumbs_vertical_slider, index) {
					if (flag || !$thumbs_vertical_slider.length ) return;
					flag = true;
					var len = $thumbs_vertical_slider[0].slick.slideCount,
						actives = [],
						i = 0;
					index = (index + len) % len;
					if ($image_slider) {
						$image_slider.trigger('to.owl.carousel', [index, duration, true]);
					}
					$thumbs_vertical_slider.find('.img-thumbnail').removeClass('selected');
					$thumbs_vertical_slider.find('.img-thumbnail:eq(' + index + ')').addClass('selected');
					$thumbs_vertical_slider.data('currentThumb', index);
					$thumbs_vertical_slider.find('.img-thumbnail.slick-active').each(function() {
						actives[i++] = $(this).index();
					});
					if ($.inArray(index, actives) == -1) {
						if (Math.abs(index - actives[0]) > Math.abs(index - actives[actives.length - 1])) {
							$thumbs_vertical_slider.get(0).slick.goTo((index - actives.length + 1) % len, false);
						} else {
							$thumbs_vertical_slider.get(0).slick.goTo(index % len, false);
						}
					}
					flag = false;
				},

				selectVerticalThumb: function($image_slider, $thumbs, index) {
					if (flag || !$thumbs.length ) return;
					flag = true;
					var len = $thumbs.find('.img-thumbnail').length,
						i = 0;

					index = (index + len) % len;
					if ($image_slider) {
						$image_slider.trigger('to.owl.carousel', [index, duration, true]);
					}
					$thumbs.find('.img-thumbnail').removeClass('selected');
					$thumbs.find('.img-thumbnail:eq(' + index + ')').addClass('selected');
					$thumbs.data('currentThumb', index);
					flag = false;
				}
			}

		});

	}).apply(this, [window.theme, jQuery]);


	// Woocommerce Quick View
	(function(theme, $) {

		theme = theme || {};

		$.extend(theme, {

			WooQuickView: {

				initialize: function() {

					this.events();

					return this;
				},

				events: function() {
					var self = this;

					$(document).on('click', '.quickview', function(e) {
						e.preventDefault();

						if (!$.fn.elevateZoom && !$('#porto-script-jquery-elevatezoom').length) {
							var js = document.createElement('script');
							js.id = 'porto-script-jquery-elevatezoom';
							$(js).appendTo('body').attr('src', js_porto_vars.ajax_loader_url.replace('/images/ajax-loader@2x.gif', '/js/libs/jquery.elevatezoom.min.js'));
						}

						var $this = $(this),
							pid = $this.attr('data-id');

						function init_quick_view_window() {

							var args = {
								href : theme.ajax_url,
								ajax : {
									data: {
										action: 'porto_product_quickview',
										variation_flag: typeof wc_add_to_cart_variation_params !== 'undefined',
										pid: pid,
										nonce: js_porto_vars.porto_nonce
									}
								},
								type : 'ajax',
								helpers : {
									overlay: {
										locked: true,
										fixed: true
									}
								},
								tpl: {
									error    : '<p class="fancybox-error">' + theme.request_error + '</p>',
									closeBtn : '<a title="' + js_porto_vars.popup_close + '" class="fancybox-item fancybox-close" href="javascript:;"></a>',
									next     : '<a title="' + js_porto_vars.popup_next + '" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
									prev     : '<a title="' + js_porto_vars.popup_prev + '" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
								},
								autoSize: true,
								autoWidth: true,
								afterShow: function(flag) {
									theme.requestTimeout(function() {
										if (typeof flag == 'undefined' || flag) {
											porto_woocommerce_init();
										}
										theme.WooProductImageSlider.initialize($('.quickview-wrap-' + pid).find('.product-image-slider'));
										// Variation Form
										var form_variation = $('.quickview-wrap-' + pid).find('form.variations_form');
										if (form_variation.length > 0) {
											form_variation.wc_variation_form();
											//form_variation.find("select option:selected").removeAttr("selected");
										}
										$(document.body).trigger('porto_init_countdown', [$('.quickview-wrap-' + pid)]);
									}, 200);
								},
								onUpdate: function() {
									theme.requestTimeout(function() {
										if (js_porto_vars.use_skeleton_screen.indexOf('quickview') == -1 || !js_porto_vars.quickview_skeleton) {
											porto_woocommerce_init();
										}
										var $slider = $('.quickview-wrap-' + pid).find('.product-image-slider');
										if (typeof $slider.data('owl.carousel') != 'undefined' && typeof $slider.data('owl.carousel')._invalidated != 'undefined')
											$slider.data('owl.carousel')._invalidated.width = true;
										$slider.trigger('refresh.owl.carousel');
										$(document.body).trigger('porto_init_countdown', [$('.quickview-wrap-' + pid)]);
									}, 300);
								}
							};
							if (js_porto_vars.use_skeleton_screen.indexOf('quickview') != -1 && js_porto_vars.quickview_skeleton) {
								delete args['href'];
								delete args['ajax'];
								args['type'] = 'inline';
								$.fancybox.open(
									js_porto_vars.quickview_skeleton,
									args
								);
								$.ajax({
									url: theme.ajax_url,
									type: 'post',
									dataType: 'html',
									data: {
										action: 'porto_product_quickview',
										variation_flag: typeof wc_add_to_cart_variation_params !== 'undefined',
										pid: pid,
										nonce: js_porto_vars.porto_nonce
									},
									success: function(res) {
										var $res = $(res);
										$res.waitForImages(function() {
											$('.skeleton-body.product').replaceWith($res);
											theme.WooQtyField.initialize();
											$(window).trigger('resize');
											args['afterShow'].call(false);
										});
									}
								});
							} else {
								$.fancybox(args);
							}
						}

						if ($.fn.fancybox) {
							init_quick_view_window();
						} else if (!$('#porto-script-jquery-fancybox').length) {
							var js1 = document.createElement('script');
							js1.id = 'porto-script-jquery-fancybox';
							$(js1).appendTo('body').on('load', function() {
								init_quick_view_window();
							}).attr('src', js_porto_vars.ajax_loader_url.replace('/images/ajax-loader@2x.gif', '/js/libs/jquery.fancybox.min.js'));
						}

						return false;
					});

					// ajax add to cart on quickview
					if (typeof wc_add_to_cart_params != 'undefined') {
						$(document.body).on('click', '.single-product .single_add_to_cart_button:not(.disabled)', function(e) {
							if ($(this).closest('.single-product').hasClass('product-type-external') || $(this).closest('.single-product').hasClass('product-type-grouped')) {
								return true;
							}
							e.preventDefault();

							var $button 		= $(this),
								product_id 		= $button.val(),
								variation_id	= $button.closest('form').find('input[name="variation_id"]').val(),
								quantity 		= $button.closest('form').find('input[name="quantity"]').val();
							if ($button.hasClass('loading')) {
								return false;
							}
							$button.removeClass('added');
							$button.addClass('loading');
							$button.parent().addClass('porto-ajax-loading');
							if (!$button.siblings('.porto-loading-icon').length) {
								$('<span class="porto-loading-icon"></span>').insertAfter($button);
							}

							var data = {
								product_id: variation_id ? variation_id : product_id,
								quantity: quantity
							};

							// Trigger event.
							$(document.body).trigger('adding_to_cart', [$button, data]);

							$.ajax({
								type: 'POST',
								url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
								data: data,
								dataType: 'json',
								success: function(response) {
									$button.parent().removeClass('porto-ajax-loading');
									if (!response) {
										return;
									}
									if (response.error && response.product_url) {
										window.location = response.product_url;
										return;
									}
									// Redirect to cart option
									if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
										window.location = wc_add_to_cart_params.cart_url;
										return;
									}

									// Trigger event.
									$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
								}
							});
						});
					}

					return self;
				}
			}

		});

	}).apply(this, [window.theme, jQuery]);


	// Woocommerce Qty Field
	(function(theme, $) {

		theme = theme || {};

		$.extend(theme, {

			WooQtyField: {

				initialize: function() {

					this.build()
						.events();

					return this;
				},

				qty_handler: function() {
					var $obj = $(this);
					if ($obj.closest('.quantity').next('.add_to_cart_button[data-quantity]').length) {
						var count = $obj.val();
						if (count) {
							$obj.closest('.quantity').next('.add_to_cart_button[data-quantity]').attr('data-quantity', count);
						}
					}
				},

				build: function() {
					var self = this;

					// Quantity buttons
					$( 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<button type="button" value="+" class="plus">+</button>' ).prepend( '<button type="button" value="-" class="minus">-</button>' );

					// Target quantity inputs on product pages
					$( 'input.qty:not(.product-quantity input.qty)' ).each( function() {
						var min = parseFloat( $( this ).attr( 'min' ) );

						if ( min && min > 0 && parseFloat( $( this ).val() ) < min ) {
							$( this ).val( min );
						}
					});

					$( 'input.qty:not(.product-quantity input.qty)' ).off('change', self.qty_handler).on('change', self.qty_handler);

					$( document ).off('click', '.quantity .plus, .quantity .minus').on( 'click', '.quantity .plus, .quantity .minus', function() {

						// Get values
						var $qty        = $( this ).closest( '.quantity' ).find( '.qty' ),
							currentVal  = parseFloat( $qty.val() ),
							max         = parseFloat( $qty.attr( 'max' ) ),
							min         = parseFloat( $qty.attr( 'min' ) ),
							step        = $qty.attr( 'step' );

						// Format values
						if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
						if ( max === '' || max === 'NaN' ) max = '';
						if ( min === '' || min === 'NaN' ) min = 0;
						if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;

						// Change the value
						if ( $( this ).is( '.plus' ) ) {

							if ( max && ( max == currentVal || currentVal > max ) ) {
								$qty.val( max );
							} else {
								$qty.val( currentVal + parseFloat( step ) );
							}

						} else {

							if ( min && ( min == currentVal || currentVal < min ) ) {
								$qty.val( min );
							} else if ( currentVal > 0 ) {
								$qty.val( currentVal - parseFloat( step ) );
							}

						}

						// Trigger change event
						$qty.trigger( 'change' );
					});

					return self;
				},

				events: function() {
					var self = this;

					$(document).ajaxComplete(function(event, xhr, options) {
						self.build();
					});

					return self;
				}
			}

		});

	}).apply(this, [window.theme, jQuery]);


	// Woocommerce Variation Form
	(function(theme, $) {

		theme = theme || {};

		var duration = 300;

		$.extend(theme, {

			WooVariationForm: {

				initialize: function() {

					this.init().events();

					return this;
				},

				init: function() {
					$('.variations_form').each(function() {
						var $variation_form = $( this ),
							$reset_variations = $variation_form.find( '.reset_variations' );

						if ($reset_variations.css('visibility') == 'hidden')
							$reset_variations.hide();
					});
					return this;
				},

				events: function() {
					var self = this;

					$( document ).on( 'check_variations', '.variations_form', function( event, exclude, focus ) {
						var $variation_form = $( this ),
							$reset_variations = $variation_form.find( '.reset_variations' );

						if ($reset_variations.css('visibility') == 'hidden')
							$reset_variations.hide();
					});

					$( document ).on( 'reset_image', '.variations_form', function(event) {
						var $product        = $(this).closest( '.product, .product-col' ),
							$product_img    = $product.find( 'div.product-images .woocommerce-main-image' );
						if ( $product.hasClass('product-col') ) { // shop pages
							$product_img = $product.find( 'div.product-image .inner img:first-child' );
						}
						var o_src           = $product_img.attr('data-o_src'),
							o_title         = $product_img.attr('data-o_title'),
							o_href          = $product_img.attr('data-o_href'),
							$thumb_img      = $product.find( '.woocommerce-main-thumb' ),
							o_thumb_src     = $thumb_img.attr('data-o_src');

						var $image_slider = $product.find('.product-image-slider'),
							$thumbs_slider = $product.find('.product-thumbs-slider'),
							links;

						if ($image_slider.length) {
							$image_slider.trigger('to.owl.carousel', [0, duration, true]);
							links = $image_slider.data('links');
						}
						if ($thumbs_slider.length) {
							$thumbs_slider.trigger('to.owl.carousel', [0, duration, true]);
							$thumbs_slider.find('.owl-item:eq(0)').click();
						}

						if ( o_src ) {
							$product_img
								.attr( 'src', o_src )
								.attr( 'srcset', '' )
								.attr( 'alt', o_title )
								.attr( 'href', o_href );

							$product_img.each(function() {
								var elevateZoom = $(this).data('elevateZoom');
								if (typeof elevateZoom != 'undefined') {
									elevateZoom.swaptheimage($(this).attr( 'src' ), $(this).attr( 'src' ));
								}
							});

							if (theme.product_image_popup && typeof links != 'undefined') {
								links[0].src = o_href;
								links[0].title = o_title;
							}
						}
						if (o_thumb_src) {
							$thumb_img.attr( 'src', o_thumb_src );
						}
					});

					$( document ).on( 'found_variation', '.variations_form', function(event, variation) {

						if (typeof variation == 'undefined') {
							return;
						}

						var $product       = $(this).closest( '.product, .product-col' ),
							$image_slider  = $product.find('.product-image-slider'),
							$thumbs_slider = $product.find('.product-thumbs-slider'),
							links;

						if ($image_slider.length) {
							$image_slider.trigger('to.owl.carousel', [0, duration, true]);
							links = $image_slider.data('links');
						}
						if ($thumbs_slider.length) {
							$thumbs_slider.trigger('to.owl.carousel', [0, duration, true]);
							$thumbs_slider.find('.owl-item:eq(0)').click();
						}

						var $shop_single_image     = $product.find( 'div.product-images .woocommerce-main-image' ).length ? $product.find( 'div.product-images .woocommerce-main-image' ) : $('.single-product div.product-images .woocommerce-main-image'),
							productimage           =  $shop_single_image.attr('data-o_src'),
							imagetitle             =  $shop_single_image.attr('data-o_title'),
							imagehref              =  $shop_single_image.attr('data-o_href'),
							$shop_thumb_image = $product.find( '.woocommerce-main-thumb'),
							thumbimage   =  $shop_thumb_image.attr('data-o_src'),
							variation_image = variation.image_src,
							variation_link = variation.image_link,
							variation_title = variation.image_title,
							variation_thumb = variation.image_thumb;

						if ( $product.hasClass('product-col') ) { // shop pages
							$shop_single_image = $product.find( 'div.product-image .inner img:first-child' );
							variation_image = variation.image.thumb_src;
						}

						if ( ! productimage ) {
							productimage = $shop_single_image.attr('data-oi') ? $shop_single_image.attr('data-oi') : ( ( ! $shop_single_image.attr('src') ) ? '' : $shop_single_image.attr('src') );
							$shop_single_image.attr('data-o_src', productimage );
						}

						if ( ! imagehref ) {
							imagehref = ( ! $shop_single_image.attr('href') ) ? '' : $shop_single_image.attr('href');
							$shop_single_image.attr('data-o_href', imagehref );
						}

						if ( ! imagetitle ) {
							imagetitle = ( ! $shop_single_image.attr('alt') ) ? '' : $shop_single_image.attr('alt');
							$shop_single_image.attr('data-o_title', imagetitle );
						}

						if ( ! thumbimage ) {
							thumbimage = $shop_thumb_image.attr('data-oi') ? $shop_thumb_image.attr('data-oi') : ( ( ! $shop_thumb_image.attr('src') ) ? '' : $shop_thumb_image.attr('src') );
							$shop_thumb_image.attr('data-o_src', thumbimage );
						}

						if ( variation_image ) {
							$shop_single_image.attr( 'src', variation_image );
							$shop_single_image.attr( 'srcset', '' );
							$shop_single_image.attr( 'alt', variation_title );
							$shop_single_image.attr( 'href', variation_link );
							$shop_thumb_image.attr( 'src', variation_thumb );
							if (theme.product_image_popup && typeof links != 'undefined') {
								links[0].src = variation_link;
								links[0].title = variation_title;
							}
						} else {
							$shop_single_image.attr( 'src', productimage );
							$shop_single_image.attr( 'srcset', '' );
							$shop_single_image.attr( 'alt', imagetitle );
							$shop_single_image.attr( 'href', imagehref );
							$shop_thumb_image.attr( 'src', thumbimage );
							if (theme.product_image_popup && typeof links != 'undefined') {
								links[0].src = imagehref;
								links[0].title = imagetitle;
							}
						}
						$shop_single_image.each(function() {
							var elevateZoom = $(this).data('elevateZoom');
							if (typeof elevateZoom != 'undefined') {
								elevateZoom.swaptheimage($(this).attr( 'src' ), $(this).attr( 'src' ));
							}
						});
					});

					// fix scrolling to top issue on fancybox quickview whenever updating variation
					var porto_fb_timer = null;
					$( document ).on( 'found_variation reset_image', '.variations_form', function(event, variation) {
						if ($(this).closest('.fancybox-inner').length && $.fancybox) {
							$(window).unbind('resize.fb', $.fancybox.update);
							if (porto_fb_timer) {
								theme.deleteTimeout(porto_fb_timer);
							}
							porto_fb_timer = theme.requestTimeout(function() {
								$(window).bind('resize.fb', $.fancybox.update);
								porto_fb_timer = null;
							}, 160);
						}
					});

					return self;
				}
			}

		});

	}).apply(this, [window.theme, jQuery]);


	// Woocommerce Events
	(function(theme, $) {

		theme = theme || {};

		$.extend(theme, {

			WooEvents: {

				initialize: function() {

					this.events();

					return this;
				},

				events: function() {
					var self = this;

					// wcml currency switcher
					$('.wcml-switcher li').on('click', function(){
						if ($(this).parent().attr('disabled') == 'disabled')
							return;
						var currency = $(this).attr('rel');
						self.loadCurrency(currency);
					});

					// woocommerce currency switcher
					$('.woocs-switcher li').on('click', function(){
						if ($(this).parent().attr('disabled') == 'disabled')
							return;
						var currency = $(this).attr('rel');
						self.loadWoocsCurrency(currency);
					});

					return self;
				},

				loadCurrency : function(currency) {
					$('.wcml-switcher').attr('disabled', 'disabled');
					$('.wcml-switcher').append('<li class="loading"></li>');
					var data = {action: 'wcml_switch_currency', currency: currency};
					$.ajax({
						type : 'post',
						url : theme.ajax_url,
						data : {
							action: 'wcml_switch_currency',
							currency : currency
						},
						success: function(response) {
							$('.wcml-switcher').removeAttr('disabled');
							$('.wcml-switcher').find('.loading').remove();
							window.location = window.location.href;
						}
					});
				},

				loadWoocsCurrency : function(currency) {
					$('.woocs-switcher').attr('disabled', 'disabled');
					$('.woocs-switcher').append('<li class="loading"></li>');
					var l = window.location.href;
					l = l.split('?');
					l = l[0];
					var string_of_get = '?';
					woocs_array_of_get.currency = currency;
					
					if (Object.keys(woocs_array_of_get).length > 0) {
						jQuery.each(woocs_array_of_get, function (index, value) {
							string_of_get = string_of_get + "&" + index + "=" + value;
						});
					}
					window.location = l + string_of_get;
				},

				removeParameterFromUrl : function(url, parameter) {
					return url
						.replace(new RegExp('[?&]' + parameter + '=[^&#]*(#.*)?$'), '$1')
						.replace(new RegExp('([?&])' + parameter + '=[^&]*&'), '$1');
				}
			}

		});

	}).apply(this, [window.theme, jQuery]);

	(function(theme, $) {

		$(document).ready(function() {
			// Woocommerce Qty Field
			if (typeof theme.WooQtyField !== 'undefined') {
				theme.WooQtyField.initialize();
			}

			// Woocommerce Quick View
			if (typeof theme.WooQuickView !== 'undefined') {
				theme.WooQuickView.initialize();
			}

			// Woocommerce Events
			if (typeof theme.WooEvents !== 'undefined') {
				theme.WooEvents.initialize();
			}

			// disable drop down
			if (!('ontouchstart' in document)) {
				$('.mini-cart').on('hide.bs.dropdown', function () {
					return false;
				});
			} else {
				$('#mini-cart .cart-head').on('click', function(e) {
					$(this).parent().toggleClass('open');
				});
				$('html,body').on('click', function(e) {
					if ($('#mini-cart').hasClass('open') && !$(e.target).closest('#mini-cart').length) {
						$('#mini-cart').removeClass('open');
					}
				});
			}

			$(document).on('tabactivate', '.woocommerce-tabs', function(e, ui) {
				var label = $(ui).attr('aria-controls');
				var panel = $('[aria-labelledby="' + label + '"');
				theme.refreshVCContent(panel);
			});
		});
	}).apply(this, [window.theme, jQuery]);


	(function (theme, $, undefined) {

		$(document).ready(function(){
			// Woocommerce Variation Form
			theme.WooVariationForm.initialize();

			// Woocommerce Product Image Slider
			theme.WooProductImageSlider.initialize();

			porto_woocommerce_init();

			$(window).bind('vc_reload', function() {
				porto_woocommerce_init();
				$('.type-product').addClass('product');
			});

			// Add wishlist popup
			if (!$('#yith-wcwl-popup-message').length) {
				$('body').prepend($('<div>').attr('id', 'yith-wcwl-popup-message').html('<div id="yith-wcwl-message"></div>').hide());
			}

			// shop horizontal filter
			$(document).on('click', '.porto-product-filters-toggle a', function(e) {
				e.preventDefault();
				$(this).closest('.porto-product-filters-toggle').toggleClass('opened');
				var $products_wrapper = $(this).closest('#main').find('.main-content').find('ul.products'), offset, $main = $(this).closest('#main').find('.main-content-wrap');
				$main.toggleClass('opened');
				if ($main.hasClass('opened')) {
					offset = -1;
				} else {
					offset = 1;
				}
				if ($products_wrapper.hasClass('grid')) {
					var cols_lg_index = 0, cols_md_index = 0, width_lg_index = 0, width_md_index = 0;
					for(var i = 1; i <= 8; i++) {
						if (!cols_lg_index && $products_wrapper.hasClass('pcols-lg-' + i)) {
							cols_lg_index = i;
							if (i + offset >= 1) {
								$products_wrapper.removeClass('pcols-lg-' + i);
								$products_wrapper.addClass('pcols-lg-' + (i + offset));
							}
						}
						if (!cols_md_index && $products_wrapper.hasClass('pcols-md-' + i)) {
							cols_md_index = i;
							if (i + offset >= 1) {
								$products_wrapper.removeClass('pcols-md-' + i);
								if (offset === -1) {
									$products_wrapper.addClass('pcols-sm-' + i);
								}
								$products_wrapper.addClass('pcols-md-' + (i + offset));
							}
						}
						if (!width_lg_index && $products_wrapper.hasClass('pwidth-lg-' + i)) {
							width_lg_index = i;
							if (i + offset >= 1) {
								$products_wrapper.removeClass('pwidth-lg-' + i);
								$products_wrapper.addClass('pwidth-lg-' + (i + offset));
							}
						}
						if (!width_md_index && $products_wrapper.hasClass('pwidth-md-' + i)) {
							width_md_index = i;
							if (i + offset >= 1) {
								$products_wrapper.removeClass('pwidth-md-' + i);
								$products_wrapper.addClass('pwidth-md-' + (i + offset));
							}
						}
					}
				}
				theme.requestTimeout(function() {
					$(window).trigger('scroll');
				}, 300);
				
				if ($main.hasClass('opened')) {
					$.cookie('porto_horizontal_filter', 'opened');
				} else {
					$.cookie('porto_horizontal_filter', 'closed');
				}
				return false;
			});
			if ($.cookie && 'opened' == $.cookie('porto_horizontal_filter') && $('#main .porto-products-filter-body').length && !theme.isTablet()) {
				$('.porto-product-filters-toggle a').trigger('click');
				$('#main .porto-products-filter-body [data-plugin-sticky]:not(.manual)').addClass('manual');
				setTimeout(function() {
					var $obj = $('#main .porto-products-filter-body [data-plugin-sticky].manual'),
						pluginOptions = $obj.data('plugin-options');
					$obj.removeClass('manual').themeSticky(pluginOptions);
					theme.requestTimeout(function() {
						$(window).trigger('scroll');
					}, 100);
				}, 500);
			}

			$(document).on('click', '.porto-product-filters.style2 .widget-title', function(e) {
				e.preventDefault();
				if ($(this).next().is(':hidden')) {
					$('.porto-product-filters.style2 .widget-title').next().hide();
					$('.porto-product-filters.style2 .widget').removeClass('opened');
					$(this).next().show();
					$(this).next().find('input[type="text"]:first-child').focus();
				} else {
					$(this).next().hide();
				}
				$(this).parent().toggleClass('opened');
				return false;
			});
			$('body').on('click', function(e) {
				if (!$(e.target).is('.porto-product-filters') && !$(e.target).is('.porto-product-filters *')) {
					$('.porto-product-filters.style2 .widget-title').next().hide();
					$('.porto-product-filters.style2 .widget').removeClass('opened');
				}
			});

			// Perform AJAX login on form submit
			$('body').on('click', '#login-form-popup form .woocommerce-Button', function(e){
				var $form = $(this).closest('form'), isLogin = $(this).hasClass('login-btn');
				$form.find('#email').val($form.find('#username').val());
				$form.find('p.status').show().text('Please wait...').addClass('loading');
				$form.find('button[type=submit]').attr('disabled', 'disabled');
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: theme.ajax_url,
					data: $form.serialize() + '&action=porto_account_login_popup_' + (isLogin ? 'login' : 'register'),
					success: function(data) {
						$form.find('p.status').html(data.message.replace('/<script.*?\/script>/s', '')).removeClass('loading');
						$form.find('button[type=submit]').removeAttr('disabled');
						if (data.loggedin === true){
							window.location.reload();
						}
					}
				});
				e.preventDefault();
			});

			// shortcodes
			$(document).on('click', '.porto-products.show-category .product-categories a', function(e) {
				e.preventDefault();
				var $this = $(this), $form = $this.closest('.porto-products').find('.pagination-form');
				$(this).parent().siblings().removeClass('current');
				$(this).parent().addClass('current');
				if (typeof $this.data('sort_id') != 'undefined') {
					$form.find('input[name="orderby"]').val($this.data('sort_id'));
					$form.find('input[name="category"]').val('');
				}
				if (typeof $this.data('cat_id') != 'undefined') {
					if (typeof $this.data('sort_id') == 'undefined') {
						$form.find('input[name="orderby"]').val($form.find('input[name="original_orderby"]').val());
					}
					if ($this.data('cat_id')) {
						$form.find('input[name="category"]').val($this.data('cat_id'));
					} else {
						$form.find('input[name="category"]').val('');
					}
				}
				var data = $form.serialize() + '&product-page=1&action=porto_woocommerce_shortcodes_products&nonce=' + js_porto_vars.porto_nonce;
				$this.closest('.porto-products').find('ul.products').trigger('porto_update_products', [data, '']);
			});
			$(document).on('click', '.porto-products .page-numbers a', function(e) {
				var $this = $(this), pagination_style,
					$shop_container = $this.closest('.porto-products').find('ul.products'),
					cur_page = $shop_container.data('cur_page'),
					max_page = $shop_container.data('max_page'),
					$form = $this.closest('.porto-products').find('.pagination-form');
				e.preventDefault();
				if ($this.closest('.pagination').hasClass('load-more')) {
					if (!cur_page || !max_page || ++cur_page > max_page) {
						return;
					}
					pagination_style = 'load_more';
					$this.data('text', $this.text());
					$this.text(js_porto_vars.loader_text);
				} else {
					var url = new RegExp("product-page(=|/)([^(&|/)]*)", "i").exec(this.href);
					cur_page = url && unescape(url[2]) || "";
					pagination_style = 'default';
				}
				var page_var = cur_page ? '&product-page=' + escape( cur_page ) : '', data = $form.serialize() + page_var + '&action=porto_woocommerce_shortcodes_products&nonce=' + js_porto_vars.porto_nonce;
				$shop_container.trigger('porto_update_products', [data, pagination_style, $this]);
				if ('default' == pagination_style) {
					theme.scrolltoContainer($shop_container);
				}
			});
			$(document).on('porto_update_products', 'ul.products', function(e, data, pagination_style, $obj) {
				var $this = $(this);
				if ($this.hasClass('loading')) {
					return;
				}
				$this.addClass('loading');
				if ('load_more' != pagination_style) {
					$this.addClass('yith-wcan-loading');
					if (!$this.children('.porto-loading-icon').length) {
						$this.append('<i class="porto-loading-icon"></i>');
					}
				}
				$.ajax({
					url: theme.ajax_url,
					data: data,
					type: 'post',
					success: function(response) {
						if ($this.data('cur_page') && $(response).find('ul.products').data('cur_page')) {
							$this.data('cur_page', $(response).find('ul.products').data('cur_page'));
						}
						if ('load_more' == pagination_style) {
							$this.append($(response).find('ul.products').html());
						} else {
							if ($this.hasClass('owl-carousel')) {
								$this.parent().css('min-height', $this.parent().height());
							}
							if ($this.hasClass('grid-creative') && typeof $this.attr('data-plugin-masonry') != 'undefined' ) {
								$this.isotope('remove', $this.children());
								var newItems = $(response).find('ul.products').children();
								$this.append(newItems);
								$this.isotope('appended', newItems);
								$this.waitForImages(function() {
									$this.isotope('layout');
								});
							} else {
								if ($(response).find('ul.products').length) {
									$this.html($(response).find('ul.products').html());
								} else {
									$this.html('');
								}
							}
						}

						if ($this.hasClass('owl-carousel')) {
							$this.trigger('destroy.owl.carousel');
							theme.requestTimeout(function() {
								var pluginOptions = $this.data('plugin-options'), opts;
								if (pluginOptions)
									opts = pluginOptions;
								$this.data('__wooProductsSlider', '').themeWooProductsSlider(opts);
								$this.parent().css('min-height', '');
							}, 100);
						}
						if ($this.closest('.porto-products').find('.shop-loop-after').length) {
							if($(response).find('.shop-loop-after').length) {
								$this.closest('.porto-products').find('.shop-loop-after').replaceWith($(response).find('.shop-loop-after'));
							} else {
								$this.closest('.porto-products').find('.shop-loop-after').remove();
							}
						}
						if (typeof $this.data('infinitescroll') != 'undefined') {
							var infinitescrollData = $this.data('infinitescroll');
							infinitescrollData.options.state.currPage = 1;
							$this.data('infinitescroll', infinitescrollData);
						}
						$this.removeClass('yith-wcan-loading');
						if ('load_more' == pagination_style && typeof $obj != 'undefined' && typeof $obj.data('text') != 'undefined') {
							$obj.text($obj.data('text'));
						}
						$(document).trigger("yith-wcan-ajax-filtered");
					},
					complete: function() {
						$this.removeClass('loading');
					}
				});
			});
		});

		// shortcode: porto_one_page_category_products
		$('.porto-onepage-category.show-products .category-section .sub-category').children('.cat-item').addClass('product-col');
		$(document).on('click', '.porto-onepage-category .sub-category a', function(e) {
			var $this = $(this), category, data;
			category = new RegExp("cat-item-([^( |\")]*)", "i").exec($this.parent().attr('class'));
			category = category && unescape(category[1]) || "";
			if (category) {
				data = $this.closest('.category-details').find('.ajax-form').serialize() + '&action=porto_woocommerce_shortcodes_products&category_description=true&category=' + category + '&nonce=' + js_porto_vars.porto_nonce;
				e.preventDefault();
				$this.closest('.category-section').find('.woocommerce > ul.products').trigger('porto_update_products', [data, '']);
			}
		});
		$(window).load(function() {
			if ($('.porto-onepage-category.show-products').length) {
				$('body').css('position', 'relative');
				$('body').scrollspy({ target: '.porto-onepage-category.show-products .category-list', offset: theme.StickyHeader.sticky_height + theme.adminBarHeight() + theme.sticky_nav_height + 20 });
				var previousScrollTop = 0, $loadObj;
				window.addEventListener('scroll', function() {
					if (!$('.porto-onepage-category.show-products.ajax-load .category-section:not(.ajax-loaded)').length) {
						return;
					}
					var currentScrollTop = $(window).scrollTop();
					if (previousScrollTop > currentScrollTop) { // up
						$loadObj = $('.porto-onepage-category.show-products.ajax-load .category-section:not(.ajax-loaded)').last();
					} else { //down
						$loadObj = $('.porto-onepage-category.show-products.ajax-load .category-section:not(.ajax-loaded)').eq(0);
					}
					previousScrollTop = $(window).scrollTop();
					if(!$loadObj.closest('.porto-onepage-category').hasClass('loading') && ($loadObj.offset().top <= $(window).scrollTop()+$(window).innerHeight()*0.7)) {
						$loadObj.trigger('porto_load_category_products');
					}
				}, {passive: true});
			}

			// sticky add to cart
			if ($('.single-product .sticky-product').length) {
				window.addEventListener('scroll', function() {
					var scrollTop = $(window).scrollTop(),
						offset = theme.adminBarHeight() + theme.StickyHeader.sticky_height;
					if ($('form.cart').offset().top + $('form.cart').height() / 2 <= scrollTop + offset) {
						$('.single-product .sticky-product').removeClass('hide');
						if (!$('.single-product .sticky-product').hasClass('pos-bottom')) {
							$('.single-product .sticky-product').css('top', offset);
						}
					} else {
						$('.single-product .sticky-product').addClass('hide');
					}
				}, {passive: true});
				$('.sticky-product .add-to-cart .button').on('click', function(e) {
					e.preventDefault();
					$('.single-product form .quantity .qty').val($('.single-product .sticky-product .add-to-cart .qty').val());
					$('.single-product form .single_add_to_cart_button').trigger('click');
				});
				$('.single-product .entry-summary .quantity').clone().prependTo('.single-product .sticky-product .add-to-cart');
			}

			// sticky filter on mobile
			if (1 === $('.shop-loop-before').length && $('.mobile-sidebar').length) {
				var init_filter_sticky = function() {
					var $obj = $('.shop-loop-before');
					if (!$obj.prev('.filter-placeholder').length) {
						$('<div class="filter-placeholder m-0"></div>').insertBefore($obj);
					}
					var $ph = $obj.prev('.filter-placeholder'),
						scrollTop = $(window).scrollTop(),
						offset = theme.adminBarHeight() + theme.StickyHeader.sticky_height;
					if ($('html.filter-sidebar-opened').length) {
						$ph.css('height', '');
						return;
					}
					if ($ph.offset().top <= scrollTop + offset) {
						$ph.css('height', $obj.outerHeight() + parseInt($obj.css('margin-bottom')));
						$obj.addClass('sticky').css('top', offset);
					} else {
						$ph.css('height', '');
						$obj.removeClass('sticky');
					}
				};
				if (window.innerWidth < 992) {
					window.removeEventListener('scroll', init_filter_sticky);
					window.addEventListener('scroll', init_filter_sticky, {passive: true});
					init_filter_sticky();
				}
				var request_timer = null;
				$(window).on('resize', function() {
					if (request_timer) {
						theme.deleteTimeout(request_timer);
						request_timer = false;
					}
					if (window.innerWidth < 992) {
						request_timer = theme.requestTimeout(function() {
							window.removeEventListener('scroll', init_filter_sticky);
							window.addEventListener('scroll', init_filter_sticky, {passive: true});
							$(window).trigger('scroll');
						}, 100);
					} else {
						window.removeEventListener('scroll', init_filter_sticky);
						$('.shop-loop-before').removeClass('sticky').css('top', '').prev('.filter-placeholder').css('height', '');
					}
				});
			}
		});

		$(document).on('click', '.porto-onepage-category.show-products .category-list .nav-link', function(e) {
			var $target = $($(this).attr('href'));
			if (!$target.length) {
				return;
			}
			e.preventDefault();
			if ($(this).closest('.porto-onepage-category').hasClass('ajax-load') && !$target.hasClass('ajax-loaded')) {
				$target.trigger('porto_load_category_products');
			}
			$target.closest('.porto-onepage-category').addClass('moving');
			$('html, body').stop().animate({
				scrollTop: $target.offset().top - theme.StickyHeader.sticky_height - theme.adminBarHeight() - theme.sticky_nav_height - 10
			}, 600, 'easeOutQuad', function() {
				$target.closest('.porto-onepage-category').removeClass('moving');
			});
		});

		$(document).on('porto_load_category_products', '.category-section', function() {
			var $target = $(this), cat_id = $target.attr('id').replace('category-', '');
			if ($target.closest('.porto-onepage-category').hasClass('loading') || $target.closest('.porto-onepage-category').hasClass('moving') || $target.hasClass('ajax-loaded')) {
				return false;
			}
			$target.css('min-height', 200);
			$target.addClass('yith-wcan-loading');
			if (!$target.children('.porto-loading-icon').length) {
				$target.append('<i class="porto-loading-icon"></i>');
			}
			$target.closest('.porto-onepage-category').addClass('loading');
			var data = $target.closest('.porto-onepage-category').find('.ajax-form').serialize() + '&action=porto_woocommerce_shortcodes_products&category_description=true&category=' + cat_id + '&nonce=' + js_porto_vars.porto_nonce;
			$.ajax({
				url: theme.ajax_url,
				data: data,
				type: 'post',
				success: function(response) {
					$target.addClass('ajax-loaded');
					$target.append($(response).html());
					$target.removeClass('yith-wcan-loading');
					$(document).trigger('yith-wcan-ajax-filtered');
					$(window).trigger('resize');
					$('body').scrollspy('refresh');
					$target.closest('.porto-onepage-category').removeClass('loading');
				}
			});
		});


		// cart page accordion
		$('.cart-v2 .cart_totals .accordion-toggle.out').removeClass('out');
		$(document).ajaxComplete(function(event, xhr, options) {
			$('.cart-v2 .cart_totals .accordion-toggle.out').each(function(){
				if($($(this).attr('href')).length && $($(this).attr('href')).is(':hidden')) {
					$(this).removeClass('collapsed');
					$($(this).attr('href')).addClass('show');
				}
			});
		});


		portoCalcSliderTitleLine($('.porto-products.title-border-middle'));
		$(window).smartresize(function() {
			portoCalcSliderTitleLine($('.porto-products.title-border-middle'));
		});

		// porto products filter element
		$('.porto_products_filter_form .btn-submit').on('click', function(e) {
			e.preventDefault();
			var data = $(this).closest('form').serializeArray(),
				submit_data = '';
			for(var i in data) {
				var param = data[i];
				if (param.value) {
					if (submit_data) {
						submit_data += '&';
					}
					submit_data += param.name + '=' + param.value;
					if ('min_price' == param.name) {
						var max_price = $(this).closest('form').find('.porto_dropdown_price_range option:selected').data('maxprice');
						if (max_price) {
							submit_data += '&max_price=' + max_price;
						}
					}
				}
			}
			location.href = $(this).closest('form').attr('action') + '?' + submit_data;
		});

		// yith wishlist
		if ($('.wishlist_table.responsive').length) {
			$(window).on('resize', function() {
				var media = window.matchMedia('(max-width: 768px)');
				if (media.matches) {
					$('.wishlist_table.responsive').addClass('mobile');
				} else {
					$('.wishlist_table.responsive').removeClass('mobile');
				}
			});
		}

		// pre-order
		if (js_porto_vars.pre_order) {
			var porto_pre_order = {
				init: function() {
					this.$add_to_cart_btn  = $('.product-summary-wrap .single_add_to_cart_button');
					this.add_to_cart_label = this.$add_to_cart_btn.html();
					$('.product-summary-wrap form.variations_form').on('show_variation', function(e, v, p) {
						if (v.porto_pre_order) {
							porto_pre_order.$add_to_cart_btn.html(v.porto_pre_order_label);
							if (v.porto_pre_order_date) {
								$(this).find('.woocommerce-variation-description').append(v.porto_pre_order_date);
							}
						} else {
							porto_pre_order.$add_to_cart_btn.html(porto_pre_order.add_to_cart_label);
						}
					}).on('hide_variation', function() {
						porto_pre_order.$add_to_cart_btn.html(porto_pre_order.add_to_cart_label);
					});
				}
			};
			if ($('div.product.skeleton-loading').length) {
				$('div.product.skeleton-loading').on('skeleton-loaded', function() {
					porto_pre_order.init();
				});
			} else {
				porto_pre_order.init();
			}
		}

		// refresh yith wishlist
		if ($('#header .my-wishlist .wishlist-count').length) {
			$(document.body).on('added_to_wishlist removed_from_wishlist added_to_cart', function(e) {
				var $obj = $('#header .my-wishlist .wishlist-count');
				if ($obj.text()) {
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: theme.ajax_url,
						data: {
							action: 'porto_refresh_wishlist_count',
							nonce: js_porto_vars.porto_nonce,
						},
						success: function(response) {
							if (response || 0 === response) {
								$obj.addClass('count-updating').text(Number(response));
								setTimeout(function() {
									$obj.removeClass('count-updating');
								}, 1000);
							}
						}
					});
				}
			});
		}

		// fix contact form 7 role="alert" issue in cart page
		if ($(document.body).hasClass('woocommerce-cart') && $('.wpcf7 .screen-reader-response').length) {
			$('.wpcf7 .screen-reader-response').attr('role', '');
		}

		// fix dokan search vendor
		$('#dokan-store-listing-filter-form-wrap .store-search-input').on('keydown', function(e) {
			if (e.which && event.which == 13) {
				$(this).closest('form').find('#apply-filter-btn').trigger('click');
				e.preventDefault();
			}
		});
	})( window.theme, jQuery );

})();

function porto_woocommerce_init($wrap) {
	'use strict';

	if (!$wrap) {
		$wrap = jQuery(document.body);
	}
	// Woo Widget Toggle
	(function($) {

		if ($.isFunction($.fn.themeWooWidgetToggle)) {

			$(function() {
				$wrap.find('.widget_product_categories, .widget_price_filter, .widget_layered_nav, .widget_layered_nav_filters, .widget_rating_filter, .porto_widget_price_filter').find('.widget-title').each(function() {
					var $this = $(this),
						opts;

					var pluginOptions = $this.data('plugin-options');
					if (pluginOptions)
						opts = pluginOptions;

					$this.themeWooWidgetToggle(opts);
				});
			});

		}

		// Woo Widget Accordion
		if ($.isFunction($.fn.themeWooWidgetAccordion)) {

			$(function() {
				$wrap.find('.widget_product_categories, .widget_price_filter, .widget_layered_nav, .widget_layered_nav_filters, .widget_rating_filter').each(function() {
					var $this = $(this),
						opts;

					var pluginOptions = $this.data('plugin-options');
					if (pluginOptions)
						opts = pluginOptions;

					$this.themeWooWidgetAccordion(opts);
				});
			});

		}

		// Woo Products Slider
		if ($.isFunction($.fn.themeWooProductsSlider)) {

			$(function() {
				$wrap.find('.products-slider:not(.manual)').each(function() {
					var $this = $(this),
						opts;

					var pluginOptions = $this.data('plugin-options');
					if (pluginOptions)
						opts = pluginOptions;

					$this.themeWooProductsSlider(opts);
				});
			});

		}

	})(jQuery);
}

function porto_woocommerce_variations_init($parent_obj) {
	'use strict';

	theme.requestTimeout(function() {
		var form_variation = $parent_obj.find('form.variations_form:not(.vf_init)');
		if (form_variation.length) {
			form_variation.each(function() {
				jQuery(this).wc_variation_form();
			});
			//form_variation.find("select option:selected").removeAttr("selected");
		}
	}, 100);
}