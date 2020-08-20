/*jslint browser: true, white: true */
/*global console,jQuery,megamenu,window,navigator*/

/**
 * EDD Ajax Cart
 */
(function($) {
    "use strict";

    $(function() {
        $('body').on('edd_cart_item_added', function(event, data) {
            $('.mega-menu-edd-cart-total').html(data.total);
            $('.mega-menu-edd-cart-count').html(data.cart_quantity);
        });
    });

})(jQuery);

/**
 * Searchbox jQuery plugin
 */
(function($) {
    "use strict";

    $.maxmegamenu_searchbox = function(form, options) {

        var plugin = this;
        var form = $(form);
        var $menu = form.parents('.max-mega-menu');
        var $wrap = $menu.parent();
        var breakpoint = $menu.attr('data-breakpoint');
        var input = $('input[type=text]', form);
        var icon = $('.search-icon', form);

        plugin.isDesktopView = function() {
            return Math.max(window.outerWidth, $(window).width()) >= breakpoint; // account for scrollbars
        };

        plugin.monitorView = function() {
            if (typeof $menu.data("view") === 'undefined') {
                if (plugin.isDesktopView()) {
                    $menu.data("view", "desktop");
                } else {
                    $menu.data("view", "mobile");
                }
            }

            plugin.checkWidth();

            $(window).resize(function() {
                plugin.checkWidth();
            });
        };

        plugin.checkWidth = function() {
            var expanding_search = $("li.mega-menu-item .mega-search.expand-to-left input[type=text], li.mega-menu-item .mega-search.expand-to-right input[type=text]", $menu);

            if ( $menu.data("view") === "mobile" ) {
                var placeholder = expanding_search.attr('data-placeholder');
                expanding_search.attr('placeholder', placeholder);
            }

            if ( $menu.data("view") === "desktop" ) {
                expanding_search.attr('placeholder', '');
            }
        };

        plugin.close_search = function() {
            input.val("");
            input.attr('placeholder', '');
            form.removeClass('mega-search-open');
            form.addClass('mega-search-closed');
        }

        plugin.open_search = function() {
            input.attr('placeholder', input.attr('data-placeholder'));
            form.removeClass('mega-search-closed');
            form.addClass('mega-search-open');
        }

        plugin.detect_background_click = function() {
            var dragging = false;

            $(document).on({
                "touchmove": function(e) { dragging = true; },
                "touchstart": function(e) { dragging = false; }
            });

            $(document).on("click touchend", function(e) { // hide menu when clicked away from
                if ( form.parent().hasClass('mega-static') ) {
                    return;
                }
                if ( ! dragging && ! $(e.target).closest(".max-mega-menu li").length && ! $(e.target).closest(".mega-menu-toggle").length ) {
                    plugin.close_search();
                }
                dragging = false;
            });
        }

        plugin.init_replacements_search = function() {

            input.val("");

            if ( $menu.data("view") === "mobile" ) {
                $(".mega-search.expand-to-left .search-icon, .mega-search.expand-to-right .search-icon", $menu).on('click', function(e) {
                    $(this).parents(".mega-search").submit();
                });
            }

            if ( $menu.data("view") === "desktop" ) {
                input.on('focus', function(e) {
                    if (! form.parent().hasClass('mega-static') && form.hasClass('mega-search-closed') && $wrap.hasClass('mega-keyboard-navigation') ) {
                        plugin.open_search();
                    }
                });

                input.on('blur', function(e) {
                    if (! form.parent().hasClass('mega-static') && form.hasClass('mega-search-open') && $wrap.hasClass('mega-keyboard-navigation') ) {
                        plugin.close_search();
                    }
                });

                icon.on('click', function(e) {
                    if (form.parent().hasClass('mega-static') ) {
                        if (input.val() != '') {
                            form.submit();
                        }
                        return;
                    }
                    
                    if (form.hasClass('mega-search-closed')) {
                        plugin.open_search();
                        input.focus();
                    } else if ( input.val() == '' ) {
                        plugin.close_search();
                    } else {
                        form.submit();
                    }
                });
            }
        };

        plugin.monitorView();
        plugin.init_replacements_search();
        plugin.detect_background_click();
    };

    $.fn.maxmegamenu_searchbox = function(options) {
        return this.each(function() {
            if (undefined === $(this).data('maxmegamenu_searchbox')) {
                var plugin = new $.maxmegamenu_searchbox(this, options);
                $(this).data('maxmegamenu_searchbox', plugin);
            }
        });
    };

    $(function() {
        $(".max-mega-menu .mega-search").maxmegamenu_searchbox();
    });
})(jQuery);


/**
 * Searchbox jQuery plugin
 */
