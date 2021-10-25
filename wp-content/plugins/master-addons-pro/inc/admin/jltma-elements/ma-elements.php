<?php

namespace MasterAddons\Admin\Dashboard\Addons\Elements;

if (!class_exists('JLTMA_Addon_Elements')) {
    class JLTMA_Addon_Elements
    {
        private static $instance = null;
        public static $jltma_elements;

        public function __construct()
        {
            self::$jltma_elements = [
                'jltma-addons'      => [
                    'title'             => esc_html__('Content Elements', MELA_TD),
                    'elements'          => [
                        [
                            'key'               => 'ma-animated-headlines',
                            'title'             => esc_html__('Animated Headlines', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/animated-headline/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/animated-headline-elementor/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=09QIUPdUQnM'
                        ],
                        [
                            'key'               => 'ma-call-to-action',
                            'title'             => esc_html__('Call to Action', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/call-to-action/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/call-to-action/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=iY2q1jtSV5o'
                        ],
                        [
                            'key'               => 'ma-dual-heading',
                            'title'             => esc_html__('Dual Heading', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/dual-heading/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/dual-heading/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=kXyvNe6l0Sg'
                        ],
                        [
                            'key'               => 'ma-accordion',
                            'title'             => esc_html__('Advanced Accordion', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/advanced-accordion/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/elementor-accordion-widget/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=rdrqWa-tf6Q'
                        ],
                        [
                            'key'               => 'ma-tabs',
                            'title'             => esc_html__('Tabs', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/tabs/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/tabs-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=lsqGmIrdahw'
                        ],
                        [
                            'key'               => 'ma-tooltip',
                            'title'             => esc_html__('Tooltip', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/tooltip/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/adding-tooltip-in-elementor-editor/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=Av3eTae9vaE'
                        ],
                        [
                            'key'               => 'ma-progressbar',
                            'title'             => esc_html__('Progress Bar', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/progress-bar/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-create-and-customize-progressbar-in-elementor/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=77-b1moRE8M'
                        ],
                        [
                            'key'               => 'ma-progressbars',
                            'title'             => esc_html__('Progress Bars', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/multiple-progress-bars/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/progress-bars-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=Mc9uDWJQMIY'
                        ],
                        [
                            'key'               => 'ma-team-members',
                            'title'             => esc_html__('Team Member', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/team-member/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/adding-team-members-in-elementor-page-builder/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=wXPEl93_UBw'
                        ],
                        [
                            'key'               => 'ma-team-members-slider',
                            'title'             => esc_html__('Team Slider', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/team-carousel/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/team-members-carousel/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=ubP_h86bP-c'
                        ],
                        [
                            'key'               => 'ma-creative-buttons',
                            'title'             => esc_html__('Creative Button', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/creative-button/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/creative-button/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=kFq8l6wp1iI'
                        ],
                        [
                            'key'               => 'ma-changelog',
                            'title'             => esc_html__('Changelogs', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/changelogs/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/changelog-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=qWRgJkFfBow'
                        ],
                        [
                            'key'               => 'ma-infobox',
                            'title'             => esc_html__('Infobox', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/infobox/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/infobox-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=2-ymXAZfrF0'
                        ],
                        [
                            'key'               => 'ma-flipbox',
                            'title'             => esc_html__('Flipbox', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/flipbox/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-configure-flipbox-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=f-B35-xWqF0'
                        ],
                        [
                            'key'               => 'ma-creative-links',
                            'title'             => esc_html__('Creative Links', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/creative-link/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-add-creative-links/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=o6SmdwMJPyA'
                        ],
                        [
                            'key'               => 'ma-image-hover-effects',
                            'title'             => esc_html__('Image Hover Effects', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/image-hover-effects/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/image-hover-effects-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=vWGWzuRKIss'
                        ],
                        [
                            'key'               => 'ma-blog',
                            'title'             => esc_html__('Blog', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/blog-element/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/blog-element-customization/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=03AcgVEsTaA'
                        ],
                        [
                            'key'               => 'ma-news-ticker',
                            'title'             => esc_html__('News Ticker', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/news-ticker/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/news-ticker-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=jkrBCzebQ-E',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'ma-timeline',
                            'title'             => esc_html__('Timeline', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/timeline/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/timeline-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=0mcDMKrH1A0'
                        ],
                        [
                            'key'               => 'ma-business-hours',
                            'title'             => esc_html__('Business Hours', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/business-hours/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/business-hours-elementor/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=x0_HY9uYgog'
                        ],
                        [
                            'key'               => 'ma-table-of-contents',
                            'title'             => esc_html__('Table of Contents', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/100-best-elementor-addons/',
                            'docs_url'          => '',
                            'tuts_url'          => ''
                        ],
                        [
                            'key'               => 'ma-image-hotspot',
                            'title'             => esc_html__('Image Hotspot', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/image-hotspot/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/image-hotspot/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=IDAd_d986Hg',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'ma-image-filter-gallery',
                            'title'             => esc_html__('Filterable Image Gallery', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/image-gallery/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/filterable-image-gallery/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=h7egsnX4Ewc'
                        ],
                        [
                            'key'               => 'ma-pricing-table',
                            'title'             => esc_html__('Pricing Table', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/pricing-table/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/pricing-table-elementor-free-widget/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=_FUk1EfLBUs'
                        ],
                        [
                            'key'               => 'ma-image-comparison',
                            'title'             => esc_html__('Image Comparison', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/image-comparison/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/image-comparison-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=3nqRRXSGk3M'
                        ],
                        [
                            'key'               => 'ma-restrict-content',
                            'title'             => esc_html__('Restrict Content', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/restrict-content-for-elementor/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/restrict-content-for-elementor/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=Alc1R_W5_Z8',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'ma-current-time',
                            'title'             => esc_html__('Current Time', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/current-time/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/current-time/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=Icwi5ynmzkQ'
                        ],
                        [
                            'key'               => 'ma-domain-checker',
                            'title'             => esc_html__('Domain Search', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/domain-search/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-ma-domain-checker-works/',
                            'tuts_url'          => '',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'ma-table',
                            'title'             => esc_html__('Dynamic Table', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/dynamic-table/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/dynamic-table-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=bn0TvaGf9l8'
                        ],
                        [
                            'key'               => 'ma-navmenu',
                            'title'             => esc_html__('Nav Menu', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/elementor-mega-menu/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/navigation-menu/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=WhA5YnE4yJg'
                        ],
                        [
                            'key'               => 'ma-search',
                            'title'             => esc_html__('Search', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/search-element/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/search-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=Uk6nnoN5AJ4'
                        ],
                        [
                            'key'               => 'ma-blockquote',
                            'title'             => esc_html__('Blockquote', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/blockquote-element/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/blockquote-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=sSCULgPFSHU'
                        ],
                        [
                            'key'               => 'ma-counter-up',
                            'title'             => esc_html__('Counter Up', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/counter-up/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/counter-up-for-elementor/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=9amvO6p9kpM'
                        ],
                        [
                            'key'               => 'ma-countdown-timer',
                            'title'             => esc_html__('Countdown Timer', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/countdown-timer/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/count-down-timer/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=1lIbOLM9C1I'
                        ],
                        [
                            'key'               => 'ma-toggle-content',
                            'title'             => esc_html__('Toggle Content', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/toggle-content/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/toggle-content/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=TH6wbVuWdTA',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'ma-gallery-slider',
                            'title'             => esc_html__('Gallery Slider', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/gallery-slider/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/gallery-slider/',
                            'tuts_url'          => '',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'ma-gradient-headline',
                            'title'             => esc_html__('Gradient Headline', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/gradient-headline/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-add-gradient-headline-in-elementor/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=NgayEI4CthU'
                        ],
                        [
                            'key'               => 'ma-advanced-image',
                            'title'             => esc_html__('Advanced Image', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/advanced-image/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/advanced-image-element/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=fhdwiiy7JiE'
                        ],
                        // [
                        //     'key'                => 'ma-image-cascading',
                        //     'title'              => esc_html__('Cascading Image', MELA_TD),
                        //     'demo_url'           => '',
                        //     'docs_url'           => '',
                        //     'tuts_url'           => ''
                        // ],
                        // [
                        //     'key'                => 'ma-image-carousel',
                        //     'title'              => esc_html__('Image Carousel', MELA_TD),
                        //     'demo_url'           => '',
                        //     'docs_url'           => '',
                        //     'tuts_url'           => ''
                        // ],
                        // [
                        //     'key'                => 'ma-logo-slider',
                        //     'title'              => esc_html__('Logo Slider', MELA_TD),
                        //     'demo_url'           => '',
                        //     'docs_url'           => '',
                        //     'tuts_url'           => ''
                        // ],
                        // [
                        //     'key'                => 'ma-twitter-slider',
                        //     'title'              => esc_html__('Twitter Slider', MELA_TD),
                        //     'demo_url'           => '',
                        //     'docs_url'           => '',
                        //     'tuts_url'           => ''
                        // ],
                        // [
                        //     'key'                => 'ma-morphing-blob',
                        //     'title'              => esc_html__('Morphing Blob', MELA_TD),
                        //     'demo_url'           => '',
                        //     'docs_url'           => '',
                        //     'tuts_url'           => ''
                        // ],


                    ]
                ]
            ];
        }

        public static function get_instance()
        {
            if (!self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }
    }
    JLTMA_Addon_Elements::get_instance();
}
