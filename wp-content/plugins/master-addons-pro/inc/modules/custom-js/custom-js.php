<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 04/08/20
 */
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.

class Extension_Custom_JS
{

    private static $instance = null;

    public function __construct()
    {
        // Add new controls to Page Settings on Advanced Tab globally
        add_action('elementor/documents/register_controls', [$this, 'jltma_add_section_custom_js_controls'], 20);
        add_action('wp_print_footer_scripts', [$this, 'jltma_page_custom_js'], 999);
    }

    public function jltma_add_section_custom_js_controls($controls)
    {
        $controls->start_controls_section(
            'jtlma_section_custom_js',
            [
                'label'         => MA_EL_BADGE . esc_html__(' Custom JS', MELA_TD),
                'tab'           => Controls_Manager::TAB_ADVANCED,
            ]
        );

        $controls->add_control(
            'jtlma_custom_js_label',
            [
                'type'          => Controls_Manager::RAW_HTML,
                'raw'           => esc_html__('Add your own custom JS here', MELA_TD),
            ]
        );

        $controls->add_control(
            'jtlma_custom_js',
            [
                'type'          => Controls_Manager::CODE,
                'show_label'    => false,
                'language'      => 'javascript',
            ]
        );

        $controls->add_control(
            'jtlma_custom_js_usage',
            [
                'type'              => Controls_Manager::RAW_HTML,
                'raw'               => __('No need to write `$( document ).ready()`, write direct code. <br> You may use both jQuery selector e.g. $(‘.selector’) or Vanilla JS selector e.g. document.queryselector(‘.selector’)', MELA_TD),
                'content_classes'   => 'elementor-descriptor',
            ]
        );

        $controls->add_control(
            'jtlma_custom_js_docs',
            [
                'type'              => Controls_Manager::RAW_HTML,
                'raw'               => __('For more information, <a href="https://master-addons.com/docs/addons/custom-js-extension/" target="_blank">click here</a>', MELA_TD),
                'content_classes'   => 'elementor-descriptor',
            ]
        );

        $controls->end_controls_section();
    }


    public function jltma_page_custom_js()
    {

        if (\Elementor\Plugin::instance()->editor->is_edit_mode() || \Elementor\Plugin::instance()->preview->is_preview_mode()) {
            return;
        }

        $document = \Elementor\Plugin::instance()->documents->get(get_the_ID());

        if (!$document) return;

        $custom_js = $document->get_settings('jtlma_custom_js');

        if (empty($custom_js)) return;

        echo "<script type='text/javascript'>(function($){
            'use strict';
            {$custom_js}
        })(jQuery);</script>";
    }



    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}

Extension_Custom_JS::get_instance();