(function($) {
    "use strict";

    $.maxmegamenu_toggle_searchbox = function(form, options) {

        var plugin = this;
        var form = $(form);
        var $wrap = form.parents('.mega-menu-wrap');
        var input = $("input[type=text]", form);
        var icon = $(".search-icon", form);
        
        plugin.open_search = function() {
            input.attr('placeholder', input.attr('data-placeholder'));
            form.removeClass('mega-search-closed');
            form.addClass('mega-search-open');
        }

        plugin.close_search = function() {
            input.attr('placeholder', '');
            form.removeClass('mega-search-open');
            form.addClass('mega-search-closed');
        }

        plugin.init_toggle_search = function() {

            input.val("");

            input.on('focus', function(e) {
                if (! form.parent().hasClass('mega-static') && form.hasClass('mega-search-closed') && $wrap.hasClass('mega-keyboard-navigation') ) {
                    plugin.open_search();
                }
            });

            input.on('blur', function(e) {
                if ( ! form.parent().hasClass('mega-static') && form.hasClass('mega-search-open') && $wrap.hasClass('mega-keyboard-navigation') ) {
                    plugin.close_search();
                }
            });

            icon.on('click', function(e) {
                if (form.hasClass('static') ) {
                    form.submit();
                } else if (form.hasClass('mega-search-closed')) {
                    input.focus();
                    plugin.open_search();
                } else if ( input.val() == '' ) {
                    plugin.close_search();
                } else {
                    form.submit();
                }
            });

        };

        plugin.init_toggle_search();

    };

    $.fn.maxmegamenu_toggle_searchbox = function(options) {
        return this.each(function() {
            if (undefined === $(this).data('maxmegamenu_toggle_searchbox')) {
                var plugin = new $.maxmegamenu_toggle_searchbox(this, options);
                $(this).data('maxmegamenu_toggle_searchbox', plugin);
            }
        });
    };

    $(function() {
        $(".mega-menu-toggle .mega-search").maxmegamenu_toggle_searchbox();
    });
})(jQuery);

/**
 * Sticky jQuery Plugin
 */
