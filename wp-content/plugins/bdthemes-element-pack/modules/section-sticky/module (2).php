<?php

namespace ElementPack\Modules\SectionSticky;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use ElementPack;
use ElementPack\Base\Element_Pack_Module_Base;

if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'bdt-section-sticky';
    }

    public function register_section($element) {
        $element->start_controls_section(
            'element_pack_section_sticky_section',
            [
                'label' => BDTEP_CP . __('Section Sticky', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_ADVANCED,
            ]
        );
        $element->end_controls_section();
    }

    public function register_controls($section, $args) {

        $section->add_control(
            'section_sticky_on',
            [
                'label'        => esc_html__('Enable Sticky', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'description'  => esc_html__('Set sticky options by enable this option.', 'bdthemes-element-pack'),
            ]
        );

        $section->add_control(
            'section_sticky_offset',
            [
                'label'     => esc_html__('Offset', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 0,
                ],
                'condition' => [
                    'section_sticky_on' => 'yes',
                ],
            ]
        );

        $section->add_control(
            'section_sticky_active_bg',
            [
                'label'     => esc_html__('Active Background Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.bdt-sticky.bdt-active' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'section_sticky_on' => 'yes',
                ],
            ]
        );

        $section->add_responsive_control(
            'section_sticky_active_padding',
            [
                'label'      => esc_html__('Active Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}}.bdt-sticky.bdt-active' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'section_sticky_on' => 'yes',
                ],
            ]
        );

        $section->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'label'     => esc_html__('Active Box Shadow', 'bdthemes-element-pack'),
                'name'      => 'section_sticky_active_shadow',
                'selector'  => '{{WRAPPER}}.bdt-sticky.bdt-active',
                'condition' => [
                    'section_sticky_on' => 'yes',
                ],
            ]
        );

        $section->add_control(
            'section_sticky_animation',
            [
                'label'     => esc_html__('Animation', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'options'   => element_pack_transition_options(),
                'condition' => [
                    'section_sticky_on' => 'yes',
                ],
            ]
        );

        $section->add_control(
            'section_sticky_bottom',
            [
                'label'       => esc_html__('Scroll Until', 'bdthemes-element-pack'),
                'description' => esc_html__('If you don\'t want to scroll after specific section so set that section ID/CLASS here. for example: #section1 or .section1 it\'s support ID/CLASS', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'condition'   => [
                    'section_sticky_on' => 'yes',
                ],
            ]
        );

        $section->add_control(
            'section_sticky_on_scroll_up',
            [
                'label'        => esc_html__('Sticky on Scroll Up', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'description'  => esc_html__('Set sticky options when you scroll up your mouse.', 'bdthemes-element-pack'),
                'condition'    => [
                    'section_sticky_on' => 'yes',
                ],
            ]
        );

        $section->add_control(
            'section_sticky_position',
            [
                'label'       => esc_html__('Position', 'bdthemes-element-pack'),
                'description' => esc_html__('By default, the element sticks to the top of the viewport. You can set the position option to use a different position.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'start',
                'options'     => [
                    'start' => 'Start',
                    'end'   => 'End',
                    'auto'  => 'Auto',
                ],
                'condition'   => [
                    'section_sticky_on' => 'yes',
                ],
            ]
        );

        $section->add_control(
            'section_sticky_zindex',
            [
                'label'     => esc_html__('Z-Index', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::NUMBER,
                'min' => -1000,
                'max' => 9999,
                'condition' => [
                    'section_sticky_on' => 'yes',
                ],
                'selectors'  => [
                    '{{WRAPPER}}.bdt-sticky.bdt-active' => 'z-index: {{VALUE}};',
                ],
            ]
        );


        $section->add_control(
            'section_sticky_off_media',
            [
                'label'     => __('Turn Off', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    '960' => [
                        'title' => __('On Tablet', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-device-tablet',
                    ],
                    '768' => [
                        'title' => __('On Mobile', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-device-mobile',
                    ],
                ],
                'condition' => [
                    'section_sticky_on' => 'yes',
                ],
                'separator' => 'before',
            ]
        );

    }


    public function sticky_before_render($section) {
        $settings = $section->get_settings_for_display();
        if ( !empty($settings['section_sticky_on']) == 'yes' ) {
            $sticky_option = [];
            if ( !empty($settings['section_sticky_on_scroll_up']) ) {
                $sticky_option['show-on-up'] = 'show-on-up: true';
            }

            if ( !empty($settings['section_sticky_offset']['size']) ) {
                $sticky_option['offset'] = 'offset: ' . $settings['section_sticky_offset']['size'];
            }

            if ( !empty($settings['section_sticky_animation']) ) {
                $sticky_option['animation'] = 'animation: bdt-animation-' . $settings['section_sticky_animation'] . '; top: 100';
            }

            if ( !empty($settings['section_sticky_bottom']) ) {
                $sticky_option['bottom'] = 'bottom: ' . $settings['section_sticky_bottom'];
            }

            if ( !empty($settings['section_sticky_position'] ) ) {
                if ( $settings['section_sticky_position'] == 'start' || $settings['section_sticky_position'] == 'end'  ) {
                    $sticky_option['position'] = 'position: ' . $settings['section_sticky_position'];
                } else {
                    $sticky_option['position'] = 'overflow-flip: true';
                }                
            }

            if ( !empty($settings['section_sticky_off_media']) ) {
                $sticky_option['media'] = 'media: ' . $settings['section_sticky_off_media'];
            }

            $section->add_render_attribute('_wrapper', 'data-bdt-sticky', implode(";", $sticky_option));
            $section->add_render_attribute('_wrapper', 'class', 'bdt-sticky');
        }
    }

    public function sticky_script_render($section) {

        if ( $section->get_settings('section_sticky_on') == 'yes' ) {
            wp_enqueue_script('ep-section-sticky');
        }

    }

    protected function add_actions() {

        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/section/element_pack_section_sticky_section/before_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/frontend/section/before_render', [$this, 'sticky_before_render'], 10, 1);
        add_action('elementor/frontend/section/after_render', [$this, 'sticky_script_render'], 10, 1);
        
        
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/container/element_pack_section_sticky_section/before_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/frontend/container/before_render', [$this, 'sticky_before_render'], 10, 1);
        add_action('elementor/frontend/container/after_render', [$this, 'sticky_script_render'], 10, 1);

    }
}