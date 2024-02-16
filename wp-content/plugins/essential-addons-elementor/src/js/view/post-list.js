var postListHandler = function ($scope, $) {
    // category
    ea.getToken();
    let $post_cat_wrap = $('.post-categories', $scope),
        $scroll_on_pagination = parseInt( $post_cat_wrap.data('scroll-on-pagination') ),
        $scroll_on_pagination_offset = parseInt( $post_cat_wrap.data('scroll-offset') );
    
    $post_cat_wrap.on('click', 'a', function (e) {
        e.preventDefault();
        let $this = $(this);
        // tab class
        $('.post-categories a', $scope).removeClass('active');
        $this.addClass('active');
        // collect props
        const $class = $post_cat_wrap.data('class'),
            $widget_id = $post_cat_wrap.data("widget"),
            $page_id = $post_cat_wrap.data("page-id"),
            $nonce = $post_cat_wrap.data("nonce"),
            $args = $post_cat_wrap.data('args'),
            $settings = $post_cat_wrap.data('settings'),
            $page = 1,
            $template_info = $post_cat_wrap.data('template'),
            $taxonomy = {
                taxonomy: $('.post-categories a.active', $scope).data('taxonomy'),
                field: 'term_id',
                terms: $('.post-categories a.active', $scope).data('id')
            };

        // ajax
        $.ajax({
            url: localize.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_more',
                class: $class,
                args: $args,
                taxonomy: $taxonomy,
                settings: $settings,
                template_info: $template_info,
                page: $page,
                page_id: $page_id,
                widget_id: $widget_id,
                nonce: localize.nonce
            },
            success: function (response) {
                var $content = $(response);

                if ($content.hasClass('no-posts-found') || $content.length == 0) {
                    $('.eael-post-appender', $scope).empty().append($content);

                    // update nav
                    $('.btn-prev-post', $scope).prop('disabled', true);
                    $('.btn-next-post', $scope).prop('disabled', true);
                } else {
                    $('.eael-post-appender', $scope)
                        .empty()
                        .append($content);

                    // update page
                    $('.post-list-pagination', $scope).data('page', 1);

                    // update nav
                    $('.btn-prev-post', $scope).prop('disabled', true);
                    $('.btn-next-post', $scope).prop('disabled', false);
                }
            },
            error: function (response) {
                console.log(response);
            }
        });
    });

    // load more
    let $pagination_wrap = $('.post-list-pagination', $scope);
    $pagination_wrap.on('click', 'button', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        // collect props
        var $this = $(this),
            $widget_id = $pagination_wrap.data("widget"),
            $page_id = $pagination_wrap.data("page-id"),
            $nonce = $pagination_wrap.data("nonce"),
            $class = $pagination_wrap.data('class'),
            $args = $pagination_wrap.data('args'),
            $settings = $pagination_wrap.data('settings'),
            $page = $this.hasClass('btn-prev-post')
                ? parseInt($pagination_wrap.data('page')) - 1
                : parseInt($pagination_wrap.data('page')) + 1,
            $template_info = $pagination_wrap.data('template'),
            $taxonomy = {
                taxonomy: $('.post-categories a.active', $scope).data('taxonomy'),
                field: 'term_id',
                terms: $('.post-categories a.active', $scope).data('id')
            };

        if (($taxonomy.taxonomy === '') || ($taxonomy.taxonomy === 'all') || ($taxonomy.taxonomy === 'undefined')) {
            $taxonomy.taxonomy = 'all';
        }

        if ($page == 1 && $this.hasClass("btn-prev-post")) {
            $this.prop('disabled', true);
        }
        $this.prop('disabled', true);

        if ($page <= 0) {
            return;
        }

        $.ajax({
            url: localize.ajaxurl,
            type: 'post',
            data: {
                action: 'load_more',
                class: $class,
                args: $args,
                taxonomy: $taxonomy,
                settings: $settings,
                page: $page,
                template_info: $template_info,
                page_id: $page_id,
                widget_id: $widget_id,
                nonce: localize.nonce
            },
            success: function (response) {
                var $content = $(response);
                if ($content.hasClass('no-posts-found') || $content.length == 0) {
                    // do nothing
                } else {

                    $('.eael-post-appender', $scope)
                        .empty()
                        .append($content);
                    if ($page == 1 && $this.hasClass("btn-prev-post")) {
                        $this.prop('disabled', true);
                    } else {
                        $('.post-list-pagination button', $scope).prop('disabled', false);
                    }
                    $pagination_wrap.data('page', $page);
                }

                if ( $scroll_on_pagination && $('.eael-post-appender', $scope).length > 0) {
                    let $post_list_container = $('.eael-post-list-container', $scope);
                    
                    if ( ! isElementInViewport( $post_list_container ) ) {
                        $('html, body').animate({
                            scrollTop: $post_list_container.offset().top - $scroll_on_pagination_offset
                        }, 500);
                    }
                }
            },
            error: function (response) {
                console.log(response);
            }
        });
    });

    function isElementInViewport (el) {
        if (typeof jQuery === "function" && el instanceof jQuery) {
            el = el[0];
        }
    
        var rect = el.getBoundingClientRect();
    
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /* or $(window).height() */
            rect.right <= (window.innerWidth || document.documentElement.clientWidth) /* or $(window).width() */
        );
    }
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/eael-post-list.default', postListHandler);
});