(function($) {

    "use strict";

    $.maxmegamenu_sticky = function(menu, options) {
        var plugin = this;
        var $menu = $(menu);
        var $wrap = $menu.parent();
        var breakpoint = $menu.attr('data-breakpoint');
        var sticky_on_mobile = $menu.attr('data-sticky-mobile');
        var sticky_on_desktop = $menu.attr('data-sticky-desktop');
        var sticky_expand = $menu.attr('data-sticky-expand');
        var sticky_expand_mobile = $menu.attr('data-sticky-expand-mobile');
        var sticky_offset = isNaN(parseInt($menu.attr('data-sticky-offset'))) ? 0 : parseInt($menu.attr('data-sticky-offset'));
        var sticky_hide_until_scroll_up = $menu.attr('data-sticky-hide');
        var sticky_hide_until_scroll_up_tolerance = isNaN(parseInt($menu.attr('data-sticky-hide-tolerance'))) ? 0 : parseInt($menu.attr('data-sticky-hide-tolerance'));
        var sticky_hide_until_scroll_up_offset = isNaN(parseInt($menu.attr('data-sticky-hide-offset'))) ? 0 : parseInt($menu.attr('data-sticky-hide-offset'));
        var sticky_transition = $menu.attr('data-sticky-transition');
        var sticky_menu_offset_top;
        var sticky_menu_offset_left;
        var sticky_menu_width;
        var sticky_menu_width_round_up;
        var sticky_menu_height;
        var is_stuck = false;
        var admin_bar_height = 0;
        var last_scroll_top = 0;
        var saved_scroll_top = 0;
        var is_vertical = $menu.hasClass('mega-menu-vertical') || $menu.hasClass('mega-menu-accordion');

        plugin.isDesktopView = function() {
            return Math.max(window.outerWidth, $(window).width()) >= breakpoint; // account for scrollbars
        };

        var sticky_hide_until_scroll_up_enabled = function() {
            return $menu.hasClass('mega-menu-horizontal') && sticky_hide_until_scroll_up == "true";
        }

        var sticky_enabled = function() {
            if ( plugin.isDesktopView() ) {
                return sticky_on_desktop === 'true';
            } else {
                return sticky_on_mobile === 'true';
            }

            return false;
        };

        plugin.calculate_menu_position = function() {
            sticky_menu_offset_top = $wrap.offset().top;

            if ($('body').hasClass('admin-bar') && $("#wpadminbar").is(":visible") && $("#wpadminbar").css('top') == '0px' && $("#wpadminbar").css('position') == 'fixed') {
                admin_bar_height = $('#wpadminbar').height();
                sticky_menu_offset_top = sticky_menu_offset_top - admin_bar_height;
            }

            if (sticky_offset < 0) {
                sticky_menu_offset_top = sticky_menu_offset_top + sticky_offset;
            } else {
                sticky_menu_offset_top = sticky_menu_offset_top - sticky_offset;
            }

            sticky_menu_offset_left = $menu.parent().offset().left;
            sticky_menu_width = window.getComputedStyle($wrap[0]).width;
            sticky_menu_width_round_up = Math.ceil(parseFloat(sticky_menu_width));
            sticky_menu_height = $wrap.height();
        };

        plugin.stick_menu = function() {
            is_stuck = true;

            var total_offset = parseInt(admin_bar_height, 10) + parseInt(sticky_offset, 10);

            if (sticky_offset < 0) {
                total_offset = parseInt(admin_bar_height, 10);
            }

            var placeholder = $("<div />").addClass("mega-sticky-wrapper").css({
                'height' : sticky_menu_height + 'px',
                'position' :'static'
            });

            $wrap.addClass('mega-sticky').wrap(placeholder).css({
                'margin-top' : total_offset + 'px'
            });

            $menu.css({
                'max-width' : sticky_menu_width_round_up + 'px'
            });

            if (sticky_menu_offset_left > 0) {
                $menu.css({
                    'margin-left' : sticky_menu_offset_left + 'px'
                });
            }

            if (is_vertical || sticky_expand === 'false') {
                $wrap.css({
                    'margin-left' : '0',
                    'margin-right' : '0',
                    'width' : sticky_menu_width_round_up + 'px',
                    'left' : sticky_menu_offset_left + 'px'
                });

                $menu.css({
                    'margin-left' : '0'
                });
            }

            if ( $(window).width() <= breakpoint ) {
                $wrap.css({
                    'width' : sticky_menu_width_round_up + 'px'
                });

                if (sticky_expand_mobile === 'true') {
                   $wrap.css({
                        'margin-left' : '',
                        'margin-right' : '',
                        'width' : '',
                        'left' : ''
                    });

                    $menu.css({
                        'max-width' : '',
                        'margin-left' : '',
                        'width' : '',
                        'left' : ''
                    });
                }
            }

            $wrap.delay(0).queue(function(next){
                $(this).addClass('mega-stuck');
                next();
            });
        };

        plugin.unstick_menu = function() {
            is_stuck = false;
            
            $wrap.removeClass('mega-sticky').removeClass('mega-hide').removeClass('mega-reveal').css({
                'margin' : '',
                'width' : '',
                'left': ''
            });

            $wrap.delay(0).queue(function(next){
                $(this).removeClass('mega-stuck');
                next();
            });

            $menu.css({
                'margin-left' : '',
                'max-width' : '',
                'left' : '',
                'width' : ''
            });

            if (sticky_transition == 'true') {
                var delay = 250;
            } else {
                var delay = 0;
            }

            $wrap.delay(delay).queue(function(next){
                $(this).unwrap();
                next();
            });
        };

        plugin.mega_sticky_on_scroll = function(){
            if ( ! sticky_enabled() ) {
                return;
            }

            var scroll_top = $(window).scrollTop();

            if (scroll_top > sticky_menu_offset_top) {
                if (!is_stuck) {
                    plugin.stick_menu();
                }
            } else {
                if (is_stuck) {
                    plugin.unstick_menu();
                }
            }
        };

        var mega_hide_on_scroll_up = function() {
            if (sticky_hide_until_scroll_up_enabled()) {

                if ( $menu.data("view") === "mobile" && $('.mega-menu-toggle', $wrap).hasClass('mega-menu-open') ) {
                    return;
                }

                var scroll_top = $(window).scrollTop();
                
                if ( scroll_top < sticky_hide_until_scroll_up_offset ) {
                    $wrap.removeClass('mega-hide');
                }
                
                saved_scroll_top = last_scroll_top;

                if (scroll_top < last_scroll_top) {
                    // scroll up
                    if (saved_scroll_top - scroll_top > sticky_hide_until_scroll_up_tolerance) {
                        $wrap.removeClass('mega-hide');
                    }
                } else {
                    // scroll down
                    if (scroll_top - saved_scroll_top > sticky_hide_until_scroll_up_tolerance) {
                        $wrap.addClass('mega-hide');
                    }
                }

                last_scroll_top = scroll_top;
            }
        }

        plugin.mega_sticky_on_resize = function() {
            if ($('input', $wrap).is(':focus')) {
                return;
            }

            if ( sticky_enabled() ) {
                if (is_stuck) {
                    plugin.unstick_menu();
                    plugin.calculate_menu_position();
                    plugin.stick_menu();
                } else {
                    plugin.calculate_menu_position();
                    plugin.mega_sticky_on_scroll();
                }
            } else {
                if (is_stuck) {
                    plugin.unstick_menu();
                }
            }
        };

        plugin.init = function() {
            plugin.calculate_menu_position();
            plugin.mega_sticky_on_scroll();

            $('.mega-menu-accordion li.mega-menu-item').on('open_panel', function() {
                plugin.calculate_menu_position();
            });

            var $window = $(window);

            $window.scroll(function() {
                 plugin.mega_sticky_on_scroll();
                 mega_hide_on_scroll_up();
            });

            var windowWidth = $window.width();

            $window.resize(function() {
                if ($window.width() != windowWidth) {
                    windowWidth = $window.width();
                    plugin.mega_sticky_on_resize();
                }
            });
        };

        plugin.init();
    };

    $.fn.maxmegamenu_sticky = function(options) {

        return this.each(function() {
            if (undefined === $(this).data('maxmegamenu_sticky')) {
                var plugin = new $.maxmegamenu_sticky(this, options);
                $(this).data('maxmegamenu_sticky', plugin);
            }
        });

    };

    $(window).on('load', function (e) {
        $(".mega-menu[data-sticky-enabled]").maxmegamenu_sticky();
    });

})(jQuery);

