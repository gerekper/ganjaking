<?php
/**
 * CSS Transform extension class.
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Extension;

use Elementor\Element_Base;
use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || die();

class CSS_Transform {

	public static function init() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ __CLASS__, 'register' ], 1 );
	}

	public static function register( Element_Base $element ) {
		$element->start_controls_section(
			'_section_css_transform',
			[
				'label' => __( 'CSS Transform', 'happy-elementor-addons' ) . ha_get_section_icon(),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'ha_transform_fx',
			[
				'label' => __( 'Enable', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'ha-css-transform-',
			]
		);

		$element->start_controls_tabs(
			'_tabs_ha_transform',
			[
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_controls_tab(
			'_tabs_ha_transform_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->add_control(
			'ha_transform_fx_translate_toggle',
			[
				'label' => __( 'Translate', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'ha_transform_fx_translate_x',
			[
				'label' => __( 'Translate X', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'condition' => [
					'ha_transform_fx_translate_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-translate-x: {{SIZE}}px;'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_translate_y',
			[
				'label' => __( 'Translate Y', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'condition' => [
					'ha_transform_fx_translate_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-translate-y: {{SIZE}}px;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'ha_transform_fx_rotate_toggle',
			[
				'label' => __( 'Rotate', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_transform_fx_rotate_mode',
			[
				'label' => __( 'Mode', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'compact' => [
						'title' => __( 'Compact', 'happy-elementor-addons' ),
						'icon' => 'eicon-plus-circle',
					],
					'loose' => [
						'title' => __( 'Loose', 'happy-elementor-addons' ),
						'icon' => 'eicon-minus-circle',
					],
				],
				'default' => 'loose',
				'toggle' => false
			]
		);

		$element->add_control(
			'ha_transform_fx_rotate_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_rotate_x',
			[
				'label' => __( 'Rotate X', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_rotate_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
					'ha_transform_fx_rotate_mode' => 'loose'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-rotate-x: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_rotate_y',
			[
				'label' => __( 'Rotate Y', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_rotate_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
					'ha_transform_fx_rotate_mode' => 'loose'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-rotate-y: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_rotate_z',
			[
				'label' => __( 'Rotate (Z)', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_rotate_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-rotate-z: {{SIZE}}deg;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'ha_transform_fx_scale_toggle',
			[
				'label' => __( 'Scale', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_transform_fx_scale_mode',
			[
				'label' => __( 'Mode', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'compact' => [
						'title' => __( 'Compact', 'happy-elementor-addons' ),
						'icon' => 'eicon-plus-circle',
					],
					'loose' => [
						'title' => __( 'Loose', 'happy-elementor-addons' ),
						'icon' => 'eicon-minus-circle',
					],
				],
				'default' => 'loose',
				'toggle' => false
			]
		);

		$element->add_control(
			'ha_transform_fx_scale_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_scale_x',
			[
				'label' => __( 'Scale (X)', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'condition' => [
					'ha_transform_fx_scale_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-scale-x: {{SIZE}}; --ha-tfx-scale-y: {{SIZE}};'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_scale_y',
			[
				'label' => __( 'Scale Y', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'condition' => [
					'ha_transform_fx_scale_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
					'ha_transform_fx_scale_mode' => 'loose',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-scale-y: {{SIZE}};'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'ha_transform_fx_skew_toggle',
			[
				'label' => __( 'Skew', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'ha_transform_fx_skew_x',
			[
				'label' => __( 'Skew X', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_skew_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-skew-x: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_skew_y',
			[
				'label' => __( 'Skew Y', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_skew_toggle' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-skew-y: {{SIZE}}deg;'
				],
			]
		);

		$element->end_popover();

		$element->end_controls_tab();

		$element->start_controls_tab(
            '_tabs_ha_transform_hover',
            [
				'label' => __( 'Hover', 'happy-elementor-addons' ),
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
            ]
		);

		$element->add_control(
			'ha_transform_fx_translate_toggle_hover',
			[
				'label' => __( 'Translate', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'ha_transform_fx_translate_x_hover',
			[
				'label' => __( 'Translate X', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'condition' => [
					'ha_transform_fx_translate_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-translate-x-hover: {{SIZE}}px;'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_translate_y_hover',
			[
				'label' => __( 'Translate Y', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'condition' => [
					'ha_transform_fx_translate_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-translate-y-hover: {{SIZE}}px;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'ha_transform_fx_rotate_toggle_hover',
			[
				'label' => __( 'Rotate', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_transform_fx_rotate_mode_hover',
			[
				'label' => __( 'Mode', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'compact' => [
						'title' => __( 'Compact', 'happy-elementor-addons' ),
						'icon' => 'eicon-plus-circle',
					],
					'loose' => [
						'title' => __( 'Loose', 'happy-elementor-addons' ),
						'icon' => 'eicon-minus-circle',
					],
				],
				'default' => 'loose',
				'toggle' => false
			]
		);

		$element->add_control(
			'ha_transform_fx_rotate_hr_hover',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_rotate_x_hover',
			[
				'label' => __( 'Rotate X', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_rotate_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
					'ha_transform_fx_rotate_mode_hover' => 'loose'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-rotate-x-hover: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_rotate_y_hover',
			[
				'label' => __( 'Rotate Y', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_rotate_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
					'ha_transform_fx_rotate_mode_hover' => 'loose'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-rotate-y-hover: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_rotate_z_hover',
			[
				'label' => __( 'Rotate (Z)', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_rotate_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-rotate-z-hover: {{SIZE}}deg;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'ha_transform_fx_scale_toggle_hover',
			[
				'label' => __( 'Scale', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_transform_fx_scale_mode_hover',
			[
				'label' => __( 'Mode', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'compact' => [
						'title' => __( 'Compact', 'happy-elementor-addons' ),
						'icon' => 'eicon-plus-circle',
					],
					'loose' => [
						'title' => __( 'Loose', 'happy-elementor-addons' ),
						'icon' => 'eicon-minus-circle',
					],
				],
				'default' => 'loose',
				'toggle' => false
			]
		);

		$element->add_control(
			'ha_transform_fx_scale_hr_hover',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_scale_x_hover',
			[
				'label' => __( 'Scale (X)', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'condition' => [
					'ha_transform_fx_scale_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-scale-x-hover: {{SIZE}}; --ha-tfx-scale-y-hover: {{SIZE}};'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_scale_y_hover',
			[
				'label' => __( 'Scale Y', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'condition' => [
					'ha_transform_fx_scale_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
					'ha_transform_fx_scale_mode_hover' => 'loose',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-scale-y-hover: {{SIZE}};'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'ha_transform_fx_skew_toggle_hover',
			[
				'label' => __( 'Skew', 'happy-elementor-addons' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'ha_transform_fx' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'ha_transform_fx_skew_x_hover',
			[
				'label' => __( 'Skew X', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_skew_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-skew-x-hover: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'ha_transform_fx_skew_y_hover',
			[
				'label' => __( 'Skew Y', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'condition' => [
					'ha_transform_fx_skew_toggle_hover' => 'yes',
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-skew-y-hover: {{SIZE}}deg;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'ha_transform_fx_transition_duration',
			[
				'label' => __( 'Transition Duration', 'happy-elementor-addons' ),
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
					'ha_transform_fx' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ha-tfx-transition-duration: {{SIZE}}s;'
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->end_controls_section();
	}
}

CSS_Transform::init();
