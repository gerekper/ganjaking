<?php

namespace ElementPack\Includes\TemplateLibrary\Editor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class ElementPack_Template_Library_Editor_Init {

    private $dir;

    function __construct() {
        $this->dir = dirname(__FILE__) . '/';

        add_action('elementor/editor/before_enqueue_scripts', array($this, 'enqueue_scripts'), 1);

        // print views and tab variables on footer.
        add_action('elementor/editor/footer', array($this, 'admin_inline_js'));
        add_action('elementor/editor/footer', array($this, 'print_views'));

        // enqueue editor css.
        add_action('elementor/editor/after_enqueue_styles', array($this, 'editor_styles'));

        // enqueue modal's preview css.
        add_action('elementor/preview/enqueue_styles', array($this, 'preview_styles'));
    }

    public function enqueue_scripts() {

        wp_enqueue_script(
            'bdt-template-library-editor-scripts',
            BDTEP_URL . 'includes/template-library/editor/assets/js/editor-template-library.min.js',
            array('jquery', 'underscore', 'backbone-marionette'),
            BDTEP_VER,
            true
        );
    }

    public function editor_styles() {
        $direction_suffix = is_rtl() ? '.rtl' : '';

        wp_enqueue_style(
            'bdt-template-library-editor-style',
            BDTEP_URL . 'includes/template-library/editor/assets/css/editor-template-library' . $direction_suffix . '.css',
            array(),
            BDTEP_VER
        );
    }

    public function preview_styles() {

        $direction_suffix = is_rtl() ? '.rtl' : '';

        wp_enqueue_style(
            'bdt-template-library-preview-style',
            BDTEP_URL . 'includes/template-library/editor/assets/css/editor-template-preview' . $direction_suffix . '.css',
            array(),
            BDTEP_VER
        );
    }

    public function admin_inline_js() { ?>
        <script type="text/javascript">
            var ElementPackLibreryData = {
                "libraryButton": "Elements Button",
                "modalRegions": {
                    "modalHeader": ".dialog-header",
                    "modalContent": ".dialog-message"
                },
                "license": {
                    "activated": true,
                    "link": "https://google.com"
                },
                "tabs": {
                    "bdt_elementpack_page": {
                        "title": "Ready Pages",
                        "data": [],
                        "settings": {
                            "show_title": true,
                            "show_keywords": true
                        }
                    },
                    "bdt_elementpack_header": {
                        "title": "Headers",
                        "data": [],
                        "settings": {
                            "show_title": false,
                            "show_keywords": true
                        }
                    },
                    "bdt_elementpack_footer": {
                        "title": "Footers",
                        "data": [],
                        "settings": {
                            "show_title": false,
                            "show_keywords": true
                        }
                    },
                    "bdt_elementpack_block": {
                        "title": "Blocks",
                        "data": [],
                        "settings": {
                            "show_title": false,
                            "show_keywords": true
                        }
                    },
                },
                "defaultTab": "bdt_elementpack_page",
                "new_demo_rang_date": "<?php echo date('Ymd', strtotime('-31 days')) ?>"
            };
        </script> <?php
                }
                public function print_views() {
                    foreach (glob($this->dir . 'views/editor/*.php') as $file) {
                        $name = basename($file, '.php');
                        ob_start();
                        include $file;
                        printf('<script type="text/html" id="view-bdt-elementpack-%1$s">%2$s</script>', $name, ob_get_clean());
                    }
                }
            }

            new ElementPack_Template_Library_Editor_Init();