/**
 * Handle tabbed functionality
 */
(function($) {
    $(function() {

        var calculate_tabbed_sub_menu_widths = function( menu_item ) {
            var menu = menu_item.parents('.mega-menu');

            if( $(menu.attr('data-panel-inner-width')).length > 0 ) {
                if ( menu.data("view") === "desktop" ) {
                    $('> ul.mega-sub-menu', menu_item).each(function() {
                        var tab_content = $(this);
                        var parent_submenu_content_width = parseInt(tab_content.width());
                        var parent_submenu_left_padding = parseInt(tab_content.css('paddingLeft'));
                        var tabs_width = $(this).find('a.mega-menu-link').first().outerWidth();

                        $('> li.mega-menu-item > ul.mega-sub-menu', $(this)).each(function() {
                            $(this).css('width', parent_submenu_content_width - tabs_width + 'px');
                            $(this).css('left', parent_submenu_left_padding + tabs_width + 'px'); 
                        });
                    });
                } else {
                    $('> ul.mega-sub-menu > li.mega-menu-item > ul.mega-sub-menu', menu_item).each(function() {
                        $(this).css('width', '');
                        $(this).css('left', ''); 
                    });
                }
            }
        }

        var calculate_tabbed_sub_menu_heights = function( menu_item ) {
            var menu = menu_item.parents('.mega-menu');
            var max_height = 0;

            if ( menu.data("view") === "desktop" ) {

                $('> ul.mega-sub-menu', menu_item).css('minHeight', '');
                
                $('> ul.mega-sub-menu > li.mega-menu-item > ul.mega-sub-menu', menu_item).each(function() {
                    var tab_content = $(this);
                    var this_height = parseInt(tab_content.css('height'));

                    if (this_height > max_height) {
                        max_height = this_height;
                    }
                });

                var border_top_width = parseInt($('> ul.mega-sub-menu', menu_item).css('borderTopWidth'),10);
                var border_bottom_width = parseInt($('> ul.mega-sub-menu', menu_item).css('borderBottomWidth'),10);
                
                $('> ul.mega-sub-menu', menu_item).css('minHeight', max_height + border_bottom_width + border_top_width);
            } else {
                $('> ul.mega-sub-menu', menu_item).css('minHeight', '');
            }
        }

        var $window = $(window);

        var windowWidth = $window.width();

        $window.resize(function() {
            if ($window.width() != windowWidth) {
                calculate_tabbed_sub_menu_widths($('li.mega-menu-tabbed'));
                calculate_tabbed_sub_menu_heights($('li.mega-menu-tabbed'));
            }            
        });

        $('li.mega-menu-tabbed, li.mega-menu-tabbed li.mega-collapse-children').on('open_panel', function() {
            var menu = $(this).parents('.mega-menu');
            var menu_item = $(this).closest(".mega-menu-tabbed");

            $("> ul.mega-sub-menu", $(this)).promise().done(function(){
                calculate_tabbed_sub_menu_widths( menu_item );
                calculate_tabbed_sub_menu_heights( menu_item );
            });

            if ( menu.data('view') == 'desktop' ) {
                if ($('> ul.mega-sub-menu > li.mega-menu-item-has-children.mega-toggle-on', menu_item).length == 0 ) {
                    $('> ul.mega-sub-menu > li.mega-menu-item-has-children', menu_item).first().addClass('mega-toggle-on');
                }

                $('li.mega-menu-tabbed').on('close_panel', function() {
                    $(".mega-toggle-on", menu).removeClass("mega-toggle-on");
                });
            }

            $('li.mega-menu-tabbed li.mega-collapse-children').on('close_panel', function() {
                var menu_item = $(this).closest(".mega-menu-tabbed");

                $("> ul.mega-sub-menu", $(this)).promise().done(function(){
                    calculate_tabbed_sub_menu_widths( menu_item );
                    calculate_tabbed_sub_menu_heights( menu_item );
                });
            });
        });
    });
})(jQuery);