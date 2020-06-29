(function(theme, $) {
	'use strict';

	theme = theme || {};

	$.extend(theme, {
		mfpConfig: {
			tClose: js_porto_vars.popup_close,
			tLoading: '<div class="porto-ajax-loading"><i class="porto-loading-icon"></i></div>',
			gallery: {
				tPrev: js_porto_vars.popup_prev,
				tNext: js_porto_vars.popup_next,
				tCounter: js_porto_vars.mfp_counter
			},
			image: {
				tError: js_porto_vars.mfp_img_error
			},
			ajax: {
				tError: js_porto_vars.mfp_ajax_error
			},
			callbacks: {
				open: function() {
					$('body').addClass('lightbox-opened');
					var fixed = this.st.fixedContentPos;
					if (fixed) {
						$('#header.sticky-header .header-main.sticky, #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .header-main, .fixed-header #header.sticky-header .main-menu-wrap').css(theme.rtl_browser?'left':'right', theme.getScrollbarWidth());
					}
					/* D3-Ahsan - Start */
					var that = $(this._lastFocusedEl);
					if ( ( that.closest('.portfolios-lightbox').hasClass('with-thumbs') ) && $(document).width() >= 1024 ){
						
						var portfolio_lightbox_thumbnails_base = that.closest('.portfolios-lightbox.with-thumbs').find('.porto-portfolios-lighbox-thumbnails').clone(),
							magnificPopup = $.magnificPopup.instance;
						
						$('body').prepend( portfolio_lightbox_thumbnails_base );
						
						var $portfolios_lightbox_thumbnails = $( 'body > .porto-portfolios-lighbox-thumbnails'),
							$portfolios_lightbox_thumbnails_carousel = $portfolios_lightbox_thumbnails.children('.owl-carousel');
						$portfolios_lightbox_thumbnails_carousel.themeCarousel( $portfolios_lightbox_thumbnails_carousel.data('plugin-options') );
						$portfolios_lightbox_thumbnails_carousel.trigger('refresh.owl.carousel');

						var $carousel_items_wrapper = $portfolios_lightbox_thumbnails_carousel.find('.owl-stage');
					
						$carousel_items_wrapper.find('.owl-item').removeClass('current');
						$carousel_items_wrapper.find('.owl-item').eq( magnificPopup.currItem.index ).addClass('current');

						$.magnificPopup.instance.next = function() {
							var magnificPopup = $.magnificPopup.instance;
							$.magnificPopup.proto.next.call(this);
							$carousel_items_wrapper.find('.owl-item').removeClass('current');
							$carousel_items_wrapper.find('.owl-item').eq( magnificPopup.currItem.index ).addClass('current');
						};
						
						$.magnificPopup.instance.prev = function() {
							var magnificPopup = $.magnificPopup.instance;
							$.magnificPopup.proto.prev.call(this);
							$carousel_items_wrapper.find('.owl-item').removeClass('current');
							$carousel_items_wrapper.find('.owl-item').eq( magnificPopup.currItem.index ).addClass('current');
						};
						
						$carousel_items_wrapper.find('.owl-item').on( 'click', function(){
							$carousel_items_wrapper.find('.owl-item').removeClass('current');
							$.magnificPopup.instance.goTo( $(this).index() );
							$(this).addClass('current');
						});
						
					}
					/* End - D3-Ahsan */
				},
				close: function() {
					$('body').removeClass('lightbox-opened');
					var fixed = this.st.fixedContentPos;
					if (fixed) {
						$('#header.sticky-header .header-main.sticky, #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .header-main, .fixed-header #header.sticky-header .main-menu-wrap').css(theme.rtl_browser?'left':'right', '');
					}
					$('.owl-carousel .owl-stage').each(function() {
						var $this = $(this),
							w = $this.width() + parseInt($this.css('padding-left')) + parseInt($this.css('padding-right'));

						$this.css({'width': w + 200});
						setTimeout(function() {
							$this.css({'width': w});
						}, 0);
					});
					/* D3-Ahsan - Start */
					var that = $(this._lastFocusedEl);
					if( ( that.parents('.portfolios-lightbox').hasClass('with-thumbs') ) && $(document).width() >= 1024 ){
						$(' body > .porto-portfolios-lighbox-thumbnails').remove();
					}
					/* End - D3-Ahsan */
				}
			}
		},
	});

}).apply(this, [window.theme, jQuery]);

