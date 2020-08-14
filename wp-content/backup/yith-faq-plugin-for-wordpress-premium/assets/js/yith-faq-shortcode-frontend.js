jQuery(function ($) {

    var $ajax_call = false;

    $(document).ready(function () {

        set_actions();

        if (window.location.hash) {

            var $hash = window.location.hash,
                $data = {
                    action : 'ywfp_find_faq',
                    page_id: yith_faq.page_id,
                    faq_id : $hash
                };

            $('#yith-faqs-container').addClass('yith-faqs-loading');

            $.post(yith_faq.ajax_url, $data, function (response) {

                if (response.success) {

                    if (response.page > 1) {
                        $('.page-' + response.page + ' a').click();
                    } else {
                        $($hash).find('.yith-faqs-title').click();
                        $('#yith-faqs-container').removeClass('yith-faqs-loading');
                        $(window).scrollTop($($hash).offset().top - 40);
                    }

                }

            });


        }

        $(document).trigger('yith_faq_loaded');

    });

    $.fn.yith_faq_filtering = function (e, obj) {
        e.preventDefault();
        var $href = obj.href,
            $hash = window.location.hash,
            $url = window.location.href,
            $this = $(obj),
            $search_box = $('.yith-faqs-search input'),
            $terms = ($search_box !== undefined ? $search_box.val() : ''),
            $container = '#yith-faqs-container',
            $categories = '.yith-faqs-categories',
            $navigation = '.yith-faqs-pagination',
            $new_page = '',
            $old_page = '';

        if ($url.indexOf('?term_id=') !== -1) {

            if ($href && $href.indexOf('?page=') !== -1) {

                $new_page = $href.substr($href.indexOf('=') + 1);

                if ($url.indexOf('&page=') !== -1) {
                    $old_page = $url.substr($url.indexOf('&page=') + 6);
                    $href = $url.replace('page=' + $old_page, 'page=' + $new_page)
                } else {
                    $href = $url + '&page=' + $new_page;
                }

            }

        }

        if ($this.is('button') || $this.is('input')) {

            if ($terms !== '') {
                $terms = $terms.replace(' ', '+');
                $href = '?faq-s=' + $terms;
            }
        }


        $($container).addClass('yith-faqs-loading');
        $($navigation).hide();

        if ($ajax_call !== false) {
            $ajax_call.abort();
            $ajax_call = false;
        }

        $ajax_call = $.ajax({
            url    : $href,
            success: function (response) {

                $ajax_call = false;
                $($container).removeClass('yith-faqs-loading');

                if ($(response).find($container).length > 0) {
                    $($container).html('').html($(response).find($container).html());

                    if (yith_faq.enable_scroll) {
                        var $scroll_top = $('.yith-faqs').offset().top - yith_faq.scroll_offset;
                        $(window).scrollTop($scroll_top);
                    }

                    set_actions();

                } else {
                    $($container).html('').html($(response).find('.woocommerce-info'));
                }

                if ($(response).find($navigation).length > 0) {

                    if ($($navigation).length === 0) {
                        $.jseldom($navigation).insertAfter($($navigation));
                    }

                    $($navigation).html($(response).find($navigation).html()).show();

                } else {
                    $($navigation).empty();
                }

                if ($(response).find($categories).length > 0) {

                    if ($($categories).length === 0) {
                        $.jseldom($categories).insertAfter($($categories));
                    }

                    $($categories).html($(response).find($categories).html()).show();

                } else {
                    $($categories).empty();
                }

                //update browser history (IE doesn't support it)
                if (!navigator.userAgent.match(/msie/i)) {
                    window.history.pushState({"pageTitle": response.pageTitle}, "", $href + $hash);
                }

                if ($hash) {
                    $($hash).find('.yith-faqs-title').click();
                }

                //trigger ready event
                $(document).trigger("ready");
                $(window).trigger("scroll");
                $(document).trigger('yith_faq_loaded');

            }
        });


    };

    $(document).on('click', '.yith-faqs-categories a, .yith-faqs-page a, .yith-faqs button', function (e) {
        $(this).yith_faq_filtering(e, this);
    });

    $(document).on('keydown', '.yith-faqs input', function (e) {

        if (e.keyCode === 13 && !e.shiftKey) {

            $(this).yith_faq_filtering(e, this);

        }

    });

    function set_actions() {

        $('.yith-faq-type-toggle .yith-faqs-title').each(function () {
            $(this).click(function () {

                var faq = $(this).parent(),
                    icon = $(this).find('.icon'),
                    icon_class = icon.attr('class'),
                    new_icon_class = '';

                faq.find('.yith-faqs-content-wrapper').slideToggle();
                faq.toggleClass('active');

                if (faq.hasClass('active')) {
                    new_icon_class = icon_class.replace('plus', 'minus').replace('down', 'up');
                } else {
                    new_icon_class = icon_class.replace('minus', 'plus').replace('up', 'down');
                }

                icon.removeClass(icon_class).addClass(new_icon_class);

            });
        });

        $('.yith-faq-type-accordion .yith-faqs-title').each(function () {
            $(this).click(function () {

                var faq = $(this).parent(),
                    icon = $(this).find('.icon'),
                    icon_class = icon.attr('class'),
                    new_icon_class = '',
                    active_faq = $('.yith-faqs-item.active'),
                    active_icon = active_faq.find('.icon'),
                    active_icon_class = active_icon.attr('class');

                if (active_icon_class !== undefined && faq.attr('id') !== active_faq.attr('id')) {
                    var active_new_icon_class = active_icon_class.replace('minus', 'plus').replace('up', 'down');
                    active_faq.find('.yith-faqs-content-wrapper').slideUp();
                    active_faq.removeClass('active');
                    active_icon.removeClass(active_icon_class).addClass(active_new_icon_class);
                }

                faq.find('.yith-faqs-content-wrapper').slideToggle();
                faq.toggleClass('active');

                if (faq.hasClass('active')) {
                    new_icon_class = icon_class.replace('plus', 'minus').replace('down', 'up');
                } else {
                    new_icon_class = icon_class.replace('minus', 'plus').replace('up', 'down');
                }

                icon.removeClass(icon_class).addClass(new_icon_class);

            });
        });

        $('.yith-faqs-link a').each(function () {

            $(this).click(function (e) {
                e.preventDefault();
                var $this = $(this),
                    $hover_text = $this.find('.hover-text'),
                    $success_text = $this.find('.success-text'),
                    $temp = $('<input>'),
                    $href = $this.data('faq');

                $hover_text.hide();
                $success_text.show();
                $('body').append($temp);
                $temp.val($href).select();
                document.execCommand("copy");
                $temp.remove();

                setTimeout(function () {
                    $this.removeClass('hover');
                    $hover_text.show();
                    $success_text.hide();
                }, 1000);

            });

            $(this).hover(function () {
                $(this).addClass('hover');
            }, function () {
                $(this).removeClass('hover');
            });

        });

    }

});
