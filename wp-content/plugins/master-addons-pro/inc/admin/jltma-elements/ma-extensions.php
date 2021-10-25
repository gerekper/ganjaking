<?php

namespace MasterAddons\Admin\Dashboard\Addons\Extensions;

if (!class_exists('JLTMA_Addon_Extensions')) {
    class JLTMA_Addon_Extensions
    {
        private static $instance = null;
        public static $jltma_extensions;

        public function __construct()
        {
            self::$jltma_extensions = [
                'jltma-extensions'         => [
                    'title'                => esc_html__('Extensions', MELA_TD),
                    'extension'             => [
                        [
                            'key'               => 'particles',
                            'title'             => esc_html__('Particles', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/particles-background/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/particles-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=sNC0pik_g3Q'
                        ],
                        [
                            'key'               => 'animated-gradient',
                            'title'             => esc_html__('Animated Gradient BG', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/gradient-background/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/animated-gradient-background-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=jANMWGDaeG0',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'reading-progress-bar',
                            'title'             => esc_html__('Reading Progress Bar', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/100-best-elementor-addons/',
                            'docs_url'          => '',
                            'tuts_url'          => ''
                        ],
                        [
                            'key'               => 'bg-slider',
                            'title'             => esc_html__('Background Slider', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/background-slider/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/background-slider-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=Z6ujz7Hunjg'
                        ],
                        [
                            'key'               => 'custom-css',
                            'title'             => esc_html__('Custom CSS', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/docs/addons/custom-css-extension/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/custom-css-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=ajXVVGJZuuM'
                        ],
                        [
                            'key'               => 'custom-js',
                            'title'             => esc_html__('Custom JS', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/docs/addons/custom-js-extension/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/custom-js-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=8G4JLw0s8sI'
                        ],
                        [
                            'key'               => 'positioning',
                            'title'             => esc_html__('Positioning', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/positioning/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/positioning-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=sXPZv3zVlmY'
                        ],
                        [
                            'key'               => 'extras',
                            'title'             => esc_html__('Container Extras', MELA_TD),
                            'demo_url'          => '',
                            'docs_url'          => '',
                            'tuts_url'          => ''
                        ],
                        [
                            'key'               => 'mega-menu',
                            'title'             => esc_html__('Mega Menu', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/elementor-mega-menu/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/navigation-menu/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=HIf0ud-5Wpo'
                        ],
                        [
                            'key'               => 'transition',
                            'title'             => esc_html__('Entrance Animation', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/entrance-animation/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/entrance-animation/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=kphJEszEAFQ',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'transforms',
                            'title'             => esc_html__('Transforms', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/transforms-extension/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/transforms-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=sH2BQT0xOnY',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'rellax',
                            'title'             => esc_html__('Rellax', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/rellax/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/rellax-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=xYvMVXoZ_NE',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'reveal',
                            'title'             => esc_html__('Reveal', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/reveal/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/reveal-extension/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=xEG1fi_lY1M',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'header-footer-comment',
                            'title'             => esc_html__('Header,Footer,Comment Form', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/header-footer-comment-builder/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/header-footer-comment-builder/',
                            'tuts_url'          => ''
                        ],
                        [
                            'key'               => 'display-conditions',
                            'title'             => esc_html__('Display Conditions', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/display-conditions/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/display-conditions/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=6hDqKVQmsr8',
                            'is_pro'            => true
                        ],
                        [
                            'key'               => 'dynamic-tags',
                            'title'             => esc_html__('Dynamic Tags', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/dynamic-tags/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/dynamic-tags/',
                            'tuts_url'          => 'https://youtu.be/vvhhMq8uz1g'
                        ],
                        [
                            'key'               => 'floating-effects',
                            'title'             => esc_html__('Floating Effects', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/floating-effect/',
                            'docs_url'          => 'https://master-addons.com/floating-effect-elementor/',
                            'tuts_url'          => '',
                            'is_pro'            => true
                        ],
                        [
                            'key'           => 'wrapper-link',
                            'title'         => esc_html__('Wrapper Link', MELA_TD),
                            'demo_url'      => '',
                            'docs_url'      => 'https://master-addons.com/docs/addons/wrapper-link/',
                            'tuts_url'      => 'https://www.youtube.com/watch?v=fsbK4G9T-qM'
                        ],
                        [
                            'key'           => 'duplicator',
                            'title'         => esc_html__('Post/Page Duplicator', MELA_TD),
                            'demo_url'      => '',
                            'docs_url'      => '',
                            'tuts_url'      => ''
                        ],
                        // [
                        //     'key'           => 'content-protection',
                        //     'title'         => esc_html__('Content Protection', MELA_TD),
                        //     'demo_url'      => '',
                        //     'docs_url'      => '',
                        //     'tuts_url'      => ''
                        // ],
                        // [
                        //     'key'           => 'morphing-effects',
                        //     'title'         => esc_html__( 'Morphing Effects', MELA_TD),
                        //     'demo_url'      => '',
                        //     'docs_url'      => '',
                        //     'tuts_url'      => ''
                        // ]
                        // [
                        //     'key'           => 'live-copy',
                        //     'title'         => esc_html__( 'Live Copy', MELA_TD),
                        //     'demo_url'      => '',
                        //     'docs_url'      => '',
                        //     'tuts_url'      => ''
                        // ]

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
    JLTMA_Addon_Extensions::get_instance();
}