// Lightbox
(function(theme, $) {
	'use strict';

	theme = theme || {};

	var instanceName = '__lightbox';

	var Lightbox = function($el, opts) {
		return this.initialize($el, opts);
	};

	Lightbox.defaults = {
		callbacks: {
			open: function() {
				$('body').addClass('lightbox-opened');
			},
			close: function() {
				$('body').removeClass('lightbox-opened');
			}
		}
	};

	Lightbox.prototype = {
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
			this.options = $.extend(true, {}, Lightbox.defaults, theme.mfpConfig, opts, {
				wrapper: this.$el
			});

			return this;
		},

		build: function() {
			if (!($.isFunction($.fn.magnificPopup))) {
				return this;
			}

			this.options.wrapper.magnificPopup(this.options);

			return this;
		}
	};

	// expose to scope
	$.extend(theme, {
		Lightbox: Lightbox
	});

	// jquery plugin
	$.fn.themeLightbox = function(opts) {
		return this.map(function() {
			var $this = $(this);

			if ($this.data(instanceName)) {
				return $this.data(instanceName);
			} else {
				return new theme.Lightbox($this, opts);
			}

		});
	}

}).apply(this, [window.theme, jQuery]);

// Visual Composer Image Zoom
(function(theme, $) {
	'use strict';

	theme = theme || {};

	var instanceName = '__toggle';

	var VcImageZoom = function($el, opts) {
		return this.initialize($el, opts);
	};

	VcImageZoom.defaults = {

	};

	VcImageZoom.prototype = {
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
			this.options = $.extend(true, {}, VcImageZoom.defaults, opts, {
				wrapper: this.$el
			});

			return this;
		},

		build: function() {
			var self = this,
				$el = this.options.container;
			$el.parent().magnificPopup($.extend(true, {}, theme.mfpConfig, {
				delegate: ".porto-vc-zoom",
				gallery: {
					enabled: true
				},
				mainClass: 'mfp-with-zoom',
				zoom: {
					enabled: true,
					duration: 300
				},
				type: 'image'
			}));

			return this;
		}
	};

	// expose to scope
	$.extend(theme, {
		VcImageZoom: VcImageZoom
	});

	// jquery plugin
	$.fn.themeVcImageZoom = function(opts) {
		return this.map(function() {
			var $this = $(this);

			if ($this.data(instanceName)) {
				return $this.data(instanceName);
			} else {
				return new theme.VcImageZoom($this, opts);
			}

		});
	}

}).apply(this, [window.theme, jQuery]);

