<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;
use \Elementor\Element_Base;


/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 2/12/2020
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.


class Extension_Wrapper_Link
{

    private static $instance = null;

    private function __construct()
    {
        add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'jltma_wrapper_link_add_controls_section'], 10, 3);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'jltma_wrapper_link_add_controls_section'], 10, 1);
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'jltma_wrapper_link_add_controls_section'], 10, 1);

        add_action('elementor/frontend/before_render', [$this, 'jltma_before_section_render'], 10, 1);
    }

    public static function jltma_wrapper_link_add_controls_section(Element_Base $element)
    {

        $tabs = Controls_Manager::TAB_CONTENT;

        if ('section' === $element->get_name() || 'column' === $element->get_name()) $tabs = Controls_Manager::TAB_LAYOUT;

        $element->start_controls_section(
            'jltma_section_wrapper_link',
            [
                'label' => MA_EL_BADGE . esc_html__('Wrapper Link', MELA_TD),
                'tab'   => $tabs,
            ]
        );

        $element->add_control(
            'jltma_section_element_link',
            [
                'label'       => esc_html__('Link', MELA_TD),
                'type'        => Controls_Manager::URL,
                'dynamic'     => [
                    'active' => true,
                ],
                'placeholder' => 'https://wrapper-link.com',
            ]
        );

        $element->end_controls_section();
    }

    public static function jltma_before_section_render(Element_Base $element)
    {

        $settings = $element->get_settings_for_display();

        $jltma_element_link  = $settings['jltma_section_element_link'];

        if ($jltma_element_link && !empty($jltma_element_link['url'])) {
            $element->add_render_attribute(
                '_wrapper',
                [
                    'data-jltma-wrapper-link' => json_encode($jltma_element_link),
                    'style' => 'cursor: pointer'
                ]
            );
        }
    }


    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}

Extension_Wrapper_Link::get_instance();
