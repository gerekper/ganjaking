jQuery(window).on("elementor/frontend/init", function() {
    let InstagramGallery = function($scope, $) {
        //force square
        var force_square = function () {
            let instafeedItem = $(".eael-instafeed-square-img .eael-instafeed-item", $scope),
                itemWidth = instafeedItem.width();

            if( itemWidth > 0 ){
                $('.eael-instafeed-item-inner').css('max-height',itemWidth);
            }
        }
        if (isEditMode){
            const myObserver = new ResizeObserver(entries => {
                // this will get called whenever div dimension changes
                entries.forEach(entry => {
                    let instafeedItem = $(".eael-instafeed-square-img .eael-instafeed-item", $scope),
                    itemWidth = instafeedItem.width();

                    if( itemWidth > 0 ){
                        $('.eael-instafeed-item-inner').css('max-height',itemWidth);
                    }
                });
            });

            let instaItem = document.querySelector('.eael-instafeed-square-img .eael-instafeed-item');
            myObserver.observe(instaItem);
        }

        if (!isEditMode) {
            var $instagram_gallery = $(".eael-instafeed", $scope).isotope({
                itemSelector: ".eael-instafeed-item",
                percentPosition: true,
                columnWidth: ".eael-instafeed-item"
            });

            $instagram_gallery.imagesLoaded().progress(function() {
                $instagram_gallery.isotope("layout");
            });
        }

        force_square();
        $( window ).on('resize',force_square);

        // ajax load more
        $(".eael-load-more-button", $scope).on("click", function(e) {
            e.preventDefault();
            let $this = $(this),
                $LoaderSpan = $("span", $this),
                $text = $LoaderSpan.html(),
                $widget_id = $this.data("widget-id"),
                $post_id = $this.data("post-id"),
                $settings = $this.data("settings"),
                $page = parseInt($this.data("page"), 10);
            // update load moer button
            $this.addClass("button--loading");
            $LoaderSpan.html(localize.i18n.loading);

            $.ajax({
                url: localize.ajaxurl,
                type: "post",
                data: {
                    action: "instafeed_load_more",
                    security: localize.nonce,
                    page: $page,
                    post_id: $post_id,
                    widget_id: $widget_id,
                    settings: $settings
                },
                success: function(response) {
                    let $html = $(response.html);
                    // append items
                    let $instagram_gallery = $(".eael-instafeed", $scope).isotope();
                    $(".eael-instafeed", $scope).append($html);
                    $instagram_gallery.isotope("appended", $html);
                    $instagram_gallery.imagesLoaded().progress(function() {
                        $instagram_gallery.isotope("layout");
                    });
                    force_square();
                    // update load more button
                    if (response.num_pages > $page) {
                        $page++;
                        $this.data("page", $page);
                        $this.removeClass("button--loading");
                        $LoaderSpan.html($text);
                    } else {
                        $this.remove();
                    }
                },
                error: function() {}
            });
        });

        var InstagramGallery = function ($src) {
            $instagram_gallery.imagesLoaded().progress(function () {
                $instagram_gallery.isotope("layout");
            });
        }

        ea.hooks.addAction("ea-lightbox-triggered", "ea", InstagramGallery);
        ea.hooks.addAction("ea-advanced-tabs-triggered", "ea", InstagramGallery);
        ea.hooks.addAction("ea-advanced-accordion-triggered", "ea", InstagramGallery);
        ea.hooks.addAction("ea-toggle-triggered", "ea", InstagramGallery);
    };
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/eael-instafeed.default",
        InstagramGallery
    );
});