// Post Ajax on Modal
(function(theme, $) {
	'use strict';

	theme = theme || {};

	var $rev_sliders;

	$.extend(theme, {

		PostAjaxModal: {

			defaults: {
				elements: '.page-portfolios'
			},

			initialize: function($elements, post_type) {
				this.$elements = ($elements || $(this.defaults.elements));
				this.post_type = ( typeof post_type == 'undefined' ? 'portfolio' : post_type );

				this.build();

				return this;
			},

			build: function() {
				var parentobj = this;

				parentobj.$elements.each(function() {

					var $this = $(this);

					if (!$this.find('a[data-ajax-on-modal]').get(0))
						return;

					var $container = $(this),
						postAjaxOnModal = {

							$wrapper: $container,
							modals: [],
							currentModal: 0,
							total: 0,

							build: function() {
								var self = this;

								self.modals = [];
								self.total = 0;

								$this.find('a[data-ajax-on-modal]').each(function() {
									self.add($(this));
								});

								$this.off('mousedown', 'a[data-ajax-on-modal]').on('mousedown', 'a[data-ajax-on-modal]', function (ev) {
									if (ev.which == 2) {
										ev.preventDefault();
										return false;
									}
								});
							},

							add: function($el) {

								var self = this,
									href = $el.attr('href'),
									index = self.total;

								self.modals.push({src: href});
								self.total++;

								$el.off('click').on('click', function(e) {
									e.preventDefault();
									self.show(index);
									return false;
								});

							},

							next: function() {
								var self = this;
								if(self.currentModal + 1 < self.total) {
									self.show(self.currentModal + 1);
								} else {
									self.show(0);
								}
							},

							prev: function() {
								var self = this;

								if((self.currentModal - 1) >= 0) {
									self.show(self.currentModal - 1);
								} else {
									self.show(self.total - 1);
								}
							},

							show: function(i) {
								var self = this;

								self.currentModal = i;

								if (i < 0 || i > (self.total-1)) {
									return false;
								}

								$.magnificPopup.close();
								$.magnificPopup.open($.extend(true, {}, theme.mfpConfig, {
									type: 'ajax',
									items: self.modals,
									gallery: {
										enabled: true
									},
									ajax: {
										settings: {
											type: 'post',
											data: {
												ajax_action: parentobj.post_type + '_ajax_modal'
											}
										}
									},
									mainClass: parentobj.post_type + '-ajax-modal',
									fixedContentPos: false,
									callbacks: {
										parseAjax: function(mfpResponse) {
											var $response = $(mfpResponse.data),
												$post = $response.find('#content article.' + parentobj.post_type),
												$vc_css = $response.filter('style[data-type]:not("")'),
												vc_css = '';

											$vc_css.each(function() {
												vc_css += $(this).text();
											});

											if ($('#' + parentobj.post_type + 'AjaxCSS').get(0)) {
												$('#' + parentobj.post_type + 'AjaxCSS').text(vc_css);
											} else {
												$('<style id="' + parentobj.post_type + 'AjaxCSS">' + vc_css + '</style>').appendTo( "head" )
											}

											$post.find('.' + parentobj.post_type + '-nav-all').html('<a href="#" data-ajax-' + parentobj.post_type + '-close data-tooltip data-original-title="' + js_porto_vars.popup_close + '" data-placement="bottom"><i class="fas fa-th"></i></a>');
											$post.find('.' + parentobj.post_type + '-nav').html('<a href="#" data-ajax-' + parentobj.post_type + '-prev class="' + parentobj.post_type + '-nav-prev" data-tooltip data-original-title="' + js_porto_vars.popup_prev + '" data-placement="bottom"><i class="fa"></i></a><a href="#" data-toggle="tooltip" data-ajax-' + parentobj.post_type + '-next class="' + parentobj.post_type + '-nav-next" data-tooltip data-original-title="' + js_porto_vars.popup_next + '" data-placement="bottom"><i class="fa"></i></a>');
											mfpResponse.data = '<div class="ajax-container">' + $post.html() + '</div>';
										},
										ajaxContentAdded: function() {
											// Wrapper
											var $wrapper = $('.' + parentobj.post_type + '-ajax-modal');

											// Close
											$wrapper.find('a[data-ajax-' + parentobj.post_type + '-close]').on('click', function(e) {
												e.preventDefault();
												$.magnificPopup.close();
												return false;
											});

											$rev_sliders = $wrapper.find('.rev_slider, rs-module');

											// Remove Next and Close
											if(self.modals.length <= 1) {
												$wrapper.find('a[data-ajax-' + parentobj.post_type + '-prev], a[data-ajax-' + parentobj.post_type + '-next]').remove();
											} else {
												// Prev
												$wrapper.find('a[data-ajax-' + parentobj.post_type + '-prev]').on('click', function(e) {
													e.preventDefault();
													if ($rev_sliders && $rev_sliders.get(0)) {
														try {$rev_sliders.revkill();} catch(err) {}
													}
													$wrapper.find('.mfp-arrow-left').trigger('click');
													return false;
												});
												// Next
												$wrapper.find('a[data-ajax-' + parentobj.post_type + '-next]').on('click', function(e) {
													e.preventDefault();
													if ($rev_sliders && $rev_sliders.get(0)) {
														try {$rev_sliders.revkill();} catch(err) {}
													}
													$wrapper.find('.mfp-arrow-right').trigger('click');
													return false;
												});
											}
											if ('portfolio' == parentobj.post_type) {
												$(window).trigger('resize');
											}
											porto_init();
											theme.refreshVCContent($wrapper);
											setTimeout(function() {
												var videos = $wrapper.find('video');
												if (videos.get(0)) {
													videos.each(function() {
														$(this)[0].play();
														$(this).parent().parent().parent().find('.video-controls').attr('data-action','play');
														$(this).parent().parent().parent().find('.video-controls').html('<i class="ult-vid-cntrlpause"></i>');
													});
												}
											}, 600);
											$wrapper.off('scroll').on('scroll', function() {
												$.fn.appear.run();
											});
										},
										change: function() {
											$('.mfp-wrap .ajax-container').click();
										},
										beforeClose: function() {
											if ($rev_sliders && $rev_sliders.get(0)) {
												try {$rev_sliders.revkill();} catch(err) {}
											}
											// Wrapper
											var $wrapper = $('.' + parentobj.post_type + '-ajax-modal');
											$wrapper.off('scroll');
										}
									}
								}), i);
							}
						};

					postAjaxOnModal.build();

					$this.data(parentobj.post_type + 'AjaxOnModal', postAjaxOnModal);
				});

				return parentobj;
			}
		}

	});

	// Key Press
	$(document.documentElement).on('keydown', function(e) {
		try {
			if (e.keyCode == 37 || e.keyCode == 39) {
				if ($rev_sliders && $rev_sliders.get(0)) {
					$rev_sliders.revkill();
				}
			}
		} catch(err) {}
	});

}).apply(this, [window.theme, jQuery]);

