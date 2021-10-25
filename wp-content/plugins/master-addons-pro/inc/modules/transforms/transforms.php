<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;
use \Elementor\Element_Base;

use \MasterAddons\Inc\Classes\JLTMA_Extension_Prototype;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
};

class Transform_Extension extends JLTMA_Extension_Prototype
{

    private static $instance = null;
    public $name = 'Transforms';
    public $has_controls = true;

    private function add_controls($element, $args)
    {

        $element_type = $element->get_type();

        $element->add_control(
            'enabled_transform',
            [
                'label' => __('Enabled Transforms', MELA_TD),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'prefix_class' => 'jltma-transform-',
            ]
        );

        $element->start_controls_tabs(
            'jltma_transform_fx_tabs',
            [
                'condition' => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );


        $element->start_controls_tab(
            'jltma_transform_fx_tab_normal',
            [
                'label' => __('Normal', MELA_TD),
                'condition' => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );


        $element->add_control(
            'jltma_transform_fx_translate_toggle',
            [
                'label'        => __('Translate', MELA_TD),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition'    => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->start_popover();

        $element->add_responsive_control(
            'jltma_transform_fx_translate_x',
            [
                'label'      => __('Translate X', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_translate_toggle' => 'yes',
                    'enabled_transform'                  => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-translate-x: {{SIZE}}px;'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_translate_y',
            [
                'label'      => __('Translate Y', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                    ],
                ],
                'condition'   => [
                    'jltma_transform_fx_translate_toggle' => 'yes',
                    'enabled_transform'                  => 'yes',
                ],
                'render_type'  => 'none',
                'handles'      => 'range',
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-translate-y: {{SIZE}}px;'
                ],
            ]
        );

        $element->end_popover();


        $element->add_control(
            'jltma_transform_fx_rotate_toggle',
            [
                'label'     => __('Rotate', MELA_TD),
                'type'      => Controls_Manager::POPOVER_TOGGLE,
                'condition' => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->start_popover();

        $element->add_responsive_control(
            'jltma_transform_fx_rotate_x',
            [
                'label'      => __('Rotate X', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_rotate_toggle' => 'yes',
                    'enabled_transform'               => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-rotate-x: {{SIZE}}deg;'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_rotate_y',
            [
                'label'      => __('Rotate Y', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_rotate_toggle' => 'yes',
                    'enabled_transform'               => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-rotate-y: {{SIZE}}deg;'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_rotate_z',
            [
                'label'      => __('Rotate Z', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_rotate_toggle' => 'yes',
                    'enabled_transform'               => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-rotate-z: {{SIZE}}deg;'
                ],
            ]
        );

        $element->end_popover();

        $element->add_control(
            'jltma_transform_fx_scale_toggle',
            [
                'label'        => __('Scale', MELA_TD),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition'    => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->start_popover();

        $element->add_responsive_control(
            'jltma_transform_fx_scale_x',
            [
                'label'      => __('Scale X', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default'    => [
                    'size' => 1,
                ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 5,
                        'step' => .1,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_scale_toggle' => 'yes',
                    'enabled_transform'              => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-scale-x: {{SIZE}}; --jltma-tfx-scale-y: {{SIZE}};'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_scale_y',
            [
                'label'      => __('Scale Y', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default'    => [
                    'size' => 1,
                ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 5,
                        'step' => .1,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_scale_toggle' => 'yes',
                    'enabled_transform'              => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-scale-y: {{SIZE}};'
                ],
            ]
        );

        $element->end_popover();

        $element->add_control(
            'jltma_transform_fx_skew_toggle',
            [
                'label'        => __('Skew', MELA_TD),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition'    => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->start_popover();

        $element->add_responsive_control(
            'jltma_transform_fx_skew_x',
            [
                'label'      => __('Skew X', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_skew_toggle' => 'yes',
                    'enabled_transform'             => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-skew-x: {{SIZE}}deg;'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_skew_y',
            [
                'label'      => __('Skew Y', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_skew_toggle'    => 'yes',
                    'enabled_transform'                 => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-skew-y: {{SIZE}}deg;'
                ],
            ]
        );

        $element->end_popover();

        $element->end_controls_tab();


        // Hover Transforms
        $element->start_controls_tab(
            'jltma_transform_fx_tab_hover',
            [
                'label' => __('Hover', MELA_TD),
                'condition' => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'jltma_transform_fx_translate_toggle_hover',
            [
                'label'        => __('Translate', MELA_TD),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition'    => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->start_popover();

        $element->add_responsive_control(
            'jltma_transform_fx_translate_x_hover',
            [
                'label'      => __('Translate X', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_translate_toggle_hover' => 'yes',
                    'enabled_transform'                         => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-translate-x-hover: {{SIZE}}px;'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_translate_y_hover',
            [
                'label'      => __('Translate Y', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_translate_toggle_hover' => 'yes',
                    'enabled_transform'                         => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-translate-y-hover: {{SIZE}}px;'
                ],
            ]
        );

        $element->end_popover();


        $element->add_control(
            'jltma_transform_fx_rotate_toggle_hover',
            [
                'label'     => __('Rotate', MELA_TD),
                'type'      => Controls_Manager::POPOVER_TOGGLE,
                'condition' => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->start_popover();

        $element->add_responsive_control(
            'jltma_transform_fx_rotate_x_hover',
            [
                'label'      => __('Rotate X', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_rotate_toggle_hover' => 'yes',
                    'enabled_transform'                      => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-rotate-x-hover: {{SIZE}}deg;'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_rotate_y_hover',
            [
                'label'      => __('Rotate Y', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_rotate_toggle_hover' => 'yes',
                    'enabled_transform'               => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-rotate-y-hover: {{SIZE}}deg;'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_rotate_z_hover',
            [
                'label'      => __('Rotate Z', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_rotate_toggle_hover' => 'yes',
                    'enabled_transform'               => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-rotate-z-hover: {{SIZE}}deg;'
                ],
            ]
        );

        $element->end_popover();

        $element->add_control(
            'jltma_transform_fx_scale_toggle_hover',
            [
                'label'        => __('Scale', MELA_TD),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition'    => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->start_popover();

        $element->add_responsive_control(
            'jltma_transform_fx_scale_x_hover',
            [
                'label'      => __('Scale X', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default'    => [
                    'size' => 1,
                ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 5,
                        'step' => .1,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_scale_toggle_hover' => 'yes',
                    'enabled_transform'              => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-scale-x-hover: {{SIZE}}; --jltma-tfx-scale-y-hover: {{SIZE}};'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_scale_y_hover',
            [
                'label'      => __('Scale Y', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default'    => [
                    'size' => 1,
                ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 5,
                        'step' => .1,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_scale_toggle_hover' => 'yes',
                    'enabled_transform'                     => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-scale-y-hover: {{SIZE}};'
                ],
            ]
        );

        $element->end_popover();

        $element->add_control(
            'jltma_transform_fx_skew_toggle_hover',
            [
                'label'        => __('Skew', MELA_TD),
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
                'condition'    => [
                    'enabled_transform' => 'yes',
                ],
            ]
        );

        $element->start_popover();

        $element->add_responsive_control(
            'jltma_transform_fx_skew_x_hover',
            [
                'label'      => __('Skew X', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_skew_toggle_hover'  => 'yes',
                    'enabled_transform'                     => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-skew-x-hover: {{SIZE}}deg;'
                ],
            ]
        );

        $element->add_responsive_control(
            'jltma_transform_fx_skew_y_hover',
            [
                'label'      => __('Skew Y', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range'      => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'condition'  => [
                    'jltma_transform_fx_skew_toggle_hover' => 'yes',
                    'enabled_transform'             => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-skew-y-hover: {{SIZE}}deg;'
                ],
            ]
        );

        $element->end_popover();



        $element->add_control(
            'jltma_transform_fx_transition_duration',
            [
                'label' => __('Transition Duration (seconds)', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'separator' => 'before',
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3,
                        'step' => .1,
                    ]
                ],
                'condition' => [
                    'enabled_transform' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--jltma-tfx-transition-duration: {{SIZE}}s;'
                ]
            ]
        );


        $element->end_controls_tab();
        $element->end_controls_tabs();
    }


    protected function add_actions()
    {
        add_action('elementor/element/common/jltma_section_transforms_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
    }


    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}

Transform_Extension::get_instance();
