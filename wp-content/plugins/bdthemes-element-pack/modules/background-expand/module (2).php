<?php

namespace ElementPack\Modules\BackgroundExpand;

use Elementor\Controls_Manager;
use ElementPack;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
    exit;
}

class Module extends Element_Pack_Module_Base {

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'bdt-background-expand';
    }

    public function register_section($element) {
        $element->start_controls_section(
            'section_element_pack_bg_expand_controls',
            [
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => BDTEP_CP . esc_html__('Background Expand', 'bdthemes-element-pack') . BDTEP_NC,
            ]
        );
        $element->end_controls_section();
    }

    public function register_controls($section, $args) {

        $section->add_control(
            'ep_bg_expand_enable',
            [
                'label'              => BDTEP_CP . esc_html__('Enable', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SWITCHER,
                'default'            => '',
                'return_value'       => 'yes',
                'frontend_available' => true,
                'prefix_class'       => 'bdt-bg-expand-',
            ]
        );

        // $section->add_control(
        //     'ep_bg_expand_selector',
        //     [
        //         'label'              => esc_html__('Selector', 'bdthemes-element-pack'),
        //         'type'               => Controls_Manager::TEXT,
        //         'placeholder'        => esc_html__('.test-class', 'bdthemes-element-pack'),
        //         'frontend_available' => true,
        //         'condition' => [
        //             'ep_bg_expand_enable' => 'yes'
        //         ],
        //     ]
        // );

        $section->add_control(
            'ep_bg_expand_container_width',
            [
                'label' => esc_html__('Container Width (px)', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1024,
                        'max' => 2100,
                    ],
                ],
                'render_type' => 'template',
                'condition'   => [
                    'ep_bg_expand_enable' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-bg-expand-container-width: {{SIZE}}px;',
                ],
            ]
        );

        $section->add_control(
            'ep_bg_expand_anim_speed',
            [
                'label' => esc_html__('Transition Speed (ms)', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 500,
                    ],
                ],
                // 'render_type' => 'none',
                'condition'   => [
                    'ep_bg_expand_enable' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ep-bg-expand-anim-speed: {{SIZE}}ms;',
                ],
            ]
        );

        $section->start_controls_tabs(
            'ep_bg_expand_tabs',
            [
                'condition' => [
                    'ep_bg_expand_enable' => 'yes'
                ],
            ]
        );

        $section->start_controls_tab(
            'ep_bg_expand_tab_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $section->add_responsive_control(
            'ep_bg_expand_border_radius',
            [
                'label'              => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::DIMENSIONS,
                'size_units'         => ['px', '%'],
                'selectors'          => [
                    '{{WRAPPER}}.bdt-bg-expand-yes' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $section->end_controls_tab();

        $section->start_controls_tab(
            'ep_bg_expand_tab_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );

        $section->add_responsive_control(
            'ep_bg_expand_border_radius_active',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}}.bdt-bg-expand-yes.bdt-bx-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $section->end_controls_tab();

        $section->end_controls_tabs();

        $section->add_control(
            'ep_bg_expand_output',
            [
                'type'        => Controls_Manager::HIDDEN,
                'default'     => '1',
                'render_type' => 'template',
                'selectors'   => [
                    '{{WRAPPER}}.bdt-bg-expand-yes' => '
									margin-left : auto;
									margin-right: auto;
									max-width   : var(--ep-bg-expand-container-width, 1300px);
									transition  : border-radius .2s, max-width var(--ep-bg-expand-anim-speed, 350ms) ease !important;'
                ],
                'condition' => [
                    'ep_bg_expand_enable' => 'yes'
                ],
            ]
        );

        $section->add_control(
            'ep_bg_expand_output_active',
            [
                'type'        => Controls_Manager::HIDDEN,
                'default'     => '1',
                'render_type' => 'template',
                'selectors'   => [
                    '{{WRAPPER}}.bdt-bg-expand-yes.bdt-bx-active' => '
									max-width        : 100%;'
                ],
                'condition' => [
                    'ep_bg_expand_enable' => 'yes'
                ],
            ]
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script('gsap', BDTEP_ASSETS_URL . 'vendor/js/gsap.min.js', [], '3.9.1', true);
        wp_enqueue_script('scroll-trigger', BDTEP_ASSETS_URL . 'vendor/js/ScrollTrigger.min.js', [], '3.9.1', true);
        wp_enqueue_script('ep-background-expand');
    }
    public function should_script_enqueue($section) {
        if ('yes' === $section->get_settings_for_display('ep_bg_expand_enable')) {
            $this->enqueue_scripts();
        }
    }

    protected function add_actions() {
        add_action('elementor/element/container/section_background/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/container/section_element_pack_bg_expand_controls/before_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/frontend/container/after_render', [$this, 'should_script_enqueue']);

        add_action('elementor/element/section/section_background/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/section/section_element_pack_bg_expand_controls/before_section_end', [$this, 'register_controls'], 10, 2);

        //render scripts
        add_action('elementor/frontend/section/after_render', [$this, 'should_script_enqueue']);
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
    }
}