// Portfolio Ajax on Page
(function(theme, $) {
	'use strict';

	theme = theme || {};

	var activePortfolioAjaxOnPage;

	$.extend(theme, {

		PortfolioAjaxPage: {

			defaults: {
				elements: '.page-portfolios'
			},

			initialize: function($elements) {
				this.$elements = ($elements || $(this.defaults.elements));

				this.build();

				return this;
			},

			build: function() {
				var self = this;

				self.$elements.each(function() {

					var $this = $(this);

					if (!$this.find('#portfolioAjaxBox').get(0))
						return;

					var $container = $(this),
						portfolioAjaxOnPage = {

							$wrapper: $container,
							pages: [],
							currentPage: 0,
							total: 0,
							$ajaxBox: $this.find('#portfolioAjaxBox'),
							$ajaxBoxContent: $this.find('#portfolioAjaxBoxContent'),

							build: function() {
								var self = this;

								self.pages = [];
								self.total = 0;

								$this.find('a[data-ajax-on-page]').each(function() {
									self.add($(this));
								});

								$this.off('mousedown', 'a[data-ajax-on-page]').on('mousedown', 'a[data-ajax-on-page]', function (ev) {
									if (ev.which == 2) {
										ev.preventDefault();
										return false;
									}
								});
							},

							add: function($el) {

								var self = this,
									href = $el.attr('href');

								self.pages.push(href);
								self.total++;

								$el.off('click').on('click', function(e) {
								   e.preventDefault();
								   /* D3-Start */
									var _class = e.target.className
									if( _class == 'owl-next' ){
										return false;
									}else if( _class == 'owl-prev' ){
										return false;
									} else{
										self.show(self.pages.indexOf(href));
									}
									/* End-D3 */
									return false;
								});

							},

							events: function() {
								var self = this;

								// Close
								$this.off('click', 'a[data-ajax-portfolio-close]').on('click', 'a[data-ajax-portfolio-close]', function(e) {
									e.preventDefault();
									self.close();
									return false;
								});

								if (self.total <= 1) {
									$('a[data-ajax-portfolio-prev], a[data-ajax-portfolio-next]').remove();
								} else {
									// Prev
									$this.off('click', 'a[data-ajax-portfolio-prev]').on('click', 'a[data-ajax-portfolio-prev]', function(e) {
										e.preventDefault();
										self.prev();
										return false;
									});
									// Next
									$this.off('click', 'a[data-ajax-portfolio-next]').on('click', 'a[data-ajax-portfolio-next]', function(e) {
										e.preventDefault();
										self.next();
										return false;
									});
								}
							},

							close: function() {
								var self = this;

								if (self.$ajaxBoxContent.find('.rev_slider, rs-module').get(0)) {
									try {self.$ajaxBoxContent.find('.rev_slider, rs-module').revkill();} catch(err) {}
								}
								self.$ajaxBoxContent.empty();
								self.$ajaxBox.removeClass('ajax-box-init').removeClass('ajax-box-loading');
							},

							next: function() {
								var self = this;
								if(self.currentPage + 1 < self.total) {
									self.show(self.currentPage + 1);
								} else {
									self.show(0);
								}
							},

							prev: function() {
								var self = this;

								if((self.currentPage - 1) >= 0) {
									self.show(self.currentPage - 1);
								} else {
									self.show(self.total - 1);
								}
							},

							show: function(i) {
								var self = this;

								activePortfolioAjaxOnPage = null;

								if (self.$ajaxBoxContent.find('.rev_slider, rs-module').get(0)) {
									try {self.$ajaxBoxContent.find('.rev_slider, rs-module').revkill();} catch(err) {}
								}
								self.$ajaxBoxContent.empty();
								self.$ajaxBox.removeClass('ajax-box-init').addClass('ajax-box-loading');

								theme.scrolltoContainer(self.$ajaxBox);

								self.currentPage = i;

								if (i < 0 || i > (self.total-1)) {
									self.close();
									return false;
								}

								// Ajax
								$.ajax({
									url: self.pages[i],
									complete: function(data) {
										var $response = $(data.responseText),
											$portfolio = $response.find('#content article.portfolio'),
											$vc_css = $response.filter('style[data-type]:not("")'),
											vc_css = '';

										if ($('#portfolioAjaxCSS').get(0)) {
											$('#portfolioAjaxCSS').text(vc_css);
										} else {
											$('<style id="portfolioAjaxCSS">' + vc_css + '</style>').appendTo( "head" )
										}

										$portfolio.find('.portfolio-nav-all').html('<a href="#" data-ajax-portfolio-close data-tooltip data-original-title="' + js_porto_vars.popup_close + '"><i class="fas fa-th"></i></a>');
										$portfolio.find('.portfolio-nav').html('<a href="#" data-ajax-portfolio-prev class="portfolio-nav-prev" data-tooltip data-original-title="' + js_porto_vars.popup_prev + '"><i class="fa"></i></a><a href="#" data-toggle="tooltip" data-ajax-portfolio-next class="portfolio-nav-next" data-tooltip data-original-title="' + js_porto_vars.popup_next + '"><i class="fa"></i></a>');
										self.$ajaxBoxContent.html($portfolio.html()).append('<div class="row"><div class="col-lg-12"><hr class="tall"></div></div>');
										self.$ajaxBox.removeClass('ajax-box-loading');
										$(window).trigger('resize');
										porto_init();
										theme.refreshVCContent(self.$ajaxBoxContent);
										self.events();
										activePortfolioAjaxOnPage = self;

										self.$ajaxBoxContent.find('.lightbox:not(.manual)').each(function() {
											var $this = $(this),
												opts;

											var pluginOptions = $this.data('plugin-options');
											if (pluginOptions)
												opts = pluginOptions;

											$this.themeLightbox(opts);
										});
									}
								});
							}
						};

					portfolioAjaxOnPage.build();

					$this.data('portfolioAjaxOnPage', portfolioAjaxOnPage);
				});

				return self;
			}
		}

	});

	// Key Press
	$(document.documentElement).on('keyup', function(e) {
		try {
			if (!activePortfolioAjaxOnPage) return;
			// Next
			if (e.keyCode == 39) {
				activePortfolioAjaxOnPage.next();
			}
			// Prev
			if (e.keyCode == 37) {
				activePortfolioAjaxOnPage.prev();
			}
		} catch(err) {}
	});

}).apply(this, [window.theme, jQuery]);

