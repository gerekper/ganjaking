/**
 * wppm.plugin.js
 * JavaScript functions required by the plugin
 *
 * @version 1.9.0
 */
(function ($) {

    'use strict';

    var WPPM_Ticker_Handler, WPPM_EL_Slider_Handler, WPPM_EL_Sharing_Handler, WPPM_Ajax_Nav_Handler, WPPM_EL_Sharing_refresh;

    // Category submenu toggle
    function wppm_cat_dropdown() {
        $(document).on('click', 'a.wppm-cat-toggle', function (e) {
            e.preventDefault();
            var this_cat = $(this).parent().find('ul.submenu');
            $('.post-cats .cat-sub').not(this_cat).hide();
            this_cat.toggle();
            $(this).toggleClass('active-link');
            return false;
        });
    }

    wppm_cat_dropdown();

    $('.wppm-tabber,.wppm-ajax-posts').on('wppm_ajax_content_loaded wppm_ajax_loaded', function () {
        wppm_cat_dropdown();
    });

    // Close category submenus when clicking on body
    $(document).on('click', function () {
        $('.post-cats .cat-sub').hide();
        $('a.wppm-cat-toggle').removeClass('active-link');
    });

    // Stop propagation for various selectors
    $(document).on('click', 'a.wppm-cat-toggle,a.share-trigger', function (e) {
        e.stopPropagation();
    });

    WPPM_Ticker_Handler = function ($scope, $) {
        var ticker_elem = $scope.find('.wppm-ticker').eq(0);
        // News Ticker
        if ($.isFunction($.fn.marquee)) {
            ticker_elem.marquee({
                duration: $(this).data('duration'),
                gap: 0,
                delayBeforeStart: 0,
                direction: 'left',
                startVisible: true,
                duplicated: true,
                pauseOnHover: true,
                allowCss3Support: true
            });
        }
    };

    WPPM_EL_Slider_Handler = function ($scope, $) {

        var slider_elem = $scope.find('.posts-slider').eq(0),
            params = '';


        if (slider_elem.length > 0) {
            params = slider_elem.data('params');

            slider_elem.find('.owl-carousel').owlCarousel({
                items: parseInt(params.items.size),
                loop: 'true' === params.loop ? true : false,
                margin: parseInt(params.slide_margin.size),
                autoplay: 'true' === params.autoplay ? true : false,
                autoplayTimeout: parseInt(params.timeout),
                autoHeight: 'true' === params.autoheight ? true : false,
                nav: 'true' === params.nav ? true : false,
                dots: 'true' === params.dots ? true : false,
                smartSpeed: parseInt(params.speed),
                navText: false,
                rtl: ($("body").is(".rtl")),
                autoplayHoverPause: true,
                animateIn: params.animatein,
                animateOut: params.animateout,
                //stagePadding: parseInt(params.stagepadding),

                responsive: {
                    0: {
                        items: parseInt(params.items_mobile.size),
                        margin: parseInt(params.slide_margin_mobile.size)
                    },
                    426: {
                        items: parseInt(params.items_tablet.size),
                        margin: parseInt(params.slide_margin_tablet.size)
                    },
                    1025: {
                        items: parseInt(params.items.size)
                    }
                }
            });
        }
    };

    WPPM_EL_Sharing_Handler = function ($scope, $) {
        var share_btn = $scope.find('a.nn-more'),
            links = $scope.find('.wppm-el-sharing-list li:not(.no-popup) a, .wppm-el-sharing-inline li:not(.no-popup) a'),
            close_btn = $scope.find('.wppm-el-sharing-list > li.sharing-modal-handle > a.close-sharing'),
            share_overlay = $scope.find('.sharing-overlay');

        // Social sharing overlay
        share_btn.on('click', function (e) {
            e.preventDefault();
            $(this).closest('.wppm-el-sharing-container').find('.sharing-overlay').toggleClass('is-open');
            $('body').toggleClass('overlay-active');
        });

        close_btn.on('click', function (e) {
            e.preventDefault();
            $(this).closest('.sharing-overlay').removeClass('is-open');
            $('body').removeClass('overlay-active');
        });

        // Sharing links
        links.on('click', function (e) {
            e.preventDefault();
            var href = $(this).attr('href');
            window.open(href, '_blank', 'width=600,height=400,menubar=0,resizable=1,scrollbars=0,status=1', true);
        });
    };


    /* Ajax next/prev links */
    WPPM_Ajax_Nav_Handler = function ($scope, $) {
        var ajax_posts = $scope.find('.wppm-ajax-posts');
        ajax_posts.each(function () {
            var parent = $(this),
                requestRunning = false,
                pagenum = 1,
                params = $(this).data('params'),
                maxposts = $(this).data('maxposts'),
                cont = $(this),
                cont_id = cont.attr('id'),
                cont_height = 0,
                status_text = cont.find('span.nav-status').data('format') || '%current% of %total%',
                new_status,
                max_mod,
                max_div,
                maxpages,
                offset,
                new_offset;

            // Set num if not exists
            if (!('num' in params)) {
                params.num = '6';
            }

            // Set offset if not exists
            if (!('offset' in params)) {
                params.offset = 0;
            }

            offset = params.offset;

            // Re calculate maxposts if there is an offset
            maxposts = maxposts - parseInt(params.offset);
            max_mod = parseInt(maxposts) % parseInt(params.num);
            max_div = parseInt(maxposts / params.num);
            maxpages = (0 === max_mod) ? max_div : max_div + 1;

            // Update nav status
            new_status = status_text.replace('%current%', pagenum).replace('%total%', maxpages);
            $(this).find('span.nav-status').text(status_text.replace('%current%', pagenum).replace('%total%', maxpages));

            // Ajax button click event
            cont.find('.wppm-ajax-nav,.wppm-ajax-loadmore').on('click', 'a.next-link,a.wppm-more-link', function (e) {
                e.preventDefault();
                var nav = $(this).parent(),
                    btn = $(this),
                    to_show = cont_id + '-sub-' + parseInt(pagenum + 1);
                new_offset = parseInt(offset) + parseInt(params.num) * parseInt(pagenum);
                params.offset = parseInt(new_offset);

                console.log(params.offset);

                if (pagenum > 1) {
                    $(this).parent().find('a.prev-link').removeClass('disabled');
                } else {
                    $(this).parent().find('a.prev-link').addClass('disabled');
                }

                if ((parseInt(params.num) * pagenum) >= maxposts) {
                    $(this).addClass('disabled');
                    return;
                }

                if (params) {
                    if (btn.is('.next-link')) {
                        cont.find('.wppm').removeClass('fade-in-top fade-out-half').addClass('fade-out-half');
                        cont.addClass('wppm-loading').css({
                            "min-height": cont_height
                        });
                    }
                    if (btn.is('.wppm-more-link')) {
                        $(btn).parent().addClass('wppm-loading');
                    }

                    if ($('#' + to_show).length) {
                        if (btn.is('.next-link')) {
                            cont.find('.wppm').removeClass('fade-in-top fade-out-half').addClass('fade-out-full');
                            cont.removeClass('wppm-loading');
                            $('#' + to_show).removeClass('fade-out-full').addClass('fade-in-top');
                            $(this).parent().find('a.prev-link').removeClass('disabled');
                            pagenum++;
                            cont.find('span.nav-status').text(status_text.replace('%current%', pagenum).replace('%total%', maxpages));
                        } else {
                            $('#' + to_show).removeClass('fade-out-full').addClass('fade-in-top');
                            $(btn).parent().removeClass('wppm-loading');
                        }
                        if ((parseInt(params.num) * pagenum) >= maxposts) {
                            $(btn).addClass('disabled');
                        }
                    } else {
                        if (requestRunning) { // don't do anything if an AJAX request is pending
                            return;
                        }

                        requestRunning = true;
                        $.ajax({
                            url: wppm_el_localize.ajax_url,
                            type: "post",

                            data: {
                                'action': 'wppm_ajaxnav_action',
                                'wppm_ajaxnav_content': params
                            },

                            success: function (response) {
                                pagenum++;

                                cont.find('span.nav-status').text(status_text.replace('%current%', pagenum).replace('%total%', maxpages));
                                $(btn).parent().find('a.prev-link').removeClass('disabled');

                                if ((parseInt(params.num) * pagenum) >= maxposts) {
                                    $(btn).addClass('disabled');
                                }

                                if (btn.is('.next-link')) {
                                    cont.find('.wppm').addClass('fade-out-full');
                                    $(response).find('.wppm').attr('id', cont_id + '-sub-' + pagenum).insertBefore(nav).addClass('fade-in-top');
                                } else {
                                    $(response).find('.wppm').attr('id', cont_id + '-sub-' + pagenum).insertBefore(btn.parent()).addClass('fade-in-top');
                                }
                                parent.trigger('wppm_ajax_content_loaded');
                                $(document).trigger('ready wppm_ajax_loaded');
                            },
                            complete: function () {
                                requestRunning = false;
                                if (btn.is('.next-link')) {
                                    cont.removeClass('wppm-loading');
                                } else {
                                    $(btn).parent().removeClass('wppm-loading');
                                }
                            },
                            error: function (errorThrown) {
                                console.log(errorThrown);
                            }
                        });
                    }
                }
            });

            cont.find('.wppm-ajax-nav').on('click', 'a.prev-link', function (e) {
                e.preventDefault();

                var to_show = cont_id + '-sub-' + parseInt(pagenum - 1);

                if ($('#' + to_show).length) {
                    cont.find('.wppm').removeClass('fade-in-top fade-out-half').addClass('fade-out-full');
                    $('#' + to_show).removeClass('fade-out-full').addClass('fade-in-top');
                    pagenum--;
                    cont.find('span.nav-status').text(status_text.replace('%current%', pagenum).replace('%total%', maxpages));
                    parent.trigger('wppm_ajax_content_loaded');
                }

                if ((parseInt(params.num) * pagenum) <= maxposts) {
                    $(this).parent().find('a.next-link').removeClass('disabled');
                }

                if (pagenum <= 1) {
                    $(this).addClass('disabled');
                    return;
                }

            });
        });
    };

    // Executer under Elementor
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/wp-post-modules-el.default', WPPM_EL_Slider_Handler);
        elementorFrontend.hooks.addAction('frontend/element_ready/wp-post-modules-el.default', WPPM_EL_Sharing_Handler);
        elementorFrontend.hooks.addAction('frontend/element_ready/wp-post-modules-el.default', WPPM_Ajax_Nav_Handler);
        elementorFrontend.hooks.addAction('frontend/element_ready/wp-post-modules-el.default', WPPM_EL_Sharing_refresh);
        elementorFrontend.hooks.addAction('frontend/element_ready/wp-post-modules-el.default', WPPM_Ticker_Handler);
    });

    WPPM_EL_Sharing_refresh = function ($scope, $) {
        $('.wppm-ajax-posts').on('wppm_ajax_content_loaded', function () {
            WPPM_EL_Sharing_Handler($(this), $);
        });
    };

})(jQuery);