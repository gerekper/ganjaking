<?php

namespace MasterAddons\Admin\Dashboard\Addons\Elements;

if (!class_exists('JLTMA_Addon_Forms')) {
    class JLTMA_Addon_Forms
    {
        private static $instance = null;
        public static $jltma_forms;

        public function __construct()
        {
            self::$jltma_forms = [
                'jltma-forms'      => [
                    'title'     => esc_html__('Form Elements', MELA_TD),
                    'elements'      => [
                        [
                            'key'               => 'contact-form-7',
                            'title'             => esc_html__('Contact Form 7', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/contact-form-7/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=1fU6lWniRqo'
                        ],
                        [
                            'key'               => 'ninja-forms',
                            'title'             => esc_html__('Ninja Form', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/ninja-form/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=1fU6lWniRqo'
                        ],
                        [
                            'key'               => 'wpforms',
                            'title'             => esc_html__('WP Forms', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/wp-forms/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=1fU6lWniRqo'
                        ],
                        [
                            'key'               => 'gravity-forms',
                            'title'             => esc_html__('Gravity Forms', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/wp-forms/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=1fU6lWniRqo'
                        ],
                        [
                            'key'               => 'caldera-forms',
                            'title'             => esc_html__('Caldera Forms', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/wp-forms/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=1fU6lWniRqo'
                        ],
                        [
                            'key'               => 'weforms',
                            'title'             => esc_html__('weForms', MELA_TD),
                            'demo_url'          => 'https://master-addons.com/demos/wp-forms/',
                            'docs_url'          => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url'          => 'https://www.youtube.com/watch?v=1fU6lWniRqo'
                        ],

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
    JLTMA_Addon_Forms::get_instance();
}