// Post Filter
(function(theme, $) {
	'use strict';

	theme = theme || {};

	$.extend(theme, {

		PostFilter: {

			defaults: {
				elements: '.portfolio-filter'
			},

			initialize: function($elements, post_type) {
				this.$elements = ($elements || $(this.defaults.elements));
				this.post_type = (typeof post_type == 'undefined' ? 'portfolio' : post_type);

				this.build();

				return this;
			},

			build: function() {
				var self = this;

				self.$elements.each(function() {
					var $this = $(this);
					$this.find('li').on('click', function(e) {
						e.preventDefault();
						if ($(this).hasClass('active')) {
							return;
						}

						var selector = $(this).attr('data-filter'),
							position = $this.data('position'),
							$parent;

						$this.find('.active').removeClass('active');

						if (position == 'sidebar') {
							$parent = $('.main-content .page-' + self.post_type + 's');
							//theme.scrolltoContainer($parent);
							$('.sidebar-overlay').click();
						} else if (position == 'global') {
							$parent = $('.main-content .page-' + self.post_type + 's');
						} else {
							$parent = $(this).closest('.page-' + self.post_type + 's');
						}

						if ('faq' == self.post_type) {
							$parent.find('.faq').each(function() {
								var $that = $(this), easing = "easeInOutQuart", timeout = 300;
								if (selector == '*') {
									if ($that.css('display') == 'none') $that.stop(true).slideDown(timeout, easing, function() {
										$(this).attr('style', '').show();
									});
									selected++;
								} else {
									if ($that.hasClass(selector)) {
										if ($that.css('display') == 'none') $that.stop(true).slideDown(timeout, easing, function() {
											$(this).attr('style', '').show();
										});
										selected++;
									} else {
										if ($that.css('display') != 'none') $that.stop(true).slideUp(timeout, easing, function() {
											$(this).attr('style', '').hide();
										});
									}
								}
							});

							if (!selected && $parent.find('.faqs-infinite').length && typeof ($.fn.infinitescroll) != 'undefined') {
								$parent.find('.faqs-infinite').infinitescroll('retrieve');
							}
						} else if ($parent.hasClass('portfolios-timeline')) {
							var selected = 0;
							$parent.find('.portfolio').each(function() {
								var $that = $(this), easing = "easeInOutQuart", timeout = 300;
								if (selector == '*') {
									if ($that.css('display') == 'none') $that.stop(true).slideDown(timeout, easing, function() {
										$(this).attr('style', '').show();
									});
									selected++;
								} else {
									if ($that.hasClass(selector)) {
										if ($that.css('display') == 'none') $that.stop(true).slideDown(timeout, easing, function() {
											$(this).attr('style', '').show();
										});
										selected++;
									} else {
										if ($that.css('display') != 'none') $that.stop(true).slideUp(timeout, easing, function() {
											$(this).attr('style', '').hide();
										});
									}
								}
							});
							if (!selected && $parent.find('.portfolios-infinite').length && typeof ($.fn.infinitescroll) != 'undefined') {
								$parent.find('.portfolios-infinite').infinitescroll('retrieve');
							}
							setTimeout(function() {
								theme.FilterZoom.initialize($parent);
							}, 400);
						} else {
							$parent.find('.' + self.post_type + '-row').isotope({
								filter: selector == '*' ? selector : '.' + selector
							});
						}

						$(this).addClass('active');

						if (position == 'sidebar') {
							self.$elements.each(function() {
								var $that = $(this);

								if ($that == $this && $that.data('position') != 'sidebar') return;
								$that.find('li').removeClass('active');
								$that.find('li[data-filter="' + selector + '"]').addClass('active');
							});
						}

						window.location.hash = '#' + selector;
						theme.refreshVCContent();

					});
				});

				function hashchange() {
					var $filter = $(self.$elements.get(0)), hash = window.location.hash;

					if (hash) {
						var $o = $filter.find('li[data-filter="' + hash.replace('#', '') + '"]');
						if (!$o.hasClass('active')) {
							$o.click();
						}
					}
				}

				$(window).on('hashchange', hashchange);
				hashchange();

				return self;
			}
		}

	});

}).apply(this, [window.theme, jQuery]);

