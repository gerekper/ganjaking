// Infinite Scroll body
( function( theme, $ ) {
    'use strict';

    theme = theme || {};

    var instanceName = '__postsinfinite';

    var PostsInfinite = function( $elements, itemSelector, options, post_type ) {
        return this.initialize( $elements, itemSelector, options, post_type );
    };

    PostsInfinite.prototype = {
        initialize: function( $elements, itemSelector, options, post_type ) {
            if ( typeof options != 'undefined' && options ) {
                this.defaults = $.extend( true, {}, options );
                this.post_type = post_type;
            } else {
                this.defaults = {
                    elements: '.' + escape( porto_infinite_scroll.post_type ) + 's-container',
                    itemSelector: porto_infinite_scroll.item_selector,
                    navSelector: 'product' == porto_infinite_scroll.post_type ? '.woocommerce-pagination' : 'div.pagination',
                    nextSelector: 'product' == porto_infinite_scroll.post_type ? '.woocommerce-pagination .page-numbers a.next' : 'div.pagination a.next',
                    loading: {
                        finishedMsg: "",
                        msgText: porto_infinite_scroll.loader_html,
                        img: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                    },
                    paginationType: typeof porto_infinite_scroll.pagination_type != 'undefined' ? porto_infinite_scroll.pagination_type : 'infinite_scroll'
                };

                if ( 'product' == porto_infinite_scroll.post_type ) {
                    this.defaults.elements += ':not(.is-shortcode)';
                }
                this.post_type = porto_infinite_scroll.post_type;
            }

            this.$elements = ( $elements || $( this.defaults.elements + ':not(.skeleton-body)' ) );
            this.itemSelector = ( itemSelector || this.defaults.itemSelector );

            if ( typeof options != 'undefined' && options ) {
                this.$elements.data( 'infiniteoptions', options );
            }

            this.defaults.behavior = this.defaults.paginationType;


            var oldins = this.$elements.data( instanceName );
            if ( oldins ) {
                oldins.destroy();
            }

            this.$elements.data( instanceName, this );
            this.build().events();

            return this;
        },

        build: function() {
            var self = this;

            self.$elements.each( function() {
                var $this = $( this ), cur_page, max_page, page_path;
                if ( $this.hasClass( 'skeleton-body' ) ) {
                    return;
                }
                if ( typeof $this.data( 'cur_page' ) != 'undefined' ) {
                    cur_page = $this.data( 'cur_page' );
                } else {
                    cur_page = porto_infinite_scroll.cur_page;
                }
                if ( typeof $this.data( 'max_page' ) != 'undefined' ) {
                    max_page = $this.data( 'max_page' );
                } else {
                    max_page = porto_infinite_scroll.max_page;
                }

                $this.infinitescroll( $.extend( self.defaults, {
                    itemSelector: self.itemSelector,
                    state: {
                        currPage: cur_page
                    },
                    maxPage: max_page,
                    infid: theme.PostsInfinite.index,
                    path: function( p ) {
                        if ( typeof $this.data( 'page_path' ) != 'undefined' ) {
                            page_path = $this.data( 'page_path' );
                            if ( 'portfolio' == self.post_type && $this.closest( '.portfolios-timeline' ).length ) {
                                var last_date = $this.find( '.timeline-date' ).last().data( 'date' );
                                if ( last_date ) {
                                    page_path += '&last_date=' + last_date;
                                }
                            }
                        } else {
                            page_path = porto_infinite_scroll.page_path;
                        }
                        return page_path.replace( '%cur_page%', p );
                    },
                } ), function( posts ) {
                    if ( 'load_more' == self.defaults.paginationType ) {
                        var infinitescroll_ins = $this.data( 'infinitescroll' );
                        if ( infinitescroll_ins ) {
                            var $nav = $( infinitescroll_ins.options.navSelector );
                            if ( $nav.length > 1 ) {
                                $nav.each( function() {
                                    if ( !$( this ).closest( '.porto-products' ).length ) {
                                        $nav = $( this );
                                        return;
                                    }
                                } );
                            }
                            if ( infinitescroll_ins.options.state.currPage >= infinitescroll_ins.options.maxPage ) {
                                $nav.addClass( 'd-none' );
                            } else {
                                $nav.show();
                            }

                            if ( $nav.find( '.next' ).data( 'text' ) ) {
                                $nav.find( '.next' ).text( $nav.find( '.next' ).data( 'text' ) );
                            }
                        }
                    }
                    var $posts = $( posts );
                    if ( $posts.find( '.porto-lazyload:not(.lazy-load-loaded)' ).length ) {
                        var ins = $posts.find( '.porto-lazyload:not(.lazy-load-loaded)' ).themePluginLazyLoad( {} ).data( '__lazyload' );
                        if ( ins && ins.loadAndDestroy ) {
                            ins.loadAndDestroy();
                        }
                    }
                    theme.refreshVCContent( $posts );
                    porto_init();

                    var behavior_action = '';
                    if ( self.post_type != 'product' && self.post_type != 'member' && self.post_type != 'faq' && self.post_type != 'portfolio' && self.post_type != 'post' ) {
                        behavior_action = 'ptu';
                    } else {
                        behavior_action = self.post_type;
                    }

                    PostsInfinite[behavior_action + 'Behavior']( $posts, $this );

                    // init CountDown after infinite
                    $( document.body ).trigger( 'porto_init_countdown', [$this] );

                    $( window ).trigger( 'resize' );
                } );

                theme.PostsInfinite.index++;
            } );

            self.resize();

            return self;
        },

        resize: function( e ) {
            var self;
            if ( typeof e == 'undefined' || typeof e.data == 'undefined' || !e.data.ins ) {
                self = this;
            } else {
                self = e.data.ins;
            }
            if ( self.$elements.length ) {
                if ( !$( document.body ).find( self.$elements ).length ) {
                    self.destroy();
                    return false;
                }
            }
            var $elements = self.$elements;
            if ( self.resizeTimer ) {
                clearTimeout( self.resizeTimer );
            }
            self.resizeTimer = setTimeout( function() {
                $elements.each( function( index ) {
                    var $this = $( this );
                    if ( $().isotope ) {
                        if ( $this.data( 'isotope' ) ) {
                            $this.isotope( 'layout' );
                        }
                    }
                } );
                delete self.resizeTimer;
            }, 800 );

            return self;
        },

        events: function() {
            var self = this;

            $( window ).on( 'resize', { ins: self }, self.resize );

            return self;
        },

        destroy: function() {
            var self = this;
            $( window ).off( 'resize', self.resize );

            self.$elements.removeData( instanceName );

            return self;
        }
    };

    $.extend( PostsInfinite, {
        index: 0,

        globalBehavior: function( $elements ) {
            $elements.each( function() {
                var $this = $( this );
                if ( $this.hasClass( 'owl-carousel' ) ) {
                    $this.removeData( '__carousel' );
                    $this.removeClass( 'owl-loaded' ).trigger( 'destroy.owl.carousel' );
                    $this.find( '#infscr-loading' ).remove();
                    if ( typeof $.fn.themeCarousel == 'function' ) {
                        $this.themeCarousel( $this.data( 'plugin-options' ) );
                    }
                }
            } );

            if ( $().isotope ) {
                setTimeout( function() {
                    $elements.each( function() {
                        var $this = $( this );
                        if ( $this.data( 'isotope' ) ) {
                            $this.isotope( 'layout' );
                        }
                    } );
                }, 800 );
            }
        },

        postBehavior: function( $posts, $this ) {
            var self = this;
            if ( $this.closest( '.blog-posts' ).hasClass( 'blog-posts-related' ) ) {
                theme.FilterZoom.initialize( $this.closest( '.blog-posts' ) );
            }

            if ( $().isotope ) {
                if ( $this.data( 'isotope' ) ) {
                    $this.isotope( 'appended', $posts );
                    theme.requestTimeout( function() {
                        $this.isotope( 'layout' );
                    }, 50 );
                }
            }
            if ( self ) {
                $posts.imagesLoaded( function() {
                    self.globalBehavior( $this );
                } );
            }
        },

        portfolioBehavior: function( $posts, $this ) {
            var self = this;
            /* D3-Start */
            $posts.each( function() {
                var img_src = $( this ).find( '.thumb-info-wrapper' ).children( 'img' ).attr( 'src' );
                $this.find( '.porto-portfolios-lighbox-thumbnails > div' ).append( '<span><img src="' + img_src + '" alt="" style="height: 84px;" /></span>' );;
            } );
            /* End-D3 */
            var $parent = $this.closest( '.page-portfolios' );

            if ( $parent.hasClass( 'portfolios-timeline' ) ) {
                var selected = 0;
                if ( $parent.find( '.portfolio-filter' ).length ) {
                    var selector = $parent.find( '.portfolio-filter .active' ).attr( 'data-filter' ), easing = "easeInOutQuart", timeout = 300;
                    $posts.each( function() {
                        var $that = $( this );
                        if ( $that.hasClass( 'timeline-date' ) ) {
                            return;
                        }
                        if ( selector == '*' ) {
                            if ( $that.css( 'display' ) == 'none' ) $that.stop().slideDown( timeout, easing, function() {
                                $( this ).attr( 'style', '' ).show();
                            } );
                            selected++;
                        } else {
                            if ( $that.hasClass( selector ) ) {
                                if ( $that.css( 'display' ) == 'none' ) $that.stop().slideDown( timeout, easing, function() {
                                    $( this ).attr( 'style', '' ).show();
                                } );
                                selected++;
                            } else {
                                $that.stop().hide();
                            }
                        }
                    } );
                }
                if ( !selected && $parent.find( '.portfolios-infinite' ).length ) {
                    $parent.find( '.portfolios-infinite' ).infinitescroll( 'retrieve' );
                }
                theme.FilterZoom.initialize( $parent );
            } else {
                if ( $().isotope ) {
                    if ( $this.data( 'isotope' ) ) {
                        $this.isotope( 'appended', $posts );
                        theme.requestTimeout( function() {
                            $this.isotope( 'layout' );
                        }, 50 );
                    }
                }
                if ( self ) {
                    $posts.imagesLoaded( function() {
                        self.globalBehavior( $this );
                    } );
                }
            }

            if ( $parent.data( 'portfolioAjaxOnPage' ) ) {
                $parent.data( 'portfolioAjaxOnPage' ).build();
            }
            if ( $parent.data( 'portfolioAjaxOnModal' ) ) {
                $parent.data( 'portfolioAjaxOnModal' ).build( $parent, 'portfolio' );
            }
        },

        memberBehavior: function( $posts, $this ) {
            var self = this;
            if ( $().isotope ) {
                if ( $this.data( 'isotope' ) ) {
                    $this.isotope( 'appended', $posts );
                    theme.requestTimeout( function() {
                        $this.isotope( 'layout' );
                    }, 50 );
                }
            }
            if ( self ) {
                $posts.imagesLoaded( function() {
                    self.globalBehavior( $this );
                } );
            }

            var $parent = $this.closest( '.page-members' );
            if ( $parent.length ) {
                if ( $parent.data( 'memberAjaxOnPage' ) ) {
                    $parent.data( 'memberAjaxOnPage' ).build();
                }
                if ( $parent.data( 'memberAjaxOnModal' ) ) {
                    $parent.data( 'memberAjaxOnModal' ).build( $parent, 'member' );
                }
            }
        },

        faqBehavior: function( $posts, $this ) {
            var self = this,
                $parent = $this.closest( '.page-faqs' );

            var selected = 0;
            if ( $parent.find( '.faq-filter' ).length ) {
                var selector = $parent.find( '.faq-filter .active' ).attr( 'data-filter' ), easing = "easeInOutQuart", timeout = 300;

                $posts.each( function() {
                    var $that = $( this );
                    if ( selector == '*' ) {
                        if ( $that.css( 'display' ) == 'none' ) $that.stop().slideDown( timeout, easing, function() {
                            $( this ).attr( 'style', '' ).show();
                        } );
                        selected++;
                    } else {
                        if ( $that.hasClass( selector ) ) {
                            if ( $that.css( 'display' ) == 'none' ) $that.stop().slideDown( timeout, easing, function() {
                                $( this ).attr( 'style', '' ).show();
                            } );
                            selected++;
                        } else {
                            $that.stop().hide();
                        }
                    }
                } );
            }
            if ( !selected && $parent.find( '.faqs-infinite' ).length ) {
                $parent.find( '.faqs-infinite' ).infinitescroll( 'retrieve' );
            }

            if ( self ) {
                $posts.imagesLoaded( function() {
                    self.globalBehavior( $this );
                } );
            }
        },

        productBehavior: function( $posts, $this ) {
            var self = this;
            if ( $().isotope ) {
                if ( $this.data( 'isotope' ) ) {
                    $this.isotope( 'appended', $posts );
                    theme.requestTimeout( function() {
                        $this.isotope( 'layout' );
                    }, 50 );
                }
            }
            if ( self ) {
                $posts.imagesLoaded( function() {
                    self.globalBehavior( $this );
                } );
            }

            // in shop page
            if ( $posts.closest( '.archive-products' ).length ) {
                // update result count
                var $count = $( '.woocommerce-result-count' ),
                    $parent = $posts.closest( '.archive-products' );
                if ( $count.length ) {
                    var $newCount = $posts.closest( '.content-area' ).find( '.woocommerce-result-count' ).eq( 0 );
                    $count[0].outerHTML = $newCount.length ? $newCount[0].outerHTML : '';
                }

                // Ajax First Load for Yith Wishlist
                if ( ( 'undefined' !== typeof yith_wcwl_l10n ) && yith_wcwl_l10n.enable_ajax_loading ) {
                    $parent.trigger( 'yith_wcwl_reload_fragments' );
                }
            }

            porto_woocommerce_init();
            // reset variations form
            porto_woocommerce_variations_init( $posts );
        },

        ptuBehavior: function( $posts, $this ) {
            var self = this;
            if ( $().isotope ) {
                if ( $this.data( 'isotope' ) ) {
                    $this.isotope( 'appended', $posts );
                    theme.requestTimeout( function() {
                        $this.isotope( 'layout' );
                    }, 50 );
                }
            }
            if ( self ) {
                $posts.imagesLoaded( function() {
                    self.globalBehavior( $this );
                } );
            }
        },

        btnAction: function( e ) {
            e.preventDefault();
            if ( $( this ).closest( '.porto-products' ).length ) {
                return;
            }
            var data_obj;
            if ( $( this ).closest( '.porto-ajax-load' ).length ) {
                var post_type = $( this ).closest( '.porto-ajax-load' ).data( 'post_type' ),
                    infinitescroll_ins = $( this ).closest( '.porto-ajax-load' ).find( '.' + post_type + 's-container' ).data( 'infinitescroll' );
                if ( infinitescroll_ins ) {
                    data_obj = infinitescroll_ins;
                }
            } else {
                data_obj = e.data;
            }
            if ( data_obj ) {
                $( this ).data( 'text', $( this ).text() );
                $( this ).text( typeof porto_infinite_scroll != 'undefined' ? porto_infinite_scroll.loader_text : js_porto_vars.loader_text );
                data_obj.scroll();
                $( this ).blur();
            }
        }
    } );

    $.extend( theme, {
        PostsInfinite: PostsInfinite
    } );

    $.fn.portoInfiniteScroll = function( page_path ) {
        return this.map( function() {
            var $this = $( this ),
                $shortcode_id = $this.find( '.shortcode-id' );
            if ( !$shortcode_id.length ) {
                return;
            }
            var shortcode_id = $shortcode_id.val(),
                post_type = $this.data( 'post_type' ),
                $wrap = $this.closest( '.porto-' + ( 'post' == post_type ? 'blog' : post_type + 's' ) ),
                wrap_cls = '.porto-' + ( 'post' == post_type ? 'blog' : post_type + 's' ) + shortcode_id;

            if ( !$wrap.length ) {
                $wrap = $this.closest( '.porto-ajax-load' );
            }
            if ( !$wrap.length ) {
                return;
            }
            /*if ( ! $wrap.find( '.pagination-wrap' ).length ) {
                return;
            }*/
            var wrap_id = $wrap.attr( 'id' ),
                $posts_wrap = $wrap.find( '.' + post_type + 's-container' ),
                extra_options = $this.data( 'ajax_load_options' ),
                item_selector = '.' + post_type + ', .timeline-date',
                ajax_call_url = page_path;
            if ( extra_options && extra_options.post_found_nothing ) {
                extra_options.post_found_nothing = encodeURI( extra_options.post_found_nothing );
            }
            // is archive
            if ( $posts_wrap.closest( '.archive-posts' ).length ) {
                var next_page_url = $posts_wrap.siblings( '.pagination-wrap' ).find( '.next' ).attr( 'href' );
                if ( !next_page_url ) {
                    next_page_url = window.location.href;
                }
                next_page_url += ( -1 !== next_page_url.indexOf( '?' ) ? '&' : '?' ) + 'portoajax=1&load_posts_only=2&type=load_more';
                ajax_call_url = next_page_url.replace( /(paged=)(\d+)|(page\/)(\d+)/, '$1$3%cur_page%' );
                item_selector = '.archive-posts .' + post_type;
            } else {
                ajax_call_url += '&post_type=' + post_type + ( extra_options ? '&extra=' + encodeURI( JSON.stringify( extra_options ) ) : '' );
            }
            $posts_wrap.data( 'page_path', ajax_call_url );
            var pagination_type = $this.hasClass( 'load-infinite' ) ? 'infinite_scroll' : 'load_more',
                ins = new theme.PostsInfinite( $posts_wrap, item_selector, {
                    navSelector: 'product' == post_type && typeof extra_options.shortcode == 'undefined' ? wrap_cls + ' .woocommerce-pagination' : wrap_cls + ' div.pagination',
                    nextSelector: 'product' == post_type && typeof extra_options.shortcode == 'undefined' ? wrap_cls + ' .woocommerce-pagination .page-numbers a.next' : wrap_cls + ' div.pagination a.next',
                    loading: {
                        finishedMsg: "",
                        msgText: '<div class="bounce-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>',
                        img: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                    },
                    paginationType: pagination_type
                }, post_type );
        } );
    };

} ).apply( this, [window.theme, jQuery] );

