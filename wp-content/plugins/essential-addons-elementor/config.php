<?php

$config = [
    'elements'   => [
        'fancy-text'             => [
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/fancy-text.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'advanced-data-table'    => [
            'dependency' => [
                'js' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/edit/advanced-data-table.min.js',
                        'type'    => 'self',
                        'context' => 'edit',
                    ],
                ],
            ],
        ],
        'progress-bar'           => [
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/progress-bar.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/progress-bar.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'price-table'            => [
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/price-table.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'creative-btn'           => [
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/creative-btn.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'team-members'           => [
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/team-members.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'data-table'             => [
            'dependency' => [
                'js' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/table-sorter/jquery.tablesorter.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/data-table.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ], // end of extend
        'img-comparison'         => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Image_Comparison',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/lib-view/twentytwenty/twentytwenty.min.css',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/img-comparison.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/imagesloaded/imagesloaded.pkgd.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/jquery.event.move/jquery.event.move.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/twentytwenty/jquery.twentytwenty.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/img-comparison.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'instagram-gallery'      => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Instagram_Feed',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/load-more.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/instagram-gallery.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/imagesloaded/imagesloaded.pkgd.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/isotope/isotope.pkgd.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/instagram-gallery.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'interactive-promo'      => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Interactive_Promo',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/interactive-promo.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'lightbox'               => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Lightbox',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/lib-view/magnific-popup/magnific-popup.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/lightbox.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/magnific-popup/jquery.magnific-popup.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/jquery.cookie/jquery.cookie.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/lightbox.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'post-block'             => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Post_Block',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/load-more.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/post-grid.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/post-block-overlay.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/post-block.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/view/load-more.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'testimonial-slider'     => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Testimonial_Slider',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/testimonial-slider.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/testimonial-slider.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'static-product'         => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Static_Product',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/post-block.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/post-block-overlay.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/static-product.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'adv-google-map'         => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Google_Map',
            'dependency' => [
                'js' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/gmap/gmap.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/adv-google-map.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/adv-google-map.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'flip-carousel'          => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Flip_Carousel',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/lib-view/flipster/jquery.flipster.min.css',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/flip-carousel.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/flipster/jquery.flipster.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/flip-carousel.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'interactive-cards'      => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Interactive_Card',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/lib-view/interactive-cards/interactive-cards.min.css',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/interactive-cards.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/nicescroll/jquery.nicescroll.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/interactive-cards/interactive-cards.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/interactive-cards.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'content-timeline'       => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Content_Timeline',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/load-more.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/content-timeline.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/vertical-timeline/vertical-timeline.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/content-timeline.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'twitter-feed-carousel'  => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Twitter_Feed_Carousel',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/twitter-feed.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/twitter-feed-carousel.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'dynamic-filter-gallery' => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Dynamic_Filterable_Gallery',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/load-more.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/filterable-gallery.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/dynamic-filter-gallery.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/imagesloaded/imagesloaded.pkgd.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/isotope/isotope.pkgd.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/magnific-popup/jquery.magnific-popup.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/jquery.resize/jquery.resize.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/view/load-more.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/dynamic-filter-gallery.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'post-list'              => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Post_List',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/post-list.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/ajax-post-search.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/post-list.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'toggle'                 => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Toggle',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/toggle.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/toggle.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'mailchimp'              => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Mailchimp',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/mailchimp.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/mailchimp.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'divider'                => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Divider',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/divider.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'price-menu'             => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Price_Menu',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/price-menu.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'image-hotspots'         => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Image_Hot_Spots',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/lib-view/animate/animate.min.css',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/image-hotspots.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/tipso/tipso.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/image-hotspots.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'one-page-navigation'    => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\One_Page_Navigation',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/one-page-navigation.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/one-page-navigation.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'counter'                => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Counter',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/lib-view/odometer/odometer-theme-default.min.css',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/counter.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/waypoint/waypoints.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/odometer/odometer.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/counter.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'post-carousel'          => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Post_Carousel',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/post-grid.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/post-carousel.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/post-carousel.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'team-member-carousel'   => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Team_Member_Carousel',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/team-member-carousel.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/team-member-carousel.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'logo-carousel'          => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Logo_Carousel',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/logo-carousel.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/logo-carousel.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'protected-content'      => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Protected_Content',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/protected-content.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'offcanvas'              => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Offcanvas',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/offcanvas.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/offcanvas/eael.offcanvas.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/offcanvas.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'advanced-menu'          => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Advanced_Menu',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/advanced-menu.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/advanced-menu.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'image-scroller'         => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Image_Scroller',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/image-scroller.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/image-scroller.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'learn-dash-course-list' => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\LD_Course_List',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/learn-dash-course-list.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/imagesloaded/imagesloaded.pkgd.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PLUGIN_PATH . 'assets/front-end/js/lib-view/isotope/isotope.pkgd.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/learn-dash-course-list.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'woo-collections'        => [
            'class'      => '\Essential_Addons_Elementor\Pro\Elements\Woo_Collections',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/woo-collections.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'woo-checkout'        => [
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/woo-checkout-pro.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/woo-checkout-pro.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'login-register'             => [
	        'dependency' => [
		        'js'  => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/login-register.min.js',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
		        'css'  => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/login-register.min.css',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
	        ],
        ],
        'woo-product-slider'        => [
	        'class'      => '\Essential_Addons_Elementor\Pro\Elements\Woo_Product_Slider',
	        'dependency' => [
		        'css' => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/lib-view/animate/animate.min.css',
				        'type'    => 'lib',
				        'context' => 'view',
			        ],
			        [
				        'file' => EAEL_PLUGIN_PATH . 'assets/front-end/css/view/quick-view.min.css',
				        'type' => 'self',
				        'context' => 'view',
			        ],
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/woo-product-slider.min.css',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
		        'js'  => [
			        [
				        'file' => EAEL_PLUGIN_PATH . 'assets/front-end/js/view/quick-view.min.js',
				        'type' => 'self',
				        'context' => 'view',
			        ],
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/woo-product-slider.min.js',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
	        ],
        ],
        'advanced-search' => [
	        'class'      => '\Essential_Addons_Elementor\Pro\Elements\Advanced_Search',
	        'dependency' => [
		        'css' => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/advanced-search.min.css',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
		        'js'  => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/advanced-search.min.js',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
	        ],
        ],
        'woo-thank-you' => [
	        'class' => '\Essential_Addons_Elementor\Pro\Elements\Woo_Thank_You',
	        'dependency' => [
		        'css' => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/woo-thank-you.min.css',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
	        ],
        ],
        'woo-cross-sells' => [
	        'class'      => '\Essential_Addons_Elementor\Pro\Elements\Woo_Cross_Sells',
	        'dependency' => [
		        'css' => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/woo-cross-sells.min.css',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
		        'js' => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/woo-cross-sells.min.js',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ]
	        ],
        ],
        'woo-account-dashboard' => [
	        'class'      => '\Essential_Addons_Elementor\Pro\Elements\Woo_Account_Dashboard',
	        'dependency' => [
		        'css' => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/woo-account-dashboard.min.css',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
		        'js'  => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/woo-account-dashboard.min.js',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
	        ],
        ],
        'fancy-chart' => [
	        'class'      => '\Essential_Addons_Elementor\Pro\Elements\Fancy_Chart',
	        'dependency' => [
		        'js' => [
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/chart/chart.js',
				        'type'    => 'lib',
				        'context' => 'view',
			        ],
			        [
				        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/fancy-chart.min.js',
				        'type'    => 'self',
				        'context' => 'view',
			        ],
		        ],
	        ],
        ],
    ],
    'extensions' => [
        'section-particles'       => [
            'class'      => '\Essential_Addons_Elementor\Pro\Extensions\EAEL_Particle_Section',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/section-particles.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/particles/particles.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/section-particles.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'section-parallax'        => [
            'class'      => '\Essential_Addons_Elementor\Pro\Extensions\EAEL_Parallax_Section',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/section-parallax.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
	                [
		                'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/gsap/gsap.min.js',
		                'type'    => 'lib',
		                'context' => 'view',
	                ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/jarallax/jarallax.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/jquery-parallax/jquery-parallax.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/view/section-parallax.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'tooltip-section'    => [
            'class'      => '\Essential_Addons_Elementor\Pro\Extensions\EAEL_Tooltip_Section',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/lib-view/tippy/tippy.min.css',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                ],
                'js'  => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/popper/popper.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/lib-view/tippy/tippy.min.js',
                        'type'    => 'lib',
                        'context' => 'view',
                    ],
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/js/edit/advanced-tooltip.min.js',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'content-protection' => [
            'class'      => '\Essential_Addons_Elementor\Pro\Extensions\Content_Protection',
            'dependency' => [
                'css' => [
                    [
                        'file'    => EAEL_PRO_PLUGIN_PATH . 'assets/front-end/css/view/protected-content.min.css',
                        'type'    => 'self',
                        'context' => 'view',
                    ],
                ],
            ],
        ],
        'xd-copy' => [
	        'class' => '\Essential_Addons_Elementor\Pro\Extensions\XD_Copy',
        ],
        'conditional-display' => [
	        'class' => '\Essential_Addons_Elementor\Pro\Extensions\Conditional_Display',
        ],
    ],
];

return $config;