// Member Ajax on Page
(function(theme, $) {
	'use strict';

	theme = theme || {};

	var activeMemberAjaxOnPage;

	$.extend(theme, {

		MemberAjaxPage: {

			defaults: {
				elements: '.page-members'
			},

			initialize: function($elements) {
				this.$elements = ($elements || $(this.defaults.elements));

				this.build();

				return this;
			},

			build: function() {
				var self = this;

				self.$elements.each(function() {

					var $this = $(this);

					if (!$this.find('#memberAjaxBox').get(0))
						return;

					var $container = $(this),
						memberAjaxOnPage = {

							$wrapper: $container,
							pages: [],
							currentPage: 0,
							total: 0,
							$ajaxBox: $this.find('#memberAjaxBox'),
							$ajaxBoxContent: $this.find('#memberAjaxBoxContent'),

							build: function() {
								var self = this;

								self.pages = [];
								self.total = 0;

								$this.find('a[data-ajax-on-page]').each(function() {
									self.add($(this));
								});

								$this.off('mousedown', 'a[data-ajax-on-page]').on('mousedown', 'a[data-ajax-on-page]', function (ev) {
									if (ev.which == 2) {
										ev.preventDefault();
										return false;
									}
								});
							},

							add: function($el) {

								var self = this,
									href = $el.attr('href');

								self.pages.push(href);
								self.total++;

								$el.off('click').on('click', function(e) {
									e.preventDefault();
									self.show(self.pages.indexOf(href));
									return false;
								});

							},

							next: function() {
								var self = this;
								if(self.currentPage + 1 < self.total) {
									self.show(self.currentPage + 1);
								} else {
									self.show(0);
								}
							},

							prev: function() {
								var self = this;

								if((self.currentPage - 1) >= 0) {
									self.show(self.currentPage - 1);
								} else {
									self.show(self.total - 1);
								}
							},

							show: function(i) {
								var self = this;

								activeMemberAjaxOnPage = null;

								if (self.$ajaxBoxContent.find('.rev_slider, rs-module').get(0)) {
									try {self.$ajaxBoxContent.find('.rev_slider, rs-module').revkill();} catch(err) {}
								}
								self.$ajaxBoxContent.empty();
								self.$ajaxBox.removeClass('ajax-box-init').addClass('ajax-box-loading');

								theme.scrolltoContainer(self.$ajaxBox);

								self.currentPage = i;

								if (i < 0 || i > (self.total-1)) {
									self.close();
									return false;
								}

								// Ajax
								$.ajax({
									url: self.pages[i],
									complete: function(data) {
										var $response = $(data.responseText),
											$member = $response.find('#content article.member'),
											$vc_css = $response.filter('style[data-type]:not("")'),
											vc_css = '';

										$vc_css.each(function() {
											vc_css += $(this).text();
										});

										if ($('#memberAjaxCSS').get(0)) {
											$('#memberAjaxCSS').text(vc_css);
										} else {
											$('<style id="memberAjaxCSS">' + vc_css + '</style>').appendTo( "head" )
										}

										var $append = self.$ajaxBox.find('.ajax-content-append'), html = '';
										if ($append.length) html = $append.html();
										self.$ajaxBoxContent.html($member.html()).prepend('<div class="row"><div class="col-lg-12"><hr class="tall m-t-none"></div></div>').append('<div class="row"><div class="col-md-12"><hr class="m-t-md"></div></div>' + html);

										self.$ajaxBox.removeClass('ajax-box-loading');
										$(window).trigger('resize');
										porto_init();
										theme.refreshVCContent(self.$ajaxBoxContent);
										activeMemberAjaxOnPage = self;
									}
								});
							}
						};

					memberAjaxOnPage.build();

					$this.data('memberAjaxOnPage', memberAjaxOnPage);
				});

				return self;
			}
		}

	});

	// Key Press
	$(document.documentElement).on('keyup', function(e) {
		try {
			if (!activeMemberAjaxOnPage) return;
			// Next
			if (e.keyCode == 39) {
				activeMemberAjaxOnPage.next();
			}
			// Prev
			if (e.keyCode == 37) {
				activeMemberAjaxOnPage.prev();
			}
		} catch(err) {}
	});

}).apply(this, [window.theme, jQuery]);

