<?php

namespace ElementPack\Modules\ParallaxEffects;

use Elementor\Controls_Manager;
use ElementPack;
use ElementPack\Base\Element_Pack_Module_Base;

if ( !defined('ABSPATH') ) {
    exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'bdt-parallax-effects';
    }

    public function register_widget_control($widget, $args) {

        $widget->add_control(
            'ep_parallax_effects_show',
            [
                'label'              => BDTEP_CP . esc_html__('Parallax/Scrolling Effects', 'bdthemes-element-pack') . BDTEP_NC,
                'type'               => Controls_Manager::SWITCHER,
                'default'            => '',
                'return_value'       => 'yes',
                'frontend_available' => true,
                'prefix_class'       => 'ep-parallax-effects-',
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_hr',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_x',
            [
                'label'              => __('Horizontal Parallax(X)', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_x_start',
            [
                'label' => esc_html__('Start', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'vw'],
                'range' => [
                    'px' => [
                        'min'  => -500,
                        'max'  => 500,
                        'step' => 10,
                    ],
                    'vw' => [
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],

                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_x'    => 'yes',
                    'ep_parallax_effects_x_custom_show' => '',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_x_end',
            [
                'label'              => esc_html__('End', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'vw'],
                'range'              => [
                    'px' => [
                        'min'  => -500,
                        'max'  => 500,
                        'step' => 10,
                    ],
                    'vw' => [
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_x'    => 'yes',
                    'ep_parallax_effects_x_custom_show' => '',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_x_custom_show',
            [
                'label'              => esc_html__('Custom', 'bdthemes-element-pack') . BDTEP_NC,
                'type'               => Controls_Manager::SWITCHER,
                'default'            => '',
                'return_value'       => 'yes',
                'frontend_available' => true,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_x'    => 'yes',
                ],
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_x_custom_value',
            [
                'label'              => esc_html__('Value', 'bdthemes-element-pack'),
                'description'        => esc_html__('Define multiple stops for a property by using a comma separated list of values.', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::TEXT,
                'condition'          => [
                    'ep_parallax_effects_x_custom_show' => 'yes',
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_x'    => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
                'label_block' => true
            ]
        );

        $widget->end_popover();

        $widget->add_control(
            'ep_parallax_effects_y',
            [
                'label'              => __('Vertical Parallax(Y)', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_y_start',
            [
                'label'              => esc_html__('Start', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'vw'],
                'range'              => [
                    'px' => [
                        'min'  => -500,
                        'max'  => 500,
                        'step' => 10,
                    ],
                    'vw' => [
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'default'            => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_y'    => 'yes',
                    'ep_parallax_effects_y_custom_show' => '',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_y_end',
            [
                'label'              => esc_html__('End', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'vw'],
                'range'              => [
                    'px' => [
                        'min'  => -500,
                        'max'  => 500,
                        'step' => 10,
                    ],
                    'vw' => [
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'default'            => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_y'    => 'yes',
                    'ep_parallax_effects_y_custom_show' => '',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_y_custom_show',
            [
                'label'              => esc_html__('Custom', 'bdthemes-element-pack') . BDTEP_NC,
                'type'               => Controls_Manager::SWITCHER,
                'default'            => '',
                'return_value'       => 'yes',
                'frontend_available' => true,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_y'    => 'yes',
                ],
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_y_custom_value',
            [
                'label'              => esc_html__('Value', 'bdthemes-element-pack'),
                'description'        => esc_html__('Define multiple stops for a property by using a comma separated list of values.', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::TEXT,
                'condition'          => [
                    'ep_parallax_effects_y_custom_show' => 'yes',
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_y'    => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
                'label_block' => true
            ]
        );

        $widget->end_popover();

        $widget->add_control(
            'ep_parallax_effects_opacity_toggole',
            [
                'label'              => __('Opacity', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_opacity',
            [
                'label'              => __('Select', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::CHOOSE,
                'options'            => [
                    'htov' => [
                        'title' => __('Hidden to Visible', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                    'vtoh' => [
                        'title' => __('Visible to Hidden', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                ],
                'toggle'             => true,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_opacity_custom_show' => '',
                    'ep_parallax_effects_opacity_toggole' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_opacity_custom_show',
            [
                'label'              => esc_html__('Custom', 'bdthemes-element-pack') . BDTEP_NC,
                'type'               => Controls_Manager::SWITCHER,
                'default'            => '',
                'return_value'       => 'yes',
                'frontend_available' => true,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_opacity_toggole' => 'yes',
                ],
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_opacity_custom_value',
            [
                'label'              => esc_html__('Value', 'bdthemes-element-pack'),
                'description'        => esc_html__('Define multiple stops for a property by using a comma separated list of values.', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::TEXT,
                'condition'          => [
                    'ep_parallax_effects_opacity_custom_show' => 'yes',
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_opacity_toggole' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
                'label_block' => true
            ]
        );

        $widget->end_popover();

        $widget->add_control(
            'ep_parallax_effects_blur',
            [
                'label'              => __('Blur', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_blur_start',
            [
                'label'              => esc_html__('Start', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                //'size_units'    => ['px', 'vw'],
                'range'              => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 20,
                        'step' => 1,
                    ],
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_blur' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_blur_end',
            [
                'label'              => esc_html__('End', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                //'size_units'    => ['px', 'vw'],
                'range'              => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 20,
                        'step' => 1,
                    ],
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                    'ep_parallax_effects_blur' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->end_popover();


        $widget->add_control(
            'ep_parallax_effects_rotate',
            [
                'label'              => __('Rotate', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_rotate_start',
            [
                'label'              => esc_html__('Start', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'range'              => [
                    'px' => [
                        'min'  => -360,
                        'max'  => 360,
                        'step' => 5,
                    ],
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_rotate_end',
            [
                'label'              => esc_html__('End', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'range'              => [
                    'px' => [
                        'min'  => -360,
                        'max'  => 360,
                        'step' => 5,
                    ],
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->end_popover();

        $widget->add_control(
            'ep_parallax_effects_scale',
            [
                'label'              => __('Scale', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_scale_start',
            [
                'label'              => esc_html__('Start', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'range'              => [
                    'px' => [
                        'min'  => -10,
                        'max'  => 10,
                        'step' => 0.1,
                    ],
                ],
                'default'            => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_scale_end',
            [
                'label'              => esc_html__('End', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'range'              => [
                    'px' => [
                        'min'  => -10,
                        'max'  => 10,
                        'step' => 0.1,
                    ],
                ],
                'default'            => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->end_popover();

        $widget->add_control(
            'ep_parallax_effects_hue',
            [
                'label'              => __('Hue', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_hue_value',
            [
                'label'              => esc_html__('Value', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'range'              => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 360,
                        'step' => 1,
                    ],
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->end_popover();


        $widget->add_control(
            'ep_parallax_effects_sepia',
            [
                'label'              => __('Sepia', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_sepia_value',
            [
                'label'              => esc_html__('Value', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'range'              => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'default'            => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );


        $widget->end_popover();

        $widget->add_control(
            'ep_parallax_effects_easing',
            [
                'label'              => __('Easing', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_easing_value',
            [
                'label'              => esc_html__('Value', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'range'              => [
                    'px' => [
                        'min'  => -10,
                        'max'  => 10,
                        'step' => 0.5,
                    ],
                ],
                'default'            => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );


        $widget->end_popover();


        $widget->add_control(
            'ep_parallax_effects_transition',
            [
                'label'        => __('Transition', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'condition'    => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value' => 'yes',
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_transition_for',
            [
                'label'     => esc_html__('Transition For', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::TEXT,
                'default'   => 'all',
                'condition' => [
                    'ep_parallax_effects_show'       => 'yes',
                    'ep_parallax_effects_transition' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'transition-property: {{VALUE||all}};',
                ],
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_transition_duration',
            [
                'label'     => esc_html__('Duration (ms)', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::TEXT,
                'default'   => '100',
                'condition' => [
                    'ep_parallax_effects_show'       => 'yes',
                    'ep_parallax_effects_transition' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'transition-duration: {{VALUE||100}}ms;',
                ],
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_transition_easing',
            [
                'label'       => esc_html__('Easing', 'bdthemes-element-pack'),
                'description' => sprintf(__('If you want use Cubic Bezier easing, Go %1s HERE %2s', 'bdthemes-element-pack'), '<a href="https://cubic-bezier.com/" target="_blank">', '</a>'),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'linear',
                'condition'   => [
                    'ep_parallax_effects_show'       => 'yes',
                    'ep_parallax_effects_transition' => 'yes',
                ],
                'selectors'   => [
                    '{{WRAPPER}}' => 'transition-timing-function: {{VALUE||linear}};',
                ],
            ]
        );


        $widget->end_popover();

        $widget->add_control(
            'ep_parallax_effects_viewport',
            [
                'label'              => __('Animation Viewport', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::POPOVER_TOGGLE,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'return_value'       => 'yes',
                //'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->start_popover();

        $widget->add_control(
            'ep_parallax_effects_viewport_start',
            [
                'label'              => esc_html__('Start', 'bdthemes-element-pack'),
                'description'        => esc_html__('Start offset. The value can be in vh, % and px. It supports basic mathematics operands + and -.', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::TEXT,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_viewport_end',
            [
                'label'              => esc_html__('End', 'bdthemes-element-pack'),
                'description'        => esc_html__('End offset. The value can be in vh, % and px. It supports basic mathematics operands + and -.', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::TEXT,
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
            ]
        );

        $widget->end_popover();

        $widget->add_control(
            'ep_parallax_effects_media_query',
            [
                'label'              => __('Parallax Start From', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'options'            => [
                    ''    => __('All Device', 'bdthemes-element-pack'),
                    '@xl' => __('Retina to Larger', 'bdthemes-element-pack'),
                    '@l'  => __('Desktop to Larger', 'bdthemes-element-pack'),
                    '@m'  => __('Tablet to Larger', 'bdthemes-element-pack'),
                    '@s'  => __('Mobile to Larger', 'bdthemes-element-pack'),
                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
                //'separator'          => 'after',
            ]
        );

        $widget->add_control(
            'ep_parallax_effects_target',
            [
                'label'              => __('Target', 'bdthemes-element-pack') . BDTEP_NC,
                'type'               => Controls_Manager::SELECT,
                'default'            => 'self',
                'options'            => [
                    'self'    => __('Self', 'bdthemes-element-pack'),
                    'section' => __('Section', 'bdthemes-element-pack'),

                ],
                'condition'          => [
                    'ep_parallax_effects_show' => 'yes',
                ],
                'render_type'        => 'none',
                'frontend_available' => true,
                'separator'          => 'after',
            ]
        );

    }

    public function section_parallax_effects_before_render($widget) {
        $settings = $widget->get_settings_for_display();
        if ( $settings['ep_parallax_effects_show'] == 'yes' ) {
            wp_enqueue_script('ep-parallax-effects');
        }
    }

    protected function add_actions() {

        add_action('elementor/element/section/section_effects/after_section_start', [$this, 'register_widget_control'], 10, 11);
        add_action('elementor/element/column/section_effects/after_section_start', [$this, 'register_widget_control'], 10, 11);
        add_action('elementor/element/common/section_effects/after_section_start', [$this, 'register_widget_control'], 10, 11);
        add_action('elementor/frontend/section/before_render', [$this, 'section_parallax_effects_before_render'], 10, 1); 
        add_action('elementor/frontend/widget/before_render', [$this, 'section_parallax_effects_before_render'], 10, 1); 
    }
}
