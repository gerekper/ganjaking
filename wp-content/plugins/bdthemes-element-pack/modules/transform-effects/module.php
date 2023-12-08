<?php

namespace ElementPack\Modules\TransformEffects;

use Elementor\Controls_Manager;
use ElementPack;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-transform-effects';
	}

	public function register_controls_widget_transform_effect($widget, $args) {

		$widget->add_control(
			'element_pack_widget_transform',
			[
				'label'        => BDTEP_CP . esc_html__('Transform Effects', 'bdthemes-element-pack'),
				'description'  => esc_html__('Don\'t use with others addon effect so it will work abnormal.', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-motion-effect-',
				'separator'    => 'before',
			]
		);

		$widget->start_controls_tabs('element_pack_widget_motion_effect_tabs');

		$widget->start_controls_tab(
			'element_pack_widget_motion_effect_tab_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_translate_toggle_normal',
			[
				'label' 		=> __('Translate', 'bdthemes-element-pack'),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'element_pack_widget_effect_transx_normal',
			[
				'label'      => esc_html__('Translate X', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'condition' => [
					'element_pack_translate_toggle_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-trans-x-normal: {{SIZE}}px;'
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_effect_transy_normal',
			[
				'label'      => esc_html__('Translate Y', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-trans-y-normal: {{SIZE}}px;'
				],
				'condition' => [
					'element_pack_translate_toggle_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
			]
		);


		$widget->end_popover();



		$widget->add_control(
			'element_pack_rotate_toggle_normal',
			[
				'label' 		=> __('Rotate', 'bdthemes-element-pack'),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->start_popover();


		$widget->add_responsive_control(
			'element_pack_widget_effect_rotatex_normal',
			[
				'label'      => esc_html__('Rotate X', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'element_pack_rotate_toggle_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-rotate-x-normal: {{SIZE||0}}deg;'
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_effect_rotatey_normal',
			[
				'label'      => esc_html__('Rotate Y', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'element_pack_rotate_toggle_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-rotate-y-normal: {{SIZE||0}}deg;'
				],
			]
		);


		$widget->add_responsive_control(
			'element_pack_widget_effect_rotatez_normal',
			[
				'label'   => __('Rotate Z', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-rotate-z-normal: {{SIZE||0}}deg;'
				],
				'condition' => [
					'element_pack_rotate_toggle_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->end_popover();


		$widget->add_control(
			'element_pack_scale_normal',
			[
				'label' 		=> __('Scale', 'bdthemes-element-pack') . BDTEP_NC,
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'element_pack_widget_effect_scalex_normal',
			[
				'label'      => esc_html__('Scale X', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'condition' => [
					'element_pack_scale_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-scale-x-normal: {{SIZE}};'
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_effect_scaley_normal',
			[
				'label'      => esc_html__('Scale Y', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'condition' => [
					'element_pack_scale_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-scale-y-normal: {{SIZE}};'
				],
			]
		);

		$widget->end_popover();

		$widget->add_control(
			'element_pack_skew_normal',
			[
				'label' 		=> __('Skew', 'bdthemes-element-pack') . BDTEP_NC,
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'element_pack_widget_effect_skewx_normal',
			[
				'label'      => esc_html__('Skew X', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'element_pack_skew_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-skew-x-normal: {{SIZE}}deg;'
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_effect_skewy_normal',
			[
				'label'      => esc_html__('Skew Y', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'element_pack_skew_normal' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-skew-y-normal: {{SIZE}}deg;'
				],
			]
		);

		$widget->end_popover();

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'element_pack_widget_motion_effect_tab_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_motion_effect_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('If you want to hover your transform effect hover by column/section, so you need to go column/section > Motion Effect > Hover Transform and set the option YES.', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$widget->add_control(
			'element_pack_translate_toggle_hover',
			[
				'label' 		=> __('Translate', 'bdthemes-element-pack'),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->start_popover();


		$widget->add_responsive_control(
			'element_pack_widget_effect_transx_hover',
			[
				'label'      => esc_html__('Translate X', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'condition' => [
					'element_pack_translate_toggle_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-trans-x-hover: {{SIZE}}px;'
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_effect_transy_hover',
			[
				'label'      => esc_html__('Translate Y', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-trans-y-hover: {{SIZE}}px;'
				],
				'condition' => [
					'element_pack_translate_toggle_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
			]
		);


		$widget->end_popover();



		$widget->add_control(
			'element_pack_rotate_toggle_hover',
			[
				'label' 		=> __('Rotate', 'bdthemes-element-pack'),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->start_popover();


		$widget->add_responsive_control(
			'element_pack_widget_effect_rotatex_hover',
			[
				'label'      => esc_html__('Rotate X', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'element_pack_rotate_toggle_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-rotate-x-hover: {{SIZE||0}}deg;'
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_effect_rotatey_hover',
			[
				'label'      => esc_html__('Rotate Y', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'element_pack_rotate_toggle_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-rotate-y-hover: {{SIZE||0}}deg;'
				],
			]
		);


		$widget->add_responsive_control(
			'element_pack_widget_effect_rotatez_hover',
			[
				'label'   => __('Rotate Z', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-rotate-z-hover: {{SIZE||0}}deg;'
				],
				'condition' => [
					'element_pack_rotate_toggle_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
			]
		);


		$widget->end_popover();


		$widget->add_control(
			'element_pack_scale_hover',
			[
				'label' 		=> __('Scale', 'bdthemes-element-pack') . BDTEP_NC,
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'element_pack_widget_effect_scalex_hover',
			[
				'label'      => esc_html__('Scale X', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'condition' => [
					'element_pack_scale_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-scale-x-hover: {{SIZE}};'
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_effect_scaley_hover',
			[
				'label'      => esc_html__('Scale Y', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'condition' => [
					'element_pack_scale_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-scale-y-hover: {{SIZE}};'
				],
			]
		);

		$widget->end_popover();

		$widget->add_control(
			'element_pack_skew_hover',
			[
				'label' 		=> __('Skew', 'bdthemes-element-pack') . BDTEP_NC,
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'return_value' 	=> 'yes',
				'condition' 	=> [
					'element_pack_widget_transform' => 'yes',
				],
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'element_pack_widget_effect_skewx_hover',
			[
				'label'      => esc_html__('Skew X', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'element_pack_skew_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-skew-x-hover: {{SIZE}}deg;'
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_effect_skewy_hover',
			[
				'label'      => esc_html__('Skew Y', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min'  => -180,
						'max'  => 180,
					],
				],
				'condition' => [
					'element_pack_skew_hover' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-skew-y-hover: {{SIZE}}deg;'
				],
			]
		);

		$widget->end_popover();

		$widget->add_control(
			'element_pack_widget_effect_transition',
			[
				'label' => __('Transition', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'element_pack_widget_transform' => 'yes',
				],
				'render_type' => 'none',
			]
		);

		$widget->start_popover();

		$widget->add_control(
			'element_pack_widget_effect_transition_duration',
			[
				'label'       => esc_html__('Duration (ms)', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '300',
				'condition' => [
					'element_pack_widget_effect_transition' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-transition-duration: {{VALUE}}ms;',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_effect_transition_delay',
			[
				'label'       => esc_html__('Delay (ms)', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'condition' => [
					'element_pack_widget_effect_transition' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-transition-delay: {{VALUE}}ms;',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_effect_transition_easing',
			[
				'label'   => esc_html__('Easing', 'bdthemes-element-pack'),
				'description' => sprintf(__('If you want use Cubic Bezier easing, Go %1s HERE %2s', 'bdthemes-element-pack'), '<a href="https://cubic-bezier.com/" target="_blank">', '</a>'),
				'type'    => Controls_Manager::TEXT,
				'default' => 'ease-out',
				'condition' => [
					'element_pack_widget_effect_transition' => 'yes',
					'element_pack_widget_transform' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-transition-easing: {{VALUE}};',
				],
			]
		);

		$widget->end_popover();

		$widget->end_controls_tab();

		$widget->end_controls_tabs();
	}

	public function register_controls_section_transform_effect($widget, $args) {
		$widget->add_control(
			'element_pack_section_transform',
			[
				'label'        => BDTEP_CP . esc_html__('Hover Transform', 'bdthemes-element-pack') . BDTEP_NC,
				'description'  => esc_html__('This feature works with our Transform Effect which you enabled from widget.', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-motion-effect-',
				'separator'    => 'before',
				'return_value' => 'wrapper',
			]
		);

		$widget->add_control(
			'element_pack_section_overflow',
			[
				'label'   => esc_html__('Content Overflow', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'visible',
				'options' => [
					'visible' => __('Visible', 'bdthemes-element-pack'),
					'hidden'  => __('Hidden', 'bdthemes-element-pack'),
				],
				'condition' => [
					'element_pack_section_transform' => 'wrapper'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-section-overflow: {{VALUE}};',
				],
			]
		);
	}

	public function register_controls_column_transform_effect($widget, $args) {
		$widget->add_control(
			'element_pack_column_transform',
			[
				'label'        => BDTEP_CP . esc_html__('Hover Transform', 'bdthemes-element-pack') . BDTEP_NC,
				'description'  => esc_html__('This feature works with our Transform Effect which you enabled from widget.', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-motion-effect-',
				'separator'    => 'before',
				'return_value' => 'wrapper',
			]
		);

		$widget->add_control(
			'element_pack_column_overflow',
			[
				'label'   => esc_html__('Content Overflow', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'visible',
				'options' => [
					'visible' => __('Visible', 'bdthemes-element-pack'),
					'hidden'  => __('Hidden', 'bdthemes-element-pack'),
				],
				'condition' => [
					'element_pack_column_transform' => 'wrapper'
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-effect-column-overflow: {{VALUE}};',
				],
			]
		);
	}

	protected function add_actions() {

		add_action('elementor/element/section/section_effects/before_section_end', [$this, 'register_controls_section_transform_effect'], 10, 11);
		add_action('elementor/element/column/section_effects/before_section_end', [$this, 'register_controls_column_transform_effect'], 10, 11);
		add_action('elementor/element/common/section_effects/before_section_end', [$this, 'register_controls_widget_transform_effect'], 10, 11);
		// add_action('elementor/frontend/widget/before_render', [$this, 'render_assets_before_render'], 10, 1);
	}
}
