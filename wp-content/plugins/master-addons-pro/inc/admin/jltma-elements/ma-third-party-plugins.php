<?php

namespace MasterAddons\Admin\Dashboard\Addons\Extensions;

if (!class_exists('JLTMA_Third_Party_Extensions')) {
    class JLTMA_Third_Party_Extensions
    {
        private static $instance = null;
        public static $jltma_third_party_plugins;

        public function __construct()
        {
            self::$jltma_third_party_plugins = [
                'jltma-plugins'         => [
                    'title'                => esc_html__('Extensions', MELA_TD),
                    'plugin'             => [
                        [
                            'key'           => 'custom-breakpoints',
                            'title'         => esc_html__('Custom Breakpoints', MELA_TD),
                            'wp_slug'       => 'custom-breakpoints-for-elementor',
                            'download_url'  => '',
                            'plugin_file'   => 'custom-breakpoints-for-elementor/custom-breakpoints-for-elementor.php'
                        ]

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
    JLTMA_Third_Party_Extensions::get_instance();
}