// Filter Zoom
(function(theme, $) {
	'use strict';

	theme = theme || {};

	$.extend(theme, {

		FilterZoom: {

			defaults: {
				elements: null
			},

			initialize: function($elements) {
				this.$elements = ($elements || this.defaults.elements);

				this.build();

				return this;
			},

			build: function() {
				var self = this;

				self.$elements.each(function() {
					var $this = $(this),
						zoom = $this.find('.zoom, .thumb-info-zoom').get(0);

					if (!zoom) return;

					$this.find('.zoom, .thumb-info-zoom').unbind('click');
					var links = [];
					var i = 0;
					$this.find('article').each(function() {
						var $that = $(this);
						if ($that.css('display') != 'none') {
							var $zoom = $that.find('.zoom, .thumb-info-zoom'),
								slide,
								src = $zoom.data('src'),
								title = $zoom.data('title');

							$zoom.data('index', i);
							if ($.isArray(src)) {
								$.each(src, function(index, value) {
									slide = {};
									slide.src = value;
									slide.title = title[index];
									links[i] = slide;
									i++;
								});
							} else {
								slide = {};
								slide.src = src;
								slide.title = title;
								links[i] = slide;
								i++;
							}
						}
					});
					$this.find('article').each(function() {
						var $that = $(this);
						if ($that.css('display') != 'none') {
							$that.off('click', '.zoom, .thumb-info-zoom').on('click', '.zoom, .thumb-info-zoom', function(e) {
								var $zoom = $(this), $parent = $zoom.parents('.thumb-info'), offset = 0;
								if ($parent.get(0)) {
									var $slider = $parent.find('.porto-carousel');
									if ($slider.get(0)) {
										offset = $slider.data('owl.carousel').current() - $slider.find('.cloned').length / 2;
									}
								}
								e.preventDefault();
								if ($.fn.magnificPopup) {
									$.magnificPopup.close();
									$.magnificPopup.open($.extend(true, {}, theme.mfpConfig, {
										items: links,
										gallery: {
											enabled: true
										},
										type: 'image'
									}), $zoom.data('index') + offset);
								}
								return false;
							});
						}
					});
				});

				return self;
			}
		}

	});

}).apply(this, [window.theme, jQuery]);


