jQuery(document).ready(function($) {
    'use strict';

    $(document.body).on('mouseenter', '.porto-tooltip', function() {
        $(this).stop(true, true).show();
        var obj = $(this).data('triggerObj');
        if (obj) {
            obj.addClass('porto-tooltip-active');
        }
    }).on('mouseleave', '.porto-tooltip', function() {
        $(this).stop().fadeOut(400);
        var obj = $(this).data('triggerObj');
        if (obj) {
            obj.removeClass('porto-tooltip-active');
        }
    });
    $(document.body).on('click', '.porto-tooltip', function() {
        var initCall = $(this).data('initCall');
        if (initCall) {
            initCall.call(this, $(this).data('triggerObj'));
        }
    });

    $.fn.portoTooltip = function(options) {
        options.target = escape(options.target.replace(/"/g, ''));
        $('.porto-tooltip[data-target="' + options.target + '"]').remove();
        return $(this).each(function() {
            if ($(this).hasClass('porto-tooltip-initialized')) {
                return;
            }

            var $this = $(this),
                $tooltip = $('<div class="porto-tooltip" data-target="' + options.target + '" style="display: none; position: absolute; z-index: 9999;">' + options.text + '</div>').appendTo('body');
            $tooltip.data('triggerObj', $this);
            if (options.init) {
                $tooltip.data('initCall', options.init);
            }
            $this.mouseenter(function() {
                $tooltip.text(options.text);
                if (options.position == 'top') {
                    $tooltip.css('top', $this.offset().top - $tooltip.outerHeight() / 2).css('left', $this.offset().left + $this.outerWidth() / 2 - $tooltip.outerWidth() / 2);
                } else if (options.position == 'bottom') {
                    $tooltip.css('top', $this.offset().top + $this.outerHeight() - $tooltip.outerHeight() / 2).css('left', $this.offset().left + $this.outerWidth() / 2 - $tooltip.outerWidth() / 2);
                } else if (options.position == 'left') {
                    $tooltip.css('top', $this.offset().top + $this.outerHeight() / 2 - $tooltip.outerHeight() / 2).css('left', $this.offset().left - $tooltip.outerWidth() / 2);
                } else if (options.position == 'right') {
                    $tooltip.css('top', $this.offset().top + $this.outerHeight() / 2 - $tooltip.outerHeight() / 2).css('left', $this.offset().left + $this.outerWidth() - $tooltip.outerWidth() / 2);
                }
                $tooltip.stop().fadeIn(100);
                $this.addClass('porto-tooltip-active');
            }).mouseleave(function() {
                $tooltip.stop(true, true).fadeOut(400);
                $this.removeClass('porto-tooltip-active');
            }).addClass('porto-tooltip-initialized');
        });
    };

    function initTooltipSection(e, $obj) {
        if (e.elementID && 'custom' != e.type) {
            if (!e.type) {
                e.type = 'section';
            }
            window.parent.wp.customize[e.type](e.elementID).focus();
            if (e.type == 'section' && window.parent.wp.customize[e.type](e.elementID).contentContainer) {
                window.parent.jQuery('body').trigger('initReduxFields', [window.parent.wp.customize[e.type](e.elementID).contentContainer]);
            } else if (e.type == 'control' && window.parent.wp.customize[e.type](e.elementID).container) {
                window.parent.jQuery('body').trigger('initReduxFields', [window.parent.wp.customize[e.type](e.elementID).container.closest('.control-section')]);
            }
        } else if ('custom' == e.type && e.elementID) {
            window.parent.wp.customize.section('porto_header_layouts').focus();
            var index = $(e.target, '.header-wrapper').index($obj),
                isMobile = $obj.closest('.visible-for-sm:visible').length ? true : false;
            $('.porto-header-builder .header-wrapper-' + (isMobile ? 'mobile' : 'desktop') + ' .header-builder-wrapper', window.parent.document).find('[data-id="' + e.elementID + '"]').eq(index).trigger('click');
        }
    }

    function initCustomizerTooltips($parent) {
        if (typeof window.parent.jQuery.redux == 'undefined') {
            return;
        }
        tooltips.forEach( function(e) {
            if ($(e.target).is($parent) || $parent.find($(e.target)).length) {
                e.type || (e.type = 'control');
                $(e.target).portoTooltip({
                    position: e.pos,
                    text: e.text,
                    target: e.target,
                    init: function($obj) {
                        initTooltipSection(e, $obj);
                    }
                });
            }
        });
    }
    var hasSelectiveRefresh = (
        'undefined' !== typeof wp &&
        wp.customize &&
        wp.customize.selectiveRefresh &&
        wp.customize.widgetsPreview &&
        wp.customize.widgetsPreview.WidgetPartial
    );
    if (hasSelectiveRefresh) {

        $(window).load(function() {
            setTimeout(function() {
                $('head > style#porto-style-inline-css').removeAttr('title');
            }, 1000);
        });

        wp.customize.selectiveRefresh.bind('partial-content-rendered', function (placement) {
            initCustomizerTooltips(placement.container);
            $('.partial-refreshing').remove();
            $('head > style#porto-style-inline-css').removeAttr('title');
            if (placement.partial.id == 'header-wrapper' || placement.partial.id == 'header') {
                if ('side' == wp.customize.instance('porto_header_builder[type]').get()) {
                    $('.header-wrapper').addClass('header-side-nav side-nav-wrap');
                    $('.page-wrapper').addClass('side-nav');
                    $(document.body).addClass('body-side');
                } else {
                    $('.header-wrapper').removeClass('header-side-nav').removeClass('side-nav-wrap');
                    $('.page-wrapper').removeClass('side-nav');
                    $(document.body).removeClass('body-side');
                }

                if ($('#header .main-menu').length && typeof theme.MegaMenu !== 'undefined') {
                    theme.MegaMenu.initialize($('#header .main-menu'));
                }
                if ($('#header .sidebar-menu').length && typeof theme.SidebarMenu !== 'undefined') {
                    theme.SidebarMenu.initialize($('#header .sidebar-menu'), $('.widget_sidebar_menu .widget-title .toggle'), $('#main-toggle-menu .menu-title'));
                    $('.sidebar-menu.side-menu-accordion').themeAccordionMenu({'open_one':true});
                }
                if ($('.header-side-nav #header').length && typeof theme.SideNav !== 'undefined') {
                    theme.SideNav.initialize($('.header-side-nav #header'));
                }
                if (typeof theme.StickyHeader !== 'undefined') {
                    theme.StickyHeader.initialize($('#header'));
                }
                if (typeof theme.Search !== 'undefined') {
                    theme.Search.initialize($('#header .searchform-popup'), $('#header .searchform'));
                }
            } else if (placement.partial.id == 'searchform') {
                // Search
                if (typeof theme.Search !== 'undefined') {
                    theme.Search.initialize($('#header .searchform-popup'));
                }
            } else if ('refresh_css_header' == placement.partial.id || 'refresh_css_header_builder' == placement.partial.id) {
                $('head > style#porto-style-inline-css-temp').remove();
                $('head > style#porto-style-inline-css').clone().attr('id', 'porto-style-inline-css-temp').insertAfter($('head > style#porto-style-inline-css'));
            } else if ('breadcrumb' == placement.partial.id) {
                if ($('#breadcrumbs-boxed #breadcrumbs-boxed').length) {
                    $('#breadcrumbs-boxed #breadcrumbs-boxed').children().appendTo($('#breadcrumbs-boxed #breadcrumbs-boxed').closest('#breadcrumbs-boxed'));
                    $('#breadcrumbs-boxed #breadcrumbs-boxed').remove();
                }
            } else if (('single-post' == placement.partial.id || 'single-portfolio' == placement.partial.id) && placement.container.length) {
                placement.container.find('.porto-carousel:not(.manual)').each(function() {
                    var $this = $(this),
                        pluginOptions = $this.data('plugin-options');
                    $this.themeCarousel(pluginOptions);
                });
                placement.container.find('.porto-lazyload').themePluginLazyLoad({effect: 'fadeIn', effect_speed: 400});
                if (placement.container.find('.porto-lazyload').closest('.owl-carousel').length) {
                    placement.container.find('.porto-lazyload').closest('.owl-carousel').on('changed.owl.carousel', function() {
                        $(this).find('.porto-lazyload:not(.lazy-load-loaded)').trigger('appear');
                    });
                }
                placement.container.find('[data-tooltip]').tooltip();
            } else if (('archive-product' == placement.partial.id || 'single-product' == placement.partial.id || 'single-product-related' == placement.partial.id || 'single-product-upsells' == placement.partial.id) && placement.container.length) {
                //theme.refreshVCContent(placement.container);
                porto_init();
                typeof porto_woocommerce_init == 'function' && porto_woocommerce_init();
                theme.WooProductImageSlider.initialize(placement.container.find('.product-image-slider'));
            }
            if ('breadcrumb' == placement.partial.id || 'footer' == placement.partial.id) {
                if (placement.container.length && typeof placement.container.data('plugin-parallax') != 'undefined') {
                    placement.container.themeParallax(placement.container.data('plugin-options'));
                }
            }
            if ('breadcrumb' == placement.partial.id) {
                if ($('.page-top.d-none').length) {
                    $('div#main').addClass('no-breadcrumbs');
                } else {
                    $('div#main').removeClass('no-breadcrumbs');
                }
            }
        });
    }
    var tooltips = [{
        target: '#header .custom-html',
        text: 'HTML',
        elementID: 'html',
        pos: 'top',
        type: 'custom'
    }, {
        target: '#header .porto-block',
        text: 'Porto Block',
        elementID: 'porto_block',
        pos: 'top',
        type: 'custom'
    }, {
        target: '.porto-html-block',
        text: 'HTML Block',
        elementID: 'html-blocks',
        pos: 'top',
        type: 'section'
    }, {
        target: '#header .logo',
        text: 'Logo',
        elementID: 'logo-icons',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '#header .header-top',
        text: 'Header Top',
        elementID: 'porto_settings[header-top-bg-color]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '#header .header-main',
        text: 'Header Main',
        elementID: 'porto_settings[header-bg]',
        pos: 'top',
        type: 'control'
    }, {
        target: '#header .header-bottom',
        text: 'Header Bottom',
        elementID: 'porto_settings[header-bottom-text-color]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '#header .header-top .top-links',
        text: 'Header Top Links',
        elementID: 'porto_settings[header-top-link-color]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '.menu-custom-block',
        text: 'Custom Menu',
        elementID: 'porto_settings[menu-block]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '#header',
        text: 'Header',
        elementID: 'skin-header',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '#header .main-menu',
        text: 'Main Menu',
        elementID: 'skin-main-menu',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '.page-top',
        text: 'Breadcrumbs',
        elementID: 'header-breadcrumb',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '#footer',
        text: 'Footer',
        elementID: 'footer-settings',
        pos: 'top',
        type: 'section'
    }, {
        target: '.mobile-toggle',
        text: 'Mobile Menu',
        elementID: 'mobile-panel-settings',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '#header .view-switcher',
        text: 'Language Switcher',
        elementID: 'skin-view-currency-switcher',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '#header .currency-switcher',
        text: 'Currency Switcher',
        elementID: 'skin-view-currency-switcher',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '#header .searchform-popup',
        text: 'Search Form',
        elementID: 'skin-search-form',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '#mini-cart',
        text: 'Mini Cart',
        elementID: 'skin-mini-cart',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '.product .labels .onhot',
        text: 'Hot Label',
        elementID: 'porto_settings[hot-color]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '.product .labels .onsale',
        text: 'Sale Label',
        elementID: 'porto_settings[sale-color]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '.yith-wcwl-add-to-wishlist a, .yith-wcwl-add-to-wishlist span',
        text: 'Wishlist',
        elementID: 'porto_settings[wishlist-color]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '.add_to_cart_button, .add_to_cart_read_more',
        text: 'Add To Cart Button',
        elementID: 'porto_settings[add-to-cart-font]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '#header .welcome-msg',
        text: 'Wecome Message',
        elementID: 'porto_settings[welcome-msg]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '#header .header-contact',
        text: 'Contact Info',
        elementID: 'porto_settings[header-contact-info]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '#header .share-links',
        text: 'Social Links',
        elementID: 'porto_settings[show-header-socials]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '#footer .logo',
        text: 'Footer Logo',
        elementID: 'porto_settings[footer-logo]',
        pos: 'top',
        type: 'control'
    }, {
        target: '#footer .footer-ribbon',
        text: 'Footer Ribbon',
        elementID: 'porto_settings[footer-ribbon]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: '#footer .footer-copyright',
        text: 'Copyright',
        elementID: 'porto_settings[footer-copyright]',
        pos: 'top',
        type: 'control'
    }, {
        target: '#footer .footer-payment-img',
        text: 'Payment Image',
        elementID: 'porto_settings[footer-payments-image]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.content-bottom-wrapper > .row > [class*="col-"]:nth-child(1)',
        text: 'Content Bottom 1',
        elementID: 'sidebar-widgets-content-bottom-1',
        pos: 'top',
        type: 'section'
    }, {
        target: '.content-bottom-wrapper > .row > [class*="col-"]:nth-child(2)',
        text: 'Content Bottom 2',
        elementID: 'sidebar-widgets-content-bottom-2',
        pos: 'top',
        type: 'section'
    }, {
        target: '.content-bottom-wrapper > .row > [class*="col-"]:nth-child(3)',
        text: 'Content Bottom 3',
        elementID: 'sidebar-widgets-content-bottom-3',
        pos: 'top',
        type: 'section'
    }, {
        target: '.content-bottom-wrapper > .row > [class*="col-"]:nth-child(4)',
        text: 'Content Bottom 4',
        elementID: 'sidebar-widgets-content-bottom-4',
        pos: 'top',
        type: 'section'
    }, {
        target: '.footer-main > .container > .row > [class*="col-"]:nth-child(1)',
        text: 'Footer Widget 1',
        elementID: 'sidebar-widgets-footer-column-1',
        pos: 'top',
        type: 'section'
    }, {
        target: '.footer-main > .container > .row > [class*="col-"]:nth-child(2)',
        text: 'Footer Widget 2',
        elementID: 'sidebar-widgets-footer-column-2',
        pos: 'top',
        type: 'section'
    }, {
        target: '.footer-main > .container > .row > [class*="col-"]:nth-child(3)',
        text: 'Footer Widget 3',
        elementID: 'sidebar-widgets-footer-column-3',
        pos: 'top',
        type: 'section'
    }, {
        target: '.footer-main > .container > .row > [class*="col-"]:nth-child(4)',
        text: 'Footer Widget 4',
        elementID: 'sidebar-widgets-footer-column-4',
        pos: 'top',
        type: 'section'
    }, {
        target: '.footer-top > .container',
        text: 'Footer Top Widget',
        elementID: 'sidebar-widgets-footer-top',
        pos: 'top',
        type: 'section'
    }, {
        target: '.footer-bottom .widget',
        text: 'Footer Bottom Widget',
        elementID: 'sidebar-widgets-footer-bottom',
        pos: 'top',
        type: 'section'
    }, {
        target: '.sidebar.porto-blog-sidebar',
        text: 'Blog Sidebar',
        elementID: 'sidebar-widgets-blog-sidebar',
        pos: 'top',
        type: 'section'
    }, {
        target: '.sidebar.porto-home-sidebar',
        text: 'Home Sidebar',
        elementID: 'sidebar-widgets-home-sidebar',
        pos: 'top',
        type: 'section'
    }, {
        target: '.sidebar.porto-woo-category-sidebar',
        text: 'Woo Category Sidebar',
        elementID: 'sidebar-widgets-woo-category-sidebar',
        pos: 'top',
        type: 'section'
    }, {
        target: '.sidebar.porto-woo-category-filter-sidebar',
        text: 'Woo Category Filter',
        elementID: 'sidebar-widgets-woo-category-filter-sidebar',
        pos: 'top',
        type: 'section'
    }, {
        target: '.sidebar.porto-woo-product-sidebar',
        text: 'Woo Product Sidebar',
        elementID: 'sidebar-widgets-woo-product-sidebar',
        pos: 'top',
        type: 'section'
    }, {
        target: 'body.page .page-share',
        text: 'Page Share',
        elementID: 'porto_settings[page-share]',
        pos: 'bottom',
        type: 'control'
    }, {
        target: 'body.blog #main',
        text: 'Blog Layout',
        elementID: 'porto_settings[post-archive-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.blog-posts article.post',
        text: 'Blog Post Layout',
        elementID: 'porto_settings[post-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-post #main',
        text: 'Single Post Page Layout',
        elementID: 'porto_settings[post-single-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-post article.post',
        text: 'Single Post Layout',
        elementID: 'porto_settings[post-content-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.related-posts .post-item',
        text: 'Related Post Style',
        elementID: 'porto_settings[post-related-style]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.post-type-archive-portfolio #main',
        text: 'Portfolio Page Layout',
        elementID: 'porto_settings[portfolio-archive-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.post-type-archive-portfolio .portfolio-row .portfolio',
        text: 'Portfolio Layout',
        elementID: 'porto_settings[portfolio-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-portfolio #main',
        text: 'Single Portfolio Page Layout',
        elementID: 'porto_settings[portfolio-single-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-portfolio article.portfolio',
        text: 'Single Portfolio Layout',
        elementID: 'porto_settings[portfolio-content-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.related-portfolios .portfolio-item',
        text: 'Related Portfolio Layout',
        elementID: 'porto_settings[portfolio-related-style]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.page-events',
        text: 'Event Layout',
        elementID: 'porto_settings[event-archive-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.post-type-archive-member #main',
        text: 'Member Page Layout',
        elementID: 'porto_settings[member-archive-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.member-row .member',
        text: 'Member View Type',
        elementID: 'porto_settings[member-view-type]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-member #main',
        text: 'Single Member Page Layout',
        elementID: 'porto_settings[member-single-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.post-type-archive-faq #main',
        text: 'Faq Page Layout',
        elementID: 'porto_settings[faq-archive-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: 'body.archive.woocommerce-page #main',
        text: 'Shop Page Layout',
        elementID: 'porto_settings[product-archive-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: 'ul.products li.product-col',
        text: 'Product Layout',
        elementID: 'porto_settings[category-addlinks-pos]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-product #main',
        text: 'Single Product Page Layout',
        elementID: 'porto_settings[product-single-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-product #content > .product',
        text: 'Single Product Layout',
        elementID: 'porto_settings[product-single-content-layout]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-product .variations',
        text: 'Product Variation Mode',
        elementID: 'porto_settings[product_variation_display_mode]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.single-product .product-images',
        text: 'Product Image',
        elementID: 'porto_settings[product-thumbs]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.woocommerce-cart #content',
        text: 'Cart Page Version',
        elementID: 'porto_settings[cart-version]',
        pos: 'top',
        type: 'control'
    }, {
        target: '.woocommerce-checkout #content',
        text: 'Checkout Page Version',
        elementID: 'porto_settings[checkout-version]',
        pos: 'top',
        type: 'control'
    }, {
        target: '#footer .follow-us .share-links',
        text: 'Follow Us Widget',
        elementID: 'porto_settings[footer-social-bg-color]',
        pos: 'bottom',
        type: 'control'
    }];
    

    initCustomizerTooltips($(document.body));


    // selective refresh
    function appendStyle(itemId, style) {
        itemId = itemId.replace('[', '').replace(']', '');
        $("style#customize-" + itemId).length ? $("style#customize-" + itemId).text(style) : $("head").append('<style id="customize-' + itemId + '">' + style + "</style>");
    }
    function hexToRGB(hex_color) {
        var hex = hex_color.replace( "#", "" ),
            red = parseInt( hex.length == 3 ? hex.substring( 0, 1 ) . hex.substring( 0, 1 ) : hex.substring( 0, 2 ), 16 ),
            green = parseInt( hex.length == 3 ? hex.substring( 1, 2 ) . hex.substring( 1, 2 ) : hex.substring( 2, 4 ), 16 ),
            blue = parseInt( hex.length == 3 ? hex.substring( 2, 3 ) . hex.substring( 2, 3 ) : hex.substring( 4, 6 ), 16 );
        return red + ',' + green + ',' + blue;
    }
    function isRTL() {
        return theme.rtl;//$('body').hasClass('rtl');
    }

    wp.customize('porto_header_builder[custom_css]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_header_builder[custom_css]', value);
        })
    });
    appendStyle('porto_header_builder[custom_css]', wp.customize.instance('porto_header_builder[custom_css]').get());
    var html_blocks = {'top': ['.page-wrapper', 'before', 'prev'], 'banner': ['.header-wrapper', 'after', 'next'], 'content-top': ['div#main', 'prepend', 'children'], 'content-inner-top': ['div.main-content', 'prepend', 'children'], 'content-inner-bottom': ['div.main-content', 'append', 'children'], 'content-bottom': ['div#main', 'append', 'children'], 'bottom': ['.page-wrapper', 'after', 'next']};
    $.each(html_blocks, function(id, arr) {
        wp.customize('porto_settings[html-' + id + ']', function(e) {
            e.bind(function(value) {
                if (!$(arr[0])[arr[2]]('.porto-block-html-' + id).length) {
                    $(arr[0])[arr[1]]('<div class="porto-html-block porto-block-html-' + id + '"></div>');
                    initCustomizerTooltips($(arr[0])[arr[2]]('.porto-block-html-' + id).parent());
                }
                $(arr[0])[arr[2]]('.porto-block-html-' + id).html(value);
            })
        });
    });

    var bgOptions = {'body-bg': 'body', 'content-bg': '#main', 'content-bottom-bg': '#main .content-bottom-wrapper', 'header-wrap-bg': '.header-wrapper', 'header-bg': '#header .header-main', 'breadcrumbs-bg': '.page-top', 'footer-bg': '#footer', 'footer-main-bg': '#footer .footer-main', 'footer-top-bg': '.footer-top', 'footer-bottom-bg': '#footer .footer-bottom'};
    $.each(bgOptions, function(option_id, css_selector) {
        wp.customize('porto_settings[' + option_id + ']', function(e) {
            e.bind(function(bg) {
                var css = '';
                if (bg) {
                    $.each(bg, function(key, value) {
                        if (value && typeof value != 'object') {
                            if ('background-image' == key) {
                                css += key + ': url(' + value + ');';
                            } else {
                                css += key + ': ' + value + ';';
                            }
                        }
                    });
                }
                if (css) {
                    css = css_selector + '{ ' + css + '}';
                }
                appendStyle('porto_settings[' + option_id + ']', css);
            });
        });
    });
    var bgGradientOptions = {'body-bg-gcolor': 'body', 'header-wrap-bg-gcolor': '.header-wrapper', 'header-bg-gcolor': '#header .header-main', 'content-bg-gcolor': '#main', 'content-bottom-bg-gcolor': '#main .content-bottom-wrapper', 'breadcrumbs-bg-gcolor': '.page-top', 'footer-bg-gcolor': '#footer', 'footer-main-bg-gcolor': '#footer .footer-main', 'footer-top-bg-gcolor': '.footer-top', 'footer-bottom-bg-gcolor': '#footer .footer-bottom'};
    $.each(bgGradientOptions, function(option_id, css_selector) {
        wp.customize('porto_settings[' + option_id + ']', function(e) {
            e.bind(function(bg) {
                var css = '';
                if (bg && bg.from && bg.to) {
                    css += 'background-image: -moz-linear-gradient(top, ' + bg.from + ', ' + bg.to + ');';
                    css += 'background-image: -webkit-gradient(linear, 0 0, 0 100%, from(' + bg.from + '), to(' + bg.to + '));';
                    css += 'background-image: -webkit-linear-gradient(top, ' + bg.from + ', ' + bg.to + ');';
                    css += 'background-image: linear-gradient(to bottom, ' + bg.from + ', ' + bg.to + ');';
                    css += 'background-repeat: repeat-x;';
                    css = css_selector + '{ ' + css + '}';
                }
                appendStyle('porto_settings[' + option_id + ']', css);
            });
        });
    });
    wp.customize('porto_settings[content-bottom-padding]', function(e) {
        e.bind(function(padding) {
            var css = '';
            if (padding['padding-top'] || padding['padding-bottom']) {
                css += '#main .content-bottom-wrapper {';
                if (padding['padding-top']) css += 'padding-top: ' + padding['padding-top'] + 'px;';
                if (padding['padding-bottom']) css += 'padding-bottom: ' + padding['padding-bottom'] + 'px;';
                css += '}';
            }
            appendStyle('porto_settings[content-bottom-padding]', css);
        });
    });
    wp.customize('porto_settings[header-text-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-text-color]', '#header, #header .header-main .header-contact .nav-top > li > a, #header .top-links > li.menu-item:before{color:' + value + '}');
        });
    });
    wp.customize('porto_settings[header-margin]', function(e) {
        e.bind(function(margin) {
            var logo_overlay = wp.customize.instance('porto_settings[logo-overlay]').get();
            if ($('body').hasClass('rtl')) {
                var temp = margin['margin-left'];
                margin['margin-left'] = margin['margin-right'];
                margin['margin-right'] = temp;
            }
            $.each(margin, function(key, value) {
                if (value == '') {
                    margin[key] = '0';
                }
            });
            var css = '@media (min-width: 992px) {';
            css += '#header { margin: ' + margin['margin-top']+'px ' + margin['margin-right']+'px ' + margin['margin-bottom']+'px ' + margin['margin-left']+'px;' + '}';
            if ( margin['margin-top'] && logo_overlay && logo_overlay['url'] ) {
                css += '#header.logo-overlay-header .overlay-logo { top: -' + margin['margin-top'] + 'px }';
                css += '#header.logo-overlay-header.sticky-header .overlay-logo { top: -' + (90 + margin['margin-top']) + 'px }';
            }
            css += '}';
            appendStyle('porto_settings[header-margin]', css);
        });
    });
    wp.customize('porto_settings[header-main-padding]', function(e) {
        e.bind(function(padding) {
            var css = '';
            if (padding['padding-top'] || padding['padding-bottom']) {
                css += '#header .header-main .header-left, #header .header-main .header-center, #header .header-main .header-right, .fixed-header #header .header-main .header-left, .fixed-header #header .header-main .header-right, .fixed-header #header .header-main .header-center {';
                if (padding['padding-top']) css += 'padding-top: ' + padding['padding-top'] + ';';
                if (padding['padding-bottom']) css += 'padding-bottom: ' + padding['padding-bottom'] + ';';
                css += '}';
            }
            appendStyle('porto_settings[header-main-padding]', css);
        });
    });
    wp.customize('porto_settings[header-main-padding-mobile]', function(e) {
        e.bind(function(padding) {
            var css = '';
            if (padding['padding-top'] || padding['padding-bottom']) {
                css += '@media (max-width: 991px) { #header .header-main .header-left, #header .header-main .header-center, #header .header-main .header-right {';
                if (padding['padding-top']) css += 'padding-top: ' + padding['padding-top'] + 'px;';
                if (padding['padding-bottom']) css += 'padding-bottom: ' + padding['padding-bottom'] + 'px;';
                css += '} }';
            }
            appendStyle('porto_settings[header-main-padding-mobile]', css);
        });
    });
    wp.customize('porto_settings[header-opacity]', function(e) {
        e.bind(function(opacity) {
            opacity = opacity.replace('%', '');
            if (opacity == '') {
                opacity = 0.8;
            } else {
                opacity = parseFloat(opacity) / 100;
            }
            var header_bg = wp.customize.instance('porto_settings[header-bg]').get(),
                css = '';
            if (header_bg['background-color'] && 'transparent' != header_bg['background-color']) {
                css += '.fixed-header #header .header-main { background-color: rgba(' + hexToRGB(header_bg['background-color']) + ',' + opacity + '); }';
                css += '@media (min-width: 992px) {';
                css += '.header-wrapper.header-side-nav.fixed-header #header { background-color: rgba(' + hexToRGB(header_bg['background-color']) + ',' + opacity + '); }';
                css += '}';
            }
            appendStyle('porto_settings[header-opacity]', css);
        });
    });
    wp.customize('porto_settings[searchform-opacity]', function(e) {
        e.bind(function(opacity) {
            opacity = opacity.replace('%', '');
            if (opacity == '') {
                opacity = 0.8;
            } else {
                opacity = parseFloat(opacity) / 100;
            }
            var searchform_bg = wp.customize.instance('porto_settings[searchform-bg-color]').get(),
                searchform_border = wp.customize.instance('porto_settings[searchform-border-color]').get(),
                css = '.fixed-header #header .searchform {';
            if (searchform_bg && 'transparent' != searchform_bg) {
                css += 'background-color: rgba(' + hexToRGB(searchform_bg) + ',' + opacity + ');';
            }
            if (searchform_border && 'transparent' != searchform_border) {
                css += 'border-color: rgba(' + hexToRGB(searchform_border) + ',' + opacity + ');';
            }
            appendStyle('porto_settings[searchform-opacity]', css + '}');
        });
    });
    var opacityOptions = {'menuwrap-opacity': ['mainmenu-wrap-bg-color', '.fixed-header #header .main-menu-wrap'], 'menu-opacity': ['mainmenu-bg-color', '.fixed-header #header .main-menu'], 'footer-opacity': ['footer-bottom-bg', '.footer-wrapper.fixed #footer .footer-bottom']};
    $.each(opacityOptions, function(option_id, arr) {
        wp.customize('porto_settings[' + option_id + ']', function(e) {
            e.bind(function(opacity) {
                opacity = opacity.replace('%', '');
                if (opacity == '') {
                    opacity = 0.8;
                } else {
                    opacity = parseFloat(opacity) / 100;
                }
                var bg = wp.customize.instance('porto_settings[' + arr[0] + ']').get();
                if (typeof bg['background-color'] != 'undefined') {
                    bg = bg['background-color'];
                }
                if (bg && 'transparent' != bg) {
                    appendStyle('porto_settings[' + option_id + ']', arr[1] + '{ background-color: rgba(' + hexToRGB(bg) + ',' + opacity + ') }');
                } else {
                    appendStyle('porto_settings[' + option_id + ']', '');
                }
            });
        });
    });
    wp.customize('porto_settings[header-fixed-show-bottom]', function(e) {
        e.bind(function(value) {
            if (value == '1' && $('.header-wrapper').hasClass('fixed-header')) {
                $('.header-wrapper').addClass('header-transparent-bottom-border');
            } else {
                $('.header-wrapper').removeClass('header-transparent-bottom-border');
            }
        });
    });
    wp.customize('porto_settings[header-top-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-top-bg-color]', '.header-top { background-color: ' + value + '; }');
        });
    });
    wp.customize('porto_settings[header-top-height]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-top-height]', value ? '.header-top { min-height: ' + value + 'px; }' : '');
        });
    });
    wp.customize('porto_settings[header-top-font-size]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-top-font-size]', value ? '#header .header-top { font-size: ' + value + 'px; }' : '');
        });
    });
    wp.customize('porto_settings[header-bottom-height]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-bottom-height]', value ? '.header-bottom { min-height: ' + value + 'px; }' : '');
        });
    });
    wp.customize('porto_settings[header-top-bottom-border]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-top-bottom-border]', !value['border-top'] ? '.header-top { border-bottom: none; }' : '.header-top { border-bottom: ' + value['border-top'] + ' solid ' + value['border-color'] + '; }');
        });
    });
    wp.customize('porto_settings[header-top-text-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-top-text-color]', '#header .header-top, .header-top .top-links > li.menu-item:after { color: ' + value + ' }');
        });
    });
    wp.customize('porto_settings[header-top-link-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value && value['regular']) {
                css += '#header .header-top .header-contact a, #header .header-top .top-links > li.menu-item > a, .header-top .welcome-msg a, #header:not(.header-corporate) .header-top .share-links > a, #header.header-19 .searchform-popup .search-toggle { color: ' + value['regular'] + ' }';
            }
            if (value && value['hover']) {
                css += '#header .header-top .header-contact a:hover, #header .header-top .top-links > li.menu-item.active > a, #header .header-top .top-links > li.menu-item:hover > a, #header .header-top .top-links > li.menu-item > a.active, #header .header-top .top-links > li.menu-item.has-sub:hover > a, .header-top .welcome-msg a:hover, #header:not(.header-corporate) .header-top .share-links > a:hover, #header.header-19 .searchform-popup .search-toggle:hover { color: ' + value['hover'] + ' }';
            }
            appendStyle('porto_settings[header-top-link-color]', css);
        });
    });
    wp.customize('porto_settings[header-top-menu-padding]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value['padding-top']) css+= 'padding-top: ' + value['padding-top'] + 'px;';
            if (value['padding-bottom']) css+= 'padding-bottom: ' + value['padding-bottom'] + 'px;';
            if (value['padding-left']) css+= 'padding-left: ' + value['padding-left'] + 'px;';
            if (value['padding-right']) css+= 'padding-right: ' + value['padding-right'] + 'px;';
            if (css) {
                css = '#header .header-top .top-links > li.menu-item > a {' + css + '}';
            }
            appendStyle('porto_settings[header-top-menu-padding]', css);
        });
    });
    wp.customize('porto_settings[header-top-menu-hide-sep]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value == '0') {
                css += '#header .header-top .top-links > li.menu-item:first-child > a { padding-' + (isRTL() ? 'right' : 'left') + ': 0; }';
                css += '#header .header-top .top-links > li.menu-item:last-child:after { display: none; }';
            } else {
                css += '#header .top-links > li.menu-item:after { content: ""; display: none; }';
                css += '#header .header-top .gap { visibility: hidden; }';
            }
            appendStyle('porto_settings[header-top-menu-hide-sep]', css);
        });
    });
    wp.customize('porto_settings[header-bottom-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-bottom-bg-color]', '.header-bottom { background-color: ' + value + '; }');
        });
    });
    wp.customize('porto_settings[header-bottom-container-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-bottom-container-bg-color]', '.header-bottom > .container { background-color: ' + value + '; }');
        });
    });
    wp.customize('porto_settings[header-bottom-text-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[header-bottom-text-color]', '#header .header-bottom { color: ' + value + '; }');
        });
    });
    wp.customize('porto_settings[header-bottom-link-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value['regular']) css += '#header .header-bottom a { color: ' + value['regular'] + '; }';
            if (value['hover']) css += '#header .header-bottom a:hover { color: ' + value['hover'] + '; }';
            appendStyle('porto_settings[header-bottom-link-color]', css);
        });
    });
    wp.customize('porto_settings[side-social-bg-color]', function(e) {
        e.bind(function(value) {
            if ('header_builder' != wp.customize.instance('porto_settings[header-type-select]').get() && 'side' == wp.customize.instance('porto_settings[header-type]').get()) {
                appendStyle('porto_settings[side-social-bg-color]', '.header-wrapper #header .share-links a { background-color: ' + value + '; }');
            }
        });
    });
    wp.customize('porto_settings[side-social-color]', function(e) {
        e.bind(function(value) {
            if ('header_builder' != wp.customize.instance('porto_settings[header-type-select]').get() && 'side' == wp.customize.instance('porto_settings[header-type]').get()) {
                appendStyle('porto_settings[side-social-color]', '.header-wrapper #header .share-links a { color: ' + value + '; }');
            }
        });
    });
    wp.customize('porto_settings[side-copyright-color]', function(e) {
        e.bind(function(value) {
            if ('header_builder' != wp.customize.instance('porto_settings[header-type-select]').get() && 'side' == wp.customize.instance('porto_settings[header-type]').get()) {
                appendStyle('porto_settings[side-copyright-color]', '.header-wrapper #header .header-copyright { color: ' + value + '; }');
            }
        });
    });
    wp.customize('porto_settings[mainmenu-wrap-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[mainmenu-wrap-bg-color]', '.main-menu-wrap { background-color: ' + value + '}');
        });
    });
    var paddingOptions = {'mainmenu-wrap-padding': '.main-menu-wrap', 'mainmenu-wrap-padding-sticky': '#header.sticky-header .main-menu-wrap, #header.sticky-header .header-main.sticky .header-left, #header.sticky-header .header-main.sticky .header-center, #header.sticky-header .header-main.sticky .header-right'};
    $.each(paddingOptions, function(option_id, css_selector) {
        wp.customize('porto_settings[' + option_id + ']', function(e) {
            e.bind(function(value) {
                var css = '';
                if (value['padding-top']) css+= 'padding-top: ' + value['padding-top'] + 'px;';
                if (value['padding-bottom']) css+= 'padding-bottom: ' + value['padding-bottom'] + 'px;';
                if (value['padding-left']) css+= 'padding-' + ( isRTL() ? 'right' : 'left' ) + ': ' + value['padding-left'] + 'px;';
                if (value['padding-right']) css+= 'padding-' + ( isRTL() ? 'left' : 'right' ) + ': ' + value['padding-right'] + 'px;';
                if (css) {
                    css = css_selector + '{' + css + '}';
                }
                appendStyle('porto_settings[' + option_id + ']', css);
            });
        });
    });
    wp.customize('porto_settings[menu-text-transform]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[menu-text-transform]', '#header .menu-custom-block a, .mega-menu > li.menu-item > a, .mega-menu .wide .popup li.sub > a, .header-side .sidebar-menu > li.menu-item > a, .sidebar-menu .wide .popup li.sub > a, .porto-view-switcher .narrow li.menu-item > a { text-transform: ' + value + ' }');
        });
    });
    wp.customize('porto_settings[mainmenu-toplevel-link-color-sticky]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value['regular']) {
                css += '#header.sticky-header .main-menu > li.menu-item > a, #header.sticky-header .main-menu > li.menu-custom-content a { color: ' + value['regular'] + ' }';
            }
            if (value['hover']) {
                css += '#header.sticky-header .main-menu > li.menu-item:hover > a, #header.sticky-header .main-menu > li.menu-item.active:hover > a, #header.sticky-header .main-menu > li.menu-custom-content:hover a { color: ' + value['hover'] + ' }';
            }
            if (value['active']) {
                css += '#header.sticky-header .main-menu > li.menu-item.active > a, #header.sticky-header .main-menu > li.menu-custom-content.active a { color: ' + value['active'] + ' }';
            }
            appendStyle('porto_settings[mainmenu-toplevel-link-color-sticky]', css);
        });
    });
    wp.customize('porto_settings[mainmenu-toplevel-alink-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[mainmenu-toplevel-alink-color]', '#header .main-menu > li.menu-item.active > a { color: ' + value + '}');
        });
    });
    wp.customize('porto_settings[mainmenu-toplevel-abg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[mainmenu-toplevel-abg-color]', '#header .main-menu > li.menu-item.active > a { background-color: ' + value + '}');
        });
    });
    wp.customize('porto_settings[menu-popup-text-transform]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[menu-popup-text-transform]', '.popup .sub-menu { text-transform: ' + value + ' }');
        });
    });
    wp.customize('porto_settings[mainmenu-popup-heading-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[mainmenu-popup-heading-color]', '#header .main-menu .wide li.sub > a, .side-nav-wrap .sidebar-menu .wide li.sub > a { color: ' + value + ' }');
        });
    });
    wp.customize('porto_settings[mainmenu-tip-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[mainmenu-tip-bg-color]', '.mega-menu .tip, .sidebar-menu .tip, .accordion-menu .tip, .menu-custom-block .tip { background: ' + value + ' }');
        });
    });
    wp.customize('porto_settings[menu-custom-text-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[menu-custom-text-color]', '#header .menu-custom-block, #header .menu-custom-block span { color: ' + value + ' }');
        });
    });
    wp.customize('porto_settings[menu-custom-link]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value['regular']) {
                css += '#header .menu-custom-block a { color: ' + value['regular'] + ' }';
            }
            if (value['hover']) {
                css += '#header .menu-custom-block a:hover { color: ' + value['hover'] + ' }';
            }
            appendStyle('porto_settings[menu-custom-link]', css)
        });
    });
    wp.customize('porto_settings[breadcrumbs-top-border]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[breadcrumbs-top-border]', '.page-top { border-top: ' + value['border-top'] + ' solid ' + value['border-color'] + '; }');
        });
    });
    wp.customize('porto_settings[breadcrumbs-bottom-border]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[breadcrumbs-bottom-border]', '.page-top { border-bottom: ' + value['border-top'] + ' solid ' + value['border-color'] + '; }');
        });
    });
    wp.customize('porto_settings[breadcrumbs-padding]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value['padding-top']) css+= 'padding-top: ' + value['padding-top'] + 'px;';
            if (value['padding-bottom']) css+= 'padding-bottom: ' + value['padding-bottom'] + 'px;';
            if (value['padding-left']) css+= 'padding-' + ( isRTL() ? 'right' : 'left' ) + ': ' + value['padding-left'] + 'px;';
            if (value['padding-right']) css+= 'padding-' + ( isRTL() ? 'left' : 'right' ) + ': ' + value['padding-right'] + 'px;';
            if (css) {
                css = '.page-top > .container {' + css + '}';
            }
            css += '.page-top .sort-source { ';
            if (value['padding-left']) {
                css += 'padding-left: ' + value['padding-left'] + 'px;';
                css += 'margin-left: -' + value['padding-left'] + 'px;';
            }
            if (value['padding-right']) {
                css += 'padding-right: ' + value['padding-right'] + 'px;';
                css += 'margin-right: -' + value['padding-right'] + 'px;';
            }
            css += '}';
            appendStyle('porto_settings[breadcrumbs-padding]', css);
        });
    });
    wp.customize('porto_settings[breadcrumbs-text-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '.page-top ul.breadcrumb > li, .page-top ul.breadcrumb > li .delimiter, .page-top .yoast-breadcrumbs, .page-top .breadcrumbs-wrap { color: ' + value + '; }';
            }
            appendStyle('porto_settings[breadcrumbs-text-color]', css);
        });
    });
    wp.customize('porto_settings[breadcrumbs-link-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '.page-top ul.breadcrumb > li a, .page-top .yoast-breadcrumbs a, .page-top .breadcrumbs-wrap a, .page-top .product-nav .product-link { color: ' + value + '; }';
            }
            appendStyle('porto_settings[breadcrumbs-link-color]', css);
        });
    });
    wp.customize('porto_settings[breadcrumbs-title-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '.page-top .page-title, .page-top .sort-source > li > a  { color: ' + value + ' }';
            }
            appendStyle('porto_settings[breadcrumbs-title-color]', css);
        });
    });
    wp.customize('porto_settings[breadcrumbs-subtitle-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '.page-top .page-sub-title { color: ' + value + ' }';
            }
            appendStyle('porto_settings[breadcrumbs-subtitle-color]', css);
        });
    });
    wp.customize('porto_settings[breadcrumbs-subtitle-margin]', function(e) {
        e.bind(function(margin) {
            if ($('body').hasClass('rtl')) {
                var temp = margin['margin-left'];
                margin['margin-left'] = margin['margin-right'];
                margin['margin-right'] = temp;
            }
            $.each(margin, function(key, value) {
                if (value == '') {
                    margin[key] = '0';
                }
            });
            var css = '.page-top .page-sub-title { margin: ' + margin['margin-top']+'px ' + margin['margin-right']+'px ' + margin['margin-bottom']+'px ' + margin['margin-left']+'px;' + '}';
            appendStyle('porto_settings[breadcrumbs-subtitle-margin]', css);
        });
    });
    wp.customize('porto_settings[footer-heading-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '#footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer .widget-title, #footer h1 a, #footer h2 a, #footer h3 a, #footer h4 a, #footer h5 a, #footer h6 a, #footer .widget-title a {color:' + value + '}';
            }
            appendStyle('porto_settings[footer-heading-color]', css);
        });
    });
    wp.customize('porto_settings[footer-label-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '#footer .widget.contact-info .contact-details strong{color:' + value + '}';
            }
            appendStyle('porto_settings[footer-label-color]', css);
        });
    });
    wp.customize('porto_settings[footer-text-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '#footer, #footer p, #footer .widget > div > ul li, #footer .widget > ul li{color:' + value + '}';
                css += '#footer .widget .tagcloud a, #footer .widget > div > ul, #footer .widget > ul, #footer .widget > div > ul li, #footer .widget > ul li, #footer .post-item-small{border-color:rgba(' + hexToRGB(value) + ',0.3)}';
            }
            appendStyle('porto_settings[footer-text-color]', css);
        });
    });
    wp.customize('porto_settings[footer-link-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value && value['regular']) {
                css += '#footer a, #footer .tooltip-icon{color:' + value['regular'] + '}';
                css += '#footer .tooltip-icon{border-color:' + value['regular'] + '}';
            }
            if (value && value['hover']) {
                css += '#footer a:hover{color:' + value['hover'] + '}';
            }
            appendStyle('porto_settings[footer-link-color]', css);
        });
    });
    wp.customize('porto_settings[footer-ribbon-text-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '#footer .footer-ribbon, #footer .footer-ribbon a, #footer .footer-ribbon a:hover, #footer .footer-ribbon a:focus{color:' + value + '}';
            }
            appendStyle('porto_settings[footer-ribbon-text-color]', css);
        });
    });
    wp.customize('porto_settings[footer-top-padding]', function(e) {
        e.bind(function(padding) {
            var css = '';
            if (padding['padding-top'] || padding['padding-bottom']) {
                css += '.footer-top {';
                if (padding['padding-top']) css += 'padding-top: ' + padding['padding-top'] + 'px;';
                if (padding['padding-bottom']) css += 'padding-bottom: ' + padding['padding-bottom'] + 'px;';
                css += '}';
            }
            appendStyle('porto_settings[footer-top-padding]', css);
        });
    });
    wp.customize('porto_settings[footer-bottom-text-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                css += '#footer .footer-bottom, #footer .footer-bottom p, #footer .footer-bottom .widget > div > ul li, #footer .footer-bottom .widget > ul li{color:' + value + '}';
            }
            appendStyle('porto_settings[footer-bottom-text-color]', css);
        });
    });
    wp.customize('porto_settings[footer-bottom-link-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value && value['regular']) {
                css += '#footer .footer-bottom a{color:' + value['regular'] + '}';
            }
            if (value && value['hover']) {
                css += '#footer .footer-bottom a:hover{color:' + value['hover'] + '}';
            }
            appendStyle('porto_settings[footer-bottom-link-color]', css);
        });
    });
    wp.customize('porto_settings[footer-social-bg-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value && 'transparent' != value) {
                css += '#footer .widget.follow-us .share-links a:not(:hover), .footer-top .widget.follow-us .share-links a:not(:hover) {background:' + value + '}';
            } else if (value && 'transparent' == value) {
                css += '#footer .widget.follow-us .share-links a, .footer-top .widget.follow-us .share-links a {background:' + value + '}';
            }
            appendStyle('porto_settings[footer-social-bg-color]', css);
        });
    });
    wp.customize('porto_settings[footer-social-link-color]', function(e) {
        e.bind(function(value) {
            var css = '',
                bgColor = wp.customize.instance('porto_settings[footer-social-bg-color]').get();
            if (value) {
                if (bgColor && 'transparent' != bgColor) {
                    css += '#footer .widget.follow-us .share-links a:not(:hover),.footer-top .widget.follow-us .share-links a:not(:hover){color:' + value + '}';
                } else if (bgColor && 'transparent' == bgColor) {
                    css += '#footer .widget.follow-us .share-links a, .footer-top .widget.follow-us .share-links a{color:' + value + '}';
                }
            }
            appendStyle('porto_settings[footer-social-link-color]', css);
        });
    });
    wp.customize('porto_settings[footer-copyright]', function(e) {
        e.bind(function(value) {
            $('#footer .footer-copyright').text(value);
        });
    });
    wp.customize('porto_settings[mobile-menu-toggle-text-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[mobile-menu-toggle-text-color]', '#header .mobile-toggle {color:' + value + '}');
        });
    });
    wp.customize('porto_settings[mobile-menu-toggle-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[mobile-menu-toggle-bg-color]', '#header .mobile-toggle {background-color:' + (value ? value : wp.customize.instance('porto_settings[skin-color]').get()) + '}');
        });
    });
    wp.customize('porto_settings[panel-bg-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                var panelType = wp.customize.instance('porto_settings[mobile-panel-type]').get();
                if ('side' == panelType) {
                    css += '#side-nav-panel';
                } else {
                    css += '#nav-panel .mobile-nav-wrap';
                }
                css += '{background-color:' + value + '}';
                if ('side' == panelType) {
                    css += '#side-nav-panel .accordion-menu li.menu-item.active > a,#side-nav-panel .menu-custom-block a:hover';
                } else {
                    css += '#nav-panel .menu-custom-block a:hover';
                }
                css += '{background-color:rgba(' + hexToRGB(value) + ',0.95)}';
            }
            appendStyle('porto_settings[panel-bg-color]', css);
        });
    });
    wp.customize('porto_settings[panel-text-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                if ('side' == wp.customize.instance('porto_settings[mobile-panel-type]').get()) {
                    css += '#side-nav-panel, #side-nav-panel .welcome-msg, #side-nav-panel .accordion-menu, #side-nav-panel .menu-custom-block, #side-nav-panel .menu-custom-block span';
                } else {
                    css += '#nav-panel, #nav-panel .welcome-msg, #nav-panel .accordion-menu, #nav-panel .menu-custom-block, #nav-panel .menu-custom-block span';
                }
                css += '{color:' + value + '}';
            }
            appendStyle('porto_settings[panel-text-color]', css);
        });
    });
    wp.customize('porto_settings[panel-border-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value) {
                if ('side' == wp.customize.instance('porto_settings[mobile-panel-type]').get()) {
                    css += '#side-nav-panel .accordion-menu li';
                } else {
                    css += '#nav-panel .accordion-menu li';
                }
                css += '{border-bottom-color:' + value + '}';
            }
            appendStyle('porto_settings[panel-border-color]', css);
        });
    });
    wp.customize('porto_settings[panel-link-hbgcolor]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value && !wp.customize.instance('porto_settings[mobile-panel-type]').get()) {
                css += '#nav-panel .accordion-menu .sub-menu li:not(.active):hover > a{background:' + value + '}';
            }
            appendStyle('porto_settings[panel-link-hbgcolor]', css);
        });
    });
    wp.customize('porto_settings[panel-link-color]', function(e) {
        e.bind(function(value) {
            var panelType = wp.customize.instance('porto_settings[mobile-panel-type]').get(),
                css = '';
            if (!value['regular']) {
                value['regular'] = ('side' == panelType ? '#fff' : '#333');
            }
            if ('side' == panelType) {
                css += '#side-nav-panel .accordion-menu li.menu-item > a, #side-nav-panel .menu-custom-block a{color:' + value['regular'] + '}';
            } else {
                css += '#nav-panel .accordion-menu li.menu-item > a, #nav-panel .accordion-menu .arrow, #nav-panel .menu-custom-block a{color:' + value['regular'] + '}';
                var skinColor = wp.customize.instance('porto_settings[skin-color]').get();
                if (skinColor) {
                    css += '#nav-panel .accordion-menu > li.menu-item > a, #nav-panel .accordion-menu > li.menu-item > .arrow{color:' + skinColor + '}';
                }
            }
            if (value['hover']) {
                if ('side' == panelType) {
                    css += '#side-nav-panel .accordion-menu li.menu-item.active > a, #side-nav-panel .menu-custom-block a:hover';
                } else {
                    css += '#nav-panel .accordion-menu li.menu-item:hover > a, #nav-panel .accordion-menu .arrow:hover, #nav-panel .menu-custom-block a:hover';
                }
                css += '{color:' + value['hover'] + '}';
            }
            appendStyle('porto_settings[panel-link-color]', css);
        });
    });
    wp.customize('porto_settings[switcher-bg-color]', function(e) {
        e.bind(function(value) {
            var css = ( value ? '#header .porto-view-switcher > li.menu-item > a {background-color:' + value + '}' : '' );
            appendStyle('porto_settings[switcher-bg-color]', css);
        });
    });
    wp.customize('porto_settings[switcher-top-level-hover]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value == '1') {
                css += '#header .porto-view-switcher > li.menu-item:hover > a, #header .porto-view-switcher > li.menu-item > a.active {color:' + wp.customize.instance('porto_settings[switcher-link-color]').get()['hover'] + ';background:' + wp.customize.instance('porto_settings[switcher-hbg-color]').get() + '}';
            }
            appendStyle('porto_settings[switcher-top-level-hover]', css);
        });
    });
    wp.customize('porto_settings[switcher-link-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value && value['regular']) {
                css += '#header .porto-view-switcher > li.menu-item:before, #header .porto-view-switcher > li.menu-item > a { color: ' + value['regular'] + ' }';
            }
            if (value && value['hover']) {
                if (wp.customize.instance('porto_settings[switcher-top-level-hover]').get()) {
                    css += '#header .porto-view-switcher > li.menu-item:hover > a, #header .porto-view-switcher > li.menu-item > a.active {color:' + value + '}';
                }
                css += '#header .porto-view-switcher .narrow li.menu-item > a, #header .porto-view-switcher .narrow li.menu-item > a.active, #header .porto-view-switcher .narrow li.menu-item:hover > a { color: ' + value['hover'] + ' }';
            }
            appendStyle('porto_settings[switcher-link-color]', css);
        });
    });
    wp.customize('porto_settings[searchform-border-color]', function(e) {
        e.bind(function(value) {
            var css = '', opacity = wp.customize.instance('porto_settings[searchform-opacity]').get().replace('%', '');
            if (opacity == '') {
                opacity = 0.8;
            } else {
                opacity = parseFloat(opacity) / 100;
            }
            if (value && 'transparent' != value) {
                css += '.fixed-header #header .searchform{border-color: rgba(' + hexToRGB(value) + ',' + opacity + ')}';
            }
            if (value) {
                css += '#header .searchform, .fixed-header #header.sticky-header .searchform, #header .searchform input, #header .searchform select, #header .searchform .selectric, #header .searchform .selectric-hover .selectric, #header .searchform .selectric-open .selectric, #header .searchform .autocomplete-suggestions, #header .searchform .selectric-items';
                if ('simple' == wp.customize.instance('porto_settings[search-layout]').get()) {
                    css += ',#header .searchform .searchform-fields';
                }
                css += '{border-color:' + value + '}';
            }
            appendStyle('porto_settings[searchform-border-color]', css);
        });
    });
    wp.customize('porto_settings[searchform-popup-border-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value && 'simple' != wp.customize.instance('porto_settings[search-layout]').get()) {
                css += '#header .searchform-popup .search-toggle:after { border-bottom-color: ' + value + ' }';
                css += '#header .search-popup .searchform { border-color: ' + value + ' }';
                css += '@media (max-width: 991px) {';
                    css += '#header .searchform { border-color: ' + value + ' }';
                css += '}';
            }
            appendStyle('porto_settings[searchform-popup-border-color]', css);
        });
    });
    wp.customize('porto_settings[searchform-hover-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[searchform-hover-color]', '#header .searchform button { color: ' + value + '}' );
        });
    });
    wp.customize('porto_settings[sticky-searchform-popup-border-color]', function(e) {
        e.bind(function(value) {
            var css = '';
            if (value && 'simple' != wp.customize.instance('porto_settings[search-layout]').get()) {
                css = '#header.sticky-header .searchform-popup .searchform { border-color: ' + value + '} #header.sticky-header .searchform-popup .search-toggle:after { border-bottom-color: ' + value + '}';
            }
            appendStyle('porto_settings[sticky-searchform-popup-border-color]', css );
        });
    });
    wp.customize('porto_settings[sticky-searchform-toggle-text-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[sticky-searchform-toggle-text-color]', ( value ? '#header.sticky-header .searchform-popup .search-toggle { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[sticky-searchform-toggle-hover-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[sticky-searchform-toggle-hover-color]', ( value ? '#header.sticky-header .searchform-popup .search-toggle:hover { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[minicart-icon-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[minicart-icon-color]', ( value ? '#mini-cart .cart-subtotal, #mini-cart .minicart-icon { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[minicart-item-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[minicart-item-color]', ( value ? '#mini-cart .cart-items, #mini-cart .cart-items-text { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[minicart-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[minicart-bg-color]', ( value ? '#mini-cart { background: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[minicart-popup-border-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[minicart-popup-border-color]', ( value ? '#mini-cart .cart-popup { border: 1px solid ' + value + '} #mini-cart .cart-popup:after{ border-bottom-color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[sticky-minicart-icon-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[sticky-minicart-icon-color]', ( value ? '.sticky-header #mini-cart .cart-subtotal, .sticky-header #mini-cart .minicart-icon { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[sticky-minicart-item-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[sticky-minicart-item-color]', ( value ? '.sticky-header #mini-cart .cart-items, .sticky-header #mini-cart .cart-items-text { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[sticky-minicart-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[sticky-minicart-bg-color]', ( value ? '.sticky-header #mini-cart { background: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[sticky-minicart-popup-border-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[sticky-minicart-popup-border-color]', ( value ? '.sticky-header #mini-cart .cart-popup { border: 1px solid ' + value + '} .sticky-header #mini-cart .cart-popup:after{ border-bottom-color: ' + value + '}' : '' ) );
        });
    });

    wp.customize('porto_settings[shop-add-links-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[shop-add-links-color]', ( value ? '.add-links .add_to_cart_button, .add-links .add_to_cart_read_more, .add-links .quickview, .yith-wcwl-add-to-wishlist a, .yith-wcwl-add-to-wishlist a:hover, .yith-wcwl-add-to-wishlist span { color: ' + value + ' }' : '' ) );
        });
    });
    wp.customize('porto_settings[shop-add-links-bg-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[shop-add-links-bg-color]', ( value ? '.add-links .add_to_cart_button, .add-links .add_to_cart_read_more, .add-links .quickview, .yith-wcwl-add-to-wishlist a, .yith-wcwl-add-to-wishlist a:hover, .yith-wcwl-add-to-wishlist span { background-color: ' + value + ' }' : '' ) );
        });
    });
    wp.customize('porto_settings[shop-add-links-border-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[shop-add-links-border-color]', ( value ? '.add-links .add_to_cart_button, .add-links .add_to_cart_read_more, .add-links .quickview, .yith-wcwl-add-to-wishlist a, .yith-wcwl-add-to-wishlist a:hover, .yith-wcwl-add-to-wishlist span { border-color: ' + value + ' }' : '' ) );
        });
    });
    wp.customize('porto_settings[wishlist-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[wishlist-color]', ( value ? '.product-summary-wrap .yith-wcwl-add-to-wishlist a:before, .product-summary-wrap .yith-wcwl-add-to-wishlist span:before, .product-summary-wrap .yith-wcwl-add-to-wishlist a:hover, .product-summary-wrap .yith-wcwl-add-to-wishlist span:hover, .product-summary-wrap .yith-wcwl-add-to-wishlist a:focus, .product-summary-wrap .yith-wcwl-add-to-wishlist span:focus { color: ' + value + '} .product-summary-wrap .yith-wcwl-add-to-wishlist a:before, .product-summary-wrap .yith-wcwl-add-to-wishlist span:before { border-color: ' + value + ' }' : '' ) );
        });
    });
    wp.customize('porto_settings[wishlist-color-inverse]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[wishlist-color-inverse]', ( value ? '.product-summary-wrap .yith-wcwl-add-to-wishlist a:hover:before, .product-summary-wrap .yith-wcwl-add-to-wishlist span:hover:before, .product-summary-wrap .yith-wcwl-add-to-wishlist a:focus:before, .product-summary-wrap .yith-wcwl-add-to-wishlist span:focus:before { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[hot-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[hot-color]', ( value ? 'article.post .post-date .sticky, .post-item .post-date .sticky, .product-image .labels .onhot, .summary-before .labels .onhot { background: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[hot-color-inverse]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[hot-color-inverse]', ( value ? 'article.post .post-date .sticky, .post-item .post-date .sticky, .product-image .labels .onhot, .summary-before .labels .onhot { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[sale-color]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[sale-color]', ( value ? '.product-image .labels .onsale, .summary-before .labels .onsale { background: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[sale-color-inverse]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[sale-color-inverse]', ( value ? '.product-image .labels .onsale, .summary-before .labels .onsale { color: ' + value + '}' : '' ) );
        });
    });
    wp.customize('porto_settings[css-code]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[css-code]', value);
        });
    });
    wp.customize('porto_settings[sticky-header-effect]', function(e) {
        e.bind(function(value) {
            if ('reveal' == value) {
                $('.header-wrapper').addClass('header-reveal');
            } else {
                $('.header-wrapper').removeClass('header-reveal');
            }
        });
    });
    wp.customize('porto_settings[show-sticky-logo]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[show-sticky-logo]', ( value == '1' ? '' : '#header.sticky-header .logo { display: none !important; }' ));
        });
    });
    wp.customize('porto_settings[show-sticky-searchform]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[show-sticky-searchform]', ( value == '1' ? '' : '#header.sticky-header .searchform-popup { display: none !important; }' ));
        });
    });
    wp.customize('porto_settings[show-sticky-minicart]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[show-sticky-minicart]', ( value == '1' ? '' : '#header.sticky-header #mini-cart { display: none !important; }' ));
        });
    });
    wp.customize('porto_settings[show-sticky-menu-custom-content]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[show-sticky-menu-custom-content]', ( value == '1' ? '' : '#header.sticky-header .menu-custom-content { display: none !important; }' ));
        })
    });
    wp.customize('porto_settings[mobile-panel-pos]', function(e) {
        e.bind(function(value) {
            if (value) {
                $('#side-nav-panel').attr('class', '').addClass(value);
            } else {
                $('#side-nav-panel').attr('class', '');
            }
            var mobile_panel_pos = value ? ( isRTL() ? ( 'panel-left' == value ? 'right' : 'left' ) : value.replace('panel-', '') ) : ( isRTL() ? 'right' : 'left' ),
                css = 'html.panel-opened { margin-' + mobile_panel_pos + ': 260px !important; margin-' + ( 'left' == mobile_panel_pos ? 'right' : 'left' ) + ': -260px !important; }';
            appendStyle('porto_settings[mobile-panel-pos]', css);
        });
    });

    wp.customize('porto_settings[menu-arrow]', function(e) {
        e.bind(function(value) {
            if ('1' == value) {
                $('.top-links.mega-menu, .top-links.accordion-menu, .main-menu.mega-menu').addClass('show-arrow');
            } else {
                $('.top-links.mega-menu, .top-links.accordion-menu, .main-menu.mega-menu').removeClass('show-arrow');
            }
        });
    });
    wp.customize('porto_settings[menu-type]', function(e) {
        e.bind(function(value) {
            $('.main-menu.mega-menu, .main-menu-wrap').removeClass('menu-flat').removeClass('menu-flat-border').removeClass('side').removeClass('menu-hover-line').removeClass('menu-hover-underline');
            if (value) {
                $('.main-menu.mega-menu, .main-menu-wrap').addClass(value);
            }
        });
    });
    wp.customize('porto_settings[menu-align]', function(e) {
        e.bind(function(value) {
            if ($('.main-menu-wrap > #main-menu').length) {
                $('.main-menu-wrap > #main-menu').removeClass('centered');
                value && $('.main-menu-wrap > #main-menu').addClass(value);
            }
        });
    });
    wp.customize('porto_settings[menu-sidebar-title]', function(e) {
        e.bind(function(value) {
            if ($('#main-sidebar-menu .widget-title').length){
                $('#main-sidebar-menu .widget-title').contents().filter(function() {
                    return this.nodeType !== 1;
                }).eq(0).replaceWith(value);
            }
        });
    });
    wp.customize('porto_settings[menu-sidebar-toggle]', function(e) {
        e.bind(function(value) {
            if ('1' == value) {
                if ($('#main-sidebar-menu .widget-title .toggle').length) {
                    $('#main-sidebar-menu .widget-title .toggle').show();
                } else {
                    $('#main-sidebar-menu .widget-title').append('<div class="toggle"></div>');
                    if (typeof theme.SidebarMenu !== 'undefined') {
                        theme.SidebarMenu.initialize($('#main-sidebar-menu .sidebar-menu'), $('#main-sidebar-menu .widget-title .toggle'), $('#main-toggle-menu .menu-title'));
                    }
                }
            } else {
                $('#main-sidebar-menu .widget-title .toggle').hide();
            }
        });
    });
    wp.customize('porto_settings[menu-title]', function(e) {
        e.bind(function(value) {
            if ($('#main-sidebar-menu .widget-title').length){
                $('#main-sidebar-menu .widget-title').contents().filter(function() {
                    return this.nodeType !== 1;
                }).eq(0).replaceWith(value);
            }
        });
    });
    wp.customize('porto_settings[blog-content_top]', function(e) {
        e.bind(function(value) {
            if (!$('#content-top').length) {
                $('div#main').prepend('<div id="content-top"></div>');
            }
        });
    });
    wp.customize('porto_settings[blog-content_inner_top]', function(e) {
        e.bind(function(value) {
            if (!$('#content-inner-top').length) {
                $('.main-content-wrap > .main-content').prepend('<div id="content-inner-top"></div>');
            }
        });
    });
    wp.customize('porto_settings[blog-content_inner_bottom]', function(e) {
        e.bind(function(value) {
            if (!$('#content-inner-bottom').length) {
                $('.main-content-wrap > .main-content').append('<div id="content-inner-bottom"></div>');
            }
        });
    });
    wp.customize('porto_settings[blog-content_bottom]', function(e) {
        e.bind(function(value) {
            if (!$('#content-bottom').length) {
                $('div#main').append('<div id="content-bottom"></div>');
            }
        });
    });
    wp.customize('porto_settings[portfolio-title]', function(e) {
        e.bind(function(value) {
            $('.post-type-archive-portfolio #content > .portfolio-archive-title').html(value);
        });
    });

    // woocommerce options
    wp.customize('porto_settings[woo-account-login-style]', function(e) {
        e.bind(function(value) {
            if ('link' == value) {
                $('body').removeClass('login-popup');
            } else {
                $('body').addClass('login-popup');
            }
        });
    });
    wp.customize('porto_settings[woo-show-product-border]', function(e) {
        e.bind(function(value) {
            appendStyle('porto_settings[woo-show-product-border]', ('1' == value ? '.product-image { border: 1px solid #ddd; width: 99.9999%; }' : ''));
        });
    });
    wp.customize('porto_settings[category-view-mode]', function(e) {
        e.bind(function(value) {
            if ($('.gridlist-toggle').length) {
                if ('list' == value) {
                    $('.gridlist-toggle #list:not(.active)').click();
                } else {
                    $('.gridlist-toggle #grid:not(.active)').click();
                }
            }
        });
    });
    wp.customize('porto_settings[category-hover]', function(e) {
        e.bind(function(value) {
            if ('1' == value) {
                $('ul.products li.product-col').removeClass('hover');
            } else {
                $('ul.products li.product-col').addClass('hover');
            }
        });
    });
    wp.customize('porto_settings[product-quickview-label]', function(e) {
        e.bind(function(value) {
            $('ul.products li.product-col .add-links .quickview').text(value).attr('title', value);
        });
    });
    wp.customize('porto_settings[product-related]', function(e) {
        e.bind(function(value) {
            if ('0' == value) {
                $('.single-product .related.products').remove();
            } else if (!$('.single-product .related.products').length) {
                if ('left_sidebar' == window.parent.wp.customize.instance('porto_settings[product-single-content-layout]').get()) {
                    $('.single-product .site-main > .product').append('<div class="related products d-none"></div>');
                } else {
                    $('.single-product div#main').append('<div class="related products d-none"></div>');
                }
            }
        });
    });
    wp.customize('porto_settings[product-upsells]', function(e) {
        e.bind(function(value) {
            if ('0' == value) {
                $('.single-product .upsells.products').remove();
            } else if (!$('.single-product .upsells.products').length) {
                $('.single-product .site-main > .product').append('<div class="upsells products d-none"></div>');
            }
        });
    });
    wp.customize('porto_settings[product-hot-label]', function(e) {
        e.bind(function(value) {
            $('ul.products li.product-col .labels .onhot, .single-product .labels .onhot').text(value);
        });
    });
    wp.customize('porto_settings[product-thumbs-count]', function(e) {
        e.bind(function(value) {
            theme.product_thumbs_count = (value ? parseInt(value) : 4);
        });
    });
    wp.customize('porto_settings[product-zoom]', function(e) {
        e.bind(function(value) {
            var product_type = window.parent.wp.customize.instance('porto_settings[product-single-content-layout]').get();
            if ('extended' == product_type || 'full_width' == product_type) {
                value = '0';
            }
            theme.product_zoom = ('1' == value ? true : false);
        });
    });
    wp.customize('porto_settings[product-zoom-mobile]', function(e) {
        e.bind(function(value) {
            theme.product_zoom_mobile = ('1' == value ? true : false);
        });
    });
    wp.customize('porto_settings[product-image-popup]', function(e) {
        e.bind(function(value) {
            theme.product_image_popup = ('1' == value ? 'fadeOut' : false);
        });
    });
    wp.customize('porto_settings[zoom-type]', function(e) {
        e.bind(function(value) {
            js_porto_vars.zoom_type = value;
        });
    });
    wp.customize('porto_settings[zoom-scroll]', function(e) {
        e.bind(function(value) {
            js_porto_vars.zoom_scroll = value;
        });
    });
    wp.customize('porto_settings[zoom-lens-size]', function(e) {
        e.bind(function(value) {
            js_porto_vars.zoom_lens_size = value;
        });
    });
    wp.customize('porto_settings[zoom-lens-shape]', function(e) {
        e.bind(function(value) {
            js_porto_vars.zoom_lens_shape = value;
        });
    });
    wp.customize('porto_settings[zoom-contain-lens]', function(e) {
        e.bind(function(value) {
            js_porto_vars.zoom_contain_lens = value;
        });
    });
    wp.customize('porto_settings[zoom-lens-border]', function(e) {
        e.bind(function(value) {
            js_porto_vars.zoom_lens_border = value;
        });
    });
    wp.customize('porto_settings[zoom-border]', function(e) {
        e.bind(function(value) {
            js_porto_vars.zoom_border = ('inner' == js_porto_vars.zoom_type ? 0 : value);
        });
    });
    wp.customize('porto_settings[zoom-border-color]', function(e) {
        e.bind(function(value) {
            js_porto_vars.zoom_border_color = value;
        });
    });
    wp.customize('porto_settings[product-crosssell]', function(e) {
        e.bind(function(value) {
            if ('0' == value) {
                $('.woocommerce-cart .cross-sells').remove();
            } else if (!$('.woocommerce-cart .cross-sells').length) {
                if ('v2' == window.parent.wp.customize.instance('porto_settings[cart-version]').get()) {
                    $('.woocommerce-cart .page-content > .woocommerce').append('<div class="cross-sells d-none"></div>');
                } else {
                    $('.woocommerce-cart .page-content > .woocommerce > .cart-collaterals').prepend('<div class="cross-sells d-none"></div>');
                }
            }
        });
    });

});