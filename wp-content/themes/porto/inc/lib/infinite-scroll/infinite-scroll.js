// Infinite Scroll body
(function(theme, $) {
    'use strict';

    theme = theme || {};

    $.extend(theme, {

        PostsInfinite: {

            defaults: {
                elements: '.' + escape( porto_infinite_scroll.post_type ) + 's-container',
                itemSelector: porto_infinite_scroll.item_selector,
                navSelector  : 'product' == porto_infinite_scroll.post_type ? '.woocommerce-pagination' : 'div.pagination',
                nextSelector : 'product' == porto_infinite_scroll.post_type ? '.woocommerce-pagination .page-numbers a.next' : 'div.pagination a.next',
                loading      : {
                    finishedMsg: "",
                    msgText: porto_infinite_scroll.loader_html,
                    img: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                },
                paginationType: typeof porto_infinite_scroll.pagination_type != 'undefined' ? porto_infinite_scroll.pagination_type : 'infinite_scroll'
            },

            initialize: function($elements, itemSelector) {
                if ('product' == porto_infinite_scroll.post_type) {
                    this.defaults.elements += ':not(.is-shortcode)';
                }
                this.$elements = ($elements || $(this.defaults.elements));
                this.itemSelector = (itemSelector || this.defaults.itemSelector);

                this.defaults.behavior = this.defaults.paginationType;
                this.build().events();

                return this;
            },

            build: function() {
                var self = this;

                self.$elements.each(function() {
                    var $this = $(this), cur_page, max_page, page_path;
                    if ($this.hasClass('skeleton-body')) {
                        return;
                    }
                    if (typeof $this.data('cur_page') != 'undefined') {
                        cur_page = $this.data('cur_page');
                    } else {
                        cur_page = porto_infinite_scroll.cur_page;
                    }
                    if (typeof $this.data('max_page') != 'undefined') {
                        max_page = $this.data('max_page');
                    } else {
                        max_page = porto_infinite_scroll.max_page;
                    }
                    if (typeof $this.data('page_path') != 'undefined') {
                        page_path = $this.data('page_path');
                    } else {
                        page_path = porto_infinite_scroll.page_path;
                    }

                    $this.infinitescroll($.extend(self.defaults, {
                        itemSelector: self.itemSelector,
                        state: {
                            currPage: cur_page
                        },
                        maxPage: max_page,
                        path: function(p) {
                            return page_path.replace('%cur_page%', p);
                        },
                    }), function(posts) {
                        if ('load_more' == self.defaults.paginationType) {
                            var infinitescroll_ins = $this.data('infinitescroll');
                            if (infinitescroll_ins ) {
                                var $nav = $(infinitescroll_ins.options.navSelector);
                                if ($nav.length > 1) {
                                    $nav.each(function() {
                                        if (!$(this).closest('.porto-products').length) {
                                            $nav = $(this);
                                            return;
                                        }
                                    });
                                }
                                $nav.find('.next').text($nav.find('.next').data('text'));
                                if (infinitescroll_ins.options.state.currPage >= max_page) {
                                    $nav.addClass('d-none');
                                }
                            }
                        }
                        var $posts = $(posts);
                        if ($posts.find('.porto-lazyload:not(.lazy-load-loaded)').length) {
                            $posts.find('.porto-lazyload:not(.lazy-load-loaded)').trigger('appear');
                        }
                        theme.refreshVCContent($posts);
                        porto_init();
                        self[porto_infinite_scroll.post_type + 'Behavior']($posts, self, $this);
                        $(window).trigger('resize');
                    });
                });

                self.resize();

                return self;
            },

            resize: function() {
                var self = this;

                if (self.resizeTimer)
                    clearTimeout(self.resizeTimer);
                self.resizeTimer = setTimeout(function() {
                    self.$elements.each(function() {
                        var $this = $(this);
                        if ($().isotope) {
                            if ($this.data('isotope')) {
                                $this.isotope('layout');
                            }
                        }
                    });
                    delete self.resizeTimer;
                }, 800);

                return self;
            },

            events: function() {
                var self = this;

                $(window).on('resize', function() {
                    self.resize();
                });

                return self;
            },

            postBehavior: function( $posts, self, $this ) {
                if ($this.closest('.blog-posts').hasClass('blog-posts-related')) {
                    theme.FilterZoom.initialize($this.closest('.blog-posts'));
                }

                if ($().isotope) {
                    if ($this.data('isotope')) {
                        $this.isotope('appended', $posts);
                        theme.requestTimeout(function() {
                            $this.isotope('layout');
                        }, 50);
                    }
                    $posts.waitForImages(function() {
                        self.resize();
                    });
                }
            },

            portfolioBehavior: function( $posts, self, $this ) {
                /* D3-Start */
                $posts.each(function() {
                    var img_src = $(this).find('.thumb-info-wrapper').children('img').attr('src');
                    $this.find('.porto-portfolios-lighbox-thumbnails > div').append('<span><img src="' + img_src + '" alt="" style="height: 84px;" /></span>');;
                });
                /* End-D3 */
                var $parent = $this.closest('.page-portfolios');

                if ($parent.hasClass('portfolios-timeline')) {
                    var selected = 0;
                    if ($parent.find('.portfolio-filter').length) {
                        var selector = $parent.find('.portfolio-filter .active').attr('data-filter'), easing = "easeInOutQuart", timeout = 300;
                        $posts.each(function() {
                            var $that = $(this);
                            if (selector == '*') {
                                if ($that.css('display') == 'none') $that.stop().slideDown(timeout, easing, function() {
                                    $(this).attr('style', '').show();
                                });
                                selected++;
                            } else {
                                if ($that.hasClass(selector)) {
                                    if ($that.css('display') == 'none') $that.stop().slideDown(timeout, easing, function() {
                                        $(this).attr('style', '').show();
                                    });
                                    selected++;
                                } else {
                                    $that.stop().hide();
                                }
                            }
                        });
                    }
                    if (!selected && $parent.find('.portfolios-infinite').length) {
                        $parent.find('.portfolios-infinite').infinitescroll('retrieve');
                    }
                    theme.FilterZoom.initialize($parent);
                } else {
                    if ($().isotope) {
                        if ($this.data('isotope')) {
                            $this.isotope('appended', $posts);
                            theme.requestTimeout(function() {
                                $this.isotope('layout');
                            }, 50);
                        }
                        $posts.waitForImages(function() {
                            self.resize();
                        });
                    }
                }

                if ($parent.data('portfolioAjaxOnPage')) {
                    $parent.data('portfolioAjaxOnPage').build();
                }
                if ($parent.data('portfolioAjaxOnModal')) {
                    $parent.data('portfolioAjaxOnModal').build($parent, 'portfolio');
                }
            },

            memberBehavior: function( $posts, self, $this ) {
                if ($().isotope) {
                    if ($this.data('isotope')) {
                        $this.isotope('appended', $posts);
                        theme.requestTimeout(function() {
                            $this.isotope('layout');
                        }, 50);
                    }
                    $posts.waitForImages(function() {
                        self.resize();
                    });
                }
            },

            faqBehavior: function( $posts, self, $this ) {
                var $parent = $this.closest('.page-faqs');

                var selected = 0;
                if ($parent.find('.faq-filter').length) {
                    var selector = $parent.find('.faq-filter .active').attr('data-filter'), easing = "easeInOutQuart", timeout = 300;
                    $posts.each(function() {
                        var $that = $(this);
                        if (selector == '*') {
                            if ($that.css('display') == 'none') $that.stop().slideDown(timeout, easing, function() {
                                $(this).attr('style', '').show();
                            });
                            selected++;
                        } else {
                            if ($that.hasClass(selector)) {
                                if ($that.css('display') == 'none') $that.stop().slideDown(timeout, easing, function() {
                                    $(this).attr('style', '').show();
                                });
                                selected++;
                            } else {
                                $that.stop().hide();
                            }
                        }
                    });
                }
                if (!selected && $parent.find('.faqs-infinite').length) {
                    $parent.find('.faqs-infinite').infinitescroll('retrieve');
                }
            },

            productBehavior: function( $posts, self, $this ) {
                porto_woocommerce_init();
                // reset variations form
                porto_woocommerce_variations_init($posts);
            },

            btnAction: function(e) {
                e.preventDefault();
                if (e.data) {
                    $(this).data('text', $(this).text());
                    $(this).text(porto_infinite_scroll.loader_text);
                    e.data.scroll();
                    $(this).blur();
                }
            }
        }

    });
    
    $.extend($.infinitescroll.prototype, {
        _binding_load_more: function porto_load_more_binding(binding) {
            var instance = this;
            if ('unbind' === binding) {
                (this.options.binder).unbind('smartscroll.infscr.' + instance.options.infid);
            } else {
                $(document).off('click', '.pagination.load-more .next', theme.PostsInfinite.btnAction).on('click', '.pagination.load-more .next', instance, theme.PostsInfinite.btnAction);
            }
        },
        _nearbottom_infinite_scroll: function() {
            var window_height = window.innerHeight || $(window).height();
            return $(window).scrollTop() + window_height > this.element.offset().top + this.element.height();
        }
    });

}).apply(this, [window.theme, jQuery]);

jQuery(document).ready(function() {
    'use strict';

    if (typeof theme.PostsInfinite !== 'undefined') {
        theme.PostsInfinite.initialize();
    }
});