/* initialize */
jQuery(document).ready(function($) {
	'use strict';

	// Visual Composer Image Zoom
	if ($.isFunction($.fn.themeVcImageZoom)) {

		$(function() {
			var $galleryParent = null;
			$('.porto-vc-zoom:not(.manual)').each(function() {
				var $this = $(this),
					opts,
					gallery = $this.attr('data-gallery');

				var pluginOptions = $this.data('plugin-options');
				if (pluginOptions)
					opts = pluginOptions;

				if (typeof opts == "undefined") {
					opts = {};
				}
				opts.container = $this.parent();

				if (gallery == 'true') {
					var container = 'vc_row';

					if ($this.attr('data-container'))
						container = $this.attr('data-container');

					var $parent = $($this.closest('.' + container).get(0));
					if ($parent.length > 0 && $galleryParent != null && $galleryParent.is($parent)) {
						return;
					} else if ($parent.length > 0) {
						$galleryParent = $parent;
					}
					if ($galleryParent != null && $galleryParent.length > 0) {
						opts.container = $galleryParent;
					}
				}

				$this.themeVcImageZoom(opts);
			});
		});
	}

	function porto_modal_open($this) {
		var trigger = $this.data('trigger-id'),
			overlayClass = $this.data('overlay-class'),
			type = $this.data('type');
		if (typeof trigger != 'undefined'/* && $('#' + escape(trigger)).length > 0*/) {
			if (typeof type == 'undefined') {
				type = 'inline';
			}
			if (type == 'inline') {
				trigger = '#' + escape(trigger);
			}
			var args = {
				items: {
					src: trigger
				},
				type: type,
			};
			if ($this.hasClass('porto-onload')) {
				args['callbacks'] = {
					'beforeClose': function() {
						if ($('.mfp-wrap .porto-modal-content .porto-disable-modal-onload').length && $('.mfp-wrap .porto-modal-content .porto-disable-modal-onload').is(':checked')) {
							$.cookie('porto_modal_disable_onload', 'true', { expires : 7 });
						}
					}
				};
			}
			if (typeof overlayClass != "undefined" && overlayClass) {
				args.mainClass = escape(overlayClass);
			}
			$.magnificPopup.open($.extend(true, {}, theme.mfpConfig, args), 0);
		}
	}

	function porto_init_magnific_popup_functions() {
		$('.lightbox:not(.manual)').each(function() {
			var $this = $(this),
				opts;

			var pluginOptions = $this.data('plugin-options');
			if (pluginOptions)
				opts = pluginOptions;

			$this.themeLightbox(opts);
		});

		// Popup with video or map
		$('.porto-popup-iframe').magnificPopup($.extend(true, {}, theme.mfpConfig, {
			disableOn: 700,
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,
			fixedContentPos: false
		}));

		// Popup with ajax
		$('.porto-popup-ajax').magnificPopup($.extend(true, {}, theme.mfpConfig, {
			type: 'ajax'
		}));

		// Popup with content
		$('.porto-popup-content').each(function() {
			var animation = $(this).attr('data-animation');
			$(this).magnificPopup($.extend(true, {}, theme.mfpConfig, {
				type: 'inline',
				fixedContentPos: false,
				fixedBgPos: true,
				overflowY: 'auto',
				closeBtnInside: true,
				preloader: false,
				midClick: true,
				removalDelay: 300,
				mainClass: animation
			}));
		});

		// Porto Modal
		$('.popup-youtube, .popup-vimeo, .popup-gmaps').each(function(index) {
			var overlayClass = $(this).find('.porto-modal-trigger').data('overlay-class'),
				args = {
					type: 'iframe',
					removalDelay: 160,
					preloader: false,

					fixedContentPos: false
				};
			if (typeof overlayClass != "undefined" && overlayClass) {
				args.mainClass = escape(overlayClass);
			}
			$(this).magnificPopup(args);
		});

		if ($('.porto-modal-trigger.porto-onload').length > 0) {
			var $obj = $('.porto-modal-trigger.porto-onload').eq(0),
				timeout = 0;
			if ($obj.data('timeout')) {
				timeout = parseInt($obj.data('timeout'), 10);
			}
			$(window).on('load', function() {
				setTimeout(function() {
					porto_modal_open($obj);
				}, timeout);
			});
		}
		$('.porto-modal-trigger').on('click', function(e) {
			e.preventDefault();
			porto_modal_open($(this));
		});

		/* Woocommerce */
		// login popup
		$('.login-popup .porto-link-login, .login-popup .porto-link-register').magnificPopup({
			items: {
				src: theme.ajax_url + '?action=porto_account_login_popup&nonce=' + js_porto_vars.porto_nonce,
				type: 'ajax'
			},
			tLoading: '<i class="porto-loading-icon"></i>',
			callbacks: {
				ajaxContentAdded: function() {
					$(window).trigger('porto_login_popup_opened');
				}
			}
		});

		$('.product-images').magnificPopup(
			$.extend(true, {}, theme.mfpConfig, {
				delegate: '.img-thumbnail a.zoom',
				type: 'image',
				gallery: { enabled:true }
			})
		);
	}

	if ($.isFunction($.fn.magnificPopup)) {
		porto_init_magnific_popup_functions();
	} else {
		setTimeout(function() {
			if ($.isFunction($.fn.magnificPopup)) {
				porto_init_magnific_popup_functions();
			}
		}, 500);
	}

	// Post Ajax Modal
	if (typeof theme.PostAjaxModal !== 'undefined') {
		// Portfolio
		if ($('.page-portfolios').length) {
			theme.PostAjaxModal.initialize($('.page-portfolios'));
		}
		// Member
		if ($('.page-members').length) {
			theme.PostAjaxModal.initialize($('.page-members'), 'member');
		}
	}

	// Portfolio Ajax on Page
	if (typeof theme.PortfolioAjaxPage !== 'undefined') {
		theme.PortfolioAjaxPage.initialize();
	}

	// Post Filter
	if (typeof theme.PostFilter !== 'undefined') {
		// Portfolio
		if ($('.portfolio-filter').length) {
			theme.PostFilter.initialize($('.portfolio-filter'), 'portfolio');
		}
		// Member
		if ($('.member-filter').length) {
			theme.PostFilter.initialize($('.member-filter'), 'member');
		}
		// Faq
		if ($('.faq-filter').length) {
			theme.PostFilter.initialize($('.faq-filter'), 'faq');
		}
	}

	// Member Ajax on Page
	if (typeof theme.MemberAjaxPage !== 'undefined') {
		theme.MemberAjaxPage.initialize();
	}

	// Filter Zooms
	if (typeof theme.FilterZoom !== 'undefined') {
		// Portfolio Filter Zoom
		theme.FilterZoom.initialize($('.page-portfolios'));
		// Member Filter Zoom
		theme.FilterZoom.initialize($('.page-members'));
		// Posts Related Style Filter Zoom
		theme.FilterZoom.initialize($('.blog-posts-related'));
	}

});