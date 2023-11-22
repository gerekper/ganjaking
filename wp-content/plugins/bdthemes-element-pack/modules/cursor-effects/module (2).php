<?php

namespace ElementPack\Modules\CursorEffects;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'bdt-cursor-effects';
    }

    public function register_section($element) {
        $element->start_controls_section(
            'element_pack_cursor_effects_section',
            [
                'tab'   => Controls_Manager::TAB_ADVANCED,
                'label' => BDTEP_CP . esc_html__('Cursor Effects', 'bdthemes-element-pack') . BDTEP_NC,
            ]
        );
        $element->end_controls_section();
    }

    public function register_controls($section, $args) {

        $section->add_control(
            'element_pack_cursor_effects_show',
            [
                'label'              => __('Show Cursor Effects', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SWITCHER,
                'return_value'       => 'yes',
                'prefix_class'       => 'bdt-cursor-effects-',
                'frontend_available' => true,
                'render_type'        => 'template',
            ]
        );
        $section->start_controls_tabs(
            'element_pack_cursor_effects_tabs'
        );

        $section->start_controls_tab(
            'element_pack_cursor_effects_tab_layout',
            [
                'label'     => esc_html__('Layout', 'bdthemes-element-pack'),
                'condition' => [
                    'element_pack_cursor_effects_show' => 'yes'
                ],
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_source',
            [
                'label'              => esc_html__('Source', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'default',
                'frontend_available' => true,
                'render_type'        => 'none',
                'options'            => [
                    'default' => esc_html__('Default', 'bdthemes-element-pack'),
                    'text'    => esc_html__('Text', 'bdthemes-element-pack'),
                    'image'   => esc_html__('Image', 'bdthemes-element-pack'),
                    'icons'   => esc_html__('Icons', 'bdthemes-element-pack'),
                ],
                'condition'          => [
                    'element_pack_cursor_effects_show' => 'yes'
                ],
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_image_src',
            [
                'label'              => esc_html__('Image', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::MEDIA,
                'frontend_available' => true,
                'render_type'        => 'template',
                'default'            => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'          => [
                    'element_pack_cursor_effects_source' => 'image'
                ]
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_icons',
            [
                'label'              => esc_html__('Icons', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::ICONS,
                'frontend_available' => true,
                'render_type'        => 'template',
                'condition'          => [
                    'element_pack_cursor_effects_source' => 'icons'
                ],
                'default'            => [
                    'value'   => 'fas fa-laugh-wink',
                    'library' => 'fa-solid',
                ],
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_style',
            [
                'label'              => __('Style', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'ep-cursor-style-1',
                'options'            => [
                    'ep-cursor-style-1' => __('Style 1', 'bdthemes-element-pack'),
                    'ep-cursor-style-2' => __('Style 2', 'bdthemes-element-pack'),
                    'ep-cursor-style-3' => __('Style 3', 'bdthemes-element-pack'),
                ],
                'frontend_available' => true,
                'render_type'        => 'template',
                'condition'          => [
                    'element_pack_cursor_effects_show'   => 'yes',
                    'element_pack_cursor_effects_source' => 'default'
                ]
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_text_label',
            [
                'label'              => esc_html__('Text Label', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::TEXT,
                // 'default'            => esc_html__('HELLO', 'bdthemes-element-pack'),
                'frontend_available' => true,
                'render_type'        => 'template',
                'condition'          => [
                    'element_pack_cursor_effects_source' => 'text',
                    'element_pack_cursor_effects_show' => 'yes'
                ]
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_speed',
            [
                'label'              => __('Speed', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'size_units'         => ['px'],
                'range'              => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 1,
                        'step' => 0.001,
                    ]
                ],
                'default'            => [
                    'unit' => 'px',
                    'size' => 0.075,
                ],
                'frontend_available' => true,
                'render_type'        => 'none',
                'condition'          => [
                    'element_pack_cursor_effects_show'   => 'yes',
                    'element_pack_cursor_effects_source' => 'default'
                ]

            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_disable_default_cursor',
            [
                'label'        => __('Disable Default Cursor', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'separator'    => 'before',
                'condition'    => [
                    'element_pack_cursor_effects_show' => 'yes'
                ],
                'selectors'    => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes' => 'cursor: none'
                ]
            ]
        );
        $section->end_controls_tab();
        $section->start_controls_tab(
            'element_pack_cursor_effects_tab_style',
            [
                'label'     => esc_html__('Style', 'bdthemes-element-pack'),
                'condition' => [
                    'element_pack_cursor_effects_show' => 'yes'
                ],
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_primary',
            [
                'label'     => esc_html__('Primary', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'element_pack_cursor_effects_source' => 'default'
                ]
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_primary_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes' => '--cursor-ball-color: {{VALUE}}',
                ],
                'condition' => [
                    'element_pack_cursor_effects_source' => ['default', 'icons']
                ]
            ]
        );
        $section->add_responsive_control(
            'element_pack_cursor_effects_primary_size',
            [
                'label'     => esc_html__('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes' => '--cursor-ball-size:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'element_pack_cursor_effects_source' => 'default'
                ]
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_secondary',
            [
                'label'     => esc_html__('Secondary', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'element_pack_cursor_effects_source' => 'default'
                ]
            ]
        );
        $section->add_control(
            'element_pack_cursor_effects_secondary_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes' => '--cursor-circle-color: {{VALUE}}',
                ],
                'condition' => [
                    'element_pack_cursor_effects_source' => 'default'
                ]
            ]
        );
        $section->add_responsive_control(
            'element_pack_cursor_effects_secondary_size',
            [
                'label'     => esc_html__('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes' => '--cursor-circle-size:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'element_pack_cursor_effects_source' => 'default'
                ]
            ]
        );
        //TEXT
        $section->add_control(
            'element_pack_cursor_effects_text_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-text' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'element_pack_cursor_effects_source' => 'text'
                ]
            ]
        );
        $section->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'element_pack_cursor_effects_text_background',
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-text',
                'condition' => [
                    'element_pack_cursor_effects_source' => 'text'
                ]
            ]
        );
        $section->add_responsive_control(
            'element_pack_cursor_effects_text_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'element_pack_cursor_effects_source' => 'text'
                ]
            ]
        );
        $section->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'element_pack_cursor_effects_text_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-text',
                'condition' => [
                    'element_pack_cursor_effects_source' => 'text'
                ]
            ]
        );
        $section->add_responsive_control(
            'element_pack_cursor_effects_text_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'element_pack_cursor_effects_source' => 'text'
                ]
            ]
        );
        $section->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'element_pack_cursor_effects_text_typography',
                'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-text',
                'condition' => [
                    'element_pack_cursor_effects_source' => 'text'
                ]
            ]
        );
        $section->add_responsive_control(
            'element_pack_cursor_effects_image_size',
            [
                'label'     => esc_html__('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-image' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'element_pack_cursor_effects_source' => 'image'
                ]
            ]
        );
        $section->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'element_pack_cursor_effects_image_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-image',
                'condition' => [
                    'element_pack_cursor_effects_source' => 'image'
                ]
            ]
        );
        $section->add_responsive_control(
            'element_pack_cursor_effects_image_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'element_pack_cursor_effects_source' => 'image'
                ]
            ]
        );

        $section->add_responsive_control(
            'element_pack_cursor_effects_icons_size',
            [
                'label'     => esc_html__('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.bdt-cursor-effects-yes .bdt-cursor-icons' => 'font-size:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'element_pack_cursor_effects_source' => 'icons'
                ]
            ]
        );
        $section->end_controls_tab();

        $section->end_controls_tabs();
    }

    public function enqueue_scripts() {
        wp_enqueue_script('cotton-js', BDTEP_ASSETS_URL . 'vendor/js/cotton.min.js', '5.3.5', true);
    }
    public function should_script_enqueue($section) {
        if ('yes' === $section->get_settings_for_display('element_pack_cursor_effects_show')) {
            $this->enqueue_scripts();
            wp_enqueue_style('ep-cursor-effects');
            wp_enqueue_script('ep-cursor-effects');
        }
    }



    protected function add_actions() {
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/common/element_pack_cursor_effects_section/before_section_end', [$this, 'register_controls'], 10, 2);

        // render scripts
        add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
    }
}
