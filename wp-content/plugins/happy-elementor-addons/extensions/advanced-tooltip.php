<?php

namespace Happy_Addons\Elementor\Extension;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;

defined('ABSPATH') || die();

class Advanced_Tooltip {

    static $should_script_enqueue = false;

    public static function init() {
        add_action('elementor/element/common/_section_style/after_section_end', [__CLASS__, 'add_controls_section'], 1);

        add_action('elementor/frontend/widget/before_render', [__CLASS__, 'should_script_enqueue']);

        add_action('elementor/preview/enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
    }

    public static function enqueue_scripts() {
        $suffix = ha_is_script_debug_enabled() ? '.' : '.min.';

        $extension_js = HAPPY_ADDONS_DIR_PATH . 'assets/js/extension-advanced-tooltip' . $suffix . 'js';

        if (file_exists($extension_js)) {
            wp_add_inline_script(
                'elementor-frontend',
                file_get_contents($extension_js)
            );
        }
    }

    /**
     * Set should_script_enqueue based extension settings
     *
     * @param Element_Base $section
     * @return void
     */
    public static function should_script_enqueue($section) {
        if (self::$should_script_enqueue) {
            return;
        }

        if ('enable' == $section->get_settings_for_display('ha_advanced_tooltip_enable')) {
            self::$should_script_enqueue = true;

            self::enqueue_scripts();

            remove_action('elementor/frontend/section/before_render', [__CLASS__, 'should_script_enqueue']);
        }
    }

    public static function add_controls_section($element) {

        $element->start_controls_section(
            '_section_ha_advanced_tooltip',
            [
                'label' => __('Happy Tooltip', 'happy-elementor-addons') . ha_get_section_icon(),
                'tab'   => Controls_Manager::TAB_ADVANCED,
            ]
        );

        $element->add_control(
            'ha_advanced_tooltip_enable',
            [
                'label'       => __('Enable Happy Tooltip?', 'happy-elementor-addons'),
                'type'        => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'happy-elementor-addons'),
                'label_off' => __('Off', 'happy-elementor-addons'),
                'return_value' => 'enable',
                'prefix_class' => 'ha-advanced-tooltip-',
                'default' => '',
                'frontend_available' => true,
            ]
        );

        $element->start_controls_tabs('ha_tooltip_tabs');

        $element->start_controls_tab('ha_tooltip_settings', [
            'label' => __('Settings', 'happy-elementor-addons'),
            'condition' => [
                'ha_advanced_tooltip_enable!' => '',
            ],
        ]);

        $element->add_control(
            'ha_advanced_tooltip_content',
            [
                'label' => __('Content', 'happy-elementor-addons'),
                'type'      => Controls_Manager::TEXTAREA,
                'description' => ha_get_allowed_html_desc('intermediate'),
                'rows' => 5,
                'default' => __('I am a tooltip', 'happy-elementor-addons'),
                'dynamic' => ['active' => true],
                'frontend_available' => true,
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_responsive_control(
            'ha_advanced_tooltip_position',
            [
                'label' => __('Position', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top' => __('Top', 'happy-elementor-addons'),
                    'bottom' => __('Bottom', 'happy-elementor-addons'),
                    'left' => __('Left', 'happy-elementor-addons'),
                    'right' => __('Right', 'happy-elementor-addons'),
                ],
                'frontend_available' => true,
                'prefix_class' => 'ha-advanced-tooltip%s-',
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_control(
            'ha_advanced_tooltip_animation',
            [
                'label' => __('Animation', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => __('None', 'happy-elementor-addons'),
                    'ha_fadeIn' => __('fadeIn', 'happy-elementor-addons'),
                    'ha_zoomIn' => __('zoomIn', 'happy-elementor-addons'),
                    'ha_rollIn' => __('rollIn', 'happy-elementor-addons'),
                    'ha_bounce' => __('bounce', 'happy-elementor-addons'),
                    'ha_slideInDown' => __('slideInDown', 'happy-elementor-addons'),
                    'ha_slideInLeft' => __('slideInLeft', 'happy-elementor-addons'),
                    'ha_slideInRight' => __('slideInRight', 'happy-elementor-addons'),
                    'ha_slideInUp' => __('slideInUp', 'happy-elementor-addons'),
                ],
                'frontend_available' => true,
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_control(
            'ha_advanced_tooltip_duration',
            [
                'label' => __('Animation Duration (ms)', 'happy-elementor-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 100,
                'max' => 5000,
                'step' => 50,
                'default' => 1000,
                'frontend_available' => true,
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_control(
            'ha_advanced_tooltip_arrow',
            [
                'label' => __('Arrow', 'happy-elementor-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'happy-elementor-addons'),
                'label_off' => __('Hide', 'happy-elementor-addons'),
                'return_value' => 'true',
                'default' => 'true',
                'frontend_available' => true,
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );


        $element->add_control(
            'ha_advanced_tooltip_arrow_notice',
            [
                'raw' => '<strong>' . esc_html__('Please note!', 'happy-elementor-addons') . '</strong> ' . esc_html__('By toggling Arrow to "HIDE" you get access to more background control.', 'happy-elementor-addons'),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'render_type' => 'ui',
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                    'ha_advanced_tooltip_arrow' => 'true',
                ],
            ]
        );

        $element->add_control(
            'ha_advanced_tooltip_trigger',
            [
                'label' => __('Trigger', 'happy-elementor-addons'),
                'type' => Controls_Manager::SELECT,
                'default' => 'hover',
                'options' => [
                    'click' => __('Click', 'happy-elementor-addons'),
                    'hover' => __('Hover', 'happy-elementor-addons'),
                ],
                'frontend_available' => true,
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_responsive_control(
            'ha_advanced_tooltip_distance',
            [
                'label' => __('Distance', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '0',
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.ha-advanced-tooltip-enable .ha-advanced-tooltip-content' => '--ha-tooltip-arrow-distance: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}}.ha-advanced-tooltip-enable .ha-advanced-tooltip-content' => '--ha-tooltip-arrow-distance: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}}.ha-advanced-tooltip-enable .ha-advanced-tooltip-content' => '--ha-tooltip-arrow-distance: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}}.ha-advanced-tooltip-enable .ha-advanced-tooltip-content' => '--ha-tooltip-arrow-distance: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_responsive_control(
            'ha_advanced_tooltip_align',
            [
                'label' => __('Text Alignment', 'happy-elementor-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'happy-elementor-addons'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-tooltip-content' => 'text-align: {{VALUE}};'
                ],
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->end_controls_tab();

        $element->start_controls_tab('ha_advanced_tooltip_styles', [
            'label' => __('Styles', 'happy-elementor-addons'),
            'condition' => [
                'ha_advanced_tooltip_enable!' => '',
            ],
        ]);

        $element->add_responsive_control(
            'ha_advanced_tooltip_width',
            [
                'label' => __('Width', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '120',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 800,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-tooltip-content' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_responsive_control(
            'ha_advanced_tooltip_arrow_size',
            [
                'label' => __('Tooltip Arrow Size (px)', 'happy-elementor-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '5',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-tooltip-content::after' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                    'ha_advanced_tooltip_arrow' => 'true',
                ],
            ]
        );

        $element->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ha_advanced_tooltip_typography',
                'separator' => 'after',
                'fields_options' => [
                    'typography' => [
                        'default' => 'yes'
                    ],
                    'font_family' => [
                        'default' => 'Nunito',
                    ],
                    'font_weight' => [
                        'default' => '500', // 100, 200, 300, 400, 500, 600, 700, 800, 900, normal, bold
                    ],
                    'font_size' => [
                        'default' => [
                            'unit' => 'px', // px, em, rem, vh
                            'size' => '14', // any number
                        ],
                    ],
                ],
                'selector' => '{{WRAPPER}} .ha-advanced-tooltip-content',
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'ha_advanced_tooltip_title_section_bg_color',
                'label'    => __('Background', 'happy-elementor-addons'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .ha-advanced-tooltip-content',
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                    'ha_advanced_tooltip_arrow!' => 'true',
                ],
            ]
        );

        $element->add_control(
            'ha_advanced_tooltip_background_color',
            [
                'label' => __('Background Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-tooltip-content' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .ha-advanced-tooltip-content::after' => '--ha-tooltip-arrow-color: {{VALUE}}',
                ],
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                    'ha_advanced_tooltip_arrow' => 'true',
                ],
            ]
        );

        $element->add_control(
            'ha_advanced_tooltip_color',
            [
                'label' => __('Text Color', 'happy-elementor-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-tooltip-content' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ha_advanced_tooltip_border',
                'label' => __('Border', 'happy-elementor-addons'),
                'selector' => '{{WRAPPER}} .ha-advanced-tooltip-content',
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                    'ha_advanced_tooltip_arrow!' => 'true',
                ],
            ]
        );

        $element->add_responsive_control(
            'ha_advanced_tooltip_border_radius',
            [
                'label' => __('Border Radius', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-tooltip-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_responsive_control(
            'ha_advanced_tooltip_padding',
            [
                'label' => __('Padding', 'happy-elementor-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ha-advanced-tooltip-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ha_advanced_tooltip_box_shadow',
                'selector' => '{{WRAPPER}} .ha-advanced-tooltip-content',
                'separator' => '',
                'condition' => [
                    'ha_advanced_tooltip_enable!' => '',
                ],
            ]
        );

        $element->end_controls_tab();

        $element->end_controls_tabs();

        $element->end_controls_section();
    }
}

Advanced_Tooltip::init();