jQuery( window ).on( 'load', function() {
    'use strict';

    var $ = jQuery;

    if ( typeof $.infinitescroll != 'undefined' ) {
        $.extend( $.infinitescroll.prototype, {
            _binding_load_more: function porto_load_more_binding( binding ) {
                var instance = this;
                if ( 'unbind' === binding ) {
                    ( this.options.binder ).off( 'smartscroll.infscr.' + instance.options.infid );
                } else {
                    $( document ).off( 'click', '.pagination.load-more .next', theme.PostsInfinite.btnAction ).on( 'click', '.pagination.load-more .next', instance, theme.PostsInfinite.btnAction );
                }
            },
            _nearbottom_load_more: function() {
                return true;
            },
            _nearbottom_infinite_scroll: function() {
                var window_height = window.innerHeight || $( window ).height();
                return $( window ).scrollTop() + window_height > this.element.offset().top + this.element.height();
            }
        } );
    }

    if ( typeof theme.PostsInfinite !== 'undefined' && typeof porto_infinite_scroll !== 'undefined' && !$( document ).find( '.archive-posts-infinite, .porto-ajax-load[data-ajax_load_options]' ).length ) {
        new theme.PostsInfinite();
    }
    var $infinite_objs = $( '.porto-ajax-load.load-infinite, .porto-ajax-load.load-more' );
    if ( $infinite_objs.length ) {

        var current_url = window.location.href;
        if ( -1 !== current_url.indexOf( '#' ) ) {
            current_url = current_url.split( '#' )[0];
        }
        var page_path = theme.ajax_url + '?action=porto_ajax_posts&nonce=' + js_porto_vars.porto_nonce + '&current_link=' + current_url + '&page=%cur_page%&category=';

        $infinite_objs.portoInfiniteScroll( page_path );
    }
} );
